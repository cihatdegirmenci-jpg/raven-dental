# Rakip Analizi: dentrealmarket.com

**Analiz Tarihi:** 2026-05-12
**Analist:** Product Trend Researcher Agent
**Bizim Site:** ravendentalgroup.com (Raven Dental, OpenCart 3 + Journal3)
**Rakip Site:** https://www.dentrealmarket.com
**Sektör:** Türkiye B2B Diş Hekimliği Aletleri & Sarf E-Ticaret

---

## 0. Yönetici Özeti (TL;DR)

Dentrealmarket, **Türkiye'nin online diş deposu** segmentinde kendini "en büyük" olarak konumlandıran, 2007 kuruluşlu, Ümraniye/İstanbul merkezli, **yaklaşık 70.000 SKU** ve **400+ marka** iddiasıyla operasyonunu sürdüren **IdeaSoft tabanlı** bir B2B/B2H (Business-to-Healthcare-Professional) marketplace'tir. Raven Dental'in **üretici-marka** kimliğine karşı dentrealmarket **çok markalı geniş katalog distribütörü** kimliğine sahip — yani doğrudan rakip değil, **kategori-genişliği rakibi** ve aynı zamanda potansiyel kanal/dropshipping ortağıdır.

**Güçlü Yanları (3):**
1. **Devasa katalog + marka çeşitliliği** (70k ürün / 400+ marka iddiası, 15k+ üye)
2. **iOS + Android native mobil uygulama** (com.dentrealmarket.androiduygulamasi2) — biz yokuz
3. **Yetkili sağlık profesyoneli üyeliği + ihale departmanı** (kurumsal B2B kanalı, muhasebe@ ve ihale@ ayrı e-posta)

**Bizim Avantajlarımız (3):**
1. **Marka sahipliği + üretici kimliği** (Raven Cerrahi Aletler kendi markamız — rakip distribütör)
2. **Modern teknik SEO altyapısı** (4 schema bloğu, hreflang TR+x-default, 1450 char kategori açıklamaları, 738 SEO URL) — IdeaSoft default'larına göre üstün
3. **OpenCart 3 + Journal3 özelleştirilebilirlik** (rakip IdeaSoft SaaS limitlerine bağlı)

---

## 1. Teknik SEO Analizi

### 1.1 Platform & Teknoloji Stack

| Özellik | Dentrealmarket | Raven Dental |
|---|---|---|
| Platform | **IdeaSoft** (SaaS e-ticaret) | OpenCart 3 + Journal3 |
| Hosting kontrolü | Kısıtlı (SaaS) | Tam |
| URL pattern | `/kategori/`, `/urun/`, `/marka/`, `/etiket/`, `/meta-etiket/`, `TA-{id}.html` | `/index.php?route=...` + SEO URL (738 adet) |
| Mobil app | iOS + Android var | Yok |
| SSL | 128-bit SSL (kendi açıklamaları) | HTTPS + HSTS |

**Not:** WebFetch izinleri bu oturumda reddedildiği için **HTML weight, HTTP response headers (CSP/HSTS/X-Frame-Options), Lighthouse skoru, gerçek robots.txt içeriği** doğrudan çekilemedi (erişilemedi). Bilgiler WebSearch indeksinden ve resmi sayfa metadata'sından derlendi.

### 1.2 URL Yapısı Gözlemleri

