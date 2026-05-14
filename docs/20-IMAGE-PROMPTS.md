# 20 — IMAGE PROMPTS: Raven Dental Home Page Asset Spec

> Master prompt document for the Raven Dental home page redesign (Journal3 theme).
> 36 distinct AI-image-generation prompts. Paste into Midjourney v6, DALL-E 3, Google Imagen, or Flux Pro.
> Generated images go to `/image/catalog/raven/home/` and supporting paths.

---

## 1. Executive Summary

Raven Dental is a Turkish B2B manufacturer-direct dental instrument supplier. The visual language for this asset set is **clinical, premium, manufacturer-grade**: think medical-device catalog meets industrial product photography, never consumer e-commerce. Every image must telegraph three things at a glance: (1) **surgical-grade quality** (brushed stainless steel, AISI 304/420/440 finish, precise machined edges), (2) **manufacturer authority** (studio-controlled lighting, generous negative space, no clutter, no human hands), and (3) **a unified Raven-blue identity** (deep clinical cobalt `#0f3a6b` fading to navy `#0a2547`).

The set divides into five visual treatments that must remain internally consistent:

- **Hero/Banner photographs (Group A)** — wide cinematic product photography, gradient blue backgrounds, single or grouped instruments as hero.
- **Branş circle icons (Group B)** — a 9-image cohesive set, each instrument cropped to square frame, lighter blue gradient (`#ebf2fa → #cfdbe8`) so they pop on the white home page. Identical lighting angle, shadow softness, and instrument scale across all 9.
- **SVG line icons (Group C)** — 24×24 monoline icons, 1.5px stroke, `currentColor`, no fill. AI generates a mockup; engineer hand-converts to SVG.
- **Brand wordmarks (Group D)** — clean Inter Bold typography on white, no embellishment, a coherent "house of sub-brands" feel.
- **OG share image (Group E)** — the only asset with text baked in (controlled, small) for social previews.

The **anti-pattern** to avoid throughout: AI-art "fingerprint" giveaways (over-saturated chrome, fake reflections, melted geometry, illegible faux-text, hyperreal sheen). We want clean, slightly understated, catalog-real.

---

## 2. Master Prompt Prefix (paste at start of EVERY Group A & B prompt)

```
Studio product photography, clinical premium dental instrument catalog,
surgical-grade stainless steel finish AISI 420, brushed satin texture,
deep Raven-blue gradient background hex #0f3a6b transitioning to #0a2547,
soft diffused key light from upper-left at 35 degrees, gentle fill from
right, subtle rim light separating instrument from background, soft
contact shadow directly under instrument, sharp focus on instrument
edges, photographic realism, no illustration, no 3D-render fakeness,
generous negative space, rule-of-thirds composition, instrument slightly
off-center, medical device catalog aesthetic, B2B manufacturer feel,
shot on Phase One IQ4 medium format with 120mm macro lens at f/8,
focus-stacked sharpness, museum-grade product photography, 8K
```

Use this as the **foundation**. Then layer the asset-specific description on top.

---

## 3. Master Negative Prompt (apply to EVERY raster prompt)

```
no people, no hands, no faces, no human body parts, no patients,
no dentists, no doctors, no consumer e-commerce styling,
no toothpaste, no toothbrush, no teeth, no gums, no blood,
no plastic toys, no cartoon, no illustration, no anime,
no flat-design, no isometric, no 3D-render plastic look,
no chrome reflections that look fake, no melted metal,
no text, no letters, no numbers, no logos, no watermarks,
no brand names (no AVEN, no RAVEN baked in unless requested),
no email addresses, no phone numbers, no URLs,
no English text, no signage, no labels on instruments,
no AI-art fingerprint, no over-saturated colors, no HDR halo,
no lens flare, no bokeh balls, no harsh flash, no overexposure,
no clutter, no busy background, no multiple competing focal points,
no rust, no scratches, no dirt, no fingerprints, no smudges,
no medical waste, no surgical gloves, no masks, no syringes,
no needles in close-up gore context
```

**Midjourney syntax:** append `--no people, hands, text, logo, watermark, illustration, cartoon, 3d-render, plastic, blood, teeth, gloves` to each prompt.

**DALL-E/ChatGPT:** rephrase as polite request — "Please ensure no people, no text overlays, no visible logos or watermarks, no English signage, and a pure clinical product-only composition."

---

## 4. The 36 Prompts

### GROUP A — HERO & BANNER PHOTOGRAPHS

---

## #1 — Hero Slide 1 · ISO 9001 / CE / DİŞSİAD authority

**File:** `/image/catalog/raven/home/hero-slide-1.jpg` · **Display size:** `1920×840` (display 960×420)

**Primary prompt (Midjourney v6):**
```
[MASTER PREFIX] An expansive overhead flat-lay arrangement of premium
surgical-grade dental instruments displayed on a brushed stainless-steel
medical tray: a polished dental mirror with stainless handle on the left,
two slim periodontal probes laid diagonally, a Gracey curette #5/6,
a surgical scalpel handle BP-3 without blade, and a delicate root
elevator placed parallel to the tray edge. Instruments rest with about
two centimeters of breathing space between each piece. The tray fills
the lower two-thirds of the frame, while the upper third reveals the
deep Raven-blue gradient background bleeding from cobalt #0f3a6b on
the right to deep navy #0a2547 on the left, with a faint cool-blue
glow emanating from the left edge as if from an off-camera softbox.
Subtle radial vignette darkens the corners. Composition follows the
rule of thirds with the dental mirror placed on the left intersection.
Soft diffused key light from upper-left at 30 degrees produces gentle
gradated shadows beneath the instruments — never hard, never multiple.
The metal surfaces show controlled specular highlights along their
spines, brushed-satin finish never mirror-chrome. Atmosphere: hushed,
authoritative, museum-grade dental device catalog cover. Inspired by
the product photography of Peter Lippmann and the medical-catalog
aesthetic of Karl Storz endoscopy brochures. NO text, NO labels,
NO logos anywhere in the frame.
--ar 16:7 --style raw --v 6 --q 2
```

**DALL-E 3 / ChatGPT variant:**
> Create a wide cinematic overhead photograph of premium dental instruments arranged on a brushed stainless-steel medical tray. The instruments — a dental mirror, two periodontal probes, a Gracey curette, a scalpel handle (no blade), and a root elevator — are laid out with generous spacing between them, each placed deliberately like museum artifacts. The tray sits in the lower portion of the frame. Above and behind it, a deep clinical-blue gradient background flows from cobalt blue on the right to a darker navy on the left, with a soft cool glow leaking in from the left as if a studio softbox were just out of frame. Lighting is soft, even, and clinical — like a high-end medical device catalog. The instruments must look authentically surgical (not consumer toothbrush territory): polished brushed-stainless, satin finish, no exaggerated chrome reflections. Compose with the rule of thirds, leave abundant negative space, and ensure no people, no text, no logos, and no English signage appear anywhere. Aspect ratio 16:7, photorealistic studio product photography.

**Negative (asset-specific):**
no toothpaste tubes, no dental chair, no patient mouth, no rubber gloves, no curing light, no x-ray film, no plastic prophy cups, no English instrument labels, no overhead surgical lamp visible

---

## #2 — Hero Slide 2 · Bulk clinic & hospital orders

**File:** `/image/catalog/raven/home/hero-slide-2.jpg` · **Display size:** `1920×840`

**Primary prompt (Midjourney v6):**
```
[MASTER PREFIX] Wide cinematic studio shot of a curated stack of premium
sealed surgical instrument kits and modular sterilization trays arranged
on a smooth dark-blue surface — a foreground stack of three closed
stainless-steel autoclave cassettes with subtle perforation patterns,
neatly aligned in slightly staggered formation, and beside them two
unmarked navy-blue cardboard kit boxes with clean unbranded surfaces
suggesting wholesale quantity. In the deep background, slightly out of
focus at f/4 depth, a soft silhouette of a stainless-steel medical
storage cart with multiple drawer levels, lit only by ambient blue
light — suggesting hospital infrastructure without revealing brand or
detail. The deep Raven-blue gradient washes from cobalt #0f3a6b in the
upper right to deep navy #0a2547 in the lower left. A faint volumetric
light beam from the upper-left rakes across the boxes, catching the
edges of the steel cassettes with crisp specular highlights. Mood:
manufacturer-direct, wholesale-scale, premium quiet confidence. Layout
leaves the left third intentionally empty for headline overlay in HTML.
Composition inspired by Hermès leather goods catalog photography and
Bang & Olufsen product stills. NO text, NO logos, NO labels on boxes.
--ar 16:7 --style raw --v 6 --q 2
```

