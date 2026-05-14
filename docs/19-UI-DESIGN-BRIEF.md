# 19 — Raven Dental Home Page UI Design Brief

> Designer: UI Designer agent · Reference: davye.com (confirmed via direct HTML/CSS inspection on 2026-05-13) · Implementer target: Journal3 v3.1.12 / OpenCart 3.0.3.8

---

## 0. TL;DR (the 60-second read)

Raven's home today is "general uyumsuz" because three things compete: huge English-baked banners, ghost-text info_blocks, and an over-wide category grid. Davye solves the same problem with a **strict vertical narrative**: small story-style category circles → brand strip → promo tabs → 3-up banner row → titled product carousels (each with side banner) → SEO description blocks → footer with brand grid. **One container width (1320 px). One accent color (orange). Section titles always have a 3 px orange underline. Products always show a bulk-price pill (Kutu/Koli) — that is the B2B move.**

Our plan: copy the *rhythm and tokens* of davye, but swap davye's orange (#ea5b0c) for Raven's existing dark-clinical-blue from the category banners (#0f3a6b, sampled from the AVEN/Raven gradient assets). That single color swap respects davye's discipline while honoring Raven's existing photography and "medical / manufactured / certified" positioning. We add three B2B innovations davye doesn't have but a clinic buyer wants: **Üretici Şeridi** (manufacturer trust strip with ISO/CE/DİŞSİAD), **Klinik Toptan CTA banner** (price tiering teaser), and **Branş Hızlı Erişim** (specialty quick-pick — 9 circles).

---

## 1. davye.com Analysis (direct from source)

### 1.1 Section order (top-to-bottom, after header)

Confirmed from `boshtml_*` / `bannerurunslider_*` IDs in the live HTML:

| # | Section | What it is |
|---|---|---|
| 1 | **Category story strip** (`.catList.anasayfa-story`) | 15 small 73×73 round category icons, owl-carousel, like Instagram stories |
| 2 | **Brand logo strip** (`.markalar-kategorileri2`) | Single horizontal row of SVG brand logos, owl-carousel |
| 3 | **Promo tabs block** (`.promo-tabs-block`) | 3 tabs (Ürün / Teknik Servis / Banka Kampanyaları) → main banner slider + side thumbnails |
| 4 | **Hero 3-up banner row** (`#resimblok_7`) | Three equal columns (txcol-sm-4 each), full-bleed promo images, click-through to category |
| 5 | **Sub-pill row** (`.suturler` + `.sutur-kategorileri`) | A heading "Emilemeyen Sütürler / Tüm Ürünler" + horizontal owl-carousel of pill-shaped sub-category links |
| 6 | **Çok Satanlar carousel** (`bannerurunslider_10`) | Section title "Çok Satanlar" with orange underline + 3-col side banner (txcol-sm-3) on left + 9-col product slider (txcol-lg-9) on right |
| 7 | **Sub-pill row #2** ("Emilebilen Sütürler") | Same pattern as #5 |
| 8 | **Yeni Gelenler carousel** (`bannerurunslider_12`) | Same banner+slider pattern as #6 |
| 9 | *(repeats banners 13-14, carousels 15-22 for other categories)* | Pattern repeats: pill row → carousel with side banner |
| 10 | **SEO description block** (`boshtml_15`) | Eight `<h2 class="readmore-titles">` paragraphs (long-form copy for Google, collapsed/expandable client-side) |
| 11 | **Features strip** (footer top, `.ygb-features`) | 4-up feature blocks (free shipping / secure / etc.) |
| 12 | **Main footer** | Brands column, Popüler Kategoriler column, About / Help / Account columns, newsletter |
| 13 | **Bottom bar** | Copyright + ETBIS badge + payment icons |

### 1.2 Color palette (from `static.ticimax.cloud/50558/CustomCss/.../style.css` — usage-frequency-sorted)

| Token | Hex | Used For |
|---|---|---|
| Brand primary | **#ea5b0c** (107+62 occurrences) | Accent underline under section titles, hover borders on category circles, "Sepete Ekle" button, all-caps callouts |
| Brand primary alt | #ea5b0d | (same orange, secondary tone) |
| Accent gold | #e5ba57 (12 occurrences) | Star ratings, secondary highlight |
| Sale red | #ff3b30 / #8b0000 / #ff6b5a | Animated gradient on `.pallb` (the "Ekstra %5 ↓" pill on koli-pricing) |
| Trust green | #42aa45 (4 occurrences) | "Stokta var" + free-shipping ticks |
| Text primary | #070707 (16 occurrences) | Section titles, product names |
| Text body | #333333 (5 occurrences) | Body copy |
| Text muted | #7b7b7b / #777 | Secondary meta (SKU, stock count) |
| Border light | #dcdcdc / #ededed (18+) | Card borders, dividers, input borders |
| Border subtle | #e5e5e5 / #eee (16) | Hover-state borders |
| Bg page | #ffffff (121 occurrences) | Page + card background |
| Bg subtle | #f3f3f3 / #fafafa | Section alternates (rare — davye is mostly all-white) |
| Bulk-price box A | **#ebf5ff** (pcbv = Kutu) | Pale-blue mini-pill on product card |
| Bulk-price box B | **#fff0f5** (pcbc = Koli) | Pale-pink mini-pill on product card |

### 1.3 Typography

- **Font family**: `'Inter', serif !important` confirmed in custom CSS (5 declarations). Urbanist is loaded but only used in one custom-jersey-designer sub-component.
- **Headings (section title "JKatAdi"):** 20 px / 600 weight / #070707 / left-aligned / 3 px underline #ea5b0c on `.satir1` span / 25 px line-height / `padding: 15px 0 5px 0`
- **Product name:** ~14 px / 500 weight / dark
- **Body small (category-circle caption):** **10 px / 600 weight / #282828 / 14 px line-height**  ← yes, davye really runs 10 px text under the circles. Compact.
- **Bulk-price pill text (`.pcbv div`, `.pcbc div`):** **11 px / 500 weight / #333**
- **Animated sale badge `.pallb`:** **8 px / 600 weight / white on red gradient**
- Letter-spacing: none notable (the design lives on tight pairings + bold weight rather than tracking)

### 1.4 Layout & spacing

- **Container max-width: 1320 px** (`max-width: 1320px` × 13 declarations). One width, everywhere. No second container ever appears.
- **Vertical rhythm between sections:** davye uses Ticimax's `t-mt-* t-mb-* t-pt-* t-pb-*` margin scale — most sections sit on **0/0** for both margin top + bottom (sections touch), with the product-slider sections (`bannerurunslider_*`) inserting `t-mt-40 t-mb-40` (40 px) breathing room. Pattern: tight strips touch, big slider sections breathe.
- **Bootstrap-like 12-col grid** (`txcol-sm-3` / `txcol-sm-4` / `txcol-lg-9`).
- **Gutters between cards:** owl-carousel `margin: 15` (15 px) for product sliders; `margin: 6` (6 px) for sub-pill rows.
- **Category circle width:** `li 110px` total, `a 73px` clickable (so 18.5 px gap each side).

### 1.5 Card style

- Product card image: square aspect, no border-radius on the image (full bleed inside the card)
- Card body: white, **no shadow**, separated only by gutter (15 px). Hover state on `.catList ul li a img`: only the border-color changes from `#cfcfcf` to **#ea5b0c**.
- Brand strip image: SVG, white background, no card border at all. Maximum air.
- Pill (sub-category) buttons: rounded full, light gray background, text only — see `.sutur-kategori` class.
- **The Innovation:** every product card carries `.pcbv` (Kutu / single-box price, pale-blue) and `.pcbc` (Koli / case price, pale-pink, with `<del>` strike + `<b>` new price), plus `.pallb` animated "Ekstra %5 ↓ İndirim" badge. **This is the single most important B2B detail on the page.**

### 1.6 Hero treatment

Davye has **no full-bleed hero slider**. The "hero zone" is the 3-tab promo block (#3 above) — a contained 1320 px wide block with the active tab's slider inside it + thumbnails alongside. Hero is *informational*, not *immersive*.

### 1.7 Category presentation

Two layers:
1. Top "story strip": 15 circular icons (74 px), single-line carousel. Caption ≤2 lines @ 10 px. *Compact, scannable, scrollable.*
2. Sub-pill rows above each category-themed product slider (pills are owl-carousels at responsive item counts).

No full-width category card grid. No mega-banner per category. **The category "selector" is light and lives at the top; the category "story" is told via product carousels below.**

### 1.8 Innovative / distinctive modules

1. **Bulk pricing on every product card** (Kutu/Koli + animated discount badge) — the killer B2B move.
2. **Sub-pill rows above carousels** — replaces tabs, lets owner add a category-context lead-in.
3. **SEO description block at bottom** with 8 collapsible h2 paragraphs — single biggest organic-traffic lever; pure SEO real estate.
4. **Promo tabs with 3 categories** (Ürün / Teknik Servis / Banka) — banking-campaign tab is uniquely Turkish-B2B (taksit promotions per bank).
5. **Brand SVG strip** — a "we carry these manufacturers" trust device immediately above the fold.

### 1.9 What davye does NOT do (we will)

- **No manufacturer/certificate trust strip.** Davye is a *distributor* — they list brands. Raven is a *manufacturer* — we must surface that USP.
- **No specialty/branş quick-pick.** Davye assumes you know your sub-category. Clinic buyers segment by procedure (cerrahi / implantoloji / endodonti / pedodonti / ortodonti / restoratif / protez / periodontoloji / teşhis) → we expose 9 specialty circles.
- **No "Klinik Toptan" CTA.** Raven's pricing tier program deserves its own banner.
- **No video / about us / testimonial slot above fold.** We can add one carefully-scoped testimonial strip below the SEO block (we already have module 256).
- **No live hero slider with full-bleed photo.** Raven already has master_slider module 26 with 2 slides — we keep it but constrain it inside the 1320 px container so the page reads as a single coherent column, like davye.

### 1.10 Overall feel — three adjectives

**Clinical · Bulk-priced · Catalog-dense.**

(Translation: it looks like a wholesale supply catalog, not a lifestyle store. Air is rare. Information density is the feature.)

---

## 2. Raven Home Page Design (top-to-bottom)

> Implementation contract: every section below references an existing Journal3 module_id where possible. Sections marked **"NEW"** need a new module. We follow davye's section rhythm but with Raven's content and palette (clinical blue accents instead of davye orange).

> All sections live inside Journal3's standard 1320 px content wrapper. No section spans full viewport width. Vertical rhythm: tight strips (0 px gap), product carousels with 40 px top/bottom.

### Section A — Hero (contained slider) — *module 26 (existing)*

```
┌─────────────────────────────────────────────────────────────┐
│  ╔════════════════════════════════════════╗  ┌─────┐ ┌─────┐│
│  ║                                        ║  │     │ │     ││
│  ║      HERO SLIDE (master_slider)        ║  │ Side│ │ Side││
│  ║      ~960 × 420 inside container       ║  │ Banr│ │ Banr││
│  ║                                        ║  │     │ │     ││
│  ║      ●○                                ║  │ 1   │ │ 2   ││
│  ╚════════════════════════════════════════╝  └─────┘ └─────┘│
│       (txcol-sm-8)                            (txcol-sm-2 × 2)│
└─────────────────────────────────────────────────────────────┘
```
- **Purpose:** Headline message + 2 secondary calls (e.g. "Yeni Sezon El Aletleri" + "Klinik Toptan Talebi").
- **Content:** Slide 1: "Türkiye'nin Üretici Diş Aletleri Merkezi · ISO 9001 · CE · DİŞSİAD" + CTA "Kataloğa Göz At". Slide 2: "Klinik & Hastane Toplu Sipariş Avantajı · 50+ adet özel fiyat" + CTA "Teklif Al".
- **Module:** **26 Hero Slider** (existing). Reuse the 2 slides; rewrite copy.
- **Side banners:** **259 Banners Top Home** (existing, currently 2 side banners). Reuse — link to "İmplant Setleri" and "Cerrahi Setler".
- **Visual:** Constrain master_slider to `max-height: 420px; max-width: 100%;` so it sits in a 8-col bay. No full-bleed. Sharp corners (0 px radius). No shadow.
- **Asset needs:** 2 new hero images at **960 × 420** (see Asset List §4). Side banners stay at current **~240 × 200** each.
- **Innovation vs davye:** davye has no slider here at all — we keep ours but contain it so it feels disciplined, not loud.

### Section B — Üretici Trust Strip — *NEW module (replaces module 86's current 4 info_blocks)*

```
┌─────────────────────────────────────────────────────────────┐
│  ─────────────────────────────────────────────────────────  │
│  [ICON] ISO 9001    [ICON] CE          [ICON] AISI 304/420 │
│   Sertifikalı       İşaretli            Tıbbi Çelik         │
│                                                              │
│  [ICON] DİŞSİAD     [ICON] Üretici     [ICON] Klinik Fiyat │
│   Üye               Firma — TR          Tablosu             │
│  ─────────────────────────────────────────────────────────  │
└─────────────────────────────────────────────────────────────┘
```
- **Purpose:** Surface the manufacturer USP within the first viewport. This is the single sentence: *Raven yapıyor, satmıyor.*
- **Content (6 cards, single horizontal row at desktop, 2×3 at mobile):**
  1. ISO 9001 Sertifikalı / "Kalite yönetim sistemi denetimli"
  2. CE İşaretli / "AB tıbbi cihaz yönetmeliğine uygun"
  3. AISI 304/420/440 Tıbbi Çelik / "Asitlemeye dayanıklı paslanmaz"
  4. DİŞSİAD Üyesi / "Diş Sağlığı Endüstrisi Derneği"
  5. Türk Üretici / "Kendi tesisimizde, distribütör değil"
  6. Klinik Fiyat Tablosu / "50+ adet üzeri özel fiyat"
- **Module:** Rebuild **module 86** (Info Blocks) with explicit CSS — see Pitfalls §6.1. Six items, not four. **Do NOT inherit Journal3's "Home Blocks" Variable style** — give the module its own style block with absolute color/typography.
- **Visual style:** Background `#ffffff`. Card border: `1px solid #e5e5e5`, border-radius `0`. Icon: 32 px, line-icon (stroked, not filled), color `#0f3a6b` (Raven blue). Title: 14 px / 600 / `#070707`. Caption: 12 px / 400 / `#7b7b7b`. Hover: border becomes `#0f3a6b`. Vertical padding inside card: 16 px.
- **Asset needs:** 6 line icons SVG, **24×24 viewbox, 1.5 px stroke** (cert badge, CE square, microscope, handshake/network, factory, price-tag). Image Prompt Engineer should generate as a single mono-style SVG set.
- **Innovation vs davye:** davye doesn't surface manufacturer status — they can't (they're a distributor). This strip is Raven's #1 differentiator. Place immediately after hero so it pays off the "Üretici Diş Aletleri Merkezi" promise.

### Section C — Branş Quick-Pick (specialty circles) — *NEW module or repurpose module 37*

```
┌─────────────────────────────────────────────────────────────┐
│                                                              │
│   ○      ○      ○      ○      ○      ○      ○      ○      ○│
│ Cerra  İmpla  Endo   Orto   Resto  Pedo   Perio  Teşhis Çekim│
│  hi    nto.   donti  donti  ratif  donti  donto.            │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```
- **Purpose:** Procedure-first navigation — clinic buyers shop by specialty, not by tool type.
- **Content (9 circles, in this order):** Cerrahi · İmplantoloji · Endodonti · Ortodonti · Restoratif · Pedodonti · Periodontoloji · Teşhis · Çekim
- **Module:** Either (a) **repurpose module 37** by switching its "tabs" mode to a flat single-row owl-carousel of 9 items, or (b) **NEW module** (recommended — cleaner, no inherited tab CSS). Bind to the corresponding OpenCart categories (we already have all 9 in /catalog).
- **Visual style (matches davye `.catList.anasayfa-story` exactly):**
  - `li` width: 110 px
  - `a` width: 73 px
  - Image: 73 × 73, `border-radius: 50%`, `border: 1px solid #cfcfcf`
  - **Hover border: 1px solid #0f3a6b** (instead of davye's orange)
  - Caption: 10 px / 600 / `#282828` / 14 px line-height / 2 lines max
  - Carousel: owl-carousel `margin: 6`, autoplay false (let user scroll), nav arrows `25×25 #ebebeb circle` placed mid-height
  - Section background: white. Vertical padding: 20 px.
- **Asset needs:** 9 round specialty icons, **150 × 150** PNG with transparent background (we'll display at 73 px @2x for retina). Style: photographed instrument hero shot cropped square against light blue gradient → Image Prompt Engineer brief (see §4).
- **Innovation vs davye:** davye does general categories (greft, enjektör, anestezi…); we segment by *procedure*, which maps to how a clinic plans purchases (a periodontist buys a "perio kit" not a "scaler"). This single layer of mental-model translation is our highest-ROI UX move.

### Section D — Manufacturer / Brand Strip — *NEW module*

```
┌─────────────────────────────────────────────────────────────┐
│   RAVEN   AVEN    RAVEN-CERRAHİ   [reserved]   [reserved]    │
│   (svg)   (svg)   (svg)           (placeholder) (placeholder)│
└─────────────────────────────────────────────────────────────┘
```
- **Purpose:** Show the sub-brands and any partner brands Raven distributes alongside its own production. Trust by association.
- **Content:** Raven, AVEN, Raven Cerrahi for sure. If there are partner brands (the WhatsApp images suggest AVEN is in the system) include them; otherwise leave 5 slots and fill later.
- **Module:** NEW module — small (one html_block module is enough). 5 slots minimum, owl-carousel if >7.
- **Visual style:** Background `#fafafa` (the only non-white section on the page — small contrast cue that the strip is "meta"). Logos at 60 px height, grayscale by default, full color on hover. Padding: 24 px vertical.
- **Asset needs:** 1 SVG per brand at ~120 × 60 viewbox. Raven has logo3 already → convert to SVG. AVEN logo to be extracted from the category banners.
- **Innovation vs davye:** Same as davye, but ours says "we make these" rather than "we resell these".

### Section E — Featured Products: "En Yeniler" — *module 27 (existing)*

```
┌─────────────────────────────────────────────────────────────┐
│  ──────────────                                              │
│  En Yeniler ▆▆▆ ←3px blue underline                          │
│                                                              │
│  ┌──────┐  ┌────┬────┬────┬────┐                            │
│  │      │  │ p1 │ p2 │ p3 │ p4 │                            │
│  │ Side │  ├────┼────┼────┼────┤                            │
│  │ banr │  │ p5 │ p6 │ p7 │ p8 │                            │
│  └──────┘  └────┴────┴────┴────┘                            │
│   3 col              9 col                                   │
└─────────────────────────────────────────────────────────────┘
```
- **Purpose:** Best-foot-forward product showcase — what's new from production this month.
- **Content:** Pull module 27's 17 "En Yeniler" products; section title "En Yeniler". Side banner promotes "İmplant El Aleti Setleri" or "Yeni Sezon Cerrahi Kit".
- **Module:** **27 Featured Products** (existing). Add a **NEW small banner module** sitting in the txcol-sm-3 slot to its left (or reuse one slot from module 259).
- **Visual style — section title (apply globally to all carousel sections):**
  - Container: 100% width, 1 px bottom border `#d4d4d4`, margin-bottom 10 px
  - Title text: `<span class="bold"><span class="satir1">En Yeniler</span></span>` — line-height 25 px, color #070707, font-size 20 px, font-weight 600, with **3 px solid #0f3a6b** border-bottom on the `.satir1` span, padding `15px 0 5px 0`
  - This title structure is reused across sections E, G, H, J — define it once in a CSS partial.
- **Product card style:** Match Journal3 default *but* add the bulk-pricing pill (next section).
- **Asset needs:** 1 vertical side banner, **300 × 450**, "İmplant Setleri — Hekim Pratik Setleri" with Raven blue gradient + product photo.
- **Innovation vs davye:** Section title visual treatment matches davye exactly (we want that "clinical catalog" feel).

### Section E.bonus — Bulk Pricing Pill (on every product card globally)

> This is not a section — it's a **product-card-level enhancement** that needs to be added everywhere products render on the home page.

- **Purpose:** Make Raven feel like a wholesale supplier, not a B2C store. This is the davye signature move.
- **Visual spec (copy davye's `.pcbv` / `.pcbc` / `.pallb` exactly, swap red→Raven-blue accent):**
  ```
  ┌──────────────────────┐
  │ [img] Kutu 1.375 ₺   │  ← .pcbv : background #ebf5ff, radius 3px, 11px/500/#333
  ├──────────────────────┤
  │ [img] Koli  ₺5̶.̶5̶0̶0̶ 5.225 ₺   │  ← .pcbc : background #fff0f5, radius 3px
  │              [%5 ↓]  │  ← .pallb : 8px white text, animated red-gradient bg
  └──────────────────────┘
  ```
- **Data:** Each product needs three fields — `unit_price`, `box_qty + box_price`, `case_qty + case_price + case_discount_pct`. Some Raven products already have multi-unit pricing in OpenCart's `product_discount` table — leverage that. If a product has none, hide the pill (don't show empty).
- **Implementation:** Override Journal3's `catalog/view/javascript/journal3/journal3.js` product-card template via a Twig snippet appended to module-output. Single global CSS file (`catalog/view/theme/journal3/stylesheet/journal3/raven-bulk-pricing.css`).
- **Innovation vs davye:** Identical visually. We are confidently copying davye here because it's the right answer.

### Section F — Klinik Toptan CTA Banner — *NEW module*

```
┌─────────────────────────────────────────────────────────────┐
│  ╔═══════════════════════════════════════════════════════╗  │
│  ║   KLİNİK & HASTANE TOPLU SİPARİŞ                     ║  │
│  ║   50+ adet üzeri özel fiyat tablosu · 48 saat kargo  ║  │
│  ║   [ Teklif Al → ]      [ Fiyat Listesi PDF ]         ║  │
│  ╚═══════════════════════════════════════════════════════╝  │
│         Background: blue gradient + instrument photo          │
└─────────────────────────────────────────────────────────────┘
```
- **Purpose:** The single highest-intent B2B conversion path — surface it in the middle of the scroll where attention dips.
- **Content:** Headline "Klinik & Hastane Toplu Sipariş Avantajı". Sub: "50+ adet üzeri özel fiyat · 48 saat kargo · KDV dahil faturalı". CTAs: "Teklif Al" (primary, opens contact form pre-filled type=toptan) + "Fiyat Listesi PDF" (secondary).
- **Module:** **NEW** html_block module, placed between Featured Products (E) and the next carousel (G). Reuses Title "Toplu Sipariş Avantajları" (module 147) if we want a small title above.
- **Visual style:** Full-row banner. Height 200 px desktop / 240 px mobile. Background: Raven-blue (#0f3a6b) linear gradient with a faded instrument photo at 25% opacity on the right side. Headline 28 px / 700 / white. Sub 14 px / 400 / #cfdbe8 (light blue-gray). Primary CTA: white bg, #0f3a6b text, 600 weight, 12 px 24 px padding, border-radius 0. Secondary CTA: transparent, 1 px white border, white text.
- **Asset needs:** 1 banner **1320 × 200** with blue gradient + faded instrument (see §4).
- **Innovation vs davye:** Davye buries their bulk-pricing affordance inside product cards. We *also* surface it as a top-level CTA. Belt + suspenders.

### Section G — "Çok Satanlar" carousel — *module 186 (Top Sellers) or 213 (Bestsellers)*

```
┌─────────────────────────────────────────────────────────────┐
│  Çok Satanlar ▆▆▆                                            │
│  ┌──────┐  ┌────┬────┬────┬────┐                            │
│  │ Side │  │ p1 │ p2 │ p3 │ p4 │                            │
│  │ banr │  │bulk│bulk│bulk│bulk│                            │
│  └──────┘  └────┴────┴────┴────┘                            │
└─────────────────────────────────────────────────────────────┘
```
- **Purpose:** Social proof — what other clinics buy.
- **Content:** Pull module 213's "Bestsellers" data. Side banner: "Cerrahi El Aletleri — Production Direct".
- **Module:** **213 Bestsellers** (existing). Audit which of 186/213 has cleaner data — keep one, disable the other.
- **Visual:** Same section-title style as E. Same 3+9 grid. Same bulk pricing pills.
- **Asset:** 1 side banner **300 × 450**.

### Section H — Branş Banner Row (3-up) — *replaces module 286 strategy*

```
┌─────────────────────────────────────────────────────────────┐
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │ İmplantoloji│  │ Cerrahi     │  │ Endodonti   │          │
│  │ (banner)    │  │ (banner)    │  │ (banner)    │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
│       4col              4col              4col               │
└─────────────────────────────────────────────────────────────┘
```
- **Purpose:** Direct visual entry to the 3 highest-margin specialties.
- **Content:** Three banners linking to /implantoloji-el-aletleri, /cerrahi-el-aletleri, /endodonti-el-aletleri.
- **Module:** **286 Big Category Banners** (existing — currently 4 banners with **English baked text**). **DECISION:** *Replace the existing assets entirely.* The English bake creates dual-text chaos with any TR overlay (the previous failed attempt). Reduce from 4 → 3 (Implantoloji, Cerrahi, Endodonti — drop Diagnostics+Extraction here; they live as branş circles in §C).
- **Visual style:** Each banner is 420 × 240, image-only, no text overlay (just baked-in TR text in the asset itself). On hover: 4 px translateY up + 0 4px 12px rgba(0,0,0,0.08) shadow. Image only, no border.
- **Asset needs:** 3 new banners **420 × 240** — see §4 — **Turkish text baked in** (İMPLANTOLOJİ / CERRAHİ / ENDODONTİ + Raven logo overlay only, NO email, NO English). This is the most important asset replacement.
- **Innovation:** Replacing the English-baked assets fixes the central visual disharmony noted in the failed-attempt postmortem.

### Section I — Sub-pill row (davye-style category breadcrumbs) — *NEW thin module*

```
┌─────────────────────────────────────────────────────────────┐
│  Cerrahi Aletler                          Tüm Ürünler →     │
│  [Bistüri] [Hemostat] [Penset] [Makas] [Ekartör] [Portegü]  │
└─────────────────────────────────────────────────────────────┘
```
- **Purpose:** Below the 3-banner row, give a horizontal sub-category quick-jump for the "Cerrahi" world (the most browsed branş per our category structure).
- **Content:** 6-8 pill links pointing to sub-categories. Pills auto-rotate via owl-carousel if too many.
- **Module:** **NEW** simple html_block module. Davye uses this pattern 4+ times — copy it.
- **Visual style:** Title row: 16 px / 600 / #070707 left + "Tüm Ürünler →" 13 px / 500 / #0f3a6b right. Pills: light gray bg (#f3f3f3), 6 px 14 px padding, border-radius 999px (full pill), 13 px / 500 / #333. Hover: bg #0f3a6b, text white.
- **Asset needs:** None.

### Section J — "Cerrahi Aletler" carousel — *module 169 (Yeni Ürünler) or 27 reuse*

```
┌─────────────────────────────────────────────────────────────┐
│  Cerrahi Aletler ▆▆▆                                         │
│  ┌──────┐  ┌────┬────┬────┬────┐                            │
│  │ banr │  │ p1 │ p2 │ p3 │ p4 │                            │
│  └──────┘  └────┴────┴────┴────┘                            │
└─────────────────────────────────────────────────────────────┘
```
- **Purpose:** Reinforce the branş banner above with actual product faces.
- **Content:** Auto-fed from category cerrahi-el-aletleri, sort by best-seller.
- **Module:** **169 Yeni Ürünler** (existing — rename to "Cerrahi Aletler" or duplicate as a new instance pointing to the cerrahi category).
- **Visual:** Same section-title pattern. Same 3+9 grid.

> **Note on repeating pattern:** Sections I+J can be cloned for "İmplantoloji" and "Endodonti" if owner wants depth. Recommendation: ship only one extra cycle (cerrahi) in Phase 1, add implantoloji+endodonti in Phase 2 once we measure scroll depth.

### Section K — Testimonials — *module 256 (existing, currently has 5 demo English entries)*

```
┌─────────────────────────────────────────────────────────────┐
│  Klinik Yorumları ▆▆▆                                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │
│  │ "..."        │  │ "..."        │  │ "..."        │       │
│  │ — Dr. X      │  │ — Op.Dr. Y   │  │ — Klinik Z   │       │
│  └──────────────┘  └──────────────┘  └──────────────┘       │
└─────────────────────────────────────────────────────────────┘
```
- **Purpose:** Social proof from real clinics.
- **Content:** Replace 5 English placeholder testimonials with **3 real Turkish entries** (request from owner — until then, leave the module **disabled**). Each: 2-3 sentence Turkish quote + Dr. title + clinic name + city.
- **Module:** **256 Testimonials** (existing). Disable until real content is available — do NOT ship demo content to production.
- **Visual:** 3-col grid. White cards, 1 px #e5e5e5 border, padding 24 px. Quote: 15 px / 400 / #333, italic. Attribution: 13 px / 600 / #070707. Subtle 24 px quote-mark in #0f3a6b at top-left.
- **Asset needs:** None (text only). Optional small avatar 48×48 per testimonial.

### Section L — SEO Description Block — *NEW module (mirrors davye's `boshtml_15`)*

```
┌─────────────────────────────────────────────────────────────┐
│  <h2>Raven Dental | Türkiye'nin Üretici Diş Aletleri Mer.</h2>│
│  <p>Lorem ipsum × 80 words on Raven manufacturing...</p>     │
│  <h2>Cerrahi El Aletleri</h2>                                │
│  <p>...</p>                                                  │
│  [+5 more h2 + p paragraphs]                                 │
│  [ Devamını Oku ▼ ] (collapse toggle)                       │
└─────────────────────────────────────────────────────────────┘
```
- **Purpose:** Pure SEO. Davye runs 9 h2 blocks here. Google loves it. This is the single highest-impact organic-search lever on the page.
- **Content:** 8 h2 sections in Turkish, ~80 words each. Topics: 1) Raven Dental kim, 2) Cerrahi El Aletleri, 3) İmplantoloji Setleri, 4) Endodonti Aletleri, 5) Ortodonti Aletleri, 6) Restoratif & Pedodonti, 7) Klinik Toplu Sipariş, 8) Kalite Standartları (ISO/CE/AISI).
- **Module:** **NEW** html_block module with custom Twig wrapper. Use davye's CSS class name `readmore-titles` so the same collapse JS pattern works (or write a simple show/hide toggle).
- **Visual style:** Section background #fafafa (slight contrast to mark "this is meta info"). H2: 16 px / 600 / #070707, with the same 3 px #0f3a6b underline trick. Body: 14 px / 400 / #555 / 1.7 line-height. Container 1320 px. After 240 px content height, fade-mask + "Devamını Oku" button.
- **Asset needs:** None (text only). **Content writing is required** — flag for content team.
- **Innovation:** This is *the* davye trick. Other Turkish dental sites don't do it. Easy SEO win.

