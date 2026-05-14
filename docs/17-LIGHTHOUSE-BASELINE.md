# 17 — Lighthouse Baseline (Production, 2026-05-12)

> **Tarih:** 2026-05-12 15:46 TR
> **Test ortamı:** Google Chrome 148 + Lighthouse 13.3.0, macOS local-to-prod
> **Site:** https://ravendentalgroup.com (LiteSpeed, PHP 7.4.33, MySQL 8.0.46)
> **Önceden uygulanan:** Tüm SEO/Security/Performance patch'leri (O11-O18 in 08-CHANGES-MADE.md)
> **Ham veri:** [`analysis/lighthouse-baseline/*.json`](../analysis/lighthouse-baseline/)

## 1. Genel Skorlar

| Sayfa | Strategy | Perf | A11y | BP | SEO | LCP | CLS | TBT/INP | FCP |
|---|---|---|---|---|---|---|---|---|---|
| Anasayfa | Mobile | **72** | 71 | 100 | 92 | 6.7s 🔴 | 0.022 ✅ | 0ms ✅ | 2.3s ⚠️ |
| Anasayfa | Desktop | 59 🔴 | 71 | 100 | 92 | 6.7s 🔴 | 0.022 ✅ | 0ms ✅ | 2.3s ⚠️ |
| Kategori (/el-aletleri) | Mobile | **78** | 67 ⚠️ | 100 | 92 | 5.3s 🔴 | 0 ✅ | 0ms ✅ | 2.0s ✅ |
| Kategori | Desktop | 62 🔴 | 67 ⚠️ | 100 | 92 | 5.3s 🔴 | 0 ✅ | 0ms ✅ | 2.0s ✅ |
| Ürün (id=432) | Mobile | 65 🔴 | 75 | 96 | 92 | 7.3s 🔴 | 0 ✅ | 40ms ✅ | 4.3s ⚠️ |
| Ürün | Desktop | 65 🔴 | 75 | 96 | 92 | 3.8s ⚠️ | 0 ✅ | 0ms ✅ | 2.5s ✅ |
| **Ortalama** | — | **67** | **71** | **99** | **92** | — | — | — | — |

**Hedefler (Google):**
- Performance ≥ 90 (mevcut: 67)
- A11y ≥ 95 (mevcut: 71)
- BP ≥ 95 (mevcut: 99 ✅)
- SEO ≥ 95 (mevcut: 92, neredeyse)
- **LCP < 2.5s** (mevcut: 5.3-7.3s — **kritik**)
- **CLS < 0.1** ✅ (mevcut: 0-0.022)
- **INP < 200ms** ✅ (mevcut: 0-40ms)

---

## 2. Kritik Bulgular

### 🔴 1. LCP yüksek (5.3-7.3s, target <2.5s)

**En büyük performance sorunu.** Google'ın 3 Core Web Vital'inden en kritik olanı.

**Mobile vs Desktop aynı LCP** — bu **TTFB (server response)** baskın olduğunu gösteriyor. Network throttling değişikliği LCP'yi etkilemiyor demek ki dosya boyutu değil, ilk byte beklemesi sorun.