**DALL-E 3 / ChatGPT variant:**
> Wide cinematic photograph of a curated stack of premium sealed dental instrument kits and sterilization trays arranged on a dark blue surface in a quiet studio setting. The foreground features three stacked stainless-steel autoclave cassettes (perforated lid pattern visible) and two unmarked navy cardboard kit boxes, all suggesting wholesale or hospital-scale quantity. Behind them, slightly out of focus, the silhouette of a hospital-grade stainless storage cart fades into the deep Raven-blue gradient background. A volumetric beam of soft cool light from the upper left rakes across the boxes, catching the edges of the steel cassettes. The left third of the image is intentionally empty negative space — clean and uncluttered — so that text overlay can be added later. Photographic realism, no labels, no brand marks, no people. Aspect ratio 16:7.

**Negative:**
no visible brand stickers on boxes, no barcodes, no shipping labels, no people pushing cart, no patient charts, no clipboards with English text, no warning signs, no biohazard symbols

---

## #3 — Side Banner: İmplant Setleri

**File:** `/image/catalog/raven/home/side-banner-1.jpg` · **Display size:** `480×400` (display 240×200)

**Primary prompt (Midjourney v6):**
```
[MASTER PREFIX] Tight three-quarter studio close-up of a single titanium
dental implant abutment standing upright in the center-right of the
frame, paired with a slender stainless-steel implant driver hex-tool
laid diagonally beside it on a polished dark-blue glass surface that
reflects a soft gradient blur. The titanium abutment has a precision
machined hex connection at the top and a subtle anodized gold-tinted
collar at the base, catching a delicate highlight along its edge.
The driver tool's tip points toward the abutment, creating quiet
narrative tension. Background: vertical Raven-blue gradient from
cobalt #0f3a6b at the top to deep navy #0a2547 at the bottom, with
a soft radial vignette concentrating attention on the implant. Light:
single large overhead softbox creating gentle wraparound highlight on
the titanium, plus a low rim-light from camera-right giving the
abutment edge crisp definition. Photographic, sharp, slight macro
feel — but never sterile microscope close-up. Mood: precision
engineering, surgical premium. Composition: instrument occupies the
right two-thirds, left third is negative blue space.
--ar 6:5 --style raw --v 6 --q 2
```

**DALL-E 3 / ChatGPT variant:**
> A tight three-quarter studio close-up photograph of a single titanium dental implant abutment standing upright in the right-center of the frame, accompanied by a slender stainless-steel implant driver tool laid diagonally beside it. The surface beneath is a polished dark-blue glass reflecting a faint gradient. The titanium has a precision-machined hex connection and a subtly anodized gold-tinted collar. The background is a vertical gradient from clinical cobalt blue at the top to deep navy at the bottom. Lighting is soft and even, like a high-end watch catalog. Generous negative space on the left. No people, no text, no labels. Aspect ratio close to 6:5.

**Negative:**
no jawbone illustration, no x-ray, no gum tissue, no full implant fixture with screw threads buried in bone diagram, no surgical drape, no English size markings on abutment

---

## #4 — Side Banner: Cerrahi Setler

**File:** `/image/catalog/raven/home/side-banner-2.jpg` · **Display size:** `480×400`

**Primary prompt (Midjourney v6):**
```
[MASTER PREFIX] Elegant fan-spread arrangement of five premium surgical
dental instruments radiating outward from a single pivot point in the
lower-left like a deck of cards: a straight Bein root elevator, a
curved periotome, Cryer elevator (left), Cryer elevator (right), and
a surgical mirror handle — all polished stainless steel with brushed
satin finish, slim ergonomic handles in matching uniform style.
Instruments occupy the right and upper portion of the frame, fanning
upward and to the right. Background: vertical Raven-blue gradient
from cobalt #0f3a6b top to deep navy #0a2547 bottom. Soft diffused
key light from upper-left at 30 degrees produces unified gradient
shadows that follow the same direction beneath each instrument
(critical — never crossing shadows). The instruments cast a single
unified soft contact shadow as if photographed under a single
overhead softbox at moderate distance. Composition: rule-of-thirds,
fan pivot point on the lower-left intersection. Photographic
catalog realism, surgical-instrument-manufacturer brochure aesthetic
inspired by Hu-Friedy and Karl Schumacher product pages.
--ar 6:5 --style raw --v 6 --q 2
```

**DALL-E 3 / ChatGPT variant:**
> An elegant fan-spread arrangement of five premium dental surgical instruments — a straight Bein root elevator, a curved periotome, two Cryer elevators (one left, one right), and a surgical mirror handle — all polished stainless steel with brushed satin finish and matching slim ergonomic handles. The instruments fan upward and to the right from a pivot point in the lower-left corner of the frame, like a deck of cards. The background is a vertical gradient from clinical cobalt blue at the top to deep navy at the bottom. Soft, unified shadow beneath the fan suggests a single overhead softbox. No people, no text, no labels. Aspect ratio approximately 6:5.

**Negative:**
no mismatched handle styles, no plastic handles, no colored grips, no rust spots, no extra instruments cluttering frame, no surgical tray underneath, no English engraving on shafts

---

## #5 — Klinik Toptan CTA Background

**File:** `/image/catalog/raven/home/bulk-cta-bg.jpg` · **Display size:** `2640×400` (display 1320×200)

**Primary prompt (Midjourney v6):**
```
[MASTER PREFIX] Ultra-wide panoramic banner background, deep Raven-blue
atmospheric gradient flowing horizontally from rich cobalt #0f3a6b on
the left to deep navy #0a2547 on the right, with a faint volumetric
light beam from the upper-left adding subtle dimension. On the right
third of the frame, a ghostly silhouette of three premium dental
forceps standing upright in a row, rendered at approximately 20%
opacity as if dimly backlit through frosted glass — barely visible,
suggesting depth without competing for attention. The left two-thirds
remain almost entirely empty deep-blue gradient space, intended for
HTML text overlay. Surface beneath has a polished glass reflection
hint at the very bottom edge — just a faint mirror gradient.
Atmosphere: quiet, authoritative, B2B negotiation room mood. The
silhouette must be subtle, almost subliminal — never sharp, never
detailed. Composition: 6.6:1 wide aspect, all action in the right
third, left two-thirds empty. If 6.6:1 ratio fails in Midjourney,
fall back to 16:9 and engineer crops in CSS.
--ar 16:9 --style raw --v 6 --q 2
```

**DALL-E 3 / ChatGPT variant:**
> An ultra-wide panoramic banner background composed almost entirely of a deep clinical-blue gradient flowing horizontally from cobalt blue on the left to a darker navy on the right. On the right third of the frame, a very faint silhouette of three dental forceps standing upright is just barely visible at around 20 percent opacity, as if dimly backlit through frosted glass. The left two-thirds is intentionally empty negative space for text overlay. Quiet, authoritative B2B mood. No people, no text, no logos. Generate at the widest aspect ratio available (ideally 16:9 or wider) — final crop will be 6.6:1.

**Negative:**
no sharp instruments competing with empty space, no busy patterns, no geometric overlays, no light beams across left side, no logos, no text, no symbols, no medical icons floating in space

**Note to engineer:** if MJ refuses 6.6:1, generate at 16:9 then crop top and bottom in CSS via `background-position: center; background-size: cover;`.

---

## #6 — Featured Side Banner: En Yeniler

**File:** `/image/catalog/raven/home/feat-en-yeniler.jpg` · **Display size:** `600×900` (display 300×450)

**Primary prompt (Midjourney v6):**
```
[MASTER PREFIX] Vertical hero shot of a single innovative-looking
premium dental instrument — a modern ergonomic implant placement
driver with a contoured satin-finish stainless handle and gold-anodized
torque collar — standing upright in the center of the frame, slightly
tilted toward the camera at 5 degrees. The instrument occupies the
central vertical axis from about 25% from the top to 75% from the top.
Background: vertical Raven-blue gradient from cobalt #0f3a6b top to
deep navy #0a2547 bottom, with a subtle radial spotlight glow centered
behind the instrument tip giving a "newest arrival" stage feel. Light:
clean overhead softbox plus delicate side rim-light from camera-left
catching the curve of the handle. The instrument has a quietly modern
form — clearly a new generation tool, not a 1970s classic. Photographic
realism, surgical premium catalog style, no glow effects, no halos.
Composition: rule-of-thirds, centered subject with generous breathing
room above and below.
--ar 2:3 --style raw --v 6 --q 2
```

**DALL-E 3 / ChatGPT variant:**
> A vertical hero photograph of a single modern dental implant placement driver — a premium tool with a contoured satin-finish stainless steel handle and a gold-anodized torque collar at the top. The instrument stands upright in the center of the frame, slightly tilted toward the camera. The background is a vertical gradient from clinical cobalt blue at the top to deep navy at the bottom, with a subtle radial glow centered behind the instrument suggesting a "spotlight on newest arrival" feel. The tool should look quietly modern and clearly new-generation — not vintage. Soft clean studio lighting, no halos or sparkle effects, no people, no text. Aspect ratio 2:3.

