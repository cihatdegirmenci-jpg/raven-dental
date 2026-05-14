# Raven Home — QA Review (2026-05-13)

> Reviewer: UI Designer agent
> Reference brief: `docs/19-UI-DESIGN-BRIEF.md`
> Source: live render at `http://localhost:8000/` (3500-line HTML, captured 20:49)
> Method: rendered HTML inspection (CSS, selectors, image refs) + on-disk image visual review
> Scope: home only (`route=common/home`)

---

## Verdict

**Needs ~12 fixes, mostly color-leak + 4 data-binding mismatches. The dark-band fix worked. Category images are cohesive enough — do NOT regenerate yet. Owner's "kategori imageları uymadı" complaint actually traces to (a) inconsistent compositional style in 2 of the 4 cat-main images and (b) those category images are NOT yet rendered on home — they live on disk only. Stop blaming the assets; the assets are fine. Wire them correctly first.**

---

## Section-by-section

### Header / Nav
- working: Inter font hits headings; menu strip is locked to Raven blue (`rgba(15,58,107,1)` = #0f3a6b) — confirmed in `.desktop-main-menu-wrapper::before {background: rgba(15,58,107,1); height: 43px}` (line 611). Logo, search, cart group all render.
- issue 1 (color leak — high impact): the GLOBAL `.btn` background = `rgba(15, 58, 141, 1)` = **#0f3a8d**, NOT #0f3a6b. This is a second, slightly more-saturated blue that conflicts with the design-token primary. 77+7 = **84 declarations** use #0f3a8d as either base or active. Net effect: every default Journal3 button (Sepete Ekle when logged-in, Tüm Ürünleri Gör in module 263, blog reply, post read-more, etc.) renders #0f3a8d, then `:hover` flips to #0f3a6b → buttons VISIBLY change hue on hover, which reads as a bug.
- issue 2 (color leak — moderate): `.title::after`, `.page-title::after`, `.menu-label`, `legend::after`, the carousel pagination active dot inside module-blocks-292 — all use `rgba(233, 102, 49, 1)` = **#e96631 (orange)**. 108+11 = 119 declarations. This is Journal3's secondary-color Variable leaking through. On home you mostly hide titles via `.title-divider:display:none`, but `.menu-label` (the "Yeni" / "Sale" pills on main-menu items 2, 13, 18, 19, 20, 21) still paints orange on the otherwise blue nav.
- issue 3 (mismatched alt text in side banner): module 259 second item links to `/el-aletleri` but `alt="Muayene Aletleri — Raven Dental"`. SEO drift, minor a11y concern.
- fix → see Priority list items 1, 2, 9.

### Hero slider + side banners
- working: master-slider 26 sits in a 75/25 split with two side banners (module 259) right of it. Both hero slides (hero-slide-1, hero-slide-2) are on-brand Raven navy with single hero objects; cohesive with the rest of the photographic system. Image sizing is correct: 960×450 @1x with 1920×900 @2x srcset.
- issue 1: `.module-master_slider-26 .ms-slide, .module-master_slider-26 .ms-container, .module-master_slider-26, .module-master_slider-26::before, .module-master_slider-26 .ms-slide-bgcont { border-radius: 10px }` (line 649). The 10 px corner-rounding fights davye's clinical/sharp rhythm. Brief §1.5 and §A both call for **sharp corners (0 px)**.
- issue 2 (data mismatch): side-banner-1 (image: alt="İmplantoloji Aletleri") LINKS to `/diagnostik-aletleri`. Wrong target. Side-banner-2 (alt="Muayene Aletleri") links to `/el-aletleri`. Wrong target.
- issue 3 (cropping): the master_slider config sets `"layout":"fillwidth"` + `"autoHeight":true` which means the rendered height of the hero is computed by image aspect (450/960 = 0.469). On a 1320 px container with 75% column = ~990 px slider width → ~464 px tall. That's a tall hero. davye doesn't do this. Brief §A constrained to 420 px.
- fix → see Priority list items 5, 6, 11.

### Trust Strip 86 (after row-bg fix)
- working: clean 6-card grid, 1 fr each, vertical 1 px divider, `.module-info_blocks-86 .info-block::before` is a 36×36 circle outlined in Raven blue containing the icomoon glyph. The 6 cards render in the correct order (ISO/CE/AISI/DİŞSİAD/Türk Üretici/Klinik Fiyat) with explicit-CSS overrides (no Variable leak). Background is white. Dark band gone here.
- issue 1 (icon emptiness — high impact): `.info-block::before` uses `font-family:icomoon` to render glyphs. The HTML emits NO `class="info-block-icon icon-*"` on items 1-6 — the markup is `<div class="info-block"><div class="info-block-content">…</div></div>` only. There's no icon glyph injected at all. The 6 outlined circles WILL render but with NO icon inside them. That's a confidence-bug: empty circles look like loading placeholders, not certified-quality markers. Even the brief §B specifically lists 6 line-icons that should sit in the circles.
- issue 2 (no section title): module 86 has no `<h3>` heading. Brief §B suggests no title — that's fine — but the `module-info_blocks-86 { padding: 0 !important }` collides with grid-row-top-2 padding rules. It currently renders edge-to-edge against grid-row-top-1 (hero). Add 24 px top margin to breathe.
- issue 3 (no hover transition delay): hover changes `background: var(--r-bg-alt)` (line 905) but the title color stays #070707. Brief expects also border-color → Raven blue on hover. Right now: nothing visible to user.
- fix → see Priority list items 3, 4.

### Branş Quick-Pick 290 — 9 circles
- working: row is a single horizontal flex grid. Circles are 73×73 with 1 px #cfcfcf border, hover flips border to #0f3a6b (line 1085). Captions are uppercase 10/600/#282828 — exact davye spec. Mobile carousel scroll-snap is correctly configured at 760 px breakpoint.
- issue 1 (data binding mismatch — HIGH impact): items 8 and 9 use the WRONG image PNG.
  - Item 8: title "Protez", link `/protez-aletleri`, image `brans-pedodonti-circle-1024x1024.png` ← should be a protez image (we don't have one)
  - Item 9: title "Çekim", link `/cekme-aletleri`, image `brans-cekim-circle-1024x1024.png` ← correct
  - Items 6 and 7 also have a label-to-link semantic miss: item 7 "Teşhis" → `/diagnostik-aletleri` (OK), but item 6 "Periodonti" → `/periodonti-aletleri` (OK; image `brans-periodontoloji-circle` matches).
  - The brief §C listed 9 specialties: Cerrahi, İmplantoloji, Endodonti, Ortodonti, Restoratif, Pedodonti, Periodontoloji, Teşhis, Çekim. Current render swapped Pedodonti out for **Protez** but reused the pedodonti image. Decision needed: either (a) commission a brans-protez-circle.png, OR (b) revert to brief's Pedodonti label and link to a pedodonti category.
- issue 2 (img sizing): `<img class="info-block-img" width="" height="" />` — no width/height attributes. CSS forces 73×73 so layout doesn't shift, but Core Web Vitals (CLS) will scold. Add `width="73" height="73"` to all 9 imgs OR have Journal3 resize cache emit them.
- issue 3 (over-large source): the source is `brans-*-circle-1024x1024.png` (~1 MB each, 1024² rendered at 73²). At 9 imgs = ~9 MB raw. Cache pipeline should emit a 146×146 (2x) variant. Currently the @2x srcset just repeats the same 1024-px URL. Fix: srcset to `brans-*-circle-146x146.png 1x, brans-*-circle-292x292.png 2x`.
- issue 4 (positioning): captions under Pedodonti/Restoratif/etc have descenders that risk overflow at 14 px line-height + 2-line wrap. "Periodontoloji" is 13 chars — at 10 px / 600 weight / 73 px container, it will wrap to 2 lines. Set max 2 lines with `-webkit-line-clamp: 2` or shorten to "Perio".
- fix → see Priority list items 7, 12.

### Title "En Yeniler" + Featured Products 27
- working: section title (module-title-147) renders `<h3>En Yeniler</h3>` and the route-common-home rule (line 808) applies the 3 px Raven-blue underline. **The underline IS rendering.** Confirmed via the `display:inline-block` + `border-bottom: 3px solid var(--r-blue-700)`. Module 27 renders 12 product cards in a swiper.
- issue 1 (still has cart-button color drift in logged-out): the login-gated CTA `body:not(.customer-logged) .product-thumb .button-group::before` is correctly hiding price and showing the "🔒 Giriş Yap / Kayıt Ol" pseudo-element with `background: #e8eef5; color: var(--r-blue-700)`. Looks OK.
- issue 2 (hover color leak — moderate): `module-products-27 ... btn-wishlist.btn:hover { color: rgba(233, 102, 49, 1) !important }` (line 707) — Journal3 default for icon hovers is orange. Hover on heart/compare icons paints them orange, NOT Raven blue. Inconsistent with brand.
- issue 3 (title-divider): `.title-divider` is hidden globally on `.route-common-home`. Good. But the title-module's outer container has `padding: 0` and `border-bottom: 1px solid var(--r-line-50)` — the davye pattern is that the 1 px hairline is the WIDTH of the parent container, and the section title sits as a 3 px overline on top of it. Currently the 1 px border-bottom exists but the inline-block title sits inside it without overlapping — visually correct, just verify on a screenshot.
- issue 4 (swiper arrow color): the swiper-buttons hover background = `rgba(221, 14, 28, 1)` = **#dd0e1c (RED)** (line 666, 707, 735, 743). On hover, prev/next arrows go red. Should be Raven blue. Same pattern in modules 27, 39, 292.
- fix → see Priority list items 9, 10.

### Klinik Toptan CTA 291
- working: module-banners-291 renders 1 banner item with image `bulk-cta-bg.jpg`, alt text + caption "KLİNİK & HASTANE TOPLU SİPARİŞ AVANTAJI". Link points to `/index.php?route=information/contact`. Image asset itself (on disk) is well-composed: deep navy, 3 instrument silhouettes on the right, ample left-third for text overlay.
- issue 1 (HIGH impact — banner caption visual is broken): the brief §F asked for a hero CTA banner with explicit `.raven-cta-banner__title` ("KLİNİK & HASTANE TOPLU SİPARİŞ"), `__sub` ("50+ adet üzeri özel fiyat · 48 saat kargo"), `__btn` ("Teklif Al"). The CSS class `.raven-cta-banner` exists in the stylesheet (line 957) AND defines a gradient bg with white button "Teklif Al". But the rendered HTML for module 291 is just a Journal3 module-banners structure with `<a><img/><div class="banner-text banner-caption"><span>KLİNİK & HASTANE TOPLU SİPARİŞ AVANTAJI</span></div></a>` — there's NO `.raven-cta-banner` wrapper, NO `.raven-cta-banner__btn`. The styles you wrote are unreferenced dead CSS. What's rendered is just the image with caption text floating on top via Journal3's default `.banner-text` positioning, with default Journal3 banner styling. Owner sees a flat image, not a styled CTA.
- issue 2: alt="İmplantoloji Aletleri — Raven Dental" — wrong alt for a bulk-CTA banner. Should be "Klinik Toptan Sipariş — Raven Dental".
- issue 3: image is generated as 1820×... but the cache requested `bulk-cta-bg-320x210h.jpg` (a tiny 320×210 thumb). On desktop this scales up to ~1320 wide — pixel-blurry on 1080p+ screens. Either commission a `1920×400` srcset or have Journal3 emit a `1320x200h` cache variant.
- fix → see Priority list items 8, 10.

### Title "Çok Aranan Branşlar" + 3 big branş banners 286
- working: title-module-163 renders `<h3>Çok Aranan Branşlar</h3>`. Module 286 renders 3 banner items 4-col each. Background images on disk are correct (cerrahi, implantoloji, endodonti, all 1408×768 navy w/ instruments on right two-thirds — the design intent for CSS text overlay on the left-third is feasible).
- issue 1 (HIGH impact — data mismatch): item 3 in module 286: caption text says "ENDODONTİ", image is `brans-endodonti-1408x768.jpg`, alt="Diagnostik Aletleri Kategorisi", link is `/diagnostik-aletleri`. The caption + image agree on Endodonti, but link + alt say Diagnostik. Same wrong-link bug as side-banner-1 of module 259. Fix: change link to `/endodonti-aletleri`, alt to "Endodonti Aletleri Kategorisi — Raven Dental".
- issue 2 (text-overlay positioning): brief §H said "CSS text overlay on left third, baked text NO". The rendered `.banner-text` is Journal3's default centered absolute positioning with light shadow — caption sits center-of-image, NOT left-third. Currently:
  - The `.banner-text` block has no override CSS in the home stylesheet. It uses Journal3 defaults.
  - Need to add: `.module-banners-286 .module-item .banner-text { position: absolute; left: 32px; top: 50%; transform: translateY(-50%); right: auto; color: white; font-size: 24px; font-weight: 700; letter-spacing: 1.5px; text-shadow: 0 2px 6px rgba(0,0,0,.5); }`
- issue 3 (image aspect): images are 1408×768 (1.83:1). Brief §H spec'd 420×240 (1.75:1) display. Render container varies. No CLS issues likely since aspect-ratio is close.
- fix → see Priority list items 6, 8.

### SEO Description Block 292
- working: 8 `<h2>` paragraphs render in order: Raven Dental kim, Cerrahi, İmplantoloji, Endodonti, Ortodonti, Restoratif/Pedodonti, Klinik Toplu Sipariş, Kalite Standartları. CSS overrides at line 1130 give h2 the 3 px Raven-blue underline correctly. Background `var(--r-bg-alt)` = #fafafa (slight contrast). Font is 14/1.7 — readable. **Solid SEO win.**
- issue 1 (carousel UI leaking into a static block): module 292 inherits Journal3 swiper-buttons CSS (lines 735-738) — `.module-blocks-292 .swiper-buttons div { background: rgba(44, 54, 64, 1); border: 4px solid white; border-radius: 50% }` and `hover background: rgba(221,14,28,1)` (red). Currently `:hover .swiper-buttons { display: block }` on this static text block could trigger phantom nav arrows. Need: `.module-blocks-292 .swiper-buttons { display: none !important }`.
- issue 2 (no "Devamını Oku" collapse): brief §L specified a 240 px max-height + fade-mask + collapse toggle. Currently the entire 700-word SEO block renders fully expanded. On mobile this is a lot of scroll. Fix: `.module-blocks-292 .expand-content { max-height: 240px; overflow: hidden; position: relative; }` + a button + JS toggle.
- issue 3 (no list-section title): davye uses an h2 readmore-title atop the block ("Hakkımızda" or "Raven Hakkında"). Currently the very first h2 ("Raven Dental — Türkiye'nin Üretici Diş Aletleri Merkezi") doubles as the section title. Acceptable.
- fix → see Priority list item 10.

### Footer
- working: Standard Journal3 4-col footer with module-side_products-39 in the top row (showing "EN ÇOK GÖRÜNTÜLENEN" tab). Footer below has product carousel cards rendered. The dark band issue did NOT affect footer (it's outside grid-row-top-N).
- issue 1: `.module-side_products-39 .side-product .btn-cart.btn:hover { color: rgba(233, 102, 49, 1) !important }` — orange hover color leak (same family of bugs as Section "En Yeniler"). Fix by unifying the orange→blue swap.
- issue 2: footer has no Raven phone/social/address column visible from the rendered modules; module 287 (phone CTA) was removed per context. If owner ever wanted brand-block + phone CTA back in footer col 1, that's a Phase 5 (per brief §N), not blocking.
- fix → see Priority list item 2.

---

## Owner complaints addressed?

### (A) Dark band — IS IT FIXED?
**YES.** Verified via direct CSS inspection:
- `.grid-row-top-2::before { display:block; left:0; width:100vw }` — NO background property. (was: `background: rgba(44,54,64,1)`)
- `.grid-row-top-3::before` — same, no background.
- `.grid-row-top-4::before` — same, no background.
- `.grid-row-top-6::before` — same, no background.
- `.grid-row-top-5::before { background: rgba(255,255,255,1) }` — explicitly white.
- `.grid-row-top-1::before` — no background.

The 7 rows now flow on the page's default white. The previously-leaking `rgba(44, 54, 64, 1)` color (dark anthracite) still exists in the HTML CSS but ONLY in unrelated selectors: `.tags a`, `.btn-dark.btn`, `.swiper-buttons div` (the nav arrow circles inside carousels), and `.route-product-product .additional-images .swiper-buttons div`. None of those are full-row bgs. Owner's complaint is resolved.

**Evidence:** grep `grid-row-top-[0-9].*background` in `/tmp/raven-home.html` returns only `grid-row-top-5{background:rgba(255,255,255,1)}`. All others are bare.

### (B) Category image uniformity — analysis
The 10 generated category images live on disk at `/image/catalog/raven/home/`:
- `cat-aerator.jpg`, `cat-anguldurva.jpg`, `cat-piyasemen.jpg`, `cat-micromotor.jpg` — 4 single-handpiece images
- `cat-main-el-aletleri.jpg`, `cat-main-sarf.jpg`, `cat-main-raven-cerrahi.jpg`, `cat-main-elektronik.jpg` — 4 main-category multi-instrument fans
- `cat-protez.jpg`, `cat-islem.jpg` — 2 odd ones (anatomical models + tray)

**Cohesion vs. branş set (9 circles + 3 banners):**
- The 4 single-handpiece cat-aerator/anguldurva/piyasemen/micromotor — **perfectly cohesive**. Same navy gradient, same backlight glow, same single-hero-object framing. Could swap with any branş circle and look at home.
- The 4 cat-main-* — **slightly divergent**. Same color family BUT compositional language is "instrument fan on a surface" (multiple instruments) vs. "single instrument standing up" (the rest of the system). The cat-main-raven-cerrahi.jpg has 8 instruments fan-spread; the rest of the system shows 1 instrument. This is **the actual source of "kategori imageları uymadı"** — the multi-instrument fan composition breaks the visual rhythm.
- cat-protez.jpg: shows pink-gum dental anatomical models. The pink tones are the FIRST and ONLY warm color in an otherwise all-cool-navy system. Conspicuous diverger.
- cat-islem.jpg: stainless steel tray + tiny instruments. Minimalist; visually quiet; not obviously dental. Reads more like "lab supply" than "dental procedure".

**The HOME PAGE DOES NOT YET REFERENCE THESE 10 IMAGES.** They are on disk only. The home shows hero, side banners, trust strip, 9 branş circles, products, CTA, 3-banner row, SEO block. There is no "category card grid" rendering at all on home that uses these `cat-*.jpg` files. So owner's complaint about "kategori imageları uymadı" may be (a) from a different page (category landing pages?), or (b) anticipatory ("they don't look like they'll fit when we add them").

**Recommendation: do NOT regenerate yet. Wire them first — see Priority list item 13.** If after wiring the cat-main-* still feel out, re-prompt only those 4 (fan composition → single hero instrument composition).

---

## Priority fix list (ordered, top 12)

1. **Global brand-color unification (#0f3a8d → #0f3a6b).** In Journal3 admin → Skin → Variables → BTN_PRIMARY (or wherever `rgba(15, 58, 141, 1)` is defined), change to `rgba(15, 58, 107, 1)`. Affects 77+ CSS declarations. Net effect: btn no longer hue-shifts on hover.

2. **Global orange→blue swap (#e96631 → #0f3a6b) for non-warning UI.** In Journal3 admin → Skin Variables → COLOR_SECONDARY (the orange), change to `rgba(15, 58, 107, 1)`. This kills the 119 orange leaks in `.title::after`, `.page-title::after`, `.menu-label`, hover colors on btn-wishlist / btn-compare, swiper pagination active dots, blog post-stats scrollbars. Single switch.

3. **Trust Strip 86 — add icons.** Either:
   - (a) In admin, set each info-block-86 module-item's "Icon" field to its line-icon class (icomoon: `icon-shield-check`, `icon-ce`, `icon-microscope`, `icon-handshake`, `icon-factory`, `icon-percent-tag`), OR
   - (b) Add 6 inline SVG via `<div class="info-block-icon">...</div>` in the module config.
   The 36×36 outlined circles currently render empty (no glyph inside). Brief §B identified this asset gap; the brief asset list §4.3 has the 6-icon sprite ready to be created.

4. **Trust Strip 86 — top margin.** Add `.module-info_blocks-86 { margin-top: 24px !important }` (currently 0 — strip touches hero).

5. **Hero — remove rounded corners.** In `<style>` block override:
   ```css
   .module-master_slider-26,
   .module-master_slider-26 .ms-slide,
   .module-master_slider-26 .ms-container,
   .module-master_slider-26::before,
   .module-master_slider-26 .ms-slide-bgcont { border-radius: 0 !important }
   ```

6. **Data binding — fix 4 mismatched link/image/alt triples.**
   - Module 259 item 1 (side-banner-1): link `/diagnostik-aletleri` → change to `/implantoloji-aletleri` (alt says İmplantoloji).
   - Module 259 item 2 (side-banner-2): link `/el-aletleri` → leave OR change alt to "El Aletleri — Raven Dental".
   - Module 290 item 8 (Protez): image `brans-pedodonti-circle...` → either rename label to "Pedodonti" + leave link `/pedodonti-aletleri` (need to verify category exists), OR commission a `brans-protez-circle.png`.
   - Module 286 item 3 (Endodonti): link `/diagnostik-aletleri` → change to `/endodonti-aletleri`, alt "Diagnostik..." → "Endodonti Aletleri Kategorisi — Raven Dental".

7. **Branş circle img performance.** Generate Journal3 cache variants `brans-*-circle-146x146.png` and `brans-*-circle-292x292.png`, then update srcset. Currently each 73×73 circle loads a 1024×1024 source = 9 × ~1 MB = ~9 MB of PNGs on first paint.

8. **Klinik Toptan CTA — rebuild as proper raven-cta-banner.** Either:
   - (a) Switch module 291 from Banners → HTML Block, paste the brief §F markup using the existing `.raven-cta-banner` CSS that's already loaded (lines 957-996), OR
   - (b) Keep module 291 as Banners but write a CSS override that styles `.module-banners-291 .module-item` AS the raven-cta-banner (full bg, white title text positioned left, "Teklif Al" pseudo-button overlay).
   - Recommend (a) — it gives a real `<a class="raven-cta-banner__btn" href="/index.php?route=information/contact&type=toptan">Teklif Al →</a>` button rather than relying on the whole-image link.

9. **3-Banner row 286 — CSS text overlay positioning.** Currently caption is Journal3-default centered. Add to home stylesheet:
   ```css
   .module-banners-286 .module-item { position: relative }
   .module-banners-286 .module-item .banner-text {
     position: absolute; left: 32px; top: 50%; transform: translateY(-50%);
     right: auto; max-width: 35%; text-align: left;
   }
   .module-banners-286 .module-item .banner-text span {
     color: white; font-family: 'Inter', sans-serif;
     font-size: 28px; font-weight: 700; letter-spacing: 1.5px;
     text-shadow: 0 2px 8px rgba(0,0,0,.5); display: block;
   }
   .module-banners-286 .module-item:hover { transform: translateY(-4px); box-shadow: 0 4px 12px rgba(0,0,0,.08) }
   ```

10. **Swiper arrow colors.** Override in home stylesheet for modules 27, 39, 213, 186, 292:
    ```css
    .module-products .swiper-buttons div:not(.swiper-button-disabled):hover,
    .module-side_products .swiper-buttons div:not(.swiper-button-disabled):hover {
      background: var(--r-blue-700) !important;
    }
    .module-blocks-292 .swiper-buttons { display: none !important }  /* static block, no arrows */
    .module-blocks-292 .swiper-pagination-bullet.swiper-pagination-bullet-active {
      background-color: var(--r-blue-700) !important;
    }
    ```

11. **Side banners — fix alt text drift.** Re-write alts to match links: item 1 `İmplantoloji Aletleri Setleri`, item 2 `El Aletleri Koleksiyonu` (or whatever the actual link target's seo name is).

12. **Branş circle caption truncation.** Add `.module-info_blocks-290 .info-block-title { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; max-height: 28px }`. Prevents "Periodontoloji" wrapping past the desired 2-line bound.

13. **Wire the 10 category images.** Currently they sit on disk unused. Owner's "kategori imageları uymadı" is premature — wire them into a NEW module first. Recommended placement: a category-card row between Hero+TrustStrip and BranşQuickPick (use module ID 37 — already exists, currently empty per context). 4 main category cards (el-aletleri, sarf, raven-cerrahi, elektronik) at 320×180, 4-col grid. The 4 cat-aerator/anguldurva/piyasemen/micromotor + cat-protez + cat-islem are likely meant for INSIDE el-aletleri / elektronik category landing pages, NOT home.

---

## Image asset review

- **9 branş circles** (`brans-{cerrahi,implantoloji,endodonti,ortodonti,restoratif,pedodonti,periodontoloji,teshis,cekim}-circle.png`): cohesive **YES**. Same radial-blue background, single instrument, cool/steely lighting, soft floor shadow. Reads as a coherent set. **Keep.**
- **3 branş banners** (`brans-{implantoloji,cerrahi,endodonti}.jpg`): proper proportion for TR overlay **YES**. Each is 1408×768 with the instrument cluster on the right two-thirds; left ~35% is clean negative space ready for white text overlay. **Keep.**
- **10 category images** — cohesive with branş set:
  - cat-aerator, cat-anguldurva, cat-piyasemen, cat-micromotor (4 single-handpiece): **YES, cohesive.** Same composition as branş circles, same blue background.
  - cat-main-el-aletleri, cat-main-sarf, cat-main-raven-cerrahi, cat-main-elektronik (4 multi-instrument fans): **PARTIAL.** Color family matches but composition diverges (fan-spread vs. single-object). On a category-card grid this might read fine because the row is uniform; on the home page mixed with branş circles, would clash.
  - cat-protez: **NO.** Pink anatomical models break the all-cool-blue language. Only image with warm tones.
  - cat-islem: **WEAK.** Tray is dental-adjacent but reads more "lab supply"; minimalist, low-info.
- **Hero slides** (hero-slide-1, hero-slide-2): cohesive. Same family.
- **Side banners 1+2** (side-banner-1, side-banner-2): cohesive.
- **Bulk CTA bg** (bulk-cta-bg.jpg): cohesive. Three instruments standing on right; left two-thirds clean for text overlay.

**Recommendations for re-generation:**
- **Re-prompt cat-protez.jpg:** change subject from "pink anatomical model" to "single denture/crown prosthetic instrument (e.g. acrylic spatula, crown carrier) on the standard navy gradient". Keep the gradient + lighting consistent with branş set. Prompt tweak: "studio shot of single prosthodontic instrument, polished stainless steel, on dark navy gradient #0a2547 → #1f5499 background, soft top-left key light, subtle floor shadow, same visual treatment as the surgical scalpel reference image".
- **Re-prompt cat-islem.jpg:** stronger dental signal. Try: "single sterile dental processing tray with two stainless cups and a clear procedure tool, on the same navy gradient, single hero object framing". Right now it's quiet enough that owner might read it as "tea set".
- **Re-prompt cat-main-* (4 main-category images):** ONLY if you want to render them on home. If they live on category landing pages (which I recommend), the fan composition is fine — category pages tolerate a louder composition. Decision tree: if home placement → re-prompt to single hero instrument; if category-landing-only → keep as-is.
- **Generate brans-protez-circle.png:** if Section C Branş Quick-Pick will retain "Protez" as item 8. Prompt: "single dental prosthetic instrument (crown carrier or wax knife), centered on radial blue gradient, exact same lighting + background as `brans-cerrahi-circle.png`".

---

## Design-token consistency

- **Inter font:** correctly enforced. Body + headings + module-titles all use Inter via the global `body { font-family: 'Inter', ... }` override at line 803-805. **PASS.**
- **Stale `#0d52d6`:** **PASS** — only 1 hit in the entire HTML, and that hit is inside a different RGB string that just happens to contain those digits. The 92 instances mentioned in the context have been correctly converted to `#0f3a6b` (`rgba(15, 58, 107, 1)` × 92).
- **`#0f3a8d` second-blue contamination:** **FAIL.** This is the highest-priority remaining color leak — 77+7=84 occurrences of `rgba(15, 58, 141, 1)`. Fix in Skin Variables (see Priority 1).
- **`#e96631` orange contamination:** **FAIL.** 119 occurrences of `rgba(233, 102, 49, 1)` across `.title`, `.page-title`, `.menu-label`, hover-color on icon buttons, blog post-stats. Fix in Skin Variables (see Priority 2).
- **`--r-*` design tokens:** correctly defined in `:root` block at line 768-800. Used by all custom raven-* CSS. **PASS.**
- **`route-common-home` section-title atom:** correctly defined at line 808-820 with `border-bottom: 3px solid var(--r-blue-700)`. **PASS** — applied to titles 147 (En Yeniler) and 163 (Çok Aranan Branşlar).
- **Container width:** the brief calls for 1320 px throughout. Journal3 default container is unclear from this HTML alone (no explicit `max-width: 1320px` on the .container element). **VERIFY** in Journal3 admin → Skin → Page Width.

---

## Footnote on what's working really well

- The design-token CSS layer is clean. `--r-blue-700`, `--r-bg-alt`, `--r-line-200` etc. are correctly defined and used in every custom module override.
- The login-gated price pseudo-element is a clever workaround for an opencart restriction — `body:not(.customer-logged) .product-thumb .button-group::before { content: '🔒 Giriş Yap / Kayıt Ol' }` cleanly replaces the cart CTA without backend changes.
- The 9-circle Branş row CSS is essentially a perfect davye-spec implementation (73 px circle, 1 px #cfcfcf border → #0f3a6b on hover, 10 px / 600 / #282828 caption).
- The SEO Description Block CSS treatment with per-h2 3 px blue underlines is the davye `boshtml_15` pattern executed faithfully — that single section will be a real SEO win.
- The dark-band fix is clean and complete. Move on from that complaint.

Owner pushback on "category images" is in good faith but premature — the actual visual problems with `cat-protez.jpg` (warm pink) and `cat-islem.jpg` (weak signal) are easy re-prompts, while `cat-main-*` decision depends on where those images will live. None are deal-breakers. The far more visible issues are the **#0f3a8d/#e96631 color leaks** (Priority 1+2) and the **4 link/image mismatches** (Priority 6) — fix those and the page snaps to the davye-clinical-catalog feel the brief promised.
