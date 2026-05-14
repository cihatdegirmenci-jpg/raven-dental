# 16 — Google SEO Rules (Reference & Checklist)

**Doküman amacı:** Bu doküman, Raven Dental projesinde (OpenCart 3.0.3.8 + Journal3, TR B2B diş hekimliği aletleri e-ticaret) yapılan/yapılacak tüm SEO geliştirme, PR review ve refaktör çalışmalarında **tek referans noktası** olarak kullanılacaktır. İçerik, Google'ın 2026-05-12 tarihi itibarıyla geçerli olan resmi kurallarına ve duyurularına dayanır.

**Son güncelleme:** 2026-05-12
**Maintainer:** Raven Dental Tech / SEO
**Cross-references:**
- `/Users/ipci/raven-dental/docs/05-SEO-STATUS.md` — mevcut SEO durum raporu
- `/Users/ipci/raven-dental/docs/15-CODE-REVIEW-SUMMARY.md` — geçmiş code review bulguları
- `/Users/ipci/raven-dental/analysis/seo-patches/` — uygulanan SEO patch'leri

**Nasıl güncellenir:** Google bir core update, spam update veya policy değişikliği duyurduğunda → ilgili bölüm güncellenip git'e PR olarak gönderilir. Her güncelleme "Son güncelleme" tarihini değiştirir.

---

## 1. Crawling & Indexing

### 1.1 robots.txt

**Resmi kaynak:** https://developers.google.com/search/docs/crawling-indexing/robots/intro

**Yapılmalı:**
- [ ] robots.txt site kök dizininde (`/robots.txt`) olmalı, UTF-8 encoded
- [ ] `Sitemap:` direktifi mutlaka eklenmeli (tam URL)
- [ ] Sadece **crawl** kontrolü için kullanılmalı (indeks engeli değil — bu noindex işidir)
- [ ] Admin paneli, sepet, ödeme, hesap sayfaları `Disallow` edilmeli
- [ ] Faceted search / parametre fırtınası oluşturan URL'ler kontrol altına alınmalı

**Yapılmamalı:**
- [ ] `Disallow` ile **indeksten** çıkarmaya çalışmak (Google sayfayı çekemese bile başka kaynaklardan indeksleyebilir)
- [ ] CSS/JS dosyalarını engellemek (mobile-first indexing için render gerekli)
- [ ] Wildcard kullanımında aşırıya kaçmak

**Raven Dental özel notları:**
- OpenCart varsayılan `robots.txt`'i (`/admin/`, `/system/`, `/catalog/controller/`, `?route=checkout/`, `?route=account/`) korunmalı
- Journal3'ün AJAX endpoint'leri (`?route=journal3/...`) crawl edilmemeli
- `index.php?route=product/product&product_id=X` formundaki çift URL'ler canonical ile yönetilmeli, robots ile değil
- **Mevcut durum:** robots.txt mevcut — analysis/seo-patches içinde gözden geçirildi

### 1.2 sitemap.xml

**Resmi kaynak:** https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap

**Yapılmalı:**
- [ ] Tek sitemap maks. **50.000 URL** ve **50 MB** (uncompressed) sınırını aşmamalı
- [ ] Bu sınırı aşan siteler için **sitemap index** dosyası kullanılmalı
- [ ] `<lastmod>` sadece sayfa **anlamlı** değişiklik aldıysa güncellenmeli (telif yılı değişikliği sayılmaz)
- [ ] Sadece canonical, indekslenebilir, 200 OK dönen URL'ler eklenmeli
- [ ] Resim ve video için ayrı sitemap (image sitemap / video sitemap) kullanılmalı

**Yapılmamalı:**
- [ ] `<priority>` ve `<changefreq>` alanlarına emek harcamak — Google bunları **yok sayar**
- [ ] noindex, 404, 301 redirect veya canonical olmayan URL'leri eklemek
- [ ] Statik üretim olmadan dinamik olarak her istekte regenerate etmek (cache şart)

**Raven Dental özel notları:**
- Ürün sayısı henüz 50k altı → tek sitemap yeterli; sitemap index opsiyonel
- Kategori sitemap'i, ürün sitemap'i, statik sayfa sitemap'i ayrı tutulmalı (debugging ve coverage tracking için)
- OpenCart yerleşik sitemap modülü `<priority>` ve `<changefreq>` üretir → Google için zararsız ama gereksiz
- **Mevcut durum:** sitemap.xml üretiliyor; lastmod doğruluğu doğrulanmalı

### 1.3 Canonical URL

**Resmi kaynak:** https://developers.google.com/search/docs/crawling-indexing/canonicalization