**Negative:**
no vintage classic instruments, no sparkle effects, no "new" stickers, no badges, no English text "NEW", no glow halos, no exclamation graphics

---

## #7 — Featured Side Banner: Çok Satanlar

**File:** `/image/catalog/raven/home/feat-cok-satan.jpg` · **Display size:** `600×900`

**Primary prompt (Midjourney v6):**
```
[MASTER PREFIX] Vertical composition of a tightly grouped cluster of
the most popular dental surgical instruments fanning vertically from
a central pivot: a #150 upper universal extraction forceps, a #151
lower universal extraction forceps, a straight Bein elevator, and a
curved Cryer elevator — four instruments fanning upward like a tight
bouquet, handles meeting at the bottom-center of the frame, working
ends pointing up and slightly outward. All polished surgical stainless,
matching slim handles, brushed satin finish. The fan occupies the
central vertical axis from about 15% from top to 85% from top.
Background: vertical Raven-blue gradient from cobalt #0f3a6b top to
deep navy #0a2547 bottom. Light: large overhead softbox produces
unified shadow direction beneath the instruments, gentle gradient
shadow at the base of the fan. Premium catalog mood, "fan favorites"
suggestion through the bouquet form. Photographic catalog realism.
--ar 2:3 --style raw --v 6 --q 2
```

**DALL-E 3 / ChatGPT variant:**
> A vertical photograph of a tightly grouped vertical fan of four popular dental surgical instruments: an upper universal extraction forceps, a lower universal extraction forceps, a straight Bein elevator, and a curved Cryer elevator. The handles meet at the bottom center of the frame and the working ends fan upward and slightly outward like a bouquet. All instruments share a matching slim handle design in polished surgical stainless steel with brushed satin finish. The background is a vertical gradient from clinical cobalt blue at the top to deep navy at the bottom. Unified shadow direction beneath the fan. Premium catalog feel, no people, no text. Aspect ratio 2:3.

**Negative:**
no consumer toothbrush, no electric handpiece, no plastic anything, no colored handles, no "bestseller" badge, no ribbons, no awards, no English text

---

## #8 — Featured Side Banner: Cerrahi Aletler

**File:** `/image/catalog/raven/home/feat-cerrahi.jpg` · **Display size:** `600×900`

**Primary prompt (Midjourney v6):**
```
[MASTER PREFIX] Vertical composition of a complete surgical kit
arrangement viewed from a slight high-angle: an open stainless-steel
sterilization cassette in the lower portion of the frame holding a
neat row of six surgical instruments (Bein elevator straight, Bein
elevator curved, periotome, scalpel handle BP-3, periosteal elevator
Molt #9, needle holder Mayo-Hegar) all parallel and uniformly spaced.
Above the cassette, the deep Raven-blue gradient background fills the
upper two-thirds — cobalt #0f3a6b at top fading to deep navy #0a2547
where it meets the cassette. The cassette is rotated 5 degrees from
the horizontal for visual interest. Each instrument shows uniform
brushed-stainless finish, satin not mirror. Light: overhead softbox
plus subtle backlight rim catching the cassette edge. Composition:
cassette occupies lower 40% of frame, empty negative blue space
above. Mood: complete professional surgical kit, ready-for-clinic.
Photographic catalog realism.
--ar 2:3 --style raw --v 6 --q 2
```

**DALL-E 3 / ChatGPT variant:**
> A vertical photograph viewed from a slight high angle: a stainless-steel sterilization cassette in the lower portion of the frame holds a neat parallel row of six surgical instruments (root elevators, a periotome, a scalpel handle, a periosteal elevator, a needle holder), all evenly spaced and matched in finish. The cassette is rotated about 5 degrees from horizontal. Above the cassette fills the deep Raven-blue gradient sky — clinical cobalt at top fading to deep navy where it meets the tray. All instruments share a brushed satin stainless finish. No people, no text, no labels. Aspect ratio 2:3.

**Negative:**
no opened sterilization pouches, no autoclave indicator strips, no English markings on cassette, no biohazard symbols, no patient charts, no surgical drape

---

## #9 — Branş Banner: İMPLANTOLOJİ (with baked TR text)

**File:** `/image/catalog/raven/home/brans-implantoloji.jpg` · **Display size:** `840×480` (display 420×240)

> **WARNING:** AI image generators frequently misspell baked-in text. Recommended workflow: generate **two versions** — (A) with text per prompt below, (B) without text (use the same prompt minus the text instruction). If version A produces garbled text, use version B and overlay HTML text in Journal3 via absolute-positioned `<h2>` element.

**Primary prompt (Midjourney v6):**
```
[MASTER PREFIX] Wide rectangular banner composition with two distinct
zones. LEFT TWO-THIRDS: deep Raven-blue gradient background from cobalt
#0f3a6b on the right edge to deep navy #0a2547 on the left edge, with
clean uppercase Turkish text reading exactly "İMPLANTOLOJİ" rendered
in pure white Inter font weight 600 semibold, no italic, sharp clean
letterforms with the dotted I and capital İ correct, positioned in the
upper-left quadrant aligned to a left margin of 8% from edge.
Importantly the text includes the Turkish capital İ with dot. Below
the headline, a small simple Raven wordmark in white in the top-right
corner of the banner. RIGHT THIRD: a single titanium dental implant
fixture with abutment connected, standing vertically, photographically
real, sharp focus, on a continuation of the same blue gradient. Soft
overhead light, subtle contact shadow. Banner aspect 7:4.
--ar 7:4 --style raw --v 6 --q 2
```

**DALL-E 3 / ChatGPT variant:**
> A wide rectangular banner. The left two-thirds is a deep clinical-blue gradient (cobalt fading to navy) with the Turkish word "İMPLANTOLOJİ" rendered in clean uppercase white Inter Semibold font in the upper-left area. The capital letter İ must have its dot correctly above it (Turkish typography). A small Raven wordmark sits in the top-right corner. The right third of the banner shows a single titanium dental implant fixture with abutment standing vertically against a continuation of the same blue gradient, lit with soft overhead studio light. Photorealistic product photography. Aspect ratio 7:4. Critical: render the Turkish text exactly as specified, do not garble or substitute letters.

**Negative:**
no garbled letters, no fake-looking text, no English translation, no extra words, no decorative typography, no italic, no script font, no shadows on text, no glow on text, no extra logos beyond the single Raven wordmark, no full mouth dental diagram

**Fallback strategy:** If text renders poorly, regenerate WITHOUT text instructions and supply path `brans-implantoloji-notext.jpg` instead. Engineer overlays `<h2>İmplantoloji</h2>` in CSS.

---

## #10 — Branş Banner: CERRAHİ (with baked TR text)

**File:** `/image/catalog/raven/home/brans-cerrahi.jpg` · **Display size:** `840×480`

**Primary prompt (Midjourney v6):**
```
[MASTER PREFIX] Wide rectangular banner with two zones. LEFT TWO-THIRDS:
deep Raven-blue gradient from cobalt #0f3a6b right edge to deep navy
#0a2547 left edge. Clean uppercase Turkish text "CERRAHİ" rendered in
pure white Inter font weight 600 semibold, no italic, sharp clean
letterforms — the final letter is the Turkish capital İ with dot.
Text positioned in upper-left, 8% from left edge. Small white Raven
wordmark in top-right corner. RIGHT THIRD: composed grouping of three
premium surgical instruments — Bein elevator straight, Cryer elevator,
and a periotome — slightly fanned, brushed stainless satin finish,
sharp focus, on the same blue gradient. Soft overhead light, unified
shadows. Banner aspect 7:4.
--ar 7:4 --style raw --v 6 --q 2
```

**DALL-E 3 / ChatGPT variant:**
> A wide rectangular banner. Left two-thirds: deep clinical-blue gradient background with the Turkish word "CERRAHİ" rendered in uppercase white Inter Semibold font in the upper-left area. The final letter is the Turkish capital İ with dot — render this character precisely. A small Raven wordmark in the top-right corner. Right third: a slight fan of three premium dental surgical instruments (root elevators, periotome) in brushed satin stainless on the same blue gradient. Photorealistic catalog photography. Aspect ratio 7:4.

**Negative:**
no garbled text, no English translation "SURGICAL", no decorative typography, no italic, no script, no shadows on text, no fake-looking letterforms, no clutter, no patient mouth, no surgical gloves

---

## #11 — Branş Banner: ENDODONTİ (with baked TR text)

**File:** `/image/catalog/raven/home/brans-endodonti.jpg` · **Display size:** `840×480`

