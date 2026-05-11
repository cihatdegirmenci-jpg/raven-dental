# Patch 02 — deletemycard() XSS + Open Redirect + Authorization Fix

> **Severity:** 🔴 CRITICAL
> **Risk:** XSS (cookie hijack), open redirect, unauthorized card deletion
> **Affected:** `catalog/controller/extension/payment/qnbpay.php` → `deletemycard()` (satır 765-790)

---

## Sorun

```php
public function deletemycard()
{
    $this->qnbpay = new qnbpay(...);
    $cardToken = $this->request->get['card_token'];   // ← Validation yok
    $customer_id = $this->customer->isLogged() ? $this->customer->getId() : 0;
    $this->qnbpay->deleteStoredCard($cardToken, $customer_id);  // ← Misafir (0) ile çağrı yapılabilir

    $redirectHtml = '<script>
        setTimeout(function(){
            window.location.href = "' . $_SERVER['HTTP_REFERER'] . '"   // ← XSS injection vector
        }, 1000);
    </script>';
    die($redirectHtml);
}
```

### 3 ayrı sorun

1. **XSS:** `$_SERVER['HTTP_REFERER']` HİÇBİR sanitize olmadan inline `<script>` içine gömülüyor. Saldırgan referer header'ını şöyle ayarlarsa:
   ```
   Referer: ";alert(document.cookie);//
   ```
   Tarayıcıda `window.location.href = "";alert(document.cookie);//"` çalışır.

2. **Open Redirect:** Aynı şekilde `Referer: https://evil.com` ile kullanıcı saldırgan sitesine yönlendirilebilir (phishing).

3. **Unauthorized Action:** `card_token` kullanıcıdan geliyor (URL parametresi). Misafir (customer_id=0) bile arbitrary card_token ile delete API çağrısı yapabilir. QNB API tarafında customer_id-card_token eşleşmesi kontrol ediliyor mu BİLİNMİYOR. Risk var.

## Fix Stratejisi

1. **Login zorunlu** — misafir kullanıcı kart silemez (mantıken zaten kartı yok)
2. **card_token validation** — sadece alfanumeric + dash + underscore karakterleri (QNB token formatı)
3. **HTTP_REFERER güvenli kullanım:**
   - Sadece kendi domain'imizden gelen Referer kabul edilir
   - Aksi halde `account/account` sayfasına yönlendir
