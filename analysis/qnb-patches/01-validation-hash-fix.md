# Patch 01 — validation() Hash Kontrolünü Aktive Et + Defense-in-Depth

> **Severity:** 🔴 CRITICAL
> **Risk:** Ödeme yapmadan sipariş tamamlama (payment bypass fraud)
> **Affected:** `catalog/controller/extension/payment/qnbpay.php` → `validation()` method (satır 587-671)

---

## Sorun

```php
$hashControl = 0;            // ← Hash doğrulama DEVRE DIŞI
if ($hashControl) {
    $x = qnbpay::validateHashKey(...);
    // ... (asla çalışmaz)
}

// Bu satıra direkt geliyor — hash kontrol YOK:
if ($status_code != '100') {
    // hata
} else {
    // ödeme başarılı kabul edilir, sipariş "complete" status'a geçer
    $this->model_checkout_order->addOrderHistory($order_id, ...);
}
```

**Saldırı:** Saldırgan kart bilgisi girmeden, doğrudan `/index.php?route=extension/payment/qnbpay/validation` adresine POST atar:
```
status_code=100
invoice_id=<order_id>_XYZ
amount=1.00
currency=TRY
```
Sistem hash kontrol etmediği için "ödendi" kabul eder, sipariş tamamlanır, mal sevk edilir.

## Fix Stratejisi

1. `$hashControl` değişkenini kaldır — koşulsuz hash kontrolü
2. Hash key yoksa veya geçersizse REDIRECT (404 değil, kullanıcı UX bozulmasın)
3. Hash key + status + invoice eşleşmesi kontrol et
4. **Defense-in-depth:** QNB API'ye `checkStatus()` ile de sor (webhook'taki pattern aynısı)
5. Tüm doğrulamalar geçtikten sonra sipariş "complete"

## ÖNCESİ (Mevcut Kod)

```php
public function validation()
{
    $this->load->model('checkout/order');
    $this->language->load('extension/payment/qnbpay');
    $this->load->model('extension/payment/qnbpay');

    $qnbpay = new qnbpay(
        $this->config->get('payment_qnbpay_app_key'),
        $this->config->get('payment_qnbpay_app_secret'),
        $this->config->get('payment_qnbpay_merchant_key'),
        $this->config->get('payment_qnbpay_sale_web_hook_key'),
        $this->config->get('payment_qnbpay_recurring_web_hook_key'),
        $this->config->get('payment_qnbpay_environment'),
        $this->config->get('payment_qnbpay_debug')
    );

    $p = $this->request->post;
    $hashControl = 0;
    if ($hashControl) {
        $x = qnbpay::validateHashKey($this->request->get['hash_key'], $this->config->get('payment_qnbpay_app_secret'));
        if (($x[0] != $this->request->get["qnbpay_status"]) or ($x[2] != $this->request->get["invoice_id"])) {
            $error_msg = "Sipariş işlemi tamamlanamadı. Hash kodu uyumlu değil.";
            $this->session->data['error'] = $error_msg;
            if ($this->config->get('payment_qnbpay_debug')) {
                dump([$this->session->data['error'], $x, $this->request->get]);
                exit;
            }
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }
    }

    // Status code kontrolü - POST veya GET'ten gelebilir
    $status_code = isset($this->request->post["status_code"]) ? $this->request->post["status_code"] : (isset($this->request->get["status_code"]) ? $this->request->get["status_code"] : '');
    $status_description = isset($this->request->post["status_description"]) ? $this->request->post["status_description"] : (isset($this->request->get["status_description"]) ? $this->request->get["status_description"] : '');
    
    if ($status_code != '100') {
        $error_msg = "Ödeme İşlemi Tamamlanamadı. (" . $status_code . " : " . $status_description . ")";
        $this->session->data['error'] = $error_msg;
        $this->response->redirect($this->url->link('checkout/checkout', '', true));
        return;
    }

    // ... (devam — sipariş tamamlanıyor)
```

## SONRASI (Yamalı Kod)