**Primary prompt (Midjourney v6):**
```
[MASTER PREFIX] Wide rectangular banner with two zones. LEFT TWO-THIRDS:
deep Raven-blue gradient from cobalt #0f3a6b right edge to deep navy
#0a2547 left edge. Clean uppercase Turkish text "ENDODONTİ" rendered
in pure white Inter font weight 600 semibold, no italic, the final
letter is the Turkish capital İ with dot. Text in upper-left, 8% margin.
Small white Raven wordmark in top-right corner. RIGHT THIRD: a fan
arrangement of five endodontic K-files with color-coded silicone stops
(white, yellow, red, blue, green corresponding to ISO sizes 15-25-30-35-40),
laid in a precise fan radiating outward, beside a small black apex
locator probe tip. Sharp focus, photographic realism. Soft overhead
light. Banner aspect 7:4.
--ar 7:4 --style raw --v 6 --q 2
```

**DALL-E 3 / ChatGPT variant:**
> A wide rectangular banner. Left two-thirds: deep clinical-blue gradient with the Turkish word "ENDODONTİ" rendered in uppercase white Inter Semibold font in the upper-left area, with the final character being the Turkish capital İ with dot. A small Raven wordmark in the top-right corner. Right third: a precise fan of five endodontic K-files with color-coded silicone stops (white, yellow, red, blue, green following ISO standard sizes), plus a small apex locator probe tip beside them, on the same blue gradient. Photorealistic, sharp focus, clean studio light. Aspect ratio 7:4.

**Negative:**
no garbled text, no English "ENDODONTICS", no italic, no decorative font, no random colored handles, no full apex locator device with screen, no patient illustration, no tooth diagram, no root canal x-ray

---

### GROUP B — BRANŞ CIRCLE ICONS (9-piece cohesive set)

> **CRITICAL FOR COHESION**: All 9 circle icons MUST share these exact specifications to look like a unified set:
>
> - **Background:** light Raven-blue gradient `#ebf2fa` top-left → `#cfdbe8` bottom-right (radial gradient centered upper-left)
> - **Framing:** square 1:1 frame, instrument centered, occupying about 60% of frame diagonal
> - **Lighting:** single soft overhead key from upper-left at 35 degrees, soft fill, single unified gradient shadow falling toward lower-right
> - **Finish:** brushed satin stainless steel, no mirror chrome
> - **Style:** photographic realism, slight macro feel, ultra-sharp focus
> - **Output:** 300×300 PNG; engineer will mask to circle via CSS `border-radius: 50%`
>
> **Generate all 9 in a single session** using consistent prompt prefix below. If MJ allows, use `--seed` to lock visual consistency.

**Group B common prefix (use for ALL 9 circles):**
```
Square 1:1 macro studio product photograph of a single premium dental
instrument centered in frame, brushed satin stainless steel surgical
finish, light Raven-blue radial gradient background from #ebf2fa upper-left
to #cfdbe8 lower-right, single soft overhead key light from 35 degrees
upper-left producing unified gradient shadow toward lower-right, gentle
fill light, slight contact shadow beneath, sharp focus, photographic
realism not illustration, instrument occupies 60% of frame diagonal,
clean catalog mood, no text, no labels, no logo
```

---

## #12 — Branş Circle: Cerrahi (Surgical Scalpel #15)

**File:** `/image/catalog/raven/home/brans-cerrahi-circle.png` · **Display size:** `300×300`

**Primary prompt (Midjourney v6):**
```
[GROUP B PREFIX] Centered: a Bard-Parker BP-3 surgical scalpel handle
with a #15 surgical blade attached, oriented diagonally from lower-left
to upper-right at 30 degrees. The polished stainless handle is hexagonal
in cross-section near the blade end, with a fine textured grip pattern
mid-handle. The #15 blade is small and curved, sharp-edge facing
upward, with a soft glint along the cutting edge. Center-frame on the
light blue gradient. The blade catches a delicate specular highlight
along its spine.
--ar 1:1 --style raw --v 6 --q 2 --seed 1001
```

**DALL-E 3 variant:**
> A square macro photograph of a Bard-Parker BP-3 surgical scalpel handle with a #15 surgical blade attached, positioned diagonally from lower-left to upper-right in the center of the frame. The polished stainless handle has a hexagonal cross-section near the blade and a fine textured grip pattern. The small curved #15 blade catches a soft highlight along its sharp edge. Background is a light blue radial gradient. Soft overhead studio lighting. Photorealistic. Aspect ratio 1:1.

**Negative:**
no full scalpel set, no blood, no cutting action, no plastic handle, no English size markings, no packaging

---

## #13 — Branş Circle: İmplantoloji (Titanium Abutment + Driver)

**File:** `/image/catalog/raven/home/brans-implantoloji-circle.png` · **Display size:** `300×300`

**Primary prompt (Midjourney v6):**
```
[GROUP B PREFIX] Centered: a single titanium dental implant abutment
standing upright in the center, paired with a slender stainless
hex-driver tool positioned diagonally to its left at 45 degrees with
the tip pointing toward the abutment hex connection. The titanium
abutment shows precision machined threads at the bottom and a clean
hex socket at the top, with a faint anodized gold collar. Both
elements brushed satin finish, no mirror chrome. Center-frame on
light blue gradient.
--ar 1:1 --style raw --v 6 --q 2 --seed 1001
```

**DALL-E 3 variant:**
> A square macro photograph of a single titanium dental implant abutment standing upright in the center of the frame, paired with a slender stainless steel hex driver tool angled diagonally to its left, tip pointing toward the abutment. The abutment has a faint anodized gold collar and precision-machined hex socket at the top. Both items brushed satin finish on a light blue radial gradient background. Soft overhead studio light. Photorealistic. Aspect ratio 1:1.

**Negative:**
no jawbone, no x-ray, no gum tissue, no implant fixture buried in bone, no English size markings, no packaging

---

## #14 — Branş Circle: Endodonti (K-File Fan)

**File:** `/image/catalog/raven/home/brans-endodonti-circle.png` · **Display size:** `300×300`

**Primary prompt (Midjourney v6):**
```
[GROUP B PREFIX] Centered: a tight fan of five endodontic K-files
radiating from a single pivot point at the bottom-center, with
color-coded silicone stops in this exact order from left to right:
white, yellow, red, blue, green (ISO sizes 15-20-25-30-35). The
metal files are thin, twisted stainless wire with fine helical
pattern, glinting subtly along their length. The fan occupies the
upper two-thirds of the frame, with handles converging at the
bottom-center. Light blue gradient background.
--ar 1:1 --style raw --v 6 --q 2 --seed 1001
```

**DALL-E 3 variant:**
> A square macro photograph of a tight fan of five endodontic K-files radiating from a single pivot at the bottom-center, with color-coded silicone stops in order from left to right: white, yellow, red, blue, green. The files are thin twisted stainless wire with a fine helical surface pattern. Light blue radial gradient background. Soft overhead studio light. Photorealistic. Aspect ratio 1:1.

**Negative:**
no rotary endo motor, no full handpiece, no patient tooth, no apex locator screen, no English size markings, no plastic packaging

---

## #15 — Branş Circle: Ortodonti (Bracket + Arch Wire)

**File:** `/image/catalog/raven/home/brans-ortodonti-circle.png` · **Display size:** `300×300`

**Primary prompt (Midjourney v6):**
```
[GROUP B PREFIX] Extreme macro centered: a single stainless steel
orthodontic edgewise bracket (twin design with four tie wings) shown
in three-quarter angle, with a thin stainless arch wire threaded
horizontally through its slot. The bracket sits dead-center, the
arch wire extends slightly off the left and right edges of the frame.
Brushed satin finish, fine machining detail visible. Light blue
gradient background. Sharp macro focus on the bracket slot.
--ar 1:1 --style raw --v 6 --q 2 --seed 1001
```

**DALL-E 3 variant:**
> An extreme macro photograph of a single stainless steel orthodontic edgewise bracket — twin design with four tie wings — shown in three-quarter view, with a thin stainless arch wire threaded horizontally through its slot. The bracket sits in the center of the frame. The arch wire extends slightly off the left and right edges. Brushed satin stainless finish. Light blue radial gradient background. Soft overhead studio light. Photorealistic, macro focus. Aspect ratio 1:1.

**Negative:**
no full mouth, no teeth, no gum tissue, no elastic ties in bright colors, no plastic bracket, no ceramic bracket (must be stainless), no English markings

---

## #16 — Branş Circle: Restoratif (Composite Spatula)

**File:** `/image/catalog/raven/home/brans-restoratif-circle.png` · **Display size:** `300×300`