### Section M — Trust + Newsletter Strip (above footer) — *module 67 (Newsletter, existing)*

```
┌─────────────────────────────────────────────────────────────┐
│  ──────────────────────────────────────────────────────────  │
│  [📦]            [🚚]           [✓]           [✉]           │
│  Üretici         48 Saat        Sertifikalı    Bültene      │
│  Direkt          Kargo          Ürünler        Kayıt        │
│                                                  [____][→]  │
│  ──────────────────────────────────────────────────────────  │
└─────────────────────────────────────────────────────────────┘
```
- **Purpose:** Final reassurance + email capture before footer.
- **Content:** 3 trust ikons (Üretici Direkt, 48 Saat Kargo, Sertifikalı Ürünler) + 1 newsletter slot.
- **Module:** Combine **67 Newsletter** (existing) with the 4th column being the email-capture. The 3 trust cells reuse the icons from §B (Section B Trust Strip).
- **Visual:** 4-col grid. White bg. 1 px top + bottom #e5e5e5. Each cell: 24 px icon, 14 px / 600 title, 12 px / 400 sub. Newsletter input: full-width within its cell, transparent bg, 1 px #cfcfcf border, 12 px 16 px padding, button on the right: #0f3a6b bg / white text / arrow icon.
- **Asset:** None new (icons reused).