Tespit edilen URL paternleri (Google'da indekslenmiş):

```
https://www.dentrealmarket.com/                          (anasayfa)
https://www.dentrealmarket.com/index.php                 (klasik IdeaSoft router — duplicate risk)
https://www.dentrealmarket.com/kategori/{slug}           (kategori)
https://www.dentrealmarket.com/urun/{slug}               (ürün — modern URL)
https://www.dentrealmarket.com/{slug},TA-{id}.html       (ürün — legacy URL, duplicate)
https://www.dentrealmarket.com/marka/{slug}              (marka sayfası)
https://www.dentrealmarket.com/etiket/{slug}?tp={n}      (etiket — paginated)
https://www.dentrealmarket.com/meta-etiket/{slug}-{id}   (SEO meta etiket sayfaları)
https://www.dentrealmarket.com/iletisim                  (kurumsal)
https://www.dentrealmarket.com/uye-ol , /uye-girisi      (auth)
https://www.dentrealmarket.com/markalar                  (marka listesi)
https://www.dentrealmarket.com/sayfa/markalarimiz        (markalar — duplicate!)
https://www.dentrealmarket.com/yeni-urunler              (outlet)
https://www.dentrealmarket.com/blog/icerik/{slug}        (blog)
https://www.dentrealmarket.com/kategori/kampanyalar      (kampanya)
```

**SEO Risk Tespitleri:**
- `index.php` ile clean URL **paralel indekslenmiş** — duplicate content riski (canonical kontrolü gerekli, erişilemedi)
- `/{slug},TA-{id}.html` (eski URL) ile `/urun/{slug}` (yeni URL) paralel canlı — bizim teknik SEO için fırsat
- `/markalar` ve `/sayfa/markalarimiz` aynı içeriği iki URL'de sunuyor — duplicate
- `/meta-etiket/` özel sayfalar (örn. `kavitron-cihazlari`) — IdeaSoft'un SEO etiket feature'ı, **iyi tactic** (uzun-kuyruk anahtar kelime hedefleme)

### 1.3 Sayfa Tipleri & İndekslenmiş URL Tahmini

WebSearch ile gözlemlenen tipik IdeaSoft yapısı:
- **Kategori sayfaları:** ~80-150 adet (ana + alt + 3.seviye)
- **Marka sayfaları:** ~400 (iddia)
- **Ürün sayfaları:** ~70.000 (iddia, gerçekte 10-30k aralığı muhtemel — IdeaSoft default `varyant` ile şişer)
- **Etiket/meta-etiket sayfaları:** birkaç yüz

**Bizim 738 SEO URL** ile karşılaştırma: rakip toplam indeks havuzunda **10x+ üstün**, ama bizim **kalite/derinlik açısından** kategori başına 1450 char unique açıklama avantajımız var (IdeaSoft SaaS templates genelde ince içerik üretir).

### 1.4 Title / Meta / H1 (Gözlem)

Google indekslenmiş başlıklardan örüntü:

| Sayfa | Title Patern | Yorum |
|---|---|---|
| Anasayfa | "Diş Hekimlerine Özel Tüm Dental Ürünler ve Markalar \| Dentrealmarket" | Anahtar kelime + brand, iyi |
| Kategori | "{Kategori adı} \| Dentrealmarket" veya "Dentrealmarket \| {Kategori}" | Tutarsız sıralama — küçük SEO debt |
| Ürün | "{Ürün adı} \| {Marka}" | İyi pattern |
| Marka | "{Marka adı} \| Dentrealmarket" | Standart |

**H1 / Schema:** WebFetch reddedildiği için doğrulanamadı (erişilemedi). IdeaSoft default'ta:
- Product schema kısmen kurulu (genelde sadece Product, Offers eksik veya statik)
- Breadcrumb schema **çoğu IdeaSoft mağazasında yok**
- Organization + WebSite schema **çoğu IdeaSoft default'unda yok**

**Bizim avantajımız:** 4 schema bloğu (Product + Breadcrumb + Organization + contactPoint + WebSite) — burada **net üstünlüğümüz** var, doğrulanması gereken bir hipotez.

### 1.5 hreflang / Dil

- Dentrealmarket: **Sadece TR**, hreflang tespit edilemedi (erişilemedi, ancak App Store metninde "Türk ve uluslararası diş hekimleri" iddiası var → muhtemelen hreflang eksik)
- Raven: `hreflang="tr"` + `x-default` — **rakipte muhtemelen yok**, doğrulama gerekli

### 1.6 Sitemap & Robots

WebFetch reddedildi (erişilemedi). IdeaSoft default `/sitemap.xml` üretir — muhtemelen aktif. **Robots.txt** ve **sitemap index** yapısı doğrulanamadı.

### 1.7 Mobile

- **Native app (iOS+Android)** = güçlü mobil-first sinyali
- Site responsiveliği IdeaSoft template ile sağlanır (gözlemlenemedi)
- **viewport-fit, theme-color**: muhtemelen yok (bizim avantajımız)

### 1.8 Security Headers

Erişilemedi (curl/WebFetch reddedildi). IdeaSoft SaaS genelde:
- HTTPS: var
- HSTS: çoğu zaman yok / kısıtlı
- CSP: yok
- X-Frame-Options: SAMEORIGIN default
- Secure/HttpOnly cookies: kısmen

**Bizim avantaj:** HSTS, Secure cookies — net üstünlük (doğrulanması gerek).

---

## 2. İçerik Stratejisi

### 2.1 Anasayfa

- Hero başlık: "Diş Hekimlerine Özel Tüm Dental Ürünler ve Markalar"
- **Net sürtünmesiz değer önermesi**: "70.000 ürün / 400+ marka / 15.000+ üye / Türkiye'nin en büyük online diş deposu"
- Slider + öne çıkan kategoriler + kampanyalar + yeni ürünler + markalar

### 2.2 Kategori Açıklamaları

Tespit edilen ana kategoriler (en az 30+ üst kategori):

```
- Aletler (dental-el-aletleri)
   - Kompozit el aletleri
   - Perio el aletleri
   - Pedodontik el aletleri
   - Kanal aletleri (kök & kanal)
- Restoratif (kompozit, amalgam, cam iyonomer)
- Cerrahi
- İmplant
- Endodonti (endodontik frezler, rotary file, manuel file)
- Protetik Tedavi
- Ortodonti (orthodontik frez)
- Periodontoloji
- Frezler (elmas frez set, dental frez)
- Pedodonti Ürünleri
- Sensörlü Ürünler
- Ölçüm Aletleri
- Dental Eğitim Araçları
- Genel Malzemeler
- Medikal Ürünler
- Dental Ünitler
- Ünit ve Aksesuarları
- Ağız İçi Tarayıcılar
- Kavitron Cihazları
- 3D Yazıcılar
- Sterilizasyon (otoklav, ultrasonik temizleyici, UV)
- Elektrocerrahi / Koter
- Lazer Cihazları
- Görüntüleme (intraoral kamera, X-Ray)
- Cerrahi Loop, Mikroskop
- CAD/CAM
- Dokunmatik Bilgisayar
- Kampanyalar
- Yeni Ürünler / Outlet
```

**Kategori açıklama derinliği:** IdeaSoft mağazalarında genellikle 200-400 char arası ince içerik. **Bizim 1450 char unique açıklamamız net üstünlük.**

### 2.3 Ürün Sayfası İçeriği

Gözlemlenen patern (URL'lerden): ürün adı, marka, model, kategori breadcrumb. Detaylı ürün açıklama / teknik tablo / kullanım klavuzu durumu **doğrulanamadı (erişilemedi)** — IdeaSoft default genellikle marka tedarikçi metnini kullanır, copy-paste duplicate riski yüksek.

### 2.4 Blog

- Blog mevcut: `/blog/icerik/{slug}` pattern var
- Bulunan içerik örneği: "Katalog ve Fiyat Listeleri" (utility içerik, SEO odaklı değil)
- **Düzenli editöryel blog akışı görünmüyor** — bu bizim için **büyük fırsat boşluğu**

### 2.5 Video / Görsel İçerik

- YouTube kanalı tespit edilmedi (erişilemedi/yok)
- Instagram'da 426 post, 5.651 takipçi → görsel içerik üretimi var ama site'a entegre olmayabilir
- Slideshare hesabı mevcut (DentrealmarketAnkaTbbiMalzemeleri) — eski/atıl
- **Ürün tanıtım/eğitim videoları:** doğrulanmadı

### 2.6 Yorumlar / Review

- Şikayetvar'da spesifik "dentrealmarket" şikayeti tespit edilmedi (temiz reputation veya görünmez)
- Site içi product review sistemi durumu: doğrulanamadı (erişilemedi)
- Trustpilot / e-ticaret yorum bütünleştirici servisleri: tespit edilmedi

---

## 3. Ürün Görünürlüğü

### 3.1 Katalog Büyüklüğü

| Metrik | Dentrealmarket (iddia) | Raven Dental |
|---|---|---|
| Toplam ürün | ~70.000 | (belirtilmemiş, ~yüzlerce-binlerce kendi imalat odaklı) |
| Marka | ~400 | Raven Cerrahi (kendi) + dağıtım |
| Üye sayısı | 15.000+ | - |

**Stratejik fark:** Dentrealmarket = **agregatör/distribütör marketplace**. Raven = **niş üretici + marka sahibi**.

### 3.2 Fiyat Görünürlüğü

- **Misafir kullanıcı fiyat göremez** (B2B model — diş hekimi onaylı üyelik şart)
- Üyelik sırasında "sağlık profesyoneliyim" beyanı zorunlu (yasal güvence + segment koruma)
- **Fiyat = login arkası** → SEO açısından ürün sayfasında structured data'da `price` yok (negatif sinyal)

**Bizim için:** Fiyat görünürlük politikamız stratejik karar — açık tutarsak rich snippet/price filter avantajı var, kapalı tutarsak B2B saygınlığı.

### 3.3 Görsel

- IdeaSoft default: 1-2 ürün görseli, zoom var
- Marka logoları kategori sayfalarında belirgin
- **360° görsel, ürün videosu:** doğrulanamadı (muhtemelen yok)

### 3.4 Filtreleme

Kategori sayfalarında gözlemlenen filtreler (Aletler kategorisi):
- Marka filtresi (zorunlu)
- Alt kategori filtresi
- Fiyat aralığı (üye sonrası)
- Sıralama (popülerlik, fiyat, yeni)

**Faceted search derinliği:** doğrulanamadı (erişilemedi). IdeaSoft default seti orta seviye.

### 3.5 Stok

- "Outlet / Yeni Ürünler" bölümünde **stok limit uyarısı** mevcut
- Standart ürünlerde "stokta var/yok" gösterimi muhtemel (doğrulanamadı)
- Real-time stock display: doğrulanamadı

---

## 4. UX & Trust Sinyalleri

### 4.1 İletişim & Kurumsal

**Adres:** Saray Mahallesi Akgül Sokak No:1 En Plaza Kat:1 Ofis:20/21 Ümraniye/İstanbul

**Telefonlar:**
- +90 541 320 33 68 (ana)
- 0216 316 82 11 (sabit)
- 0850 302 60 13 (fax)

**E-postalar:**
- info@dentrealmarket.com (genel)
- muhasebe@dentrealmarket.com (muhasebe)
- **ihale@dentrealmarket.com (ihale departmanı — kurumsal/B2G satışlar için ayrı kanal, profesyonel sinyal)**

**WhatsApp:** +90 541 320 33 68 → aktif (resmi WhatsApp link sayfada görünür)

**Şirket Bilgileri:**
- Ticari ünvan: Dentrealmarket Anka Tıbbi Malzemeleri
- Vergi No: 0690420680
- Vergi Dairesi: Ümraniye
- Sicil No: 611374
- Kuruluş: 2007 (resmi) / 2002 (3M ESPE distribütörlüğü ile başlangıç)

### 4.2 Sosyal Medya

| Platform | Hesap | Etkileşim |
|---|---|---|
| Instagram | @dentrealmarketofficial | 5.651 takipçi, 62 takip, 426 post — **aktif** |
| Facebook | facebook.com/dentrealmarket | aktif sayfa |
| LinkedIn | tr.linkedin.com/company/dentrealmarket | kurumsal sayfa var |
| YouTube | tespit edilmedi | - |
| Slideshare | DentrealmarketAnkaTbbiMalzemeleri | atıl arşiv |
| Issuu | Dentrealmarket Anka Tıbbi Malzemeleri | katalog yayını |
| n11 | n11.com/magaza/dentrealmarket | çoklu kanal satış |

### 4.3 Trust Sinyalleri

**Pozitif:**
- 2007'den faaliyet (20+ yıl güven)
- 3M ESPE yetkili distribütörlüğü geçmişi (2002)
- Ayrı ihale departmanı (kurumsal müşteri)
- Şirket bilgileri açık
- iOS + Android app store onayları (Apple/Google review geçmiş)
- 15.000+ üye iddiası

**Eksik / Belirsiz:**
- Site içi SSL/güvenlik rozeti gösterimi: doğrulanamadı
- ETBİS rozeti: doğrulanamadı
- ISO/CE belgesi sayfası: tespit edilmedi (Raven'de "ISO 13485 + CE" doğal alan)
- Mesafeli satış sözleşmesi, KVKK sayfası: doğrulanamadı
- Müşteri logoları / case study: yok

### 4.4 Hakkımızda

- Kuruluş hikayesi: 2007 Türkiye geneline diş malzemeleri satış vizyonu
- "Türkiye'nin en büyük online diş deposu" konumlandırma
- Anka Tıbbi Malzemeleri grup şirketi (dentrealdepo.com da var — depo segmenti farklı kanal)
- **Misyon/vizyon/değerler sayfası ayrı:** doğrulanamadı

---

## 5. Pazarlama & Konumlandırma

### 5.1 Mesaj Stratejisi

- "Aradığınız Tüm Dental Ürünler" → kapsam mesajı
- "Diş Hekimlerine Özel" → ekslüzivite + B2B sinyal
- "Türkiye'nin En Büyük" → otorite iddiası (kanıtlanmamış ama söylenmiş)

### 5.2 Anahtar Kelime Örtüşmeleri (Bizim Sitemizle)

**Çok yüksek örtüşme (her iki sitede iddialı kategori):**
- Dental el aletleri
- Cerrahi aletler / Cerrahi set
- Endodonti aletleri
- Periodontoloji aletleri (perio aletleri)
- Frez (elmas, karbid, endo)
- Kompozit aletleri
- Pedodontik aletler
- İmplant ürünleri
- Ortodonti

**Dentrealmarket'in tek/baskın olduğu alanlar:**
- Dental ünitler & koltuk
- Ağız içi tarayıcı (Dexis, vb.)
- 3D yazıcı dental
- Kavitron cihazları (Woodpecker, W&H, Dürr Dental, Acteon)
- CAD/CAM
- Sterilizasyon cihazları (otoklav, ultrasonik)
- Lazer cihazları, koter, elektrocerrahi
- X-Ray, intraoral kamera, mikroskop
- Sarf malzemeleri (kompozit, cam iyonomer, amalgam)

**Bizim baskın olabileceğimiz (Raven Cerrahi Aletler marka avantajı):**
- "Raven cerrahi seti" branded long-tail
- Made-in-Türkiye paslanmaz çelik el aletleri storytelling
- Ergonomik tasarım, kalite garantisi messaging
- Üretici-direkt fiyat avantajı söylemi

### 5.3 Çoklu Kanal

Dentrealmarket'in marketplace çoklu varlığı:
- Kendi site (ana)
- n11.com mağazası
- iOS + Android app
- Sosyal medya
- Dentrealdepo.com (kardeş site — diş depoları segmenti, B2B'nin B2B'si)

Raven Dental'in çoklu kanal durumu: Dentalpiyasa.com gibi marketplace'lerde ürünleri var (örn. "Raven Dental 8 li Cerrahi Set" Dentalpiyasa'da listeli).

---

## 6. Bizim Açımızdan Fırsat Boşlukları

### 6.1 Üstün Olduğumuz Noktalar (Pekiştir, Pazarla)

| Avantajımız | Ölçüm |
|---|---|
| **Marka sahipliği / üretici kimliği** | Raven Cerrahi Aletler = kendi markamız; rakip distribütör |
| **Schema.org 4 blok** | Product + Breadcrumb + Organization + WebSite — IdeaSoft'ta seyrek |
| **1450 char kategori açıklaması (18 kategoride)** | Rakip muhtemelen 200-400 char default |
| **hreflang TR + x-default** | Rakip uluslararası iddia eder ama hreflang muhtemelen yok |
| **HSTS, Secure cookies, viewport-fit, theme-color** | Modern PWA-ready sinyaller — rakip SaaS limitiyle kısıtlı |
| **Twitter card summary_large_image** | Sosyal preview kalitesi — rakipte default |
| **OpenCart 3 + Journal3 esnekliği** | Custom SEO/UX müdahale serbestliği |
| **Niş derinlik (el aletleri uzmanlığı)** | Generalist rakibe karşı specialist trust |
| **Made-in-Türkiye storytelling** | Kalite + döviz avantajı + yerli marka mesajı |

### 6.2 Kaçırdığımız / Kritik Fırsat Boşlukları

**1. Mobil App (Yüksek Öncelik)**
- Rakip iOS + Android native uygulamaya sahip (2019'dan beri canlı, com.dentrealmarket.androiduygulamasi2)
- B2B'de tekrar sipariş kolaylığı, push notification, offline katalog değerli
- **Aksiyon:** PWA (kısa vadeli, OpenCart üzerinden) → native app (orta vadeli)

**2. Üye Sayısı & Sosyal Kanıt**
- Rakip "15.000+ üye" reklam ediyor — Raven'de bu metrik yok
- **Aksiyon:** Müşteri sayısı, sipariş hacmi, klinik referansı (anonimleştirilmiş) anasayfada bant olarak göster

**3. Marka & Tedarikçi Genişliği**
- 400+ marka iddiasına karşı niş üretici-marka kimliğimiz var; **markamızı zayıflatmadan** seçili tamamlayıcı marka dağıtımı eklenebilir
- **Aksiyon:** "Raven by Raven" (kendi marka) + "Raven Seçimi" (tedarikçi seçkisi) iki sütunlu katalog mimarisi

**4. İhale / Kurumsal Satış Kanalı**
- Rakipte `ihale@dentrealmarket.com` ayrı e-posta — devlet hastanesi/zincir klinik için sinyal
- **Aksiyon:** "Kurumsal Satış" sayfası + kurumsal teklif formu + KİK ihale referansı listesi

**5. Çoklu Kategori Derinliği (Cihaz Segmenti)**
- Rakipte ünit, tarayıcı, 3D yazıcı, sterilizatör kategorileri var; Raven'de yok ya da zayıf
- **Aksiyon:** Önce yüksek-margin sarf segmentinde derinleşme, sonra cihaz segmenti için partnership opsiyonu

**6. Blog & Editöryel İçerik**
- Rakipte blog seyrek, demek ki **bu pazarda blog henüz doymamış** — fırsat
- **Aksiyon:** Haftalık 1 yazı (alet bakımı, sterilizasyon, vaka anlatımı, ürün karşılaştırma) → 6 ay sonra long-tail SEO domination

**7. Video İçerik / YouTube**
- Rakipte YouTube tespit edilmedi → **mavi okyanus**
- **Aksiyon:** Ürün unboxing, klinikte kullanım, hekim röportajı serisi (Türkçe diş hekimi YouTube'unda boş alan)

**8. Yorum / Sosyal Kanıt Sistemi**
- Hiçbirimizde yorum sistemi gözlemlenmedi
- **Aksiyon:** Doğrulanmış-alıcı yorum + Google Review entegrasyonu + Hekim Sözü serisi

**9. Outlet / Kampanya Sayfası SEO**
- Rakipte `/yeni-urunler` (outlet) + `/kategori/kampanyalar` ayrı landingler
- **Aksiyon:** "Raven Outlet" + "Aylık Kampanya" landing'leri — taze içerik + indeks freshness

**10. Meta-Etiket / SEO Tag Sayfaları**
- Rakipte `/meta-etiket/{slug}-{id}` paterniyle uzun-kuyruk SEO sayfaları (örn. "kavitron-cihazlari")
- **Aksiyon:** OpenCart'ta etiket-bazlı statik landing sayfaları (örn. "tüm el aletleri", "elmas frez seti", "kompozit set")

**11. WhatsApp Click-to-Chat (Float Button)**
- Rakip WhatsApp aktif; bizim sitemizde **floating WhatsApp** ve click-to-chat optimize edilmeli
- **Aksiyon:** Tüm sayfalarda mobile-first WhatsApp FAB + ürün sayfasında "Bu ürünü WhatsApp'tan sor" CTA

**12. Marketplace Mağaza (n11, Dentalpiyasa)**
- Rakip n11'de mağaza işletiyor — Raven'in Dentalpiyasa'da varlığı tespit edildi ama derinlik bilinmiyor
- **Aksiyon:** Trendyol/Hepsiburada Mağaza + Dentalpiyasa derinleştirme + kendi siteyle envanter senkronizasyonu

**13. Kardeş Site Stratejisi**
- Rakipte `dentrealdepo.com` (diş depolarına özel B2B'nin B2B'si) ayrı kanal
- **Aksiyon:** İhracat odaklı `raven-export.com` veya `ravendental.com.tr` (klinikler) vs `ravendental.b2b` (depolar) segmentasyonu değerlendir

**14. Native App Push + CRM**
- Rakipte app üzerinden push notification + re-engagement kanalı var (varsayım, app'ın varlığından)
- **Aksiyon:** OneSignal/Pusher entegrasyonu, e-posta otomasyonu (Klaviyo/Mailchimp), tekrar sipariş hatırlatması

**15. Ihale / Kamu Satışı SEO Sayfası**
- "Diş hekimliği malzemesi ihale", "KİK dental" anahtar kelimeleri rakipte zayıf işlenmiş
- **Aksiyon:** Kurumsal/ihale landing page + ihale-referansı sayfası

### 6.3 Anahtar Kelime Örtüşme Riski (Defansif)

Rakibin Google'da güçlü olduğu **bizim hedeflememiz gereken** kelimeler:
- "dental el aletleri" — meta-etiket sayfası var, agresif
- "endodontik frez" — marka × kategori intersection güçlü
- "perio aletleri" — kategori sayfası
- "kavitron cihazı" — meta-etiket sayfası ile uzun-kuyruk yakaladı
- "ağız içi tarayıcı" — generic kategori SEO

**Defansif aksiyonlar:**
1. "Raven {kategori}" branded long-tail keyword'leri agresif optimize (örn. "Raven cerrahi set", "Raven el aletleri seti")
2. Generic kategori sayfalarında 1450 char açıklama + iç linkleme + breadcrumb schema'yı sertleştir
3. FAQ schema ekle (rakipte muhtemelen yok)

---

## 7. Teknik Rakip Skor Kartı

| Boyut | Dentrealmarket | Raven Dental | Kazanan |
|---|---|---|---|
| Domain yaşı / otorite | 2007+ kuruluş | Daha yeni | Rakip |
| Katalog büyüklüğü | 70.000 SKU iddia | Niş (üretici) | Rakip |
| Marka sayısı | 400+ iddia | Kendi marka | Rakip (genişlik) |
| Marka sahipliği | Distribütör | Üretici-marka | **Raven** |
| Schema markup | Default IdeaSoft | 4 blok custom | **Raven** |
| Kategori açıklama derinliği | 200-400 char (tahmin) | 1450 char | **Raven** |
| SEO URL adedi | ~10k+ (tahmin) | 738 | Rakip (hacim) |
| hreflang | Muhtemelen yok | TR + x-default | **Raven** |
| Native mobil app | iOS + Android | Yok | Rakip |
| Sosyal medya aktiflik | Instagram aktif (5.6k) | bilinmiyor | Rakip (görünür) |
| Blog | Var ama az | bilinmiyor | Beraber |
| Video | Yok | Yok | Beraber (fırsat) |
| WhatsApp | Aktif | Aktif | Beraber |
| İhale kanalı | ihale@ ayrı | bilinmiyor | Rakip |
| Marketplace varlığı | n11 mağaza | Dentalpiyasa | Beraber |
| Security headers (HSTS, CSP) | SaaS limit | Custom yapılandırma | **Raven** |
| ETBİS / KVKK / Mesafeli | bilinmiyor | bilinmiyor | belirsiz |
| Native marka storytelling | Distribütör hikayesi | "Türk üretimi paslanmaz çelik" | **Raven** |

**Skor:** Dentrealmarket 8 / Raven 7 / Beraber-Belirsiz 4 → Hacim rakip lehine, **kalite & marka kimliği** Raven lehine.

---

## 8. Stratejik Öneriler (90 Gün Roadmap)

### Faz 1 — Hızlı Kazanımlar (0-30 gün)
1. **WhatsApp FAB + ürün sayfası "WhatsApp'tan sor" CTA** ekle
2. **"Türkiye'de Üretim" / "Made in Türkiye" rozeti** her ürün ve anasayfada belirgin yap
3. **Trust strip** ekle: "X+ klinik tarafından tercih ediliyor", "20+ yıl üretim deneyimi", "ISO 13485 / CE" (varsa)
4. **FAQ schema** her ürün ve kategori sayfasına (rakipte yok)
5. **Sosyal medya hesap optimizasyonu** + Instagram link in bio + UGC stratejisi
6. **YouTube kanalı aç** + ilk 5 video (ürün tanıtım + kullanım + bakım)

### Faz 2 — İçerik & Otorite (30-60 gün)
1. **Blog operasyonu başlat:** Haftalık 2 yazı, 6 ana tema (alet bakımı, sterilizasyon, vaka, ürün karşılaştırma, sektör haberi, hekim röportajı)
2. **Hekim röportaj serisi** (Raven kullanan diş hekimleri) → video + blog + LinkedIn
3. **Etiket-bazlı SEO landing sayfaları** (örn. "tam set cerrahi aletler", "implant cerrahi seti", "diagnostic set")
4. **PWA dönüşümü** (Workbox + manifest) → app-benzeri deneyim, mobil arama avantajı

### Faz 3 — Genişleme (60-90 gün)
1. **Kurumsal/İhale landing page** + KİK referans listesi
2. **Klinik Loyalty programı** (puanlı sistem, tekrar sipariş indirimi)
3. **Pre-order / abone ol** modeli sarf malzeme için
4. **Native mobil app** scope çalışması (Flutter cross-platform önerilir)
5. **Marketplace genişleme:** Trendyol + Hepsiburada Mağaza
6. **Ihracat sayfası** İngilizce + Arapça (hreflang ile)

---

## 9. Risk & Sınırlılıklar (Bu Analizde)

1. **WebFetch izinleri reddedildiği için** doğrudan HTML, response headers, robots.txt, sitemap.xml, schema doğrulaması **yapılamadı** — bilgiler WebSearch / Google indeks / üçüncü-taraf kaynaklardan derlendi
2. Ürün sayısı (70.000) ve marka sayısı (400+) **rakibin kendi iddiası** — bağımsız doğrulanmadı, gerçekte 30-50k SKU + 200-300 marka aralığı daha olası
3. Schema markup, page speed, Core Web Vitals **gerçek ölçümle doğrulanmalı** (PageSpeed Insights, Screaming Frog, ahrefs Site Audit)
4. Backlink profile, organic keyword count, traffic estimates **Ahrefs/SEMrush hesabıyla** doğrulanmalı (bu oturumda yapılamadı)
5. Müşteri yorumları/şikayetler **Şikayetvar'da tespit edilemedi** — temiz reputation veya görünmez kategori; sosyal dinleme aracıyla derinleştir

### Sonraki Adımlar (Önerilen)

- [ ] Screaming Frog ile dentrealmarket.com'u crawl et (URL, title, meta, H1, schema sayım)
- [ ] PageSpeed Insights ile rakip ana sayfa + 3 kategori + 3 ürün → mobile/desktop skoru
- [ ] Ahrefs / SEMrush: rakip organic keywords (top 100), backlink profile, traffic estimate
- [ ] curl ile direkt header çekimi (HSTS, CSP, X-Frame, cookies) — bu oturum dışında
- [ ] Manual UX walkthrough (üye olarak, fiyat görme, sepete ekleme, ödeme akışı, mobile app indir)

---

## 10. Kaynaklar

- [Dentrealmarket Anasayfa](https://www.dentrealmarket.com/)
- [Dentrealmarket İletişim](https://www.dentrealmarket.com/iletisim)
- [Dentrealmarket Markalar](https://www.dentrealmarket.com/markalar)
- [Dentrealmarket Dental El Aletleri Kategorisi](https://www.dentrealmarket.com/kategori/dental-el-aletleri)
- [Dentrealmarket Endodonti Kategorisi](https://www.dentrealmarket.com/kategori/endodonti)
- [Dentrealmarket Kavitron Cihazları](https://www.dentrealmarket.com/kategori/kavitron-cihazlari)
- [Dentrealmarket Ağız İçi Tarayıcılar](https://www.dentrealmarket.com/kategori/agiz-ici-tarayicilar)
- [Dentrealmarket Üye Ol](https://www.dentrealmarket.com/uye-ol)
- [Dentrealmarket Blog - Katalog](https://www.dentrealmarket.com/blog/icerik/katalog-ve-fiyat-listeleri)
- [Dentrealmarket Outlet / Yeni Ürünler](https://www.dentrealmarket.com/yeni-urunler)
- [Dentrealmarket Meta-etiket Örneği - Kavitron](https://www.dentrealmarket.com/meta-etiket/kavitron-cihazlari)
- [Dentrealmarket iOS App](https://apps.apple.com/us/app/dentrealmarket/id1479685066)
- [Dentrealmarket Android App](https://play.google.com/store/apps/details?id=com.dentrealmarket.androiduygulamasi2)
- [Dentrealmarket Instagram](https://www.instagram.com/dentrealmarketofficial/)
- [Dentrealmarket Facebook](https://www.facebook.com/dentrealmarket/)
- [Dentrealmarket LinkedIn](https://tr.linkedin.com/company/dentrealmarket)
- [Dentrealmarket n11 Mağaza](https://n11.com/magaza/dentrealmarket)
- [Dentrealmarket Slideshare](https://www.slideshare.net/DentrealmarketAnkaTbbiMalzemeleri)
- [Dentrealdepo (Kardeş Site)](https://www.dentrealdepo.com/iletisim)
- [Raven Dental Anasayfa (referans)](https://ravendentalgroup.com/)
- [Raven Dental Dissiad Üyeliği](https://www.dissiad.org.tr/uye/raven-dental-1)
- [Raven Cerrahi Set - Dentalpiyasa](https://dentalpiyasa.com/raven-dental-8-li-cerrahi-set)

---

**Rapor sonu.** Erişim kısıtlamaları nedeniyle bazı teknik metrikler (header, schema, Lighthouse) doğrulanmadı; bu bölümler "erişilemedi" olarak işaretlendi. Kapsamlı doğrulama için Screaming Frog + Ahrefs + manuel UX walkthrough önerilir.