**Primary prompt (Midjourney v6):**
```
[GROUP B PREFIX] Centered: the working end of a stainless steel
composite placement spatula, with a slim leaf-shaped flat blade tip
on one end pointing upper-right, photographed from a 60-degree
three-quarter angle. The handle is hex-shaped, brushed satin finish.
A tiny highlight catches the edge of the spatula tip. Light blue
gradient background. The composite spatula occupies the central
diagonal of the frame.
--ar 1:1 --style raw --v 6 --q 2 --seed 1001
```

**DALL-E 3 variant:**
> A square macro photograph of the working end of a stainless steel composite placement spatula — a slim leaf-shaped flat blade on a hex-shaped brushed stainless handle, photographed from a three-quarter angle. The spatula occupies the central diagonal of the frame, tip pointing toward upper-right. Light blue radial gradient background. Soft overhead studio light. Photorealistic macro. Aspect ratio 1:1.

**Negative:**
no actual composite paste smear, no curing light, no patient tooth, no tooth-colored material on tip, no plastic handle, no English markings

---

## #17 — Branş Circle: Pedodonti (Pediatric Mirror + Probe)

**File:** `/image/catalog/raven/home/brans-pedodonti-circle.png` · **Display size:** `300×300`

**Primary prompt (Midjourney v6):**
```
[GROUP B PREFIX] Centered: a small pediatric dental mirror with
slightly smaller round head than adult version, paired with a slim
periodontal probe with a soft pink-tinted ergonomic silicone grip
mid-handle. The two instruments crossed in a delicate X-shape at
center frame, mirror upper-left, probe upper-right. Stainless satin
finish on metal sections, soft pink grip on the probe. Light blue
gradient background. The pink grip is muted, not bright candy-pink.
--ar 1:1 --style raw --v 6 --q 2 --seed 1001
```

**DALL-E 3 variant:**
> A square macro photograph of two small pediatric dental instruments crossed in a delicate X-shape at center frame: a pediatric dental mirror (smaller round head than adult version) on the upper-left, and a slim periodontal probe with a soft muted pink silicone grip on the upper-right. Stainless satin finish on metal sections. Light blue radial gradient background. Soft overhead studio light. The pink should be muted and professional, not bright candy-pink. Photorealistic. Aspect ratio 1:1.

**Negative:**
no children, no pediatric patient, no cartoon characters, no toys, no toothbrush, no bright candy-pink, no glitter, no stickers, no English markings

---

## #18 — Branş Circle: Periodontoloji (Gracey Curette #5/6)

**File:** `/image/catalog/raven/home/brans-periodontoloji-circle.png` · **Display size:** `300×300`

**Primary prompt (Midjourney v6):**
```
[GROUP B PREFIX] Centered macro: the working tip of a Gracey curette
#5/6, with its distinctive offset curved blade shown in detail at
center frame. The double-ended instrument is oriented diagonally from
lower-left to upper-right, with one curved working end visible in
sharp focus, the other end fading into soft focus. Stainless brushed
satin handle with the characteristic textured grip pattern. The
curette tip catches a delicate highlight along its curved cutting
edge. Light blue gradient background.
--ar 1:1 --style raw --v 6 --q 2 --seed 1001
```

**DALL-E 3 variant:**
> A square macro photograph of a Gracey curette #5/6 dental scaling instrument, oriented diagonally from lower-left to upper-right at center frame. One curved offset working end is in sharp focus, the other end fades into soft focus. The handle is brushed satin stainless with a textured grip pattern. A delicate highlight catches the curved cutting edge. Light blue radial gradient background. Photorealistic macro. Aspect ratio 1:1.

**Negative:**
no gums, no teeth, no calculus deposit, no patient mouth, no ultrasonic scaler tip, no plastic handle, no English size markings

---

## #19 — Branş Circle: Teşhis (Mirror + Explorer Probe Crossed)

**File:** `/image/catalog/raven/home/brans-teshis-circle.png` · **Display size:** `300×300`

**Primary prompt (Midjourney v6):**
```
[GROUP B PREFIX] Centered: a standard adult dental mirror with round
front-surface mirror head, and a stainless explorer probe #23 (shepherd
hook tip), crossed at center frame in a perfect X-shape. The mirror
occupies the upper-left to lower-right diagonal; the explorer occupies
the upper-right to lower-left diagonal. Both have matching slim
brushed-stainless handles, satin finish. The mirror's reflective face
shows a soft reflection of the light blue gradient background. The
explorer's sharp hook tip catches a tiny highlight. Light blue radial
gradient background.
--ar 1:1 --style raw --v 6 --q 2 --seed 1001
```

**DALL-E 3 variant:**
> A square macro photograph of two classic dental diagnostic instruments crossed in a perfect X-shape at center frame: a standard adult dental mirror with round front-surface mirror head on the upper-left to lower-right diagonal, and a #23 explorer probe (shepherd hook tip) on the upper-right to lower-left diagonal. Matching slim brushed-stainless handles with satin finish. The mirror face reflects a soft hint of the blue gradient. The explorer hook tip catches a small highlight. Light blue radial gradient background. Photorealistic. Aspect ratio 1:1.

**Negative:**
no patient mouth, no teeth visible in mirror reflection, no plastic handle, no English markings, no full diagnostic kit, no x-ray film

---

## #20 — Branş Circle: Çekim (Extraction Forceps Head)

**File:** `/image/catalog/raven/home/brans-cekim-circle.png` · **Display size:** `300×300`

**Primary prompt (Midjourney v6):**
```
[GROUP B PREFIX] Centered macro: the working head of a #150 upper
universal extraction forceps, shown in three-quarter angle from above
and slightly to the left. The two curved beaks are slightly open,
revealing their precisely machined inner gripping surfaces. The hinge
joint is sharp and clean. Brushed satin stainless finish. The forceps
shaft extends downward out of frame. Light blue radial gradient
background. Macro focus on the beak inner surfaces.
--ar 1:1 --style raw --v 6 --q 2 --seed 1001
```

**DALL-E 3 variant:**
> A square macro photograph of the working head of a #150 upper universal dental extraction forceps, shown in three-quarter angle from above-left. The two curved beaks are slightly open, revealing precisely machined inner gripping surfaces. The hinge joint is sharp and clean. Brushed satin stainless steel finish. The shaft extends downward out of frame. Light blue radial gradient background. Photorealistic macro focus. Aspect ratio 1:1.

**Negative:**
no tooth gripped in forceps, no blood, no patient mouth, no full forceps showing handles, no English markings, no plastic, no other instruments in frame

---

### GROUP C — SVG LINE ICONS

> **CRITICAL LIMITATION:** AI image generators produce **raster output only**, never true SVG. Three workflow options:
>
> 1. **Recommended:** Use **Recraft.ai** (vector-friendly model) — it can output editable SVG directly.
> 2. **Alternative:** Generate as 512×512 PNG with the prompts below; engineer manually traces in Figma/Illustrator and exports SVG.
> 3. **Fastest:** Skip AI for icons — engineer copies free SVGs from Heroicons / Lucide / Tabler (all MIT-licensed) matching the concepts below.
>
> **All Group C icons share this spec:** 24×24 viewBox, 1.5px stroke, no fill, rounded line caps and joins, `currentColor` stroke so CSS can recolor. Monoline, consistent visual weight, geometric clarity.

**Group C common prompt prefix:**
```
Minimal monoline icon design, 1.5px uniform stroke weight, no fill,
rounded line caps and joins, geometric clarity, designed on a 24x24
grid, black stroke on pure white background, centered composition,
extra-clean vector aesthetic similar to Heroicons or Lucide icon
library, single concept clearly readable at 24x24 pixels, no shading,
no gradient, no decorative flourish
```

---

## #21 — ISO Certificate Badge

**File:** `iso-cert.svg` (engineer adds path) · **Size:** `24×24` viewBox

**Primary prompt (Midjourney v6):**
```
[GROUP C PREFIX] A circular badge or rosette outline with a checkmark
inside it, with two short ribbon tails extending downward from the
bottom of the circle like a certification seal. Clean minimal lines.
--ar 1:1 --style raw --v 6 --q 2
```

**DALL-E variant:**
> A minimal monoline icon showing a circular certificate badge with a checkmark inside, with two small ribbon tails extending down from the bottom of the circle. 1.5px stroke, no fill, on white background. 24x24 grid design.

**Negative:**
no text inside badge, no "ISO" letters, no numbers, no shading, no fill

**Heroicons alternative:** `badge-check` icon (close match, recolor via CSS).

---

## #22 — CE Mark

**File:** `ce-mark.svg` · **Size:** `24×24` viewBox