### Section N — Footer (4-col + bottom bar) — *modules 72, 75, 76, 77, 266, 228, 61, 287, 86 (existing)*

```
┌─────────────────────────────────────────────────────────────┐
│  RAVEN logo          | Hakkımızda  | Destek      | Hesabım  │
│  Adres / Tel / Mail  | Hakkımızda  | İletişim    | Giriş    │
│  Sosyal: f i i       | Markamız    | Kargo & Tslim│ Kayıt   │
│  Telefon CTA         | Kalite      | İade        | Sipariş  │
│                      | ...         | KVKK        | ...      │
├─────────────────────────────────────────────────────────────┤
│  © 2026 Raven Dental · KVKK · Çerez · [Visa] [Mastercard]   │
└─────────────────────────────────────────────────────────────┘
```
- **Module mapping:** Col1 = brand block (NEW small HTML module) + module 61 (Social Icons) + module 287 (phone CTA). Col2 = module 72 (Hakkımızda links). Col3 = module 75 (Destek). Col4 = module 76 (Hesabım). Bottom: module 77 (Copyright) + module 266 (Bottom Menu) + module 228 (Payment Icons).
- **Visual style:** Background **#070707** (dark — only place on the page that's dark). Text: 13 px / 400 / #cfcfcf. Headings: 14 px / 600 / #fff. Column gap 32 px. Padding 48 px vertical. Link hover: text → #0f3a6b? No — on a dark bg use lighter accent: **#5a8fc4** (Raven blue at 70% lightness). Bottom bar: same dark, 1 px top border #1f1f1f, 12 px / 400 / #888, payment icons 28 px height.