**Yapılmalı:**
- [ ] Her sayfa **self-referencing** canonical içermeli (`<link rel="canonical" href="...">`)
- [ ] Mutlak URL kullanılmalı (https://, domain dahil)
- [ ] Tek `<link rel="canonical">` per sayfa
- [ ] Pagination'da her sayfa **kendisini** canonical göstermeli (page 2 → page 1 DEĞİL)
- [ ] HTTP header üzerinden veya HTML head'de tutarlı şekilde verilmeli

**Yapılmamalı:**
- [ ] Tüm pagination sayfalarını page 1'e canonical göstermek (Google diğer sayfaları yok sayar)
- [ ] Cross-domain canonical kullanırken farklı içerik göstermek
- [ ] Canonical'ı JavaScript ile sonradan inject etmek (Google bunu indirgenmiş bir sinyal olarak görür)

**Raven Dental özel notları:**
- OpenCart `index.php?route=product/product&product_id=123` ve SEO URL `urun-adi` aynı sayfayı verir → canonical SEO URL'i göstermeli
- Filter/sort parametreleri (`?sort=`, `?order=`, `?limit=`) canonical'da olmamalı
- Journal3'ün AJAX category load'u canonical'ı bozmamalı
- **Mevcut durum:** canonical default olarak SEO URL'e yönlendiriyor; query string varyantları test edilmeli

### 1.4 Meta Robots

**Resmi kaynak:** https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag

**Yapılmalı:**
- [ ] İndekslenmesin istenen sayfalarda `<meta name="robots" content="noindex, follow">`
- [ ] Snippet kontrolü için `max-snippet`, `max-image-preview`, `max-video-preview` kullanılabilir
- [ ] X-Robots-Tag HTTP header'ı non-HTML kaynaklarda (PDF, image) tercih edilmeli

**Yapılmamalı:**
- [ ] noindex'i robots.txt ile `Disallow` edilmiş sayfaya koymak (Google sayfayı çekemediği için noindex'i göremez)
- [ ] "nofollow" sitewide kullanmak (link equity ölür)
- [ ] noarchive ve nosnippet'i gereksiz yere koymak (CTR'a zarar verir)

**Raven Dental özel notları:**
- Sepet, hesap, ödeme, kullanıcı paneli → `noindex, nofollow`
- Boş kategoriler → `noindex, follow`
- "Out of stock" ürün politikası bölüm 5.2'de detaylandırıldı
- **Mevcut durum:** Hesap/sepet sayfalarında noindex doğru

### 1.5 URL Structure

**Resmi kaynak:** https://developers.google.com/search/docs/crawling-indexing/url-structure

**Yapılmalı:**
- [ ] Kısa, açıklayıcı, anahtar kelime içeren slug
- [ ] Kelimeler arasında **hyphen** (`-`) — underscore (`_`) değil
- [ ] Küçük harf, ASCII karakterler (Türkçe karakterler ASCII karşılıklarına dönüştürülmeli: `ç→c`, `ş→s`, `ı→i`, `ğ→g`, `ü→u`, `ö→o`)
- [ ] Hiyerarşi mantıklı olmalı: `/kategori/alt-kategori/urun-adi`

**Yapılmamalı:**
- [ ] Session ID, tracking parametre, kullanıcı ID içeren URL'ler
- [ ] Stop word doldurmak (`/the-best-of-our-...`)
- [ ] URL'i sonradan değiştirip 301 yönlendirme yapmamak

**Raven Dental özel notları:**
- OpenCart SEO URL aktif olmalı (`config.php` + `.htaccess`)
- Ürün slug'larında Türkçe karakter URL'e gitmemeli: "Diş Hekimliği Aynası" → `dis-hekimligi-aynasi`
- Hyphen vs underscore kuralı: SADECE hyphen
- **Mevcut durum:** SEO URL aktif, slug temizliği bazı eski kayıtlarda eksik olabilir

### 1.6 Pagination

**Resmi kaynak:** https://developers.google.com/search/docs/specialty/ecommerce/pagination-and-incremental-page-loading

**Yapılmalı:**
- [ ] **Her paginated sayfa kendisine** canonical göstermeli
- [ ] `<a href>` ile gerçek link verilmeli (sadece JavaScript ile değil)
- [ ] "Load More" + traditional pagination hybrid yaklaşımı tercih edilmeli
- [ ] Pure infinite scroll varsa, fallback olarak `?page=N` URL'leri crawlable olmalı

**Yapılmamalı:**
- [ ] `rel="prev"` / `rel="next"` kullanmak (Google 2019'da deprecate etti, 2016'dan beri kullanmıyor; Bing hâlâ kullanır → istenirse bırakılabilir)
- [ ] Tüm pagination sayfalarını page 1'e canonical göstermek
- [ ] JavaScript-only infinite scroll yapmak (ürünler orphan kalır)