**Primary prompt (Midjourney v6):**
```
[GROUP C PREFIX] Two stylized letterforms "C" and "E" rendered as
monoline outlines sitting inside a square or implied square frame —
the C as an open partial circle, the E as three horizontal strokes
joined by a vertical. Both letters geometric, monoline, no serif.
Spacing between them slightly tight.
--ar 1:1 --style raw --v 6 --q 2
```

**DALL-E variant:**
> A minimal monoline icon showing the letters "C" and "E" side by side, drawn as geometric monoline shapes — the C as an open partial circle and the E as three horizontal strokes joined by a vertical. Both letters fit inside an implied square. 1.5px stroke, no fill, on white. Note: the letters CE are acceptable here as they form a standardized mark.

**Negative:**
no extra text, no certification badge frame, no shading, no fill, no decorative serif

**Note:** CE letters are the only acceptable "baked text" in Group C since they form a standardized compliance mark.

---

## #23 — Steel / Material

**File:** `steel.svg` · **Size:** `24×24` viewBox

**Primary prompt (Midjourney v6):**
```
[GROUP C PREFIX] Three parallel horizontal bars of equal length stacked
vertically with even spacing, representing layered steel sheets or
material grade. Each bar is a thin rounded rectangle outline. Centered
on the 24x24 grid.
--ar 1:1 --style raw --v 6 --q 2
```

**DALL-E variant:**
> A minimal monoline icon showing three parallel horizontal bars of equal length stacked vertically with even spacing, representing layered steel sheets or material grade. Each bar is a thin rounded rectangle outline. Centered. 1.5px stroke, no fill, on white.

**Negative:**
no text, no labels, no shading, no fill, no perspective

**Lucide alternative:** `layers` icon.

---

## #24 — Network (DİŞSİAD)

**File:** `network.svg` · **Size:** `24×24` viewBox

**Primary prompt (Midjourney v6):**
```
[GROUP C PREFIX] Three small circles arranged in a triangle formation
(one at top, two at bottom-left and bottom-right), connected by three
straight thin lines forming the triangle edges. The circles are small
filled or open dots, the connecting lines monoline. Represents network
or connected nodes.
--ar 1:1 --style raw --v 6 --q 2
```

**DALL-E variant:**
> A minimal monoline icon showing three small circles arranged in a triangular formation — one at the top, two at the bottom corners — connected by three thin straight lines forming the triangle. Represents a network of connected nodes. 1.5px stroke, on white.

**Negative:**
no text, no extra nodes, no curved lines, no shading

**Heroicons alternative:** `share` icon, or custom three-dot triangle.

---

## #25 — Factory

**File:** `factory.svg` · **Size:** `24×24` viewBox

**Primary prompt (Midjourney v6):**
```
[GROUP C PREFIX] Simple factory building silhouette outline: a flat
horizontal base, two sawtooth roof peaks on the left half, a vertical
chimney rising from the right half with one small puff of smoke
suggested by a tiny rounded curve. Monoline outline. Centered.
--ar 1:1 --style raw --v 6 --q 2
```

**DALL-E variant:**
> A minimal monoline icon of a small factory silhouette: a flat horizontal base, two sawtooth roof peaks on the left half, and a vertical chimney rising from the right half with one tiny rounded smoke curve at the top. Monoline outline, 1.5px stroke, on white background.

**Negative:**
no text, no windows full of detail, no smokestack pollution, no trucks, no shading

**Lucide alternative:** `factory` icon.

---

## #26 — Price Tag

**File:** `price-tag.svg` · **Size:** `24×24` viewBox

**Primary prompt (Midjourney v6):**
```
[GROUP C PREFIX] Classic price tag shape — a rectangle with one short
end angled into a triangle point, with a small circular hole near the
point for a string. Inside the rectangle, a single percent sign "%"
character rendered as monoline. Tag rotated 15 degrees counter-clockwise.
--ar 1:1 --style raw --v 6 --q 2
```

**DALL-E variant:**
> A minimal monoline icon of a classic price tag shape: a rectangle with one end angled into a triangle point, a small circular hole near the point. Inside the rectangle, a percent sign "%" drawn in monoline. The tag is rotated about 15 degrees counter-clockwise. 1.5px stroke, on white.

**Negative:**
no price numbers, no currency symbols, no shading, no fill, no string attached

**Heroicons alternative:** `tag` icon with `%` added as monoline text.

---

## #27 — Box

**File:** `box.svg` · **Size:** `24×24` viewBox

**Primary prompt (Midjourney v6):**
```
[GROUP C PREFIX] A 3D shipping box drawn as a flat-perspective hex
outline (top diamond visible plus front and side rectangles), with
a simple horizontal line across the top suggesting tape seam. Clean
isometric-like view. Monoline.
--ar 1:1 --style raw --v 6 --q 2
```

**DALL-E variant:**
> A minimal monoline icon of a shipping box drawn as a flat-perspective outline: a top diamond plus front and side rectangles forming the 3D box silhouette, with a thin horizontal line across the top suggesting a tape seam. 1.5px stroke, on white.

**Negative:**
no text, no shipping labels, no barcodes, no shading, no fill

**Heroicons alternative:** `cube` icon, or `archive-box`.

---

## #28 — Truck

**File:** `truck.svg` · **Size:** `24×24` viewBox

**Primary prompt (Midjourney v6):**
```
[GROUP C PREFIX] Side view of a delivery truck silhouette: a longer
rectangular cargo container on the right, a shorter cab on the left
with one window outline, two wheel circles at the bottom (one under
cab, one under container). Monoline outline. Centered.
--ar 1:1 --style raw --v 6 --q 2
```

**DALL-E variant:**
> A minimal monoline icon of a delivery truck in side view: a long rectangular cargo container on the right, a shorter cab on the left with one window outline, two wheel circles at the bottom. 1.5px stroke, on white background.

**Negative:**
no text, no logos on truck side, no shading, no exhaust smoke, no road lines

**Heroicons alternative:** `truck` icon.

---

## #29 — Check Shield

**File:** `check-shield.svg` · **Size:** `24×24` viewBox

**Primary prompt (Midjourney v6):**
```
[GROUP C PREFIX] A shield outline shape — flat top, curved sides
tapering to a rounded point at the bottom — with a clean checkmark
inside the shield, centered. Monoline outline, no shading.
--ar 1:1 --style raw --v 6 --q 2
```

**DALL-E variant:**
> A minimal monoline icon of a shield with a checkmark inside it. The shield has a flat top and curved sides tapering to a rounded point at the bottom. The checkmark is centered inside. 1.5px stroke, on white.

**Negative:**
no text, no decorative crests, no shading, no fill, no double-line emphasis

**Heroicons alternative:** `shield-check` icon (exact match).

---

## #30 — Mail Envelope

**File:** `mail.svg` · **Size:** `24×24` viewBox

**Primary prompt (Midjourney v6):**
```
[GROUP C PREFIX] A classic mail envelope: a horizontal rectangle with
a triangular flap on top forming a "V" line that touches the bottom
corners of the flap. Monoline outline, no shading. Centered.
--ar 1:1 --style raw --v 6 --q 2
```

**DALL-E variant:**
> A minimal monoline icon of a mail envelope: a horizontal rectangle with a triangular V-shaped flap line on top. 1.5px stroke, on white background.

**Negative:**
no text, no @ symbol, no postmark, no shading, no fill

**Heroicons alternative:** `envelope` icon (exact match).

---

### GROUP D — BRAND WORDMARK SVGs

> **CRITICAL LIMITATION:** Same as Group C — AI generates raster only. Recommended workflow:
>
> 1. **Best:** Engineer hand-builds in Figma using Inter Black/Bold and exports SVG directly. AI prompts here serve as visual mockup reference only.
> 2. **Alternative:** Generate raster with the prompts below, then trace in Figma.
>
> **All Group D wordmarks share this spec:** ~120×60 viewBox, pure black `#000000` ink (or `currentColor` for theming), white background (or transparent), no shadows, no decorative effects, Inter font family (Bold/Black weight), all caps, optical letter-spacing of about +20 tracking, sit on a clean baseline.

---

## #31 — RAVEN Wordmark

**File:** `brand-raven.svg` · **Size:** `~120×60` viewBox

**Primary prompt (Midjourney v6):**
```
A minimalist logotype wordmark design for a premium dental instrument
manufacturer: the word "RAVEN" rendered in clean uppercase letters,
Inter Black or Inter Heavy font, sharp geometric letterforms, slightly
extended letter-spacing, with a small superscript "R" registered
trademark mark to the upper-right of the final letter N. Pure black
ink on pure white background, no shadow, no gradient, no decorative
effect. The letter R may have one tiny subtle stylistic detail (a
slight bevel on the inner counter or a subtle elongation of the leg)
but must remain readable and clean — no eagle silhouette, no bird,
no icon, just type. Single horizontal baseline. Studio brand identity
reference: Helvetica Specimen, IBM Plex Display, Apollinaire
Mag wordmarks.
--ar 2:1 --style raw --v 6 --q 2
```