---

## 3. Design Tokens (compact / copyable)

```css
/* =========================================================
   RAVEN DESIGN TOKENS — derived from davye.com analysis
   Save to: catalog/view/theme/journal3/stylesheet/journal3/raven-tokens.css
   ========================================================= */
:root {
  /* Brand */
  --r-blue-900:  #0a2547;   /* deepest — headings on white */
  --r-blue-700:  #0f3a6b;   /* PRIMARY — accent, section underline, primary CTA */
  --r-blue-500:  #2360a3;   /* hover/active link */
  --r-blue-300:  #5a8fc4;   /* links on dark bg */
  --r-blue-100:  #ebf2fa;   /* pcbv pill bg (cool side) */

  --r-gold-500:  #c8932b;   /* star ratings, secondary highlight */
  --r-red-500:   #d63a30;   /* sale badge (.pallb) */
  --r-pink-100:  #fff0f5;   /* pcbc pill bg (warm side) */
  --r-green-500: #2f9c46;   /* stock-ok / trust */

  /* Neutrals */
  --r-ink-900:   #070707;   /* primary text + headings */
  --r-ink-700:   #333333;   /* body */
  --r-ink-500:   #7b7b7b;   /* meta, muted */
  --r-ink-300:   #b4b4b4;   /* placeholders */
  --r-line-200:  #e5e5e5;   /* card borders, dividers */
  --r-line-100:  #ededed;   /* subtle dividers, input borders */
  --r-line-50:   #d4d4d4;   /* section-title bottom border */
  --r-bg:        #ffffff;   /* page bg */
  --r-bg-alt:    #fafafa;   /* SEO block, brand strip bg */
  --r-bg-dark:   #070707;   /* footer */

  /* Typography */
  --r-font:      'Inter', -apple-system, BlinkMacSystemFont, system-ui, sans-serif;
  --r-fs-h1:     28px;  --r-lh-h1: 36px;  --r-fw-h1: 700;   /* hero headline */
  --r-fs-h2:     20px;  --r-lh-h2: 25px;  --r-fw-h2: 600;   /* section title (JKatAdi) */
  --r-fs-h3:     16px;  --r-lh-h3: 22px;  --r-fw-h3: 600;   /* sub-section title */
  --r-fs-body:   14px;  --r-lh-body: 21px;
  --r-fs-meta:   13px;  --r-lh-meta: 18px;
  --r-fs-mini:   12px;
  --r-fs-tiny:   11px;                                     /* bulk-pricing pill text */
  --r-fs-micro:  10px;                                     /* category circle caption */
  --r-fs-badge:   8px;                                     /* .pallb sale badge */

  /* Spacing scale (4 / 8 / 12 / 16 / 24 / 32 / 40 / 48 / 64) */
  --r-s-1:  4px;
  --r-s-2:  8px;
  --r-s-3:  12px;
  --r-s-4:  16px;
  --r-s-6:  24px;
  --r-s-8:  32px;
  --r-s-10: 40px;   /* between product-carousel sections */
  --r-s-12: 48px;
  --r-s-16: 64px;

  /* Radius — davye is mostly sharp, only pills are rounded */
  --r-r-0:    0;            /* default for cards, banners, buttons */
  --r-r-pill: 999px;        /* sub-category pills */
  --r-r-full: 50%;          /* category circles */
  --r-r-mini: 3px;          /* bulk-pricing pills */

  /* Shadows — davye uses ALMOST NONE. Reserve for hover only. */
  --r-sh-0:    none;
  --r-sh-hover:0 4px 12px rgba(7,7,7,.08);

  /* Transitions */
  --r-tr-fast:   150ms ease;
  --r-tr-norm:   250ms ease;
}

/* Container — single width, all sections */
.r-container { max-width: 1320px; margin: 0 auto; padding: 0 var(--r-s-4); }

/* Section title atom (the "JKatAdi" pattern) */
.r-section-title {
  width: 100%;
  margin: 0 0 var(--r-s-3);
  border-bottom: 1px solid var(--r-line-50);
  text-align: center;
}
.r-section-title__inner {
  display: inline-block;
  position: relative;
  color: var(--r-ink-900);
  font-size: var(--r-fs-h2);
  line-height: var(--r-lh-h2);
  font-weight: var(--r-fw-h2);
  padding: 15px 0 5px;
  border-bottom: 3px solid var(--r-blue-700);
}
```

