# Theme Patches — Birleşik OCMOD

> **Durum:** Yerelde HAZIR, üretime UYGULANMADI.
> Tek dosya: `raven.ocmod.xml` — 27 operasyon, 12 farklı dosyayı yamalıyor.

## Kapsanan Sorunlar (7 Blok, 27 Operasyon)

| Blok | Hedef | Operasyon | Sorun |
|---|---|---|---|
| **A** | `controller/journal3/seo.php` | 3 | og:title + site_title config_meta_title öncelikli; Twitter card → summary_large_image |
| **B** | `theme/journal3/template/common/header.twig` | 4 | viewport-fit, theme-color, og:locale, og:site_name, search noindex, çift H1 fix |
| **C** | `template/account/login.twig`, `forgotten.twig`, `register.twig`, `information/contact.twig` | 10 | type="email", autocomplete attrs, required, minlength (6 form alanı) |
| **D** | `template/journal3/headers/mobile/header_mobile_1..3.twig` | 3 | Mobile header `<h1>` → `<span>` (çift H1 önle) |
| **E** | `template/journal3/product_card.twig` | 4 | `loading="lazy" decoding="async"` |
| **F** | `template/product/category.twig` | 1 | Breadcrumb `<ul>` → `<ol>` + Schema.org BreadcrumbList microdata |
| **G** | `template/error/not_found.twig` | 1 | 404 sayfasına search box + popüler kategoriler |

## Hedeflenen Etkiler

| Kategori | Beklenen iyileşme |
|---|---|
| **SEO meta** | og:title doğru, Twitter card optimized, viewport ideal |
| **Çift/üçlü H1** | Çözüldü (mobile span + anasayfa-only sr-only) |
| **Mobile UX** | Email/tel klavyesi otomatik, password manager çalışır |
| **Accessibility** | Required, autocomplete attrs aria için faydalı |
| **Image performans** | Lazy loading → LCP iyileşmesi |
| **Breadcrumb SEO** | Schema.org microdata → Google rich result |
| **404 UX** | Bounce rate düşer |
| **Search noindex** | Thin content engellendi |

## Uygulama Adımları

### 1. Sunucuya yükle
```
/home/ravenden/public_html/system/raven.ocmod.xml
```

cPanel API ile (örnek):
```bash
curl -X POST "https://${HOST}:2083/execute/Fileman/save_file_content" \
  -H "Authorization: cpanel ${USER}:${TOKEN}" \
  --data-urlencode "dir=/home/ravenden/public_html/system" \
  --data-urlencode "file=raven.ocmod.xml" \
  --data-urlencode "content@analysis/theme-patches/raven.ocmod.xml"
```

### 2. OpenCart Admin
- Eklentiler → Değişiklikler (Modifications) → mavi yenile (↻)
- Console'da çıkan modification çıktısını incele — 27 operasyon "applied" olmalı
- Hata varsa hangi search string'in match olmadığını gösterir

### 3. Doğrulama
```bash
# H1 çift değil mi?
curl -sL https://ravendentalgroup.com/ | grep -c '<h1'  # 1 olmalı (anasayfa)
curl -sL https://ravendentalgroup.com/diagnostik-aletleri | grep -c '<h1'  # 1 olmalı

# og:title yeni meta_title gösteriyor mu?
curl -sL https://ravendentalgroup.com/ | grep 'og:title'
# Beklenen: "Diş Hekimliği Aletleri ve Cerrahi Ekipmanlar | Raven Dental"

# Twitter card summary_large_image mı?
curl -sL https://ravendentalgroup.com/ | grep 'twitter:card'

# Viewport-fit eklendi mi?
curl -sL https://ravendentalgroup.com/ | grep 'viewport-fit'

# og:locale eklendi mi?
curl -sL https://ravendentalgroup.com/ | grep 'og:locale'

# Lazy loading var mı? (kategori sayfası)
curl -sL https://ravendentalgroup.com/diagnostik-aletleri | grep -c 'loading="lazy"'

# Breadcrumb microdata?
curl -sL https://ravendentalgroup.com/diagnostik-aletleri | grep 'BreadcrumbList'

# 404 enhancement?
curl -sL https://ravendentalgroup.com/abc-yok-zaten-test | grep -c 'popular-categories'
```

### 4. Rollback (gerekirse)
```bash
# system/raven.ocmod.xml dosyasını sil
# Admin → Modifications → Refresh
# Eski davranışa döner (5 dakika)
```

## Validation

Tüm 27 operasyon'un `<search>` string'leri yerel kopyada (`code/`) match veriyor — uygulanmadan önce test edildi (`grep -F` ile).

XML syntax: `xmllint --noout` ile valid doğrulandı.

## Risk

| Risk | Olasılık | Etki | Mitigasyon |
|---|---|---|---|
| Search string ileride değişir (theme update) | Orta | Düşük | Patch uygulanmaz, eski davranış geri gelir (silent) |
| Twitter card image dimensions yetersiz (200x200) | Yüksek | Düşük | summary_large_image 1200x675 idealdir — DB'den `seoTwitterCardsImageDimensions` ayarı da güncellenmeli (ayrı SQL) |
| Mobile header `<span>`'da styling kaybı | Düşük | Düşük | CSS'te `h1.logo-text` selector'u `.logo-text` ile değiştirilmeli (sınırlı theme |
| Form `required` attribute eski browser'larda HTML5 validation eksikse JS lazım | Düşük | Düşük | Şimdilik gerekli — JS validation zaten var |
| `.lazyload` class ile JS-bazlı lazy loading varsa `loading="lazy"` ile çakışır mı | Düşük | Düşük | İkisi farklı: native vs JS. Çakışma olmaz |

## Bilinen Sınırlamalar

- **Image dimensions DB ayarları:** Twitter card 200x200 hâlâ `oc_journal3_setting.seoTwitterCardsImageDimensions` field'ında. Bu OCMOD ile değişmiyor — ayrı SQL UPDATE gerek.
- **CSS dosyasında `.logo-text` styling:** Theme CSS'te muhtemelen `h1.logo a` selector'u var, bu OCMOD sadece HTML'i değiştirdiği için CSS yedek selector eklenebilir.
- **`required` attribute server-side validation YERINE GEÇMEZ** — HTML5 sadece client hint, server tarafında zaten doğrulama var.

## Sıralı Uygulama (Önerilen)

Tek seferde 27 operasyonu uygulamak yerine **parça parça** yapmak istersen, XML'i bloklara böl:

1. **A** (seo.php — 3 op) — en güvenli, sadece controller
2. **B** (header.twig — 4 op) — viewport + meta tag'ler
3. **C** (forms — 10 op) — form UX, kullanıcı testi gerek
4. **D** (mobile h1 — 3 op) — CSS kontrolü gerekebilir
5. **E** (lazy loading — 4 op) — performans, anlık görülebilir
6. **F** (breadcrumb — 1 op) — SEO, GSC'den izlenebilir
7. **G** (404 — 1 op) — UX, manuel test

Her bloktan sonra: cache temizle + dış doğrulama.

## Sonraki

- VPS satın aldıktan sonra test ortamına önce uygula (test.ravendentalgroup.com)
- Test sonra production'a aktar
- ROADMAP'te Faz 4'e bağlı (`docs/12-ROADMAP.md`)
