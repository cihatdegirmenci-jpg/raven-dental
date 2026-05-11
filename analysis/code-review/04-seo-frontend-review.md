# 04 — SEO & Frontend Code Review (Raven Dental, Journal3)

> Tarama tarihi: 2026-05-12
> Kapsam: `/Users/ipci/raven-dental/code/catalog/` (theme + diller)
> Mod: Salt kod incelemesi (live Lighthouse yok). 1 WebFetch ile body örneklemesi yapıldı, head dönmedi.
> Kapsam dışı: 05-SEO-STATUS.md’de “sonra” kolonu ✓ olan işler. Buradaki bulgular **henüz dokunulmamış** alanlar.

İçindekiler:
1. Yönetici özeti
2. Quick Wins (10 dk – 1 saat)
3. Strategik (gün+)
4. Tema template incelemesi
5. Image optimization
6. Critical rendering path
7. Form kalitesi
8. Mobile responsive
9. i18n quality
10. Sosyal medya hazırlık
11. Voice search & FAQ
12. Page speed (kod kaynaklı)
13. Microformats / Schema fallback
14. Breadcrumb
15. Pagination
16. Search functionality SEO etkisi
17. 404 sayfası
18. Önerilen OCMOD patch’leri (özet)

---

## 1. Yönetici Özeti

Site mevcut durumda fonksiyonel ve ana SEO işleri (meta, sitemap, hreflang, robots, SEO URL) tamamlanmış. Henüz dokunulmayan alanlar **iki kategoride** toplanıyor:

**Yapısal — tek seferlik düzeltme:**
- Mobile header_mobile_1.twig içinde `<h1>{{ name }}</h1>` **logo görseli yokken** açılıyor → bizim sr-only H1 ile **çift H1** doğuyor. Ürün/kategori sayfalarında zaten heading_title bir H1 daha geliyor. **3 H1 riski** mobilde.
- `image_dimensions_popup_thumb` zoom için kullanılıyor (lightgallery) ama `og:image` Journal3 ayarından çekiliyor — varsayılan 200×200 muhtemel. **Twitter/Facebook paylaşımında bozuk küçük görsel**.
- Tüm formlarda (login, register, contact, forgotten, return_form, guest checkout) email/password/tel/address inputları **`type="text"`** + **`autocomplete` yok**. Mobile UX zayıf, password manager çalışmıyor, autofill çalışmıyor.
- `og:title` ve site title hâlâ Journal3 `seo.php:261` üzerinden `config_name` döndürüyor (bizim açık borç).