**Breakpoints** (override Journal3 defaults, match davye):
- mobile  ≤ 479
- large-mobile 480-767
- tablet 768-1040
- desktop 1041-1140
- wide-desktop 1141-1320
- max 1320+ (cap content width)

> Journal3 default breakpoints are 470 / 760 / 980 / 1024 / 1300 — close enough that we can keep Journal3's media-query breakpoints and just override the **container max-width** to 1320 px. No need to fight the theme.

---

## 4. Asset List

> File path convention: `catalog/view/theme/journal3/img/raven/` for icons; `image/catalog/raven/home/` for banners. All paths absolute below.

### 4.1 Banners (raster, AI-generatable)

| File | Path | Size | Description for Image Prompt Engineer |
|---|---|---|---|
| hero-slide-1.jpg | `/image/catalog/raven/home/hero-slide-1.jpg` | 1920×840 (display 960×420) | Wide-format clinical photograph of dental surgical instruments on brushed-steel surgical tray, soft top-down light, hint of Raven-blue (#0f3a6b) light bleed from left. **Turkish text overlay baked NO** — text goes via Journal3 slider, not in image. |
| hero-slide-2.jpg | `/image/catalog/raven/home/hero-slide-2.jpg` | 1920×840 | Same style: a "Klinik Toptan" feel — multiple instrument boxes stacked, hospital cart in soft focus background |
| side-banner-1.jpg | `/image/catalog/raven/home/side-banner-1.jpg` | 480×400 (display 240×200) | Vertical-ish promo: "İmplant Setleri" — single implant kit photo on blue gradient #0f3a6b → #1f5499 |
| side-banner-2.jpg | `/image/catalog/raven/home/side-banner-2.jpg` | 480×400 | "Cerrahi Setler" — surgical kit fan-out photo |
| bulk-cta.jpg | `/image/catalog/raven/home/bulk-cta-bg.jpg` | 2640×400 (display 1320×200) | Blue gradient #0f3a6b → #0a2547, with a faint instrument silhouette at 25% opacity on the right third. Pure background — text added in HTML overlay. |
| feat-side-1.jpg | `/image/catalog/raven/home/feat-en-yeniler.jpg` | 600×900 (display 300×450) | Vertical side banner for "En Yeniler" carousel — new instrument hero shot, blue gradient. |
| feat-side-2.jpg | `/image/catalog/raven/home/feat-cok-satan.jpg` | 600×900 | Same style for "Çok Satanlar" |
| feat-side-3.jpg | `/image/catalog/raven/home/feat-cerrahi.jpg` | 600×900 | Same style for "Cerrahi Aletler" |
| branş-implantoloji.jpg | `/image/catalog/raven/home/brans-implantoloji.jpg` | 840×480 (display 420×240) | **TR text baked: "İMPLANTOLOJİ"** in white 600 weight, Raven logo top-left, blue gradient bg, instruments on right. NO English. NO email. |
| branş-cerrahi.jpg | `/image/catalog/raven/home/brans-cerrahi.jpg` | 840×480 | Same: "CERRAHİ" baked TR text |
| branş-endodonti.jpg | `/image/catalog/raven/home/brans-endodonti.jpg` | 840×480 | Same: "ENDODONTİ" baked TR text |

### 4.2 Category circle icons (9 specialty round assets — Section C)

| File | Size | Desc |
|---|---|---|
| brans-cerrahi-circle.png | 300×300 (display 73px) | Square crop, light blue gradient bg, single surgical scalpel center, soft shadow |
| brans-implantoloji-circle.png | 300×300 | Single implant abutment center |
| brans-endodonti-circle.png | 300×300 | Endo file fan |
| brans-ortodonti-circle.png | 300×300 | Bracket close-up |
| brans-restoratif-circle.png | 300×300 | Composite spatula tip |
| brans-pedodonti-circle.png | 300×300 | Small pediatric mirror |
| brans-periodontoloji-circle.png | 300×300 | Curette tip |
| brans-teshis-circle.png | 300×300 | Dental mirror + probe |
| brans-cekim-circle.png | 300×300 | Forceps head |

All 9 should look like a **set** — same lighting, same bg gradient, same instrument framing — so the row reads as a coherent navigation device.

### 4.3 SVG icons (Trust Strip + Newsletter Strip)

Single SVG sprite at `/catalog/view/theme/journal3/img/raven/trust-icons.svg`:
- iso-cert (badge with check)
- ce-mark (CE letterform in square)
- steel (microscope or steel-bar abstract)
- network (3 connected nodes — for DİŞSİAD)
- factory (chimney + roof)
- price-tag (tag with %)
- box (shipping box)
- truck (delivery)
- check-shield (sertifikalı)
- mail (newsletter)

Style: line-only, 1.5 px stroke, 24×24 viewbox, no fill. Color set via CSS `currentColor`.

### 4.4 Brand strip logos

| File | Notes |
|---|---|
| `/catalog/view/theme/journal3/img/raven/brand-raven.svg` | Convert from existing PNG logo3 (916×332). |
| `/catalog/view/theme/journal3/img/raven/brand-aven.svg` | Extract AVEN wordmark from existing category banner images |
| `/catalog/view/theme/journal3/img/raven/brand-raven-cerrahi.svg` | Create — wordmark "Raven Cerrahi" |
| (placeholders) | Reserve 2 more SVG slots for partner brands if added later |

### 4.5 Assets to DELETE / RETIRE

- `/image/catalog/...` the 4 existing English-baked category banners (Implantology / Surgery / Diagnostics / Extraction). They have been failing the design — replace with Section H's 3 new TR-text versions.
- Existing module 86 info-blocks-style icons if they're emoji-based — replace with the new line-icon set.

---

## 5. Implementation Order (phased rollout)

> Each phase is independently deployable. If a phase breaks, the home page degrades gracefully to the previous phase's state.

### Phase 1 — Foundations (week 1) — *high-impact, low-risk*

1. Create `raven-tokens.css` and `raven-overrides.css` files in Journal3 theme.
2. Add the `.r-section-title` atom and apply to all existing carousel modules (27, 169, 186, 213). **Result:** All section titles get the davye-style blue underline. Visual coherence jumps immediately.
3. Rebuild **module 86 (Info Blocks)** as Section B (6-up Üretici Trust Strip) with **explicit inline CSS overrides** (do NOT rely on Variable inheritance). Generate 6 SVG icons.
4. Replace the 4 module 286 category banners with 3 new TR-text banners (Section H). Reduce module 286 from 4 → 3 items.
5. Deploy & verify on staging.

### Phase 2 — Branş navigation (week 2)

1. Build the **NEW Branş Quick-Pick module** (Section C) — 9 circles, owl-carousel, davye-style.
2. Commission and integrate the 9 specialty circle images.
3. Test on mobile: row scrolls horizontally, captions don't break.
4. Build the Sub-pill row module (Section I) — single instance below the Branş banner row.
5. Deploy & verify.

### Phase 3 — Bulk pricing pill (week 3) — *the high-value B2B move*

1. Build the global product-card override (`raven-bulk-pricing.css` + Twig snippet for `.pcbv` / `.pcbc` / `.pallb`).
2. Audit OpenCart product_discount data — identify products with multi-tier pricing. For products without, plan a data-entry pass.
3. Roll out pill on **module 27** first as canary. Verify visually + check Lighthouse perf.
4. Once stable, roll out to modules 169, 186, 213, 27 globally.
5. Deploy & verify.

### Phase 4 — Klinik Toptan + SEO block (week 4)

1. Build the **Klinik Toptan CTA banner** (Section F) as NEW html_block module.
2. Generate 1 banner asset (`bulk-cta-bg.jpg`).
3. Build the **SEO Description module** (Section L) with 8 h2 sections. Content team writes copy in parallel.
4. Add "Devamını Oku" collapse JS.
5. Deploy & verify.

### Phase 5 — Polish & testimonials (week 5)

1. Real testimonials collected → enable module 256 with TR content (Section K).
2. Brand strip module (Section D) — once partner brand list is finalized.
3. Newsletter trust strip (Section M) tweak.
4. Footer dark-theme polish (Section N) — only if owner wants to migrate from current light footer.
5. Final QA, Lighthouse run, mobile audit, deploy to production.

---

## 6. Pitfalls (specific gotchas)

### 6.1 Journal3 Variable bleed — the lesson from the failed attempt

Journal3's "Variable" styles (`__VAR__COLOR_*`, `__VAR__Home Blocks`, `__VAR__BTN_PRIMARY`) live on the module-level and inherit across all modules of that type. The previous attempt failed because module 86's variable was inherited from a default "Home Blocks" style that set text color to a ghost-light-gray — that's why "subtitles didn't render." **Defense:** for every NEW or rebuilt module, in Journal3 admin, do NOT pick a Variable — instead, enter explicit Style values (Text Color #070707, Background #ffffff, etc.) directly. This decouples your module from the cross-module inheritance graph. Audit existing modules by viewing the rendered HTML in DevTools and checking for `color: var(--variable-...)` rather than literal hex.

### 6.2 The 1320 px container collision

Journal3's default container can be 1170/1300 depending on theme version. Davye is 1320. If we add custom modules with `max-width: 1320px` but Journal3's wrapper is 1170, our blocks will sit narrower than expected. **Fix:** in Journal3 → Skin → "Page Width" set to 1320. Then verify on all pages that header + footer don't break.

### 6.3 The English-baked category banners

Module 286 currently has 4 banners with English baked in ("Implantology" etc.). Any TR overlay you add via Journal3's text-on-image creates dual-text chaos. **Do not try to overlay text.** Either (a) use them image-only with the EN baked text visible (acceptable interim), or (b) replace assets fully via Image Prompt Engineer. Recommendation in this brief: **(b)** — full replacement. Don't half-fix.

### 6.4 Owl-carousel + lazy-load timing

Davye's `.catList .owl-carousel` initializes after `data-lazy-function="owlSlider"` fires. If you put the Branş circles inside a section that lazy-loads, the carousel might init before images are sized → first-load layout shift. **Fix:** set explicit `width: 73px; height: 73px;` on the `img` element via CSS *before* the carousel inits, so even if JS is slow, the layout doesn't reflow.

### 6.5 Bulk-pricing pill data requirements

The `.pcbv` / `.pcbc` system assumes every product has 3 price tiers (single / box / case). If a product has only a single price, the pill area renders empty and the card looks broken. **Fix:** in the Twig partial, wrap the entire pill block in `{% if box_price or case_price %}` so absent-data products gracefully show just the single price. We also need a data-entry pass — without bulk-pricing data on at least 60% of products, the B2B promise rings hollow.

### 6.6 Bonus — section-title atom: don't apply via `!important`

The `.r-section-title__inner` border-bottom (the 3 px blue underline) needs `padding: 15px 0 5px` — Journal3's default `.module-title` has its own padding. If you stack `!important` declarations to win specificity, you'll lock yourself out of future overrides. **Better:** namespace under `.r-section-title__inner` with no `!important` and target it via `body .r-section-title .r-section-title__inner` for specificity — once. Document the pattern in `raven-tokens.css` header comment.

---

## Hand-off

- **(1) Image Prompt Engineer** — 14 banner + 9 specialty circle + 10 SVG icon + 3 brand-logo assets in §4.
- **(2) Content Writer** — 8 SEO h2 paragraphs in §L + 3 real Turkish testimonials in §K.
- **(3) Implementer** — Phase 1 module work — start with the `.r-section-title` atom because every section gains from it immediately.
