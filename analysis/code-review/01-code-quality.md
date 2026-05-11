# 01 — Code Quality Review (Custom Code)

> **Kapsam:** Bu projede *bu oturumda biz yazdığımız* veya *bolkarco tarafından özel eklenmiş* dosyalar. Vanilla OpenCart kodu ve QNB Pay güvenlik bulguları (zaten `qnb-pay-security-audit.md`'de) **DAHIL DEĞİL** — bu doküman tamamen "kod kalitesi" eksenine odaklı: okunabilirlik, edge-case, DRY, best-practice, maintainability.
>
> **Tarih:** 2026-05-12
> **Reviewer:** Code Reviewer agent
> **İncelenen dosya sayısı:** 7
> **Bulgu sayısı:** 41 (1 Critical, 7 High, 18 Medium, 15 Low) + 6 ✅ Good Pattern
> **Çıktı dili:** TR + EN karışık (kod yorumları TR, başlıklar EN — proje stili)

---

## 0. Özet ve TL;DR

Bu oturumda yazdığımız kodlar **çoğunlukla iyi**: `.htaccess` hardening solid bir referans, OCMOD XML mantığı doğru, `header.twig` edit'i fail-safe. Buna karşılık:

- **En riskli iki nokta:** (a) `_dotfile_htaccess`'teki `<FilesMatch "^\.">` LiteSpeed/Apache regex'inin kendi sub-request mimarisinde **tüm dotfile path'leri yakalamayabilir**, (b) `robots.txt`'te `Disallow: /catalog/` ve `Allow: /catalog/view/` çakışması bazı crawler'larda CSS/JS bloklanmasına yol açabilir.
- **bolkarco JS kodu** (`qnbpay.js`) eski tarz jQuery + global değişken kirliliği var ama küçük yüzey + işlevsel. Refactor öneri seviyesi: 🟢 Low.
- **`qnbpay.php` patched controller** (bizim 3 yamamızla birleşik) — patch'lerin kendisi doğru ama mevcut kod hâlâ `dump()` global function, `$pan = $new_str = ...` çift atama, copy-paste blokları gibi miras smell'leri taşıyor (bunlar bolkarco'nun "DÜZELTME" çalışmasından kalmış).
- **`raven.ocmod.xml`** sorun #3 (Twitter image) için patch içermiyor ama yorum satırı bırakılmış — bu ya silinmeli ya tamamlanmalı (yarım iz bırakma).

> İlk fix önceliği: **F1 (header.twig.bak production'da)**, **F2 (robots.txt /catalog/ engelleme)**, **F3 (.htaccess regex test)**. Sonra geri kalan iyileştirmeler.

---

## 1. `_dotfile_htaccess` (3.7 KB, bu oturumda yazıldı)

Üretime `/public_html/.htaccess` olarak deploy edilecek. Aşağıdaki bulgular Apache 2.4 + LiteSpeed Web Server (mevcut hosting) bağlamında.

### 🔴 Critical — yok

### 🟠 High

#### H-1.1 `<FilesMatch "^\.">` LiteSpeed'de subrequest dotfile'ları yakalamaz
**Dosya:** `_dotfile_htaccess:12-14`
```apache
<FilesMatch "^\.">
    Require all denied
</FilesMatch>
```
**Sorun:** `<FilesMatch>` sadece request edilen son segment'in dosya adına bakar. Apache + LiteSpeed'de subrequest path'i (örn. `.well-known/acme-challenge/xyz`) için `.well-known` bir **dizin**, dosya değil — bu kural onu tetiklemez ama `RewriteCond %{REQUEST_URI}` testleri yapanlar için tutarsız çıkabilir. Daha ciddi: `/some/normal/.bak` gibi path'lerde dosya adı ".bak" değil "<isim>.bak", regex `^\.` eşleşmez (dosya adı `.` ile başlamıyor).

**Test ne göstermeli:**
```bash
curl -sI https://ravendentalgroup.com/.git/HEAD       # 403 beklenir
curl -sI https://ravendentalgroup.com/.env            # 403 (FilesMatch \.env$ ile yakalanıyor zaten)
curl -sI https://ravendentalgroup.com/.well-known/x   # 200 (ACME için lazım — bu doğru)
curl -sI https://ravendentalgroup.com/admin/.htaccess # 403
```

**Önerilen düzeltme:** Dotfile koruması için ayrıca `RewriteRule` eklensin (FilesMatch'ten daha güvenli):
```apache
# --- 2. Dotfile + dotdir erişim engeli (.well-known hariç) ---
RewriteCond %{REQUEST_URI} (^|/)\.(?!well-known/) [NC]
RewriteRule .* - [F,L]

# .htaccess dosyasının kendisini ayrıca engelle (subrequest paranoyası)
<Files ~ "^\.ht">
    Require all denied
</Files>
```
Mevcut `<FilesMatch "^\.">` bloğu (12-14) ve `<Files ".htaccess">` bloğu (15-17) çıkarılabilir.

#### H-1.2 `<FilesMatch>` regex'i `.tar.gz` ve `.sql.gz`'yi yakalamıyor
**Dosya:** `_dotfile_htaccess:7-9`
```apache
<FilesMatch "\.(zip|tar|gz|tgz|bak|backup|sql|sql\.gz|env|log|sh|yml|yaml|ini|conf|inc|swp|orig|old|tmp)$">
```
**Sorun:** `<FilesMatch>` uzantı tabanlı çalışır; **son** uzantıyı eşler. `dump.sql.gz` için son uzantı `gz`, o yüzden `gz` kuralı yakalar ✓. Ama `dump.tar.gz` da `gz` yakalar ✓. `sql\.gz` alternative'i **fazlalık** — `sql` ve `gz` ayrı ayrı zaten içeride. Sorun değil ama gereksiz karışıklık.

Ayrıca eksik: `.pem`, `.key`, `.crt`, `.pfx`, `.json.bak`, `.dist`, `.lock`, `.local`, `.example`, `.test`, `.cache`, `.psd`, `.git*`, `.svn*`.

**Önerilen düzeltme:**
```apache
<FilesMatch "(?i)\.(zip|tar|gz|tgz|bz2|7z|rar|bak|backup|sql|env|log|sh|yml|yaml|ini|conf|inc|swp|orig|old|tmp|dist|local|example|test|cache|pem|key|crt|pfx|p12|psd|lock)$">
    Require all denied
</FilesMatch>
```
`(?i)` ile büyük/küçük harf duyarsız — `BACKUP.SQL` da yakalanır. Ayrıca `sql\.gz` özel kuralını kaldırdık (zaten `gz` ile yakalanıyor).

#### H-1.3 OpenCart rewrite kurallarında "system/download" engellemesi yanlış path döndürüyor
**Dosya:** `_dotfile_htaccess:93`
```apache
RewriteRule ^system/download/(.*) /index.php?route=error/not_found [L]
```
**Sorun:** `[L]` flag external redirect değil internal rewrite. Bu route gerçekte var mı? OpenCart 3'te `catalog/controller/error/not_found.php` var ama bu kural sadece `system/download/` URL prefix'ine vurur — ki bu zaten 1. ve 2. blokta `.tar`, `.gz` vs ile engelli olmalı. Ek olarak `system/` zaten OpenCart'ın korumalı klasörü, public erişim olmaması gerek.

Daha temiz çözüm:
```apache
# /system, /vendor, /storage altındaki dosyalar public değil
RewriteRule ^(system|vendor|storage)/ - [F,L]
```

### 🟡 Medium

#### M-1.4 `Header always unset X-Powered-By` + `Header unset X-Powered-By` çift kayıt
**Dosya:** `_dotfile_htaccess:38-39`
```apache
Header always unset X-Powered-By
Header unset X-Powered-By
```
**Sorun:** Aynı header iki kez unset ediliyor. `always` modifier her durumda (5xx dahil) çalışır, ikincisi zaten kapsanmış. Tek satır yeter.

**Önerilen:**
```apache
Header always unset X-Powered-By
```

#### M-1.5 HSTS `preload` ve `includeSubDomains` özelliklerinin değerlendirilmesi
**Dosya:** `_dotfile_htaccess:37`
```apache
Header set Strict-Transport-Security "max-age=31536000; includeSubDomains" env=HTTPS
```
**Sorun:** `preload` direktifi yok. Bu kararlı bir karar mı? `preload` HSTS preload list'e başvurmaya hazır olduğunu ima eder. Sub-domain'ler **kesinlikle** HTTPS-only mi (cdn, mail, beta vs.)? Eğer henüz değilse `includeSubDomains` riskli (örn. http://staging.ravendentalgroup.com varsa erişilemez olur).

**Önerilen aksiyon:** Önce subdomain envanteri çıkar. Hepsi HTTPS-only ise `includeSubDomains; preload` ekle ve [hstspreload.org](https://hstspreload.org)'a kayıt yap.

#### M-1.6 `mod_deflate` text/javascript MIME'ı outdated
**Dosya:** `_dotfile_htaccess:46`
```apache
AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript
```
**Sorun:** `text/javascript` modern browser/server'larda `application/javascript` olarak servis edilir. `text/javascript` artık RFC9239 ile geri yasal olsa da fiilen az kullanılıyor. Eksik MIME: `application/manifest+json`, `application/wasm`, `image/x-icon`, `font/ttf`, `font/otf`.

**Önerilen:**
```apache
AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css
AddOutputFilterByType DEFLATE application/javascript application/x-javascript application/json application/xml application/rss+xml application/atom+xml application/manifest+json application/wasm
AddOutputFilterByType DEFLATE image/svg+xml image/x-icon
AddOutputFilterByType DEFLATE font/woff font/woff2 font/ttf font/otf
```

#### M-1.7 SQL injection ön-filtresi false-positive üretebilir
**Dosya:** `_dotfile_htaccess:83-88`
```apache
RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} (\<|%3C).*iframe.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} base64_(en|de)code\(.*\) [NC]
RewriteRule ^(.*)$ - [F,L]
```
**Sorun:** Bu kurallar **defense-in-depth** için var ama:
1. `base64_(en|de)code` legitime kullanım vurabilir (örn. bir editör ürün açıklamasında "base64_encode" kelimesi geçen blog yazısı için arama yaparsa).
2. WAF rolünü ucuz regex'le oynamak fragile. OpenCart 3 zaten kendi input sanitization'ını yapar.
3. `GLOBALS` kuralı eski PHP 5 register_globals açığını hedefliyor — PHP 7.4'te etkisiz, gereksiz.

**Öneri:** Bu blok ya tamamen çıkarılsın (false-positive riski > savunma değeri) ya da daha hedefli hâle getirilsin:
```apache
# Sadece script/iframe injection — base64_encode + GLOBALS çıkarıldı
RewriteCond %{QUERY_STRING} (?:%3C|<)\s*script [NC,OR]
RewriteCond %{QUERY_STRING} (?:%3C|<)\s*iframe [NC]
RewriteRule ^ - [F,L]
```
Veya WAF'a (Cloudflare/ModSecurity) bu sorumluluğu devret.

#### M-1.8 `text/xml` ve `application/xml` 0-second cache — sitemap için sorun yaratır mı?
**Dosya:** `_dotfile_htaccess:57-58`
```apache
ExpiresByType text/xml                      "access plus 0 seconds"
ExpiresByType application/xml               "access plus 0 seconds"
```
**Sorun:** Sitemap.xml + googlebase.xml dinamik üretilir (OpenCart feed). 0 saniye cache demek crawler her gelişinde DB'ye bir kategori/ürün dump query'si tetikler. 369 URL'lik sitemap CPU-intensive değil ama yine de 1 dakikalık cache faydalı.

**Önerilen:**
```apache
ExpiresByType text/xml                      "access plus 5 minutes"
ExpiresByType application/xml               "access plus 5 minutes"
```
Veya `controller/extension/feed/google_sitemap.php`'ye `Cache-Control: public, max-age=300` header'ı ekle.

#### M-1.9 ETag kapatılmış ama Last-Modified hâlâ açık — tutarsız strateji değil ama belgelemek lazım
**Dosya:** `_dotfile_htaccess:40-42`

Yorum yok. `ETag None` ve `unset ETag` yapılırken **neden** yapıldığı `08-CHANGES-MADE.md`'de açıklanmış (Cloudflare uyumluluk) — ama `.htaccess` içine de tek satır yorum eklenmeli: `# Cloudflare/CDN ile uyumluluk için ETag kapalı, Last-Modified kullanılıyor`

### 🟢 Low

#### L-1.10 Bölüm başlıkları TR — uluslararası kontribütör için süpriz
Yorum satırları (`# --- 1. Hassas dosyaları engelle ---`) TR. Mevcut proje TR-only ekipte sorun değil ama ileride OpenCart community'ye katkı verilirse karışacak. Maintainability açısından kabul edilebilir.

#### L-1.11 `Options -MultiViews` kuralı LiteSpeed'de no-op olabilir
LiteSpeed `mod_negotiation` desteklemez. Apache fallback için doğru ama LiteSpeed'de uyarı log'a düşebilir. Zararsız.

### ✅ Good Patterns

- **Numaralı bölümler + başlık yorumları** — `.htaccess` dosyaları çabuk karmaşıklaşır, bu hijyen güzel.
- **`<IfModule>` guard'ları** — `mod_headers.c`, `mod_deflate.c`, `mod_expires.c`, `mod_rewrite.c` hepsi `<IfModule>` içinde. Modül yoksa fatal hata yerine sessiz devre dışı.
- **Hem `Files` hem `FilesMatch` ile derin koruma** — `config.php` ve `admin/config.php` ayrı bloklarda. (Aslında tek `FilesMatch "config\.php$"` da yeterdi ama açık olması iyi.)
- **`env=HTTPS` HSTS guard** — HTTP üzerinden HSTS göndermek RFC ihlali. Doğru yapılmış.

---

## 2. `robots.txt` (945 byte, bu oturumda yazıldı)

### 🟠 High

#### H-2.1 `Disallow: /catalog/` + `Allow: /catalog/view/` çakışması bazı crawler'larda CSS/JS bloklar
**Dosya:** `robots.txt:22, 35`
```
Disallow: /catalog/
...
Allow: /catalog/view/
```
**Sorun:** Google bu durumda **daha spesifik kural** kazanır (Allow /catalog/view/ daha uzun → wins). Bing/Yandex/diğer crawler'lar her zaman bu kuralı uygulamaz; bazıları "ilk eşleşen" mantığını kullanır. OpenCart'ın CSS/JS'i `/catalog/view/javascript/`, `/catalog/view/theme/journal3/stylesheet/` altında — bunlar tarayıcı için gerekli.

**Test yapılması gereken:** Google Search Console'da **robots.txt Tester** ile bir CSS dosyası kontrol et. Şu an siten zaten yayında, kontrol kolay:
```
URL: https://ravendentalgroup.com/catalog/view/theme/journal3/stylesheet/style.css
User-agent: Googlebot → Allowed/Blocked?
```

**Öneri:** Daha güvenli yaklaşım — sadece CSS/JS/image olmayanı engelle:
```
# Engelleme - PHP kaynaklarına direkt erişim
Disallow: /catalog/controller/
Disallow: /catalog/model/
Disallow: /catalog/language/

# CSS/JS/image açık (default)
# /catalog/view/ ve /image/ allow gerekmiyor — zaten engellenmiyor
```
Veya tüm `/catalog/` engellemesini çıkar. OpenCart vanilla kurulumlarda `/catalog/` zaten "view" hariç dosya servis etmez (controller/model PHP'leri direkt erişilmez).

#### H-2.2 Sitemap URL'i SEO URL kullanmıyor — index.php?route= raw URL
**Dosya:** `robots.txt:38`
```
Sitemap: https://ravendentalgroup.com/index.php?route=extension/feed/google_sitemap
```
**Sorun:** `.htaccess` Rewrite kuralında `^sitemap\.xml$ → index.php?route=...` zaten var. Crawler'lara **temiz URL** verilmeli, ham route değil. Hem cosmetic hem de query string parameter'lı URL'in canonical signal'i bozulur.

**Önerilen düzeltme:**
```
Sitemap: https://ravendentalgroup.com/sitemap.xml
```
Bu sayede crawler `.htaccess` rewrite kuralı üzerinden gelir — daha temiz.

### 🟡 Medium

#### M-2.3 `Disallow: /index.php?route=...` ile `Disallow: /*?route=...` arasında redundancy
**Dosya:** `robots.txt:27-32`
```
Disallow: /index.php?route=account/
Disallow: /index.php?route=checkout/
Disallow: /index.php?route=affiliate/
Disallow: /index.php?route=product/search
Disallow: /*?route=account/
Disallow: /*?route=checkout/
```
**Sorun:** `/*?route=account/` zaten `/index.php?route=account/` URL'ini de yakalar. İlki yedek olabilir ama `/*?route=affiliate/` ve `/*?route=product/search` eksik — tutarsız.

**Önerilen:** Sadece `/*?route=` formuna geç (daha kapsayıcı):
```
Disallow: /*?route=account/
Disallow: /*?route=checkout/
Disallow: /*?route=affiliate/
Disallow: /*?route=product/search
```
Veya hepsini ekle. Şu an asimetrik.

#### M-2.4 `User-agent: *` sonrası boş satır var
**Dosya:** `robots.txt:1-2`
```
User-agent: *

# Engelleme - ...
```
**Sorun:** Bu RFC9309'da yasak değil ama bazı eski crawler'lar `User-agent`'ı bir sonraki direktifle ilişkilendirmek için **kesintisiz blok** bekler. Riskli değil ama paranoid olalım.

**Önerilen:**
```
User-agent: *
# Engelleme - parametre tabanlı duplicate content
Disallow: /*?sort=
...
```

#### M-2.5 Crawl-delay direktifi yok
Google ignore eder ama Bing/Yandex saygı duyar. Shared hosting + 369 URL = makul. Eklemek `Crawl-delay: 1` zarar vermez.

### 🟢 Low

#### L-2.6 Yorum stili tutarsız
Bazı yerlerde `# Engelleme - X`, bazılarında düz `Disallow:`. Hepsi yorumlu olsun ya da hiçbiri.

### ✅ Good Patterns

- **Çoklu filter parameter engelleme** — `sort=`, `order=`, `limit=`, `filter_*=` hepsi → duplicate content kaynaklarını kapatmak doğru SEO hijyeni.
- **`/admin/` ve `/system/`** explicit engellemesi — `.htaccess` 403 verir zaten ama crawler'a sinyal vermek best practice.

---

## 3. `catalog/view/theme/journal3/template/common/header.twig` (H1 düzenlemesi)

Bu oturumda `body` tag'inden sonra eklenen blok (satır 78-82).

### 🟠 High

#### H-3.1 `header.twig.bak-20260511` production'a yansıyan code'da mevcut
**Dosya:** `catalog/view/theme/journal3/template/common/header.twig.bak-20260511`
**Sorun:** Yedek dosya tema klasöründe duruyor (`ls -la` çıktısında 6529 byte). Risk:
1. OpenCart bazı durumlarda twig dosyaları tarar ve tüm `.twig` uzantılı dosyaları yorumlamaya çalışır (varlık dizini için).
2. Web sunucu üzerinden direkt erişilebilir (`.bak` zaten `.htaccess` ile engellenmiş ✓ ama hâlâ disk space + git noise).
3. Daha kötüsü: bir developer "yedeği kullanayım" diye yanlışlıkla yedeği aktif edebilir.

**Önerilen:** Yedek dosyaları **tema klasörü dışında** tut:
```bash
# Doğru yer
mkdir -p /home/ravenden/backups/theme/
mv catalog/view/theme/journal3/template/common/header.twig.bak-20260511 \
   /home/ravenden/backups/theme/
```
Git'e dahilse `.gitignore`'a `*.bak-*` ekle.

#### H-3.2 `j3.settings.get('journal3_home_h1') ?: ...` Twig fallback Journal3 truthy değerlerinde devreye girmiyor
**Dosya:** `catalog/view/theme/journal3/template/common/header.twig:79`
```twig
{% set raven_h1 = j3.settings.get('journal3_home_h1') ?: (heading_title is defined and heading_title ? heading_title : title) %}
```
**Sorun:** Doc'ta zaten not edilmiş ama kod hâlâ bu hâlde: `j3.settings.get` Journal3 internals fallback chain ile `config_name` döndürebiliyor. Bu durumda `?:` (Elvis) **truthy** algılar, fallback'e geçmez. Sonuç: H1 hâlâ "Raven Dental" yazıyor.

OCMOD XML (raven.ocmod.xml) bunu **doğru** çözüyor (heading_title öncelik, j3.settings bypass). Yani burası **OCMOD ile düzelecek** — ama mevcut twig dosyası yanlış mantık taşıyor; OCMOD refresh edilmeden önce production'da `j3.settings` sonucu dönmeye devam ediyor.

**Önerilen aksiyon:** İki seçenek:
1. **OCMOD'u uygula** (Admin → Modifications → Refresh) — twig dosyasına dokunma, OCMOD storage/modification'da override eder.
2. **Veya** twig dosyasını **doğrudan** OCMOD'taki gibi düzelt:
   ```twig
   {% if heading_title is defined and heading_title %}
     {% set raven_h1 = heading_title %}
   {% elseif title is defined and title %}
     {% set raven_h1 = title|split('|')|first|trim %}
   {% else %}
     {% set raven_h1 = 'Diş Hekimliği Aletleri ve Cerrahi Ekipmanlar - Raven Dental' %}
   {% endif %}
   {% if raven_h1 %}
     <h1 class="sr-only" style="position: absolute; height: 1px; width: 1px; clip: rect(0,0,0,0);">{{ raven_h1 }}</h1>
   {% endif %}
   ```

Aralarındaki fark: OCMOD seçeneği Journal3 update'lerine dirençli (önerilen).

### 🟡 Medium

#### M-3.3 Inline CSS — Tailwind/CSS class'a çevrilebilir
**Dosya:** `header.twig:81`
```twig
<h1 class="sr-only" style="position: absolute; height: 1px; width: 1px; clip: rect(0,0,0,0);">{{ raven_h1 }}</h1>
```
**Sorun:** `sr-only` class'ı zaten Bootstrap/Tailwind standardı ve aynı görsel-gizleme özelliklerini taşır. Inline `style` redundant + CSP (Content-Security-Policy) sıkılaştırıldığında inline style yasak olur.

**Önerilen:** `class="sr-only"` yeterli (Journal3 zaten Bootstrap kullanıyor). Eğer kuşkulu ise ek class definition:
```css
/* customCSS Journal3 panelinden */
.sr-only-raven {
  position: absolute !important;
  height: 1px;
  width: 1px;
  clip: rect(0,0,0,0);
  overflow: hidden;
}
```

#### M-3.4 Yorum satırı bilgi taşıyor ama "Raven Dental" prefix'i her edit'i markaya bağlıyor
**Dosya:** `header.twig:78`
```twig
{# Raven Dental — SEO H1: prioritize page title (heading_title), fall back to journal setting or site name #}
```
**Sorun:** Yorumda hem "Raven Dental" hem İngilizce hem TR niyet açıklaması var. Sonrası OCMOD yorumlarında "SEO H1: heading_title öncelik..." TR — tutarsız. Maintainability açısından küçük problem.

**Önerilen:** OCMOD ile aynı dile/biçeme hizala:
```twig
{# RAVEN: SEO H1 - heading_title öncelik, sonra title'dan brand suffix'i ayır #}
```

### ✅ Good Patterns

- **`is defined and ...` çift kontrolü** — Twig'de `heading_title is defined` olmadan direkt kullanım `Undefined variable` hatası verir. Burası doğru savunmuş.
- **Inline yorum** — Edit nedeni bir blok yorumu ile belirtilmiş — bir sonraki developer için anlamlı.
- **Fail-safe fallback** — `raven_h1` her durumda en azından `title`'a düşer. Boş H1 ihtimali yok.

---

## 4. `analysis/theme-patches/raven.ocmod.xml`

### 🟡 Medium

#### M-4.1 `Sorun 3` yorum bloğu kod içermiyor — yarım iz
**Dosya:** `raven.ocmod.xml:53-62`
```xml
<!--
    Sorun 3: Twitter Card image dimensions 200×200 (yetersiz).
             Optimal Twitter summary_large_image için 1200×675.
    Hedef:   catalog/controller/journal3/seo.php — twitter:image kısmı
    NOT:     Bu sadece BU site için override — Journal3 ayarı kalıyor ama bizim XML
             bu controller satırlarını dönüştürüyor değil. ...
-->
```
**Sorun:** Yorum problem'i tarif ediyor ama hiçbir `<operation>` yok. XML okuyan bir başka geliştirici "ne bu, eksik mi?" diye anlayamaz.

**Önerilen seçenekler:**
1. **Sil** — yorum eksik patch'ı temsil etmesin. Notu `theme-patches/README.md` veya `docs/12-ROADMAP.md`'ye taşı.
2. **TODO marker bırak:**
   ```xml
   <!-- TODO Sorun 3 (Twitter image 1200x675): DB üzerinden çözülecek — oc_journal3_setting tablosu. Bu OCMOD'ta yok. -->
   ```

#### M-4.2 Regex search string'i Journal3 update'inde kırılabilir
**Dosya:** `raven.ocmod.xml:16, 20`
```xml
<search regex="true"><![CDATA[~'title'\s+=> \$this->config->get\('config_name'\),~]]></search>
```
**Sorun:** Bu regex çok dar — şu hâle:
```php
'title' => $this->config->get('config_name'),
```
eşleşir. Ama:
```php
'title'  => $this->config->get('config_name'),  // 2 boşluk
'title' => $this -> config->get('config_name'),  // farklı spacing
```
eşleşmez. Journal3 v3.1.13+ minor update'leri spacing değiştirebilir.

**Önerilen:** Daha esnek regex:
```xml
<search regex="true"><![CDATA[~'title'\s*=>\s*\$this->config->get\(\s*'config_name'\s*\)~]]></search>
<add position="replace"><![CDATA['title' => ($this->config->get('config_meta_title') ?: $this->config->get('config_name'))]]></add>
```
Trailing virgül opsiyonel hâle gelir, OCMOD davranışı tutarlı kalır.

### 🟢 Low

#### L-4.3 `<version>` ve `<author>` placeholder
**Dosya:** `raven.ocmod.xml:5-6`
```xml
<version>1.0</version>
<author>Raven Dental Team</author>
```
Tek-developer projede gereksiz değil ama versiyon bumping plan'ı yoksa "1.0" ileride değişiklik geçince anlamsızlaşır. Düşük öncelik.

### ✅ Good Patterns

- **Açıklayıcı yorum blokları** — Her `<file>` öncesi "Sorun N" + "Hedef: ..." açık tariflenmiş. OCMOD okumayanlar için bile anlaşılır.
- **OCMOD seçimi (doğrudan dosya edit yerine)** — Journal3 update'lerine dayanıklılık doğru karar.

---

## 5. `analysis/qnb-patches/patched/catalog/controller/extension/payment/qnbpay.php` (896 satır)

Bu dosya **bizim 3 patch'imizin uygulanmış hâli**. Patch'lerin **kendisi** doğru (security audit'te zaten kapsandı). Bu bölümde **yamasız** (yani bolkarco'dan miras) kod kalitesi sorunlarını ele alıyoruz.

### 🔴 Critical

#### C-5.1 Global `dump()` function dosya başında — production'a sızabilir
**Dosya:** `qnbpay.php:3-8`
```php
<?php

function dump($x)
{
    echo "<pre>";
    print_r($x);
    echo "</pre>";
}

class Controllerextensionpaymentqnbpay extends Controller
```
**Sorun:** Çoklu açıdan kötü:
1. **Global namespace polluter** — PHP class autoload'ı bu dosyayı load eder, `dump()` global olarak tüm runtime'a sızar. Başka modül `dump()` tanımlarsa fatal collision.
2. **Production'a debug bırakma riski** — `dump(...);exit;` çağrıları kod içinde birden fazla yerde yorum satırı olarak duruyor (satır 393, 512, 597, 619). Birinin yorumunu kaldırması sayfanın tamamen ölmesine + payment data leak'e yol açar (kart numarası, customer info ekrana basılır).
3. **OpenCart 3'te `Controller` class içinde `$this->log->write()` kullanmak best practice** — debug için.

**Önerilen düzeltme:**
```php
<?php

class ControllerExtensionPaymentQnbpay extends Controller
{
    private function debugLog($context, $data)
    {
        if ($this->config->get('payment_qnbpay_debug')) {
            $this->log->write('[QNBPAY] ' . $context . ' :: ' . print_r($data, true));
        }
    }
    // ...
```
Sonra her `//dump(...)` yerine: `$this->debugLog('process input', $data);`

**Acil:** `dump()` global fonksiyonu **sil**. Tüm `//dump(...);exit;` yorum satırlarını da temizle.

### 🟠 High

#### H-5.2 Class adı PSR-1/OpenCart convention ihlali
**Dosya:** `qnbpay.php:10`
```php
class Controllerextensionpaymentqnbpay extends Controller
```
**Sorun:** OpenCart 3 convention'ı `ControllerExtensionPaymentQnbpay` (CamelCase her segmentte). Mevcut `Controllerextensionpaymentqnbpay` lowercase — sadece PHP case-insensitive class lookup sayesinde çalışıyor. Bu strict_types veya PSR-loader değişikliğinde kırılır. Diğer payment modülleri kontrol et:

```bash
grep -h "^class " catalog/controller/extension/payment/*.php | head -5
# class ControllerExtensionPaymentCod ...
# class ControllerExtensionPaymentPaypoint ...
```
Sadece qnbpay yanlış yazılmış.

**Önerilen:**
```php
class ControllerExtensionPaymentQnbpay extends Controller
```

#### H-5.3 `recurringCancel()` — admin'e email atıyor ama hata sessiz yutuluyor
**Dosya:** `qnbpay.php:64`
```php
try {
    $mail = new Mail(...);
    // ...
    $mail->send();
} catch (Throwable $e) { /* sessizce geç */ }
```
**Sorun:** Müşteri "iptal talebi alındı" mesajını alıyor ama admin **email'i hiç gönderilmemiş** olabilir (SMTP düşmüş, mail config bozuk, vb.). Yorum açıkça "sessizce geç" diyor — bu **kesinlikle log'lanmalı**:
```php
} catch (Throwable $e) {
    $this->log->write('[QNBPAY] recurringCancel mail failed: ' . $e->getMessage() . ' | order=' . $order_id);
}
```
Aksi takdirde admin haber alamaz, müşteri parasını ödemeye devam eder.

Bu bir ürün/business risk → 🟠 High.

#### H-5.4 `recurringCancel()` SQL injection riski — açık değil ama prepared statement kullanmıyor
**Dosya:** `qnbpay.php:42-47`
```php
$this->db->query("INSERT INTO `" . DB_PREFIX . "qnbpay_recurring_cancel_requests` SET
    `order_id` = '" . (int)$order_id . "',
    `customer_id` = '" . (int)$this->customer->getId() . "',
    `requested_at` = NOW(),
    `status` = 'pending',
    `notes` = ''");
```
**Sorun:** Şu an `(int)` cast ile değerler emniyetli — gerçek injection yok ✓. Ama:
1. OpenCart 3 `$this->db->escape()` standard yöntem; `(int)` cast tutarsız stil.
2. `notes = ''` hardcoded — ileride bu kolon kullanıcı input alırsa, mevcut kalıp escape yapmadan kullanılacak.
3. `INSERT ... SET` syntax MySQL-specific (MariaDB de destekler ama PostgreSQL portu olursa kırılır).

**Önerilen:**
```php
$this->db->query(
    "INSERT INTO `" . DB_PREFIX . "qnbpay_recurring_cancel_requests` " .
    "(order_id, customer_id, requested_at, status, notes) VALUES " .
    "(" . (int)$order_id . ", " . (int)$this->customer->getId() . ", NOW(), 'pending', '" .
    $this->db->escape('') . "')"
);
```
Veya prepared statement (OpenCart 3 doğrudan PDO sağlamıyor ama yine `escape()` zorunluluk).

#### H-5.5 `recurringCancel()` `'status_id'` hardcoded fallback `7`
**Dosya:** `qnbpay.php:70`
```php
$this->config->get('payment_qnbpay_order_status_id_cancel_requested') ?: 7,
```
**Sorun:** `7` magic number — neyi temsil ediyor? OpenCart default status table'ında `7=Canceled`. Ama bu kurulumda muhtemelen farklı bir mapping olabilir. Production'da `oc_order_status` tablosunu kontrol etmek lazım — yanlış status_id seçilirse müşteri "iptal" yerine "siparişiniz reddedildi" gibi mesaj görür.

**Önerilen:**
```php
// status_id config'te tanımlı değilse "Canceled" (default 7) — admin panelinden ayar yapılmalı
$cancel_status_id = (int)$this->config->get('payment_qnbpay_order_status_id_cancel_requested');
if ($cancel_status_id <= 0) {
    $cancel_status_id = 7; // OpenCart default "Canceled" — admin panelinde override edilmeli
}
```

### 🟡 Medium

#### M-5.6 Cumulative copy-paste — "sadece peşin göster" fallback bloğu 3 yerde tekrarlanıyor
**Dosya:** `qnbpay.php:253-267, 277-289, 297-309`
**Sorun:** `ajax()` method'unda token-error, rates-error, status-error olmak üzere 3 farklı koşulda neredeyse aynı response yapısı tekrarlanıyor (sadece message text farklı). DRY ihlali.

**Önerilen refactor:**
```php
private function sendFallbackInstallments($total, $totalText, $message): void
{
    $response = [
        "status"    => "success",
        "message"   => $message,
        "taksitler" => [
            "1" => [
                "taksit" => 1,
                "aylik"  => number_format($total, 2, '.', ''),
                "toplam" => number_format($total, 2, '.', ''),
                "text"   => "1 x " . number_format($total, 2, '.', '') . ", " . $totalText . " : " . number_format($total, 2, '.', '')
            ]
        ]
    ];
    $this->response->addHeader('Content-Type: application/json; charset=utf-8');
    $this->response->setOutput(json_encode($response, JSON_UNESCAPED_UNICODE));
}
```
Üç çağrı yerine: `$this->sendFallbackInstallments($total, $data['toplam_text'], 'Token alınamadı...');`

#### M-5.7 `process()` — `$pan = $new_str = str_replace(...)` çift atama anlamsız
**Dosya:** `qnbpay.php:551, 581`
```php
$pan = $new_str = str_replace(' ', '', $this->request->post['pan']);
```
**Sorun:** `$new_str` değişkeni hiçbir yerde kullanılmıyor (kontrol edildi). Code smell — muhtemelen yeniden adlandırma sırasında kalıntı. Aynı satır iki kez de var (satır 551 ve 581 — kart bilgisi extract için).

**Önerilen:**
```php
$pan = str_replace(' ', '', $this->request->post['pan']);
list($ay, $yil) = explode('/', $this->request->post['expirationdate']);
```
Ve satır 581-582 zaten satır 551-552 ile birebir aynı — **silinebilir**. Sadece ilk extraction yeterli.

#### M-5.8 `process()` — `is_3d` koşulu okumaya elverişsiz boolean logic
**Dosya:** `qnbpay.php:601`
```php
if (!empty($qnbpay->is_3d) && $qnbpay->is_3d == 4 or !empty($this->qnbpay) && $this->qnbpay->is_3d == 8) {
```
**Sorun:**
1. `and`/`or` keyword'leri (low precedence) vs `&&`/`||` (high) — karışık kullanım. `or`'ın precedence'ı `=`'den de düşük. Burası tehlikeli.
2. Parantez eksikliği: `(A && B == 4) or (C && D == 8)` mi yoksa `A && (B == 4 or C) && D == 8` mi? Okurken ayrıştırması zor.
3. `$qnbpay` ve `$this->qnbpay` farklı objelere mi referans? `index()` method'unda `$this->qnbpay = new qnbpay(...)`, `process()` method'unda `$qnbpay = new qnbpay(...)`. İki ayrı instance.

**Önerilen:**
```php
$is3d4 = !empty($qnbpay->is_3d) && $qnbpay->is_3d == 4;
$is3d8 = !empty($this->qnbpay) && $this->qnbpay->is_3d == 8;
if ($is3d4 || $is3d8) {
    $qnbpayForm = $qnbpay->generatePaymentLink();
    $mode = "redirect";
}
```
Ayrıca `$this->qnbpay` referansı `process()` içinde set edilmiyor (sadece `index()` ve `deletemycard()` set ediyor). Burada `$this->qnbpay` muhtemelen `null` — bu condition **hiç tetiklenmiyor olabilir**.

#### M-5.9 `process()` — same/recurring kontrol mantığı `$isSame` flag-spaghetti
**Dosya:** `qnbpay.php:441-456`
```php
$isSame = true;
if (count($productRecurrings) > 0) {
    $recurring_payment = true;
    $types = array_map('gettype', $productRecurrings);
    if (!$this->same($types)) {
        $isSame = false;
    }
    if ($isSame) {
        foreach ($productRecurrings as $productRecurring) {
            if ($productRecurring != $productRecurrings[0]) {
                $isSame = false;
            }
        }
    }
}
```
**Sorun:** `same()` helper sadece bir kez kullanılıyor (types için), `$isSame` flag + nested foreach... aşırı karmaşık. Tek satır eşdeğeri var.

**Önerilen:**
```php
$recurring_payment = !empty($productRecurrings);
if ($recurring_payment) {
    $first = $productRecurrings[0];
    $allSame = !in_array(false, array_map(fn($p) => $p == $first, $productRecurrings), true);
    if (!$allSame) {
        $this->session->data['error'] = "Sipariş edilen ürünlerin hepsi aynı tekrarlı ödeme değerine sahip olması gerekir";
        $this->response->redirect($this->url->link('checkout/checkout', '', true));
        return;
    }
}
```
PHP 7.4 arrow function'lı versiyon (kuruluş PHP 7.4.33).

#### M-5.10 `process()` — yorum satırı `//dump(...);exit;` çoklu yer
**Dosya:** `qnbpay.php:393, 512, 597, 619`
**Sorun:** Debug breadcrumb'lar production kodunda. Maintainability sorunu. C-5.1 ile birlikte temizlenmeli.

#### M-5.11 `validation()` — `$hashParts[2]` magic index
**Dosya:** `qnbpay.php:682, 688`
```php
if (empty($hashParts[2]) || $hashParts[2] != $invoice_id) {
```
**Sorun:** Index `0`, `2` vs kullanılıyor — yorum satırında "validateHashKey döner: [status, total, invoiceId, orderId, currencyCode]" demiş ✓ (bu iyi). Ama daha okunabilir:
```php
[$hashStatus, $hashTotal, $hashInvoiceId, $hashOrderId, $hashCurrency] = $hashParts + [null, null, null, null, null];
if (empty($hashInvoiceId) || $hashInvoiceId != $invoice_id) {
    // ...
}
```
Destructuring + default-fill. Daha az hata.

#### M-5.12 `validation()` — `htmlspecialchars` kullanımı yarım
**Dosya:** `qnbpay.php:696`
```php
$this->session->data['error'] = "Ödeme İşlemi Tamamlanamadı. (" . htmlspecialchars($status_code) . " : " . htmlspecialchars($status_description) . ")";
```
**Sorun:** `htmlspecialchars()` default ENT_HTML401 + ISO-8859-1 — UTF-8 string'lerde mojibake riski. PHP 8.1+ default UTF-8 ama PHP 7.4.33'te şart koşmalı.

**Önerilen:**
```php
$status_code = htmlspecialchars($status_code, ENT_QUOTES, 'UTF-8');
$status_description = htmlspecialchars($status_description, ENT_QUOTES, 'UTF-8');
```

#### M-5.13 `webhook()` — `$this->request->get['do']` undefined koruması yok
**Dosya:** `qnbpay.php:793, 803, 811`
```php
if ($this->request->get['do'] == 'sale') {
```
**Sorun:** `$_GET['do']` yoksa PHP 7.4 Notice "Undefined index". PHP 8.x Warning. Veri tutarsızlığı durumunda log spam.

**Önerilen:**
```php
$action = $this->request->get['do'] ?? '';
if ($action === 'sale') {
    // ...
} elseif ($action === 'refund') {
    // ...
} elseif ($action === 'recurring') {
    // ...
} else {
    $this->log->write('[QNBPAY] webhook unknown action: ' . $action);
}
```
Switch/case da olabilir, ama küçük case sayısında if/elseif okunabilir.

#### M-5.14 `webhook()` — refund status_id magic number `11`
**Dosya:** `qnbpay.php:807`
```php
$this->model_checkout_order->addOrderHistory(
    $order_id,
    11, // İade durumu
    'QNB Pay Webhook: İade işlemi',
    false
);
```
**Sorun:** `11` magic number. OpenCart default status table'ında `11=Refunded` evet, ama config'leştirilmeli (H-5.5 ile aynı pattern).

**Önerilen:** Yeni config key:
```php
$refundStatusId = (int)$this->config->get('payment_qnbpay_refund_status_id') ?: 11;
$this->model_checkout_order->addOrderHistory($order_id, $refundStatusId, ...);
```

#### M-5.15 `validation()` — `$this->session->data['order_id'] = $order_id;` reset gereksiz
**Dosya:** `qnbpay.php:728`
```php
$this->session->data['order_id'] = $order_id;
```
**Sorun:** Session'da zaten varsa override anlamsız. Yoksa invoice_id'den çıkarıldı zaten. Riskli değil ama logic'i izlemek zor.

### 🟢 Low

#### L-5.16 `index()` — $data assignments birinin üstüne yazıyor
**Dosya:** `qnbpay.php:143-144`
```php
$data['total'] = $this->cart->getTotal(); // + shipping
$data['total'] = $grandTotal;
```
İlk satır anlamsız — hemen overwrite ediliyor. Sil veya yorum yap ("debug için, prod'da gereksiz").

#### L-5.17 `index()` — yorum: "Token Error" hardcoded fallback message
**Dosya:** `qnbpay.php:128`
```php
$error_message = isset($tokenResult['message']) ? $tokenResult['message'] : 'Token Error';
```
İngilizce — site TR. Language file'a taşınmalı:
```php
$error_message = $tokenResult['message'] ?? $this->language->get('error_token');
```

#### L-5.18 `process()` — `tax->calculate(...)` sonucu `$x = ...; $itemAmount = floatval(number_format($x, 2, '.', ''));` iki adım
**Dosya:** `qnbpay.php:423-424`
```php
$x = $this->tax->calculate(...) * $currency_rate;
$itemAmount = floatval(number_format($x, 2, '.', ''));
```
`$x` değişkeni isim olarak bilgi taşımıyor. Tek satır + isimlendirme:
```php
$itemAmount = round($this->tax->calculate(...) * $currency_rate, 2);
```
`number_format → floatval` chain'i `round()` ile equivalent ve daha hızlı.

#### L-5.19 `process()` — `$itemTotal` accumulator değişkeni hesaba katıldıktan sonra kontrol et
**Dosya:** `qnbpay.php:438`
```php
$itemTotal += $itemAmount * intval($product["quantity"]);
```
Bu doğru ama `$itemTotal` hiç kullanılmıyor sonra... bekle, satır 514'te `if ($qnbpay->order['total'] < $itemTotal)` ile kontrolde kullanılıyor ✓. False alarm; sadece okuma sırasında uzak bağlamlı.

#### L-5.20 Comment dili karışık
TR yorumlar arasına `// + shipping`, `// bin installment list` İngilizce. Tutarsız. (`Türkçe`'ye geçilebilir veya hepsi İngilizce.)

#### L-5.21 `same()` helper sadece bir kez kullanılıyor
**Dosya:** `qnbpay.php:890-895`
```php
public function same($arr)
{
    return $arr === array_filter($arr, function ($element) use ($arr) {
        return ($element === $arr[0]);
    });
}
```
Public method olarak class'a expose edilmiş ama sadece dosya içi kullanım için. `private` olmalı. Ayrıca M-5.9 refactor'unda zaten gereksiz hale geliyor.

### ✅ Good Patterns

- **3 PATCH yorum marker'ı** — `[PATCH 01]`, `[PATCH 02]`, `[PATCH 03]` ile her güvenlik patch'inin konumu belirgin. Code archeology kolay.
- **Defense-in-depth `validation()`** — Hash kontrol + status code + QNB API re-query üçlüsü güzel layered defense.
- **`hasProducts()` + `vouchers` kontrolü** — Boş cart durumu doğru handle edilmiş (`process()` başı).
- **`isLogged()` guard'ı `deletemycard()` ve `recurringCancel()` başında** — Patch öncesi yokmuş; biz ekledik. Doğru pattern.

---

## 6. `catalog/view/javascript/qnbpay/qnbpay.js` (90 satır, bolkarco)

### 🟡 Medium

#### M-6.1 Global değişken kirliliği — `selectedText`, `selectedVal`
**Dosya:** `qnbpay.js:2-3`
```javascript
$("#payment_qnbpay_card").on("change", function () {
  selectedText = $(this).find("option:selected").text();
  selectedVal = $(this).find("option:selected").val();
```
**Sorun:** `var`/`let`/`const` yok → `window.selectedText`, `window.selectedVal` global olarak yaratılır. Strict mode'da hata, başka script ile çakışma riski.

**Önerilen:**
```javascript
$("#payment_qnbpay_card").on("change", function () {
  const selectedText = $(this).find("option:selected").text();
  const selectedVal = $(this).find("option:selected").val();
```

#### M-6.2 `console.log(cardN)` production'da kalmış
**Dosya:** `qnbpay.js:31`
```javascript
function getInstallmentsByCard(cardN) {
  console.log(cardN);
```
**Sorun:** Kart numarası (en azından ilk 6 hanesi BIN) **console'a basılıyor**. PCI-DSS kapsamında olmayabilir (sadece BIN ama yine de hassas) — kullanıcının browser console'unda görünüyor.

**Önerilen:** Sil. Veya `if (window.__qnbpay_debug) console.log(cardN);` koşullu.

#### M-6.3 jQuery selector + DOM dependency'leri robust değil
**Dosya:** Tüm dosya
**Sorun:** Element yoksa (twig render etmemişse) `.on()` sessizce hata vermez ama sonraki `.find()` boş döner. Kullanıcı kart seçimi yapamaz → sessiz fail. Ek kontrol:
```javascript
const $card = $("#payment_qnbpay_card");
if ($card.length === 0) {
  console.warn('QNB Pay: kart seçici bulunamadı, event bağlanmadı');
  return;
}
$card.on("change", function () { ... });
```

#### M-6.4 `key == 1` (loose equality) — `if (key == 1) $("#payment_qnbpay_total").val(value.toplam);`
**Dosya:** `qnbpay.js:61`
```javascript
$.each(data.taksitler, function (key, value) {
  if (key == 1) $("#payment_qnbpay_total").val(value.toplam);
```
**Sorun:** PHP'den gelen JSON object key'leri **string** ("1", "2", ...). `key == 1` truthy çünkü loose equality JS'de "1" == 1 → true. Strict equality `===` kullansaydık çalışmazdı. Niyetin "ilk taksit (peşin)" olduğu açık ama implicit type coercion fragile.

**Önerilen:**
```javascript
$.each(data.taksitler, function (key, value) {
  if (String(key) === "1") {
    $("#payment_qnbpay_total").val(value.toplam);
  }
```

### 🟢 Low

#### L-6.5 jQuery ait helpers (`$.each`) modern JS'de `Object.entries().forEach()`
Stil tercihi. Mevcut codebase jQuery üzerine kurulu (OpenCart 3 default), bu yüzden kabul edilebilir. Refactor gerekmiyor.

#### L-6.6 Türkçe-İngilizce karışık string'ler
`'Peşin'` (TR) + `'QNB Pay AJAX Hatası:'` (TR) + `'QNB Pay:'` İngilizce-TR mix. Locale-bound string'ler i18n için language file'a taşınmalı (admin tarafı zaten var). Çağrı `data['button_back']` gibi twig'den geçer.

### ✅ Good Patterns

- **Hata fallback'i — "Peşin" seçeneği gösterilir** — Network hatası → kullanıcı yine de ödeme yapabilir. Resilient UX.
- **Defansif response check** `if (!data || typeof data !== 'object')` — Boş/non-JSON response durumu handle edilmiş.
- **PHP backend ile uyumlu fallback** — Sunucu tarafı `qnbpay.php:253-289` aynı `taksitler[1]` fallback'i döner, JS de aynı şekilde davranır. Backend/frontend tutarlı.

---

## 7. `catalog/view/javascript/qnbpay/qnbpay-script.js` (273 satır, bolkarco)

Bu dosya kart form animasyonu/SVG render'ı + IMask integration. SVG icon string'leri ~100-260 arası satırları kaplıyor.

### 🟡 Medium

#### M-7.1 Yorum satırı dead code (`// generatecard.addEventListener...`)
**Dosya:** `qnbpay-script.js:~210`
```javascript
// generatecard.addEventListener('click', function () {
//     randomCard();
// });
```
**Sorun:** Yorumlanmış kod 6 ay öncesinden mi (Aralık 2024 timestamp). Test/dev için bırakılmış. Production'da sil — version control'de tarihçe var zaten.

#### M-7.2 SVG icon string'leri inline (~5000 char each, ~15 icon)
**Dosya:** `qnbpay-script.js:106-200`
**Sorun:** Card brand icon'ları (amex, visa, mastercard, jcb, discover, ...) string olarak JS dosyası içine gömülmüş. Her bir ~5000 karakter, toplam 80KB+ inline SVG.
- Browser parse time: tüm JS'i okumadan icon göstermek mümkün değil
- Caching: JS değişirse icon'lar da yeniden indirilir
- Maintainability: SVG düzenlemesi imkânsız

**Önerilen:** SVG dosyalarını ayrı asset olarak yükle:
```
catalog/view/image/qnbpay/icons/visa.svg
catalog/view/image/qnbpay/icons/mastercard.svg
...
```
JS'te:
```javascript
const ICON_BASE = 'catalog/view/image/qnbpay/icons/';
ccicon.innerHTML = `<img src="${ICON_BASE}${cctype}.svg" alt="${cctype}">`;
```
Veya CSS background-image. JS file size 99KB → 5KB civarına iner.

Bu büyük refactor, üreticisi (bolkarco)'ya feedback verilmeli.

#### M-7.3 `cardjs()` global function exposure
**Dosya:** `qnbpay-script.js:1`
```javascript
function cardjs() {
  const name = document.getElementById("qnbpayCardOwner");
  // ...
}
```
**Sorun:** Function global. Twig template `cardjs()` çağırıyor olmalı. Daha temiz: IIFE veya `window.QnbpayCard = { init: cardjs }`.

#### M-7.4 Test kart numaraları production code'da
**Dosya:** `qnbpay-script.js:~250-270` (preview'dan)
```javascript
const testCards = [
  "4000056655665556",
  "5200828282828210",
  "371449635398431",
  // ...
];
```
**Sorun:** `randomCard()` test kart üretici function — `// generatecard.addEventListener('click', randomCard);` yorumlu. Production'a sızdırma riski düşük (yorumlu) ama dead code.

**Önerilen:** `randomCard()` ve `testCards` array'ini tamamen sil.

### 🟢 Low

#### L-7.5 jQuery + raw DOM (`document.getElementById`) karışık
Mevcut JS jQuery import etmiyor, native DOM kullanıyor — tutarlı. qnbpay.js (öteki dosya) jQuery kullanıyor. Stil farkı dosyalar arası tutarsız ama her dosya içinde tutarlı.

### ✅ Good Patterns

- **IMask integration ile kart input mask** — Brand-specific format pattern'i kullanıyor (Amex 4-6-5, Visa 4-4-4-4). UX iyileştirici.
- **Card flip animation** — `.flipped` class toggle ile CSS animation tetikleniyor. Native + clean.

---

## 8. `catalog/view/javascript/qnbpay/qnbpay-imask.js` (1775 satır)

**Bu dosya IMask kütüphanesinin vendor distribution'ı.** Üçüncü taraf kod, review kapsamı dışı. Sadece şu not:

### 🟢 Low

#### L-8.1 Vendor library inline yüklenmiş — CDN seçeneği değerlendirilebilir
**Dosya:** `qnbpay-imask.js` (82 KB)
**Sorun:** IMask `imask.js` resmi CDN üzerinden alınabilir (jsdelivr/unpkg). Avantajları:
- Browser cache miss → multiple site visitors'ta paylaşılan cache
- Üretici tarafından düzenli güncelleme

Dezavantajları:
- 3rd-party request → privacy/CSP overhead
- CDN downtime → checkout broken

Mevcut self-hosted yaklaşım daha güvenli (özellikle payment için). Aksiyon: **olduğu gibi bırak**, sadece sürüm güncelleme zamanı kontrol edilsin (IMask 6.x → 7.x değişikliklerine dikkat).

#### L-8.2 Library version belirsiz
Dosya içinde version string'i grep edilemedi (test minify edilmiş). Maintainability için JS yorumu:
```javascript
/*! IMask v6.0.5 — https://imask.js.org/ — MIT License */
```
Eklenebilir (ileride upgrade ne sürümden geldiğimizi gösterir).

---

## Sonuç ve Öncelikli İyileştirme Listesi

### En yüksek öncelik (önce bunlar)

| # | Bulgu | Dosya | Sev. | Eylem |
|---|-------|-------|------|-------|
| F1 | header.twig.bak production'da | `catalog/view/theme/journal3/template/common/header.twig.bak-20260511` | 🟠 | Tema klasörü dışına taşı (`/home/ravenden/backups/theme/`) |
| F2 | robots.txt /catalog/ engellemesi CSS/JS bloklayabilir | `robots.txt:22` | 🟠 | `Disallow: /catalog/` yerine `/catalog/controller/`, `/catalog/model/`, `/catalog/language/` |
| F3 | .htaccess dotfile regex'i yetersiz | `_dotfile_htaccess:12-14` | 🟠 | `RewriteCond %{REQUEST_URI} (^&#124;/)\.(?!well-known/)` ekle |
| F4 | Sitemap URL ham route | `robots.txt:38` | 🟠 | `Sitemap: https://ravendentalgroup.com/sitemap.xml` |
| F5 | qnbpay.php global `dump()` function | `qnbpay.php:3-8` | 🔴 | Sil + `//dump(...)` yorumları temizle |
| F6 | header.twig Twig fallback Journal3'te kırık | `header.twig:79` | 🟠 | OCMOD'u **uygula** (Admin → Modifications → Refresh) — XML doğru, twig'de bırakma |

### Orta öncelik

| # | Bulgu | Dosya | Sev. | Eylem |
|---|-------|-------|------|-------|
| F7 | qnbpay.php class adı convention dışı | `qnbpay.php:10` | 🟠 | `ControllerExtensionPaymentQnbpay` (CamelCase) |
| F8 | recurringCancel() mail fail sessiz | `qnbpay.php:64` | 🟠 | Catch'te `$this->log->write(...)` ekle |
| F9 | recurringCancel() status_id hardcoded 7 | `qnbpay.php:70` | 🟠 | Config fallback + admin panelde ayar |
| F10 | OCMOD Sorun 3 yorum eksik patch | `raven.ocmod.xml:53-62` | 🟡 | Sil veya TODO marker bırak |
| F11 | qnbpay.js console.log kart numarası | `qnbpay.js:31` | 🟡 | Sil |
| F12 | qnbpay-script.js SVG icon inline 80KB | `qnbpay-script.js:106-200` | 🟡 | Bolkarco'ya feedback — ayrı asset'e geç |
| F13 | qnbpay.js global selectedText/Val | `qnbpay.js:2-3` | 🟡 | `const` ekle |
| F14 | .htaccess SQL injection ön-filter false-positive | `_dotfile_htaccess:83-88` | 🟡 | base64_encode kuralını sil veya WAF'a devret |
| F15 | qnbpay.php "sadece peşin" 3x copy-paste | `qnbpay.php:253-309` | 🟡 | `sendFallbackInstallments()` helper |

### Düşük öncelik (vakit varsa)

- HSTS preload header ekleme (subdomain audit sonrası)
- mod_deflate MIME type listesini genişlet (`application/wasm`, `font/ttf`, vb.)
- robots.txt User-agent boş satır temizliği
- qnbpay.php `$pan = $new_str = ...` çift atama silimi
- qnbpay.php magic number 11 (refund status) config'leştir
- qnbpay.php `is_3d` boolean logic netleştir (`&&`/`||` karışıklık)
- Yorum dili tutarlılık (TR/EN karışım) — tüm dosyalar
- qnbpay.js loose equality (`==` → `===`)
- qnbpay-script.js test kart numaralarını sil
- header.twig inline `style` CSS class'a taşı

### Genel değerlendirme

**Bu oturumdaki çalışmamızın kalitesi: B+/A-**

✅ İyi yapılan şeyler:
- `.htaccess` katmanlı savunma yapısı (security headers + file blocking + compression)
- OCMOD XML stratejisi (Journal3 update'lerine dayanıklılık)
- Patch'lerin kendisi (security review'da doğrulandı)
- Doc'larda her edit'in açıklaması (`08-CHANGES-MADE.md`)

⚠️ Eksik kalan / düzeltilmesi gereken:
- Twig edit'i OCMOD'a göre **eski mantıkta kaldı** → OCMOD uygulanmadan H1 hâlâ "Raven Dental"
- robots.txt'te `/catalog/` engellemesi → CSS/JS crawler tarafından bloklanıyor olabilir (test edilmeli)
- `header.twig.bak` tema klasöründe → yedek doğru yere taşınmalı

**Bolkarco kodu değerlendirmesi: C+/B-**
- İşlevsel ama eski tarz JS + global function pollution + class naming convention ihlali
- Patch'lerimiz bunları kısmen düzeltti (login guard, hash control, redirect validation)
- Önerilen: Bolkarco'ya bu code-review özetini paylaş — F5, F7, F8, F11, F12'yi adresleyebilir

---

**Bu doc canlıdır — yapılan düzeltmeler işaretlendikçe yan tarafa `✅ Düzeltildi (YYYY-MM-DD)` ekleyin.**