**Raven Dental özel notları:**
- Journal3 kategori sayfasında AJAX pagination var → her sayfa için `?page=N` URL'i HTML'de bulunmalı
- Page 1 canonical = base URL (kategori URL'i), page 2+ canonical = kendisi
- **Mevcut durum:** Journal3 default davranışı kontrol edilmeli

---

## 2. On-page SEO

### 2.1 Title Tag

**Resmi kaynak:** https://developers.google.com/search/docs/appearance/title-link

**Yapılmalı:**
- [ ] 50-60 karakter (~580 piksel) — desktop ve mobilde tam görünmeli
- [ ] Önemli anahtar kelimeyi başa al
- [ ] Brand sona: `Ürün Adı - Kategori | Raven Dental`
- [ ] Her sayfa **benzersiz** olmalı

**Yapılmamalı:**
- [ ] Keyword stuffing (`Diş Aynası Dis Aynasi Mirror Mirror Mirror`)
- [ ] ALL CAPS
- [ ] Tüm sitede aynı başlık şablonu

**Raven Dental özel notları:**
- Format: `{Ürün Adı} - {Kategori} | Raven Dental` (ürün)
- `{Kategori} - Profesyonel Diş Hekimliği Aletleri | Raven Dental` (kategori)
- TR karakterler title'da serbestçe kullanılabilir
- **Mevcut durum:** docs/05-SEO-STATUS.md kontrol edilmeli; bazı kategori sayfalarında title çakışması rapor edilmiş

### 2.2 Meta Description

**Resmi kaynak:** https://developers.google.com/search/docs/appearance/snippet

**Yapılmalı:**
- [ ] 140-160 karakter (~920 piksel)
- [ ] CTA içermeli (Keşfedin, İnceleyin, Sipariş Verin)
- [ ] Sayfanın değer önermesini özetlemeli
- [ ] Her sayfa benzersiz olmalı

**Yapılmamalı:**
- [ ] Sayfa içeriğiyle alakasız yazmak (Google %62 oranında yeniden yazıyor)
- [ ] Boş bırakmak (Google rastgele bir snippet seçer)
- [ ] Aynı meta'yı tüm site genelinde tekrarlamak

**Raven Dental özel notları:**
- Ürün açıklamasının ilk 150 karakteri otomatik fallback olabilir, ama manuel optimize edilmiş daha iyi
- "CE sertifikalı", "Türkiye üretimi", "B2B fiyat" gibi differentiator kelimeler eklenmeli
- **Mevcut durum:** patch'lerle güncelleniyor; eksik olan ürünler için template aktif

### 2.3 H1-H6 Hierarchy

**Resmi kaynak:** https://developers.google.com/search/docs/appearance/structured-data/article (heading kullanımı dolaylı)

**Yapılmalı:**
- [ ] **Tek H1** per sayfa (ürün sayfası → ürün adı; kategori → kategori adı)
- [ ] Mantıksal hiyerarşi: H1 → H2 → H3 (skip etme)
- [ ] H1 sayfa konusunu yansıtmalı, title tag ile uyumlu olmalı

**Yapılmamalı:**
- [ ] Logo'yu H1 yapmak
- [ ] CSS class için H tag kullanmak ("daha büyük yazı" amacıyla)
- [ ] Birden fazla H1 (HTML5 izin verse de SEO için sakıncalı)

**Raven Dental özel notları:**
- Journal3 bazı widget'larda H2/H3'ü dekoratif kullanır → kontrol edilmeli
- **Mevcut durum:** docs/15-CODE-REVIEW-SUMMARY.md'de heading order bulgusu var

### 2.4 Image Alt Text

**Resmi kaynak:** https://developers.google.com/search/docs/appearance/google-images

**Yapılmalı:**
- [ ] Görselin **ne olduğunu** açıkla (anahtar kelime stuffing değil)
- [ ] Dekoratif görsellerde `alt=""` (boş alt) — alt'ı silme
- [ ] Ürün görselleri: marka + model + açı: "Raven Dental ayna no.5 ön görünüm"
- [ ] Lazy-loading kullanılırken `loading="lazy"` ekle

**Yapılmamalı:**
- [ ] `alt="dis aynasi dis aynasi en iyi dis aynasi"` (keyword stuffing)
- [ ] Tüm görsellerde aynı alt
- [ ] Alt yerine sadece file name

**Raven Dental özel notları:**
- Şu anki patch hedefi: `/Users/ipci/raven-dental/analysis/seo-patches/11-image-alts/`
- Türkçe alt yazıları doğru; ek: kategori bağlamı eklenebilir
- **Mevcut durum:** çalışma aktif (patch 11)

### 2.5 Internal Linking

**Resmi kaynak:** https://developers.google.com/search/docs/crawling-indexing/links-crawlable

**Yapılmalı:**
- [ ] Açıklayıcı **anchor text** ("daha fazla" değil → "diş aynaları kategorisi")
- [ ] Önemli sayfalar 3 click'ten uzakta olmamalı
- [ ] Breadcrumb mutlaka olmalı
- [ ] İlgili ürünler / cross-sell internal link sağlar

**Yapılmamalı:**
- [ ] JavaScript-only navigation (`<div onclick>`)
- [ ] Aşırı miktarda link tek sayfada (~150 üzeri risk)
- [ ] Anchor olarak sadece "buraya tıklayın"

**Raven Dental özel notları:**
- "İlgili Ürünler" widget'ı zaten var → anchor text optimize edilebilir
- Kategori filter'ları faceted URL'e götürüyorsa nofollow + noindex değerlendir
- **Mevcut durum:** breadcrumb mevcut; ürün-ürün internal link şeması güçlendirilmeli

### 2.6 Schema.org Structured Data

**Resmi kaynak:** https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data

**E-commerce için öncelik sırası:**

1. **Product** + Offer (price, priceCurrency, availability) — ürün sayfası zorunlu
2. **BreadcrumbList** — tüm derin sayfalarda
3. **Organization** (logo, sameAs, contactPoint) — anasayfa
4. **AggregateRating** + **Review** — değerlendirme varsa (bkz. bölüm 8)
5. **FAQPage** — gerçek FAQ varsa (sadece markup için fake soru yaratma)
6. **WebSite** + SearchAction — site arama kutusu varsa

**Yapılmalı:**
- [ ] JSON-LD formatı tercih et (Google önerisi)
- [ ] HTML server-side render edilmeli (JavaScript inject etme)
- [ ] Görünen içerikle eşleşmeli (consistency önemli)
- [ ] Rich Results Test ile doğrula

**Yapılmamalı:**
- [ ] Kullanıcıya gösterilmeyen veriyi schema'ya koymak
- [ ] FAQ schema'yı fake soru-cevapla doldurmak (2023'den beri Google bunları rich results'tan çıkardı)
- [ ] Self-serving review schema (bölüm 8)

**Raven Dental özel notları:**
- Ürün schema'sında `gtin`, `mpn`, `brand: "Raven Dental"`, `manufacturer: "Raven Dental"` olmalı
- B2B fiyatlandırma çoklu offer ile gösterilebilir (`priceSpecification`)
- **Mevcut durum:** Product schema entegre; AggregateRating eklenmesi planlanmış

### 2.7 Open Graph + Twitter Cards

**Resmi kaynak:** https://ogp.me/ ; https://developer.twitter.com/en/docs/twitter-for-websites/cards/overview/abouts-cards

**Yapılmalı:**
- [ ] `og:title`, `og:description`, `og:image` (≥1200x630), `og:url`, `og:type`
- [ ] Ürün için `og:type=product`
- [ ] `twitter:card=summary_large_image`
- [ ] Resim mutlak URL ve HTTPS

**Raven Dental özel notları:**
- Sosyal paylaşımlarda B2B alıcılara hitap eden açıklama
- Logo değil, ürün görseli paylaş

### 2.8 hreflang (Multi-language)

**Resmi kaynak:** https://developers.google.com/search/docs/specialty/international/localized-versions

**Yapılmalı:**
- [ ] Her sayfa **self-referencing** hreflang içermeli
- [ ] Symmetric (TR→EN linkliyorsa EN→TR de linklemeli)
- [ ] ISO 639-1 dil + opsiyonel ISO 3166-1 ülke: `tr-TR`, `en`, `en-US`
- [ ] `x-default` fallback olarak eklenmeli

**Raven Dental özel notları:**
- **Site TR-only.** Şu an çoklu dil yok → tek dil için hreflang **gerekli değil**
- Gelecekte EN versiyonu açılırsa:
  ```html
  <link rel="alternate" hreflang="tr-TR" href="https://raven-dental.com.tr/urun" />
  <link rel="alternate" hreflang="en"    href="https://raven-dental.com/product" />
  <link rel="alternate" hreflang="x-default" href="https://raven-dental.com.tr/urun" />
  ```
- **Mevcut durum:** hreflang uygulanmamış (gerekli değil)

---

## 3. Content Quality

### 3.1 E-E-A-T

**Resmi kaynak:** https://developers.google.com/search/docs/fundamentals/creating-helpful-content ; Quality Rater Guidelines

**E-E-A-T = Experience, Expertise, Authoritativeness, Trustworthiness**

**Yapılmalı:**
- [ ] **Experience:** Ürünün gerçek kullanımı, sahadan örnekler
- [ ] **Expertise:** Yazar/üretici uzmanlığı net belirtilmeli (CE sertifika no, üretim tesisi adresi, ISO belgesi)
- [ ] **Authoritativeness:** Sektör derneği üyelikleri, fuar katılımları, hakemli dergi yayınları
- [ ] **Trustworthiness:** İade politikası, gizlilik politikası, fiziksel adres, telefon, KEP adresi, MERSİS no, ticaret sicil no

**Yapılmamalı:**
- [ ] Anonim "ekibimiz" yazarları
- [ ] Sahte uzman fotoğrafı / stock image bio
- [ ] Karşılaştırma sayfalarında rakip ürünleri haksız aşağılamak

**Raven Dental özel notları:**
- B2B diş hekimliği aletleri = **yarı-YMYL** (sağlık hizmeti dolaylı etkilenir)
- "Üretici olarak biz" pozisyonu güçlü E-E-A-T sinyali → öne çıkarılmalı
- Sertifikalar (CE, ISO 13485 vb.) anasayfa + about + ürün sayfasında görünür olmalı
- **Mevcut durum:** kurumsal sayfa var; sertifika görünürlüğü iyileştirilmeli

### 3.2 YMYL

**YMYL = Your Money Your Life** — yanlış bilginin maddi/sağlık zararı verebileceği konular.

**Diş hekimliği aletleri için durum:**
- B2C ilaç/tıp ürünü = tam YMYL
- B2B profesyonel medikal alet = **yarı-YMYL** (son kullanıcı diş hekimi, profesyonel filtre var)
- Ama Google yine de yüksek standart bekliyor

**Yapılmalı:**
- [ ] Kullanım talimatları, sterilizasyon prosedürü açık
- [ ] Risk ve kontrendikasyon belirtilmeli
- [ ] Üretim tarihi, son kullanma, lot numarası ürün sayfasında
- [ ] CE sertifika ve sınıfı (Class I, IIa vb.) belirtilmeli

**Raven Dental özel notları:**
- Ürün açıklamasında klinik kullanım amacı net olmalı
- "Tıbbi tavsiye değildir" disclaimer gereksiz (B2B alıcı zaten profesyonel) ama kullanım kılavuzu linki olmalı
- **Mevcut durum:** sertifika bilgileri kısmen — tüm ürünlere yayılması planlanıyor

### 3.3 Helpful Content

**Resmi kaynak:** https://developers.google.com/search/docs/fundamentals/creating-helpful-content

**Yapılmalı:**
- [ ] People-first content (önce kullanıcı, sonra arama motoru)
- [ ] Orijinal değer kat (sadece ürün adı + spec listesi yetmez)
- [ ] Kullanım senaryoları, karşılaştırma, video, infografik
- [ ] Tarih/güncellik bilgisi

**Yapılmamalı:**
- [ ] "Arama motoru için" SEO doldurması
- [ ] Aynı şablonun yüzlerce ürüne kopyalanması
- [ ] Hangi sorunu çözdüğü belirsiz içerik

### 3.4 AI-Generated Content

**Resmi kaynak:** https://developers.google.com/search/docs/fundamentals/using-gen-ai-content

**Google politikası:** "AI kullanımı kendi başına spam değil, ama düşük kalite ve scaled abuse spam'dir."

**Yapılmalı:**
- [ ] AI ile üretilen içerik **insan editöryal review**'dan geçmeli
- [ ] Gerçeklik kontrolü (özellikle YMYL'de)
- [ ] Orijinal değer ekle (sadece rephrase değil)
- [ ] AI ile büyük hacim üretirken kalite eşiği düşmemeli

**Yapılmamalı:**
- [ ] "Günde 50-500 AI makalesi" tarzı scaled content abuse
- [ ] Hiçbir editöryal kontrolden geçmemiş AI metni
- [ ] AI'la üretilen yüzlerce benzer kategori açıklaması
- [ ] Author byline'da fake kişi/AI'ı insan gibi göstermek

**Raven Dental özel notları:**
- Ürün açıklamaları AI ile draft edilirse: ürün manageri **mutlaka** review etmeli
- Blog yazıları AI taslak + insan editör modelinde gitmeli
- Author bilgisi gerçek kişi olmalı
- **Mevcut durum:** ürün açıklamaları çoğunlukla manuel; AI kullanımı politikası yazılmalı

### 3.5 Duplicate / Thin Content

**Yapılmalı:**
- [ ] Her ürün için benzersiz açıklama (varyantlar canonical ile birleştirilebilir)
- [ ] Kategori açıklamasını anlamlı uzunlukta yaz
- [ ] Filter sonuç sayfalarını noindex et

**Yapılmamalı:**
- [ ] Üretici spec sheet'i kopyala-yapıştır
- [ ] 50 kelime altı kategori sayfaları
- [ ] Aynı içerik farklı URL'lerde (`?utm=...` versiyonları)

**Raven Dental özel notları:**
- Variant ürünler (örn. farklı boy aynalar) parent product + variant offer modeli ile yönetilmeli
- Filter URL'leri canonical + noindex
- **Mevcut durum:** filter parametre yönetimi review patch'inde

### 3.6 Keyword Cannibalization

**Yapılmalı:**
- [ ] Site genelinde keyword map çıkar (her hedef kelime tek sayfa)
- [ ] Çakışan sayfaları birleştir (301) veya farklı intent'e ayır

**Raven Dental özel notları:**
- "Diş aynası" hem kategori hem populer ürün → kategori ana, ürünler long-tail
- **Mevcut durum:** Search Console query analizi yapılmalı

---

## 4. Core Web Vitals & Performance

**Resmi kaynak:** https://developers.google.com/search/docs/appearance/core-web-vitals

### 4.1 Threshold'lar (2026)

- [ ] **LCP (Largest Contentful Paint):** < **2.5 saniye** (75th percentile)
- [ ] **INP (Interaction to Next Paint):** < **200 ms** (75th percentile) — FID 2024 Mart'ta retire oldu
- [ ] **CLS (Cumulative Layout Shift):** < **0.1** (75th percentile)

Threshold "geçti" sayılması için kullanıcıların **%75'i** "good" almalı (Google CrUX dataset, URL bazlı).

### 4.2 Yapılmalı

- [ ] LCP element preload (hero görsel `<link rel="preload" as="image">`)
- [ ] CSS critical inline, geri kalanı defer
- [ ] JavaScript code-splitting, gereksizleri defer/async
- [ ] Görseller `<img width height>` attribute (CLS önleme)
- [ ] Web font'lar `font-display: swap`
- [ ] CDN üzerinden statik asset servis et
- [ ] WebP/AVIF görsel formatı

### 4.3 Yapılmamalı

- [ ] Hero görsele `loading="lazy"` koymak (LCP'yi kaybeder)
- [ ] Reklam/widget alanlarını boyutsuz inject etmek (CLS yapar)
- [ ] Üçüncü parti script'leri render-blocking yüklemek

### 4.4 Mobile-First Indexing

**Resmi kaynak:** https://developers.google.com/search/docs/crawling-indexing/mobile/mobile-sites-mobile-first-indexing

- [ ] Mobil ve desktop **aynı içerik** sunulmalı
- [ ] Mobil viewport meta: `<meta name="viewport" content="width=device-width, initial-scale=1">`
- [ ] Mobile-Friendly test geçilmeli
- [ ] Hamburger menü içeriği SEO bakımından "gizli" sayılmaz

### 4.5 HTTPS

- [ ] **Zorunlu** — HTTP siteler ranking handikabı yer
- [ ] HSTS header eklenmeli
- [ ] Mixed content yasak

### 4.6 Tools & Targets

- [ ] PageSpeed Insights mobile ≥ 75
- [ ] Lighthouse Performance ≥ 80, SEO ≥ 95, Accessibility ≥ 90
- [ ] Search Console > Core Web Vitals raporu yeşil

**Raven Dental özel notları:**
- Journal3 bol JS yükler → critical path optimize edilmeli
- Ürün listeleme sayfasındaki çok sayıda görsel → lazy + WebP şart
- OpenCart cache (system cache + Journal3 cache + Object Cache) aktif olmalı
- **Mevcut durum:** docs/05-SEO-STATUS.md'de CWV ölçümleri kayıtlı; LCP iyileştirme patch'leri uygulandı

---

## 5. E-commerce Specific

### 5.1 Product Schema (Merchant Listing Eligible)

**Resmi kaynak:**
- https://developers.google.com/search/docs/appearance/structured-data/product
- https://developers.google.com/search/docs/appearance/structured-data/merchant-listing

**Required (rich results için):**
- [ ] `@type: Product`
- [ ] `name`
- [ ] `image` (yüksek çözünürlük)
- [ ] `offers` → `price`, `priceCurrency` (TRY), `availability`
- [ ] `brand`

**Strongly recommended:**
- [ ] `gtin` / `gtin13` / `gtin12` / `gtin8` — Google jenerik `gtin` öneriyor, format'ı otomatik algılar
- [ ] `mpn` — GTIN yoksa **zorunlu**, varsa birlikte kullan
- [ ] `sku`
- [ ] `description`
- [ ] `aggregateRating` + `review` (gerçek yorumlar)

**Availability değerleri (case-sensitive):**
- `https://schema.org/InStock`
- `https://schema.org/OutOfStock`
- `https://schema.org/PreOrder`
- `https://schema.org/BackOrder`
- `https://schema.org/Discontinued`

**Raven Dental özel notları:**
- Üretici olarak GTIN üretebilir / GS1 Türkiye üyeliği değerlendirilmeli
- MPN = ürün kodumuz (internal SKU farklı ise)
- brand: `{"@type": "Brand", "name": "Raven Dental"}`
- manufacturer ayrıca eklenebilir
- **Mevcut durum:** Product schema patch uygulandı; GTIN eksik bazı ürünlerde

### 5.2 Out of Stock Handling

**Google önerisi (2023 güncel):**
- Geçici stokta yokluk → sayfayı **kaldırma, noindex yapma**. `availability: OutOfStock` ile bırak.
- Kalıcı olarak ürün gitti → 301 redirect (kategoriye veya yeni ürüne) VEYA 410 Gone
- Çok sayıda OutOfStock ürün varsa kategori sayfasının kalitesi düşer → filtreleme önerilir

**Raven Dental özel notları:**
- B2B'de stoksuzluk yaygın değil ama "ön sipariş" / "üretime girdi" durumu PreOrder ile
- Discontinued ürünler için redirect map tutulmalı
- **Mevcut durum:** dokümante edilmemiş — politika yazılmalı

### 5.3 Category Page SEO

- [ ] Üst kısımda kısa kategori açıklaması (200-500 kelime, benzersiz)
- [ ] H1 = kategori adı
- [ ] Breadcrumb şart
- [ ] Filtre URL'leri canonical + noindex,follow
- [ ] Pagination kuralları (bölüm 1.6)
- [ ] Internal link ile alt kategoriler ve popüler ürünler

**Raven Dental özel notları:**
- Mevcut kategori açıklamaları çoğunlukla kısa → uzatma planı var
- **Mevcut durum:** patch'ler arası bir görev

### 5.4 Product Variant URLs

- [ ] Tek ürün altında varyantları offer ile gösterebilirsin (boy, renk vb.)
- [ ] Tamamen farklı SKU/içerik → ayrı ürün sayfası + canonical kendisine
- [ ] Varyant query string (`?color=red`) ise canonical ana ürüne

### 5.5 Shopping Ads / Merchant Center

**Resmi kaynak:** https://support.google.com/merchants/answer/7052112

- [ ] Feed'deki veri site'taki schema ile **tutarlı** olmalı
- [ ] Görsel min. 100x100, önerilen 500x500 (2026 deadline'lar geliyor)
- [ ] GTIN, brand, price, availability, link, image_link zorunlu
- [ ] Türkiye için TL fiyat + KDV durumu net

**Raven Dental özel notları:**
- B2B için Merchant Center kullanımı opsiyonel ama görünürlük için faydalı
- **Mevcut durum:** Merchant Center entegrasyonu yapılmamış

---

## 6. Local SEO (Türkiye)

**Resmi kaynak:** https://support.google.com/business/

### 6.1 Google Business Profile

- [ ] İşletme profili oluşturulup doğrulanmalı
- [ ] Ad, adres, telefon (NAP) **tutarlı**
- [ ] Kategori: "Tıbbi Malzeme Tedarikçisi" veya "Diş Hekimliği Malzemesi Üreticisi"
- [ ] Çalışma saatleri güncel
- [ ] Foto: tesis, ürün, sertifika

### 6.2 LocalBusiness Schema

- [ ] `@type: MedicalEquipmentSupplier` veya `Organization` + `LocalBusiness`
- [ ] address, telephone, openingHours, geo
- [ ] sameAs: sosyal medya, LinkedIn

### 6.3 NAP Consistency

- [ ] Tüm sitedeki adres/telefon **birebir aynı**
- [ ] Site, GBP, fatura, sosyal medya tutarlı

### 6.4 Türkiye Özel İpuçları

- [ ] `tr-TR` lang attribute HTML'de
- [ ] Türkçe karakter URL'lerde değil ama content'te kullan
- [ ] Yandex.Webmaster da eklenebilir
- [ ] KEP, MERSİS, vergi no footer'da görünür
- [ ] Mesafeli satış sözleşmesi, iade, gizlilik politikası linkleri

**Raven Dental özel notları:**
- Üretici → tesis adresi önemli E-E-A-T sinyali
- Türkiye'de "yerli üretim" + sertifika kombinasyonu zayıf rakipler arasında ayırt edici
- **Mevcut durum:** GBP durumu doğrulanmalı

---

## 7. Spam & Penalty Risks

**Resmi kaynak:** https://developers.google.com/search/docs/essentials/spam-policies

### 7.1 Hidden Text / Hidden Links

- [ ] Beyaz arka planda beyaz yazı, `display:none` ile keyword stuffing → manuel aksiyon
- [ ] CSS ile ekran dışına atılmış text

### 7.2 Keyword Stuffing

- [ ] Doğal olmayan yoğunlukta keyword tekrarı
- [ ] Anchor text spam (her link aynı keyword)

### 7.3 Cloaking

- [ ] Googlebot'a farklı, kullanıcıya farklı içerik göstermek → yasaktır
- [ ] User-agent kontrolü ile farklı içerik

### 7.4 Link Schemes

- [ ] Satın alınan link
- [ ] Reciprocal link spam ("link değişimi")
- [ ] PBN (private blog network)
- [ ] Sponsorlu link `rel="sponsored"`, UGC link `rel="ugc"`, güvenilmeyen `rel="nofollow"`

### 7.5 Sneaky Redirects

- [ ] Mobil kullanıcıyı farklı domain'e atmak
- [ ] User-agent'a göre yönlendirme

### 7.6 User-Generated Spam

- [ ] Yorum spam'i temizlenmeli
- [ ] CAPTCHA / moderasyon

### 7.7 AI-Generated Content Spam (2024+)

- [ ] Bölüm 3.4'e bakınız
- [ ] Scaled content abuse = yüksek hacim + düşük kalite → 2024 Mart Helpful Content & 2026 Mart Spam Update agresif cezalandırıyor
- [ ] SpamBrain AI'ı cloaking + AI farm tespitinde gelişmiş

### 7.8 Back Button Hijacking (2026 yeni)

**Resmi kaynak:** https://developers.google.com/search/blog/2026/04/back-button-hijacking

- [ ] Kullanıcının back button'una bastığında istenmeyen sayfa açma → spam policy ihlali

**Raven Dental özel notları:**
- Üretici olarak third-party marka isimlerini metinde kötüye kullanmak → SEO hem yasal risk
- "X marka muadili" tarzı içeriklerde dikkat
- **Mevcut durum:** content guideline yazılmalı

---

## 8. Reviews & Reputation

### 8.1 Review Schema Doğru Kullanımı

**Resmi kaynak:** https://developers.google.com/search/docs/appearance/structured-data/review-snippet

**Yapılmalı:**
- [ ] **Product** ve **AggregateRating** için review markup geçerli
- [ ] Gerçek müşteri yorumlarına bağlı
- [ ] author, datePublished, reviewRating, reviewBody alanları

**Yapılmamalı:**
- [ ] Self-serving review (LocalBusiness/Organization'a kendi sitende review markup) → 2019'dan beri Google rich results'ı **göstermiyor**
- [ ] Sahte yorum üretmek → manuel aksiyon riski
- [ ] Görünmeyen yorumları schema'ya koymak

### 8.2 Google Business Profile Review Policy (2026 güncel)

**Resmi kaynak:** https://support.google.com/business/answer/7400114

**2026 itibarıyla yasak:**
- [ ] Müşteriden personel adı söylemesini istemek
- [ ] Lokasyonda otururken review baskısı (Google GPS/IP ile tespit ediyor)
- [ ] Review kiosk / shared tablet
- [ ] Review gating (önce duygu ölçüp pozitifleri yönlendirme)
- [ ] İndirim/hediye karşılığı review

**Ceza:** Yorum kaldırma, profil kısıtlama, "fake review removed" uyarı banner'ı.

### 8.3 User-Generated Content

- [ ] Yorumlar moderasyondan geçmeli
- [ ] Spam yorum + link silinmeli
- [ ] Şikayet hızlı yanıtlanmalı

### 8.4 Q&A Schema

- [ ] Sadece gerçek soru-cevap için
- [ ] Pazarlama metnini Q&A formatına çevirme (FAQ schema cezalandırması 2023)

**Raven Dental özel notları:**
- B2B müşteri yorumu az ama değerli → kalite > miktar
- Müşteri = diş hekimi → uzman görüşü olarak E-E-A-T sinyali
- **Mevcut durum:** review modülü Journal3'te aktif; structured data entegrasyonu var

---

## 9. Monitoring & Tools

### 9.1 Google Search Console

**Kaynak:** https://search.google.com/search-console

- [ ] Property doğrulanmış (DNS veya HTML)
- [ ] Sitemap submit edilmiş
- [ ] Coverage / Pages raporu haftalık takip
- [ ] Core Web Vitals raporu
- [ ] Manual Actions / Security Issues — sıfır olmalı
- [ ] Performance raporu — query, CTR, position

### 9.2 URL Inspection Tool

- [ ] Yeni publish edilen önemli sayfalar için "Request Indexing"
- [ ] Render edilen HTML'in canonical, robots, structured data'sı doğru mu?

### 9.3 Mobile-Friendly Test

**Not:** Google standalone tool'u retire etti, Search Console içinde "Mobile Usability" var.

### 9.4 Rich Results Test

**Kaynak:** https://search.google.com/test/rich-results

- [ ] Tüm ürün sayfaları için pass
- [ ] Breadcrumb pass
- [ ] FAQ (varsa) pass

### 9.5 Lighthouse / PageSpeed Insights

**Kaynak:** https://pagespeed.web.dev/

- [ ] Mobile + Desktop ayrı ölçüm
- [ ] Field data (CrUX) yanı sıra lab data
- [ ] CI/CD'de Lighthouse CI entegre edilebilir

### 9.6 Schema Markup Validator

**Kaynak:** https://validator.schema.org/

- [ ] Schema.org spec'e uygunluk kontrol
- [ ] Rich Results Test'e ek olarak kullanılır

**Raven Dental özel notları:**
- Search Console + GA4 + Looker Studio dashboard kurulmalı
- Lighthouse CI Journal3 production cache problemini çözmek için staging'de
- **Mevcut durum:** GSC kurulu, sitemap submit edilmiş, weekly review yok — proseslenmeli

---

## 10. Raven Dental Special Notes

### 10.1 Dil & Pazar

- TR-only site → hreflang gereksiz
- HTML `<html lang="tr">` zorunlu
- Türkçe karakterler URL'de değil, content'te kullan
- Para birimi TRY, fiyat KDV durumu (KDV dahil/hariç) açık

### 10.2 Yarı-YMYL Pozisyonu

- B2B medikal alet = E-E-A-T çok önemli
- Sertifika ve üretici kimliği her ürün sayfasında görünür olmalı:
  - CE sertifika numarası
  - ISO 13485 belgesi
  - Üretim tesisi (Türkiye, şehir)
  - Lot/seri numarası gösterimi
- "Bu site tıbbi tavsiye vermez" tarzı disclaimer B2B'de gereksiz ama "kullanım için diş hekimliği lisansı gerektirir" bilgisi olabilir

### 10.3 Üretici Pozisyonu

- **Raven Dental üreticidir** → 3. parti marka isimleri schema/meta/title/H1'de **kullanılmamalı**
- "X marka muadili" tarzı içerikler hem yasal risk hem trademark spam
- brand alanında her zaman `"Raven Dental"`
- manufacturer alanında üretim tesisi bilgisi
- "Made in Türkiye" + sertifika kombinasyonu öne çıkarılmalı

### 10.4 OpenCart + Journal3 Platform Kısıtları

- OpenCart 3.0.3.8 SEO URL feature aktif olmalı
- Journal3'ün AJAX-heavy kategorisi → fallback HTML link şart
- Journal3 default schema bazen Google standart formatına uymuyor → manuel override
- Cache (Journal3 + OpenCart system + Object Cache) production'da açık
- vQmod/OCmod ile yapılan SEO modifikasyonları version-controlled olmalı

### 10.5 Referans Patch'ler

- `analysis/seo-patches/11-image-alts/` — image alt text optimizasyonu (aktif iş)
- `analysis/seo-patches/*` — uygulanan diğer patch'ler
- `docs/05-SEO-STATUS.md` — mevcut durum raporu
- `docs/15-CODE-REVIEW-SUMMARY.md` — code review bulguları

### 10.6 PR Checklist (Bu Doc'a Bağlı)

Her SEO ilişkili PR'da review'cı bu listeyi kontrol etmeli:

- [ ] Title/meta description bölüm 2.1-2.2'ye uygun
- [ ] H1 tek ve doğru
- [ ] Image alt'lar bölüm 2.4'e uygun
- [ ] Canonical self-reference
- [ ] Yeni schema eklendiyse Rich Results Test'ten geçti
- [ ] robots/noindex yanlışlıkla eklenmedi
- [ ] CWV regresyonu yok (Lighthouse check)
- [ ] Mobile responsive
- [ ] Internal link açıklayıcı anchor
- [ ] TR karakter URL'e kaçmadı
- [ ] 3. parti marka adı schema/title/meta'da yok
- [ ] Sertifika/E-E-A-T sinyalleri kayıp olmadı

---

## Versiyon Geçmişi

| Tarih | Değişiklik | Yazan |
|-------|-----------|-------|
| 2026-05-12 | İlk versiyon — 10 bölüm, Google 2026 kuralları | SEO/Tech |

## Bekleyen Güncellemeler

- [ ] 2026 Eylül itibarıyla Google AI Overviews için yeni schema sinyalleri (takip edilecek)
- [ ] Merchant Center 500x500 görsel deadline (2026)
- [ ] YMYL elections genişlemesi etkisi (medical alet için doğrudan etkili değil ama izlenecek)

## Resmi Kaynak Bağlantıları (Tek Liste)

- Search Central docs: https://developers.google.com/search/docs
- Spam policies: https://developers.google.com/search/docs/essentials/spam-policies
- Helpful content: https://developers.google.com/search/docs/fundamentals/creating-helpful-content
- AI-generated content: https://developers.google.com/search/docs/fundamentals/using-gen-ai-content
- Core Web Vitals: https://developers.google.com/search/docs/appearance/core-web-vitals
- Product schema: https://developers.google.com/search/docs/appearance/structured-data/product
- Merchant listing: https://developers.google.com/search/docs/appearance/structured-data/merchant-listing
- Sitemaps: https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap
- Canonical: https://developers.google.com/search/docs/crawling-indexing/canonicalization
- Robots meta: https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag
- Mobile-first: https://developers.google.com/search/docs/crawling-indexing/mobile/mobile-sites-mobile-first-indexing
- Hreflang: https://developers.google.com/search/docs/specialty/international/localized-versions
- Pagination: https://developers.google.com/search/docs/specialty/ecommerce/pagination-and-incremental-page-loading
- Review snippet: https://developers.google.com/search/docs/appearance/structured-data/review-snippet
- Title link: https://developers.google.com/search/docs/appearance/title-link
- Snippet: https://developers.google.com/search/docs/appearance/snippet
- Google Images: https://developers.google.com/search/docs/appearance/google-images
- Search Console: https://search.google.com/search-console
- PageSpeed Insights: https://pagespeed.web.dev/
- Rich Results Test: https://search.google.com/test/rich-results
- Schema Validator: https://validator.schema.org/
