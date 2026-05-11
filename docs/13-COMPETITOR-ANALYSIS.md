# 13 - Competitor Analysis (Rakip Analizi)

> **Durum:** İskelet — gerçek analiz Faz 3'te yapılacak.
> Hedef: TR pazarındaki diş hekimliği aleti satıcılarının SEO, ürün, fiyat stratejisini anlamak.

## Pazar Genel Bakış

### Sektör
- B2B + B2C diş hekimliği aletleri / sarf malzemeleri
- Birincil müşteri: Diş hekimleri, klinikler, dental laboratuvarlar
- İkincil: Dental öğrenciler, eğitim kurumları
- Türkiye'de ~35.000 aktif diş hekimi (TDB istatistik)

### Pazar büyüklüğü (Tahmini)
- Yıllık ~₺500M-1B (Türkiye)
- Sarf + ekipman + tek kullanımlık + protez malzemeleri toplamı
- COVID sonrası %15-20 büyüme

## Tespit Edilecek Rakipler (TODO — araştırılacak)

### Birincil (büyük TR sağlayıcılar)
- [ ] **dentaltedarik.com** — büyük envanterli portal
- [ ] **ucalsis.com** — eski oyuncu
- [ ] **dentaltop.com** — anahtar kelime hedefli
- [ ] **dentamed.com.tr**
- [ ] **denteks.com**
- [ ] **megamed.com.tr**

### İkincil (niş sağlayıcılar)
- [ ] Marka distribütörleri (Komet, NSK, Bien-Air, Mani vb. distribütörleri)
- [ ] İmplant şirketleri (Straumann, Megagen, Implant Direct distribütörleri)
- [ ] Online marketplace: Trendyol, Hepsiburada (dental kategori)

### Marka üreticileri (TR'de üretim yapan)
- [ ] **NSK Türkiye distribütörü**
- [ ] **Tosse** (sarf)
- [ ] **Mani** (eğeler)
- [ ] **Brasseler** distribütörü

## Analiz Çerçevesi

Her rakip için doldurulacak:

### A. SEO Görünürlük
- Domain Rating (Ahrefs/Moz tahmini)
- Aylık organik trafik tahmin (SimilarWeb)
- En iyi anahtar kelimeler (1. sayfa Google)
- Backlink sayısı (kaba)
- Schema markup kullanımı
- Sitemap yapısı

### B. İçerik Stratejisi
- Blog var mı? Kaç makale?
- Hangi anahtar kelimeleri hedefliyor?
- Ürün açıklama uzunluğu (genel ortalama)
- Kategori açıklamaları (uzun mu kısa mı?)
- Video içerik var mı? (YouTube kanalı)

### C. Ürün Görünürlüğü
- Ürün sayısı
- Görsel kalitesi (resim sayısı, açı, zoom)
- Fiyat şeffaflığı (login gerektiriyor mu?)
- Filtre sistemi gelişmiş mi?
- Sosyal kanıt: yorum, puan, sipariş sayısı

### D. UX
- Site hızı (Lighthouse)
- Mobile responsive
- Checkout süreç (3-4 adım vs tek sayfa)
- Sepet/kart işlemleri
- Müşteri hizmetleri (canlı destek, WhatsApp)

### E. Pazarlama
- Newsletter
- Sosyal medya aktif mi (Instagram, LinkedIn)
- Google Ads varlığı (rakip kelime testleri)
- Etkinlik/fuarda görünür mü
- B2B vs B2C odaklılık

## Analiz Çıktıları (Faz 3'te oluşturulacak)

- [ ] `analysis/competitors/dentaltedarik-com.md`
- [ ] `analysis/competitors/ucalsis-com.md`
- [ ] `analysis/competitors/dentaltop-com.md`
- [ ] ... (her rakip için ayrı md)
- [ ] `analysis/competitor-summary.md` — karşılaştırma tablosu

## Anahtar Kelime Çakışmaları (Tahmini)

Raven Dental'in hedeflediği anahtar kelimeler için TOP 10 sıralamadaki muhtemel rakipler:

| Anahtar Kelime | Beklenen 1. sayfa rakipleri |
|---|---|
| "diş hekimliği aletleri" | dentaltedarik, ucalsis, dentaltop, dentamed |
| "endodonti aletleri" | mani.tr, dentaltedarik, brasseler |
| "implant aletleri" | implant distribütörleri, dentaltedarik |
| "toptan diş aletleri" | dentaltedarik, ucalsis (B2B odaklı) |
| "ortodonti pens" | dentaltedarik, ucalsis, ortodonti-özel sağlayıcılar |

## Fırsat Boşlukları (Tahmini)

Bu boşluklar daha sonra valide edilecek:

1. **Türkçe içerik kalitesi:** Çoğu rakip ürün listesinde "thin content" — uzun ürün açıklaması ve uzun-kuyruk anahtar kelime fırsatı var.
2. **Marka SEO:** "Raven Dental" + "Raven Cerrahi" kombinasyonu — niş, korunabilir.
3. **Mobile UX:** Birçok eski-stil rakip site mobile-zayıf — Lighthouse 90+ rakip yok mu?
4. **Video içerik:** YouTube'da Türkçe diş hekimliği aleti tutorial videoları az. Fırsat.
5. **Blog:** Çoğu rakip blog ihmal etmiş. Uzun-kuyruk için açık.
6. **Schema markup:** Çoğu rakipte Product schema eksik. Bizim Journal3 + customCodeHeader avantajı.

## Faz 3 Görev Listesi

- [ ] 10 rakibi araştır + ekran görüntüsü al
- [ ] Her rakip için A-E framework doldur
- [ ] Anahtar kelime karşılaştırma (Ahrefs Free, Ubersuggest, KeywordTool)
- [ ] Top 50 organic keyword çakışması
- [ ] Backlink kaynağı incele (kim onlara link veriyor?)
- [ ] Fiyat karşılaştırma (10 popüler ürün)
- [ ] Müşteri yorumları analizi (Google Reviews, Trustpilot, Şikayetvar)
- [ ] SWOT matrisi (Raven vs market)

## Rakip Analizi Araçları

| Araç | Ücret | Kullanım |
|---|---|---|
| **SimilarWeb** | Ücretsiz (sınırlı) | Trafik tahmini |
| **Ahrefs Free Tools** | Ücretsiz | Backlink checker, broken link checker |
| **Ubersuggest** | Ücretsiz (3 query/gün) | Keyword volumes |
| **KeywordTool.io** | Ücretsiz (sınırlı) | Keyword suggestions |
| **Google Search** | Ücretsiz | Manuel sıralama kontrol |
| **WhatRuns / Wappalyzer** | Ücretsiz extension | Tech stack tespit |
| **GTmetrix** | Ücretsiz | Performance karşılaştırma |
| **PageSpeed Insights** | Ücretsiz | Lighthouse rakip karşılaştırma |