4. **Inline `<script>` yerine HTTP redirect** — XSS yüzeyi yok
5. **Optionally:** CSRF token kontrolü (Faz 2'de POST + CSRF)

## SONRASI (Yamalı Kod)

```php
public function deletemycard()
{
    // [PATCH 02] Login zorunlu
    if (!$this->customer->isLogged()) {
        $this->response->redirect($this->url->link('account/login', '', true));
        return;
    }

    // [PATCH 02] card_token validation
    $cardToken = $this->request->get['card_token'] ?? '';
    if (empty($cardToken) || !preg_match('/^[a-zA-Z0-9\-_]{8,128}$/', $cardToken)) {
        // Geçersiz token formatı — saldırı veya bot
        $this->response->redirect($this->url->link('account/account', '', true));
        return;
    }

    $this->qnbpay = new qnbpay(
        $this->config->get('payment_qnbpay_app_key'),
        $this->config->get('payment_qnbpay_app_secret'),
        $this->config->get('payment_qnbpay_merchant_key'),
        $this->config->get('payment_qnbpay_sale_web_hook_key'),
        $this->config->get('payment_qnbpay_recurring_web_hook_key'),
        $this->config->get('payment_qnbpay_environment'),
        $this->config->get('payment_qnbpay_debug')
    );

    $customer_id = $this->customer->getId();
    $this->qnbpay->deleteStoredCard($cardToken, $customer_id);

    // [PATCH 02] Güvenli redirect — sadece same-origin referer kabul edilir
    $referer = $this->request->server['HTTP_REFERER'] ?? '';
    $host = $this->request->server['HTTP_HOST'] ?? '';
    $allowedReferer = '';
    if (!empty($referer) && !empty($host)) {
        // Same-origin kontrolü
        $refererHost = parse_url($referer, PHP_URL_HOST);
        if ($refererHost && $refererHost === $host) {
            $allowedReferer = $referer;
        }
    }
    
    if (!empty($allowedReferer)) {
        $this->response->redirect($allowedReferer);
    } else {
        $this->response->redirect($this->url->link('account/account', '', true));
    }
}
```

## Değişiklik Özeti

| Değişiklik | Sebep |
|---|---|
| `isLogged()` kontrolü | Misafir kart silemez |
| `preg_match` ile token regex | Garbage/injection token reddedilir |
| `parse_url` + same-origin | Open redirect engellenir |
| `$this->response->redirect()` | HTTP 302 — JS yok, XSS yüzeyi yok |
| `die($redirectHtml)` KALDIRILDI | XSS source removed |
| `$this->request->server` | OpenCart wrapper (daha güvenli) |

## Test Adımları

### Pozitif test 1 (login kullanıcı kendi kartını siler)
1. Login yap
2. Hesabım → Kayıtlı Kartlar
3. Bir kartın yanında "Sil" butonu
4. Tıkla → `?card_token=ABC123`
5. ✅ Kart silinir
6. Referer aynı domain → o sayfaya geri dön

### Pozitif test 2 (manuel URL ile direct erişim, login'liyken)
```
https://test.ravendentalgroup.com/index.php?route=extension/payment/qnbpay/deletemycard&card_token=ABC123
```
- Tarayıcıdan direct ziyaret
- Referer yok veya farklı host
- ✅ Kart silinir + `/index.php?route=account/account`'a yönlendirir

### Negatif test 1 (misafir — engellenmeli)
- Logout
- Direct URL ziyaret
- Beklenen: `/index.php?route=account/login`'e yönlendirir
- Kart silinmez

### Negatif test 2 (XSS PoC — engellenmeli)
```bash
curl -X GET "https://test.ravendentalgroup.com/index.php?route=extension/payment/qnbpay/deletemycard&card_token=ABC123" \
  -H 'Referer: ";alert(1);//' \
  -b "OCSESSID=<login_session>"
# Beklenen: HTTP 302 redirect — XSS payload görmezden gelinir, account sayfasına
```

### Negatif test 3 (Open redirect PoC — engellenmeli)
```bash
curl -X GET "https://test.ravendentalgroup.com/.../deletemycard&card_token=ABC123" \
  -H 'Referer: https://evil.com/phishing'
# Beklenen: HTTP 302 → /index.php?route=account/account (NOT evil.com)
```

### Negatif test 4 (geçersiz token — engellenmeli)
```bash
curl "...deletemycard&card_token=<script>alert(1)</script>"
# Beklenen: 302 → account/account (token regex'i geçemez)
```

## Risk

### Uygulama Riski
- Eğer mevcut UI'da `<a href="...">` link kullanıcı tıkladıktan sonra `Referer` aynı domain oluyor, **legitim akış bozulmaz**.
- AJAX ile çağrılıyorsa: AJAX `XMLHttpRequest`'in Referer header'ı default olarak gönderilmez veya boş olur. Bu durumda `/account/account`'a fallback olur — kullanıcı kart silindiğini görmek için sayfayı manuel refresh edebilir.
- **Mitigation:** Test ortamında UI flow'unu doğrula.

### Geri Dönüş
- 5 dakika — eski dosyayı `.bak-YYYYMMDD`'den geri yükle

### Customer Experience
- ⚠️ AJAX-bazlı silmede page reload gerekebilir (test edilmeli)
- Diğer akış aynı

## İleri İyileştirme (Faz 2)

- POST request + CSRF token (GET ile destructive action best practice değil)
- Audit log: kim hangi card_token'ı sildi (oc_qnbpay_audit tablosu?)
- Card ownership pre-check: silmeden önce yerel DB'de kontrol et (eğer cached card list varsa)

## Dependency

Bu yama tek başına uygulanır. 01 ve 03'ten bağımsız.