**Olası nedenler:**
- LiteSpeed cache devre dışı veya yanlış yapılandırılmış
- Veritabanı sorgu yavaşlığı (henüz tam etkin olmayabilir — yeni index'ler 2026-05-12 deploy)
- 4.5MB total page weight (mobil için ağır)
- Render-blocking CSS (style.min.css 1.2 MB)

**Aksiyon planı:**
1. **LiteSpeed Cache eklenti** (LSCache for OpenCart) — server-side HTML cache, TTFB <100ms'ye düşürür
2. **CSS critical-inline + defer** — render-blocking azalt
3. **Image lazy loading** kategori grid'de daha agresif
4. **Hero image preload** (LCP elementi olan slider görseli)
5. **CDN** (Cloudflare Free) — global assets cache + DDoS

### ⚠️ 2. A11y 67-75 (target 95+)

5 binary failure tespit edildi:

| Issue | Açıklama | Çözüm |
|---|---|---|
| **button-name** | Buttons do not have an accessible name | Search ikon, "X" close button'a `aria-label` |
| **color-contrast** | Yetersiz kontrast | Açık gri text üzerine açık zemin: rengi koyulaştır |
| **heading-order** | H1→H3 atlama | H2 ara başlık ekle veya H3'ü H2 yap |
| **label** | Form inputs label'sız | Newsletter, search input'lara `<label>` veya `aria-label` |
| **link-name** | Empty `<a>` linkleri | Footer'da boş anchor'lar — yine de bir tane vardı? Çift kontrol |

### ⚠️ 3. SEO 92 (target 95+)

Tek issue: **`crawlable-anchors`** — bazı linkler crawl edilemiyor.

Detay: muhtemelen JS-only navigation veya `<a href="javascript:void(0)">` patterns. Ana menü ve kategori linkleri OK ama bazı mobile drawer/dropdown linkleri javascript:void olabilir.

### 🟢 4. Best Practices 96-100 — Mükemmel

HTTPS, secure cookies (yeni patch sayesinde HttpOnly+SameSite), no console errors, no deprecated APIs. Korumalı.

---

## 3. Performance Iyileştirme Öncelikleri (ROI sırasıyla)

| # | İyileştirme | Tahmini Kazanım | Süre | Risk |
|---|---|---|---|---|
| 1 | **LiteSpeed Cache eklentisi** kur + aktive et | LCP -2 to -4s 🔥 | 1-2 saat | Düşük |
| 2 | **Cloudflare Free CDN** + proxy | LCP -1 to -2s | 2 saat | Orta (DNS) |
| 3 | **Hero image preload** + responsive `<source>` | LCP -0.5s | 30 dk | Düşük |
| 4 | **Render-blocking CSS defer** | FCP -0.5s | 1 saat | Orta |
| 5 | **Unused CSS purge** (PurgeCSS) | 65KB savings | 2 saat | Orta |
| 6 | **A11y fixes** (5 binary) | A11y 71→90 | 1 saat | Düşük |
| 7 | **Crawlable anchors** fix | SEO 92→95+ | 30 dk | Düşük |
| 8 | **WebP image conversion** | LCP -0.3s, 30%+ image savings | 4 saat | Düşük |
| 9 | **OPcache enable** (cPanel'den) | TTFB -50-100ms | 5 dk | Düşük |
| 10 | **HTTP/2 push** disable + early hints | LCP -0.2s | 30 dk | Düşük |

**Quick wins toplam (1+9+7):** ~2 saatlik iş ile LCP 6.7s → ~3-4s'e düşebilir.

---

## 4. Önceden Yapılmış İyileştirmelerin Etkisi

Deploy listesi (08-CHANGES-MADE.md O11-O18) Lighthouse'ta gözlenen etkiler:

| Patch | Lighthouse Etkisi |
|---|---|
| DB indexes (O18 perf-01) | TTFB iyileşmesi (henüz tam yansımamış olabilir — yeni indexler) |
| InnoDB migration | Concurrent yazma iyileşti; read performansı kullanıcı görmüyor |
| Twig autoreload off | -30 to -100ms per request — birinci ve sonraki requestlerde fark |
| Secure cookies (HttpOnly+SameSite) | BP skoru 100 (önceden eksikti) |
| og:title meta_title-öncelikli | SEO 92 |
| Sitemap hierarchy + GSC submit | SEO indexing (Lighthouse'ta direkt görünmez ama Google crawl önceliği artar) |
| sameAs schema | SEO sinyali (Lighthouse görmez ama Google Knowledge Graph yardımcı) |
| Site i18n cleanup | A11y/SEO content quality (mevcut metin TR-only) |

---

## 5. Sonraki Adım — Önerilen Sıra

**Bu hafta (~4 saat iş):**
1. ☐ **LSCache eklenti** kur — en yüksek ROI
2. ☐ **OPcache enable** cPanel'den (Settings/PHP > extensions)
3. ☐ **Crawlable anchors** fix (5 dk OCMOD)
4. ☐ **A11y 5 binary fix** (button-name, label, color-contrast vs.)

**Bu ay (~8 saat iş):**
5. ☐ **Cloudflare Free** kurulum + DNS proxy
6. ☐ **Hero image preload** + WebP conversion
7. ☐ **Render-blocking CSS defer**
8. ☐ Lighthouse re-baseline → öncesi/sonrası karşılaştırma

**Beklenen 30 gün sonrası skoru:**
- Perf: 67 → **85+**
- LCP: 6.7s → **<2.5s**
- A11y: 71 → **90+**
- SEO: 92 → **98+**

---

## 6. Re-baseline Talimatı

3-7 gün sonra (cache build + Google'ın yeni indeksleme ile) tekrar ölç:

```bash
CHROME="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"
for s in mobile desktop; do
  CHROME_PATH="$CHROME" lighthouse "https://ravendentalgroup.com/" \
    --chrome-flags="--headless=new --no-sandbox" \
    --output=json --output-path="lh_${s}_$(date +%Y%m%d).json" \
    --form-factor=$s --quiet
done
```

Trend grafiği için `_summary.json`'ları kümülatif tut.

---

## 7. Cross-references
- [`docs/16-GOOGLE-SEO-RULES.md`](./16-GOOGLE-SEO-RULES.md) — Core Web Vitals detaylı
- [`docs/15-CODE-REVIEW-SUMMARY.md`](./15-CODE-REVIEW-SUMMARY.md) — performance bulguları
- [`docs/08-CHANGES-MADE.md`](./08-CHANGES-MADE.md) — uygulanan patch'ler
- [`analysis/lighthouse-baseline/`](../analysis/lighthouse-baseline/) — ham JSON raporlar
