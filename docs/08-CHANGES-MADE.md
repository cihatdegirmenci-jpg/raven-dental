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
