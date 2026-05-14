# 22 — UI Implementation Rules (Mockup-fidelity)

> **STATUS:** Active (2026-05-13 itibarıyla zorunlu)
> **KAPSAM:** Tüm Journal3 modül/CSS/layout değişiklikleri
> **OWNER:** Cihat Değirmenci — review sırasında "mock tasarıma sadık kalmak lazım" dedi

---

## Ground Truth

**Mockup = source of truth.**

| URL | Amaç |
|---|---|
| http://localhost:8000/preview/raven-home.html | **Mockup** — design intent |
| http://localhost:8000/ | **Live** — implementation (must match mockup) |

Local Docker iki URL'i de serve eder. Her UI değişiklikten sonra **yan yana** karşılaştır.

---

## Zorunlu Kurallar

### 1. Mockup-aykırı sapma yapma

Eğer mockup'ta bir element/renk/spacing varsa, live'da da olmalı. Eksiksiz.

**Yasak:**
- Mockup'ta görünen text'i `display: none` ile gizlemek (örn: branş daire altındaki "Cerrahi", "İmplantoloji" label'ları)
- Mockup rengiyle live rengin farklı olması
- Mockup'taki section'ı atlamak / silmek

**İzinli sapma:**
- Login-gated pricing (sektör gereği, mockup'ta da uygulandı — `body:not(.customer-logged)` koşulu)
- Mobile responsive uyarlamalar (mockup desktop-only, mobile davye-paterni)
- Kullanıcı onayı ile yeni innovation eklemesi

### 2. Her UI commit öncesi 6-kontrol

Aşağıdaki listeyi sıraya geç. Ardından commit/deploy yap.

- [ ] Mockup'ta var olan her bölüm live'da da var mı?
- [ ] Renk paleti aynı mı? (`#0f3a6b` primary blue, `#fff` bg, `#070707` ink, `#fafafa` alt-bg)
- [ ] Inter font yüklü ve uygulanıyor mu?
- [ ] Section title'lar `.r-section-title__inner` paterni — 20px / 600 / `#070707` + 3px Raven blue alt-çizgi?
- [ ] Branş daire altında kategori adı görünüyor mu?
- [ ] Spacing scale (4/8/12/16/24/32/40/48px) hareket sırasında bozulmadı mı?

### 3. CSS yazarken `display: none` kuralı

`display: none !important` yazmadan önce 2 soru sor:
1. Bu element mockup'ta görünüyor mu? Eğer evet → **gizleme**.
2. Bu Journal3 demo content mı (Lorem text, demo testimonials, vb.)? Sadece o durumda gizle.

**Örnek hata (2026-05-13):**
```css
/* Yanlış — bu kural branş daire altındaki "Cerrahi"/"İmplantoloji" label'larını gizledi */
.module-info_blocks-290 .info-block-content,
.module-info_blocks-290 .info-block .content {
  display: none !important;
}
```
Mockup'ta o label'lar görünüyordu. Bu kural mockup-aykırı.

**Doğrusu:** Spesifik child element'i gizle, container'ı değil:
```css
/* Sadece .info-block-text'i gizle, title görünür kalsın */
.module-info_blocks-290 .info-block-text { display: none !important; }
```

### 4. Journal3 default'ları mockup'a uydur

Journal3 default'tan farklı olan ana mockup token'lar:

| Token | Journal3 default | Mockup (Raven) | Override |
|---|---|---|---|
| Primary blue | `rgba(13,82,214)` `#0d52d6` | `rgba(15,58,107)` `#0f3a6b` | `oc_journal3_variable.Accent 2` |
| Font | Montserrat | Inter | customCSS `body, .module-title { font-family: 'Inter' }` |
| Section title | gradient bg + center align | 3px solid blue underline + left align | customCSS `.route-common-home .module-title h3` |
| Card border-radius | 7px | 0px | customCSS `.r-card` override |
| Grid row bg | dark anthracite | white/transparent | Layout 1 `row.options.background` clear |

### 5. Mockup güncellenirse, doc'a yansıt

Mockup değiştiğinde (yeni section, yeni renk, vb.) **22-UI-RULES.md ve 19-UI-DESIGN-BRIEF.md** güncellenmeli. Eski mockup ile yeni implementasyon karıştırılmaz.

---

## Geçmiş sapma örnekleri (ders niteliğinde)

| Tarih | Sapma | Nasıl tespit edildi | Düzeltme |
|---|---|---|---|
| 2026-05-13 | Branş daire altında label kayboldu | Kullanıcı: "mockda kategori görsellerinin altında kategori isimleri neden yok" | `.info-block-content` `display: block` ile geri açıldı |
| 2026-05-13 | Menü rengi Journal3 default mavi (#0d52d6) | Kullanıcı: "menüdeki renkler neden değişmedi" | `oc_journal3_variable Accent 2` → #0f3a6b |
| 2026-05-13 | Grid row dark anthracite bg | Kullanıcı: "burda arka plan koyu olan doğru mu" | Layout 1'in 7 row'unda `background-color` cleared |
| 2026-05-13 | English-baked branş banner text | Kullanıcı: "general uyumsuz çok kötü" | Asset'ler text-free yeniden üretildi, TR text CSS overlay |

---

## Hızlı erişim

- Mockup HTML kaynak: `/tmp/raven-home-mockup.html` (master copy)
- Live customCSS: `oc_journal3_setting WHERE setting_group='custom_code' AND setting_name='customCSS'`
- Brand variable: `oc_journal3_variable WHERE variable_name='Accent 2'`
- Home layout: `oc_journal3_layout WHERE layout_id=1`