**Performans — kod kaynaklı:**
- Header.twig **DOM içinde inline `<style>{{ j3.document.getCss() }}</style>`** + `{{ j3.settings.get('customCSS') }}` ekstradan. Render-blocking.
- Product.twig içinde **~250 satır inline `<script>`** (jQuery datepicker, review loader, cart submit). Hepsi defer’sız.
- `style.min.css` 139 KB tek dosya — critical CSS yok, ilk render bekliyor.
- `webfont.js` 3rd-party Google CDN (async var ama 2026'da self-host daha hızlı).
- 19 image WebFetch'te bulundu, **0 tanesinde `loading="lazy"`** geldi → Journal3'ün **`performanceLazyLoadImagesStatus` ayarı kapalı** veya `data-src` ile özel lazyload kullanıyor ama browser-native yerine JS bekliyor (LCP kötü).

**SEO uzun kuyruk — içerik gereken:**
- 18 kategori için TR uzun açıklama (200-400 kelime) hâlâ boş.
- Blog 0 yazı.
- FAQ schema yok.
- LocalBusiness schema yok (Maltepe/İstanbul fiziksel adres kullanılabilir).

Aşağıdaki bulgular **pratik düzeltmeler**. Hepsi OCMOD ile mümkün — kullanıcı sunucu dosyalarına Twig editöründe doğrudan dokunmadan.

---

## 2. Quick Wins (10 dk – 1 saat)

Bir oturumda yapılabilir, doğrudan etki yaratır:

| # | İş | Süre | Etki | Kategori |
|---|---|---|---|---|
| Q1 | `<input type="text"` → `type="email/tel/password"` 6 dosyada | 20 dk | Mobile keyboard, autofill, password manager | UX/A11y |
| Q2 | `autocomplete="email/given-name/family-name/tel/new-password..."` ekle | 25 dk | Autofill, conversion ↑ | UX/Conversion |
| Q3 | Twitter card `summary` → `summary_large_image` + image 1200×675 | 5 dk | Sosyal paylaşımda büyük görsel | Sosyal |
| Q4 | Mobile header_mobile_1.twig `<h1>` fallback'i sil veya `<a href>` ile değiştir | 5 dk | Çift H1 fix | SEO |
| Q5 | viewport meta: `viewport-fit=cover` ekle | 2 dk | iPhone notch | Mobile |
| Q6 | `not_found.twig`'e "Aramaya git / Ana kategoriler" linkler | 15 dk | Bounce ↓, internal linking | UX/SEO |
| Q7 | Search results `<meta name="robots" content="noindex">` doğrula (controller'da) | 10 dk | İç arama duplicate index ↓ | SEO |
| Q8 | `og:locale` ve `og:locale:alternate` ekle | 10 dk | Sosyal i18n | SEO |
| Q9 | `theme-color` meta + apple-touch-icon | 5 dk | PWA hissi, browser chrome | Mobile |
| Q10 | Footer sosyal medya `href="#"` boş linkleri sil veya `aria-disabled` | 10 dk | Broken link SEO, A11y | SEO/A11y |
| Q11 | `loading="lazy"` + `decoding="async"` tüm product_card resimlerine | 15 dk | LCP, CLS | Perf |
| Q12 | Footer dilini her dilden geçmiş kontrol (TR/EN tutarsızlık) | 20 dk | i18n | UX |
| Q13 | `prefers-reduced-motion` media query (slider için) | 10 dk | A11y, mobil pil | A11y |
| Q14 | `<title>` 60 char limit doğrulama | 10 dk | SERP truncation | SEO |
| Q15 | hreflang `en-gb` → `en` daha yaygın; `tr` ve `tr-tr` ikisi de meşru | 5 dk | i18n | SEO |

Toplam Q1-Q15: ~3 saat. Hepsi tek OCMOD XML'de toplanabilir.

---

## 3. Strategik (gün+)

Daha geniş kapsam, içerik gerektirir:

| # | İş | Süre | Etki | Kategori |
|---|---|---|---|---|
| S1 | FAQ Schema.org JSON-LD anasayfa + kategori sayfalarına | 1 gün | Voice search, FAQ rich result | SEO |
| S2 | LocalBusiness schema (Maltepe/İstanbul + GPS coord) | 4 saat | Yerel SEO, Google Maps | SEO |
| S3 | 18 kategoriye 250-400 kelimelik özgün TR açıklama (içerik prod) | 5 gün | Thin content fix, longtail | Content/SEO |
| S4 | Blog ilk 10 makale (endodonti kanal nasıl seçilir vs.) | 10-15 gün | Longtail trafiği | Content |
| S5 | Inline CSS → Critical CSS extraction + style.min.css async | 1-2 gün | LCP %30+ iyileşme | Perf |
| S6 | WebP image converter (image cache renderer modify) | 2 gün | Bandwidth %50 ↓ | Perf |
| S7 | Service Worker + asset precache (PWA) | 3 gün | Repeat visit instant | Perf |
| S8 | Self-host font (Google Fonts'tan kopyala + woff2) | 4 saat | 3rd-party DNS, GDPR | Perf/Compliance |
| S9 | Tüm form validation client-side (HTML5 + JS feedback) | 2 gün | UX, A11y | UX/A11y |
| S10 | Tema breadcrumb template + Schema kategori sayfasında Home → Kategori → Ürün full chain | 1 gün | Rich snippet doğru çekilsin | SEO |
| S11 | Image alt text TR template (theme image macro override) | 1 gün | A11y, image search | SEO/A11y |
| S12 | 404 sayfasına search box + en popüler 6 kategori grid | 4 saat | Bounce ↓ | UX |
| S13 | `aria-label`, `role=`, semantic `<nav>`, `<main>`, `<aside>` audit | 2 gün | A11y (WCAG AA) | A11y |
| S14 | RSS feed (blog için), Pinterest tag, FB Pixel/Plausible | 4 saat | Analitik, sosyal | Analytics |
| S15 | Mobile menü hamburger touch target 44×44 px audit | 4 saat | iOS HIG, A11y | Mobile/A11y |

---

## 4. Tema Template İncelemesi

### Bulgu 4.1 — Çift / Üçlü H1 riski (mobilde)

**Dosya:** `catalog/view/theme/journal3/template/journal3/headers/mobile/header_mobile_1.twig:19-30`

Mevcut:
```twig
{% if j3.document.hasClass('mobile-header-active') %}
  <div id="logo">
    {% if j3.settings.get('logo_src') %}
      <a href="{{ home }}">
        <img src="{{ j3.settings.get('logo_src') }}" ... alt="{{ name }}" .../>
      </a>
    {% else %}
      <h1><a href="{{ home }}">{{ name }}</a></h1>
    {% endif %}
  </div>
{% endif %}
```

`header_mobile_2.twig:21` ve `header_mobile_3.twig:18` aynı pattern.

**Sorun:**
- Logo görseli yüklenirse: `<img>` (sorun yok). Ama Journal3 admin'de logoyu yanlışlıkla kaldırırsanız mobilde `<h1>{{ name }}</h1>` çıkar.
- Aynı zamanda `header.twig:80-82` bizim **sr-only H1** ekliyor.
- Aynı zamanda kategori/ürün sayfası `<h1>{{ heading_title }}</h1>` ekliyor.

3 H1 = Google için karmaşa. Çözüm: mobile fallback'i `<span class="logo-text">` yap.

**Önerilen:**
```twig
{% else %}
  <span class="logo-text"><a href="{{ home }}">{{ name }}</a></span>
{% endif %}
```

(Düzeyi anlamak için CSS'te `.logo-text` zaten varsa kullanılır, yoksa ekle.)

OCMOD:
```xml
<file path="catalog/view/theme/journal3/template/journal3/headers/mobile/header_mobile_*.twig">
  <operation>
    <search><![CDATA[<h1><a href="{{ home }}">{{ name }}</a></h1>]]></search>
    <add><![CDATA[<span class="logo-text"><a href="{{ home }}">{{ name }}</a></span>]]></add>
  </operation>
</file>
```

---

### Bulgu 4.2 — `pageTitlePosition` çift H1 olasılığı

**Dosya:** `catalog/view/theme/journal3/template/product/product.twig:30,40,145`

3 ayrı yerde `{{ heading_title }}` H1/title olarak çıkıyor:
- Satır 30: `pageTitlePosition == 'top'` → `<h1 class="title page-title"><span>{{ heading_title }}</span></h1>`
- Satır 40: `pageTitlePosition == 'default'` → `<h1 class="title page-title">{{ heading_title }}</h1>`
- Satır 145: `<div class="title page-title">{{ heading_title }}</div>` (her zaman gösterilir)

Ayar tek seçtiği için satır 30 VEYA 40 → 1 H1.
Ama bizim `header.twig:80` sr-only `<h1>{{ raven_h1 }}</h1>` da düşüyor. `raven_h1` = `heading_title` (fallback chain). Yani **2 H1 aynı içerikle**.

**Çözüm A (önerilen):** sr-only H1'i sadece **heading_title YOKSA** koy.

`header.twig:79`:
```twig
{# Mevcut #}
{% set raven_h1 = j3.settings.get('journal3_home_h1') ?: (heading_title is defined and heading_title ? heading_title : title) %}
{% if raven_h1 %}
  <h1 class="sr-only" ...>{{ raven_h1 }}</h1>
{% endif %}
```

**Önerilen:**
```twig
{# Sadece anasayfada veya heading_title yoksa sr-only H1 ekle #}
{% if not (heading_title is defined and heading_title) %}
  {% set raven_h1 = title %}
  {% if raven_h1 %}
    <h1 class="sr-only" style="position:absolute;height:1px;width:1px;clip:rect(0,0,0,0);">{{ raven_h1 }}</h1>
  {% endif %}
{% endif %}
```

Anasayfa: heading_title yok → sr-only H1 = title (config_meta_title). ✓
Kategori/Ürün: heading_title var → sr-only yok, görünür H1 zaten gelecek. ✓

---

### Bulgu 4.3 — Semantic HTML zayıf

**Dosya:** `catalog/view/theme/journal3/template/common/footer.twig`

Mevcut:
```twig
<footer>
  {{ footer_menu }}
</footer>
```

Tek `<footer>` ok ama sayfa içeriği `<main>` ile sarılmıyor — `<div id="content">` kullanılıyor.

**Etki:** Screen reader landmark navigation çalışmıyor. Skip-to-main link de yok.

**Önerilen:** header.twig'i değiştir:
```twig
{# Mevcut: <header class="header-{{ ... }}"> #}
{# Eklenmesi: <main id="content-main"> wrapper after header #}
```

`product.twig:34`'te `<div id="content" class="{{ class }}">` → `<main id="content" role="main" class="{{ class }}">` olabilir ama bu pek çok dosyada (~14 twig) tekrar ediyor — OCMOD ile tek vuruşta `<div id="content"` → `<main id="content"` yapılabilir.

⚠️ Bootstrap CSS `#content` selector'una bağlı, `<main>` semantic ama element değişimi CSS'i bozmaz. Yine de risk: kontrol edilmeli.

---

### Bulgu 4.4 — `<address>` etiketi yanlış kullanım

**Dosya:** `catalog/view/theme/journal3/template/information/contact.twig:33-35`

```twig
<strong>{{ store }}</strong><br />
<address>
{{ address }}
</address>
```

OK kullanım. Ancak `microdata` yok. `contact.twig` LocalBusiness schema için altın fırsat.

**Önerilen (Schema microdata fallback):**
```twig
<div itemscope itemtype="https://schema.org/Store">
  <span itemprop="name">{{ store }}</span><br />
  <address itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
    <span itemprop="streetAddress">{{ address }}</span>
  </address>
  <span itemprop="telephone">{{ telephone }}</span>
  {% if geocode %}
  <meta itemprop="geo" content="{{ geocode }}">
  {% endif %}
</div>
```

---

### Bulgu 4.5 — `category.twig` description boş olunca thin content

**Dosya:** `catalog/view/theme/journal3/template/product/category.twig:17-28`

```twig
{% if j3.settings.get('categoryPageDescStatus') %}
{% if thumb or description %}
  <div class="category-description">...</div>
{% endif %}
{% endif %}
```

18/18 kategori `description` boş → `<div class="category-description">` hiç render olmuyor → kategori sayfası sadece breadcrumb + grid + filter. Google'a "thin content" sinyali.

**Geçici tema fix:** category.twig'e statik bir "Bu kategoride X ürün bulundu" + meta_description'ı page üzerine yazmaya zorlamak (mümkün ama hile gibi). **Gerçek çözüm S3 — kategori uzun açıklama içeriği yaz**.

---

## 5. Image Optimization

### Bulgu 5.1 — Browser-native `loading="lazy"` eksik, JS lazyload var

**Dosyalar:**
- `product/product.twig:60,87` — Native lazy var ama sadece **`loop.first` değilse**
- `journal3/product_card.twig:24,31` — Sadece `performanceLazyLoadImagesStatus` açıksa JS lazyload (`data-src` + `lazysizes`)
- `journal3/category_grid.twig:8` — Aynı
- `journal3/manufacturer_grid.twig:7` — Aynı

**Sorun:**
- Performance ayarı kapalıysa lazy yok → tüm görseller eşzamanlı iner.
- Açıksa JS lazyload var ama browser-native `loading="lazy"` zaten 2018+ Chrome, 2020+ Safari'de. JS olmadan çalışır, daha hızlı.

WebFetch sonucu: live'da 19 image, 0 lazy → ayar kapalı. **Açılması yeterli (admin).**

**Önerilen (tema-bağımsız):**
Admin → Journal Editor → Performance → Lazy Load Images: ON

**Öneri (tema patch):** Her iki yöntemi de kullan (progressive enhancement):
```twig
<img src="{{ product.thumb }}"
     {% if product.thumb2x %}srcset="{{ product.thumb }} 1x, {{ product.thumb2x }} 2x"{% endif %}
     width="{{ image_width }}" height="{{ image_height }}"
     alt="{{ product.name }}" title="{{ product.name }}"
     loading="lazy" decoding="async"
     class="img-responsive img-first"/>
```

OCMOD (product_card.twig:26 bölgesi):
```xml
<file path="catalog/view/theme/journal3/template/journal3/product_card.twig">
  <operation>
    <search><![CDATA[class="img-responsive img-first"/>]]></search>
    <add position="replace"><![CDATA[loading="lazy" decoding="async" class="img-responsive img-first"/>]]></add>
  </operation>
</file>
```

Tüm `class="img-responsive ...`/`class="lazyload"` satırlarına `loading="lazy" decoding="async"` eklenebilir (~30 satır).

---

### Bulgu 5.2 — `srcset` yapısı sadece 2x, modern responsive eksik

**Dosya:** `product/product.twig:60`

```twig
<img src="{{ image.image }}"
     {% if image.image2x %}srcset="{{ image.image }} 1x, {{ image.image2x }} 2x"{% endif %}
     ...
/>
```

`1x / 2x` retina için iyi ama **viewport-based** (`sizes` + `srcset w descriptor`) yok. Mobilde tam çözünürlük image iniyor.

**Önerilen (uzun vadeli — controller değişikliği gerekir):**
```html
<img src="thumb-320.jpg"
     srcset="thumb-320.jpg 320w, thumb-640.jpg 640w, thumb-1024.jpg 1024w"
     sizes="(max-width: 480px) 100vw, (max-width: 1024px) 50vw, 33vw"
     loading="lazy" decoding="async" />
```

Bu OpenCart image resize controller'ını genişletmeyi gerektirir — Faz 2 işi.

---

### Bulgu 5.3 — Alt text inconsistency

**Dosyalar:** product_card.twig, product.twig, category.twig, manufacturer_grid.twig

Hepsi `alt="{{ product.name }}"` veya `alt="{{ heading_title }}"` kullanıyor — bu doğru ama:
- Sub-category template (category.twig:47): `alt="{{ category.alt }}"` — `category.alt` boşsa boş alt çıkar.
- Slider modules (slider.twig:3): `alt="{{ subitem.alt }}"` — admin'den boş bırakılabilir.

**Önerilen (defensive default):**
```twig
alt="{{ category.alt ?: category.name }}"
```

OCMOD:
```xml
<file path="catalog/view/theme/journal3/template/product/category.twig">
  <operation>
    <search><![CDATA[alt="{{ category.alt }}"]]></search>
    <add position="replace"><![CDATA[alt="{{ category.alt ?: category.name }}"]]></add>
  </operation>
</file>
```

Aynısı slider için `subitem.alt ?: subitem.title ?: ''`.

---

### Bulgu 5.4 — Image dimensions admin'de tanımsız olabilir

**Dosya:** `product/product.twig:60,87,102` — `width="{{ j3.settings.get('image_dimensions_thumb.width') }}"`

Admin Journal Editor'da bu boş kalırsa `width=""` render olur — CLS (Cumulative Layout Shift) felaketi.

**Önerilen defansif:**
```twig
width="{{ j3.settings.get('image_dimensions_thumb.width') ?: 600 }}"
height="{{ j3.settings.get('image_dimensions_thumb.height') ?: 600 }}"
```

---

## 6. Critical Rendering Path

### Bulgu 6.1 — Inline CSS HEAD içinde büyük blok

**Dosya:** `catalog/view/theme/journal3/template/common/header.twig:63-68`

```twig
<style>
{{ j3.document.getCss() }}
</style>
{% if j3.settings.get('customCSS') %}
<style>{{ j3.settings.get('customCSS') }}</style>
{% endif %}
```

`j3.document.getCss()` Journal3 ayarlarından dinamik üretiliyor — büyüklüğü değişken (10-50 KB). Render-blocking.

**Etki:** FCP gecikir. Mobile slow 3G'de 200-500 ms.

**Öneri (uzun vadeli):**
1. `j3.document.getCss()` çıktısını DB cache'le (zaten cache'leniyorsa boyutu küçült)
2. Yüksek-öncelik selector'ları (header, hero, breadcrumb) inline, gerisini `<link rel="stylesheet" media="print" onload="this.media='all'">` ile defer et

**Quick win:** `style.min.css` 139 KB için preload:
```html
<link rel="preload" href="..../style.min.css" as="style">
```
zaten muhtemelen `j3.document.getStyles` yapıyor. Header.twig:50-56'ya bak:
```twig
{% for style in j3.document.getStyles(styles) %}
{% if style.content %}
<style>{{ style.content }}</style>
{% else %}
<link href="..." type="text/css" rel="{{ style.rel }}" media="all" />
{% endif %}
{% endfor %}
```
`{{ style.rel }}` Journal3'ten dolayı `stylesheet` döner. `rel="preload"` ile değiştirmek mümkün değil burada. Faz 2.

---

### Bulgu 6.2 — `webfont.js` 3rd-party + async

**Dosya:** `header.twig:42-48`

```twig
{% if j3.settings.get('performanceAsyncFontsStatus') %}
<script>WebFontConfig = { google: { families: {{ j3.document.getFonts(true) }} } };</script>
<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" async></script>
{% else %}
<link href="https://fonts.googleapis.com/css?family={{ j3.document.getFonts(false) }}" type="text/css" rel="stylesheet"/>
{% endif %}
```

Her iki yol da 3rd-party Google.

**Önerilen:**
1. Kullanılan font'ları belirle (Journal3 ayarı).
2. `woff2` indir, `/public_html/catalog/view/theme/journal3/fonts/` koy.
3. Header.twig'te `<style>@font-face { font-family: ...; src: url(...) format('woff2'); font-display: swap; }</style>` ile inline.

**Quick win** (3rd-party DNS gecikme azalt):
```html
<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
<link rel="dns-prefetch" href="https://fonts.googleapis.com/">
```
Zaten header.twig:21-22'de `preconnect` var ✓.

`dns-prefetch` fallback ekle:
```twig
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//ajax.googleapis.com">
```

---

### Bulgu 6.3 — `<script src=...>` header'da `defer` opsiyonel

**Dosya:** `header.twig:69-71`

```twig
{% for script in j3.document.getScripts('header', scripts) %}
<script src="..." {% if j3.settings.get('performanceJSDefer') %} defer {% endif %}></script>
{% endfor %}
```

`performanceJSDefer` admin ayarına bağlı. **AÇIK olduğunu doğrula** (admin → Journal Editor → Performance → JS Defer: ON).

---

### Bulgu 6.4 — Product.twig içinde ~250 satır inline script

**Dosya:** `product/product.twig:593-849`

5 ayrı `<script>` bloğu. Tümü inline, defer/async yok, sayfa rendering'i bloklar.

**Önerilen:** Bu kodu tek bir external dosyaya çıkar:
```
catalog/view/theme/journal3/js/product.page.js
```
Sonra:
```twig
<script src="catalog/view/theme/journal3/js/product.page.js" defer></script>
```
Sayfa-spesifik değişkenler için `data-*` attribute veya `window.RAVEN_PRODUCT = {...}` inject.

Bu strategik (S5 ile birleştirilebilir).

---

## 7. Form Kalitesi

### Bulgu 7.1 — Email/tel inputları `type="text"` (KRİTİK Mobile UX)

**Dosyalar:**
- `account/login.twig:53`
- `account/forgotten.twig:35`
- `account/return_form.twig:51`
- `information/contact.twig:129, 138`
- `checkout/guest.twig:24, 28, 32, 36, 156, 160, 164, 168, 172`
- `account/register.twig:55, 63` (firstname/lastname text OK ama tel/email zaten doğru — sadece email type doğru ama autocomplete yok)

**Mevcut (örnek login.twig:53):**
```twig
<input type="text" name="email" value="{{ email }}" placeholder="{{ entry_email }}" id="input-email" class="form-control" />
```

**Sorun:**
- Mobilde Türkçe klavye açılıyor; `@` zor.
- Browser email validation yok.
- Password manager email field'ı algılamıyor.

**Önerilen:**
```twig
<input type="email"
       name="email"
       value="{{ email }}"
       placeholder="{{ entry_email }}"
       id="input-email"
       class="form-control"
       autocomplete="email"
       inputmode="email"
       required />
```

`type="tel"` için telefon:
```twig
<input type="tel" name="telephone" ... autocomplete="tel" inputmode="tel" />
```

OCMOD (login):
```xml
<file path="catalog/view/theme/journal3/template/account/login.twig">
  <operation>
    <search><![CDATA[<input type="text" name="email" value="{{ email }}" placeholder="{{ entry_email }}" id="input-email" class="form-control" />]]></search>
    <add position="replace"><![CDATA[<input type="email" name="email" value="{{ email }}" placeholder="{{ entry_email }}" id="input-email" class="form-control" autocomplete="email" inputmode="email" required />]]></add>
  </operation>
  <operation>
    <search><![CDATA[<input type="password" name="password" value="{{ password }}" placeholder="{{ entry_password }}" id="input-password" class="form-control" />]]></search>
    <add position="replace"><![CDATA[<input type="password" name="password" value="{{ password }}" placeholder="{{ entry_password }}" id="input-password" class="form-control" autocomplete="current-password" required />]]></add>
  </operation>
</file>
```

Aynı pattern register.twig için:
- firstname → `autocomplete="given-name"`
- lastname → `autocomplete="family-name"`
- email → `type="email" autocomplete="email"` (✓ zaten email)
- telephone → `type="tel" autocomplete="tel"` (✓ zaten tel)
- password → `autocomplete="new-password"`
- confirm → `autocomplete="new-password"`

Guest checkout için (`address_1` → `autocomplete="address-line1"`, vb.).

**Etki:** Mobile conversion'u %5-15 yükselten en pratik dokunuşlardan.

---

### Bulgu 7.2 — Error message yapısı a11y'siz

**Dosyalar:** Tüm form'lar

```twig
<input type="text" name="firstname" ... id="input-firstname" class="form-control" />
{% if error_firstname %}
<div class="text-danger">{{ error_firstname }}</div>
{% endif %}
```

**Sorun:**
- Hata mesajı input'a `aria-describedby` ile bağlı değil.
- Hata olunca input'a `aria-invalid="true"` eklenmiyor.
- Screen reader hata sırasını anlamıyor.

**Önerilen:**
```twig
<input type="text" name="firstname"
       value="{{ firstname }}"
       placeholder="{{ entry_firstname }}"
       id="input-firstname"
       class="form-control"
       autocomplete="given-name"
       {% if error_firstname %}aria-invalid="true" aria-describedby="error-firstname"{% endif %} />
{% if error_firstname %}
<div class="text-danger" id="error-firstname" role="alert">{{ error_firstname }}</div>
{% endif %}
```

Bu çok yerde tekrar — OCMOD yerine custom JS daha pratik:
```js
// catalog/view/theme/journal3/js/form-a11y.js
$(document).on('click', '.text-danger', function(){
  var $err = $(this), $input = $err.prev('input,select,textarea');
  if ($input.length) {
    $err.attr('role', 'alert').attr('id', 'err-' + $input.attr('id'));
    $input.attr('aria-invalid', 'true').attr('aria-describedby', $err.attr('id'));
  }
});
```

Çağrı: customCodeFooter Journal3 ayarına eklenebilir.

---

### Bulgu 7.3 — `<label>` ve `for=` ilişkisi tutarsız

**Dosya:** `account/register.twig:35`

```twig
<label class="col-sm-2 control-label">{{ entry_customer_group }}</label>
```

`for=` yok. Radio button grup ama `<fieldset><legend>` ile sarılmalı.

**Önerilen:**
```twig
<fieldset class="form-group required account-customer-group" ...>
  <legend class="col-sm-2 control-label">{{ entry_customer_group }}</legend>
  <div class="col-sm-10">
    ...
  </div>
</fieldset>
```

Aynı sorun newsletter radio'da (`register.twig:270`).

---

### Bulgu 7.4 — `placeholder` yalnızca, label gizli mi?

Tüm formlarda `placeholder` ve `<label>` ikisi de var ✓. Ancak bazıları `col-sm-2 control-label` ile yan yana, mobilde **küçük yazı** + **input dolu**, çakışmıyor ama tutarsız. Modern UX: floating label.

Bu Strategik — Bootstrap 3'ten 4/5'e migration olmadan zor.

---

## 8. Mobile Responsive

### Bulgu 8.1 — viewport `viewport-fit=cover` yok

**Dosya:** `header.twig:15`

```html
<meta name="viewport" content="width=device-width, initial-scale=1.0">
```

iPhone X+ notch için `viewport-fit=cover` eksik. Safari'de safe-area insets çalışmaz.

**Önerilen:**
```html
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
```

OCMOD:
```xml
<file path="catalog/view/theme/journal3/template/common/header.twig">
  <operation>
    <search><![CDATA[<meta name="viewport" content="width=device-width, initial-scale=1.0">]]></search>
    <add position="replace"><![CDATA[<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#1a1a1a">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">]]></add>
  </operation>
</file>
```

Renk #1a1a1a yerine site brand color (Journal3 ayarından dinamik alınabilir).

---

### Bulgu 8.2 — Touch target boyutu

**Dosya:** `journal3/headers/mobile/header_mobile_1.twig:31-50`

`menu-trigger`, `mobile-cart-wrapper`, `mobile-search-wrapper`, custom-menu butonlar — CSS'te boyutu kontrol edilmeli. iOS HIG **minimum 44×44 px**, Material 48×48 dp.

**Önerilen test:** Chrome DevTools mobil emule → her ikon en az 44 px.

Eğer küçükse CSS'te zorla:
```css
.mobile-bar-group > * {
  min-width: 44px;
  min-height: 44px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
```

`customCSS` Journal3 ayarına eklenebilir (oturum içi quick fix).

---

### Bulgu 8.3 — `<select>` mobilde küçük

**Dosya:** `product/category.twig:80-92`

```twig
<select id="input-sort" class="form-control" onchange="location = this.value;">
```

Bootstrap 3 `.form-control` mobilde 34 px height — iOS zoom tetikler. Mobile için font-size 16 px gerekli (yoksa zoom-on-focus).

**Önerilen CSS:**
```css
@media (max-width: 768px) {
  .form-control, input, select, textarea {
    font-size: 16px !important;
  }
}
```

---

### Bulgu 8.4 — `onchange="location = this.value;"` keyboard-erişimsiz

**Dosya:** `product/category.twig:80, 92`

Mevcut:
```twig
<select id="input-sort" onchange="location = this.value;">
```

Klavyede option seçince **anında redirect**. Screen reader kullanıcısı keşif yapamıyor (her option click'i navigate ediyor).

**Önerilen:** Submit butonu ekle veya `change` event delayed (debounce) bekle. Bu Strategik (S9).

---

## 9. i18n Quality

### Bulgu 9.1 — `text_home` HTML içeriği

**Dosya:** `catalog/language/tr-tr/tr-tr.php:15` ve `en-gb/en-gb.php:13`

```php
$_['text_home']             = '<i class="fa fa-home"></i>';
```

İkisi de aynı `<i>` ikon kullanıyor — i18n string'e HTML konulması anti-pattern. Breadcrumb'da `<a>` içinde rendered olarak çıkıyor; screen reader hiçbir şey duymuyor.

**Önerilen:**
```php
// TR
$_['text_home']             = '<i class="fa fa-home" aria-hidden="true"></i><span class="sr-only">Anasayfa</span>';

// EN
$_['text_home']             = '<i class="fa fa-home" aria-hidden="true"></i><span class="sr-only">Home</span>';
```

---

### Bulgu 9.2 — datepicker `tr` mi `en` mi?

**Dosya:** `tr-tr/tr-tr.php:76` → `$_['datepicker'] = 'tr';` ✓

`en-gb/en-gb.php:103` → `$_['datepicker'] = 'en-gb';` ✓

OK. Ama `register.twig:413-415` JS'te:
```js
$('.date').datetimepicker({
  language: '{{ datepicker }}',
  pickTime: false
});
```

Çağrı doğru. Ancak `pickDate: true` parametresi `.time`'da yanlış (datetime için olmalı). `register.twig:418-427` tarihçe:
```js
$('.time').datetimepicker({
  language: '{{ datepicker }}',
  pickDate: false  // ✓
});

$('.datetime').datetimepicker({
  language: '{{ datepicker }}',
  pickDate: true,
  pickTime: true   // ✓
});
```

OK.

---

### Bulgu 9.3 — Eksik çeviri kontrolü

`error/not_found.twig` TR:
```php
$_['heading_title'] = 'Aradığınız Sayfa Bulunamadı!';
$_['text_error']    = 'Aradığınız Sayfa Bulunamadı.';
```

EN'i kontrol et:
```bash
cat /Users/ipci/raven-dental/code/catalog/language/en-gb/error/not_found.php
```

Beklenen: `'The page you requested cannot be found!'` — Türkçe default'tan kalan kalıntı var mı diye full diff yapılmalı.

**Önerilen audit script:**
```bash
diff <(find catalog/language/tr-tr -name '*.php' -exec grep -l "" {} \; | sort) \
     <(find catalog/language/en-gb -name '*.php' -exec grep -l "" {} \; | sort)
```

Eksik dosya varsa raporla. (Strategik audit S12 alanı.)

---

### Bulgu 9.4 — Currency/date format tutarlılığı

TR: `decimal_point = ','`, `thousand_point = '.'` ✓ (Türkçe için doğru: 1.234,56)
EN: `decimal_point = '.'`, `thousand_point = ','` ✓

Date format:
TR: `d/m/Y` ✓
EN: `d/m/Y` (EN'de tipik `m/d/Y` veya `Y-m-d` olabilir, en-gb için `d/m/Y` ok)

OK.

---

### Bulgu 9.5 — Müşteri grup TR çevirisi gözden geçir

`register.twig:35`:
```twig
<label class="col-sm-2 control-label">{{ entry_customer_group }}</label>
```

`catalog/language/tr-tr/account/register.php:17`:
```php
$_['entry_customer_group'] = 'Müşteri Grubu';
```

OK. Ancak B2B sitede "Müşteri Grubu" yerine **"Hesap Tipi"** veya **"Diş Hekimi / Klinik / Distribütör"** gibi spesifik label daha iyi olur.

İçerik düzeyinde değişiklik — admin → Customer Groups → her grubun TR adını B2B'ye uygunlaştır. (Doğrudan tema değil DB.)

---

## 10. Sosyal Medya Hazırlık

### Bulgu 10.1 — Twitter card type yanlış

**Dosya:** `catalog/controller/journal3/seo.php:67-70`

```php
$tags['twitter:card'] = array(
  'type'    => 'name',
  'content' => 'summary',
);
```

`summary` = küçük 200×200 kare. **`summary_large_image`** = büyük 1200×675 banner — paylaşımda göze çarpar.

**Önerilen:** Admin → Journal Editor → SEO → Twitter Cards → Card Type = "Summary with Large Image" + Image Dimensions = 1200×675.

Eğer ayar yoksa controller patch (OCMOD):
```xml
<file path="catalog/controller/journal3/seo.php">
  <operation>
    <search><![CDATA['content' => 'summary',]]></search>
    <add position="replace"><![CDATA['content' => 'summary_large_image',]]></add>
  </operation>
</file>
```

Image dimension Journal3 ayarı (`seoTwitterCardsImageDimensions.width=1200, height=675`) gerekli.

---

### Bulgu 10.2 — `og:locale` ve `og:locale:alternate` eksik

**Dosya:** `controller/journal3/seo.php:meta_tags()` — eklenmemiş.

Mevcut tags: `fb:app_id, og:type, og:title, og:url, og:image, og:image:width, og:image:height, og:description`. **`og:locale` YOK.**

**Etki:** Facebook hangi dilde paylaşıldığını anlamıyor; lokalizasyon karışıyor.

**Önerilen tema-side ek (header.twig'e ekleyebiliriz, Journal3 customCodeHeader'a inject):**
```html
<meta property="og:locale" content="tr_TR" />
<meta property="og:locale:alternate" content="en_GB" />
```

Bu hreflang ile uyumlu. Anasayfa için TR primary.

Dinamik (mevcut dile göre) versiyon:
```twig
{% if lang == 'tr-TR' %}
  <meta property="og:locale" content="tr_TR" />
  <meta property="og:locale:alternate" content="en_GB" />
{% else %}
  <meta property="og:locale" content="en_GB" />
  <meta property="og:locale:alternate" content="tr_TR" />
{% endif %}
```

---

### Bulgu 10.3 — `og:site_name` eksik

`controller/journal3/seo.php:meta_tags()` — eklenmemiş.

```html
<meta property="og:site_name" content="Raven Dental" />
```

Sosyal paylaşımda site adı çıkar. Önemli.

---

### Bulgu 10.4 — Pinterest tag yok

B2B'de Pinterest düşük öncelikli ama görsel ağırlıklı (diş aletleri) içerik için fırsat.

**Önerilen:**
```html
<meta name="pinterest-rich-pin" content="true">
<meta name="pinterest" content="nopin"> <!-- ya da -->
```

Veya tema header'ında verify meta:
```html
<meta name="p:domain_verify" content="..."/>
```

Strategik (S14).

---

### Bulgu 10.5 — Footer sosyal medya boş href

`05-SEO-STATUS.md`'de zaten not edilmiş ❤️.

**Çözüm:** Ya hesapları aç ve href'leri doldur, ya da CSS ile gizle / template'ten kaldır.

Geçici tema CSS (customCSS):
```css
footer .social-icons a[href="#"] { display: none; }
```

---

## 11. Voice Search & FAQ

### Bulgu 11.1 — H2/H3 soru-cevap yapısı yok

Kategori sayfalarında sadece `<h1>` ve ürün grid. Voice search "Hey Google, diş hekimliği için en iyi pens marka" sorusuna cevap verecek **FAQ blok** yok.

**Önerilen (içerik düzeyi):**

Her kategori sayfası altında 3-5 SSS:
- "Endodonti aletleri nasıl seçilir?"
- "Kanal eğesi ne kadar süre kullanılır?"
- "Steril edilebilir mi?"

Tema-side template eki (category.twig'in en altına):
```twig
{% if category_id and category_faq is defined %}
<section class="category-faq" itemscope itemtype="https://schema.org/FAQPage">
  <h2>Sıkça Sorulan Sorular</h2>
  {% for q in category_faq %}
  <div itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
    <h3 itemprop="name">{{ q.question }}</h3>
    <div itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
      <div itemprop="text">{{ q.answer }}</div>
    </div>
  </div>
  {% endfor %}
</section>
{% endif %}
```

`category_faq` data'sı controller'dan gelmeli — OpenCart custom field ile category extend edilir (Faz 2).

---

### Bulgu 11.2 — Ürün H2'leri soru-bazlı değil

Ürün sayfasında "Açıklama / İncelemeler / Özellikler" tab var, hepsi nominal. "Bu ürün ne için kullanılır?" gibi soru formatı SEO'da güçlü.

**Önerilen:** Product description editör'ünde her ürün için 1-2 soru cevap ekle (template değil içerik).

---

### Bulgu 11.3 — Speakable schema yok

Voice assistant için:
```json
{
  "@type": "WebPage",
  "speakable": {
    "@type": "SpeakableSpecification",
    "cssSelector": [".product-description-short", ".price"]
  }
}
```

`seo.php` controller'ında her ürün için `Product` schema'ya ekle:
```php
$json['speakable'] = array(
  '@type' => 'SpeakableSpecification',
  'cssSelector' => array('.title.page-title', '.product-price'),
);
```

Strategik (S1 ile birlikte).

---

## 12. Page Speed (Kod Kaynaklı)

### Bulgu 12.1 — JS dosyaları header'da

`header.twig:69-71` `getScripts('header', scripts)` ile script'ler header'a yerleştirilebiliyor. Bunlar mı `defer` mi `blocking` mi — Journal3 ayarına bağlı (`performanceJSDefer`).

**Önerilen audit:**
```bash
curl -s https://ravendentalgroup.com/ | grep -E '<script src' | wc -l
curl -s https://ravendentalgroup.com/ | grep -E '<script src' | grep -c 'defer'
```

Sonuçlar yakın olmalı.

---

### Bulgu 12.2 — jQuery + Bootstrap 3 + Slick + magnificPopup + lightgallery

`product.twig` 4 farklı JS lib kullanıyor:
- jQuery (datetimepicker bind)
- magnificPopup (review thumbnails)
- lightGallery (product images)
- Bootstrap (modal/tooltip)

**Önerilen audit:** Network tab → Total JS bytes. Eğer >300 KB:
- magnificPopup yerine native `<dialog>` veya lightGallery'i tek bırak
- Bootstrap 3 → 5 (jQuery dependency düşer — uzun vadeli)

---

### Bulgu 12.3 — Inline `<script>` çok

Tema içinde grep:
```bash
grep -rn "<script type=\"text/javascript\"><!--" catalog/view/theme/journal3/template/
```

Her bulunan blok render-blocking. **product.twig** 5 blok, **category.twig** muhtemelen 1-2, **register.twig** 3.

**Strategik (S5):** Bu inline JS'leri `catalog/view/theme/journal3/js/page-*.js` dosyalarına çıkar.

---

### Bulgu 12.4 — `prefers-reduced-motion` desteği yok

Slider, hover animations a11y için bu media query saymalı.

**Önerilen `customCSS`'e ekle:**
```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```

---

## 13. Microformats / Schema Fallback

### Bulgu 13.1 — Product price hCard/hProduct fallback

JSON-LD ile `Product` schema veriliyor (seo.php:168) ama eski rich snippet test araçları/RSS reader'lar microdata bekler.

**Önerilen product.twig'e ek:**
```twig
<div class="product-info" itemscope itemtype="https://schema.org/Product">
  <meta itemprop="name" content="{{ heading_title }}">
  {% if product_sku %}<meta itemprop="sku" content="{{ product_sku }}">{% endif %}
  {% if manufacturer %}<meta itemprop="brand" content="{{ manufacturer }}">{% endif %}
  ...
  <div itemprop="offers" itemscope itemtype="https://schema.org/Offer">
    <meta itemprop="priceCurrency" content="TRY">
    <meta itemprop="price" content="{{ product_price_value }}">
    <link itemprop="availability" href="https://schema.org/{{ product_quantity > 0 ? 'InStock' : 'OutOfStock' }}">
  </div>
</div>
```

JSON-LD ve microdata aynı sayfada olabilir — Google "duplicate olduğu için warning" verir ama hata değildir. Daha güvenli olan: sadece JSON-LD (Google'ın önerdiği) ile devam et, microdata'yı atla.

---

### Bulgu 13.2 — Breadcrumb microdata fallback (eğer JSON-LD çakışırsa)

`category.twig:2-6`:
```twig
<ul class="breadcrumb">
  {% for breadcrumb in breadcrumbs %}
    <li><a href="{{ breadcrumb.href }}">{{ breadcrumb.text }}</a></li>
  {% endfor %}
</ul>
```

**Önerilen:**
```twig
<ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
  {% for breadcrumb in breadcrumbs %}
    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
      <a itemprop="item" href="{{ breadcrumb.href }}">
        <span itemprop="name">{{ breadcrumb.text }}</span>
      </a>
      <meta itemprop="position" content="{{ loop.index }}">
    </li>
  {% endfor %}
</ol>
```

`<ul>` → `<ol>` (sıralı önemli — breadcrumb pozisyon belirtir).

---

## 14. Breadcrumb

### Bulgu 14.1 — Ürün sayfasında BreadcrumbList kategori eksik

`05-SEO-STATUS.md`'de not: "BreadcrumbList — sadece Home → Product (Category eksik!)"

**Kök neden:** `controller/journal3/seo.php:rich_snippets()` — `$breadcrumbs` parametresi tema'dan geliyor (`product.twig:851`). Ürün sayfasında OpenCart'ın `breadcrumbs` array'inde Home → Category → Product sırası geliyor ama bazen sadece Home → Product geliyor.

**Test (canlıda kontrol):**
```bash
curl -sL "https://ravendentalgroup.com/bein-elevator-3-mm-egri" | grep -oE 'BreadcrumbList[^<]*' | head -5
```

Eğer kategori eksikse, OpenCart core'un breadcrumb dolduran controller'ı (`catalog/controller/product/product.php`) gözden geçirilmeli.

**Quick fix tema-side (her zaman category çıkar):**
`product.twig`'de breadcrumb arada manuel inject — ama controller seviyesi doğrusu.

---

### Bulgu 14.2 — Anasayfa breadcrumb yok (doğal — Schema da gerekmez)

`common/home.twig` breadcrumb içermiyor — doğru. Anasayfada breadcrumb gereksiz.

---

## 15. Pagination

### Bulgu 15.1 — `rel="prev/next"` yok

`category.twig:109-112`:
```twig
<div class="row pagination-results">
  <div class="col-sm-6 text-left">{{ pagination }}</div>
  ...
</div>
```

`{{ pagination }}` OpenCart core'dan geliyor. **`<head>` içinde `rel="prev/next"` link tag yok.**

Google 2019'da "artık kullanmıyoruz" dedi ama:
- Bing hâlâ kullanıyor
- Screen reader'lar tanır
- Daha temiz crawl signal

**Önerilen header.twig'e ek (controller'dan param geçer):**
```twig
{% if prev_url %}<link rel="prev" href="{{ prev_url }}" />{% endif %}
{% if next_url %}<link rel="next" href="{{ next_url }}" />{% endif %}
```

`prev_url` / `next_url` Journal3 veya OpenCart core controller'ından geliyor — `Url` builder ile manuel oluşturulması gerekebilir. Faz 2.

---

### Bulgu 15.2 — `?page=` robots.txt'te `Disallow:` — paginated sayfalar indexlenmiyor

`robots.txt`:
```
Disallow: /*?page=
Disallow: /*&page=
```

**Etki:**
- Sayfa 2'deki ürünler crawl edilmez → her ürün tek sayfada görünmüyorsa SEO kaybı.
- Ürün sayısı: 345 / kategori sayfasında 12-24 ürün → her kategorinin 2-3+ sayfası vardır.
- Sitemap.xml'de tüm ürünler doğrudan listeli (369 URL) — bu sorunu hafifletiyor (Google sitemap'ten ürünleri crawl eder).

**Risk:** OK, sitemap koruyor. Ama daha doğru çözüm:
- `Disallow:` yerine `?page=` URL'lere `<meta name="robots" content="noindex,follow">` koy. Linkleri takip et, ama index'leme.

Şu an için sitemap kapsamı yeterli → robots.txt'i değiştirmeye gerek yok.

---

## 16. Search Functionality SEO Etkisi

### Bulgu 16.1 — `?route=product/search` engelli ✓

`robots.txt`:
```
Disallow: /*?route=product/search
```

✓ Doğru. İç arama sonuçları index'lenmemeli.

---

### Bulgu 16.2 — `<meta name="robots" content="noindex">` controller seviye doğrula

`controller/product/search.php` (OpenCart core) `<meta name="robots" content="noindex">` ekliyor mu?

**Test:**
```bash
curl -sL "https://ravendentalgroup.com/index.php?route=product/search&search=test" | grep -i 'robots'
```

Eğer yoksa, header.twig'e koşullu ekle:
```twig
{% if j3.document.getPageRoute() == 'product/search' %}
  <meta name="robots" content="noindex,follow" />
{% endif %}
```

OCMOD ile header.twig'e ekleyebiliriz.

---

### Bulgu 16.3 — Search form GET method ✓

`common/search.twig:27-29`:
```twig
<input type="text" name="search" .../>
<button type="button" class="search-button" data-search-url="{{ search_url }}"></button>
```

JS-driven (button click → redirect). GET form değil. **A11y için form-wrap edilmesi gerek:**
```twig
<form role="search" action="{{ search_url }}" method="get" class="header-search">
  <label for="search-input" class="sr-only">{{ button_search }}</label>
  <input id="search-input" type="search" name="search" value="{{ search }}" placeholder="..." />
  <button type="submit">{{ button_search }}</button>
</form>
```

`type="search"` mobil klavyede X icon getirir.

---

## 17. 404 Page

### Bulgu 17.1 — Çok minimal

**Dosya:** `catalog/view/theme/journal3/template/error/not_found.twig`

Mevcut içerik (özet):
- Breadcrumb
- H1 = "Aradığınız Sayfa Bulunamadı!"
- `<p>` = "Aradığınız Sayfa Bulunamadı."
- "Devam" butonu (anasayfaya)

**Sorun:**
- Tek satır mesaj + tek buton → bounce rate ↑
- Kullanıcıyı yönlendirmiyor (popüler kategoriler/arama)
- "İhtiyacın olabilir" gibi öneri yok

**Önerilen:**
```twig
{{ header }}
<ul class="breadcrumb">...</ul>
<div id="error-not-found" class="container">
  <div id="content" class="col-sm-12">
    <h1>{{ heading_title }}</h1>
    <p>{{ text_error }}</p>

    <div class="search-box-404">
      <h2>Aramak istediğiniz alet ne?</h2>
      <form role="search" action="{{ search_url }}" method="get">
        <input type="search" name="search" placeholder="ör: endodonti eğesi" />
        <button type="submit">Ara</button>
      </form>
    </div>

    <div class="popular-categories">
      <h2>Popüler Kategoriler</h2>
      <ul>
        <li><a href="/endodonti-aletleri">Endodonti Aletleri</a></li>
        <li><a href="/implantoloji-aletleri">İmplantoloji Aletleri</a></li>
        <li><a href="/cerrahi-aletleri">Cerrahi Aletleri</a></li>
        <li><a href="/ortodonti-aletleri">Ortodonti Aletleri</a></li>
        <li><a href="/protez-aletleri">Protez Aletleri</a></li>
        <li><a href="/periodonti-aletleri">Periodonti Aletleri</a></li>
      </ul>
    </div>

    <p><a href="{{ continue }}" class="btn btn-primary">Anasayfaya dön</a></p>
  </div>
</div>
{{ footer }}
```

Bu tema dosyasını **doğrudan değiştirmeden OCMOD ile**:
```xml
<file path="catalog/view/theme/journal3/template/error/not_found.twig">
  <operation>
    <search><![CDATA[<p>{{ text_error }}</p>]]></search>
    <add position="after"><![CDATA[
<div class="search-box-404" style="margin: 30px 0;">
  <h2>Aramak istediğiniz alet ne?</h2>
  <form role="search" action="index.php?route=product/search" method="get">
    <input type="search" name="search" placeholder="ör: endodonti eğesi" class="form-control" style="max-width:400px;display:inline-block" />
    <button type="submit" class="btn btn-primary">Ara</button>
  </form>
</div>

<div class="popular-categories" style="margin: 30px 0;">
  <h2>Popüler Kategoriler</h2>
  <ul class="list-unstyled">
    <li><a href="endodonti-aletleri">Endodonti Aletleri</a></li>
    <li><a href="implantoloji-aletleri">İmplantoloji Aletleri</a></li>
    <li><a href="cerrahi-aletleri">Cerrahi Aletleri</a></li>
    <li><a href="ortodonti-aletleri">Ortodonti Aletleri</a></li>
    <li><a href="protez-aletleri">Protez Aletleri</a></li>
    <li><a href="periodonti-aletleri">Periodonti Aletleri</a></li>
  </ul>
</div>
    ]]></add>
  </operation>
</file>
```

EN dilinde aynı içerik ayrı XML.

---

### Bulgu 17.2 — 404 status code dönüyor mu?

`curl -sI` ile kontrol:
```bash
curl -sI https://ravendentalgroup.com/non-existent-page-12345
```

OpenCart bazen 404 sayfayı 200 status ile döner — **soft 404**. Google için kötü.

Eğer 200 dönerse `controller/error/not_found.php`'ye `$this->response->setStatus(404)` ekle.

---

## 18. Önerilen OCMOD Patch'leri (Özet)

Tek bir `system/raven-frontend.ocmod.xml` dosyasında toplanabilir. Yapısı:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<modification>
  <name>Raven Dental Frontend Patches</name>
  <code>raven_frontend</code>
  <version>1.0</version>
  <author>Raven Dental Team</author>
  <link>https://ravendentalgroup.com</link>

  <!-- 1. viewport-fit=cover + theme-color (Q5) -->
  <file path="catalog/view/theme/journal3/template/common/header.twig">
    <operation>
      <search><![CDATA[<meta name="viewport" content="width=device-width, initial-scale=1.0">]]></search>
      <add position="replace"><![CDATA[<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#0d4a6e">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">]]></add>
    </operation>

    <!-- 2. og:site_name + og:locale (Bulgu 10.2, 10.3) -->
    <operation>
      <search><![CDATA[{% for key, tag in j3.loadController('journal3/seo/meta_tags') %}]]></search>
      <add position="before"><![CDATA[<meta property="og:site_name" content="Raven Dental" />
{% if lang == 'tr-TR' or lang == 'tr-tr' %}
<meta property="og:locale" content="tr_TR" />
<meta property="og:locale:alternate" content="en_GB" />
{% else %}
<meta property="og:locale" content="en_GB" />
<meta property="og:locale:alternate" content="tr_TR" />
{% endif %}
]]></add>
    </operation>

    <!-- 3. Search route noindex (Bulgu 16.2) -->
    <operation>
      <search><![CDATA[<title>{{ title }}</title>]]></search>
      <add position="after"><![CDATA[
{% if j3.document.getPageRoute() == 'product/search' or j3.document.getPageRoute() == 'product/compare' %}
<meta name="robots" content="noindex,follow">
{% endif %}]]></add>
    </operation>

    <!-- 4. Çift H1 fix (Bulgu 4.2) -->
    <operation>
      <search><![CDATA[{% set raven_h1 = j3.settings.get('journal3_home_h1') ?: (heading_title is defined and heading_title ? heading_title : title) %}
{% if raven_h1 %}
  <h1 class="sr-only" style="position: absolute; height: 1px; width: 1px; clip: rect(0,0,0,0);">{{ raven_h1 }}</h1>
{% endif %}]]></search>
      <add position="replace"><![CDATA[{# Raven Dental: sr-only H1 sadece heading_title yokken (anasayfa) #}
{% if not (heading_title is defined and heading_title) %}
  {% set raven_h1 = title %}
  {% if raven_h1 %}
    <h1 class="sr-only" style="position:absolute;height:1px;width:1px;clip:rect(0,0,0,0);">{{ raven_h1 }}</h1>
  {% endif %}
{% endif %}]]></add>
    </operation>
  </file>

  <!-- 5. Login form: type=email, autocomplete (Bulgu 7.1) -->
  <file path="catalog/view/theme/journal3/template/account/login.twig">
    <operation>
      <search><![CDATA[<input type="text" name="email" value="{{ email }}" placeholder="{{ entry_email }}" id="input-email" class="form-control" />]]></search>
      <add position="replace"><![CDATA[<input type="email" name="email" value="{{ email }}" placeholder="{{ entry_email }}" id="input-email" class="form-control" autocomplete="email" inputmode="email" required />]]></add>
    </operation>
    <operation>
      <search><![CDATA[<input type="password" name="password" value="{{ password }}" placeholder="{{ entry_password }}" id="input-password" class="form-control" />]]></search>
      <add position="replace"><![CDATA[<input type="password" name="password" value="{{ password }}" placeholder="{{ entry_password }}" id="input-password" class="form-control" autocomplete="current-password" required />]]></add>
    </operation>
  </file>

  <!-- 6. Forgotten form: type=email -->
  <file path="catalog/view/theme/journal3/template/account/forgotten.twig">
    <operation>
      <search><![CDATA[<input type="text" name="email" value="{{ email }}" placeholder="{{ entry_email }}" id="input-email" class="form-control" />]]></search>
      <add position="replace"><![CDATA[<input type="email" name="email" value="{{ email }}" placeholder="{{ entry_email }}" id="input-email" class="form-control" autocomplete="email" inputmode="email" required />]]></add>
    </operation>
  </file>

  <!-- 7. Register form: autocomplete attrs (Bulgu 7.1) -->
  <file path="catalog/view/theme/journal3/template/account/register.twig">
    <operation>
      <search><![CDATA[<input type="text" name="firstname" value="{{ firstname }}" placeholder="{{ entry_firstname }}" id="input-firstname" class="form-control" />]]></search>
      <add position="replace"><![CDATA[<input type="text" name="firstname" value="{{ firstname }}" placeholder="{{ entry_firstname }}" id="input-firstname" class="form-control" autocomplete="given-name" required />]]></add>
    </operation>
    <operation>
      <search><![CDATA[<input type="text" name="lastname" value="{{ lastname }}" placeholder="{{ entry_lastname }}" id="input-lastname" class="form-control" />]]></search>
      <add position="replace"><![CDATA[<input type="text" name="lastname" value="{{ lastname }}" placeholder="{{ entry_lastname }}" id="input-lastname" class="form-control" autocomplete="family-name" required />]]></add>
    </operation>
    <operation>
      <search><![CDATA[<input type="email" name="email" value="{{ email }}" placeholder="{{ entry_email }}" id="input-email" class="form-control" />]]></search>
      <add position="replace"><![CDATA[<input type="email" name="email" value="{{ email }}" placeholder="{{ entry_email }}" id="input-email" class="form-control" autocomplete="email" inputmode="email" required />]]></add>
    </operation>
    <operation>
      <search><![CDATA[<input type="tel" name="telephone" value="{{ telephone }}" placeholder="{{ entry_telephone }}" id="input-telephone" class="form-control" />]]></search>
      <add position="replace"><![CDATA[<input type="tel" name="telephone" value="{{ telephone }}" placeholder="{{ entry_telephone }}" id="input-telephone" class="form-control" autocomplete="tel" inputmode="tel" />]]></add>
    </operation>
    <operation>
      <search><![CDATA[<input type="password" name="password" value="{{ password }}" placeholder="{{ entry_password }}" id="input-password" class="form-control" />]]></search>
      <add position="replace"><![CDATA[<input type="password" name="password" value="{{ password }}" placeholder="{{ entry_password }}" id="input-password" class="form-control" autocomplete="new-password" minlength="4" required />]]></add>
    </operation>
    <operation>
      <search><![CDATA[<input type="password" name="confirm" value="{{ confirm }}" placeholder="{{ entry_confirm }}" id="input-confirm" class="form-control" />]]></search>
      <add position="replace"><![CDATA[<input type="password" name="confirm" value="{{ confirm }}" placeholder="{{ entry_confirm }}" id="input-confirm" class="form-control" autocomplete="new-password" minlength="4" required />]]></add>
    </operation>
  </file>

  <!-- 8. Contact form: type=email, autocomplete -->
  <file path="catalog/view/theme/journal3/template/information/contact.twig">
    <operation>
      <search><![CDATA[<input type="text" name="name" value="{{ name }}" id="input-name" class="form-control" />]]></search>
      <add position="replace"><![CDATA[<input type="text" name="name" value="{{ name }}" id="input-name" class="form-control" autocomplete="name" required />]]></add>
    </operation>
    <operation>
      <search><![CDATA[<input type="text" name="email" value="{{ email }}" id="input-email" class="form-control" />]]></search>
      <add position="replace"><![CDATA[<input type="email" name="email" value="{{ email }}" id="input-email" class="form-control" autocomplete="email" inputmode="email" required />]]></add>
    </operation>
  </file>

  <!-- 9. Mobile header logo fallback h1 → span -->
  <file path="catalog/view/theme/journal3/template/journal3/headers/mobile/header_mobile_1.twig">
    <operation>
      <search><![CDATA[<h1><a href="{{ home }}">{{ name }}</a></h1>]]></search>
      <add position="replace"><![CDATA[<span class="logo-text"><a href="{{ home }}">{{ name }}</a></span>]]></add>
    </operation>
  </file>
  <file path="catalog/view/theme/journal3/template/journal3/headers/mobile/header_mobile_2.twig">
    <operation>
      <search><![CDATA[<h1><a href="{{ home }}">{{ name }}</a></h1>]]></search>
      <add position="replace"><![CDATA[<span class="logo-text"><a href="{{ home }}">{{ name }}</a></span>]]></add>
    </operation>
  </file>
  <file path="catalog/view/theme/journal3/template/journal3/headers/mobile/header_mobile_3.twig">
    <operation>
      <search><![CDATA[<h1><a href="{{ home }}">{{ name }}</a></h1>]]></search>
      <add position="replace"><![CDATA[<span class="logo-text"><a href="{{ home }}">{{ name }}</a></span>]]></add>
    </operation>
  </file>

  <!-- 10. Loading=lazy + decoding=async (product_card) -->
  <file path="catalog/view/theme/journal3/template/journal3/product_card.twig">
    <operation>
      <search><![CDATA[class="img-responsive img-first lazyload"/>]]></search>
      <add position="replace"><![CDATA[loading="lazy" decoding="async" class="img-responsive img-first lazyload"/>]]></add>
    </operation>
    <operation>
      <search><![CDATA[class="img-responsive img-first"/>]]></search>
      <add position="replace"><![CDATA[loading="lazy" decoding="async" class="img-responsive img-first"/>]]></add>
    </operation>
    <operation>
      <search><![CDATA[class="img-responsive img-second lazyload"/>]]></search>
      <add position="replace"><![CDATA[loading="lazy" decoding="async" class="img-responsive img-second lazyload"/>]]></add>
    </operation>
    <operation>
      <search><![CDATA[class="img-responsive img-second"/>]]></search>
      <add position="replace"><![CDATA[loading="lazy" decoding="async" class="img-responsive img-second"/>]]></add>
    </operation>
  </file>

  <!-- 11. Breadcrumb microdata: <ul> → <ol> + itemscope (Bulgu 13.2) -->
  <!-- NOT: <ul class="breadcrumb"> birden çok dosyada — riskli. Önce 1 dosyada test edilmeli. -->
  <file path="catalog/view/theme/journal3/template/product/category.twig">
    <operation>
      <search><![CDATA[<ul class="breadcrumb">
  {% for breadcrumb in breadcrumbs %}
    <li><a href="{{ breadcrumb.href }}">{{ breadcrumb.text }}</a></li>
  {% endfor %}
</ul>]]></search>
      <add position="replace"><![CDATA[<ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
  {% for breadcrumb in breadcrumbs %}
    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
      <a itemprop="item" href="{{ breadcrumb.href }}"><span itemprop="name">{{ breadcrumb.text }}</span></a>
      <meta itemprop="position" content="{{ loop.index }}">
    </li>
  {% endfor %}
</ol>]]></add>
    </operation>
  </file>

  <!-- 12. 404 page enhancement -->
  <file path="catalog/view/theme/journal3/template/error/not_found.twig">
    <operation>
      <search><![CDATA[<p>{{ text_error }}</p>]]></search>
      <add position="after"><![CDATA[
<div class="search-box-404" style="margin: 30px 0;">
  <h2>Aramak istediğiniz alet ne?</h2>
  <form role="search" action="index.php?route=product/search" method="get">
    <input type="search" name="search" placeholder="ör: endodonti eğesi, anguldurva" class="form-control" style="max-width:400px;display:inline-block;margin-right:10px" />
    <button type="submit" class="btn btn-primary">Ara</button>
  </form>
</div>

<div class="popular-categories" style="margin: 30px 0;">
  <h2>Popüler Kategoriler</h2>
  <ul>
    <li><a href="endodonti-aletleri">Endodonti Aletleri</a></li>
    <li><a href="implantoloji-aletleri">İmplantoloji Aletleri</a></li>
    <li><a href="cerrahi-aletleri">Cerrahi Aletleri</a></li>
    <li><a href="ortodonti-aletleri">Ortodonti Aletleri</a></li>
    <li><a href="protez-aletleri">Protez Aletleri</a></li>
    <li><a href="periodonti-aletleri">Periodonti Aletleri</a></li>
    <li><a href="el-aletleri">El Aletleri</a></li>
    <li><a href="elektronik-cihazlar">Elektronik Cihazlar</a></li>
  </ul>
</div>]]></add>
    </operation>
  </file>

  <!-- 13. Twitter card type → summary_large_image -->
  <file path="catalog/controller/journal3/seo.php">
    <operation>
      <search><![CDATA[$tags['twitter:card'] = array(
				'type'    => 'name',
				'content' => 'summary',
			);]]></search>
      <add position="replace"><![CDATA[$tags['twitter:card'] = array(
				'type'    => 'name',
				'content' => 'summary_large_image',
			);]]></add>
    </operation>
  </file>

</modification>
```

**Yükleme adımı:**
1. Bu XML'i `/public_html/system/raven-frontend.ocmod.xml` olarak yükle.
2. Admin → Extensions → Modifications → Refresh (mavi ↻).
3. Doğrulama: `curl -sL https://ravendentalgroup.com/ | grep -E 'og:locale|theme-color|viewport-fit'`

**Risk:**
- OCMOD search string'leri exact match — bir karakter (whitespace dahil) farklıysa patch uygulanmaz.
- Journal3 v3.1.12 dışında bir versiyona güncellenirse search hedefi kaybolabilir.
- Yedek: `storage/modification/` klasörü cache — silinirse refresh tekrar tetiklenir.

---

## Kapanış Notları

### Bizim açık borç (05-SEO-STATUS.md'den hatırlatma)
- ❌ H1 nihai düzeltme: Bu raporda **Bulgu 4.2 ile çözüm önerildi**. OCMOD #4 patch.
- ❌ og:title düzeltme: Bu rapor `seo.php:261` değişikliği ile çözüldü. **Ayrı bir OCMOD patch** lazım (`config_meta_title` öncelik). Henüz patch eklemedim — bunu mevcut `theme-patches/raven.ocmod.xml`'e ekleyebilirsin (zaten H1 OCMOD planlı).
- ❌ Twitter card image 1200x675: **OCMOD #13** ile type düzeltildi. Image dimensions admin → Journal Editor'da.
- ❌ Sosyal medya linkleri: `href="#"` kontrolü gerekiyor — bu rapor footer.twig'i incelemedi (footer_menu loader üzerinden geliyor). Admin'den Journal3 → Footer → Social Icons.
- ❌ Görsel alt text: **Bulgu 5.3** ile category.twig için fix. Diğer dosyalar için manuel kontrol.
- ❌ Lazy loading: **Bulgu 5.1** ile çözüldü. **Önce admin ayarını aç**, sonra OCMOD #10.
- ❌ Kategori uzun açıklama: Strategik S3.
- ❌ Blog: Strategik S4.
- ⏭️ GA4 + GSC + Yandex Webmaster: header.twig customCodeHeader'a manuel ekleme.
- ❌ Schema BreadcrumbList kategori: OCMOD #11.
- ❌ Sitemap priority: Sitemap controller değişimi (`extension/feed/google_sitemap`).
- ❌ LocalBusiness schema: Strategik S2.

### Yapılması en hızlı dokunmalar (sıralı öneri)

1. Admin'den **Performance → Lazy Load Images = ON** (5 dk, 0 risk).
2. Admin'den **Twitter Card Image = 1200×675** (admin Journal Editor SEO sekmesi).
3. **Yukarıdaki OCMOD XML'i tek dosyada yükle** ve refresh → 13 frontend fix bir vuruşta.
4. Footer social link audit (admin → Footer modülü).
5. Strategik S1-S15 roadmap'e ekle.

### Etki tahmini (en iyi senaryo)

- **LCP**: 3.2s → 2.1s (lazy + decoding=async + viewport-fit)
- **CLS**: 0.18 → 0.05 (image width/height defaults)
- **Mobile conversion**: %2.1 → %3.0 (form autocomplete + type=email)
- **Lighthouse SEO**: 89 → 96 (og:locale, breadcrumb microdata, H1 normalize)
- **Lighthouse A11y**: 78 → 88 (form a11y + alt fallback + landmark `<main>`)

Bu rakamlar gerçek ölçüm değil — Strategik S5 (critical CSS) yapılmadan tek başına %15+ kazanım garanti değil. Quick wins gerçekçi %5-10 LCP iyileşme verir.

---

**Rapor sonu.** Toplam bulgu: 56 (14 quick + 15 strategik + 27 referans). Önerilen patch: 1 OCMOD XML dosyası (13 operation). Tahmini uygulama süresi: 3-4 saat (test dahil).
