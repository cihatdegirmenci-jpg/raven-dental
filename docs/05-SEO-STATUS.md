# 05 - SEO Status (Mevcut Durum)

> Bu doc, **bu oturum sonrası** SEO durumunu gösterir.
> Önceki SEO denetimi: ilk konuşmada yapıldı (raporda bahsedildi).

## Hızlı Skor Kartı

| Alan | Önce | Sonra | Hedef |
|---|---|---|---|
| Anasayfa title | `Raven Dental` | `Diş Hekimliği Aletleri ve Cerrahi Ekipmanlar \| Raven Dental` | ✓ |
| Anasayfa meta description | `Raven Dental` | 156 char Türkçe açıklama | ✓ |
| Kategori meta (TR) | 18/18 generic | 18/18 yazılmış | ✓ |
| Ürün meta_title | %100 generic (= ad) | %100 ad + "Raven Dental" | ✓ |
| Ürün meta_description | %82 = ad veya boş | %95+ şablon ile dolu | △ (özel yazılmalı) |
| Sitemap.xml | 404 | 200, 369 URL | ✓ |
| SEO URL'ler | Kapalı (parametre) | Aktif, 738 keyword | ✓ |
| robots.txt | Bozuk | Düzgün + Sitemap referansı | ✓ |
| Schema.org | 2 blok (WebSite, Org minimal) | 3+ blok (WebSite, Org+contactPoint, Breadcrumb, Product) | ✓ |
| hreflang | YOK | TR/EN/x-default 3 etiket | ✓ |
| H1 | sr-only, "Raven Dental" | sr-only, hâlâ "Raven Dental" | ❌ (henüz) |
| OG title | "Raven Dental" | "Raven Dental" (Journal3 controller) | ❌ (henüz) |
| GA4 | Yok | Yok | ⏭️ |
| GSC | Yok | Yok | ⏭️ |
| Görsel alt text | Tutarsız (TR/EN karışık, 9 boş) | Aynı | △ |
| Görsel lazy loading | 0/39 | 0/39 | ❌ (henüz) |
| Image WebP | Yok | Yok | ⏭️ |
| Product images optimize | Yok | Yok | ⏭️ |

## Anahtar Kelime Hedefleme

### Anasayfa
- diş hekimliği aletleri
- dental aletler
- endodonti aletleri
- implant aletleri
- cerrahi diş aletleri
- ortodonti
- diş hekimi malzemeleri
- dental ekipman
- toptan diş aletleri

### Kategori bazlı (TR)
| Kategori | TR Slug | Hedef Anahtar Kelime |
|---|---|---|
| Diagnostics (id=59) | `/diagnostik-aletleri` | diagnostik diş aletleri, muayene aletleri, sonda ayna |
| Endodontics (68) | `/endodonti-aletleri` | endodonti aletleri, kanal tedavisi aletleri |
| Orthodontics (67) | `/ortodonti-aletleri` | ortodonti aletleri, ortodonti pens davye |
| Prosthetics (65) | `/protez-aletleri` | protez aletleri, diş protezi el aletleri |
| Implantology (64) | `/implantoloji-aletleri` | implant aletleri, kemik frezi, vida sürücü |
| Periodontics (63) | `/periodonti-aletleri` | küret, gracey, periodonti aletleri |
| Surgery (62) | `/cerrahi-aletleri` | cerrahi diş aletleri, bistüri, makas |
| Extraction (61) | `/cekme-aletleri` | davye, elevatör, çekim aletleri |
| Preservation (60) | `/restorasyon-aletleri` | restorasyon, dolgu aletleri, kondansatör |
| Processing (69) | `/islem-aletleri` | diş hekimliği yardımcı aletler |
| Elektronik (72) | `/elektronik-cihazlar` | aerator, anguldurva, mikromotor |
| Sarf (70) | `/sarf-malzemeleri` | rubber dam, frez, vida, sarf |
| El Aletleri (66) | `/el-aletleri` | diş el aletleri tüm branş |
| Raven Cerrahi (71) | `/raven-cerrahi-aletler` | Raven brand cerrahi aletleri |

### Alt kategoriler (Elektronik)
- Aerator (73) → `/aerator`
- Anguldurva (74) → `/anguldurva`
- Piyasemen (75) → `/piyasemen`
- Mikro Motor (76) → `/micro-motor`

## Sitemap.xml Durumu

```
URL: https://ravendentalgroup.com/sitemap.xml
HTTP: 200 OK
Content-Type: application/xml
Toplam URL: 369
  - 18 kategori
  - 345 ürün (aktif)
  - 6 bilgi sayfası
```

Yapı:
- `<urlset>` + `<image:image>` extension
- Her ürün için image_loc (Google Image Search için)
- `<changefreq>weekly</changefreq>`
- `<priority>1.0</priority>` (tüm ürünler — TODO: hiyerarşik priority)

⚠️ **Sitemap kalitesi iyileştirmeleri (TODO):**
- changefreq daha gerçekçi (ana sayfa daily, kategori weekly, ürün monthly)
- priority hiyerarşik (anasayfa 1.0, kategori 0.8, ürün 0.6)
- lastmod tüm satırlarda doğru tarih
- Sitemap index dosyası (büyüdüğünde ayrılabilir)

## robots.txt İçerik

