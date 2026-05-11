# 06 - Security Status (Güvenlik Analizi)

> Bu oturumda yapılan güvenlik düzeltmeleri + açık kalan riskler.

## ✅ Tamamlanan Güvenlik İyileştirmeleri

### Critical (kritik) — hepsi kapatıldı
| Bulgu | Durum | Detay |
|---|---|---|
| `/admin.zip` (852 MB) public erişilebilir | ✅ Silindi | Tüm admin kodu indirilebiliyordu |
| `toptandetal/Arsiv.zip` (19 MB) public | ✅ Silindi | Eski proje yedeği |
| `/error_log` (186 KB) içinde DB şifresi | ✅ Silindi + 403 | Eski hata loglarında plaintext şifre |
| `/admin/error_log` (469 KB) sızıntı | ✅ Silindi | Admin tarafı log |
| Admin şifresi `12345` | ✅ Değiştirildi | Brute-force trivial |
| DB user şifresi error_log'da sızdı | ✅ Rotate (2 kez) | Yeni: 32 char alfanümerik |

### High (yüksek)
| Bulgu | Durum | Detay |
|---|---|---|
| `.htaccess` yok / güvenlik header'ı yok | ✅ Düzeltildi | HSTS, X-Frame, nosniff, vb. eklendi |
| `display_errors` açık olabilir | ✅ Düzeltildi | `.htaccess` ile kapatıldı |
| Backup/zip/sql dosyaları açık | ✅ Engellendi | FilesMatch ile blocked |
| Dotfile (`.git`, `.env` vb.) erişimi | ✅ Engellendi | FilesMatch `^\.` |
| Directory listing aktif olabilir | ✅ Kapatıldı | `Options -Indexes` |
| X-Powered-By header sızıntı | ✅ Gizlendi | `Header unset X-Powered-By` |

### Medium (orta)
| Bulgu | Durum | Detay |
|---|---|---|
| ETag sızıntısı | ✅ Kapatıldı | FileETag None |
| robots.txt User-agent eksik | ✅ Düzeltildi | Format düzeltildi |
| 6 demo manufacturer (Apple, Sony) | ✅ Silindi | Fingerprinting riski |

## ⚠️ Açık Kalan Güvenlik Riskleri

### 1. PHP 7.4 EOL (Risk: HIGH)
**Durum:** PHP 7.4.33 — Aralık 2022'den beri EOL.
**Risk:** Güvenlik patch'i yok, yeni CVE'lere açık.
**Çözüm:** VPS migration sırasında PHP 8.2/8.3'e yükselt.

### 2. OpenCart 3.0.3.8 sürümü
**Durum:** OpenCart 3.0.3.8 (Şubat 2020 release).
**Son güncel:** OpenCart 4.x ana sürüm var; 3.x branch'i hâlâ desteklenir ama 3.0.3.9 + güvenlik patch'leri çıktı.
**Risk:** Bilinen vulnerability'ler:
  - CVE-2023-47444 (RCE, OpenCart 3.0.3.8 etkilenir)
  - Çeşitli XSS'ler admin panelinde
**Çözüm:** Önce 3.0.3.9 + en son patch'lere yükselt (theme uyumu kontrol gerekir). Veya OpenCart 4 migration (büyük iş, ayrı proje).

### 3. QNB Pay Modülü — Güvenlik Review Beklemede (Risk: UNKNOWN→HIGH)
**Geliştirici:** bolkarco (3. taraf)
**Bolkarco'nun raporladığı bulgular (WhatsApp mesajı):**
- CSRF koruması eksik
- Input validation yetersiz
- Webhook güvenliği zayıf
- SQL Injection riski
- "Anasayfa hesabım my orders'tan sipariş geçmişi" → muhtemel IDOR

**Bizim ön bulgularımız (dış kontrol):**
- `/index.php?route=account/order` → 302 redirect to login (login bypass YOK)
- Login form'da CSRF token görünmüyor (standart OpenCart sorunu)
- Webhook endpoint URL henüz tanımlı değil (review gerek)

