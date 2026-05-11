# 02 - Güvenlik İncelemesi (QNB Pay Hariç)

**Tarih:** 2026-05-12
**Kapsam:** OpenCart 3.0.3.8 core (custom alanlar dahil) + Journal3 v3.1.12 + custom .htaccess + dental-spesifik kod. **QNB Pay modülü ayrı dökümante edildi** (`qnb-pay-security-audit.md`), burada **tekrar edilmiyor**.
**Yaklaşım:** Sample-based, custom kod öncelikli, vendor kod özetle.

> **Notasyon:** 🔴 CRITICAL = doğrudan exploit/kanıt | 🟠 HIGH = yüksek olasılık | 🟡 MEDIUM = uygun koşulda exploit | 🟢 LOW/INFO = best practice / defense-in-depth | "Risk:" = ispatsız varsayım

---

## 1. Özet (TL;DR)

Sonradan dokunulan custom alanlar (Journal3 customCodeHeader inject, .htaccess hardening, header.twig H1) **kendi başlarına yeni güvenlik açığı eklemiyor** — JSON-LD ve hreflang düzgün escape edilmiş, header.twig'deki Twig auto-escape üretici. Ancak OpenCart 3.0.3.8 vanilla kodunda **6 doğrulanmış ve birden fazla high/medium seviye** açık var:

1. 🔴 **Parola hash'leme zayıf**: SHA1(salt+SHA1(...)) ile düz MD5 fallback — bilinen ve hızlıca kırılabilir.
2. 🔴 **Session cookie HttpOnly/Secure/SameSite YOK** — `setcookie(...)` 5 argümanla çağrılıyor; `php.ini` ayarı bypass ediliyor.
3. 🟠 **Session fixation** — login sonrası session ID rotate edilmiyor.
4. 🟠 **Open redirect** — `/index.php?route=common/currency/currency` ve `common/language/language` POST `redirect` parametresini valid hostname kontrolü yapmadan kullanıyor (phishing zinciri için kullanılabilir).
5. 🟠 **CSRF yok** — Catalog tarafında (`account/edit`, `account/address/edit`, `register`, login) hiçbir POST formunda CSRF token yok.
6. 🟡 **Brute-force koruması zayıf** — `config_login_attempts` saatte X deneme username başına; IP başına değil + captcha yok.
7. 🟡 **DOM-XSS (self) qnbpay-script.js'de** — kart sahibi adı `innerHTML` ile yazılıyor; PCI form context'inde önemsiz değil.
8. 🟡 **XXE riski OCMOD modification XML işlemede** — `DOMDocument::loadXml()` `LIBXML_NONET` kullanılmadan; admin-only ama yine de.
9. 🟡 **Host header injection** — `startup.php` `$_SERVER['HTTP_HOST']`'u doğrudan kullanıyor.
10. 🟢 **CSP yok** (.htaccess'te), inline JS+eval ile sıkı CSP zor — en az `frame-ancestors` ve `form-action` eklenebilir.

---

## 2. .htaccess İncelemesi

**Dosya:** `/Users/ipci/raven-dental/code/_dotfile_htaccess` (canlıda `/public_html/.htaccess`)

### 2.1 🟢 Mevcut hardening (iyi)
- `<FilesMatch>` ile `zip|bak|sql|env|log|sh|yml|...` blok
- Dotfile (`^\.`) blok
- `config.php` / `admin/config.php` blok
- `Options -Indexes -MultiViews`
- HSTS, X-Frame-Options=SAMEORIGIN, X-Content-Type-Options=nosniff, Referrer-Policy, Permissions-Policy
- X-Powered-By unset, ETag kapalı
- mod_rewrite QUERY_STRING'de basit script/iframe/base64 ön-filtre

### 2.2 🟡 MEDIUM — CSP başlığı yok

```apache
# Mevcut header bloğunda yok:
# Content-Security-Policy
```

**Risk:** Bir XSS payload yansıdığında hiçbir kısıtlama yok. Journal3 inline `<script>` ve `<style>` blokları yüzünden sıkı CSP zor, ama minimal frame/form koruması mümkün.

**Önerilen:**

```apache
<IfModule mod_headers.c>
    # Mevcut header'lara ek:
    # Sıkı değil, sadece frame ve form clickjack/redirect engelle
    Header set Content-Security-Policy "frame-ancestors 'self'; form-action 'self' https://*.qnbpay.com.tr https://qnbpay.com.tr; base-uri 'self';"

    # Cookie güvenlik flag'leri (eğer mod_session kuruluysa)
    # Daha temizi PHP tarafında session.php düzeltmesi (Finding 4.2'ye bak)
</IfModule>
```

**Test:**
```bash
curl -sI https://ravendentalgroup.com/ | grep -i "content-security-policy"
# Beklenen: header görünür
```

### 2.3 🟡 MEDIUM — QUERY_STRING regex blok'u eksik desenler

Mevcut:
```apache
RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} (\<|%3C).*iframe.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} base64_(en|de)code\(.*\) [NC]
RewriteRule ^(.*)$ - [F,L]
```

**Risk:** Bypass kolay:
- Double-encoding: `%253Cscript%253E` → `%3Cscript%3E` → `<script>` (mevcut regex %3C yakalar ama %253C yakalamaz)
- Whitespace varyantları: `<svg/onload=...>`, `<img src=x onerror=...>`
- Çeşitli vektörler (`javascript:`, `data:`, `vbscript:`) yok
- POST body kontrol etmiyor (mod_rewrite sadece query string)

**Öneri:** Mod_security veya ModSecurity OWASP CRS taşıma fikri olmalı. WAF (Cloudflare) bu işi çok daha iyi yapar. **Bu regex'ler sahte güven veriyor** — gerçek koruma WAF + uygulama-seviye sanitization.

### 2.4 🟢 INFO — Admin dizinine IP whitelist yok

**Mevcut:** `/admin/` URL'ine herkes erişebilir. Login ekranı yeterli ama defense-in-depth eksik.

**Önerilen (VPS sonrası):**

```apache
# .htaccess'e (veya admin/.htaccess olarak):
<Directory "/home/ravenden/public_html/admin">
    <RequireAll>
        Require all denied
        # Çalışan IP'ler (CIDR olabilir):
        Require ip 185.X.Y.0/24
        Require ip 78.X.Y.Z
        # Geçici testler için:
        # Require ip <kullanıcı_IP>
    </RequireAll>
</Directory>
```

**Alternatif:** Admin path obfuscation:

1. `/admin/` → `/yonetim-X9K2/` rename (config.php DIR sabitleri güncelle)
2. Modification engine refresh
3. Backup almadan yapma

Bu rename kötüye kullanıma karşı bot/scanner'ları yavaşlatır, ama auth gücünü artırmaz.

### 2.5 🟢 INFO — robots.txt admin'i exposing

**Dosya:** `/Users/ipci/raven-dental/code/robots.txt:20`

```
Disallow: /admin/
```

**Risk:** Saldırgan adversary robots.txt'ye bakıp `/admin/` URL'ini bulur. Bot tarayıcı için bu küçük yardım.

**Öneri:** `Disallow: /admin/` satırını **kaldır** (admin sayesinde sitemap'te zaten yok, indekslenmeyecek). Bunun yerine `<meta name="robots" content="noindex">` admin sayfalarına eklenmiş zaten.

---

## 3. OpenCart Core — Custom Konfigürasyon ile Etkileşim

### 3.1 🔴 CRITICAL — Parola hash'leme SHA1+MD5 fallback

**Dosya:** `/Users/ipci/raven-dental/code/system/library/cart/customer.php:49`
**Dosya:** `/Users/ipci/raven-dental/code/system/library/cart/user.php:40`

```php
$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user
    WHERE username = '" . $this->db->escape($username) . "'
    AND (
        password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "')))))
        OR password = '" . $this->db->escape(md5($password)) . "'
    )
    AND status = '1'");
```

**Risk:**
- **SHA1 collision attack** (SHAttered, 2017) ile teorik olarak bozulabilir; pratik olarak password DB'si sızarsa GPU'lu hashcat ile saniyede milyarlarca deneme yapılabilir.
- **MD5 fallback** çok daha kötü — 2020'den bu yana MD5 trivial olarak kırılabilir. Eski OpenCart 1.x'ten upgrade edilmiş hesaplar hâlâ MD5 olabilir.
- **SQL'de hash hesaplaması** — password DB sunucusunda hashlanıyor, query log'una düz parola sızabilir (eğer slow query log açıksa).
- DB dökümü çalınırsa: yöneticilerin ve müşterilerin parolaları kısa sürede ele geçer. OpenCart customer base'i ~yüzlerce kayıt — rainbow table ile dakikalar içinde tüm zayıf parolalar kırılır.

**Etki kapsamı:**
- `oc_user` (admin/staff users) — kritik
- `oc_customer` (müşteriler) — KVKK + tekrar kullanım (password reuse)

**Çözüm (önerilen, kısa vadeli):**
1. **VPS migration'da** bcrypt'a geçen 3rd-party OCMOD yükle. Topluluk extension: "Argon2 / Bcrypt password upgrade" var.
2. Yeni shema:
   ```sql
   ALTER TABLE oc_customer ADD COLUMN password_v2 VARCHAR(255) NULL AFTER password;
   ALTER TABLE oc_user ADD COLUMN password_v2 VARCHAR(255) NULL AFTER password;
   ```
3. Login akışına ek (Cart\User::login pseudocode):
   ```php
   if ($row['password_v2'] && password_verify($password, $row['password_v2'])) {
       // başarı
   } else if ($legacy_match) {
       // Eski hash doğru — yeni bcrypt hash kaydet:
       $new_hash = password_hash($password, PASSWORD_BCRYPT);
       $this->db->query("UPDATE oc_user SET password_v2 = '" . $this->db->escape($new_hash) . "' WHERE user_id = ...");
   }
   ```
4. 1-2 ay sonra MD5/SHA1 sütununu drop et.

**Acil çözüm (kısıtlı):** Tüm admin kullanıcıları "şifreni değiştir" zorunluluğuyla yenile + güçlü parola politikası (min 16 char). Bu OCMOD/eklenti gerektirir.

**Test senaryosu (PoC, gerçek atak değil — sadece doğrula):**
```sql
-- DB'de bir test kullanıcısı oluştur, sonra:
SELECT username, password, salt FROM oc_user WHERE username = 'admin';
-- password sütunu 40 karakterlik SHA1 hex döndürüyor mu?
-- → Evet ise: zayıf.
```

### 3.2 🔴 CRITICAL — Session cookie HttpOnly / Secure / SameSite YOK

**Dosya:** `/Users/ipci/raven-dental/code/system/framework.php:112`
**Dosya:** `/Users/ipci/raven-dental/code/catalog/controller/startup/session.php:25`

```php
setcookie(
    $config->get('session_name'),
    $session->getId(),
    ini_get('session.cookie_lifetime'),
    ini_get('session.cookie_path'),
    ini_get('session.cookie_domain')
);
// → 5-arg setcookie() — secure/httponly/samesite yok!
```

**Risk:**
- `setcookie()`'nin 5 parametre formu kullanılmış → 6. (secure), 7. (httponly) ve options array (samesite) belirtilmiyor.
- `php.ini` ayarındaki `session.cookie_httponly = On` **sadece PHP'nin kendi `session_start()`'unda etkili**. OpenCart manual `setcookie()` yaptığı için bu ayar **uygulanmıyor**.
- Sonuç: JavaScript `document.cookie` üzerinden session ID okuyabilir → XSS'le tek aşamada hesap ele geçirme.
- Secure flag yok → HTTP fallback yapan saldırgan (örn. SSLStrip, downgrade) cookie'yi clear-text görür.
- SameSite yok (default Lax modern browser'larda devreye girer ama eski browser'larda CSRF saldırılarına açık).

**Test (PoC):**
```bash
curl -sI 'https://ravendentalgroup.com/' | grep -i 'set-cookie'
# Beklenen sonuç (şu anda):
# set-cookie: PHPSESSID=abc...; path=/
# Yani HttpOnly/Secure/SameSite YOK.
```

**Çözüm (kod yaması):**

```php
// system/framework.php satır 112 ve catalog/controller/startup/session.php satır 25:

// PHP 7.3+:
setcookie(
    $config->get('session_name'),
    $session->getId(),
    array(
        'expires'  => ini_get('session.cookie_lifetime') ? time() + (int)ini_get('session.cookie_lifetime') : 0,
        'path'     => ini_get('session.cookie_path') ?: '/',
        'domain'   => ini_get('session.cookie_domain') ?: '',
        'secure'   => !empty($_SERVER['HTTPS']),  // HTTPS'de zorla
        'httponly' => true,                        // JS erişimini engelle
        'samesite' => 'Lax',                       // CSRF koruması
    )
);
```

**Not:** Aynı pattern admin tarafında da var — `admin/controller/startup/session.php` (varsa) ve aynı kontrol yapılmalı.

```bash
# Tüm setcookie örneklerini incele:
grep -rn "setcookie" /home/ravenden/public_html/system/ /home/ravenden/public_html/catalog/controller/startup/
```

### 3.3 🟠 HIGH — Session fixation (login sonrası ID rotate yok)

**Dosya:** `/Users/ipci/raven-dental/code/system/library/cart/user.php:39-63`
**Dosya:** `/Users/ipci/raven-dental/code/system/library/cart/customer.php:45-70`

`login()` metodu başarıyla çalıştığında `$this->session->data['user_id'] = ...` yapıyor ama **session_regenerate_id() çağrılmıyor**. Mevcut session ID korunuyor.

**Risk:**
1. Saldırgan kurbana sahte bir session ID veriyor (`?PHPSESSID=ABC123` URL veya cookie injection).
2. Kurban giriş yapıyor — aynı session ID artık authenticated.
3. Saldırgan aynı session ID ile authenticated state'i kullanır.

**Çözüm (önerilen):**

```php
// Cart\User::login() içinde (system/library/cart/user.php:42-44 arasına):
if ($user_query->num_rows) {
    // Session fixation koruması:
    $old_session_data = $this->session->data;
    $this->session->destroy();
    $new_id = $this->session->start();
    $this->session->data = $old_session_data;

    $this->session->data['user_id'] = $user_query->row['user_id'];
    // ... mevcut kod
}
```

Aynısı `Cart\Customer::login()` için de uygulanmalı.

**Not:** OpenCart'ın session API'sinde `regenerate_id` direkt yok — `destroy()` + `start()` benzer etkiyi verir. Veri kaybı olmasın diye önce data backup gerekli.

**Test:**
```bash
# 1. Sahte session ID üret:
SESSION="aaaaaaaaaaaaaaaaaaaaaa"
curl -c cookies.txt -b "OCSESSID=$SESSION" https://ravendentalgroup.com/

# 2. Login yap (cookies.txt ile):
curl -c cookies.txt -b cookies.txt -X POST \
  -d "email=test@x.com&password=test123" \
  https://ravendentalgroup.com/index.php?route=account/login

# 3. cookies.txt'i kontrol et — OCSESSID hâlâ aaaa...aa mi?
# Mevcut: EVET (fixation) — düzeltme: farklı.
```

### 3.4 🟠 HIGH — Open redirect: currency/language

**Dosya:** `/Users/ipci/raven-dental/code/catalog/controller/common/currency.php:58`
**Dosya:** `/Users/ipci/raven-dental/code/catalog/controller/common/language.php:53`

```php
public function currency() {
    if (isset($this->request->post['code'])) {
        $this->session->data['currency'] = $this->request->post['code'];
    }
    if (isset($this->request->post['redirect'])) {
        $this->response->redirect($this->request->post['redirect']);  // ❌ HİÇBİR DOĞRULAMA YOK
    }
    ...
}
```

**Risk:** Saldırgan phishing maili gönderiyor:
```
https://ravendentalgroup.com/index.php?route=common/currency/currency
```
Form POST'unda `redirect=https://r4venden-secure.evil.com/login` → kurban yasal domain'den başlayıp tıklıyor, sahte siteye yönleniyor. Pop-up tüm form alanları kopyalanmış, kullanıcı kimlik bilgilerini giriyor.

**Karşılaştırma:** `account/login.php:79` daha güvenli — `strpos(..., $this->config->get('config_url')) !== false` kontrolü yapıyor (ama bu da prefix kontrolü değil, **substring** kontrolü, yine bypass'lı: `https://ravendentalgroup.com.evil.com/...` çalışır).

**Çözüm:**

```php
// catalog/controller/common/currency.php:58 → değiştir:
if (isset($this->request->post['redirect'])) {
    $redirect = $this->request->post['redirect'];

    // Sadece kendi domain'imize redirect'e izin ver:
    $allowed_prefixes = array(
        $this->config->get('config_url'),
        $this->config->get('config_ssl'),
    );
    $is_valid = false;
    foreach ($allowed_prefixes as $prefix) {
        if ($prefix && strpos($redirect, $prefix) === 0) {  // strpos === 0 (başlangıç, substring değil!)
            $is_valid = true;
            break;
        }
    }
    // Veya: relative path'e izin ver:
    if (!$is_valid && substr($redirect, 0, 1) === '/' && substr($redirect, 0, 2) !== '//') {
        $is_valid = true;
    }

    if ($is_valid) {
        $this->response->redirect($redirect);
        return;
    }
}
$this->response->redirect($this->url->link('common/home'));
```

Aynı düzeltme `common/language.php:53`'e de uygulanmalı.

**Bonus:** `account/login.php:79` ve `affiliate/login.php:19` ile `admin/controller/common/login.php:17`'de aynı **substring** kontrolü var → `strpos(..., $config_url) !== false` yerine `strpos(..., $config_url) === 0` kullanılmalı.

**Test PoC:**
```bash
# Mevcut (vulnerable):
curl -i -X POST \
  -d "code=USD&redirect=https://evil.example.com/login" \
  https://ravendentalgroup.com/index.php?route=common/currency/currency
# Beklenen response: 302 Location: https://evil.example.com/login
```

### 3.5 🟠 HIGH — CSRF token yok (catalog tarafı tüm POST formları)

**Etkilenen:**
- `catalog/controller/account/edit.php` — profil bilgisi güncelleme
- `catalog/controller/account/address.php` (add/edit/delete) — adres CRUD
- `catalog/controller/account/password.php` — parola değiştirme
- `catalog/controller/account/register.php` — kayıt
- `catalog/controller/account/login.php` — giriş
- `catalog/controller/account/newsletter.php` — abone
- `catalog/controller/common/currency.php` ve `common/language.php` — currency/lang switch
- `catalog/controller/checkout/*` — checkout adımları

**Doğrulama:**
```bash
grep -rn "csrf\|_token\|nonce" /Users/ipci/raven-dental/code/catalog/controller/account/ 2>/dev/null
# → tek bir match bile yok
```

**Risk:**
Saldırgan kötü niyetli bir blog post veya forum mesajına şu HTML'i koyar:
```html
<img src="https://evil.com/x.gif" onload="
  fetch('https://ravendentalgroup.com/index.php?route=account/edit', {
    method: 'POST',
    credentials: 'include',
    body: new URLSearchParams({
      firstname: 'Hacked',
      lastname: 'User',
      email: 'attacker@evil.com',
      telephone: '...',
    })
  })
">
```

Kurban siteye giriş yapmış durumda ise, profil bilgisi değiştirilir, email "attacker@evil.com" olur → forgotten password → hesap takeover.

**Modern tarayıcılar default `SameSite=Lax` cookie davranışıyla bunu kısmen koruyor**. Ama:
- Eski Safari, IE/Edge versiyonları
- POST → SameSite=Lax'ın koruduğu yer **top-level navigation değil image/fetch**. Lax modunda `<img src=...>` ile GET POST işe yarayan eski exploitler hâlâ var.
- `Set-Cookie` header'larında SameSite eklenmediği için (Finding 3.2) modern browserda da koruma "Lax" değil "None" — yani aktif CSRF.

**Çözüm:**
- **Kısa vade (1 hafta):** Cookie SameSite=Lax flag'i ekle (Finding 3.2 düzeltmesi). Bu çoğu CSRF'i durdurur.
- **Orta vade (1 ay+):** CSRF token mekanizması kur. OpenCart 4'te varsayılan, ama 3.x'te 3rd-party OCMOD lazım. Veya custom basit:
  ```php
  // catalog/controller/startup/csrf.php (yeni):
  if (empty($this->session->data['csrf_token'])) {
      $this->session->data['csrf_token'] = bin2hex(random_bytes(16));
  }

  if ($this->request->server['REQUEST_METHOD'] === 'POST') {
      $sent = $this->request->post['csrf_token'] ?? '';
      if (!hash_equals($this->session->data['csrf_token'], $sent)) {
          return new Action('error/not_found');
      }
  }
  ```
  Form template'larına `<input type="hidden" name="csrf_token" value="{{ csrf_token }}">` eklenecek.

**Test PoC:**
```html
<!DOCTYPE html>
<html><body>
<h1>Tıkla</h1>
<form id="f" action="https://ravendentalgroup.com/index.php?route=account/edit"
      method="POST">
  <input name="firstname" value="Pwned">
  <input name="lastname" value="User">
  <input name="email" value="attacker@evil.com">
  <input name="telephone" value="5550000">
</form>
<script>document.getElementById('f').submit();</script>
</body></html>
```
Kurban (logged-in raven dental hesabı) bunu açtığı an profil değişir.

### 3.6 🟡 MEDIUM — Brute force koruması yetersiz

**Dosya:** `/Users/ipci/raven-dental/code/admin/controller/common/login.php:94-99`
**Dosya:** `/Users/ipci/raven-dental/code/catalog/controller/account/login.php:160-164`

```php
$login_info = $this->model_user_user->getLoginAttempts($this->request->post['username']);
if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts'))
    && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
    $this->error['error_attempts'] = $this->language->get('error_attempts');
}
```

**Risk:**
- Sayaç **username başına**, **IP başına değil**. Saldırgan 100 farklı username dener (`admin`, `administrator`, `cihat`, `ravenden`, ...) → her birine 5 deneme şansı + paralel.
- Captcha YOK.
- 1 saat sonra sayaç sıfırlanıyor → düşük velocity, uzun süreli credential stuffing pratiği.
- Lockout sadece "warning" gösterir, **istek hâlâ DB'ye gidip authentication kontrol yapar** — DoS amaçlı kullanılabilir.

**Çözüm:**
1. **Cloudflare WAF rate limit** (free tier 10K req/ay, login endpoint için 5/dakika)
2. **VPS'te fail2ban** — apache log'lardan 401/302 pattern + `/login` URL → 1 saat block
3. **Captcha** — Google reCAPTCHA v3 OCMOD extension (3rd party)
4. **IP-bazlı sayım** — `oc_customer_login` ve `oc_user_login` tablolarına `ip` kolonu ekle, IP+username birleşik counter

**Test:**
```bash
# Mevcut davranış: 5 başarısız deneme sonrası 1 saat block
# 1 saat bekleme yok, sadece username değiştir:
for u in admin root manager test user; do
  for i in {1..10}; do
    curl -s -d "username=$u&password=wrong$i" \
      https://ravendentalgroup.com/admin/index.php?route=common/login \
      -o /dev/null -w "%{http_code}\n"
  done
done
# Tümü 200 (login form yeniden) — IP block yok.
```

### 3.7 🟡 MEDIUM — Host header injection

**Dosya:** `/Users/ipci/raven-dental/code/catalog/controller/startup/startup.php:4-6`

```php
$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store
    WHERE REPLACE(`ssl`, 'www.', '') = '" .
    $this->db->escape('https://' . str_replace('www.', '', $_SERVER['HTTP_HOST'])
    . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
```

**Risk:**
- `$_SERVER['HTTP_HOST']` saldırgan tarafından kontrol edilebilir (proxy chain, curl `-H "Host: evil.com"`).
- Şu anda sadece DB lookup için kullanılıyor — **doğrudan SQLi değil**, ama:
  - "şifremi unuttum" maillerinde `HTTP_HOST` kullanılırsa → password reset link'i saldırganın domain'ine yönlendirilebilir
  - Cache key olarak kullanılırsa cache poisoning
- OpenCart bunu yapan başka yerler var mı kontrol edilmeli:
  ```bash
  grep -rn 'HTTP_HOST' /home/ravenden/public_html/catalog/ /home/ravenden/public_html/system/
  ```

**Çözüm (.htaccess seviyesinde):**

```apache
# .htaccess'e ekle — sadece bilinen hostname'leri kabul et:
RewriteCond %{HTTP_HOST} !^ravendentalgroup\.com$ [NC]
RewriteCond %{HTTP_HOST} !^www\.ravendentalgroup\.com$ [NC]
RewriteRule .* - [F,L]
```

**Test:**
```bash
curl -H "Host: evil.com" https://ravendentalgroup.com/
# Şu anda: 200 (eski OpenCart davranışı)
# Düzeltme sonrası: 403
```

### 3.8 🟡 MEDIUM — Admin file manager: traversal koruması zayıf

**Dosya:** `/Users/ipci/raven-dental/code/admin/controller/common/filemanager.php`

`upload()` (satır 191-288):
```php
$directory = rtrim(DIR_IMAGE . 'catalog/' . $this->request->get['directory'], '/');
// directory tek başına `realpath()` ile çözülüyor:
if (substr(str_replace('\\', '/', realpath($directory)), 0, strlen(DIR_IMAGE . 'catalog'))
    != str_replace('\\', '/', DIR_IMAGE . 'catalog')) {
    $json['error'] = $this->language->get('error_directory');
}
```

**Risk:**
- `realpath()` based check **doğru pattern** ama path TAM olarak `image/catalog`'la başlamalı kontrol var — bu OK.
- Ama upload edilen dosya extension check'i:
  ```php
  $allowed = array('jpg', 'jpeg', 'gif', 'png');
  if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
      $json['error'] = $this->language->get('error_filetype');
  }
  ```
  - **Magic byte check yok** — sadece extension. Bir saldırgan PHP yükleyemez (extension reject) AMA **polyglot dosya** mümkün (örn. JPG comment'inde PHP kod, `.htaccess` ile yorumlanabilir hale getirilirse).
  - LiteSpeed/Apache'de `image/catalog/*.jpg.php` yüklenemez ama eğer `.htaccess` bypass varsa...
- `move_uploaded_file($file['tmp_name'], $directory . '/' . $filename);` — `$filename` `basename()` ile sanitize edilmiş, OK.

**Çözüm (defense-in-depth):**

```apache
# image/catalog/.htaccess (oluştur):
<FilesMatch "\.(php|phtml|php3|php4|php5|phar|pl|py|jsp|asp|sh|cgi)$">
    Require all denied
</FilesMatch>

# Veya daha sıkı — sadece image MIME'lara izin ver:
<FilesMatch "^[^.]+$">
    Require all denied
</FilesMatch>
```

```php
// admin/controller/common/filemanager.php upload() — magic byte check ekle:
if (!$json) {
    // Mevcut MIME header check'e ek:
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $real_mime = $finfo->file($file['tmp_name']);
    $allowed_real_mime = array('image/jpeg', 'image/png', 'image/gif');
    if (!in_array($real_mime, $allowed_real_mime)) {
        $json['error'] = $this->language->get('error_filetype');
    }
}
```

**Not:** Bu istismarı için admin paneline erişim gerek. Yani **defense-in-depth**; ama admin kullanıcısı compromise olursa, RCE buradan tetiklenebilir.

### 3.9 🟡 MEDIUM — XXE riski OCMOD modification XML işlemde

**Dosya:** `/Users/ipci/raven-dental/code/admin/controller/marketplace/install.php:268-270`
**Dosya:** `/Users/ipci/raven-dental/code/admin/controller/marketplace/modification.php:141-143`

```php
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->loadXml($xml);
```

**Risk:**
- PHP 8.0+'da libxml2 default'u external entity load'unu kapatıyor. Ama:
  - **Mevcut sunucu PHP 7.4** — libxml2 default davranışı historikal olarak XXE'ye açıktı (PHP'nin default'u 7.4'te kapalı diye iddialar var ama LIBXML_NONET açıkça flag'lenmeli — best practice).
  - 7.4'ten sonra `libxml_disable_entity_loader()` deprecated ama yine de `LIBXML_NONET` flag'i `loadXml`'e geçilmeli.
- Etki: Eğer XXE çalışırsa, saldırgan (admin yetkisi gerek) sunucudaki dosyaları okuyabilir, internal HTTP isteği yapabilir (SSRF), DoS yaratabilir.
- Bu kod **admin-only** — pre-auth değil. Yani saldırı yüzeyi admin compromise sonrası lateral movement.

**Çözüm:**

```php
// install.php:268 ve modification.php:141 → değiştir:
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->loadXml($xml, LIBXML_NONET | LIBXML_NOENT | LIBXML_DTDLOAD);
// LIBXML_NONET: Network erişimi kapat (SSRF blok)
// LIBXML_NOENT: Entity expansion kontrolünde olduğunu garantile
// LIBXML_DTDLOAD: DOCTYPE'ı parse etme
```

Daha güvenlisi PHP 8.0+'da:
```php
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->resolveExternals = false;
$dom->substituteEntities = false;
$dom->loadXml($xml, LIBXML_NONET);
```

**Test PoC (admin kullanıcısı olarak):**
```xml
<!-- malicious.ocmod.xml -->
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE foo [
    <!ENTITY xxe SYSTEM "file:///etc/passwd">
]>
<modification>
    <name>&xxe;</name>
    <code>test</code>
    <version>1.0</version>
    <author>x</author>
    <link>http://x</link>
</modification>
```
Bunu admin → Extensions → Modifications → Install'a yükle. Liste'de `name` sütununda `/etc/passwd` içeriği görünürse XXE çalışıyor demektir.

---

## 4. Journal3 Custom Code (Inject Pattern) Güvenliği

### 4.1 🟢 INFO — customCodeHeader inject pattern güvenli

**Dosya:** `/Users/ipci/raven-dental/code/catalog/view/theme/journal3/template/common/header.twig:72-74`

```twig
{% if j3.settings.get('customCodeHeader') %}
{{ j3.settings.get('customCodeHeader') }}
{% endif %}
```

**Mevcut içerik (DB `oc_journal3_setting`):**
```html
<link rel="alternate" hreflang="tr-tr" href="https://ravendentalgroup.com/" />
<link rel="alternate" hreflang="en-gb" href="https://ravendentalgroup.com/?language=en-gb" />
<link rel="alternate" hreflang="x-default" href="https://ravendentalgroup.com/" />

<script type="application/ld+json">
{
  "@type": "Organization",
  "name": "Raven Dental",
  ...
}
</script>
```

**Twig auto-escape default'u `true`**, ancak `{{ var|raw }}` veya `{{ html_safe_var }}` durumunda escape edilmez. Burada `{{ j3.settings.get(...) }}` çıktısı **muhtemelen raw** geçiyor (HTML olarak render edilmesi gerekiyor — hreflang/JSON-LD).

**Doğrulama:**
```bash
grep -n "twig.set_escape\|autoescape" /Users/ipci/raven-dental/code/system/library/template/twig.php
```

**Risk profili:**
- ✅ İçerik **DB'den geliyor** ve sadece **admin yetkisi olan kullanıcı** değiştirebilir → düşük risk
- ✅ Static içerik, kullanıcı input'u akmıyor → low concern
- ⚠️ Eğer ileride bu alana **dinamik kullanıcı verisi** girilirse (örn. `{{ customer.name }}`) — XSS açık olur

**Öneri:**
- Mevcut içerik OK.
- **Süreç önerisi:** customCodeHeader/Footer'a değişiklik yapmadan önce **manuel review** (admin sadece kendi yazdığını yazıyor, otomatik input akmamalı).
- KVKK uyumu için **Google Analytics consent banner** (gibi) eklenecekse, runtime decision logic eklendiğinde XSS riski yeniden değerlendirilmeli.

### 4.2 🟢 INFO — `customCodeHeader/Footer` `"php": true` flag'i

**Dosya:** `/Users/ipci/raven-dental/code/system/library/journal3/data/settings/settings/custom_code.json`

```json
{
  "customCodeHeader": {
    "type": "Input",
    "php": true
  },
  ...
}
```

**Risk soruşturması:** `"php": true` ne anlama geliyor? `system/library/journal3/settings.php`'de eval/include kullanılmıyor (line-by-line kontrol edildi). Muhtemelen sadece admin UI'da PHP highlight veya admin save endpoint'inde markup işaretleyici. **Pratik bir eval/RCE noktası BULUNAMADI.**

**Sonuç:** Risk düşük. Yine de:
```bash
grep -rn "eval(\|include.*custom\|require.*custom" /Users/ipci/raven-dental/code/admin/controller/journal3/ /Users/ipci/raven-dental/code/system/library/journal3/ 2>/dev/null
# Sonuç: hiçbir match yok ✓
```

### 4.3 🟢 INFO — Journal3 user-controlled veri akışı dolaylı XSS

**Dosya:** `/Users/ipci/raven-dental/code/catalog/view/theme/journal3/template/common/header.twig:78-82`

Bizim eklediğimiz:
```twig
{% set raven_h1 = j3.settings.get('journal3_home_h1') ?: (heading_title is defined and heading_title ? heading_title : title) %}
{% if raven_h1 %}
  <h1 class="sr-only" ...>{{ raven_h1 }}</h1>
{% endif %}
```

`heading_title` ve `title` controller'dan geliyor. Twig auto-escape devrededir (`{{ var }}` default escape eder), bu yüzden XSS YOK.

**Doğrulama:**
- `heading_title` çoğu controller'da DB'den (kategori/ürün adı) → admin tarafından girilen veri
- `title` `document->setTitle()`'dan → static veya admin-controlled

**Risk:** Sıfır pratik — ama admin tarafında kategori adına `<script>alert(1)</script>` yazılırsa, twig auto-escape onu render etmez. Twig'i bypass eden tek yol `|raw` filter — burada yok.

---

## 5. PII / KVKK Eksiklikleri

### 5.1 🟠 HIGH — PII tablolarında şifreleme YOK

**Etkilenen tablolar:** `oc_customer`, `oc_address`, `oc_order`, `oc_customer_login`

**Saklanan PII alanlar:**
| Tablo | Alan | Hassas Seviye |
|---|---|---|
| `oc_customer` | `firstname`, `lastname`, `email`, `telephone` | High (kimlik) |
| `oc_customer` | `password`, `salt`, `token` | Critical |
| `oc_customer` | `ip` (last login) | Medium (IP, KVKK kapsamında PII) |
| `oc_customer` | `custom_field` (JSON) | Yapısına göre değişir |
| `oc_address` | `firstname`, `lastname`, `company`, `address_1/2`, `city`, `postcode`, `country/zone` | High |
| `oc_order` | Tüm payment_* ve shipping_* PII | High |
| `oc_customer_login` | `email`, `ip` | Medium |

**Risk:**
- DB sızıntısı durumunda tüm PII düz metin
- KVKK Madde 6 — özel nitelikli kişisel veri değil ama "ad/soyad/e-posta/telefon/adres" zaten kişisel veri kapsamında, **uygun teknik önlemler** (encryption-at-rest, access control) gerekir
- KVKK Madde 12 — veri sahibinin silme ve **anonimleştirme** hakkı
- Login IP'leri 30+ gün saklanıyor mu? KVKK saklama süresi politikası lazım

**Çözüm önerileri (öncelik sırası):**

1. **DB-level encryption-at-rest (VPS'de MySQL InnoDB encryption)**:
   ```sql
   -- VPS migration sonrası:
   ALTER TABLE oc_customer ROW_FORMAT=DYNAMIC ENCRYPTION='Y';
   ALTER TABLE oc_address ENCRYPTION='Y';
   ALTER TABLE oc_order ENCRYPTION='Y';
   ```
   MySQL master key dosyasını file system'den ayrı tut (LUKS encrypted volume).

2. **Application-level encryption (hassas alanlar için)**:
   - Telefon ve mail için **deterministic encryption** (search-able) — `AES-256-SIV`
   - `custom_field` için **probabilistic encryption** — `AES-256-GCM`
   - OpenCart 3.x'te kolay değil; OCMOD ile geçici çözüm veya OpenCart 4 migration'da değerlendir.

3. **Veri saklama politikası**:
   ```sql
   -- Düzenli temizlik (cron):
   DELETE FROM oc_customer_login WHERE date_added < DATE_SUB(NOW(), INTERVAL 90 DAY);
   DELETE FROM oc_customer_activity WHERE date_added < DATE_SUB(NOW(), INTERVAL 180 DAY);
   ```

4. **KVKK silme hakkı (Right to Erasure)**:
   - "Hesabımı sil" özelliği yok. Admin'in manuel silme yapması gerekiyor → 1 ay içinde otomatize edilmeli.
   - Order kayıtlarında PII anonimize edilmeli (silme değil — yasal saklama yükümlülüğü):
     ```sql
     -- Hesap silme akışı (KVKK):
     UPDATE oc_order
     SET firstname = CONCAT('Anon-', order_id),
         lastname = '',
         email = CONCAT('deleted-', order_id, '@example.invalid'),
         telephone = ''
     WHERE customer_id = ?;
     ```

5. **Veri ihlali notification — KVKK madde 12/5**:
   - Veri ihlali tespit edildiğinde 72 saat içinde Kişisel Verileri Koruma Kurulu'na ve etkilenen kullanıcılara bildirim zorunluluğu.
   - **Şu anda monitoring yok.** Log analysis (fail2ban + Wazuh) en az 30 gün retention ile kurulmalı.

### 5.2 🟡 MEDIUM — Çerez bildirimi (KVKK + ePrivacy)

**Durum:** HTML'de cookie banner görünmüyor (önceki güvenlik audit doc'unda da belirtilmiş — 06-SECURITY-STATUS.md §9).

**Risk:** KVKK + ePrivacy regülasyonu — kullanıcının açık rızası olmadan analitik/marketing çerez set edilemez.

**Çözüm:**
- Journal3 built-in cookie banner var (admin → Settings → Cookie Notice) — **aktif et**
- Tracking çerezleri (Google Analytics, Facebook Pixel) banner onayı verilene kadar **delay/dont-load** olmalı
- "Sadece zorunlu çerezler" seçeneği gerek (currency, language, session — zorunlu; rv = recently viewed, gtm, fb = onay)

### 5.3 🟡 MEDIUM — `oc_customer.salt` sütununun varlığı

Salt DB'de plain saklanıyor. Hash sızarsa salt da sızar → rainbow table tek müşteri başına özelleşir, ama mass-cracking hâlâ mümkün (hashcat per-salt mode).

**Çözüm:** bcrypt/argon2 migration (Finding 3.1 ile birlikte).

---

## 6. File Upload — Catalog Tarafı

### 6.1 🟡 MEDIUM — `/index.php?route=tool/upload` PHP shell engellemesi naive

**Dosya:** `/Users/ipci/raven-dental/code/catalog/controller/tool/upload.php:47-52`

```php
// Check to see if any PHP files are trying to be uploaded
$content = file_get_contents($this->request->files['file']['tmp_name']);

if (preg_match('/\<\?php/i', $content)) {
    $json['error'] = $this->language->get('error_filetype');
}
```

**Risk:**
- Bu kontrol sadece `<?php` string'ini arıyor. **`<?=` short tag** veya `<?` short open tag bypass eder.
- Eğer PHP `short_open_tag` aktifse (php.ini ayarı), `<? echo system($_GET[c]); ?>` çalışır.

**Çözüm:**
```php
// catalog/controller/tool/upload.php:50 → değiştir:
if (preg_match('/(\<\?php|\<\?=|\<\?\s)/i', $content)) {
    $json['error'] = $this->language->get('error_filetype');
}

// Ayrıca ASP/JSP yi de blok:
if (preg_match('/<%|<script\s+language="php"/i', $content)) {
    $json['error'] = $this->language->get('error_filetype');
}
```

### 6.2 🟢 INFO — DIR_UPLOAD klasör hardening

`move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD . $file);`

`$file = $filename . '.' . token(32);` ile dosya adı obfuscate ediliyor (good).

**Ek öneri (varsa zaten yapıldı kontrol et):**
```apache
# storage/upload/.htaccess (oluştur veya doğrula):
<FilesMatch ".*">
    Require all denied
</FilesMatch>

# Veya tüm execute'i blok:
RemoveHandler .php .phtml .php3 .php4 .php5 .phar
RemoveType .php .phtml .php3 .php4 .php5 .phar
php_flag engine off
```

---

## 7. Image Processing (GD / ImageTragick türü riskler)

### 7.1 🟢 INFO — system/library/image.php GD-based, ImageMagick yok

**Dosya:** `/Users/ipci/raven-dental/code/system/library/image.php`

```php
if (!extension_loaded('gd')) {
    exit('Error: PHP GD is not installed!');
}
```

**Sonuç:** OpenCart GD kullanıyor — **ImageMagick policy.xml CVE'lerinden (CVE-2016-3714 ImageTragick) etkilenmez**.

GD'nin kendi geçmiş CVE'leri:
- CVE-2019-11038 (GD `gdImageColorMatch` heap buffer overflow) — PHP 7.4'te patch'li
- CVE-2018-14883 (EXIF GIF parse) — PHP 7.4'te patch'li

**Risk:** Düşük. PHP 7.4.33'e güncel olmasa da, **GD specific RCE recent CVE** PHP versiyon log'larında tetik etmedi (kontrol gerek). VPS'te PHP 8.2'ye geçişle tamamen kapatılır.

### 7.2 🟢 INFO — `image/cache/` dizini

OpenCart yeniden boyutlandırılmış imajları `image/cache/` altına yazar. Bu dizin:
- ✅ `.htaccess` ile `Disallow` yapıldığı için Google bot taramaz (robots.txt §22).
- ⚠️ **`image/cache/.htaccess`** ile PHP execute disable edilmeli (defense-in-depth):

```apache
# image/cache/.htaccess (oluştur):
<FilesMatch "\.(php|phtml|php3|php4|php5|phar)$">
    Require all denied
</FilesMatch>
```

---

## 8. Session/Cookie Ek Bulgular

### 8.1 🟡 MEDIUM — `php.ini` `safe_mode = Off` ve `allow_url_fopen = On`

**Dosya:** `/Users/ipci/raven-dental/code/php.ini`

```ini
safe_mode = Off                    # PHP 7+'da deprecated, no-op — OK
allow_url_fopen = On              # ⚠️ RFI riski
```

**Risk (`allow_url_fopen = On`):**
- Bir kod açığı `file_get_contents($user_input)` yaparsa, saldırgan `https://evil.com/shell.php` URL'i geçirebilir — SSRF.
- OpenCart kodunda `file_get_contents` URL'ye veriliyor mu kontrol et:
  ```bash
  grep -rn "file_get_contents" /Users/ipci/raven-dental/code/catalog/ /Users/ipci/raven-dental/code/admin/ | grep -v "DIR_\|__DIR__\|\$file\b" | head -20
  ```

**Bulguya bağlı çözüm:** Eğer hiçbir yerde user input URL'ye veriliyorsa, OK. Aksi halde:

```ini
allow_url_fopen = Off
allow_url_include = Off  # önemli; OFF olmalı
```

Sadece bazı extension'lar URL fetch yapıyorsa, **cURL**'a geçirmek daha güvenli (allow_url_fopen kapalı kalır).

### 8.2 🟢 INFO — `php.ini` `upload_max_filesize = 999M` aşırı

```ini
upload_max_filesize = 999M
max_execution_time = 36000  # 10 saat?!
```

**Risk:**
- 999MB upload: yanlışlıkla disk doldurma + DoS (memory_limit 64M ama post_max ile çakışıyor).
- `max_execution_time = 36000` (10 saat) — hung process'ler birikip resource tüketebilir.

**Çözüm:**
```ini
upload_max_filesize = 32M       ; OpenCart ürün resmi için yeterli
post_max_size = 40M             ; upload + form data
max_execution_time = 120        ; 2 dakika fazla bile
memory_limit = 256M             ; OpenCart 3.x için minimum
```

---

## 9. DOM-XSS / Client-Side (qnbpay JS)

### 9.1 🟡 MEDIUM — qnbpay-script.js innerHTML self-XSS

**Dosya:** `/Users/ipci/raven-dental/code/catalog/view/javascript/qnbpay/qnbpay-script.js:221-237`

```js
name.addEventListener("input", function () {
    if (name.value.length == 0) {
      document.getElementById("svgname").innerHTML = "John Doe";
    } else {
      document.getElementById("svgname").innerHTML = this.value;  // ❌ XSS sink
      document.getElementById("svgnameback").innerHTML = this.value;
    }
});

cardnumber_mask.on("accept", function () {
    if (cardnumber_mask.value.length == 0) {
      document.getElementById("svgnumber").innerHTML = "0123 4567 8910 1112";
    } else {
      document.getElementById("svgnumber").innerHTML = cardnumber_mask.value;
    }
});
```

**Risk:**
- Kart sahibi adı alanına `<img src=x onerror=alert(1)>` yazılırsa, SVG kart önizleme bölümünde `innerHTML` ile inject edilir.
- **Self-XSS** (kullanıcı kendine zarar verir) — pratik impact düşük. AMA:
  - **PCI-DSS** kart formu kontekstinde, herhangi bir XSS sink kart bilgisi sızdırabilir
  - Bir saldırgan kurbanı clipboard/social engineering ile kötü payload kopyala-yapıştır yaptırırsa, mevcut DOM'a script inject ederek kart numarası exfiltrate edebilir
- iframe-based QNB hosted form değil ise (kart sahibi formu **bizim** server'da işleniyor demektir), bu özellikle önemli

**Çözüm:**
```js
// qnbpay-script.js:226 → değiştir:
document.getElementById("svgname").textContent = this.value;  // ✅ textContent escape'lidir
document.getElementById("svgnameback").textContent = this.value;
```

Aynı düzeltme `svgnumber`, `svgexpire`, `svgsecurity` setter'larına da uygulanmalı (satır 233, 235, 241, 243, 250, 252).

**Bonus:** Form'da CSP nonce-based script-src eklenirse, inline JS'ler de korunur.

**Test PoC:**
```
1. /index.php?route=checkout/checkout sayfasına git
2. QNB Pay ödeme yöntemi seç
3. Kart Sahibi alanına: <img src=x onerror="alert('xss')">
4. SVG önizleme bölümünde alert popup
```

### 9.2 🟢 INFO — qnbpay.js AJAX endpoint'i CSRF korumalı değil

**Dosya:** `/Users/ipci/raven-dental/code/catalog/view/javascript/qnbpay/qnbpay.js:32`

```js
$.ajax("index.php?route=extension/payment/qnbpay/ajax&getInstallments=1", {
    type: "POST",
    data: { card: cardN },
    ...
});
```

POST endpoint'i CSRF token gönderilmiyor (Finding 3.5 kapsamında). Ama bu endpoint **sadece taksit listesi döndürür** — state-changing değil. Düşük risk.

**Not:** Tam QNB Pay audit'inde işlenecek (qnb-pay-security-audit.md).

---

## 10. Cookie / CSRF — OpenCart Default Davranışı

### 10.1 🟠 HIGH — Composite finding

**Genel durum:**
- Session cookie: HttpOnly/Secure/SameSite **yok** (Finding 3.2)
- Session ID: login sonrası rotate edilmiyor (Finding 3.3)
- POST endpoint'leri: CSRF token **yok** (Finding 3.5)

Bu üç açık birleşince: **modern tarayıcılarda default SameSite=Lax cookie davranışı tek savunma kalıyor**. Çoğu CSRF için bu yeterli, ama:
1. Eski browser'lar (legacy iOS Safari, IE/Edge)
2. SameSite explicit set edilmediği için bazı browser'larda **None** olarak davranır
3. POST CSRF mümkün kalır (Finding 3.5'teki PoC)

**Yaklaşım:** Hepsi birden çözülecek — Finding 3.2 (cookie flag), Finding 3.3 (regenerate ID), Finding 3.5 (CSRF token).

---

## 11. Diğer Bulgular

### 11.1 🟢 INFO — Admin panel `/admin/error_log` türü leak

`docs/08-CHANGES-MADE.md`'de zaten silindiği belirtilmiş. Tekrar oluşmasını önlemek için:

```ini
; php.ini içinde:
display_errors = Off
log_errors = On
error_log = /home/ravenden/private/php-errors.log   ; web root DIŞINDA
```

Şu anda `.htaccess` `php_flag display_errors off` yapıyor — OK ama `error_log` path'i web root dışına alınmalı.

### 11.2 🟢 INFO — config.php.SAMPLE varlığı

**Dosya:** `/Users/ipci/raven-dental/code/config.php.SAMPLE`

Bu standart OpenCart dağıtımı — içerik yer tutucu değerlerden ibaret. Yine de canlıda yerini test et:

```bash
curl -sI https://ravendentalgroup.com/config.php.SAMPLE
# Beklenen: 200 OK (içerik leak değil, ama varlığı OpenCart version fingerprint'i)
# İdeal: 404 veya 403 — `.htaccess`'e SAMPLE blok'u ekle:
```

```apache
<FilesMatch "\.(sample|example|dist)$">
    Require all denied
</FilesMatch>
```

### 11.3 🟢 INFO — `oc_user.ip` her login'de updated

**Dosya:** `system/library/cart/user.php:22`

```php
$this->db->query("UPDATE " . DB_PREFIX . "user SET ip = '"
    . $this->db->escape($this->request->server['REMOTE_ADDR'])
    . "' WHERE user_id = '" . (int)$this->session->data['user_id'] . "'");
```

- KVKK kapsamında IP de PII. DB sızıntısında admin'in son IP'si açığa çıkar.
- **Saldırı vektörü değil**, ama silinme/anonimleştirme politikası gerek.

### 11.4 🟡 MEDIUM — Admin filemanager directory walk

**Dosya:** `admin/controller/common/filemanager.php:14`

```php
$filter_name = rtrim(str_replace(array('*', '/', '\\'), '', $this->request->get['filter_name']), '/');
```

`..` karakterleri (path traversal için) sanitize **edilmiyor**.

Ama 39. satırda `realpath()` check var:
```php
if (substr(str_replace('\\', '/', realpath($directory) . '/' . $filter_name), 0, strlen(DIR_IMAGE . 'catalog'))
    == str_replace('\\', '/', DIR_IMAGE . 'catalog')) {
    // glob...
}
```

**Bypass'ın olup olmadığı kontrol gerek:**
```
?directory=../../../../etc/&filter_name=passwd
```
→ `realpath` ile `directory` resolve edilir, eğer `image/catalog` dışına çıktıysa `glob()` çalışmaz. **Muhtemelen güvenli** ama emin olmak için PoC test:

```bash
# Admin login sonrası:
curl -b "OCSESSID=$SID" -G \
  "https://ravendentalgroup.com/admin/index.php" \
  --data-urlencode "route=common/filemanager" \
  --data-urlencode "user_token=$TOKEN" \
  --data-urlencode "directory=../../../../etc" \
  --data-urlencode "filter_name=passwd"
# Beklenen: image listesi boş döner (güvenli)
```

---

## 12. Acil Aksiyon Listesi (Bu hafta)

| # | Bulgu | Yapılacak | Süre |
|---|---|---|---|
| 1 | **3.2** Session cookie flags | `system/framework.php` ve `catalog/controller/startup/session.php` `setcookie()` çağrısını HttpOnly+Secure+SameSite=Lax ile array-form'a değiştir | 30 dk |
| 2 | **3.4** Open redirect | `common/currency.php` ve `common/language.php`'de redirect param doğrulama ekle (`strpos === 0` + relative-only) | 1 saat |
| 3 | **3.6** Brute force | Cloudflare Free aktivasyon — login URL'lerine rate limit (5 req/dakika/IP) | 2 saat |

**Birikmiş test:** Üç düzeltme sonrası tüm site smoke test (checkout, login, register, profile edit, password reset).

---

## 13. Kısa Vade (1 hafta)

| # | Bulgu | Yapılacak |
|---|---|---|
| 4 | **3.3** Session fixation | `Cart\User::login()` ve `Cart\Customer::login()`'a session destroy+regenerate ekle |
| 5 | **3.5** CSRF token | Catalog POST endpoint'lerine CSRF token mekanizması (custom startup controller veya 3rd-party OCMOD) |
| 6 | **3.7** Host header | `.htaccess`'e `HTTP_HOST` whitelist kontrolü ekle |
| 7 | **9.1** qnbpay-script.js innerHTML | `innerHTML` → `textContent` (6 satır değişiklik) |
| 8 | **6.1** Upload PHP filter | `tool/upload.php`'de `<?=` ve `<? ` detect ekle |
| 9 | **2.2** CSP header | Minimal CSP (`frame-ancestors`, `form-action`, `base-uri`) `.htaccess`'e |
| 10 | **7.2** image/cache .htaccess | PHP execute blok dosyası oluştur |
| 11 | **8.2** php.ini sıkılaştır | upload_max_filesize, max_execution_time, memory_limit düzeltme |

---

## 14. Orta Vade (1 ay+) / VPS Migration sırasında

| # | Bulgu | Yapılacak |
|---|---|---|
| 12 | **3.1** Parola hash | bcrypt migration extension (kademeli — login'de eski hash başarılıysa yeni bcrypt'e re-hash) |
| 13 | **3.8** Admin file manager hardening | finfo magic byte check + `image/catalog/.htaccess` |
| 14 | **3.9** XXE OCMOD | `loadXml($xml, LIBXML_NONET)` (admin-only ama defense-in-depth) |
| 15 | **5.1** PII encryption | MySQL InnoDB encryption-at-rest (VPS migration sonrası, master key isolation) |
| 16 | **5.1** Veri saklama politikası | `oc_customer_login` 90 gün, `oc_customer_activity` 180 gün cron temizliği |
| 17 | **5.1** KVKK silme akışı | "Hesabımı sil" customer self-service endpoint + order PII anonymize SQL |
| 18 | **5.2** Cookie consent banner | Journal3 built-in cookie banner aktivasyonu + tracking script lazy-load |
| 19 | **3.6** WAF + fail2ban | Cloudflare WAF + VPS'te fail2ban + ModSecurity OWASP CRS |
| 20 | **Çapraz: PHP 8.2** | PHP 8.2 migration (07-PERFORMANCE.md'de planlı) |
| 21 | **Admin path obfuscation** | `/admin/` → `/yonetim-XXX/` rename + IP whitelist |
| 22 | **Vulnerability scan** | Wapiti, Nuclei, ZAP — VPS sonrası tam pentest |

---

## 15. Test Doğrulama Komutları

Aşağıdaki komutlar düzeltme sonrası tekrar çalıştırılarak doğrulanmalı:

```bash
# 1. Session cookie flags (Finding 3.2)
curl -sI https://ravendentalgroup.com/ | grep -i "set-cookie"
# Beklenen: HttpOnly; Secure; SameSite=Lax (hepsi)

# 2. Open redirect (Finding 3.4)
curl -i -X POST -d "code=USD&redirect=https://evil.com" \
  https://ravendentalgroup.com/index.php?route=common/currency/currency 2>&1 | grep -i "location"
# Beklenen: Location: https://ravendentalgroup.com/... (evil.com değil)

# 3. Host header (Finding 3.7)
curl -H "Host: evil.com" -sI https://ravendentalgroup.com/
# Beklenen: 403 veya 400 (whitelist eklenince)

# 4. CSP (Finding 2.2)
curl -sI https://ravendentalgroup.com/ | grep -i "content-security-policy"
# Beklenen: header görünür

# 5. config.php.SAMPLE (Finding 11.2)
curl -sI https://ravendentalgroup.com/config.php.SAMPLE
# Beklenen: 403

# 6. Brute force (Finding 3.6) — fail2ban veya Cloudflare aktif sonrası:
for i in {1..10}; do
  curl -sI -d "username=admin&password=wrong" \
    https://ravendentalgroup.com/admin/index.php?route=common/login
done
# 5. denemeden sonra 429 Too Many Requests veya 403 (block)

# 7. Image dir PHP execute (Finding 7.2)
# Manuel test: image/cache/.htaccess oluştur, sonra:
echo '<?php phpinfo();' > /tmp/test.php
# Yükleme (admin filemanager) sonrası /image/cache/test.php çağırılırsa:
# Beklenen: 403 (PHP execute disable)
```

---

## 16. Kapsam Dışı / Refer Edildi

- **QNB Pay modülü**: `analysis/qnb-pay-security-audit.md`'de işlendi. Bu doc'ta tekrar yok.
- **OpenCart core CVE'leri** (CVE-2023-47444 vb.): `docs/06-SECURITY-STATUS.md §2`'de listeli, VPS migration sonrası OpenCart 3.0.3.9'a yükseltme planlı.
- **SSL/TLS yapılandırma**: `06-SECURITY-STATUS.md §8` — bu doc'ta tekrar değil.
- **Bolkarco'nun raporladığı QNB Pay bulguları** (CSRF/SQLi/IDOR/webhook): QNB Pay audit doc'unda.

---

## 17. Sonuç ve Risk Skoru

| Kategori | Critical | High | Medium | Low/Info |
|---|---|---|---|---|
| OpenCart core | 2 (3.1, 3.2) | 3 (3.3, 3.4, 3.5) | 4 (3.6, 3.7, 3.8, 3.9) | 0 |
| .htaccess | 0 | 0 | 2 (2.2, 2.3) | 2 (2.4, 2.5) |
| Journal3 custom | 0 | 0 | 0 | 3 (4.1, 4.2, 4.3) |
| PII / KVKK | 0 | 1 (5.1) | 2 (5.2, 5.3) | 0 |
| File upload | 0 | 0 | 1 (6.1) | 1 (6.2) |
| Image proc | 0 | 0 | 0 | 2 (7.1, 7.2) |
| Session/php.ini | 0 | 0 | 2 (8.1, 8.2) | 0 |
| Client-side JS | 0 | 0 | 1 (9.1) | 1 (9.2) |
| Diğer | 0 | 0 | 1 (11.4) | 3 (11.1, 11.2, 11.3) |
| **Toplam** | **2** | **4** | **13** | **12** |

**Genel değerlendirme:**

Mevcut canlı site **istismar açısından "exploitable but not critical-without-prerequisites"** durumda. En kritik açıklar (parola hash, cookie flags, open redirect, CSRF) saldırgan iletişimi başlattığında **bir kaç saatlik exploit window** sağlar — özellikle Cookie+CSRF combo'su, bir XSS bulunduğu anda full account takeover'a açar.

**Hızlı kazanım yolu:**
1. Bu haftaki 3 acil aksiyon (cookie flags + open redirect + Cloudflare WAF) — **toplam ~4 saat** iş.
2. 1 hafta içinde session fixation + CSRF token + qnbpay innerHTML — **~6-8 saat** iş.
3. VPS migration sırasında bcrypt + PHP 8.2 + WAF + fail2ban — **1-2 gün** iş.

**Skor (subjektif, 100 üzerinden):**
- Şu anki güvenlik puanı: **52/100** (vanilla OpenCart 3.0.3.8 + bizim hardening = ortalama üzeri ama hâlâ exploitable known issues)
- Acil aksiyon sonrası: **68/100**
- Kısa vade sonrası: **78/100**
- VPS migration + tüm aksiyonlar sonrası: **86/100** (modern e-ticaret B2B sitesi için yeterli)

90+ için OpenCart 4 migration veya custom platform gerekir (kapsam dışı, ayrı proje).