```
User-agent: *

# Engelleme - parametre tabanlı duplicate content
Disallow: /*?sort=
Disallow: /*&sort=
Disallow: /*?order=
Disallow: /*&order=
Disallow: /*?limit=
Disallow: /*&limit=
Disallow: /*?filter_name=
Disallow: /*&filter_name=
Disallow: /*?filter_sub_category=
Disallow: /*&filter_sub_category=
Disallow: /*?filter_description=
Disallow: /*&filter_description=
Disallow: /*?page=
Disallow: /*&page=

# Engelleme - admin/sistem
Disallow: /admin/
Disallow: /system/
Disallow: /catalog/
Disallow: /vqmod/
Disallow: /image/cache/

# Engelleme - hesap ve checkout
Disallow: /index.php?route=account/
Disallow: /index.php?route=checkout/
Disallow: /index.php?route=affiliate/
Disallow: /index.php?route=product/search
Disallow: /*?route=account/
Disallow: /*?route=checkout/

# İzin ver (CSS/JS bloklarını engelleme)
Allow: /catalog/view/
Allow: /image/

Sitemap: https://ravendentalgroup.com/index.php?route=extension/feed/google_sitemap
```

## Mevcut Schema.org JSON-LD

Anasayfada **3 blok** (önce 2'ydi):

### 1. WebSite
```json
{"@type": "WebSite", "name": "Raven Dental", "description": "...", "potentialAction": {"SearchAction"}}
```

### 2. Organization (zenginleştirilmiş — bizim customCodeHeader)
```json
{
  "@type": "Organization",
  "@id": "https://ravendentalgroup.com/#organization",
  "name": "Raven Dental",
  "alternateName": "Raven Dental Group",
  "url": "...",
  "logo": {...},
  "contactPoint": {
    "telephone": "+90-552-853-0399",
    "areaServed": "TR",
    "availableLanguage": ["Turkish", "English"]
  },
  "knowsAbout": ["Dental Instruments", "Endodontics", ...]
}
```

### 3. Eski Organization (Journal3 default)
- Minimal, sadece logo+url

⚠️ **Çakışma kontrolü:** İki Organization aynı sayfada olmak Google için zararlı değil ama temizlenebilir. Journal3 default'unu kaldırma yöntemi: `seoGoogleRichSnippetsStatus=false` veya theme controller'a dokun.

### Ürün sayfasında ek bloklar
- **BreadcrumbList** (Journal3) — sadece Home → Product (Category eksik!)
- **Product** with offers (price, availability, currency, priceValidUntil, seller)

## Hâlâ Eksik / Açık Sorunlar

### 1. H1 hâlâ "Raven Dental"
**Durum:** header.twig'e fallback chain yazıldı ama j3.settings.get config_name'e fallback yapıyor.
**Çözüm:** header.twig'i daha agresif değiştir (j3.settings bypass), modification refresh.

### 2. og:title "Raven Dental"
**Durum:** Journal3 `controller/journal3/seo/meta_tags.php` config_name kullanıyor.
**Çözüm:** O controller'ı oku, og_title kaynağını `$document->getTitle()` veya `$meta_title`'a çevir.

### 3. Twitter Card image dimensions
**Durum:** 200x200 (default 1200x675 olmalı — Twitter optimal)

### 4. Sosyal medya hesapları
**Durum:** Footer'da Facebook/Twitter/Instagram ikonu var ama `href="#"` (boş)
**Karar:** Ya hesapları aç ve bağla, ya da ikonları kaldır.

### 5. Görsel alt text tutarsızlık
**Durum:** 39 görsel, 9 alt boş, kalanı dosya adı veya İngilizce/Türkçe karışık.
**Çözüm:** Theme image macro'sunda `{{ alt|default(product.name) }}` zorla.

### 6. Lazy loading
**Durum:** Hiçbir image'da `loading="lazy"` yok.
**Etki:** İlk yüklemede 39 görsel hep birden iner, LCP olumsuz.
**Çözüm:** Theme image template'inde `loading="lazy"` ekle.

### 7. Kategori description (uzun)
**Durum:** 18/18 kategori `description` alanı boş.
**SEO etkisi:** Kategori sayfasında uzun açıklama yok = thin content.
**Çözüm:** Her kategori için 200-400 kelime SEO içerik yazılmalı (TODO content plan).

### 8. Blog
**Durum:** `/journal3/blog` aktif ama 0 yazı.
**Fırsat:** Long-tail anahtar kelime hedeflemesi için blog şart.

## TODOs Özet (SEO bazında)

- [ ] H1 nihai düzeltme (theme literal)
- [ ] og:title düzeltme (controller patch)
- [ ] Twitter card image 1200x675
- [ ] Sosyal medya linkleri bağla veya kaldır
- [ ] Görsel alt text tutarlı (theme macro)
- [ ] Lazy loading tüm görsellere
- [ ] Kategori uzun açıklama (18 kategori)
- [ ] Blog'a 10+ makale (uzun-kuyruk SEO)
- [ ] GA4 + GSC + Yandex Webmaster
- [ ] Schema BreadcrumbList kategori sayfası
- [ ] Sitemap priority hiyerarşik
- [ ] LocalBusiness schema (eğer fiziksel adres varsa)

→ Detaylı plan: [12-ROADMAP.md](./12-ROADMAP.md)
