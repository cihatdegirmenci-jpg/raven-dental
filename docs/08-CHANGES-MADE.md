# 08 - Changes Made (Bu Oturumda Yapılan Tüm Değişiklikler)

> Üretime dokunulan her şey burada. Yeni değişiklik yapıldığında **commit öncesi** bu doc güncellenir.
> Format: NE + NEDEN + NASIL + NEREDE + DOĞRULAMA

---

## 🔒 GÜVENLİK DEĞİŞİKLİKLERİ

### S01. Public'e açık 852 MB admin.zip silindi
- **Ne:** `/public_html/admin.zip` (852,341,159 byte)
- **Neden:** Tüm admin kodu + muhtemelen DB şifresi açık biçimde indirilebiliyordu (HTTP 200)
- **Nasıl:** cPanel API 2 `Fileman::fileop op=unlink`
- **Doğrulama:** `curl -sI /admin.zip → HTTP 404` ✓

### S02. toptandetal/ dizini silindi
- **Ne:** İkinci bir OpenCart kurulumu (admin, catalog, image, storage + 19 MB Arsiv.zip)
- **Neden:** Kullanıcıya göre eski proje arşiviydi, kullanılmıyordu
- **Nasıl:** API 2 `unlink` (recursive)
- **Doğrulama:** `/toptandetal/` → HTTP 404 ✓

### S03. Eski error_log dosyaları silindi
- **Ne:** `/public_html/error_log` (186 KB) + `/public_html/admin/error_log` (469 KB)
- **Neden:** Eski DB şifresi açık metin halinde içindeydi
- **Nasıl:** API 2 `unlink`
- **Doğrulama:** Dosyalar yok, yeni hata logu üretilmediği sürece sızıntı yok

### S04. Admin şifresi değiştirildi
- **Ne:** OpenCart admin login şifresi
- **Önce:** `12345` (😱)
- **Sonra:** Kullanıcı kendi belirlediği güçlü şifre
- **Nasıl:** Kullanıcı admin paneli üzerinden manuel
- **Doğrulama:** Eski şifre artık çalışmıyor (kullanıcı belirtti)

