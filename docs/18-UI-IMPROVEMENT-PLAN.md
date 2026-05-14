# 18 — UI/UX İyileştirme Planı (Anasayfa + Kategori + Ürün + Mobil)

> **Tarih:** 2026-05-12
> **Maintainer:** Raven Dental Tech / UX
> **Site:** https://ravendentalgroup.com (OpenCart 3.0.3.8 + Journal3 v3.1.12)
> **Yöntem:** Anasayfa HTML fetch (449 KB, 2896 satır) + Journal3 modül envanteri (production dump 24 modül) + 6 rakip raporu sentezi + Lighthouse baseline (docs/17) + Google SEO kuralları (docs/16)
> **Hedef:** B2B diş hekimliği e-ticaretinde profesyonel UX baseline kurmak; Lighthouse Perf 67 → 85+, A11y 71 → 95+, LCP 6.7s → <2.5s; rakiplerde olmayan AggregateRating / sözlük / video boşluklarını yakalamak

**Cross-references**
- [`docs/00-QUICK-CONTEXT.md`](./00-QUICK-CONTEXT.md) — son durum
- [`docs/16-GOOGLE-SEO-RULES.md`](./16-GOOGLE-SEO-RULES.md) — SEO uyum referansı
- [`docs/17-LIGHTHOUSE-BASELINE.md`](./17-LIGHTHOUSE-BASELINE.md) — performans/A11y baseline
- [`analysis/competitors/_summary.md`](../analysis/competitors/_summary.md) — 6 rakip karşılaştırma
- [`analysis/competitors/_action-plan.md`](../analysis/competitors/_action-plan.md) — 30/60/90 plan

---

## 0. CANLI JOURNAL3 REFERANSI (gelecekte buradan güncelleme yapacağız)

> **2026-05-13 itibarıyla:** Performans hedefleri (LCP/Lighthouse perf) DURDURULDU. Mevcut iyileştirme yeterli.
> Bu bölüm **devamlı güncellenir** — her UI değişikliği sonrası buraya not düşülür.

### 0.1 Anasayfa Modül Haritası (production'daki canlı modüller)