**Eylem (FAZ 2'de):**
- `catalog/controller/extension/payment/qnbpay.php` (38 KB) line-by-line review
- `system/library/qnbpay.php` (38 KB) review
- Webhook URL'i bul, HMAC doğrulama var mı kontrol et
- POST endpoint'lerini CSRF token kontrolü için tara
- IDOR test: A kullanıcısı B'nin order_id'sini deneyebilir mi?

### 4. OpenCart CSRF Koruması (genel) — Risk: MEDIUM
**Durum:** OpenCart 3.x default CSRF token yok / sınırlı.
**Etki:** Admin panel'de XSS varsa CSRF ile yetki yükseltme mümkün.
**Çözüm:**
- Admin paneli için CSRF eklenti (3rd party)
- Veya admin URL'ini rename et + IP whitelist
- Veya OpenCart 4'e migration (CSRF built-in)

### 5. Admin URL Standart (Risk: LOW-MEDIUM)
**Durum:** `/admin/` — herkes bilir.
**Çözüm:** Rename + IP whitelist
```apache
# .htaccess'e ekle:
<Directory "/admin">
    Order deny,allow
    Deny from all
    Allow from XX.XX.XX.XX  # senin IP
</Directory>
```
Veya admin path'ini değiştir (`/yonetim123/`) — admin/config.php + admin/index.php'i taşı.

### 6. CSP (Content Security Policy) Eksik — Risk: MEDIUM
**Durum:** CSP header yok.
**Etki:** XSS payload yansıtılırsa hiçbir kısıtlama olmadan çalışır.
**Çözüm:**
```apache
Header set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://*; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data: https:; font-src 'self' https://fonts.gstatic.com; frame-src 'self' https:;"
```
⚠️ `unsafe-inline` ve `unsafe-eval` Journal3 inline script'leri için gerekli — sıkılaştırılmalı.

### 7. Rate Limit Yok — Risk: MEDIUM
**Durum:** Login form'a 1000 brute-force isteği atılabilir.
**Çözüm:** 
- CloudFlare ile rate limit (free 10K/ay)
- VPS'te fail2ban + nginx rate_limit module
- OpenCart extension: "Login Attempt Limit" (3rd party)

### 8. SSL/TLS Yapılandırma (Risk: LOW)
**Durum:** Let's Encrypt veya NetInternet SSL — gerçek versiyon test edilmedi.
**Test:** https://www.ssllabs.com/ssltest/analyze.html?d=ravendentalgroup.com
**Hedef:** A+ skor
- TLS 1.2 ve 1.3 only
- TLS 1.0/1.1 disabled
- Strong cipher suites
- HSTS (var ✓)
- HSTS preload eklenmeli (Chrome HSTS preload list)

### 9. KVKK Uyumu — Risk: LEGAL
**Durum:** KVKK'ya göre:
- Çerez bildirimi yok (HTML'de cookie banner görünmüyor)
- Gizlilik politikası var (/gizlilik-politikasi) ama içerik kontrol edilmeli
- Müşteri verisi (oc_customer, oc_order) DB'de saklı — silme/anonim hakkı destekleniyor mu?
- KVKK Kurulu kayıt zorunluluğu varsa kontrol et

**Çözüm:**
- Çerez bandı ekle (Journal3 default'u var, aktif et)
- Gizlilik politikası KVKK'ya göre revize et
- Hukuk danışmanı görüşü al

### 10. Backup Stratejisi (Risk: BUSINESS CONTINUITY)
**Durum:** NetInternet haftalık yapıyor (sağlayıcı).
**Eksik:** Bağımsız offsite backup yok.
**Risk:** NetInternet kaybı / saldırı durumunda tam veri kaybı.
**Çözüm (VPS sonrası):**
- Günlük tam backup → S3-uyumlu uzak (Hetzner Storage Box, Backblaze B2)
- 30 gün retention
- Aylık restore test

## QNB Pay Detaylı Audit Checklist (TODO Faz 2)

### Files to review
1. `catalog/controller/extension/payment/qnbpay.php` (38,956 byte)
2. `system/library/qnbpay.php` (37,590 byte)
3. `admin/controller/extension/payment/qnbpay.php`
4. `admin/model/extension/payment/qnbpay.php`
5. `catalog/view/javascript/qnbpay/*.js`

### Checklist
- [ ] **CSRF:** Form `<input name="csrf_token">` var mı? Server-side doğrulama var mı?
- [ ] **Input validation:** Kart numarası, CVV, expiry — server-side regex var mı? Sadece JS validation tehlikeli.
- [ ] **PAN/CVV logging:** Hiçbir log dosyasında kart bilgisi yazılıyor mu? `error_log()`, `file_put_contents()` ara.
- [ ] **HMAC webhook:** QNB callback URL'i HMAC ile signed mi? `hash_hmac('sha256', ...)` var mı?
- [ ] **Idempotency:** Aynı order_id 2 kez submit edilirse ne olur? Double-charge riski?
- [ ] **IDOR:** `/index.php?route=account/order/info&order_id=X` → kullanıcı kendi order'larından başkasına bakabilir mi?
- [ ] **Webhook endpoint:** Public mi, IP-whitelisted mi?
- [ ] **Encryption:** Kart bilgisi DB'ye saklanıyor mu? (PCI DSS — tokenize edilmeli, asla raw saklanmamalı)
- [ ] **Production vs sandbox:** Test BIN'ler production'da bloklanıyor mu?
- [ ] **PCI DSS scope:** Form NeoCart server'da mı işliyor yoksa iframe ile QNB hosted çözüm mü?

### PoC test senaryoları
1. **CSRF test:** Başka bir domain'den `<form action="https://ravendentalgroup.com/index.php?route=extension/payment/qnbpay/submit">` ile request gönder, çalışıyor mu?
2. **IDOR test:** A user ile login ol → /order_id=X1, sonra X2, X3, ... probe.
3. **Webhook spoof:** QNB IP'sinden gelmiyorsa, sahte "ödendi" webhook gönder.

## Hassas Bilgi Sızıntı Sayımı (Bu Oturum)

| Bilgi | Yer | Durum |
|---|---|---|
| Eski DB şifresi `+e^p!$O,9A?=` | error_log + chat | Şifre rotate edildi, error_log silindi |
| cPanel API token (orijinal) | Chat | Kullanıcı revoke etmeli (yapıldı mı?) |
| DB şifresi 2. versiyon | Chat (cat çıktısı) | Tekrar rotate edildi |
| QNB Pay merchant token | DB dump | Local'de yer alıyor, repo'ya pushlanmamış |
| Runner token'lar | İlk commit | Force push ile temizlendi |

## Acil Hijyen Aksiyon Listesi (Kullanıcı için)

1. ⏭️ **cPanel API token'ı revoke et** — chat'te görünmüştü
   - cPanel → Manage API Tokens → eski `claude-seo` revoke
   - Yeni token oluştur, lazımsa
2. ⏭️ **DB user şifresini bir kez daha rotate et** (paranoia için)
3. ⏭️ **VPS migration'da:**
   - PHP 8.2'ye yükselt
   - fail2ban kur
   - UFW + sıkı SSH config
   - Cloudflare Free proxy aç (DDoS + bot koruma)

## Güvenlik Tarama Önerileri (VPS sonrası)

```bash
# Lynis (Linux security audit)
apt install lynis
lynis audit system

# Wapiti (web app scanner)
wapiti -u https://ravendentalgroup.com -o /tmp/wapiti-report

# Nuclei (template-based scanner)
nuclei -u https://ravendentalgroup.com -severity high,critical

# SSL Labs A+ test
curl -s "https://api.ssllabs.com/api/v3/analyze?host=ravendentalgroup.com" | jq
```