**DALL-E variant:**
> A minimalist logotype design for a premium dental instrument manufacturer: the word "RAVEN" in clean uppercase Inter Black font, sharp geometric letterforms, slightly extended letter-spacing. Add a small superscript registered trademark "R" mark in a circle to the upper-right of the final N. Pure black ink on white background, no shadows, no gradients, no decorations. The letter R may have one tiny subtle stylistic detail but no eagle or bird iconography — just clean type. Aspect ratio approximately 2:1.

**Negative:**
no bird icon, no eagle silhouette, no decorative flourish, no italic, no script, no shadow, no gradient, no extra letters, no Cyrillic look-alikes, no spelling errors

**Engineer note:** Final SVG should set `R` in `font-weight: 900` (Inter Black), `letter-spacing: 0.05em`, `font-family: Inter, system-ui, sans-serif`. The ® symbol is the Unicode `U+00AE` character at 0.5em.

---

## #32 — AVEN Wordmark

**File:** `brand-aven.svg` · **Size:** `~120×60` viewBox

**Primary prompt (Midjourney v6):**
```
A minimalist logotype wordmark for a sub-brand: the word "AVEN" in
uppercase Inter Bold (slightly lighter weight than RAVEN to visually
indicate sub-brand status), sharp geometric letterforms, optionally
with a very subtle italic slant of about 3 degrees forward to suggest
movement and distinct identity from the parent RAVEN. Pure black ink
on white background, no shadow, no decoration.
--ar 2:1 --style raw --v 6 --q 2
```

**DALL-E variant:**
> A minimalist logotype showing the word "AVEN" in uppercase Inter Bold, sharp geometric letterforms, with an optional very subtle 3-degree forward italic slant. Pure black ink on white background, no shadows, no decorations, no icon. Aspect ratio 2:1.

**Negative:**
no bird icon, no decorative flourish, no script, no shadow, no spelling variant, no extra letters

---

## #33 — RAVEN CERRAHİ Two-Line Wordmark

**File:** `brand-raven-cerrahi.svg` · **Size:** `~120×80` viewBox

**Primary prompt (Midjourney v6):**
```
A minimalist two-line logotype: top line "RAVEN" in uppercase Inter
Black, bottom line "CERRAHİ" in uppercase Inter Medium (lighter weight),
the bottom line slightly smaller in size at about 70% of the top line.
Both lines left-aligned. The Turkish capital İ in CERRAHİ must have
its dot rendered correctly above it. Optional thin horizontal divider
line between the two words, or just optical spacing. Pure black on
white. No icon, no decoration.
--ar 3:2 --style raw --v 6 --q 2
```

**DALL-E variant:**
> A minimalist two-line logotype: top line "RAVEN" in uppercase Inter Black; bottom line "CERRAHİ" in uppercase Inter Medium at about 70% of the top line's size, left-aligned. The Turkish İ character must show its dot. An optional thin horizontal divider line between the two words. Pure black ink on white. Aspect ratio approximately 3:2.

**Negative:**
no garbled İ character, no English translation "SURGICAL", no icons, no decorative flourish, no italic, no script

---

## #34 — RAVEN ENDO Wordmark

**File:** `brand-raven-endo.svg` · **Size:** `~120×80` viewBox

**Primary prompt (Midjourney v6):**
```
A minimalist two-line logotype: top line "RAVEN" in uppercase Inter
Black; bottom line "ENDO" in uppercase Inter Medium at 70% size,
left-aligned. Optional thin horizontal divider line between the words.
Pure black ink on white background. No icon, no decoration.
--ar 3:2 --style raw --v 6 --q 2
```

**DALL-E variant:**
> A minimalist two-line logotype: top "RAVEN" in uppercase Inter Black; bottom "ENDO" in uppercase Inter Medium at about 70% size, left-aligned. Optional thin horizontal divider line between words. Pure black ink on white. Aspect ratio approximately 3:2.

**Negative:**
no icons, no decorative flourish, no italic, no script, no shadow

---

## #35 — RAVEN ORTHO Wordmark

**File:** `brand-raven-ortho.svg` · **Size:** `~120×80` viewBox

**Primary prompt (Midjourney v6):**
```
A minimalist two-line logotype: top line "RAVEN" in uppercase Inter
Black; bottom line "ORTHO" in uppercase Inter Medium at 70% size,
left-aligned. Optional thin horizontal divider line between the words.
Pure black ink on white background. No icon, no decoration.
--ar 3:2 --style raw --v 6 --q 2
```

**DALL-E variant:**
> A minimalist two-line logotype: top "RAVEN" in uppercase Inter Black; bottom "ORTHO" in uppercase Inter Medium at about 70% size, left-aligned. Optional thin horizontal divider line between words. Pure black ink on white. Aspect ratio approximately 3:2.

**Negative:**
no icons, no decorative flourish, no italic, no script, no shadow, no orthodontic bracket illustration

**Engineer note on Group D coherence:** All five wordmarks must share the same Inter typeface, same baseline relationship between primary and secondary lines, same divider treatment (if any), same color. The hierarchy is: RAVEN (master, Black weight) → AVEN (sibling, Bold) → RAVEN CERRAHİ / ENDO / ORTHO (children, Black + Medium two-line).

---

### GROUP E — OG SHARE IMAGE (with controlled baked text)

---

## #36 — Open Graph Share Image

**File:** `/image/catalog/raven/og-image.jpg` · **Display size:** `1200×630`

**Primary prompt (Midjourney v6):**
```
[MASTER PREFIX] Wide social-media share banner composition. Background:
deep Raven-blue gradient from cobalt #0f3a6b in the upper-left flowing
to deep navy #0a2547 in the lower-right, with a subtle volumetric
light beam from the upper-left adding depth. Layout: LEFT HALF holds
clean baked-in white typography in two stacked lines — top line a
larger "RAVEN" wordmark in Inter Black uppercase white, slightly
extended letter-spacing; below it on a second line, smaller Turkish
text reading "Türkiye'nin Üretici Diş Aletleri Merkezi" in Inter
Medium white, sentence case, single line. All Turkish characters
including the lowercase i with dot and the capital Ü with umlaut must
render precisely. Text is left-aligned with a 6% left margin.
RIGHT HALF: a faint silhouette of three premium dental forceps
standing upright at about 40% opacity, partially fading into the
gradient background — visible but secondary to the text. Soft single
light from upper-left. Aspect ratio exactly 1200x630.
--ar 40:21 --style raw --v 6 --q 2
```

**DALL-E 3 / ChatGPT variant:**
> A wide social-media share banner sized 1200x630 pixels. Background is a deep clinical-blue gradient flowing from cobalt blue in the upper-left to deep navy in the lower-right, with a subtle light beam from the upper-left adding depth. The left half of the banner has clean baked-in white typography: a large "RAVEN" wordmark in uppercase Inter Black on top, and beneath it on a second line, smaller Turkish text reading exactly "Türkiye'nin Üretici Diş Aletleri Merkezi" in Inter Medium. The Turkish characters (lowercase i with dot, capital Ü with umlaut) must render precisely. Text is left-aligned with a 6% margin. The right half shows a faint silhouette of three dental forceps standing upright at about 40% opacity, fading into the gradient. Soft light from upper-left. Photorealistic, clean, B2B premium feel.

**Negative:**
no garbled Turkish characters, no English translation, no extra text, no phone number, no email, no URL, no www, no decorative flourish, no logos other than the RAVEN wordmark, no people, no patient mouth, no teeth diagram, no clutter on right half

**Engineer fallback:** If Turkish text renders poorly, generate WITHOUT text (just the gradient + forceps silhouette) and have engineer overlay text in a one-time Photoshop pass or use a server-side Sharp/PIL script to composite Inter font text onto the rendered image.

---

## 5. Quality Checklist (per generated image)

Before uploading any generated image to the Journal3 theme, verify:

### Visual quality
- [ ] **Resolution check:** Image is at least the target display size × 2 (for retina). Hero slides need 1920×840 minimum source.
- [ ] **No AI fingerprint:** No melted geometry, no impossible reflections, no warped metal, no extra fingers/legs/limbs (yes, AI sometimes adds hands even when not asked).
- [ ] **Color fidelity:** Sample the background in Photoshop/Preview — should fall within hex range `#0a2547` to `#0f3a6b` (or `#cfdbe8` to `#ebf2fa` for circles). If too purple or too teal, regenerate.
- [ ] **Blue gradient direction:** Matches the spec (horizontal for hero/banner, vertical for side-banners, radial for circles).
- [ ] **Shadow consistency:** All shadows fall in the same direction within a single image. No crossing/contradictory shadows.