```php
public function validation()
{
    $this->load->model('checkout/order');
    $this->language->load('extension/payment/qnbpay');
    $this->load->model('extension/payment/qnbpay');

    $qnbpay = new qnbpay(
        $this->config->get('payment_qnbpay_app_key'),
        $this->config->get('payment_qnbpay_app_secret'),
        $this->config->get('payment_qnbpay_merchant_key'),
        $this->config->get('payment_qnbpay_sale_web_hook_key'),
        $this->config->get('payment_qnbpay_recurring_web_hook_key'),
        $this->config->get('payment_qnbpay_environment'),
        $this->config->get('payment_qnbpay_debug')
    );

    // GET ve POST'tan oku — QNB callback hem GET hem POST gönderebilir
    $hashKey            = $this->request->get['hash_key']       ?? $this->request->post['hash_key']       ?? '';
    $qnbpayStatus       = $this->request->get['qnbpay_status']  ?? $this->request->post['qnbpay_status']  ?? '';
    $invoiceId          = $this->request->get['invoice_id']     ?? $this->request->post['invoice_id']     ?? '';
    $status_code        = $this->request->get['status_code']    ?? $this->request->post['status_code']    ?? '';
    $status_description = $this->request->get['status_description'] ?? $this->request->post['status_description'] ?? '';

    // [PATCH 01] Hash key kontrolü — ZORUNLU
    if (empty($hashKey)) {
        $this->session->data['error'] = "Ödeme doğrulanamadı: hash key eksik.";
        $this->response->redirect($this->url->link('checkout/checkout', '', true));
        return;
    }

    $hashParts = qnbpay::validateHashKey($hashKey, $this->config->get('payment_qnbpay_app_secret'));
    // validateHashKey döner: [status, total, invoiceId, orderId, currencyCode]

    if (empty($hashParts[2]) || $hashParts[2] != $invoiceId) {
        // invoice_id hash içinde yoksa veya eşleşmiyorsa: sahte istek
        $this->session->data['error'] = "Ödeme doğrulanamadı: invoice eşleşmedi.";
        $this->response->redirect($this->url->link('checkout/checkout', '', true));
        return;
    }

    if (!empty($qnbpayStatus) && $hashParts[0] != $qnbpayStatus) {
        // status hash içinde başka, request'te başka: sahte
        $this->session->data['error'] = "Ödeme doğrulanamadı: status eşleşmedi.";
        $this->response->redirect($this->url->link('checkout/checkout', '', true));
        return;
    }

    // Status code kontrolü
    if ($status_code != '100') {
        $this->session->data['error'] = "Ödeme İşlemi Tamamlanamadı. (" . htmlspecialchars($status_code) . " : " . htmlspecialchars($status_description) . ")";
        $this->response->redirect($this->url->link('checkout/checkout', '', true));
        return;
    }

    // Invoice ID'den order_id'yi çıkar
    $order_id = isset($this->session->data['order_id']) ? $this->session->data['order_id'] : 0;
    if ($order_id == 0 && $invoiceId != '') {
        $explode = explode('_', $invoiceId);
        $order_id = isset($explode[0]) ? (int)$explode[0] : 0;
    }

    if ($order_id == 0) {
        $this->session->data['error'] = "Sipariş ID bulunamadı. Lütfen müşteri hizmetleri ile iletişime geçin.";
        $this->response->redirect($this->url->link('checkout/checkout', '', true));
        return;
    }

    // [PATCH 01] DEFENSE IN DEPTH: QNB API'ye tekrar sor
    $qnbpay->getToken();
    $statusResult = $qnbpay->checkStatus($order_id);
    $apiStatus = is_array($statusResult) ? ($statusResult['status_code'] ?? null) : (isset($statusResult->status_code) ? $statusResult->status_code : null);

    if ($apiStatus != '100') {
        // QNB tarafında ödeme yok / başarısız
        $this->session->data['error'] = "Ödeme QNB tarafında onaylanamadı. Sipariş tamamlanmadı.";
        $this->response->redirect($this->url->link('checkout/checkout', '', true));
        return;
    }

    // Tüm kontroller geçti — sipariş başarılı
    $this->session->data['order_id'] = $order_id;

    $message = "Kredi Kartı Ödeme Başarılı";
    $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_qnbpay_order_status_id'), $message, false);

    // Transaction kaydı oluştur
    $object                = new stdClass();
    $object->order_id      = $order_id;
    $object->status        = 'success';
    $object->amount        = $this->request->post['amount'] ?? '';
    $object->currency      = $this->request->post['currency'] ?? '';
    $object->type          = 'sale';
    $object->reference     = $this->session->data['qnbpay_paymentid'] ?? '';
    $object->operation     = '';
    $object->transactionId = $this->session->data['qnbpay_paymentid'] ?? '';
    $object->message       = $message;
    $object->code          = $status_code;
    $object->purchase_url  = '';
    $this->model_extension_payment_qnbpay->appendi((array)$object);

    $this->response->redirect($this->url->link('checkout/success', '', true));
}
```