| Bölüm | Modül ID | Modül Tipi | Edit Notu |
|---|---|---|---|
| Header — Classic | 4 | header_desktop_classic | journal3 admin / header |
| Mobile Header | 5 | header_mobile_1 | Multi-variant 16/17/18 var |
| Main Menu Desktop | 3 | main_menu | 8 top-level menü item (Anasayfa, El Aletleri, Elektronik, Sarf, Raven Cerrahi, Toptan, Duyurular, İletişim) |
| Blog-Phone Desktop | 64 | main_menu | İkincil menü |
| Mobile Main Menu | 219 | main_menu | Mobile drawer |
| Hero Slider | 26 | master_slider | 2 slide (Adsız tasarım PNG) — WebP serve aktif |
| Banners Top Home | 259 | banners | 2 görsel (İmplantoloji + Muayene banner) |
| Info Blocks Trust | **86** | info_blocks | 4 kart: **CE/ISO 9001, Türk Üretimi, Hızlı Kargo, Klinik Fiyatlandırma** (✓ B2B updated 2026-05-13) |
| Categories Carousel | 37 | categories | Tab carousel + 18 kategori |
| Title "Yeni Ürünler" | 147 | title | "**Toplu Sipariş Avantajları**" — 5+/10+/25+ avantaj mesajı (✓ updated 2026-05-13) |
| Featured Products | 27 | products | "En Yeniler" carousel (17 ürün) |
| Button "Tüm Ürünleri Gör" | 263 | button | "All Products" CTA |
| Title Bottom-Row 1 | 163 | title | "İmplant uygulamaları" — kategori banner üst yazısı |
| Big Category Banners | 286 | banners | 4 kategori banner (886×886 width/height fix 2026-05-13) |
| Title Bottom-Row 2 | 162 | title | "İletişim" başlık |
| Button İletişim CTA | 287 | button | "0552 853 03 99" çağırma butonu |
| Side Products "Görüntülenen" | 39 | side_products | Footer'da yer alıyor (üste taşınması önerilen) |
| Filter | 36 | filter | Kategori sidebar — Stok Durumu/Markalar/Etiketler (✓ TR'leştirildi) |
| Icons Menu Social | 61 | icons_menu | FB + 2 IG link |
| Icons Menu Payment | 228 | icons_menu | Visa + Mastercard |
| Links Menu - Hakkımızda | **72** | links_menu | Footer: 8 kurumsal sayfa (✓ 4 yeni eklendi 2026-05-13) |
| Links Menu - Destek | 75 | links_menu | İletişim/Site Haritası/Markalar |
| Links Menu - Hesabım | 76 | links_menu | Hesabım/Bülten |
| Copyright Footer | 77 | links_menu | Copyright satırı |
| Bottom Menu | 266 | bottom_menu | Footer footer menü |
| Newsletter | 67 | newsletter | Bülten kayıt formu |
| Header Notice | 56 | header_notice | **DISABLED** (cookie/duyuru bant — eski içerik kaldırıldı) |
| Notification | 137 | notification | **DISABLED** (cookie consent — eski) |
| Account Menu | 126 | accordion_menu | Hesabım sidebar (✓ TR'leştirildi 2026-05-12) |
| Related Products | 253 | products | "Birlikte Alınanlar" ürün sayfası (✓ TR) |
| Testimonials | 256 | testimonials | **BOŞ** (demo content kaldırıldı, gerçek hekim sözleri bekliyor) |
| Top Menu | 2/13/14 | top_menu | Üst bant menü variants |

### 0.2 Tema Klasör Haritası (file paths)

| İçerik | Yol |
|---|---|
| Anasayfa twig | `catalog/view/theme/journal3/template/common/home.twig` (yok — modüller layout 0 üzerinde render) |
| Header twig | `catalog/view/theme/journal3/template/common/header.twig` |
| Footer twig | `catalog/view/theme/journal3/template/common/footer.twig` |
| Product card | `catalog/view/theme/journal3/template/journal3/product_card.twig` |
| Module twig'leri | `catalog/view/theme/journal3/template/journal3/{master_slider,products,banners,categories,info_blocks,...}.twig` |
| Settings JSON şemaları | `system/library/journal3/data/settings/module/<type>/general.json` |
| Inline CSS — slider | Journal3 generates per-module CSS at runtime |
| Custom CSS | `oc_journal3_setting WHERE setting_group='custom_code' AND setting_name='customCSS'` |
| Custom Header Code | `oc_journal3_setting ... 'customCodeHeader'` (canonical, OG, sameAs schema buraya) |
| Custom Footer Code | `oc_journal3_setting ... 'customCodeFooter'` (A11y JS + WhatsApp widget buraya) |

### 0.3 Rakiplerle Karşılaştırılma — Bizim Pozisyonumuz

**Bizim üstün olduklarımız (korunmalı):**
- ✅ TR-only odak (rakipler EN/AR pazarlama yapıyor)
- ✅ **Üretici pozisyonu** (rakiplerin çoğu distribütör — bizim modelimiz daha düşük marj + daha hızlı fiyat hareketi)
- ✅ ISO 9001 + CE belgesi + AISI tıbbi paslanmaz çelik standartları → Info-blocks 86'da öne çıkarıldı
- ✅ DİŞSİAD üyesi (legal trust)
- ✅ 4-blok Schema.org (Product + BreadcrumbList + Organization + WebSite)
- ✅ hreflang TR + x-default
- ✅ HSTS + secure cookies + Best Practices 100
- ✅ Sitemap hiyerarşi (1 anasayfa + 18 kategori + 345 ürün)
- ✅ Türkçe SEO URL'ler (slug'lar)
- ✅ 18/18 kategori 1450+ char açıklama (rakipler çoğunlukla thin)

**Rakiplerden eksiklerimiz (sıralı UI iş):**
- ❌ AggregateRating (6/6 rakipte yok — mavi okyanus, biz ilk olabiliriz)
- ❌ YouTube/Video (6/6 rakipte yok — mavi okyanus)
- ❌ Blog/içerik (5/6 rakipte var, biz boş)
- ❌ Native mobile app (4/6 rakipte var, biz yok — PWA ile başlanabilir)
- ❌ Sözlük (Dentalpiyasa /sozluk yutuyor)
- ❌ Klinik fiyat tablosu ürün sayfasında (B2B'de standart, bizde yok)
- ❌ Bulk add-to-cart (B2B müşterilerin çoğu beklediği, bizde yok)
- ❌ Q&A bölümü ürün sayfasında

### 0.4 Yapılan UI İyileştirmeleri (Bu Doc'a Eklendikçe Kayıt)

| Tarih | İş | Modül | Önce | Sonra | Rakip Ref. |
|---|---|---|---|---|---|
| 2026-05-12 | Footer sosyal medya temizliği | icons_menu 61 | 3 boş # link | FB + 2 IG canlı | Dentalpiyasa pattern |
| 2026-05-12 | WhatsApp widget | customCodeFooter | yoktu | Sağ-alt sabit FAB | Dismalzemeleri pattern |
| 2026-05-12 | Header notice + cookie disable | modules 56, 137 | aktif demo | hidden | — |
| 2026-05-12 | 18 kategori uzun açıklama | oc_category_description | thin | 1450+ char üretici pozisyonu | Davye thin (pozisyon avantajı) |
| 2026-05-12 | og:title meta_title-öncelikli | journal3/seo.php OCMOD | category 'name' (uppercase) | meta_title TR | — |
| 2026-05-12 | Site i18n cleanup (~150 string) | skin_settings + modules | EN karışık | TR temiz | — |
| 2026-05-12 | Alt text 10 görsel | banner modules | boş | TR açıklayıcı | — |
| 2026-05-12 | Banner alt text + slider | modules 26/98/201/259/286 | bazı boş | TR açıklayıcı | — |
| 2026-05-12 | Product 432 Kavo brand fix | oc_product_description | "Kavo Tip Airetor" | "Hava Türbini Aerator" | — |
| 2026-05-12 | A11y 71 → 96 | customCodeFooter JS | button/label/contrast eksik | aria-label, role, contrast | — |
| 2026-05-13 | Account sidebar TR | module 126 | EN labels | Hesabım/Adres/Sipariş TR | — |
| 2026-05-13 | Test product 455 KORUNUR | oc_product | — | Cihat kullanıyor | — |
| 2026-05-13 | Cloud Dashboard kaldır | journal3.php + dashboard.json | "Dashboard" menü | kaldırıldı (rollback edildi → restore) | — |
| 2026-05-13 | 8 kurumsal sayfa yaz | oc_information + footer 72 | 4 boş + 2 thin | 8 zenginleştirilmiş (Hakkımızda kurumsal, KVKK/Mesafeli/İade/Çerez yeni) | TR e-ticaret yasal |
| 2026-05-13 | SARF + RAVEN menü href fix | main_menu 3 | yanlış cat 109 | cat 70/71 | — |
| 2026-05-13 | Banner 286 width/height (CLS) | banners 286 | boş | 886×886 | — |
| 2026-05-13 | Title 147 "%30 indirim" | title 147 | yasal risk metni | "Toplu Sipariş Avantajları" 5+/10+/25+ | B2B niş |
| 2026-05-13 | Info-blocks 86 → B2B trust | info_blocks 86 | Güvenli/iade/taksit/kargo (B2C) | **CE/ISO + Türk + Hızlı + Klinik** (B2B) | Davye'nin DİŞSİAD vurgusu rakip |
| 2026-05-13 | **Raven Dental Fark** 6 kart (yeni modül) — **local only** | info_blocks **289** (yeni) + layout 1 bottom.row[1] | Title 162 "İletişim" + Button 287 telefon CTA | 6 kart 3×2 grid: Üretici / Klinik fiyat / 2 Yıl Garanti / 24h Kargo / ISO+CE / DİŞSİAD. Distinct icomoon icons. | Davye'nin değer önerisi sloganları → B2B-spesifik 6 kanıt. Prod deploy bekliyor (kullanıcı onayı) |
| 2026-05-13 | **Header Notice 56 aktive** — **local only** | header_notice 56 | DISABLED (demo content) | "Klinik fiyatı: 10+ alet siparişinde özel toptan teklif \| ☎ 0552 853 03 99 \| Teklif İste" — sayfa üstü B2B banner | Dismalzemeleri'nin top notice pattern'i. Prod deploy bekliyor |
| 2026-05-13 | **side_products 39 footer→top** — **local only** | layout_id 1, `top` position'a yeni satır | Sadece footer grid'inde | Yeni `top.row[4]` (products satırından sonra). Yeni ziyaretçi için boş (CSS yüklü, DOM yok), dönen ziyaretçi "Son Görüntülenen" görür | Davye/Dentalpiyasa'da returning visitor experience. Prod deploy bekliyor |
| 2026-05-13 | **Categories 37 → Branş Kartları** — **local only** | categories 37 + module_name | 4-tab carousel (El Aletleri/Sarf/Raven Cerrahi/Elektronik 5 alt-kat her tab) | Tek grid 10 El Aletleri alt-kategorisi (Çekim, Cerrahi, Diagnostik, Endodonti, İmplantoloji, İşlem, Ortodonti, Periodonti, Protez, Restorasyon) — 5 col desktop / 3 tablet / 2 phone. Tabs kaldırıldı. Title "El Aletleri — 10 Branş" | B2B müşterinin ilgilendiği "branş ile ürün ara" UX. Davye'nin branş kategorisi yok. Prod deploy bekliyor |
| 2026-05-13 | **Copyright 77 → 3 satır legal footer** — **local only** | links_menu 77 | Tek satır "Copyright © 2021" | 3 satır: (1) © 2026 Raven Dental — Tüm hakları saklıdır (2) Şirket ünvanı + Adres + Telefon + Email (3) VKN/MERSIS/KEP/Tic. Sicil placeholder ("güncelleniyor" — user dolduracak) | KVKK 6698 + e-ticaret 6502 yasal gereklilik. Prod deploy bekliyor (placeholder data user'dan bekleniyor) |

### 0.5 Sıradaki UI İşleri (Onay Beklenenler)

- [ ] **Main menü reorganization** — şu an 8 top-level + dropdown sub-items mevcut. Mega-menu grid (büyük dropdown panel, kategori grid + featured product) önerilir.
- [x] **Bottom-row 2** ("İletişim" Title 162 + Button 287) → **"Raven Dental Fark"** 6-madde info_blocks 289 (Üretici, Klinik fiyat, 2 yıl garanti, 24h kargo, ISO/CE, DİŞSİAD) — *local only, prod deploy bekliyor*
- [x] **Side products 39** footer'dan home top'a yeni satır — *local only, prod deploy bekliyor*
- [x] **Header notice 56** aktive — *local only, prod deploy bekliyor*
- [x] **Branş card grid** — Categories 37: 4-tab carousel → 10 branş tek-grid (5×2 desktop) — *local only, prod deploy bekliyor*
- [ ] **Footer trust band** — DİŞSİAD logo + CE/ISO rozet + Tendata + "Türk Üretimi" şerit. Logo asset'leri kullanıcıdan beklenecek.
- [ ] **Testimonials 256** — gerçek hekim sözleri ile aktive et (5 demo EN testimonial var; user real quotes ile yer değişmeli)
- [ ] **Mega menü** EL ALETLERİ alt menü grid'i (10 branş 2 kolon)
- [x] **Footer KEP/VKN/MERSIS satırı** — links_menu 77'ye 3 satır eklendi (Copyright + Adres satırı + Tax IDs placeholder) — *local only, kullanıcı VKN/MERSIS/KEP gerçek değerlerini verecek*

### 0.6 Kullanılmayan / Pasif Modüller (kullanılabilir hammadde)

| Modül Tipi | Açıklama | Kullanım Önerisi |
|---|---|---|
| `blog_posts` | Blog yazısı carousel/list | Faz 2 — blog 5 makale yayını sonrası anasayfada "Son Yazılar" |
| `form` | Custom form modülü | "Teklif İste" formu (>500 adet B2B) |
| `manufacturers` | Marka grid | — (biz tek marka Raven, gerek yok) |
| `gallery` | Lightbox image gallery | Ürün sayfasında çoklu görsel |
| `flyout_menu` | Mega-menu flyout | Ana menü reorganization için |
| `accordion_menu` | Mobile/sidebar accordion | Faz 2 — kategori filter UX |
| `countdown` | Geri sayım timer | Kampanya bitiş — kullanılırsa |
| `popup` | Modal popup | Welcome popup, newsletter signup |

---

## 1. Yönetici Özeti (TL;DR)

Anasayfa HTML'i, production DB dump'ı ve 6 rakip raporu birlikte okunduğunda **10 stratejik karar** çıkıyor:

1. **Hero görseli LCP'yi öldürüyor.** Master Slider modülü 26 iki PNG slide (960×450 / 1920×900) sunuyor; `loading="lazy"` yok, preload yok, WebP yok, `<source>` responsive yok. Mobile LCP 6.7s'nin tek başına ~3-4s'sini bu element çekiyor. **Faz 1 must-fix.**
2. **17 ürün kartının 17'sinde sıfır yıldız.** "no-rating" CSS class'ı, rich snippet hakkını boşa harcıyor — 6 rakibin de hiçbirinde AggregateRating yok, bu **mavi okyanus**. Önce review akışını WhatsApp/email ile başlatıp 60 günde 100+ yorum biriktirmek gerekiyor.
3. **Trust signal eksikliği kritik.** Anasayfada CE, ISO 13485, DİŞSİAD, Tendata, "Türk üretimi" rozet **yok**. İnfo-blocks modülü (id 86) "Güvenli Alışveriş / %100 kolay iade / Taksitli / Ücretsiz kargo 500 TL üzeri" gösteriyor ama bunlar B2C jargonu; B2B hekim için CE+ISO+klinik fiyat+lot/seri+hızlı kargo daha önemli.
4. **Ana menü 22 madde** — bilişsel yük yüksek. SARF ve RAVEN CERRAHİ ALETLER linksiz dead-end (`<a>` href yok). El Aletleri altında 10 alt-branş tek dropdown'a sıkıştırılmış; mega-menü görsel grid'e dönüştürülmeli.
5. **Footer 2 ödeme ikonu** (Visa + Mastercard) — sadeleştirme doğru ama trust signal'i taşıyor olmalı. DİŞSİAD logosu + "Türk üretimi" rozeti + sertifika küçük thumb'ları footer'a inmiyor.
6. **Kategori sayfası filtre** mevcut Journal3 filter modülü (id 36) "Stok Durumu" + "Markalar" verse de; B2B'ye gereken **Sertifika (CE/ISO), Steril paket, Material (AISI 304/420), Otoklavlanabilir, Lot/seri** filtreleri eksik.
7. **Ürün sayfasında "klinik / toplu fiyat" yok.** Tek SATIN AL + SORU SOR butonu var; **B2B alıcı için "5+ / 10+ / 25+ adet"** kademeli fiyat tablosu, "Teklif İste" formu, Q&A bölümü olmalı.
8. **Mobil header** sadece hamburger + logo + cart; **sticky bottom-bar yok** (sepete ekle + WhatsApp). WhatsApp FAB sağ-altta zaten var ama mobil thumb-zone'a göre konum doğrulanmalı.
9. **Blog menü item'ı header'da var** (`/index.php?route=journal3/blog`) ama anasayfada hiç blog kart'ı görünmüyor — içerik ekosistemi tamamen boş. İlk 5 makale yayınlanır yayınlanmaz anasayfaya "Son Yazılar" carousel'i eklenmeli (Journal3'ün blog_posts modülü mevcut, sadece aktif edilmemiş).
10. **PWA altyapısı yok.** Anasayfa HTML'de manifest link yok, theme-color yok, service-worker yok. Hafta 4'te 1-2 günlük iş ile "Ana Ekrana Ekle" prompt'u + offline cache eklenebilir; 4/6 rakipte native app var.

### En Acil 5 Hamle (Bu Hafta — ~6 saat iş)

| # | İş | Süre | Tahmini Etki |
|---|---|---|---|
| 1 | Hero görsel WebP + responsive `<picture>` + preload | 1 saat | LCP −1 ila −2s |
| 2 | Info-blocks içeriğini B2B-vari yapmak: CE/ISO badge + "Türk üretimi" + "Klinik fiyat" + "Hızlı kargo" + DİŞSİAD logosu | 1.5 saat | Trust + dönüşüm |
| 3 | Ana menü 22 → 8 madde + mega-menü grid (10 branş + 4 elektronik alt) | 2 saat | Bilişsel yük −60% |
| 4 | Footer'a trust band ekle (DİŞSİAD + CE + ISO + KEP + VKN + İTO + Tendata) | 1 saat | E-E-A-T / B2B trust |
| 5 | Side products (mod 39) "Bestseller" + "Toplu sipariş öne çıkanlar" tab ekle | 30 dk | İçerik derinlik |

---

## 2. Journal3 Modül Envanteri

### 2.1 Production'da Aktif Modüller (24 adet — `oc_journal3_module` tablosu)

| ID | Tür | İsim | Status | Nerede Kullanılıyor |
|---|---|---|---|---|
| 3 | main_menu | Main Menu - Desktop | ✅ | Desktop header (22 madde) |
| 4 | header_desktop_classic | Classic | ✅ | Aktif tema |
| 5 | header_mobile_1 | Mobile 1 | ✅ | Mobil aktif |
| 16/17/18 | header_mobile_2/3/4 | — | ✅ | Yedek (kullanılmıyor) |
| 26 | master_slider | Slider Top Home | ✅ | **Anasayfa hero** (2 slide) |
| 36 | filter | Filter | ✅ | Kategori sol-sütun |
| 56 | header_notice | Header Notice | ✅ | Boş (script header'da var) |
| 61 | icons_menu | Social Icons | ✅ | Footer sosyal (FB+IG×2) |
| 119 | catalog | Catalog Categories | ✅ | Search dropdown'da 4 üst kategori |
| 137 | notification | Notification Module | ✅ | Toast bildirim altyapı |
| 228 | icons_menu | Payments - Icons Menu | ✅ | Footer ödeme (Visa+MC) |
| 253 | products | Related - Also Bought | ✅ | Ürün sayfası "İlgili Ürünler" |
| 256 | testimonials | Testimonials | ✅ | Aktif ama anasayfada görünmüyor (demo silindi) |
| 266 | bottom_menu | Bottom Menu | ✅ | Footer alt çubuk |
| 267/268/269 | header_desktop_mega/compact/slim | — | ✅ | Yedek tema (aktif değil) |
| 98 | banners | Fashion Banner | ✅ | Anasayfada kullanılmıyor |
| 158 | banners | Deals Banner | ✅ | Anasayfada kullanılmıyor |
| 201 | banners | Specials Banner | ✅ | Anasayfada kullanılmıyor |
| 259 | banners | Banners Top Home | ✅ | **Anasayfa slider sağı (2 banner)** |
| 286 | banners | New Banners | ✅ | **Bottom (4 kategori card)** |

### 2.2 Anasayfa HTML'inde Render Olan Modüller (Layout 1)

Anasayfa fetch'inden DOM'da gerçek görülen modüller (production dump'a göre — `oc_module` ile birleşik):

| Modül ID | Tip | Anasayfa Konumu | Görev |
|---|---|---|---|
| 26 | master_slider | top-row 1 sol | Hero slider (2 PNG slide, başlık layer'ı boş!) |
| 259 | banners | top-row 1 sağ | 2 kategori banner (İmplantoloji, Muayene) |
| 86 | info_blocks | top-row 2 | 4 kart: Güvenli Alışveriş / İade / Taksitli / Kargo |
| 147 | title-module | top-row 3 sol | "İlk alışverişe özel %30 indirim!" başlığı |
| 37 | categories | top-row 3 sağ | Tab'lı kategori carousel (4 üst kategori × N alt) |
| 27 | products | top-row 4 sol | "En Yeniler" tab — 17 ürün carousel |
| 263 | button | top-row 4 sağ | "Tüm Ürünleri Gör" CTA |
| 163 | title-module | bottom-row 1 sol | "En Yeni Kategoriler" başlığı |
| 286 | banners | bottom-row 1 sağ | 4 ana kategori büyük banner (886×886) |
| 162 | title-module | bottom-row 2 sol | "İletişim" başlığı |
| 287 | button | bottom-row 2 sağ | "İletişim" CTA |
| 39 | side_products | footer-row 1 | "En Çok Görüntülenen" tab carousel |
| 61 | icons_menu | footer-row 2 col-3 | Sosyal medya (FB+IG×2) |
| 72 | links_menu | footer-row 3 col-1 | "Hakkımızda" (4 link) |
| 75 | links_menu | footer-row 3 col-2 | "Destek" (3 link) |
| 76 | links_menu | footer-row 3 col-3 | "Hesabım" (2 link) |
| 67 | newsletter | footer-row 3 col-4 | Bülten formu |
| 77 | links_menu | footer-row 4 col-1 | "Copyright © 2021" |
| 228 | icons_menu | footer-row 4 col-2 | Ödeme ikonları (Visa+MC) |

### 2.3 Journal3'te Desteklenen Tüm Modül Tipleri (Source: `system/library/journal3/data/settings/module/`)

| Modül Tipi | Ne Yapar | Anasayfada Kullanılabilir mi? | Mevcut Durum |
|---|---|---|---|
| **master_slider** | Hero slider (layer-based, hızlı, MS animasyon) | Evet — hero | ✅ 26 aktif |
| **fullscreen_slider** | Full-viewport hero | Evet — alternatif | ❌ Kullanılmıyor |
| **layer_slider** | Layer-based animasyon (master_slider'ın hafif versiyonu) | Evet | ❌ Kullanılmıyor |
| **slider** | Basit ürün/banner slider | Evet | ❌ Kullanılmıyor |
| **banners** | Statik banner grid | Evet | ✅ 5 adet (98,158,201,259,286) |
| **info_blocks** | İkonlu trust/feature kartları | Evet | ✅ 86 |
| **products** | Ürün carousel/grid (filter + tab) | Evet | ✅ 27 (En Yeniler) |
| **side_products** | Mini ürün listesi (popüler/yeni/satılan) | Evet — sidebar veya bottom | ✅ 39 |
| **product_blocks** | Daha zengin ürün widget | Evet | ❌ Kullanılmıyor |
| **categories** | Kategori grid/carousel/tab | Evet — branş erişimi | ✅ 37 (tab'lı) |
| **catalog** | Hiyerarşik kategori ağacı | Sidebar | ✅ 119 |
| **manufacturers** | Marka logoları carousel | Evet | ❌ Kullanılmıyor (Raven mono-brand) |
| **gallery** | Görsel galeri | Evet — atölye foto | ❌ Kullanılmıyor |
| **testimonials** | Müşteri yorum carousel | Evet — sosyal kanıt | ⚠️ 256 aktif ama anasayfada görünmüyor |
| **blog_posts** | Blog yazı carousel | Evet — son yazılar | ❌ Kullanılmıyor (içerik yok) |
| **blog_categories** | Blog kategorileri | Blog sayfası | ❌ Kullanılmıyor |
| **blog_search** | Blog arama | Blog sayfası | ❌ Kullanılmıyor |
| **blog_side_posts** | Side blog | Sidebar | ❌ Kullanılmıyor |
| **blog_tags** | Tag cloud | Blog | ❌ Kullanılmıyor |
| **blog_comments** | Yorum widget | Blog | ❌ Kullanılmıyor |
| **newsletter** | Bülten formu | Evet — footer | ✅ 67 |
| **countdown** | Geri sayım (kampanya) | Evet | ❌ Kullanılmıyor |
| **popup** | Popup banner | Açılışta | ❌ Kullanılmıyor |
| **notification** | Toast bildirim | Pasif altyapı | ✅ 137 |
| **header_notice** | Üst sabit bildirim çubuğu | Üst global | ✅ 56 (boş) |
| **layout_notice** | Layout-içi bildirim | Sayfa-spesifik | ❌ Kullanılmıyor |
| **main_menu** | Ana navigasyon | Header | ✅ 3 (22 madde, fazla!) |
| **top_menu** | Ek üst menü | Header | ✅ 2 (anasayfa/hakkımızda/iletişim) |
| **footer_menu / bottom_menu** | Alt menü | Footer | ✅ 266 |
| **links_menu** | Link listesi | Sidebar/footer | ✅ 72/75/76/77 (4 adet) |
| **icons_menu** | İkonlu menü (ödeme/sosyal) | Footer/header | ✅ 61/228 |
| **flyout_menu** | Hover'da açılan büyük menü | Header alternatif | ❌ Kullanılmıyor |
| **side_menu** | Yan menü | Sidebar | ❌ Kullanılmıyor |
| **accordion_menu** | Mobil dostu akordeon | Mobile menu | ❌ Kullanılmıyor |
| **header_desktop_*** | 4 header varyantı | Tek aktif olmalı | ✅ Classic (4) aktif |
| **header_mobile_*** | 4 mobil header varyantı | Tek aktif olmalı | ✅ Mobile 1 (5) aktif |
| **filter** | Kategori filter UI | Kategori sol-sütun | ✅ 36 (sadece stok + marka) |
| **title** | Başlık + alt başlık modülü | Sayfa içi | ✅ 147/162/163 |
| **button** | CTA buton modülü | Sayfa içi | ✅ 263/287 |
| **form** | Kullanıcı form (teklif iste vb.) | Sayfa içi | ❌ Kullanılmıyor — B2B'de kritik |
| **product_label** | Ürün rozetleri (Yeni/İndirim) | Ürün grid | ✅ Otomatik |
| **product_tabs** | Ürün sayfası tab'ları (Açıklama/Spec/Review) | Ürün sayfası | ✅ Tema seviyesi |
| **product_extra_button / product_exclude_button** | Ekstra ürün buton (Soru Sor, Teklif İste) | Ürün grid + sayfa | ⚠️ Kısmen (SATIN AL + SORU SOR var) |
| **grid / row / column** | Layout primitive | Sayfa düzeni | ✅ Sistem |
| **blocks** | Custom HTML container | Çok yönlü | ❌ Az kullanılıyor |

**Önemli boşluk:** **blog_posts, form, manufacturers (DİŞSİAD/CE rozet için), gallery, countdown, popup, flyout_menu, accordion_menu** — hepsi B2B UX için faydalı olabilir, hiçbiri aktif değil.

---

## 3. Mevcut Anasayfa Bölüm-Bölüm Analizi

### 3.1 Header (Classic, Layout 1)

**Yapı:**
- **Top-bar (top-menu-2):** ANASAYFA · HAKKIMIZDA · İLETİŞİM (3 link)
- **Third-menu (top-menu-14):** TESLİMAT
- **Mid-bar:** Logo (916×332 PNG!) + Search dropdown (Tümü/El Aletleri/Elektronik/Raven/Sarf) + GİRİŞ/KAYIT/FAVORİLER + Sepet
- **Main-menu (main-menu-3):** ANASAYFA · EL ALETLERİ ▾ (10 alt) · ELEKTRONİK ▾ (4 alt) · SARF · RAVEN CERRAHİ ALETLER · TOPTAN ALIŞVERİŞ · DUYURULAR · İLETİŞİM (22 madde toplam)
- **Main-menu-2 (main-menu-64):** 05528530399 (tel) · Blog
- **Mobile:** ayrı header (mobile-1) — hamburger + logo + search/cart minik

**Sorunlar:**
| # | Sorun | Etki | Çözüm |
|---|---|---|---|
| H1 | Logo PNG 916×332, gerçek display ~200×60 | LCP +500ms, gereksiz 80 KB | WebP 400×140 + responsive `<picture>` |
| H2 | 22 menü maddesi, 10'u tek dropdown'da boğulmuş | Bilişsel yük + tap target | Mega-menü grid (4 ana + 14 alt) — flyout_menu modülü ile |
| H3 | "SARF" ve "RAVEN CERRAHİ ALETLER" `<a>` href'siz, click ölü | Crawl + UX kırık | href ekle: `/sarf` ve `/raven-cerrahi-aletler` slug oluştur |
| H4 | Top-bar'da 3 link (anasayfa/hakkımızda/iletişim) tekrar — main menüde de var | Gereksiz | Top-bar'ı sade tut: sadece kargo ücretsiz eşik + Tel + Dil |
| H5 | Search dropdown'da sadece 4 üst kategori | Spesifik branş yok | "El Aletleri > Endodonti / Cerrahi / İmplant" hiyerarşi |
| H6 | Telefon `tel:+905528530399` main-menu-64'te (sağda boğuk) | Tıklanabilirlik düşük | Header top-bar'a taşı, "0552 853 03 99 — Hemen Ara" CTA |
| H7 | "Hesabım" dropdown yok (GİRİŞ/KAYIT static) | Kayıtlı kullanıcı UX zayıf | Login sonrası "Hesabım ▾ → Siparişler / Adres / Çıkış" |
| H8 | "Sepetiniz boş" string anlık DOM'da — JS load'a kadar görünür | CLS riski + UX | `<noscript>` fallback + skeleton |

### 3.2 Hero (Master Slider — Modül 26)

**Mevcut:**
- 2 slide PNG: "Adsız tasarım (2).png" + "Adsız tasarım.png" (960×450 / 1920×900)
- Alt text Türkçe (✅ "Raven Dental — Profesyonel Diş Hekimliği Aletleri")
- Layer text'ler boş (`<div class="ms-layer"></div>`)
- Autoplay açık, 2.5s delay, fade-wave geçiş
- **CTA yok, başlık yok, alt yazı yok** — sadece görsel

**Sorunlar:**
| # | Sorun | Etki | Çözüm |
|---|---|---|---|
| HE1 | LCP elementi, lazy/preload yok | LCP 6.7s | `<link rel="preload" as="image" imagesrcset="...">` + `<picture>` ile WebP |
| HE2 | PNG kullanımı | Boyut 2-4× WebP | WebP'ye çevir (60-70% boyut tasarrufu) |
| HE3 | Slide içinde başlık + alt yazı + CTA yok | "Üretici mi distribütör mü?" karmaşası | Her slide'a hero text + CTA layer ekle |
| HE4 | İçerik genel ("tasarım") — branş veya value-prop yok | Bounce oranı | Slide 1: "Türk Üretimi Cerrahi Aletler — Klinik Fiyatı" + "Toptan Sipariş" CTA. Slide 2: "20+ Yıl Üretici Deneyimi — CE / ISO 13485" + "Sertifikalı Aletleri Görün" CTA |
| HE5 | Master Slider JS lib (`masterslider.js`) render-blocking | TBT artırıyor | Lazy-init veya statik fallback (ilk slide HTML, JS yüklendikten sonra animasyon başla) |

**Önerilen yeni hero blok (ASCII):**

```
+-----------------------------------------------------------+
|  [bg: WebP 1920×600, optimize edilmiş, fallback JPEG]    |
|                                                           |
|     TÜRK ÜRETİMİ CERRAHİ ALETLER                          |
|     Klinik fiyatı — CE / ISO 13485 sertifikalı           |
|     ────────────                                          |
|     [Üretici Fiyatı Al]  [WhatsApp Sipariş]              |
|                                                           |
|                                       Slide 1/3   ● ○ ○  |
+-----------------------------------------------------------+
| trust band: [DİŞSİAD] [CE] [ISO 13485] [Türk Üretimi]    |
+-----------------------------------------------------------+
```

### 3.3 Top-Row 1 Sağı — Banners 259 (2 Kategori Banner)

**Mevcut:**
- 320×210 banner × 2 → İmplantoloji + Muayene linki
- WebP fallback yok, alt text ✅ Türkçe

**Sorunlar:**
- Sadece 2 kategori spotlight — 4 ana kategori varken (El Aletleri, Elektronik, Sarf, Raven Cerrahi) seçim tutarsız
- 320×210 mobile'da büyüyor (responsive değil), desktop'ta küçük

**Öneri:** İmplantoloji + Muayene yerine **"Klinik Açılış Paketi"** (B2B'ye uygun) + **"Raven Cerrahi Set"** (private label vurgu). Veya direkt hero'ya entegre et, bu sütunu sil.

### 3.4 Top-Row 2 — Info Blocks 86 (4 Trust Kartı)

**Mevcut:**
- Güvenli Alışveriş / %100 kolay iade / Taksitli Alışveriş / Ücretsiz kargo (500 TL üzeri)
- İkon yok (sadece text title + boş text)
- Alt yazıları boş, tıklanabilir değil

**Sorunlar:**
- Mesajlar **B2C jargonu** ("Taksitli" → B2B hekim için fatura/açık hesap daha kritik)
- B2B müşterinin değer önermesi yok: CE / ISO / Türk üretimi / hızlı kargo süresi (kaç gün?)
- İkon yok — visual hierarchy yok

**Önerilen yeni 4 kart:**

| Kart | Title | Subtitle | İkon |
|---|---|---|---|
| 1 | CE / ISO 13485 Sertifikalı | Tıbbi Cihaz Yönetmeliğine uyumlu | shield-check |
| 2 | Türk Üretimi | Doğrudan üreticiden klinik fiyat | flag-tr |
| 3 | Hızlı Kargo | Türkiye geneli 1-3 iş günü | truck-fast |
| 4 | Klinik Fiyatlandırma | 5+, 10+, 25+ adet kademeli | tag-percent |

Aynı modül (info_blocks 86) — sadece içerik güncellenecek + ikon class atanacak.

### 3.5 Top-Row 3 — Title 147 + Categories 37 (Branş Tab Carousel)

**Mevcut:**
- Title: "İlk alışverişe özel %30 indirim!" (subtitle: "iletişime geçin")
- 4 tab: El Aletleri / Elektronik / Sarf / Raven Cerrahi
- Her tab'ta swiper carousel (5 kategori per row desktop)
- Alt yazılar otomatik kesilmiş ("...")

**Sorunlar:**
| # | Sorun | Etki | Çözüm |
|---|---|---|---|
| C1 | "%30 indirim" claim — yasal risk, gerçek mi? | Trust + tüketici yasası | "Toplu sipariş avantajları için iletişime geçin" |
| C2 | Tab UX'i alt kategoriler için fena değil ama **branş** (Endodonti/Cerrahi/...) görselliği eksik | Hekim "Endodonti" altında ne var diye merak ederken upper-funnel yardımcı olmuyor | Tab değil **branş kart grid** (9 branş: Endodonti, Cerrahi, İmplantoloji, Ortodonti, Periodonti, Çekim, Restorasyon, Protez, Diagnostik) — her kart 1 ikon + 1 görsel + 1 cümle açıklama |
| C3 | Alt yazılar "..." ile kesik (database'de uzun) | UX kırık | Manuel kısa açıklama (40-60 char) gir |
| C4 | Swiper navigation butonları küçük (target-size A11y) | A11y skoru | min 44×44 |

**Önerilen yeni "Branş Hızlı Erişim" bloğu (ASCII):**

```
                   BRANŞA GÖRE ALET SETLERİ
+---------+ +---------+ +---------+ +---------+
| [icon]  | | [icon]  | | [icon]  | | [icon]  |
| ENDO    | | CERRAHİ | | İMPLANT | | ORTODONTİ|
| 45 ürün | | 62 ürün | | 38 ürün | | 27 ürün |
+---------+ +---------+ +---------+ +---------+
+---------+ +---------+ +---------+ +---------+
| PERIO   | | ÇEKİM   | | PROTEZ  | | DIAG    |
| 18 ürün | | 33 ürün | | 22 ürün | | 51 ürün |
+---------+ +---------+ +---------+ +---------+
                           +---------+
                           | RESTOR  |
                           | 29 ürün |
                           +---------+
```

### 3.6 Top-Row 4 — Products 27 "En Yeniler" (17 Ürün Carousel)

**Mevcut:**
- Tab "En Yeniler" (tek tab)
- 17 ürün swiper carousel, mobile 1 sütun / desktop 4 sütun
- Her kart: görsel + "Yeni" rozet + ad + model + boş açıklama + 5 boş yıldız + SATIN AL + SORU SOR butonu
- "Quick view" tooltip aktif
- "Karşılaştır" + "Alışveriş listesi" butonları var

**Sorunlar:**
| # | Sorun | Etki | Çözüm |
|---|---|---|---|
| P1 | 17 ürünün **17'sinde** 0 yıldız (no-rating CSS) | AggregateRating SERP boş, CTR düşük | Review akışı başlat: WhatsApp sipariş sonrası "Yorumlayın → %5 indirim" |
| P2 | Fiyat görünmüyor (login-wall mı?) | B2B'de doğru olabilir ama klinik müşteri 2× tıklamaya zorlanıyor | Fiyat görünür + "Hekim girişi ile %X klinik indirim" rozet ekle |
| P3 | "Test" isimli placeholder ürün hâlâ canlıda (id=455) | Profesyonel imaj | Admin'den temizle |
| P4 | Tek tab "En Yeniler" — "Çok Satanlar / İndirimli / Bestseller" yok | İçerik derinlik | Products modülü tab'ları (sale/featured/special) aktive |
| P5 | "SATIN AL" + "SORU SOR" alt-alta, mobile'da kart yüksekliği fazla | UX | Sticky CTA: hover'da SATIN AL üstte, "SORU SOR" alt micro |
| P6 | Ürün açıklaması "..." kesik | UX | 80-100 char özet alanı admin'de doldur |

### 3.7 Top-Row 4 Sağı — Button 263 "Tüm Ürünleri Gör"

**Mevcut:** "Tüm Ürünleri Gör" CTA → `/index.php?route=product/catalog` (eski URL)

**Sorun:** Pretty URL kullan: `/tum-urunler` veya `/katalog`. Aynı zamanda **canonical SEO URL** kullanılmalı (`route=product/catalog` direct değil).

### 3.8 Bottom-Row 1 — Title 163 + Banners 286 (4 Kategori Büyük Banner)

**Mevcut:**
- Title: "En Yeni Kategoriler"
- 4 banner 886×886 (sabit boy): İmplantoloji / Cerrahi / Diagnostik / Çekim
- Width/height HTML attribute boş — CLS riski!

**Sorunlar:**
| # | Sorun | Çözüm |
|---|---|---|
| B1 | width="" height="" boş → layout shift | width="886" height="886" şart |
| B2 | 886×886 PNG/JPEG WebP değil | WebP çevir |
| B3 | "En Yeni Kategoriler" — kategoriler **yeni değil**, mevcut başlık yanıltıcı | "Popüler Branşlar" veya "Çok Sipariş Edilen Kategoriler" |
| B4 | 4 kategori spotlight: tekrar mı? Top-row 3'te de aynı kategoriler tab'la var | İki bölümden birini sil (önerim: top-row 3'ü branş card grid'e çevir, bottom-row 1'i "İndirimli Setler" yap) |

### 3.9 Bottom-Row 2 — Title 162 + Button 287 (İletişim)

**Mevcut:** "İletişim" başlığı + CTA → `/index.php?route=information/contact`

**Sorun:**
- Çok basit, anlamsız konum (footer öncesi)
- WhatsApp + telefon + adres yok (sadece text CTA)

**Öneri:** Tüm bottom-row 2'yi sil. Yerine **"Klinik avantaj mesajı"** bloğu:

```
+--------------------------------------------+
|  RAVEN DENTAL FARK                         |
|                                            |
|  ✓ 20+ yıl üretici deneyimi               |
|  ✓ CE / ISO 13485 sertifikalı             |
|  ✓ DİŞSİAD üyesi                          |
|  ✓ 5+ adetten klinik fiyat                |
|  ✓ Türkiye geneli 1-3 gün kargo            |
|  ✓ %100 iade + 2 yıl üretici garantisi    |
|                                            |
|  [Toptan Teklif İste]  [WhatsApp Sipariş] |
+--------------------------------------------+
```

### 3.10 Footer-Row 1 — Side Products 39 ("En Çok Görüntülenen")

**Mevcut:**
- Tek tab "EN ÇOK GÖRÜNTÜLENEN"
- Mini ürün carousel (70×70 görsel, küçük)
- Quickview + Sepete Ekle + Wishlist + Compare butonları

**Sorunlar:**
- Footer'da bu kadar büyük yer kaplaması doğru değil (görsel hiyerarşi açısından "üst-bilgi" gibi davranıyor)
- Side products modülü genelde sidebar için tasarlanmış — full-width footer'da carousel'e dönüşmesi ekstra JS yükü
- "EN ÇOK GÖRÜNTÜLENEN" yerine **"Çok Satanlar"** veya **"Bestseller — Bu Hafta"** daha güçlü sosyal kanıt

**Öneri:** Modülü footer'dan sil, üst kısma (bottom-row 1'e) "Çok Satanlar" tabı olarak entegre et. Footer'ı küçült.

### 3.11 Footer — Sosyal İkonlar (61), Links Menu (72/75/76/77), Newsletter (67), Bottom Menu (266), Copyright (77), Ödeme İkonları (228)

**Mevcut:**
- Sosyal: Facebook (dentmadikal.co) + Instagram (raven.dental) + Instagram (ravendisdeposu)
- Links: Hakkımızda / Destek / Hesabım / Bülten — 4 sütun
- Newsletter: e-mail + KVKK checkbox
- Bottom: "Copyright © 2021" + Visa + Mastercard

**Sorunlar:**
| # | Sorun | Çözüm |
|---|---|---|
| F1 | Facebook hesap adı "dentmadikal" — Raven Dental ile alakasız (eski isim?) | Trust kırıcı; ya Raven Dental Facebook hesabı aç ya da link kaldır |
| F2 | 2 ayrı Instagram (raven.dental + ravendisdeposu) — kullanıcı kafası karışır | Tek hesaba konsolide |
| F3 | KEP adresi yok, VKN yok, MERSİS yok, Ticaret Sicil yok | B2B trust — Medexsepeti rakibinde var, bizde yok |
| F4 | DİŞSİAD logosu yok (yıllık aidat ödüyoruz, kullanılmıyor!) | Trust kazanç sıfır |
| F5 | "Copyright © 2021" — 5 yıllık tarih, "© 2026" olmalı | Otomatik `{{ "now"\|date("Y") }}` |
| F6 | Şartlar / İade / KVKK / Mesafeli Satış linkleri eksik (sadece Gizlilik + Şartlar var) | Yasal: mesafeli satış sözleşmesi zorunlu |
| F7 | Adres + telefon + email yok | Trust kayıp |
| F8 | Newsletter — KVKK checkbox tıklamadan submit'e izin verilmemeli (şu an verilir) | Tüketici Yasası uyum |

**Önerilen yeni footer (ASCII):**

```
+------------------------------------------------+
| HIZLI ERİŞİM        | KURUMSAL    | DESTEK    |
| El Aletleri         | Hakkımızda  | İletişim  |
| Elektronik          | Sertifikalar| SSS       |
| Raven Cerrahi       | Blog        | Kargo     |
| Sarf                | Sözlük      | İade      |
| Toptan Alışveriş    | Basın       | Garanti   |
+------------------------------------------------+
| BÜLTEN              | İLETİŞİM                |
| [email][Gönder]     | ☎ 0552 853 03 99       |
| □ KVKK kabul        | ✉ info@ravendental...  |
|                     | 📍 [Adres]              |
|                     | WhatsApp  Instagram FB  |
+------------------------------------------------+
| TRUST BAND                                     |
| [DİŞSİAD] [CE] [ISO 13485] [Tendata]          |
| [Türk Üretimi] [KEP] [ETBİS]                  |
+------------------------------------------------+
| © 2026 Raven Dental   VKN: XX  MERSİS: XX     |
| [Visa] [MC] [Banka Havalesi]                  |
| KVKK · Mesafeli Satış · Çerez · Gizlilik     |
+------------------------------------------------+
```

---

## 4. Rakip Karşılaştırma Matrisi (Bizim Eksikler)

| Özellik | Davye | Medexsepeti | Dentalpiyasa | Dentrealmarket | Dismalzemeleri | Dentalsepet | **Raven (biz)** | Aksiyon |
|---|---|---|---|---|---|---|---|---|
| Blog | ❌ | ✅ blog.sub | ✅ /sozluk | ❌ | ✅ /blog | ❌ | ❌ (boş Journal3 blog) | 5 makale (Faz 1) |
| Sözlük | ❌ | ❌ | ✅ /sozluk A-Z | ❌ | ❌ | ❌ | ❌ | **Mavi okyanus** 50 → 500 terim |
| Native App | ❌ | ✅ Android | ✅ iOS+Android+depo | ✅ iOS+Android | ❌ | ✅ iOS+Android | ❌ | PWA ile başla (Faz 1, 4 saat iş) |
| **AggregateRating** | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | **6/6 boş — kazanma fırsatı** |
| YouTube | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | **6/6 boş — kazanma fırsatı** |
| Kargo eşik banner | ✅ "1000 TL üzeri ücretsiz" | ⚠️ | ⚠️ | ⚠️ | ✅ | ⚠️ | ❌ (info-blocks'ta var ama belirgin değil) | Header notice modülü 56'ya yaz |
| Teknik servis vaadi | ✅ | ⚠️ | ⚠️ | ⚠️ | ✅ | ⚠️ | ❌ | "2 yıl üretici garantisi" rozet |
| WhatsApp widget | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ✅ | ⚠️ | ✅ FAB 0552 853 03 99 | Korunmalı, mobile UX optimize |
| Fiyat şeffaf | ⚠️ | ⚠️ | ✅ açık (DP10 kupon) | ⚠️ | ⚠️ | ⚠️ | ⚠️ (login-wall?) | Stratejik karar gerekli |
| Sertifika rozeti (CE/ISO) | ❌ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ❌ (yok) | İnfo-blocks 86'ya ekle (Faz 1) |
| DİŞSİAD logosu | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ | ❌ (üyeyiz ama yok) | Footer trust band |
| Adres + Maps | ✅ Fatih | ✅ Kucukcekmece | ✅ İTÜ Arı | ✅ Ümraniye | ✅ Ankara | ✅ | ❌ | Footer + iletişim sayfası |
| KEP / VKN footer | ⚠️ | ✅ | ✅ | ⚠️ | ⚠️ | ⚠️ | ❌ | Footer trust band |
| Subscription model | ❌ | ✅ Medex Plus | ❌ | ⚠️ ihale | ❌ | ❌ | ❌ | Faz 2 (90+ gün) |
| Bulk add-to-cart | ❌ | ⚠️ | ✅ pazaryeri | ⚠️ | ❌ | ⚠️ | ❌ | B2B kritik (Faz 2) |
| Compare | ✅ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ✅ var (Journal3) | UX optimize |
| Quickview | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ✅ var | Korunmalı |
| Schema (Product+Org+Breadcrumb+Website) | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ✅ 4 blok | Korunmalı, AggregateRating ekle |
| hreflang | ⚠️ EN | ✅ TR+EN | ❌ | ❌ | ❌ | ❌ | ✅ TR+x-default | Korunmalı |
| Türkçe slug temizliği | ⚠️ attribute-stack | ⚠️ case karışık | ✅ flat slug | ⚠️ | ⚠️ | ⚠️ | ✅ 738 SEO URL temiz | Korunmalı |
| Kategori uzun açıklama | ✅ | ⚠️ kısa | ⚠️ orta | ❌ kısa | ⚠️ | ❌ kısa | ✅ 18×1450 char | Korunmalı |
| **Sosyal medya boş link** | ✅ gerçek | ✅ gerçek (zayıf) | ✅ gerçek | ✅ gerçek | ⚠️ | ✅ gerçek | ⚠️ (FB hesabı isimsiz, 2 IG) | Konsolide et |

**Özet:** Biz teknik SEO'da iyi, içerik derinlikte iyi, mobile/A11y modern stack'te iyi — ama **trust visualization (rozet)** + **B2B UX (klinik fiyat, bulk cart)** + **review/sözlük/video boşluğunda** geriyiz.

---

## 5. Sıralı İyileştirme Planı

### 5.1 Faz 1 — Quick Wins (Bu Hafta, ~10 saat)

> **Hedef:** Lighthouse Perf 67 → 78, A11y 71 → 90, trust signal'i görünür kıl, dead links temizle.

| # | İş | Modül | Süre | Etki | LCP/A11y Riski |
|---|---|---|---|---|---|
| 1 | Hero görsel WebP + preload + responsive `<picture>` | master_slider 26 + customCodeHeader | 1 h | LCP −1.5s 🔥 | Düşük |
| 2 | Logo PNG → WebP (916×332 → 400×140) | catalog/view + tema settings | 30 dk | LCP −300ms | Düşük |
| 3 | Info-blocks 86 → B2B trust (CE/ISO/Türk üretimi/klinik fiyat) + ikon | info_blocks 86 | 1.5 h | Trust + dönüşüm | Sıfır |
| 4 | Ana menü 22 → 8 madde + mega-menü grid (flyout_menu yeni modül) | flyout_menu (yeni) + main_menu 3 | 2 h | Bilişsel yük −60% | Sıfır |
| 5 | SARF / RAVEN CERRAHİ ALETLER `<a>` href ekle | main_menu 3 admin | 15 dk | Crawl + UX | Sıfır |
| 6 | Footer trust band (DİŞSİAD + CE + ISO + Tendata + Türk Üretimi rozet) | banners (yeni 401) | 1 h | E-E-A-T | Sıfır |
| 7 | Footer KEP + VKN + adres + tel | bottom_menu 266 + links_menu (yeni) | 30 dk | B2B trust | Sıfır |
| 8 | Bottom-row 2 (sadece İletişim CTA) → "Raven Dental Fark" 6-madde kart | info_blocks (yeni) | 1 h | Dönüşüm | Sıfır |
| 9 | Side products 39 footer'dan sil, üste taşı | side_products 39 layout | 30 dk | Layout temizlik | Sıfır |
| 10 | Banner 286 width/height boş → 886×886 doldur (CLS) | banners 286 admin | 15 dk | CLS −0.02 | Sıfır |
| 11 | "Test" ürün id=455 sil | OpenCart admin | 5 dk | Profesyonel imaj | Sıfır |
| 12 | Title 147 "%30 indirim" → "Toplu Sipariş Avantajları" | title-module 147 | 5 dk | Yasal risk azalt | Sıfır |

**Beklenen toplu etki:** LCP 6.7s → 4.5-5.0s, A11y 71 → 88-92, dönüşüm sinyali +20-30%.

### 5.2 Faz 2 — İçerik + Etkileşim (30 gün, ~30 saat)

> **Hedef:** Review akışı başlat, blog ilk 5 makale, sözlük altyapı, branş card grid, kategori filtre B2B'leşsin.

| # | İş | Modül | Süre | Etki |
|---|---|---|---|---|
| 13 | Kategori "tab" yerine "branş card grid" (9 branş büyük kart) | categories 37 yenile veya yeni 401 | 4 h | Upper-funnel UX |
| 14 | Branş ikonografi: 9 özel SVG (Endo/Cerrahi/İmplant/Ortodonti/Periodonti/Çekim/Protez/Diagnostik/Restorasyon) | icon set hazırla | 4 h | Görsel kimlik |
| 15 | Products 27 tab'a "Çok Satanlar" + "İndirimli" ekle | products 27 admin | 1 h | İçerik derinlik |
| 16 | Review akışı: sipariş sonrası WhatsApp + email otomasyonu | OpenCart event hook | 6 h | AggregateRating hazırlık |
| 17 | Bulk add-to-cart (B2B): kategori sayfasında "5 ürün seç + tek tıklamayla sepete" | tema custom JS | 6 h | B2B AOV ↑ |
| 18 | Klinik fiyat tablosu (5+/10+/25+) ürün sayfasında | tema product.twig | 4 h | B2B dönüşüm |
| 19 | Ürün sayfası Q&A bölümü ("Bu ürün için soru sor" — moderasyonlu) | yeni modül + OCMOD | 5 h | E-E-A-T |
| 20 | "Teklif İste" formu (>500 adet B2B) | form modülü (Journal3'te var ama aktif değil) | 2 h | Lead capture |
| 21 | Blog ilk 5 makale yayını + anasayfa "Son Yazılar" carousel (blog_posts modülü aktif) | blog_posts (yeni 402) | 8 h (yazı) + 1 h (modül) | İçerik motoru |
| 22 | Sözlük altyapı (/sozluk) + 20 başlangıç terimi | bilgi sayfası + URL routing | 6 h | Long-tail SEO |
| 23 | Mobile sticky bottom-bar (Sepete Ekle + WhatsApp) | tema custom CSS/JS | 3 h | Mobile dönüşüm |
| 24 | Header notice 56 aktif: "🚚 500 TL üzeri ücretsiz kargo \| ☎ 0552..." | header_notice 56 | 30 dk | Üst trust |
| 25 | Kategori filtre B2B genişlet: Sertifika, Steril, Material AISI 304/420, Otoklavlanabilir | filter 36 attribute mapping | 3 h | B2B niş |

### 5.3 Faz 3 — PWA + Sözlük Genişleme + Video (60-90 gün, ~50 saat)

| # | İş | Modül | Süre | Etki |
|---|---|---|---|---|
| 26 | PWA: manifest.json + service worker + "Ana Ekrana Ekle" prompt | tema custom + system/library | 8 h | Native app alternatifi |
| 27 | Sözlük 50 → 200 terim + Schema.org DefinedTerm | bilgi sayfası + OCMOD | 30 h (yazı) + 2 h (markup) | Long-tail SEO |
| 28 | YouTube kanal + 5 başlangıç video + product page'de video embed | tema product.twig + yeni schema | 20 h | Mavi okyanus |
| 29 | Testimonials 256 aktif (gerçek hekim sözleri) + Trustpilot widget | testimonials 256 + 3rd party | 4 h | Sosyal kanıt |
| 30 | Hesabım dropdown (kayıtlı kullanıcı için sticky menü) | tema header.twig | 4 h | Retention |
| 31 | "Recently viewed" ürün carousel (cookie-based) | Journal3 viewed | 2 h | Cross-sell |
| 32 | Compare tool full-page (mevcut tooltip → /karsilastir sayfası) | route-product-compare | 2 h | B2B karar destek |

### 5.4 Faz 4 — Subscription + Mobile App + Uluslararası (180+ gün)

| # | İş | Süre |
|---|---|---|
| 33 | Raven Plus subscription (Medex Plus modeli — %10 indirim + ücretsiz kargo + öncelikli destek) | 80 h |
| 34 | React Native veya Flutter native app (iOS + Android) | 6 ay |
| 35 | EN locale tekrar aktivasyon (hreflang altyapısı zaten hazır) | 4 hafta |
| 36 | Klinik Açılış Paketi konfigüratör (25 alet hazır set) | 60 h |

---

## 6. Her İyileştirme İçin Detay (Faz 1 + Faz 2)

### 6.1 Hero Görsel Optimizasyonu

**Modül:** master_slider 26
**İçerik:** Mevcut PNG'leri WebP'ye çevir, slide layer text'lerine başlık + alt yazı + CTA ekle.

**Görsel:**
- Slide 1: 1920×600 WebP, q=80 (target <100 KB) — başlık: "Türk Üretimi Cerrahi Aletler", alt yazı: "CE / ISO 13485 sertifikalı klinik fiyat", CTA: "[Hemen İncele]"
- Slide 2: aynı boyut — başlık: "20+ Yıl Üretici Deneyimi", alt yazı: "DİŞSİAD üyesi · Tendata kayıtlı", CTA: "[Sertifikalarımız]"
- Slide 3 (opsiyonel): "Toptan Alışveriş — 5+ adet kademeli fiyat" + WhatsApp CTA

**HTML head'e ekle:**
```html
<link rel="preload" as="image"
      imagesrcset="/image/cache/catalog/slider/hero1-960w.webp 960w,
                   /image/cache/catalog/slider/hero1-1920w.webp 1920w"
      imagesizes="100vw"
      href="/image/cache/catalog/slider/hero1-1920w.webp" fetchpriority="high">
```

**Beklenen süre:** 1 saat
**Lighthouse etkisi:** LCP −1.5s, Perf +8 puan

### 6.2 Logo Optimizasyonu

**Sorun:** `LOGO3_compressed-1-916x332.png` — 916×332 native, gerçek görünüm ~200×60. Her sayfada 30-50 KB.

**Çözüm:** `LOGO3-400x140.webp` (q=85), `<picture>` tag'ı ile fallback.

**Süre:** 30 dk

### 6.3 Info-Blocks 86 — B2B Trust Kartları

**Mevcut içerik (yeniden yazılacak):**
```
Eski: Güvenli Alışveriş | %100 kolay iade | Taksitli Alışveriş | Ücretsiz kargo (500 TL ve üzeri)
Yeni: CE / ISO 13485    | Türk Üretimi    | Hızlı Kargo (1-3 gün) | Klinik Fiyatlandırma
```

**Yeni içerikler:**
- **Kart 1 (CE/ISO 13485):**
  - İkon: `shield-check` (Font Awesome veya inline SVG)
  - Title: `CE / ISO 13485 Sertifikalı`
  - Subtitle: `Tıbbi Cihaz Yönetmeliğine tam uyum`
- **Kart 2 (Türk Üretimi):**
  - İkon: `flag-tr` veya TR bayrağı SVG
  - Title: `Türk Üretimi`
  - Subtitle: `Üreticiden klinik fiyatına doğrudan satış`
- **Kart 3 (Hızlı Kargo):**
  - İkon: `truck-fast`
  - Title: `Hızlı Kargo`
  - Subtitle: `Türkiye geneli 1-3 iş günü teslimat`
- **Kart 4 (Klinik Fiyat):**
  - İkon: `tag-percent`
  - Title: `Klinik Fiyatlandırma`
  - Subtitle: `5+, 10+, 25+ adet kademeli toptan fiyat`

**Süre:** 1.5 saat (admin panelde info-blocks 86 edit + ikon SVG yükle)
**Etki:** Üst-fold trust hierarchy + dönüşüm sinyali

### 6.4 Ana Menü 22 → 8 Madde

**Mevcut (22 madde):**
ANASAYFA · EL ALETLERİ ▾ (Diagnostik/Endodonti/Çekim/İmplantoloji/Ortodonti/Periodonti/Restorasyon/İşlem/Protez/Cerrahi=10) · ELEKTRONİK ▾ (Aerator/Anguldurva/Micro Motor/Piyasemen=4) · SARF (linksiz) · RAVEN CERRAHİ ALETLER (linksiz) · TOPTAN ALIŞVERİŞ · DUYURULAR · İLETİŞİM

**Önerilen (8 madde + mega-menü):**

```
ANASAYFA · EL ALETLERİ ▾ · ELEKTRONİK ▾ · SARF · RAVEN CERRAHİ ▾ · TOPTAN · BLOG · İLETİŞİM
```

**Mega-menü içerikleri (hover'da büyük grid):**
- **EL ALETLERİ ▾** — 10 branş 3 sütunlu grid + her birinin yanında ürün sayısı + bir hero görsel ("Yeni Endodonti Eğeleri" gibi)
- **ELEKTRONİK ▾** — 4 alt + grid
- **RAVEN CERRAHİ ▾** — Set kategorileri (İmplant Cerrahisi Seti, Periodontal Set, Genel Cerrahi Set, vb.)

**Modül:** Journal3'ün `flyout_menu` modülü (henüz aktif değil) bunun için uygun. Mevcut main_menu 3 silinmez ama mega-menü ile genişletilir.

**Süre:** 2 saat
**Etki:** Bilişsel yük −60%, click-depth azalır

### 6.5 Footer Trust Band

**Yeni modül:** `banners` tipi yeni modül (önerilen ID: 401) — 7 rozet:

| Rozet | Görsel | Link |
|---|---|---|
| DİŞSİAD | DİŞSİAD logosu PNG → WebP | dissiad.org.tr/uye/raven-dental-1 |
| CE | CE markası SVG | /sertifikalar#ce |
| ISO 13485 | ISO 13485 logo | /sertifikalar#iso |
| Tendata | Tendata logo | /sertifikalar#tendata |
| Türk Üretimi | TR bayrak + "Made in Türkiye" | /hakkimizda |
| KEP | KEP rozet | /iletisim |
| ETBİS | ETBİS rozet | /iletisim |

**Süre:** 1 saat (rozet görsellerinin tedariki ayrı, ~2 saat)

### 6.6 Branş Card Grid (top-row 3 yenileme)

**Modül:** Mevcut `categories 37` yerine yeni `banners` tipi modül (önerilen ID: 402) — 9 büyük kart.

**Layout:**
- 4-3-2 sütun responsive (desktop / tablet / mobile)
- Her kart: ikon (60×60 SVG) + branş adı + ürün sayısı dinamik
- Hover'da: kısa açıklama overlay

**İçerikler:**
| Branş | URL | İkon | Ürün # |
|---|---|---|---|
| Endodonti | /endodonti-aletleri | tooth-canal | 45 |
| Cerrahi | /cerrahi-aletleri | scalpel | 62 |
| İmplantoloji | /implantoloji-aletleri | implant-screw | 38 |
| Ortodonti | /ortodonti-aletleri | brackets | 27 |
| Periodonti | /periodonti-aletleri | gum | 18 |
| Çekim | /cekme-aletleri | tooth-extract | 33 |
| Protez | /protez-aletleri | denture | 22 |
| Diagnostik | /diagnostik-aletleri | mirror | 51 |
| Restorasyon | /restorasyon-aletleri | filling | 29 |

**Süre:** 4 saat (modül + ikon set)

### 6.7 Klinik Fiyat Tablosu (ürün sayfası)

**Konum:** Ürün sayfası — fiyat alanının altı, "Sepete Ekle" butonu öncesi.

**HTML iskelet (örnek):**
```html
<table class="klinik-fiyat">
  <thead><tr><th>Adet</th><th>Birim Fiyat</th><th>Toplam</th></tr></thead>
  <tbody>
    <tr><td>1-4</td><td>₺250</td><td>—</td></tr>
    <tr><td>5-9</td><td>₺225 (%10 ↓)</td><td>5 × ₺225 = ₺1.125</td></tr>
    <tr><td>10-24</td><td>₺200 (%20 ↓)</td><td>10 × ₺200 = ₺2.000</td></tr>
    <tr><td>25+</td><td><a href="/teklif-iste">Teklif İste</a></td><td>—</td></tr>
  </tbody>
</table>
```

**Süre:** 4 saat (tema product.twig + DB attribute mapping)

### 6.8 Review Akışı Otomasyonu

**Akış:**
1. Sipariş "delivered" statüsüne döndüğünde event hook tetiklenir
2. WhatsApp Business API + email aynı anda gönderilir
3. Müşteriye ürün başına link (`/urun-yorum/{order_id}/{product_id}/{token}`)
4. Yorum bırakıldıktan sonra "%5 sonraki sipariş kuponu"
5. AggregateRating otomatik hesaplanır (Journal3'ün review widget'ı zaten var, sadece teşvik akışı kurulacak)

**Süre:** 6 saat (OpenCart event hook + email template + WhatsApp Business API entegrasyonu)

### 6.9 Bulk Add-to-Cart (Kategori Sayfası)

**Mevcut:** Her ürün tek tek "Sepete Ekle" — B2B'de 25+ ürün eklemek 25× tıklama
**Önerilen:** Kategori sayfasının üstüne **"Çoklu Seç"** toggle. Aktif olduğunda her kartta checkbox + adet input. Sticky footer'da "X ürün seçildi · ₺Y toplam · [Sepete Ekle]"

**Süre:** 6 saat (tema custom JS + AJAX endpoint)

---

## 7. Mobile UX Özel Önerileri

### 7.1 Sticky Bottom Bar

**Mevcut:** Sadece sağ-altta WhatsApp FAB (56×56px, doğru target-size ✅)
**Eksik:** Ürün sayfasında "Sepete Ekle" sticky değil, scroll'da ulaşılamıyor

**Öneri:** Ürün sayfasında **mobile-only sticky bottom-bar**:
```
+--------------------------------------+
| ₺250 (Klinik fiyat: ₺200 5+)         |
| [Sepete Ekle]  [WhatsApp Sor]        |
+--------------------------------------+
```

### 7.2 Mobile Menu Reorganizasyon

**Mevcut:** Hamburger'a tüm 22 madde dökülüyor — accordion bile değil
**Öneri:** Accordion mode (Journal3'te `.accordion-menu` class zaten var):
- ANASAYFA
- ▾ EL ALETLERİ (10 alt, sadece tap'la açılır)
- ▾ ELEKTRONİK (4 alt)
- ▾ RAVEN CERRAHİ
- SARF
- TOPTAN
- BLOG
- İLETİŞİM
- ☎ 0552 853 03 99 (CTA olarak vurgulu)
- 💬 WhatsApp (CTA)

### 7.3 Touch Target Sizes (✅ A11y Düzeltildi)

Lighthouse A11y patch'inde (header HTML'de embed) `min-height: 44px` + `padding: 12px 8px` zaten uygulanmış. Korunmalı.

### 7.4 Mobil Search Daha Erişilebilir

**Mevcut:** Mobile header'da search yok, sadece menu trigger + cart
**Öneri:** Mobile header'a search ikonu ekle (sağ üst), tap'la full-screen search overlay açılsın

---

## 8. Performans + Erişilebilirlik Kontrolü

Önerilen tüm değişikliklerin Lighthouse skorlarına etkisi:

| Değişiklik | LCP | CLS | INP | TBT | A11y | Risk |
|---|---|---|---|---|---|---|
| Hero WebP + preload | **−1.5s** ✅ | 0 | 0 | 0 | 0 | Düşük |
| Logo WebP | **−300ms** ✅ | 0 | 0 | 0 | 0 | Düşük |
| Info-blocks SVG ikon | +0 (inline SVG) | 0 | 0 | 0 | **+2** ✅ | Sıfır |
| Ana menü kısaltma + mega | 0 | 0 | **−20ms** ✅ | **−50ms** ✅ | 0 | Düşük |
| Branş card grid (yeni 9 görsel) | **+200ms** ⚠️ | 0 (width/height set) | 0 | 0 | 0 | Orta — WebP + lazy şart |
| Footer trust band (7 rozet) | +0 (footer altında, lazy) | 0 | 0 | 0 | 0 | Sıfır |
| Klinik fiyat tablosu | 0 | 0 | 0 | 0 | 0 | Sıfır |
| Mobile sticky bottom-bar | 0 | 0 | 0 | +10ms | 0 | Düşük |
| Review akışı (geriden gelen JS) | 0 | 0 | 0 | 0 | 0 | Sıfır |
| Bulk cart JS | 0 | 0 | +5ms | +30ms | 0 | Düşük |
| PWA service worker | 0 | 0 | 0 | 0 | 0 | Sıfır (geri planda) |

**Net beklenen Lighthouse hareketi:**
- Performance: 67 → **85-88** (LCP düşüşü baskın)
- A11y: 71 → **94-96**
- Best Practices: 96-100 → korunur
- SEO: 92 → **95-98** (crawlable-anchors fix + AggregateRating eklenince)

**A11y kritik kurallar (Faz 1 + 2 boyunca korunmalı):**
- ✅ Target-size 44×44 (zaten patch'lendi)
- ✅ Color contrast (header beyaz, footer koyu — patch'lendi)
- ✅ button-name + label + link-name (JS patch aktif)
- ✅ heading-order (H1 sr-only + H2 inject)
- ⚠️ Yeni eklenen mega-menü → `role="menubar"` + arrow-key navigasyon
- ⚠️ Yeni branş card grid → `<a>` element olmalı, `<div onclick>` değil
- ⚠️ Klinik fiyat tablosu → `<table>` semantic, `<th scope="col">`

---

## 9. Risk Uyarıları

| Risk | Olasılık | Etki | Hafifletme |
|---|---|---|---|
| LiteSpeed cache yeni layout'u eski cache'ten servis eder | Yüksek | Yüksek | Her büyük layout değişikliğinden sonra `/admin/index.php?route=common/dashboard&clearcache=1` |
| Journal3 modification cache'i ters tepki verir | Orta | Yüksek | `storage/modification/` dokunma; sadece admin Modification Refresh |
| Yeni branş ikonları farklı stilde olur (görsel tutarsızlık) | Yüksek | Orta | Tek SVG icon set'i (Lucide veya custom) — tek dosyada hepsi |
| WebP olmayan eski tarayıcı (IE/eski Safari) fallback gerekir | Düşük | Düşük | `<picture>` ile JPEG/PNG fallback |
| Mega-menü genişlerse mobile'da çakışır | Yüksek | Orta | `@media (max-width:768px)` accordion modu zorunlu |
| Klinik fiyat tablosu KDV dahil/hariç karışıklığı | Yüksek | Yüksek | Tek bir politika belirle, her fiyatın altına "KDV dahil" / "KDV hariç" not |
| Review akışı: 100+ müşteriye otomatik WhatsApp = SPAM bildirimi | Orta | Yüksek | WhatsApp Business API resmi template, opt-in checkbox sipariş aşamasında |
| Sözlük 200+ AI generated terim → Google "scaled content abuse" | Yüksek | Çok yüksek | Her terim insan editöryal review (docs/16 §3.4) |
| Bulk cart 25+ ürünü tek tek validate ederken AJAX timeout | Orta | Orta | Batched payload (5'er ürün halinde) |
| Mobile sticky bottom-bar iOS Safari'de footer'ı kaplar | Yüksek | Düşük | `padding-bottom: env(safe-area-inset-bottom)` + body padding |

---

## 10. PR Checklist

Her UI/UX değişiklik PR'ında aşağıdaki kontroller yapılmalı:

### 10.1 Genel
- [ ] Anasayfa HTML byte size 500 KB altında kalıyor (mevcut 449 KB)
- [ ] Lighthouse Perf mobile **regresyon yok** (yeni ölçüm vs `docs/17`)
- [ ] Lighthouse A11y skoru 90+ korunuyor
- [ ] LCP element doğru tanımlanmış (`<link rel="preload">` veya `fetchpriority="high"`)
- [ ] Yeni görseller WebP (PNG/JPEG fallback `<picture>` ile)
- [ ] `<img>` her görselde `width` + `height` attribute (CLS önleme)
- [ ] Türkçe alt text doldurulmuş (boş alt değil, dosya adı değil)

### 10.2 SEO
- [ ] Yeni sayfa varsa: title 50-60 char + meta_description 140-160 char + self-canonical
- [ ] Yeni route varsa: sitemap.xml'e eklendi mi?
- [ ] Hreflang TR + x-default korunuyor
- [ ] Schema.org Product/Breadcrumb/Organization/WebSite bloklarına zarar verilmedi
- [ ] `docs/16-GOOGLE-SEO-RULES.md` §1-10 ihlali yok
- [ ] 3. parti marka adı meta/title/H1/schema'da değil (Mani/Hu-Friedy/Dentsply vb. metinde olabilir ama metadata'da olmaz)

### 10.3 A11y
- [ ] Yeni interaktif element `role` + `aria-label` doğru
- [ ] Tab order mantıklı
- [ ] Color contrast 4.5:1 minimum (WCAG AA)
- [ ] Touch target ≥ 44×44
- [ ] Heading order bozulmamış (H1 → H2 → H3)
- [ ] Form input'ları `<label>` ile bağlı
- [ ] Yeni JS ile dynamic content eklendiyse `aria-live` veya focus management

### 10.4 Mobile
- [ ] Viewport meta korunuyor
- [ ] Mobile (320-480px) breakpoint'inde overflow yok
- [ ] Touch gesture (swipe, tap) çalışıyor
- [ ] Sticky bottom-bar safe-area-inset desteği
- [ ] Hamburger menü accordion düzgün açılıyor

### 10.5 B2B-Spesifik
- [ ] Klinik fiyat tablosu doğru kademeli (5+/10+/25+)
- [ ] KDV dahil/hariç açıkça belirtilmiş
- [ ] "Teklif İste" formu spam protection (reCAPTCHA veya honeypot)
- [ ] Bulk cart 5-50 ürün arası test edildi
- [ ] WhatsApp link `https://wa.me/...` korunuyor (`tel:` değil)

### 10.6 Journal3 / OpenCart
- [ ] Modül admin panelinden düzenlendi (DB doğrudan edit edilmedi)
- [ ] OCMOD değişikliği varsa version-controlled (analysis/theme-patches)
- [ ] `storage/modification/` dokunulmadı
- [ ] Cache temizliği gerekiyorsa rollback planı var
- [ ] Yeni modül ID'leri çakışmıyor (mevcut 24 + yeni 401/402)

### 10.7 İçerik
- [ ] Tüm metinler Türkçe (English string kalmamış)
- [ ] CTA verb-first ("İncele", "Hemen Ara", "Teklif İste") — "Tıkla" yasak
- [ ] Trust signal yeri görünür (üst-fold içinde)
- [ ] Yasal disclaimer (KDV, garanti) küçük puntoyla ama görünür
- [ ] Demo / placeholder içerik kalmadı (örnek: "test" ürün id=455 silindi)

---

## 11. Sonraki Adım Önerisi

**Bu hafta:** Faz 1'in 12 işini paralel iki ekip ile (frontend dev + içerik manager) 1-2 günde bitir. Cache temizliği + Lighthouse re-baseline son adım.

**Önce karar vermesi gereken stratejik konular** (kullanıcıdan onay):
1. **Fiyat şeffaflığı:** Login-wall var mı? Yoksa public mi? (Dentalpiyasa public yapıyor — biz aynı politikayı izleyelim mi?)
2. **DİŞSİAD logosu kullanım izni:** Aldık mı? Dernek logo kullanım koşullarını kontrol et.
3. **CE / ISO 13485 sertifika numarası:** Ürün sayfasına yazılacak mı yoksa "Belge görmek için iletişime geçin" tarzı kapalı mı?
4. **Footer adres:** Üretim tesisi adresi public yapılacak mı? (Tendata'da zaten var)
5. **Klinik fiyat politikası:** 5+/10+/25+ kademe gerçek midir? Yoksa "Teklif İste" tek noktadan mı yönetilecek?

Bu 5 karar netleştikten sonra Faz 1 başlayabilir.

---

## Versiyon Geçmişi

| Tarih | Değişiklik | Yazan |
|---|---|---|
| 2026-05-12 | İlk versiyon — 3 katmanlı analiz + faz planı + risk + checklist | ArchitectUX |

---

**Doküman sonu.** Cross-check için: `docs/16-GOOGLE-SEO-RULES.md` (SEO uyum), `docs/17-LIGHTHOUSE-BASELINE.md` (perf metrikleri), `analysis/competitors/_action-plan.md` (30/60/90 sıralama).