### Content correctness
- [ ] **No text in image** (except #9-11, #31-36 where explicitly required). Even tiny garbled "metal stamp" text on a forceps shaft means regenerate.
- [ ] **No brand names baked in** unless requested (no "AVEN" sneaking into a Raven image, no fake watermark).
- [ ] **No people, no hands, no patient mouths** anywhere.
- [ ] **Instruments look authentic:** A real dentist would not laugh at the tool shapes. Cross-reference Hu-Friedy, Karl Schumacher, or Devemed catalog photos.
- [ ] **Turkish text (#9-11, #36):** Spelled exactly right with correct İ-with-dot and Ü-with-umlaut characters. If even one character is off, regenerate or fall back to HTML overlay.

### Set cohesion (Group B especially)
- [ ] All 9 circle icons share the same background gradient color
- [ ] All 9 share the same light angle (upper-left, 35 degrees)
- [ ] All 9 have similar instrument scale (60% of frame diagonal)
- [ ] All 9 have unified shadow direction (toward lower-right)
- [ ] If using Midjourney, all 9 generated with the same `--seed` value for maximum consistency

### File hygiene
- [ ] Saved as JPG (quality 85-90) for raster photos, PNG-24 for circle icons (transparency support), SVG for icons/wordmarks
- [ ] Filename matches exactly the path specified in the asset list
- [ ] EXIF stripped (no camera metadata, no AI watermark in metadata)
- [ ] File size under 300KB for hero (compress with TinyPNG/Squoosh), under 80KB for circles, under 8KB per SVG

---

## 6. Batching Tip — Recommended Generation Order

Generate assets in this sequence for maximum efficiency and visual consistency:

### Phase 1 — Easiest, fastest, sets the foundation (~30 min)
1. **Group D wordmarks (#31-35)** — Engineer hand-builds in Figma using Inter font. No AI needed if you trust your typography. AI prompts above serve only as visual reference for letter-spacing and proportion decisions.
2. **Group C SVG icons (#21-30)** — Use Heroicons / Lucide / Tabler free libraries. Engineer copies the closest matching free SVG, recolors via CSS `currentColor`. AI prompts above are fallback only.

### Phase 2 — Set cohesion is critical (~1-2 hr)
3. **Group B circle icons (#12-20)** — Generate all 9 in ONE continuous Midjourney session, using the same `--seed 1001` value across all prompts. If any one circle looks off-brand compared to the others, regenerate that single circle (not the whole set). Aim for visual consistency over individual perfection.

### Phase 3 — Hero photography (~1-2 hr)
4. **Hero slides (#1, #2)** — These are the highest-stakes images. Generate 4-6 variations of each, pick the best. Both must share atmosphere/lighting/color so they look like a pair.
5. **Side banners (#3, #4)** — Match the hero atmosphere.
6. **Feature banners (#6, #7, #8)** — Three vertical stripes that will sit side-by-side on the home page. Generate together; ensure all three have the same gradient direction and lighting.

### Phase 4 — Specialty (~1 hr)
7. **Branş banners with text (#9, #10, #11)** — RISK: AI text is unreliable. Generate two versions of each: (A) with text baked in per prompt, (B) without text. Use whichever works; HTML overlay text on (B) if (A) fails.
8. **Bulk CTA background (#5)** — Single image, but ultra-wide aspect. If MJ refuses 6.6:1, generate 16:9 and crop in CSS.

### Phase 5 — Final (~15 min)
9. **OG share image (#36)** — Generate with text; if garbled, composite Inter font onto a no-text version using Photoshop or a Sharp/PIL script. This is a single image, used for social previews only.

### Cross-cutting tips

- **Lock the seed.** For Midjourney, use `--seed 1001` for all Group B circles and `--seed 2001` for all Group A photos. This dramatically improves intra-set consistency.
- **Generate 4× then pick 1.** Budget 4 generations per slot. The "first try works" myth costs more time than expected — quality control beats speed.
- **Color-match in post.** Even good AI outputs drift slightly off the exact Raven-blue. Run final JPGs through a 5-minute Photoshop curves pass to lock the background to exact `#0f3a6b → #0a2547`.
- **Compress aggressively.** All hero raster assets should be under 300KB, ideally 150-200KB. Use Squoosh.app (mozJPEG quality 82) or TinyPNG.
- **Strip EXIF.** Run final files through `exiftool -all= file.jpg` to remove AI-generation metadata before deploying. The Journal3 deploy should not telegraph "AI-generated" through metadata.

---

## 7. Asset Path Summary (engineer reference)

```
/image/catalog/raven/home/
├── hero-slide-1.jpg               (#1, 1920x840)
├── hero-slide-2.jpg               (#2, 1920x840)
├── side-banner-1.jpg              (#3, 480x400)   İmplant Setleri
├── side-banner-2.jpg              (#4, 480x400)   Cerrahi Setler
├── bulk-cta-bg.jpg                (#5, 2640x400)  Klinik Toptan
├── feat-en-yeniler.jpg            (#6, 600x900)
├── feat-cok-satan.jpg             (#7, 600x900)
├── feat-cerrahi.jpg               (#8, 600x900)
├── brans-implantoloji.jpg         (#9, 840x480)   TR text baked
├── brans-cerrahi.jpg              (#10, 840x480)  TR text baked
├── brans-endodonti.jpg            (#11, 840x480)  TR text baked
├── brans-cerrahi-circle.png       (#12, 300x300)
├── brans-implantoloji-circle.png  (#13, 300x300)
├── brans-endodonti-circle.png     (#14, 300x300)
├── brans-ortodonti-circle.png     (#15, 300x300)
├── brans-restoratif-circle.png    (#16, 300x300)
├── brans-pedodonti-circle.png     (#17, 300x300)
├── brans-periodontoloji-circle.png(#18, 300x300)
├── brans-teshis-circle.png        (#19, 300x300)
└── brans-cekim-circle.png         (#20, 300x300)

/image/catalog/raven/icons/        (SVG sprite directory — engineer creates)
├── iso-cert.svg                   (#21, 24x24 viewBox)
├── ce-mark.svg                    (#22)
├── steel.svg                      (#23)
├── network.svg                    (#24)
├── factory.svg                    (#25)
├── price-tag.svg                  (#26)
├── box.svg                        (#27)
├── truck.svg                      (#28)
├── check-shield.svg               (#29)
└── mail.svg                       (#30)

/image/catalog/raven/brand/        (brand wordmark directory)
├── brand-raven.svg                (#31, ~120x60 viewBox)
├── brand-aven.svg                 (#32)
├── brand-raven-cerrahi.svg        (#33, ~120x80)
├── brand-raven-endo.svg           (#34)
└── brand-raven-ortho.svg          (#35)

/image/catalog/raven/
└── og-image.jpg                   (#36, 1200x630, social share)
```

---

## 8. Tool-Specific Cheat Sheet

### Midjourney v6
- Append `--ar X:Y --style raw --v 6 --q 2` to every prompt
- Use `--seed N` (any integer) on the first image of a set, then reuse same seed for cohesion
- Use `--no people, hands, text` shortcut for shared negative prompt
- Upscale at 2× for hero slides to reach 1920px width

### DALL-E 3 (via ChatGPT)
- Paste the "DALL-E variant" version of each prompt — it's tuned for ChatGPT's natural-language interface
- ChatGPT may sanitize "scalpel" or "blade" terminology — rephrase as "surgical instrument with cutting edge" if blocked
- DALL-E does not accept negative prompts directly — fold negatives into the positive prompt as "Please ensure no X, no Y..."
- Output max is 1792×1024 — for hero slides at 1920×840, upscale 1.1× then crop

### Google Imagen 3
- Best for photorealistic product shots (Group A)
- Accepts negative prompts via the "Negative prompt" field — paste the master negative directly
- Excellent at rendering text — best option for #9-11 Turkish text and #36 OG image
- Aspect ratios available: 1:1, 9:16, 16:9, 3:4, 4:3 — for 6.6:1 (#5), use 16:9 and crop

### Flux Pro (via Replicate or fal.ai)
- Strongest for ultra-photorealistic instrument detail
- Accepts long natural-language prompts well — paste DALL-E variant text directly
- Best for Group A heroes and Group B circles
- Costs more per generation; use sparingly for hero-quality finals

### Recraft.ai
- Only model that outputs editable SVG directly
- Use for Group C icons (#21-30) and Group D wordmarks (#31-35) to skip the trace-from-raster step
- Style: select "Icon" or "Vector Illustration" mode

---

**End of asset spec.**

For questions about composition, brand voice, or specific instrument references, see `19-UI-DESIGN-BRIEF.md` and `18-UI-IMPROVEMENT-PLAN.md`.