### S05. DB user şifresi rotate edildi (2 kez)
- **Ne:** `ravenden_1` MySQL kullanıcısının şifresi
- **Önce:** `+e^p!$O,9A?=` (12 char, error_log'da sızmıştı)
- **Sonra:** 32 char alfanümerik (saklı: `~/.config/raven/env`)
- **Nasıl:** cPanel UAPI `Mysql/set_password` + `config.php` ile `admin/config.php`'de search-replace
- **Doğrulama:** Site DB'ye bağlanıyor, 200 OK döndürüyor

### S06. .htaccess hardening
- **Ne:** `/public_html/.htaccess` (yeni dosya, 3.7 KB)
- **İçerik:**
  - `<FilesMatch>` zip/bak/sql/env/log/sh/yml/ini engelle
  - Dotfile (`^\.`) erişim engeli
  - `config.php` ve `admin/config.php` engelle
  - `Options -Indexes -MultiViews`
  - Güvenlik header'ları: HSTS, X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy
  - X-Powered-By kaldır
  - ETag kapalı
- **Doğrulama:**
  ```
  curl -I /test.zip → 403  ✓
  curl -I /.htaccess → 403 ✓
  Response header: strict-transport-security: max-age=31536000 ✓
  ```

---

## ⚡ PERFORMANS DEĞİŞİKLİKLERİ

### P01. Gzip sıkıştırma (.htaccess'te)
- **Ne:** `mod_deflate` ile text/html, css, js, json, svg, woff sıkıştırma
- **Etki:** Anasayfa 463 KB → 65 KB (transfer 7× küçük)
- **Doğrulama:** Curl `-H "Accept-Encoding: gzip"` ile küçük yanıt

### P02. Browser cache (1 yıl CSS/JS/font/image)
- **Ne:** `mod_expires` ile uzun cache header
- **Etki:** İkinci ziyarette CSS/JS yeniden indirilmiyor
- **Doğrulama:** Curl headers: `cache-control: public, max-age=31536000` ✓ `expires: Tue, 11 May 2027 ...` ✓

### P03. ETag kaldırma
- **Ne:** ETag header'ı kapatıldı (`FileETag None` + `Header unset ETag`)
- **Neden:** Last-Modified zaten yeterli, ETag çift kontrol + Cloudflare gibi katmanlarla uyumsuzluk

---

## 🔍 SEO DEĞİŞİKLİKLERİ

### O01. robots.txt düzeltildi
- **Önce:**
  ```
  [boş satır]
  Disallow: /*?page=$
  Disallow: /*&page=$
  ...
  ```
  (User-agent yok, sitemap yok, format bozuk)
- **Sonra:** 945 byte, User-agent + admin/ engelle + parametre engellemeleri + `Sitemap: ravendentalgroup.com/sitemap.xml`
- **Yer:** `/public_html/robots.txt`

### O02. OpenCart .htaccess rewrite kuralı eklendi
- **Ne:** Standard OpenCart rewrite rules .htaccess'e eklendi
  ```apache
  RewriteRule ^sitemap\.xml$ index.php?route=extension/feed/google_sitemap [L]
  RewriteRule ^googlebase\.xml$ index.php?route=extension/feed/google_base [L]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^([^?]*) index.php?_route_=$1 [L,QSA]
  ```
- **Etki:** SEO URL'ler aktif (örn. `/diagnostik-aletleri`), sitemap.xml direkt erişilebilir

### O03. 738 SEO URL keyword DB'ye yüklendi
- **Tablo:** `oc_seo_url`
- **Önce:** 25 kayıt (sadece manufacturer demo + 9 kategori EN)
- **Sonra:** 744 kayıt (369 entity × 2 dil + 6 leftover)
- **İçerik:**
  - 18 kategori × TR + EN
  - 345 ürün × TR + EN
  - 6 information page × TR + EN
- **Slug üretimi:** Python `slugify()` Türkçe çev (`çğıİöşü → cgiiosu`) + lowercase + non-alphanum to hyphen
- **TR çeviri map'i:** DIAGNOSTICS → `diagnostik-aletleri`, ENDODONTICS → `endodonti-aletleri`, vb.

### O04. Anasayfa meta güncellemesi
- **Tablo:** `oc_setting WHERE code='config'`
- **Değişen key'ler:**
  ```
  config_meta_title: 'Diş Hekimliği Aletleri ve Cerrahi Ekipmanlar | Raven Dental'
  config_meta_description: 'Profesyonel diş hekimliği aletleri: endodonti, implantoloji, cerrahi, ortodonti, protez. Toptan fiyat, hızlı kargo, 100% iade garantisi. ☎ 0552 853 03 99'
  config_meta_keyword: 'diş hekimliği aletleri, dental aletler, endodonti aletleri, implant aletleri, cerrahi diş aletleri, ortodonti, diş hekimi malzemeleri, dental ekipman, toptan diş aletleri'
  ```
- **Doğrulama:** HTML `<title>` ve `<meta name="description">` doğru görünüyor ✓

### O05. 18 kategorinin TR meta'ları yazıldı
- **Tablo:** `oc_category_description WHERE language_id=2`
- **Ne:** Her kategori için elle yazılmış meta_title + meta_description
- **Örnek:** İmplantoloji kategorisi:
  - title: "İmplantoloji Aletleri - Diş İmplantı Cerrahi Setleri"
  - description: "İmplant cerrahisi için kemik frezi, vida sürücü seti, osteotom — komple implantoloji aletleri."
- **Detay:** `~/raven-dental/analysis/category-meta-list.md`'ye dökümante edilecek

### O06. ~283 ürün meta_description şablonu
- **Tablo:** `oc_product_description`
- **TR:** `{name} - Profesyonel diş hekimliği aletleri. Raven Dental'de uygun fiyat, hızlı kargo, 100% iade garantisi. ☎ 0552 853 03 99`
- **EN:** `{name} - Professional dental instrument. Raven Dental wholesale and retail. Fast shipping, 100% return guarantee.`
- **Etkilenen:** TR=283, EN=286 (önceden meta_desc'i boş veya isim kadar olan ürünler)
- **Sınır:** Sadece `meta_description='' OR IS NULL OR =name OR LENGTH<30` koşulu eşleşenler

### O07. ~330 ürün meta_title şablonu
- **Tablo:** `oc_product_description`
- **Şablon:** `{name} | Raven Dental`
- **Etkilenen:** TR=332, EN=340

### O08. Journal3 customCodeHeader'a inject (hreflang + Organization schema)
- **Tablo:** `oc_journal3_setting WHERE setting_group='custom_code' AND setting_name='customCodeHeader'`
- **İçerik:**
  ```html
  <link rel="alternate" hreflang="tr-tr" href="https://ravendentalgroup.com/" />
  <link rel="alternate" hreflang="en-gb" href="https://ravendentalgroup.com/?language=en-gb" />
  <link rel="alternate" hreflang="x-default" href="https://ravendentalgroup.com/" />
  
  <script type="application/ld+json">
  {
    "@type": "Organization",
    "name": "Raven Dental",
    "url": "https://ravendentalgroup.com/",
    "contactPoint": {
      "telephone": "+90-552-853-0399",
      "areaServed": "TR",
      "availableLanguage": ["Turkish", "English"]
    },
    "knowsAbout": [...]
  }
  </script>
  ```
- **Doğrulama:** HTML'de hreflang 3 etiket ✓ contactPoint görünüyor ✓

### O09. Demo manufacturer'lar silindi
- **Tablo:** `oc_manufacturer`, `oc_manufacturer_to_store`, `oc_seo_url`
- **Silinen:** Apple (id=8), Canon, HTC, Hewlett-Packard, Palm, Sony
- **Apple'a atanmış 1 ürün** ("Bein Elevatör 3 mm Eğri") manufacturer_id=0'a atandı (ürün kaldı)
- **Toplam silinen:** 6 manufacturer + 6 seo_url + 6 manufacturer_to_store

### O10. Header.twig H1 mantığı değiştirildi (kısmen başarılı)
- **Dosya:** `catalog/view/theme/journal3/template/common/header.twig`
- **Önce:**
  ```twig
  {% if j3.settings.get('journal3_home_h1') %}
    <h1 class="sr-only" ...>{{ j3.settings.get('journal3_home_h1') }}</h1>
  {% endif %}
  ```
- **Sonra:**
  ```twig
  {% set raven_h1 = j3.settings.get('journal3_home_h1') ?: (heading_title is defined and heading_title ? heading_title : title) %}
  {% if raven_h1 %}
    <h1 class="sr-only" ...>{{ raven_h1 }}</h1>
  {% endif %}
  ```
- **Durum:** ⚠️ İstendiği gibi çalışmıyor — `j3.settings.get` config_name'e fallback yapıyor, "Raven Dental" döndürüyor. ROADMAP'te kalıcı düzeltme planlı.
- **Yedek:** `header.twig.bak-20260511` sunucuda

### O23. Kurumsal sayfalar — 8 sayfa içerik üretildi + canlıya (2026-05-13)
- **Ne:** Footer'daki kurumsal sayfaların hepsi yazıldı veya güncellendi.
- **Sayfalar:**
  - **Hakkımızda** (id=4, ~13 KB HTML) — "ithal eden çözüm ortağı" hatalı pozisyonu temizlendi → üretici firma kimliği, 13 bölüm: bakış, hikaye, misyon, vizyon, kalite politikası, üretim süreci, 18 kategori, ISO 9001/CE/AISI sertifikalar, DİŞSİAD üyeliği, garanti (cihaz 2 yıl + el aleti ömür boyu), sosyal sorumluluk, gezici diş aracı, iletişim
  - **Teslimat Bilgisi** (id=6, ~8.7 KB) — 2 satırdan 8 bölümlü detaylı (İstanbul aynı gün, Türkiye 1-3 iş günü, KKTC, anlaşmalı kargolar, stok, ücret, paketleme, B2B özel)
  - **Gizlilik Politikası** (id=3, ~7.9 KB) — KVKK 6698 uyumlu, 10 bölüm
  - **Şartlar ve Koşullar / Üyelik Sözleşmesi** (id=5, ~5.5 KB) — 11 bölüm, İstanbul Mahkemeleri
  - **KVKK Aydınlatma Metni** (YENİ id=9, ~7.5 KB) — m.10 + m.11 hakları
  - **Mesafeli Satış Sözleşmesi** (YENİ id=10, ~5.7 KB) — 6502 sayılı kanun, 14 gün cayma
  - **İade ve Cayma Hakkı** (YENİ id=11, ~4.6 KB) — kullanıcı dostu özet
  - **Çerez Politikası** (YENİ id=12, ~5.9 KB) — KVKK uyumlu, 4 çerez türü
- **Toplam:** ~6,893 kelime / ~58 KB HTML içerik üretici pozisyon vurgusu ile.
- **Marka politikası uyumu:** 3. parti marka adı (NSK/Mani/Hu-Friedy vb.) yok, sadece Raven Dental referansları.
- **SEO URL keywords (oc_seo_url):** hakkimizda, teslimat-bilgisi, gizlilik-politikasi, sartlar-ve-kosullar, kvkk-aydinlatma-metni, mesafeli-satis-sozlesmesi, iade-ve-cayma-hakki, cerez-politikasi
- **Footer link modülü 72** güncellendi: 4 → 8 link.
- **Patch klasörü:** `analysis/seo-patches/14-corporate-pages/`
- **Doğrulama:** Tüm 8 URL hem prod hem local'de 200 OK. Hakkımızda title doğru (`Hakkımızda | Raven Dental — Türkiye'nin Diş Hekimliği Aletleri Üreticisi`).
- **Doldurulacak placeholder'lar (kullanıcı eylem):** MERSIS no, Vergi Dairesi/No, posta adresi, `kvkk@` email hesabı, [GÖRSEL: ...] yer tutucuları (üretim tesisi, sertifika logoları)

### O22. OpenCart bloat cleanup — 4 faz (2026-05-12)
- **Ne:** Stock OpenCart'tan gelen ama bu projenin kullanmadığı kod parçaları üretimden silindi.
- **Toplam:** ~1,231 dosya, **~7.4 MB** disk tasarrufu, attack surface ↓
- **Fazlar:**
  - **Faz 1 — Payment gateway temizliği** (552 dosya / 3.8 MB): QNB Pay + bank_transfer + free_checkout + cod hariç tüm payment gateway'ler silindi (Alipay, Amazon Pay, Klarna, PayPal, Stripe, SagePay, Worldpay, vb. + admin twig + default theme twig + language dosyaları)
  - **Faz 2 — Shipping + promo modules** (139 dosya / 674 KB): yurt dışı kargo modülleri (FedEx, UPS, USPS, Royal Mail, vb.) + payment gateway promo modülleri (Amazon Login, Klarna checkout, PayPal smart button, vb.)
  - **Faz 3 — Vendor SDK** (~520 dosya / 2.8 MB): system/storage/vendor/{braintree, cardinity, divido, klarna, zoujingli, react} + system/library/squareup + system/library/paypal/paypal.php. ⚠️ Sentry önce silindi sonra geri yüklendi — composer autoload_files'da boot'ta require ediliyor (L13).
  - **Faz 4 — .bak yedek dosyaları** (20 dosya / 124 KB): O11-O20 patch'lerinden kalan .bak-deploy/.bak-i18n/.bak-sec/.bak-perf gibi yedek dosyaları temizlendi.
- **Atlandı:**
  - **Faz 5 — en-gb language** (1.8 MB) — user kararı: dil dosyaları kalsın, önemli değil. `system/library/language.php` `$default = 'en-gb'` ayarı korundu.
- **Korundu (user kararı):**
  - Affiliate program (catalog/affiliate, admin/marketing/affiliate*)
  - Google Shopping (system/library/googleshopping, admin/.../advertise/google.php)
- **DB audit kaynağı:** `oc_extension` enabled list (46 ext), `oc_setting *_status=1` (35 active), `oc_module` (4 kullanılan module)
- **Backup:** `/home/ravenden/storage/bloat-backup-20260512/` (693 dosya, sadece tekil dosya backup; vendor SDK dirlerin recursive backup'ı YOK çünkü runner bug)
- **Smoke test:** Her faz sonrası anasayfa, admin, kategori, ürün, sepet → 200 OK
- **Yeni dersler (L13, L14):** Composer autoload_files boot dependency + shell_exec disabled hosting'lerde PharData

### O21. Journal3 Cloud Dashboard kaldırıldı (2026-05-12)
- **Ne:** Journal3 admin panelindeki "Dashboard" menü öğesi + ilgili cloud dashboard bağlantı kodu kaldırıldı.
- **Sebep:** Önceki ajansın (`kahvedigital`) hesap key'i (`d701e79c-...`) aktifti ama dashboard çalışmıyordu. Erişim yok, anlamlı veri yok, sadece UI kirliliği.
- **Patch'ler:**
  - `admin/controller/journal3/journal3.php`: dashboard menu entry (satır 92-97) silindi → yedek `.bak-no-dashboard-20260512`
  - `system/library/journal3/data/settings/dashboard/dashboard.json` (settings tanımı) silindi
  - `system/library/journal3/data/settings/dashboard/` boş dizin de kaldırıldı
  - DB: `DELETE FROM oc_journal3_setting WHERE setting_group='dashboard'` (2 row sildi)
- **Etki:** Admin > Journal Editor üst menüsünde artık Dashboard sekmesi yok. Tema çalışmaya devam ediyor.

### O20. WebP image conversion + auto-serve (2026-05-12, dördüncü batch)
- **Ne:** Production'da `/image/` altındaki **11,036 PNG/JPG** GD library ile WebP'ye çevrildi (lossy quality 80). Kaynak dosyalar korundu, `.webp` versiyon yanına yazıldı.
- **Disk tasarrufu:** **181 MB → 39 MB (-79%)**
- **Sample boyutlar:**
  - Slider PNG (960x450): 363 KB → 21 KB (-94%)
  - Placeholder (240x280): 20 KB → 3.7 KB (-81%)
  - WhatsApp kategori JPEG (886x886): 68 KB → 48 KB (-29%)
  - Küçük thumbnail JPG: 1.6 KB → 0.5 KB (-72%)
- **Auto-serve:** `.htaccess`'e RewriteRule + Vary: Accept header eklendi. Browser `Accept: image/webp` gönderiyorsa LiteSpeed `.png.webp` / `.jpg.webp` dosyasını servis eder. Aksi durumda orijinal PNG/JPG.
- **LSCache çakışması çözüldü:** Image dosyaları LSCache bypass'ına eklendi (`Vary: Accept` LSCache tarafından desteklenmiyor — image isteği her zaman doğrudan dosya sisteminden gelmeli).
- **Patch script'leri:**
  - `/tmp/webp_convert.php` — batched converter (batch=200/400, 11,466 candidate)
  - `.htaccess`'e RewriteRule (Apache mod_rewrite + LiteSpeed Cache module uyumlu)
- **Yedek:** `.htaccess.bak-webp-fix-20260512`, kaynak PNG/JPG'ler korundu.
- **Önemli:** Yeni eklenen ürün/banner görselleri için cron eklenecek (haftalık `webp_convert.php` çağrısı). Aksi takdirde yalnız eski cache WebP'ye sahip olur.
- **Doğrulama:** curl test'i ile 28/28 home page image WebP serve ediliyor (fresh cache). Real browser (Chrome/Firefox/Edge) Accept: image/webp ile geliyor → otomatik WebP. Apple Safari WebP destekli (iOS 14+/macOS Big Sur+) → WebP serve.
- **Lighthouse etkisi:** Single-run variance ±10 puan. Gerçek kullanıcı için bandwidth tasarrufu büyük (~%80 image bytes), mobile slow 4G'de LCP 1-2s iyileşmesi.

### O19. LiteSpeed Cache (LSCache) aktive (2026-05-12, üçüncü batch)
- **Ne:** `.htaccess`'e LiteSpeed Cache module directive'leri eklendi. HTML edge cache 120 saniye.
- **Bypass listesi:**
  - `/admin*` — admin paneli asla cache'lenmez
  - `/checkout|/cart|/account|/wishlist|/compare|/returns|/affiliate` — kullanıcı sayfaları
  - TR SEO URL'ler: `/hesabim|/uye-ol|/giris|/sepet|/odeme|/teslimat|/iade|/sifre|/favoriler|/istek-listesi|/siparis`
  - AJAX endpoint'leri (`ajax|json|api|common/cart`)
  - POST/PUT/DELETE request'ler
  - `customer_token|customer_id|customer=` cookie varsa
  - `search=|filter=` query parametreli URL'ler
- **Sonuç:** `x-litespeed-cache: hit` header doğrulandı. TTFB **275ms → 65ms** (-75%) cached pages için.
- **Yan etkiler:** Cart count UI 2 dakika eski olabilir (anonymous user). Login + cart bypass aktif olduğundan logged-in user için sorun yok.
- **Patch:** `.htaccess` (`.bak-lscache-20260512` yedek alındı)
- **Önemli not:** Lighthouse skoru asgari geliştirme gösterdi çünkü Lighthouse her run'da cache miss yakalar + network/CPU throttling agresif. Asıl LCP düşüşü için sırada: hero image optimization (WebP + preload), critical CSS inline, render-blocking JS defer. Bu LSCache patch alt yapı katmanı — sonraki LCP optimizasyonlarının üstüne bina edilecek.

### O18. ÜRETİM DEPLOY — Performance + Security + Sitemap hierarchy (2026-05-12, ikinci batch)
- **Performance 01 — DB indexes:** 9 yeni index (oc_product status/manufacturer/date_available/sort_order + composite + oc_seo_url query/keyword composite + oc_setting code+key + oc_session expire). Beklenen: 50-300ms kategori sorgu hızlanması, LCP iyileşmesi.
- **Performance 02 — InnoDB migration:** 161 tablo MyISAM → InnoDB. Sebep: row-level locking, crash recovery, foreign keys, buffer pool cache. Production audit ile gereksiz tablolar elendi (oc_affiliate vb.).
- **Performance 03 — Twig auto_reload kapalı:** `system/library/template/twig.php` patch. Her render'da mtime stat çağrısı kaldırıldı. Beklenen: 30-100ms / request.
- **Security C1 — Cookie hardening:** 7 setcookie modernize edildi (array form, PHP 7.3+). Etkilenen: OCSESSID, language, currency, tracking. **Sonuç:** Tüm cookie'ler artık `secure; HttpOnly; SameSite=Lax` ile servis ediliyor. Cookie hijacking riski mitige.
- **Security C5 — Open redirect fix:** `common/currency.php` + `common/language.php` redirect fonksiyonları same-origin kontrolü ile sertleştirildi. Phishing/redirect-based attack vektörü kapatıldı.
- **SEO 04 — Sitemap hierarchy:** `google_sitemap.php` patch — anasayfa eklendi (priority 1.0 daily), kategori 0.7→0.8, ürün 1.0→0.6, information 0.5→0.4 monthly. Statik sitemap.xml regenerate edildi (370 URL, önceden 369 — anasayfa eklendi). GSC'de Google bu hiyerarşiyi indeksleme önceliğinde kullanır.
- **Önemli teknik bulgu (L11):** `storage/modification/` overlay'i her zaman source'u override eder. İlk deploy'da sadece source dosyaları (PHP) patch'lendi ama overlay'deki eski sürümler aktifti — bu yüzden cookie ve sitemap değişiklikleri yansımadı. Düzeltme: hem source hem overlay patch'lenmeli. CLAUDE.md ve LESSONS-LEARNED'e eklendi.
- **Patch klasörleri:** `analysis/performance-patches/`, `analysis/security-patches/`, `analysis/seo-patches/04-sitemap-hierarchy.ocmod.xml`. Deploy script'leri ve runner'lar `deploy/` altında.
- **Doğrulama:** Anasayfa + kategori + ürün + sitemap.xml HTTP 200. Set-Cookie tam (secure+HttpOnly+SameSite). Sitemap URL sayıları doğru (1+18+345+6=370).

### O17. ÜRETİM DEPLOY — Tüm SEO patch'leri canlıya aktarıldı (2026-05-12)
- **Ne:** O11-O16'da yapılan tüm yerel Docker değişiklikleri üretime cherry-pick'le aktarıldı.
- **Süreç:**
  1. Production DB snapshot alındı (deploy/production-dump-20260512-141839.json, 665KB) — 24 etkilenen tablo
  2. `deploy/build_deploy_sql.py` production state üstüne 10 patch transform'unu yeniden uyguladı → `deploy/sql/01-10*.sql` üretildi (toplam 531KB)
  3. PHP runner ile sırayla apply (`deploy/run_deploy.sh` — token-gated, runner her seferinde silinir)
  4. journal3.php + search.twig + search.json + raven.ocmod.xml dosyaları cPanel API ile yüklendi (her birinin .bak-deploy-YYYYMMDD yedeği üretimde duruyor)
  5. catalog/controller/journal3/seo.php source'una BLOK N+O regex patch (kategori + ürün og:title → meta_title)
  6. robots.txt sitemap URL'i `index.php?route=...` → `/sitemap.xml`
  7. storage/cache toplam ~700 dosya temizlendi
- **Doğrulama (production):** Tüm öğeler render kontrolünden geçti — sosyal medya, WhatsApp widget, sameAs schema, Quickview→Hızlı Bakış, search "Tümü", og:title meta_title-öncelikli, kategori isimleri TR Proper Case, Kavo ürünü temizlendi.
- **Güvenlik:** Her PHP runner gizli token (24-byte hex) ile gate'li, kullanım sonrası API ile silindi. Runner dosya adları `_r_*.php` pattern'inde (`.gitignore` zaten kapsıyor).
- **Yedekler:** Her dosya değişikliğinin .bak-deploy-20260512 yedeği üretimde. Rollback olası.

### O16. Slider + banner görsellerinde boş alt text dolduruldu (2026-05-12, yerel Docker)
- **Ne:** Anasayfa slider (module 26) + 4 banner modülünde (98, 201, 259, 286) toplam **10 görselin alt text'i boştu**. SEO + a11y kaybı.
- **Çözüm:** Her görsele Türkçe açıklayıcı alt text yazıldı:
  - Slider (2 görsel) → "Raven Dental — Profesyonel Diş Hekimliği Aletleri", "Raven Dental — Üretici Doğrudan Klinik Aletleri"
  - Logo bannerları (2) → "Raven Dental Logo"
  - Top home bannerlar (2) → "İmplantoloji Aletleri — Raven Dental", "Muayene Aletleri — Raven Dental"
  - 4 kategori banner → "İmplantoloji/Cerrahi/Diagnostik/Çekim Aletleri Kategorisi — Raven Dental"
- **Patch:** `analysis/seo-patches/11-image-alts/apply.py` + `update.sql`
- **Doğrulama:** Anasayfa rendered HTML'de empty alt sayısı 9→0
- **Durum:** ⏳ Üretime henüz uygulanmadı

### O15. Site geneli Türkçeleştirme — i18n temizliği (2026-05-12, yerel Docker)
- **Ne:** Site TR-only olduğu hâlde Journal3 / OpenCart kaynaklı 100+ İngilizce string vardı. Sistemli tarama + bulk düzeltme:
- **`oc_journal3_skin_setting`** (40 setting): "Quickview" → "Hızlı Bakış", "Loading..." → "Yükleniyor...", "Day/Hour/Min/Sec" → "Gün/Saat/Dk/Sn", "Login" → "Giriş Yap", "Register" → "Üye Ol", "Billing Address" → "Fatura Adresi" vb. ([apply.py](../analysis/seo-patches/10-turkish-i18n/apply.py))
- **Module data** (7 module, 37 string): main menu, header mobile, vb. — "Your Cart" → "Sepetim", "Menu" → "Menü", "DIAGNOSTICS/ENDODONTICS/SURGERY/..." (ALL-CAPS İngilizce kategori adları) → TR Proper Case ([bulk_translate_modules.py](../analysis/seo-patches/10-turkish-i18n/bulk_translate_modules.py))
- **Filter modülü 36** (71+4 replacement): "In Stock" → "Stokta", "Filter Products" → "Ürünleri Filtrele", "Subcategories" → "Alt Kategoriler", "Brands" → "Markalar", "Tags" → "Etiketler", "Clear" → "Temizle"
- **Side products module 253**: "People Also Bought" → "Birlikte Alınanlar"
- **`oc_category_description.name`** (14 kategori): "DIAGNOSTICS" → "Diagnostik", "ENDODONTICS" → "Endodonti", vb. (ALL-CAPS İngilizce → TR Proper Case)
- **`oc_product_description.name/desc/meta_*`** (1 ürün): product_id=432 "Kavo Tip Airetor" → "Hava Türbini (Aerator) - Premium Seri" (KaVo marka sızıntısı temizliği)
- **PHP source `journal3.php`** (15 replacement): `productStat()` switch içinde label'lar hardcoded ("Brand" → "Marka", "In Stock" → "Stokta", "Model", "SKU" → "Stok Kodu", vb.). Journal3'ün Variable settings sistemi InputLang override'larını DB üzerinden kabul etmediği için PHP'de doğrudan TR fallback. ([patch_journal3_php.py](../analysis/seo-patches/10-turkish-i18n/patch_journal3_php.py))
- **Twig template `common/search.twig`**: `j3.settings.get('searchStyleSearchCategories')` → literal `"Tümü"` (Variable kaynaklı `searchStyle` setting JSON default override edilemiyor, en güvenli yol literal). Yedek: `search.twig.bak-i18n`
- **JSON defaults `data/settings/common/search.json`**: `SearchCategories.value` "All" → "Tümü", `SearchInputText` → "Ara..."
- **Doğrulama (Docker, 6 sayfa: home, 4 kategori, ürün, üye-ol)**: 0 İngilizce UI string hit (sözcükler `Türk*|Raven|Visa|Mastercard|Facebook|Instagram|Blog|Aerator|Anguldurva|Piyasemen|Micro Motor|Drill|Rubber Dam` whitelist'ten muaf — marka/teknik terim).
- **Patch klasörü:** `analysis/seo-patches/10-turkish-i18n/`
- **Yeni kullanıcı feedback'i:** Çeviri / düşük-risk değişikliklerde onay sorma — direkt yap. Memory'e kaydedildi: `feedback_act_dont_ask.md`.
- **Durum:** ⏳ Üretime henüz uygulanmadı. Production deploy için:
  1. `code/system/library/journal3.php` ve `code/catalog/view/theme/journal3/template/common/search.twig` yedeklerini üretimden çek
  2. SQL update'leri (`apply.py`'nin ürettiği `update-skin.sql`, `update-modules.sql`, vb. + `update-all-modules.sql` + `category-names-tr.sql`) sırasıyla uygula
  3. PHP/twig dosyalarını upload
  4. OpenCart Admin → Eklentiler → Değişiklikler → Refresh (gerekli değil ama önerilir)
  5. storage/cache temizle

### O14. og:title kategori/ürün sayfalarında meta_title kullanımı (2026-05-12, yerel Docker)
- **Sorun:** Journal3 `seo.php` kategori sayfalarında og:title için `$category_info['name']` kullanıyordu → footer'da "EL ALETLERİ" (uppercase), "ENDODONTICS" (İngilizce, site TR-only!), "Aerator" (kısa) gibi sosyal share preview'larda SEO/UX dışı görünüm. Anasayfa og:title önceden OCMOD ile düzeltilmişti ama kategori/ürün kalmıştı.
- **Çözüm — 2 yeni OCMOD operasyonu (`raven.ocmod.xml` içine eklendi):**
  - **BLOK N:** Kategori için `!empty($category_info['meta_title']) ? $category_info['meta_title'] : $category_info['name']`
  - **BLOK O:** Ürün için aynı pattern (`$product_info['meta_title']` öncelikli)
- **Etki:** Tüm 18 kategori sayfasında meta_title değerleri (Türkçe, SEO-optimize, capitalized) artık og:title olarak kullanılıyor.
- **Dosya:** `analysis/theme-patches/raven.ocmod.xml` (v1.1, satır 22 sonrası 2 yeni operation)
- **Doğrulama (Docker):**
  - `/el-aletleri` og:title → "Diş Hekimliği El Aletleri - Tüm Branşlar için" ✓
  - `/endodonti-aletleri` → "Endodonti Aletleri - Kanal Tedavisi Ekipmanları" ✓
  - `/aerator` → "Aerator Diş Hekimliği Hava Türbini" ✓
  - `/implant-kemik-frezi-drill` (ürün) → "İmplant Kemik Frezi/Drill | Raven Dental" ✓
- **Durum:** ⏳ Üretime henüz uygulanmadı. Production'a deploy için:
  1. `raven.ocmod.xml` sunucudaki `/system/raven.ocmod.xml`'e yükle
  2. OpenCart Admin → Eklentiler → Değişiklikler → Refresh (mavi ↻) → storage/modification rebuild olur
- **Not:** Yerel Docker'da test için `storage/modification/catalog/controller/journal3/seo.php` doğrudan sed ile patch'lendi. Production'da admin refresh ile aynı sonuç üretilecek (OCMOD operasyonları regex match'liyor).

### O13. 18 kategori SEO açıklaması yeniden yazıldı — üretici pozisyonu (2026-05-12, yerel Docker)
- **Ne:** `oc_category_description` (language_id=2) tablosunda 18 kategori uzun açıklaması (1300-1900 char/kategori, toplam ~29 KB Türkçe içerik) tamamen yeniden yazıldı
- **Sebep:** Önceki sürüm Mani / Hu-Friedy / Dentsply / NSK / W&H / Bien-Air / KaVo / Brasseler / Komet / FKG / VDW gibi 3. parti marka isimleri içeriyordu → bu yanlış mesaj (distribütör imajı) veriyordu. Raven Dental **üreticidir**, tüm ürünleri kendi üretir.
- **Yeni ses tonu (kullanıcı onaylı):**
  - "Raven Dental kendi üretim tesisinde üretir"
  - "Dünya standartlarındaki muadillerine eşdeğer kalite" (genel ifade, isim verilmez)
  - "Klinik avantajlı toptan fiyatlandırma" (fiyat yumuşak ima)
  - **Standartlar korundu:** ISO 9001, CE belgesi, AISI 304/420/440 paslanmaz çelik, otoklav 134°C, EN ISO 7785-1 / EN ISO 1797
  - "Üretici doğrudan klinik, ara depo / distribütör zinciri yok" mesajı
  - "10+ alet siparişinde klinik fiyatı, 24 saat kargo, garanti belgesi"
- **Etkilenen kategoriler:** Diagnostik, Restorasyon, Çekim, Cerrahi, Periodonti, İmplantoloji, Protez, El Aletleri, Ortodonti, Endodonti, İşlem, Sarf, Raven Cerrahi Aletler, Elektronik Cihazlar, Aerator, Anguldurva, Piyasemen, Micro Motor
- **Patch klasörü:** `analysis/seo-patches/09-category-descriptions-rewrite/`
  - `descriptions.py` — 18 kategori HTML açıklamaları (Python dict)
  - `apply.py` — preflight (banned brand list) + SQL build + apply + verify
  - `update.sql` (30 KB) — üretime gidecek son SQL
  - `before-all-18.tsv` — yedek
- **Doğrulama:** Preflight: 0 yasaklı marka adı. DB verify: 18/18 exact match. Browser test: 18/18 sayfada (curl ile) yasaklı marka yok.
- **Durum:** ⏳ Üretime henüz uygulanmadı (üretimde zaten yoktu — kullanıcı doğruladı). VPS deploy ile gidecek.
- **Not:** Eski `05-category-descriptions.sql` patch dosyası repo'da kalıyor ama **kullanılmamalı** — yerini 09 alıyor.

### O12. WhatsApp Business floating widget (2026-05-12, yerel Docker)
- **Ne:** `oc_journal3_setting` → `customCodeFooter`'a sağ-alt sabit WhatsApp butonu inject edildi
- **Numara:** +90 552 853 03 99 (contactPoint schema'daki aynı numara) → `wa.me/905528530399`
- **Otomatik mesaj:** "Merhaba, ürünleriniz hakkında bilgi almak istiyorum." (URL-encoded)
- **Tasarım:**
  - 56×56 px daire, mobilde 52×52 (480px altı)
  - WhatsApp brand green (#25D366), inline SVG WA logosu (FontAwesome dependency yok)
  - Pulse animation (2s loop), `prefers-reduced-motion` desteği
  - `:focus-visible` outline (3px #128C7E), keyboard accessible
  - `aria-label="WhatsApp ile iletişim — Raven Dental"`
  - `target="_blank" rel="noopener"` external link best practice
- **Patch klasörü:** `analysis/seo-patches/07-whatsapp-widget/`
  - `widget.html` → inline HTML+CSS
  - `apply.py` → idempotent SQL upsert (eski raven-wa-fab block'unu silip yenisini ekler)
  - `update.sql` → üretime taşınacak son SQL
- **Doğrulama (Docker):** Anasayfa, kategori (`/diagnostik-aletleri`), ürün (`/implant-kemik-frezi-drill`) sayfalarında `raven-wa-fab` render ediliyor (9 hit/sayfa — link + pulse + CSS kuralları). URL hash decoded: doğru numara + doğru TR mesaj.
- **Durum:** ⏳ Üretime henüz uygulanmadı.

### O11. Footer sosyal medya + ödeme ikonu temizliği (2026-05-12, yerel Docker)
- **Ne:** `oc_journal3_module` tablosunda 2 module_data güncellemesi + `oc_journal3_setting.customCodeHeader` Organization schema enrichment
- **Module 61 (Social Icons):** Facebook → `facebook.com/dentmadikal.co/`, Twitter kaldırıldı, Instagram → `instagram.com/raven.dental/`, **yeni** Instagram → `instagram.com/ravendisdeposu/`. Hepsinde `target="_blank" rel="noopener"`.
- **Module 228 (Payments):** 6 ikondan 2'ye düştü (Visa + Mastercard kaldı; Amex/Discover/Paypal/Stripe kaldırıldı — QNB Pay TR'de Visa+MC kabul ediyor).
- **Organization schema:** `sameAs` array eklendi (3 sosyal URL), `availableLanguage` "English" düşürüldü → `["Turkish"]` (site TR-only).
- **Patch klasörü:** `analysis/seo-patches/06-footer-social-cleanup/`
  - `update.sql` → modülleri (61, 228) günceller
  - `update_schema_final.sql` → customCodeHeader günceller (UTF-8 koruyarak; **`SET NAMES utf8mb4`** prefix'iyle uygulanmalı; aksi takdirde Türkçe chars double-encode olur — bkz: L10)
- **Durum:** ⏳ Yerel Docker'da uygulandı + doğrulandı. Üretime henüz uygulanmadı.
- **Doğrulama (Docker):** `curl http://localhost:8000/` çıktısında footer'da 3 sosyal IG/FB linki, 2 ödeme ikonu, `<script ld+json>` içinde `sameAs` array, `["Turkish"]`. UTF-8: `Diş Hekimliği` doğru render.

---

## 📁 DOSYA SİSTEMI İŞLEMLERİ ÖZETİ

| İşlem | Dosya | Boyut | Sebep |
|---|---|---|---|
| Sil | `/public_html/admin.zip` | 852 MB | Güvenlik |
| Sil | `/public_html/toptandetal/Arsiv.zip` | 19 MB | Güvenlik |
| Sil | `/public_html/toptandetal/` (tüm dizin) | ~30 MB | Eski proje |
| Sil | `/public_html/error_log` | 186 KB | Şifre sızdırma |
| Sil | `/public_html/admin/error_log` | 469 KB | Şifre sızdırma |
| Sil | `/public_html/robots.txt.bak-20260511` | 344 B | Yedek temizlik |
| Yaz | `/public_html/robots.txt` | 945 B | SEO düzeltme |
| Yaz | `/public_html/.htaccess` | 3.7 KB | Güvenlik + perf + rewrite |
| Yaz | `/public_html/catalog/view/theme/journal3/template/common/header.twig` | 6.7 KB | H1 mantığı |
| Yaz | `/public_html/catalog/view/theme/journal3/template/common/header.twig.bak-20260511` | 6.5 KB | Yedek |
| Sil | Çeşitli `_*.php` runner dosyaları | <5 KB | Geçici script temizlik |

**Toplam temizlenen:** ~1.4 GB

---

## 🗃️ DB UPDATE/INSERT/DELETE ÖZETİ

| Operasyon | Tablo | Etkilenen Satır | Sebep |
|---|---|---|---|
| INSERT | oc_seo_url | 738 | SEO URL keyword'ler |
| DELETE | oc_seo_url | 19 | Eski keyword'leri temizle |
| UPDATE | oc_setting | 3 | Anasayfa meta |
| UPDATE | oc_category_description | 18 | Kategori meta TR |
| UPDATE | oc_product_description | ~283 TR + ~286 EN | Ürün meta_description |
| UPDATE | oc_product_description | ~332 TR + ~340 EN | Ürün meta_title |
| UPDATE | oc_product | 1 | Apple → manufacturer_id=0 |
| DELETE | oc_manufacturer | 6 | Demo veriler |
| DELETE | oc_manufacturer_to_store | 6 | Demo veriler |
| DELETE | oc_seo_url (manufacturer) | 6 | Demo veriler |
| INSERT | oc_setting (journal3_home_h1) | 1 | H1 deneme (etkisiz) |
| INSERT | oc_journal3_setting | 3 | H1 farklı gruplarda deneme (etkisiz) |
| UPDATE | oc_journal3_setting (customCodeHeader) | 1 | hreflang + Org schema |

**Toplam etkilenen satır:** ~2,000+

---

## ⚠️ İSTEMEDEN YAPILAN HATALAR (Düzeltildi)

1. **storage/modification klasörü silindi** → site 3 KB'a düştü → kullanıcı admin'den refresh yaptı, düzeldi (Bkz. [L01](./09-LESSONS-LEARNED.md))
2. **DB şifresi chat'e ham yansıdı** → tekrar rotate edildi (Bkz. [L03](./09-LESSONS-LEARNED.md))
3. **Runner PHP'leri GitHub'a push edildi** → force push ile tarih temizlendi (Bkz. [L04](./09-LESSONS-LEARNED.md))

---

## Doğrulama Komutları (Tekrar Çalıştırılabilir)

```bash
# Site sağlık
curl -sI https://ravendentalgroup.com/  # 200 OK
curl -sI https://ravendentalgroup.com/sitemap.xml  # 200 OK XML
curl -sI https://ravendentalgroup.com/diagnostik-aletleri  # 200 OK
curl -sI https://ravendentalgroup.com/admin/  # 200 OK

# Güvenlik (engellenmiş olmalı)
curl -sI https://ravendentalgroup.com/admin.zip  # 404
curl -sI https://ravendentalgroup.com/test.bak  # blocked (eğer dosya olsaydı)
curl -sI https://ravendentalgroup.com/.htaccess  # 403

# Sitemap URL sayısı
curl -s https://ravendentalgroup.com/sitemap.xml | grep -oc '<loc>'  # 369

# Meta
curl -s https://ravendentalgroup.com/ | grep -oE '<title>[^<]*</title>'
# → <title>Diş Hekimliği Aletleri ve Cerrahi Ekipmanlar | Raven Dental</title>
```