## Değişiklik Özeti

| Değişiklik | Açıklama |
|---|---|
| `$hashControl = 0;` SİLİNDİ | Hash kontrolü artık opsiyonel değil, zorunlu |
| Hash key empty kontrolü | Yoksa redirect — sahte request reddedilir |
| validateHashKey çağrısı | invoice_id ve status karşılaştırması |
| `checkStatus()` çağrısı eklendi | QNB API'ye tekrar soruluyor (webhook ile aynı pattern) |
| `htmlspecialchars()` | Error mesajındaki status_description XSS önleme |
| Null coalesce operator (`??`) | PHP 7.0+ — temiz syntax |

## Test Adımları

### Pozitif test (legitim ödeme — çalışmalı)
1. Normal checkout akışı
2. QNB Pay'de ödeme yap
3. QNB callback `/validation` çağırır
4. Hash + invoice + status_code=100 + API checkStatus=100
5. ✅ Sipariş "complete" status'una geçer
6. checkout/success sayfası

### Negatif test 1 (sahte POST — engellenmeli)
```bash
curl -X POST "https://test.ravendentalgroup.com/index.php?route=extension/payment/qnbpay/validation" \
  -b "OCSESSID=<test_session>" \
  -d "status_code=100&invoice_id=12345_X"
# Beklenen: redirect to /checkout/checkout, "hash key eksik" hatası
```

### Negatif test 2 (yanlış hash — engellenmeli)
```bash
curl -X POST "https://test.ravendentalgroup.com/index.php?route=extension/payment/qnbpay/validation" \
  -d "status_code=100&invoice_id=12345_X&hash_key=SAHTE_HASH"
# Beklenen: "hash key invoice eşleşmedi" hatası
```

### Negatif test 3 (gerçek hash ama farklı invoice — engellenmeli)
- Saldırgan kendi hash'ini elde eder, başka order_id'ye uygular
- Hash içindeki invoice_id ile URL'deki invoice_id farklı
- Beklenen: "invoice eşleşmedi" hatası

### Negatif test 4 (hash + invoice doğru ama QNB API "ödenmedi" dönüyor)
- Race condition / replay attack senaryosu
- checkStatus() reddetmeli
- Beklenen: "QNB tarafında onaylanamadı" hatası

## Risk

### Uygulama Riski
- ⚠️ Hash key URL parametresinde QNB callback'inde gelmeli — eğer QNB callback bunu göndermiyorsa, **legitim ödemeler de reddedilir**.
- **Mitigation:** Test ortamında sandbox QNB ile gerçek bir sipariş yap. Hash key gerçekten geliyor mu doğrula. Gelmiyorsa QNB merchant ayarlarında etkinleştirilmesi gerekebilir.

### Geri Dönüş
- 5 dakika içinde rollback mümkün — eski dosyayı (`.bak-YYYYMMDD`) geri yükle

### Customer Experience
- **Negatif değişim YOK** — legitim kullanıcı akışı aynı
- Hata mesajları biraz daha açıklayıcı (debug için)

## Dependency

Bu yama tek başına uygulanır. 02 ve 03'ten bağımsız.
