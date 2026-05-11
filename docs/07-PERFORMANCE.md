# 07 - Performance (Performans Analizi)

> **Durum:** Baseline ölçüm henüz alınmadı. Bu doc planları içerir, gerçek skorlar Faz 3'te eklenecek.

## Mevcut Bilinen Faktörler

| | Değer | Yorum |
|---|---|---|
| Anasayfa HTML (ham) | 463 KB | Büyük, çok inline JS var |
| Anasayfa HTML (gzip) | 65 KB | 7× sıkışma — iyi |
| 39 görsel | henüz toplam ölçülmedi | Lazy loading YOK — hepsi ilk yüklemede iniyor |
| TTFB (TR'den) | Tahmini 600-1200ms | Shared hosting + CloudLinux LVE limitleri |
| LiteSpeed | Aktif | LSCache değil, default web server |
| Browser cache (CSS/JS) | 1 yıl | ✓ (bizim .htaccess) |
| Gzip | Aktif | ✓ |
| ETag | Kapalı | ✓ |
| HTTP/2 | Aktif | NetInternet sağlıyor |
| HTTP/3 | Aktif (alt-svc) | NetInternet sağlıyor |
| OPcache | Bilinmiyor | Shared paket — muhtemelen var ama tuned değil |
| Redis cache | YOK | OpenCart file cache kullanıyor |

## Beklenen Lighthouse Skoru (tahmin)

| Metric | Mevcut tahmini | Hedef (VPS sonrası) |
|---|---|---|
| Performance | 35-55 | **85+** |
| Accessibility | 70-80 | 90+ |
| Best Practices | 80-85 | 95+ |
| SEO | 80-90 | 95+ |
| LCP | ~3-5s | **<2.5s** |
| FID/INP | ~200-400ms | <200ms |
| CLS | ~0.1-0.3 | <0.1 |
| TTFB | 600-1200ms | <250ms |

## Bilinen Performans Sorunları

### 1. Görsel optimizasyonu eksik
- **39 görsel anasayfa**, hepsi JPG/PNG (WebP yok)
- Lazy loading YOK
- `srcset` yok (retina @2x sürümleri yok)
- Image cache klasöründe çoğu resmin 200×280h.png placeholder hali var ama gerçek resim 1000×1000 büyüklükte

**Çözüm:**
- Theme image macro'sunda `loading="lazy"` ve `decoding="async"`
- VPS'te ImageMagick + cron → WebP versiyonu üret
- `<picture>` ile `<source type="image/webp">` fallback

### 2. JS bundle büyük
- `journal3/dist/journal.js` — admin panel'de **4.8 MB** (frontend'e gitmiyor ama yine de)
- Frontend JS: jQuery + Bootstrap + Journal3 custom ≈ 800 KB toplam

**Çözüm:**
- Defer non-critical JS (header.twig'de `defer` flag mevcut)
- Bootstrap-only-needed (full Bootstrap yerine)
- jQuery 3.x'e yükseltme (eğer Journal3 uyumlu ise)

### 3. CSS critical path
- Çok sayıda CSS dosyası (`.css` 149 adet)
- Critical CSS inline değil
- Print CSS render-blocking olabilir

**Çözüm:**
- Critical CSS extraction (penthouse / critters)
- Non-critical CSS `media="print" onload="this.media='all'"` tekniği
- CSS dosyalarını birleştir (Journal3 build sistemi)

### 4. OpenCart query sayısı
- Anasayfa ~30-50 DB query (tipik)
- Kategori sayfası ~50-100 query
- Ürün sayfası ~80-150 query

**Çözüm:**
- Redis cache backend (`storage/cache/` yerine)
- OPcache JIT (PHP 8.2'de)
- Query Cache (MariaDB)
- Eager loading (N+1 query'leri tespit et)

### 5. Inline script'ler
- `<script>window['Journal'] = {...}</script>` — 30 KB
- Çok sayıda inline JS Journal3 settings için
- CSP-friendly değil (`unsafe-inline` gerektirir)

## Faz 3 Ölçüm Planı

### A. Lighthouse — yerel
```bash
# Önce baseline al
npx unlighthouse --site https://ravendentalgroup.com \
  --output-html /Users/ipci/raven-dental/analysis/lighthouse-current.html

# Mobil + desktop iki ayrı
```

### B. PageSpeed Insights API
```bash
curl "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=https://ravendentalgroup.com&strategy=mobile" \
  > /Users/ipci/raven-dental/analysis/psi-mobile.json
```

### C. WebPageTest
- https://www.webpagetest.org/ (TR location varsa kullan)
- Filmstrip + waterfall analizi

### D. Detayli ölçüm
- TTFB → curl -w
- DNS, connect, SSL, server, content kırılımı
- Largest image → optimize edilebilir mi?
- Render-blocking resources sayısı

## Optimizasyon Öncelik Sırası

### Quick wins (1-2 saat — VPS gerek yok)
1. Lazy loading tüm görsellere (theme edit)
2. CSS minify + concat (Journal3 build)
3. Defer non-critical JS

### Orta vadeli (VPS sonrası, 1 gün)
4. PHP 8.2 + OPcache JIT (otomatik kazanç)
5. Redis cache backend
6. MariaDB query cache + buffer pool tuning
7. WebP image generation
8. Critical CSS inline

### Uzun vadeli (proje gerek)
9. JS bundle modernization (Vite/Webpack)
10. Service Worker (offline + cache)
11. Image CDN (Cloudflare Image Resizing veya BunnyCDN)
12. AMP veya progressive enhancement

## Cloudflare Free İle Beklenen Kazanım

| | Origin Only | + CF Free |
|---|---|---|
| TTFB (cache hit) | 250ms | **80-150ms** |
| TTFB (cache miss) | 250ms | 280ms (overhead) |
| Static asset CDN | Tek nokta (origin) | 200+ PoP edge cache |
| DDoS koruma | NetInternet basit | CF anti-DDoS |
| Brotli compression | Manuel kurulum | Otomatik |
| HTTP/3 | NetInternet'te var | CF de sağlar |
| Page Rules cache | LiteSpeed | CF Page Rules |

## Performans Hedefleri (VPS + CF sonrası)

### Mobil Lighthouse
- Performance: **85+**
- LCP: < 2.5s
- INP: < 200ms
- CLS: < 0.1
- TTFB: < 800ms

### Desktop Lighthouse
- Performance: **95+**
- LCP: < 1.5s
- TTFB: < 250ms

### Real User Metrics (Chrome UX Report — GSC'den)
- 75th percentile LCP: "Good" (yeşil) bölge
- 75th percentile INP: "Good"
- 75th percentile CLS: "Good"

## Baseline Ölçümleri (Henüz alınmadı)

→ analysis/lighthouse-baseline.json
→ analysis/psi-baseline.json
→ analysis/page-weight-current.txt

Faz 3'te doldurulacak.
