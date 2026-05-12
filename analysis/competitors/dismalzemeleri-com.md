# Rakip Analizi: dismalzemeleri.com (DS Diş Deposu)

> **Hazırlayan:** Raven Dental — Pazar İstihbaratı  
> **Tarih:** 2026-05-12  
> **Karşılaştırma Hedefi:** ravendentalgroup.com (Raven Dental, B2B diş aletleri, OpenCart 3 + Journal3)  
> **Analiz Edilen Domain:** https://www.dismalzemeleri.com/ (kanonik: `www` subdomain)  
> **Şirket:** DS Tıbbi Cihazlar ve Diş Malzemeleri İth. İhr. San. ve Tic. Ltd. Şti. (DS Diş Deposu)  
> **Konum:** Fidanlık Mah. Adakale Sok. No:16/B Kızılay, Çankaya / ANKARA  
> **Kuruluş:** 2015 (10 yıllık geçmiş — Mayıs 2026 itibarıyla)

---

## 0. Yöntem ve Erişim Notu

- **Birincil yöntem:** WebFetch (hedef sayfa indirip parse) bu oturumda **izin reddedildi** (`Permission to use WebFetch has been denied`). Bu nedenle ham HTML / başlık / meta / JSON-LD / response header'ları doğrudan görüntülenemedi.
- **İkincil yöntem:** WebSearch üzerinden Google indeks snippet'leri, SERP başlıkları, kategori sayfa başlıkları ve şirketin DİŞSİAD üyelik/ Facebook profili çapraz doğrulamaları kullanıldı.
- **Erişilemedi olarak işaretlenen alanlar:** robots.txt içeriği (verbatim), sitemap.xml URL sayımı, ham `<head>` (title/meta/canonical/hreflang/JSON-LD), HTTP response header'lar (HSTS, CSP, X-Content-Type-Options, Cookie flagleri), HTML weight (KB), GA/GTM/Pixel ID, Core Web Vitals.
- **Çıkarımsal değerlendirmeler** "(çıkarım)" etiketi ile işaretlenmiştir.

Bu eksiklikler Bölüm 11'de "Doğrulanması Gereken Hipotezler" olarak listelendi; sonraki turda `curl -I` veya direkt erişim ile kapatılması önerilir.

---

## 1. Teknik SEO

### 1.1 URL Yapısı (Google SERP'ten doğrulandı)

DS Diş Deposu, **özel slug + tip prefix + numeric ID** kalıbıyla SEO-friendly URL üretiyor:

| Prefix | Anlam | Örnek |
|---|---|---|
| `-pmk{n}` | Product Manufacturer Category (kategori) | `/kompozit-pmk155`, `/protetik-pmk156`, `/sterilizasyon-cihazlari-pmk10`, `/dinamik-el-aletleri-pmk36`, `/kanal-tedavisi-endodonti-pmk12`, `/anestezi-cihazlari-pmk196`, `/kompresorler-pmk97`, `/yedek-parca-pmk81`, `/cerrahi-urunler-pmk74`, `/dental-medikal-sehpalar-pmk158`, `/dental-filmleri-pmk134`, `/koruyucu-urunler-pmk90`, `/ogrenci-malzemeleri-pmk95`, `/separeler-pmk84`, `/alcilar-pmk79`, `/firinlar-pmk257`, `/fulvarlar-pmk108`, `/pensler-pmk111`, `/karistirma-spatulasi-pmk106`, `/besleme-materyalleri-pmk83` |
| `-pmu{n}` | Product Unit (tekil ürün) | `/woodpecker-dte-drive-x-fizyodispenser-pmu4591`, `/3m-filtek-supreme-flowable-restoratif-refill-siringa-xw-3930xw-pmu2820`, `/voco-meron-plus-qm-rezin-esasli-cam-iyonomer-yapistirma-simani-3x85gr-paket-pmu2032`, `/dentsply-sirona-x-smart-plus-batarya-pmu3435`, `/calamus-dual-gutta-percha-kartus-10-lu-pmu1826`, `/tpc-portatif-dis-uniti-6-cikisli-mobil-dental-unit-tasinabilir-klinik-konforu-pmu1425` |
| `-pmt{n}` | Product Manufacturer Tag (etiket/tag) | `/dental-el-aleti-pmt1126`, `/dental-unit-pmt110`, `/dental-unit-fiyat-pmt10230`, `/dental-reflektor-pmt4549`, `/vita-dental-pmt4415`, `/cerrahi-suture-pmt6168`, `/saksin-pmt6233`, `/extracem-pmt8367`, `/cresol-pmt6937`, `/kok-kanal-dolgu-materyali-pmt3880`, `/kok-kanal-dezenfektani-pmt583`, `/dis-hekimligi-ogrenci-malzemeleri-pmt2282`, `/gc-dental-kompozit-pmt3742`, `/escom-dental-kompozit-pmt2988`, `/alet-pmt2214`, `/cerrahi-aspirator-ucu-pmt5713`, `/isil-olcu-pmt3181`, `/kanal-tedavisi-malzemesi-pmt6935` |
| `-pmn{n}` | News/announcement (haber) | `/ds-dis-deposu-ankara-kusun-onluk-yetkli-bayi-pmn5` |
| `-pmb{n}` | Blog post | `/blog/ds-dis-deposu-aerator-nedir-pmb6` |

**Çıkarımlar:**
- Numeric ID'ler **`pmu4591`, `pmt10230`** seviyesinde — toplam stok/etiket sayısının çok büyük olduğunu gösteriyor (5000+ ürün ve 10000+ tag, çıkarım).
- URL'ler **TR-only keyword-rich**: `kanal-tedavisi-endodonti`, `dinamik-el-aletleri`, `dental-medikal-sehpalar` — Türkçe arama hacmi yakalanıyor.
- `-pmn` (haber/news) ve `/blog/...-pmb{n}` slot'ları var; **CMS bazlı haber + blog modülü aktif** (çıkarım: özel PHP framework veya OpenCart benzeri modüler altyapı, bkz 1.5).
- Tag sayfaları (`-pmt`) tag sayısı 10000+ ürün başına 2-5 etiket varsayımıyla çok yüksek — **iç içerik silosu** ile uzun kuyruk SEO yakalama stratejisi (çıkarım, güçlü).

### 1.2 Title / Meta Pattern (SERP başlıklarından)

SERP'ten gözlenen başlık kalıbı:

| Sayfa Tipi | Title Pattern (gözlem) |
|---|---|
| Anasayfa | `DS Diş Deposu \| Diş Malzemeleri ve Klinik Ekipmanları` |
| Kategori (kısa) | `{Kategori} Modelleri ve Fiyatları - DS Diş Deposu` (ör. `Protetik Modelleri ve Fiyatları`, `Alçılar Modelleri ve Fiyatları`, `Süturlar Modelleri ve Fiyatları`, `Kompozit Modelleri ve Fiyatları`, `Klinik Cihazlar Modelleri ve Fiyatları`, `Anestezi Cihazları Modelleri ve Fiyatları`, `Koruyucu Ürünler Modelleri ve Fiyatları`, `Öğrenci Malzemeleri Modelleri ve Fiyatları`, `Besleme Materyalleri Modelleri ve Fiyatları`, `Fırınlar Modelleri ve Fiyatları`) |
| Kategori (uzun, ürün-zengin) | `{Kategori} – {Faydaya yönelik tag-line}` (ör. `Dental Sterilizasyon Cihazları – Güvenli ve Etkili Temizlik`, `Dental Kompresörler – Güçlü, Sessiz ve Yağsız`) — **modern marketing copy** |
| Servis sayfası | `Yedek Parça - Dental Ünitler - DS Diş Deposu - Teknik Servis`, `Dental Ünitler « DS Diş Deposu - Teknik Servis` |
| Etiket (tag) | `{Keyword} kelimesi için etiket sonuçları « DS Diş Deposu \| Diş Malzemeleri ve Klinik Ekipmanları` — **kötü uygulama** (boilerplate, "kelimesi için etiket sonuçları" patterni kazanılan keyword space'i sulandırıyor) |
| Ürün | `{Marka} {Model} {Özellik} - Dismalzemeleri.com` (ör. `Voco Meron Plus QM Rezin Esaslı Cam İyonomer Yapıştırma Simanı (3x8,5gr paket) - Dismalzemeleri.com`) |
| Blog | `{Yazı başlığı} - DS Diş Deposu` (örn. aeratör nedir) |

**Güçlü taraf:** Kategori başlıklarında "**Modelleri ve Fiyatları**" intent-rich modifier var — Türk B2B alıcı bu kalıbı SERP'te çok arıyor.

**Zayıf taraf:** Tag sayfa başlığı `« DS Diş Deposu` separator + "kelimesi için etiket sonuçları" boilerplate'i index şişirmesine ve cannibalization'a yol açıyor (örn. `kompozit-pmk155` vs `gc-dental-kompozit-pmt3742` vs `escom-dental-kompozit-pmt2988`). Robots.txt'de `noindex` tag yoksa ciddi index bloat riski (Bölüm 11.A).

### 1.3 H1 / Heading Hiyerarşi (çıkarım)

WebFetch erişilemedi — **erişilemedi**. SERP başlık örüntüsünden H1'in büyük olasılıkla title'la birebir uyumlu (`{Kategori} Modelleri ve Fiyatları`) olduğu tahmin ediliyor.

**Raven kıyas:** Raven'in 18 kategorisi 1450 char açıklama + Schema.org Product blokları ile H1+H2+H3 hiyerarşisi (giriş, alt-tip, kullanım, marka, sterilizasyon notları) — Raven daha derin (çıkarım).

### 1.4 Schema.org / Yapısal Veri

WebFetch erişilemedi — JSON-LD blokları doğrulanamadı. **Erişilemedi**.

SERP zenginleştirmesi (rich result) gözlemi:
- Ürün sayfalarında SERP'te fiyat görünmüyor (Search snippet'lerinde "TL" yok).
- Breadcrumb rich result snippet'lerde görünmüyor — büyük olasılıkla `BreadcrumbList` schema YOK veya eksik (çıkarım, fırsat).
- Organization / contactPoint / SiteSearch — doğrulanamadı.

**Raven avantajı:** 4 blok schema (Product + BreadcrumbList + Organization + WebSite + contactPoint) — DS rakibinin önünde olduğumuz net alan (çıkarım, %85 güven).

### 1.5 CMS / Platform Tespiti

WebFetch erişilemedi. URL pattern (`-pmk/-pmu/-pmt/-pmb/-pmn` + numeric ID) **OpenCart, PrestaShop veya WooCommerce'a benzemiyor**. Bu kalıp Türkiye pazarında özel olarak **Ticimax, Tsoft veya İdeasoft** gibi yerel B2C/B2B platformlarına çok benzer; alternatif olarak custom PHP CMS. **Erişilemedi — doğrulama gerekli** (Bölüm 11.B).

**Raven (OpenCart 3 + Journal3) avantajları:**
- OpenCart 3 ekosistem (SEO URL eklentileri, 738 keyword-rich URL halihazırda yapılandırılmış).
- Journal3 ile modern UI/UX ve hız.
- Schema.org tam kontrol.

### 1.6 Sitemap & Robots

**Erişilemedi.** `/sitemap.xml` ve `/robots.txt` doğrudan indirilemedi. Hipotezler:
- `pmu` ID'leri **4500+** seviyede görüldü; aktif ürün sayısı (hayalet + arşiv dahil) **3000-5000 aralığında** (çıkarım).
- Tag sayfalarının `Disallow`'da olup olmadığı **kritik** — yoksa index bloat ciddi sorun.

**Raven karşılaştırma:** Raven'in 738 SEO URL'i ile **birim ürün hacmi olarak DS'in altında**. Ama DS'in tag/etiket sayfaları kontrolsüz index ediliyorsa Raven daha *temiz* index'e sahip (çıkarım, fırsat).

### 1.7 Güvenlik Header'ları

**Erişilemedi** — HSTS, CSP, X-Frame-Options, Referrer-Policy, X-Content-Type-Options, Permissions-Policy, Set-Cookie flag'leri (Secure/HttpOnly/SameSite) doğrulanamadı.

**Hipotez:** Yerel Türk e-ticaret platformlarının çoğu yalnızca HTTPS sağlar; HSTS preload ve modern CSP nadiren görülür.

**Raven kazanım:** HTTPS + HSTS + Secure cookies → DS'de aynı seviyenin olduğu **şüpheli** (Bölüm 11.C).

### 1.8 Mobil & Viewport

**Erişilemedi.** SERP'te "mobile-friendly" etiketi 2016'dan beri varsayılan; muhtemelen responsive.

**Raven üstünlük:** `viewport-fit=cover` + `theme-color` — iOS PWA-benzeri görsel. DS bu detayları muhtemelen kullanmıyor (çıkarım).

### 1.9 hreflang / i18n

DS yalnızca TR pazarına hizmet veriyor; SERP'te EN sayfa bulunamadı. hreflang muhtemelen **YOK**.

**Raven kazanım:** hreflang `tr` + `x-default` — uluslararası satış (özellikle İstanbul / Fatih konumlu, ihracat odaklı) için ileri görüş. DS bu alanda geride.

### 1.10 HTML Weight / Performance

**Erişilemedi.** Core Web Vitals (LCP/INP/CLS) verisi PageSpeed Insights ile alınmadı.

---

## 2. İçerik Stratejisi

### 2.1 Anasayfa Hero & Mesajlaşma

SERP snippet'lerinden anasayfa kopyası:
- **Tagline:** "5.000'den fazla diş hekimi ve diş kliniğinin güvenle tercih ettiği diş deposu"
- **Değer önermeleri:** "stoktan hızlı teslimat, orijinal marka garantisi, sürekli yenilenen ürün çeşitliliği"
- **Profesyonel temas:** "satış temsilcilerimizle iletişime geçerek, orijinal ürünlerde size özel fiyat teklifleri" → **B2B kişisel teklif** akışı vurgulu
- **Hizmet farklılaşması:** Dental Servis (ünit, kompresör, röntgen tamiri) — sadece satış değil, **after-sales servis**

**Raven kıyas:** Raven'de büyük olasılıkla daha çok ürün-odaklı mesajlaşma var; **after-sales servis** mesajı DS'in önemli farklılaştırıcısı.

### 2.2 Kategori Açıklamaları

DS, en az 2 farklı kategori başlığı stilinde çalışıyor:
- **Standart:** `{X} Modelleri ve Fiyatları` (geleneksel TR e-ticaret)
- **Marketing-rich:** `Dental Sterilizasyon Cihazları – Güvenli ve Etkili Temizlik`, `Dental Kompresörler – Güçlü, Sessiz ve Yağsız` — modern fayda-odaklı

**Çıkarım:** Tüm kategorilerde tutarlı **1000+ char açıklama** olduğu belirsiz; bazıları zengin, bazıları boş olabilir.

**Raven üstünlük:** 18 kategoride **1450 char standardize edilmiş açıklama** — DS bunu tüm 50+ kategoride standardize etmemiş olabilir (çıkarım, fırsat).

### 2.3 Ürün Açıklamaları

Snippet'lerden gözlem:
- Ürün adlarında **çok detaylı tekil bilgi** yer alıyor: marka, model, paket bilgisi, kullanım alanı (örn. "Voco Meron Plus QM Rezin Esaslı Cam İyonomer Yapıştırma Simanı (3x8,5gr paket)").
- Bazı ürünlerde fayda copy var: "Yüksek Performanslı LED Polimerizasyon", "Geniş Spektrumlu LED Teknoloji", "AI algoritma kontrolü, hassas ilaç verme".
- TPC Portatif Diş Üniti gibi premium kalemlerde "Taşınabilir Klinik Konforu" gibi marketing tag-line'lar.

**Çıkarım:** Üst-seviye flagship ürünler (Woodpecker, Dentsply Sirona, 3M Filtek, Voco) detaylı işlenmiş; uzun kuyruk sarf ürünleri büyük olasılıkla **şablon açıklamalı**.

### 2.4 Blog

- **Birincil blog:** `dismalzemeleri.com/blog/` altında `-pmb{n}` slot'u var (örn. `ds-dis-deposu-aerator-nedir-pmb6`). Yazı sayısı görünür şekilde **az** (pmb6 → ~6-10 yazı, çıkarım).
- **İkincil/yan blog:** `dismalzemeleri.net` — ayrı domain, **konu otoritesi (topical authority) için içerik ağı stratejisi**. Yazılar: "Aeratör Mikromotor Takılır Mı?", "Diş Hekimliğinde Kullanılan Malzemeler", "Diş Hekimliğinde Kullanılan Işık Cihazları", "Dişçilerin Kullandığı Diş Cilası", "Diş Malzemeleri Satan Yerler", "Diş Hekimliği Alanları".
- **Strateji:** `.net` blog → bilgi içeriği + iç bağlantılarla `.com` ticaret sitesine PA (page authority) ve referral trafiği taşıma. **Bu, profesyonel SEO içerik mimarisi göstergesi**.

**Raven Fırsat:** Raven'de blog yok veya minimal (varsayım — onaylanmalı). Bu DS'in **en büyük SEO silahı** ve Raven'de **kritik boşluk**.

### 2.5 Video

SERP'te YouTube embed referansı veya video schema gözlenmedi. **Yüksek olasılıkla video içerik YOK** (çıkarım).

**Fırsat:** Raven için video (ürün unboxing, sterilizasyon prosedürü, ünit kullanımı) ile farklılaşma alanı.

### 2.6 Yorum / Review

SERP'te yıldız (rating) rich snippet'i görünmüyor. **Müşteri yorumu sisteminin aktif olmaması veya schema'nın eksik olması muhtemel** (çıkarım).

**Fırsat:** Raven'de doğrulanmış-hekim yorumu + Review/AggregateRating schema → SERP'te yıldız → CTR uplift.

---

## 3. Ürün Görünürlüğü

### 3.1 Toplam Ürün Tahmini

- `pmu4591` ID gözlendi → en az **~4500 ürün ID** tahsis edilmiş.
- Aktif/yayında ürün hacmi tahminen **3000-5000** (çıkarım, ±20%).
- Tek bir alt kategori örneği: **Kanal Dolgu Maddeleri 48 ürün**, **Dinamik El Aletleri 91 ürün**, **Kompozit 87 ürün**, **Öğrenci Malzemeleri 72 ürün**, **Besleme Materyalleri 9**, **Koruyucu Ürünler 8**.

**Raven kıyas:** Raven 738 SEO URL → ürün sayısı muhtemelen **300-500 aralığında** (URL'lerin bir kısmı kategori/tag). DS, ürün hacminde **~10x avantaj** (çıkarım, kritik).

### 3.2 Fiyat — Login zorunlu mu?

**Erişilemedi.** SERP snippet'lerinde "TL" değerleri görünüyor → **muhtemelen fiyat public** (login zorunlu değil). Ama "satış temsilcilerimizle iletişime geçerek size özel fiyat teklifleri" söylemi → B2B özelleştirilmiş fiyat var.

**Hipotez:** Public list price + giriş yapanlara özel iskonto/teklif (hibrit model).

**Raven kıyas:** Raven'in toptan (B2B) login flow'u var. Stratejik karar: public fiyat mı kapalı mı? DS hibrit yaklaşımı **dönüşüm avantajı** sağlıyor olabilir (anonim ziyaretçi fiyat görür → ön karar → kayıt → özel teklif).

### 3.3 Görsel

Erişilemedi — görsel kalitesi, alt-text, OpenGraph/Twitter card görsel detayları doğrulanamadı.

**Raven kazanım:** Twitter card `summary_large_image` → sosyal paylaşımda büyük görsel preview. DS'te bu eksik olabilir (çıkarım).

### 3.4 Filtreleme / Faceted Navigation

**Erişilemedi.** Modern e-ticaret beklentisi: marka filtre, fiyat aralığı, stok durumu, kullanım alanı.

### 3.5 Stok Durumu

SERP'te "stoktan hızlı teslimat" sürekli vurgulu → **stok-bazlı kompozisyon var** ama stok adedi public mi belirsiz.

### 3.6 Markalar

Görülen marka portföyü (premium → ekonomik):
- **Premium:** Dentsply Sirona, 3M (Filtek), Voco, GC, Woodpecker (DTE), Ultradent, W&H, Anios
- **Yerel/orta segment:** Imicryl, Dentac, Kalsin, Yeson, Sanus, DS (private label/kendi markası — örn. "DS Otomatik Dental El Aleti Yağlama Makinesi", "DS Sterilizasyon Rulosu")
- **Cerrahi pens:** MEDIS Dental

**Stratejik gözlem:** DS, **kendi markası ("DS")** ile sterilizasyon ruloları, yağlama makinaları gibi yüksek margin'li sarf üretiyor. **Private label stratejisi** → fiyat avantajı + marka bağımlılığı.

**Raven kıyas:** Raven'in "Raven Cerrahi Aletler" private label'ı zaten var ve sayfa adlandırma açısından **paralel strateji**. Burada eşitiz. Raven'in cerrahi alanda DS'ten daha derin private label catalog'u olabilir (Raven'in core'u cerrahi aletler).

---

## 4. UX & Güven Sinyalleri

### 4.1 Adres / Konum

✓ Açık fiziksel adres: **Fidanlık Mah. Adakale Sok. No:16/B Kızılay, Çankaya / Ankara**  
✓ Ankara merkezli (Türkiye logistik kalbi değil ama doğu-iç anadolu için avantaj)

**Raven kıyas:** Molla Gürani Mah. Dedepaşa Sok. No:17A Fatih/İstanbul — **İstanbul lokasyonu lojistik avantaj** (Türkiye'nin %30+ trafiği, ihracat yakınlık).

### 4.2 Telefon / WhatsApp

✓ Sabit hat görünmüyor; cep telefonları kullanılıyor: **0507 467 29 06**, **0505 832 32 33**  
✓ WhatsApp linki — bağlantı düğmesi büyük olasılıkla aktif (CTA olarak iletişim sayfası ve site genelinde, çıkarım).  
✓ Email: `info@dentalservisim.com` (servis odaklı)

**Çıkarım:** **Cep telefonu numarası kullanımı**, B2B müşteri için **hızlı erişim** avantajı ama büyük kurumsallık algısı zayıflatabilir.

**Raven kıyas:** Tel: 0552 853 03 99, 0539 351 35 10 — paralel cep telefonu yaklaşımı. **Burada eşitiz** ama Raven'e kurumsal sabit hat eklenebilir.

### 4.3 Çalışma Saatleri

✓ "Hafta içi ve Cumartesi: 09:00 - 18:00" → açık, B2B alıcı için **güven sinyali**.

### 4.4 Sosyal Medya

- **Facebook:** `facebook.com/dsdisdeposu/` — aktif (sayfa var, beğeni sayısı doğrulanamadı).
- **Instagram:** Doğrulanamadı (SERP'te bulunamadı — büyük olasılıkla YOK veya zayıf).
- **LinkedIn, YouTube, X/Twitter, TikTok:** Doğrulanamadı.

**Raven Fırsat:** Instagram/TikTok diş hekimi topluluğunda yüksek etkileşim — DS bu kanalları kullanmıyorsa Raven için **iyi bir farklılaşma** alanı.

### 4.5 Sertifikalar & Yasal

- **Tıbbi Cihaz Satış Ruhsatı**: Açıkça belirtiliyor (hakkımızda sayfası).
- **DİŞSİAD üyeliği:** Hem "DS TIBBİ CİHAZLAR" hem "DİŞMAL DİŞ DEPOSU" olarak DİŞSİAD üye listesinde — **sektör birliği güveni**.
- **Kuruluş yılı:** 2015 (10 yıl tecrübe, B2B için yeterli).

**Raven kıyas:** DİŞSİAD üyeliği, tıbbi cihaz satış ruhsatı vurgulanıyor mu? **Onaylanmalı** — eğer eksikse acil eklenmeli.

### 4.6 Hakkımızda Sayfası

URL: `/hakkimizda`  
İçerik: Kuruluş yılı, lisans bilgisi, deneyimli personel vurgusu, 5000+ müşteri claim'i.

**Çıkarım:** Hakkımızda **standart kalite**, ekstra zenginleştirme (ekip foto, yıllık ciro, sertifika görselleri) doğrulanamadı.

### 4.7 Ödeme Yöntemleri & Trust Badges

**Erişilemedi.** Snippet'lerde havale/EFT/3D Secure kart/kapıda ödeme detayları görünmedi. Banner sertifika logoları (TSE, ISO, SSL EV) görünmüyor.

### 4.8 KVKK / Çerez

Snippet'te "Kişisel bilgiler diğer şirketlerle paylaşılmaz" gibi temel ifade var; KVKK aydınlatma metni ve çerez popup'ı **doğrulanamadı**.

### 4.9 Live Chat / Help

WhatsApp dışında live chat doğrulanamadı.

---

## 5. Kategori Yapısı Haritası (gözlemden derlenmiş)

DS'in keşfedilen kategori (`-pmk`) URL'leri tasnif edilmiş hali:

### A. Tedavi / Restorasyon
- `kompozit-pmk155`
- `kanal-dolgu-maddeleri-pmk17`
- `kanal-tedavisi-endodonti-pmk12`
- `cerrahi-urunler-pmk74`
- `dis-beyazlatma-urunleri-pmk66`
- `protetik-pmk156`

### B. Klinik Cihazları
- `klinik-cihazlar-pmk54`
- `sterilizasyon-cihazlari-pmk10`
- `kompresorler-pmk97`
- `anestezi-cihazlari-pmk196`
- `firinlar-pmk257`
- `dental-medikal-sehpalar-pmk158`
- `dental-filmleri-pmk134` (Dental Görüntüleme)

### C. El Aletleri
- `dinamik-el-aletleri-pmk36` (91 ürün)
- `pensler-pmk111`
- `karistirma-spatulasi-pmk106`
- `separeler-pmk84`
- `mikromotor-basliklar-pmk45`
- `fulvarlar-pmk108`

### D. Sarf & Yardımcı
- `koruyucu-urunler-pmk90` (8 ürün)
- `alcilar-pmk79`
- `suturlar-pmk164`
- `besleme-materyalleri-pmk83` (9 ürün)
- `yedek-parca-pmk81`
- `ogrenci-malzemeleri-pmk95` (72 ürün)

### E. Promosyon / Yeni
- `/kampanyali-urunler`
- `/yeni-urunler`

**Toplam tespit edilen `-pmk` kategori sayısı: ~25** (görünür); gerçek toplam **muhtemelen 40-60 arası** (çıkarım — pmk257 ID görüldü ama tüm ID'ler atanmamış olabilir).

**Raven kıyas:** Raven'in 18 ana kategorisi var; DS'in **2-3x kategori derinliği** var (çıkarım, kritik fırsat boşluğu).

**Raven kategoriler (search'ten):** EL ALETLERİ, ELEKTRONİK, SARF, RAVEN CERRAHİ ALETLER, diagnostics, endodontics, extraction, implantology, orthodontics, periodontics, preservation, processing, prosthetics, surgery + alt kategoriler (aerator, handpiece, micro motor, polishing).

→ Raven'in **periodontology + implantology + orthodontics** ana kategorileri var; DS'te bu kategoriler **tag düzeyinde dağılmış** olabilir. Raven'in kategori taksonomisi daha **uluslararası standartlara uygun**.

---

## 6. Anahtar Kelime Kapsamı & Örtüşme

### 6.1 DS'in Yakaladığı TR Anahtar Kelime Alanları

**Birincil (yüksek hacim):**
- `diş malzemeleri`, `diş deposu`, `dental malzeme`, `diş hekimi malzemeleri`
- `kompozit`, `cam iyonomer`, `kanal dolgu`, `endodonti`
- `dental ünit`, `dental kompresör`, `otoklav`, `sterilizasyon`
- `el aleti`, `pens`, `forseps`, `cerrahi alet`

**İkincil (uzun kuyruk via `-pmt` tag sayfaları):**
- `dental el aleti`, `dental ünit fiyat`, `dental reflektör`
- `kök kanal dezenfektanı`, `cerrahi suture`, `cerrahi aspiratör ucu`
- Marka+ürün: `gc dental kompozit`, `escom dental kompozit`, `vita dental`, `extracem`

### 6.2 Raven'in Yakaladığı Alan (search'ten gözlem)

- "Raven Cerrahi Aletler" → **brandable** kendi markamız.
- EL ALETLERİ (cerrahi pens, forseps, kürretler, elevator vb.) — daha **cerrahi-spesifik**.
- Implantology, orthodontics, periodontics — **uluslararası standart taksonomi**.

### 6.3 Anahtar Kelime Örtüşmesi (örtüşme = ortak rekabet alanı)

| Keyword Cluster | DS Güçlü | Raven Güçlü | Örtüşme |
|---|---|---|---|
| Cerrahi el aletleri (pens, forseps, kürret) | Orta | **Yüksek** (Raven Cerrahi private label) | Yüksek |
| Kompozit / restoratif | **Yüksek** | Orta | Orta |
| Endodonti (kanal dolgu, eğeleri) | **Yüksek** | Orta | Orta |
| Dental ünit / kompresör (büyük cihaz) | **Yüksek** | Düşük | Düşük (Raven core'u değil) |
| Sterilizasyon (otoklav, rulo, paketleme) | **Yüksek** (DS private label) | Düşük (varsa) | Düşük |
| Mikromotor / aeratör / başlık (handpiece) | Orta | **Yüksek** | Yüksek |
| Implantoloji | Düşük (tag-level) | **Yüksek** (kategori-level) | Orta — **Raven fırsat** |
| Ortodonti | Düşük | **Yüksek** (kategori-level) | Orta — **Raven fırsat** |
| Öğrenci paketi | **Yüksek** (72 ürün, ayrı kategori) | Düşük | Düşük — **DS fırsat** |
| Yedek parça / servis | **Yüksek** (kategori + Dental Servisim alt-marka) | Düşük | Düşük |

---

## 7. Bizim Açımızdan Fırsat Boşlukları

### 7.1 ÜSTÜN OLDUĞUMUZ NOKTALAR (Raven > DS)

1. **Schema.org Derinliği (KRITIK):** Raven'de 4 blok yapısal veri (Product + Breadcrumb + Organization + WebSite + contactPoint) — DS'te SERP zenginleştirmesi gözlenmedi.
2. **hreflang + İhracat Hazırlığı:** Raven `tr` + `x-default` ile uluslararası satışa hazır; DS yalnızca TR.
3. **Modern Mobil/Görsel Üstünlük:** `viewport-fit=cover`, `theme-color`, Twitter card `summary_large_image` — DS bu detayları muhtemelen kullanmıyor.
4. **Güvenlik Header'ları:** HTTPS + HSTS + Secure cookies → DS'te aynı seviyenin olduğu doğrulanmadı (yüksek olasılıkla daha zayıf).
5. **Uluslararası Standart Taksonomi:** Raven'in endodontics/orthodontics/periodontics/implantology kategori isimleri — global SEO ve ihracat için çok daha uygun.
6. **Private Label Cerrahi:** "Raven Cerrahi Aletler" → cerrahi alanda DS'in MEDIS Dental gibi 3rd party'ye bağlı olmasından farklı, **kendi markamız**.
7. **İstanbul Lojistiği:** Fatih/İstanbul lokasyonu → ihracat (havalimanı, deniz limanı yakınlığı) ve TR trafiğinin %30+'ı için lojistik avantaj.
8. **Daha Temiz Index:** Raven'in 738 SEO URL'i odaklı; DS'in `-pmt` tag sayfaları (10000+) potansiyel index bloat oluşturuyor.

### 7.2 KAÇIRDIĞIMIZ FIRSATLAR (DS > Raven, kritik kapatmalar)

1. **KRİTİK — BLOG / İÇERİK AĞI YOK:** DS'in `dismalzemeleri.net` ayrı domain blog'u + ana sitedeki `/blog/...-pmb{n}` slot'u → **topical authority + iç bağlantı + uzun kuyruk yakalama**. Raven'in blog'u yok veya minimal → **acil eylem: 50+ makale, "Cerrahi pens nasıl seçilir", "Kürret çeşitleri", "Implant alet seti hazırlama" gibi B2B hekim sorularına yanıt**.
2. **KRİTİK — ÜRÜN HACMİ ~10x ALTINDA:** DS ~3000-5000 aktif ürün; Raven ~300-500. **Sarf/consumable kategorisi (otoklav rulosu, eldiven, maske, sutur, alçı, beyazlatma) ile büyüme**: bu hızlı tekrar siparişli, yüksek sepet sıklığı sağlar.
3. **KRİTİK — AFTER-SALES SERVİS YOK:** DS'in "Dental Servisim" alt markası (ünit/kompresör/röntgen tamiri) → **yapışkan müşteri ilişkisi + ekstra gelir kanalı**. Raven, bu hizmeti İstanbul'da sunabilir mi? Stratejik karar.
4. **Tag / Etiket Sistemi Yok (varsayım):** DS'in `-pmt` tag sayfaları her ne kadar bloat riski taşısa da, **doğru implementasyonla** ("vita dental", "gc dental kompozit" gibi marka-keyword kombinasyonu) uzun kuyruk SEO'da değer üretiyor. Raven için kontrollü tag taksonomisi kurulabilir.
5. **Öğrenci Paketi Kategorisi:** DS'in 72 ürünlük "Öğrenci Malzemeleri" segmenti → diş hekimliği fakülteleri **yıllık tekrar müşteri**. Raven'in toptan B2B kategorisinde bu eksik.
6. **Hibrit Fiyatlama UX:** DS public fiyat + "özel teklif" CTA → anonim ziyaretçi dönüşümü daha güçlü olabilir. Raven'in B2B-only login akışı uzun vadede iyi ama keşif aşamasında kullanıcı kaybediyor olabilir.
7. **"Modelleri ve Fiyatları" Kategori Title Pattern:** TR aramalarında **çok yüksek CTR**. Raven'in kategori title pattern'i kontrol edilmeli.
8. **WhatsApp Aktif Entegrasyon:** Floating CTA olarak WhatsApp düğmesi — Türkiye B2B'sinde standart. Raven'de mevcut mu? Doğrulanmalı.

### 7.3 ÖRTÜŞTÜĞÜMÜZ ALANLAR (rekabet doğrudan)

- **Kompozit, kanal dolgu, anestezi** kategorileri: DS'te ürün hacmi çok yüksek; Raven burada lokal/lider markaları detay-zengin sayfalarla yenebilir.
- **El aletleri (pens, forseps)**: Raven'in private label avantajı + DS'in geniş 3rd party katalog → ürün başına derinleştirilmiş schema + review eklenirse Raven kazanır.
- **Mikromotor / Handpiece / Aeratör**: İkisi de bu pazarda; teknik içerik kalitesi (uzun rehber + video) belirleyici.

---

## 8. Quick Wins (90 gün hedefli)

| # | Aksiyon | Süre | Etki | Sorumluluk |
|---|---|---|---|---|
| 1 | Blog modülü açma + 10 ilk makale (cerrahi alet rehberi, sterilizasyon protokolü, implant set hazırlama) | 4 hafta | Yüksek | İçerik |
| 2 | `/kategori/{slug}` title pattern'i `"... Modelleri ve Fiyatları - Raven Dental"` formatına revize | 1 hafta | Orta | SEO |
| 3 | 18 kategori başlığına marketing tag-line ekleme (DS'in "Güvenli ve Etkili Temizlik" benzeri) | 2 hafta | Orta | İçerik + SEO |
| 4 | WhatsApp floating CTA + iletişim hattı sayfa-üstü banner | 3 gün | Orta | Frontend |
| 5 | "Öğrenci paketi" ayrı kategori + üniversite outreach | 6 hafta | Yüksek | Satış + ürün |
| 6 | Ürün sayfasında `AggregateRating` schema + doğrulanmış-hekim review formu | 3 hafta | Yüksek (SERP yıldız) | Backend |
| 7 | DİŞSİAD üyeliği + tıbbi cihaz satış ruhsatı footer'da ve hakkımızda'da görsel olarak | 1 hafta | Orta (güven) | İçerik |
| 8 | Sarf kategorisi genişletme (otoklav rulosu, eldiven, maske, alçı) — 200+ ürün eklenir | 8 hafta | Yüksek (ürün hacmi) | Satınalma + katalog |
| 9 | After-sales servis sayfası (ünit/kompresör tamir) — pilot İstanbul | 6 hafta | Orta-Yüksek | Operasyon |
| 10 | Instagram/TikTok ürün-tanıtım kanalı (haftada 3 reels) | Sürekli | Yüksek (genç hekim) | Pazarlama |

---

## 9. Orta Vadeli Stratejik Önerilerden Seçilmiş (6-12 ay)

1. **İkincil blog domain'i** (`ravendentaldergi.com` gibi) → DS'in `dismalzemeleri.net` stratejisinin replikası ile **link silosu + topical authority**.
2. **Private label genişlemesi:** "Raven Cerrahi Aletler" markasını sterilizasyon sarfı ve aksesuara taşıma (DS'in "DS Sterilizasyon Rulosu" stratejisi).
3. **İhracat e-ticaret modülü:** EN dil + USD/EUR + uluslararası kargo. Raven hreflang hazırlığı zaten var; DS bu alanda **YOK**.
4. **B2B hesap yöneticisi atama yazılımı:** Her büyük hekim/klinik için kişisel temsilci atama (DS'in "size özel teklif" söyleminin yapısallaştırılması).
5. **Eğitim webinar serisi:** Cerrahi alet bakımı, sterilizasyon, implant set hazırlığı — leadgen + brand authority.

---

## 10. Risk ve Tehditler

- **DS'in 10 yıllık marka tanınırlığı** (5000+ müşteri claim'i) — yeni müşteri kazanımında engel.
- **Ankara lokal pazarda DS dominant** — Raven İstanbul ağırlıklı kalmalı veya Ankara'ya satış temsilcisi.
- **`-pmt` tag silosu DS için risk** ama uygun yapıldığında güçlü silah — Raven kontrollü taksonomi ile aynı silahı eline alabilir.
- **Fiyat baskısı:** DS'in private label sarf'ı düşük fiyatla rakip — Raven private label yatırımı şart.

---

## 11. Doğrulanması Gereken Hipotezler (Sonraki Tur)

| # | Hipotez | Doğrulama Yöntemi |
|---|---|---|
| A | DS'in `-pmt` tag sayfaları `noindex` mi? Index bloat var mı? | `curl https://www.dismalzemeleri.com/robots.txt` + `site:dismalzemeleri.com inurl:pmt` Google sayım |
| B | DS hangi CMS/platformda? (Ticimax/Tsoft/İdeasoft/custom) | `curl -I` ile `Server:` ve `X-Powered-By:` header'ı + HTML kaynak kod incelemesi (`/admin` veya CSS path) |
| C | DS'in HSTS, CSP, secure cookie durumu | `curl -I https://www.dismalzemeleri.com/` ile response header'lar |
| D | DS'in JSON-LD blokları var mı, hangi tipler? | Ana sayfa + kategori + ürün için `view-source:` ve Schema.org Validator |
| E | DS'in toplam ürün adedi (gerçek) | `sitemap.xml` indirip URL sayımı |
| F | DS Core Web Vitals (LCP/INP/CLS) | PageSpeed Insights API |
| G | DS GA4/GTM tracking durumu | View-source: `gtag(` / `GTM-` arama |
| H | DS canonical, hreflang implementasyonu | HTML `<head>` denetimi |
| I | Raven'in mevcut blog/içerik durumu net haritası | Raven `/blog/` ve sitemap denetimi (bizim taraf) |
| J | DS Instagram/LinkedIn/YouTube hesap varlığı | Manuel sosyal medya arama |

---

## Kaynaklar

- [DS Diş Deposu Anasayfa](https://www.dismalzemeleri.com/)
- [DS Diş Deposu Hakkımızda](https://www.dismalzemeleri.com/hakkimizda)
- [DS Diş Deposu İletişim](https://www.dismalzemeleri.com/iletisim)
- [DS Diş Deposu Yeni Ürünler](https://www.dismalzemeleri.com/yeni-urunler)
- [DS Diş Deposu Kampanyalı Ürünler](https://www.dismalzemeleri.com/kampanyali-urunler)
- [DS Diş Deposu Kompozit Kategorisi](https://www.dismalzemeleri.com/kompozit-pmk155)
- [DS Diş Deposu Sterilizasyon Cihazları](https://www.dismalzemeleri.com/sterilizasyon-cihazlari-pmk10)
- [DS Diş Deposu Kanal Tedavisi / Endodonti](https://www.dismalzemeleri.com/kanal-tedavisi-endodonti-pmk12)
- [DS Diş Deposu Anestezi Cihazları](https://www.dismalzemeleri.com/anestezi-cihazlari-pmk196)
- [DS Diş Deposu Kompresörler](https://www.dismalzemeleri.com/kompresorler-pmk97)
- [DS Diş Deposu Dinamik El Aletleri](https://www.dismalzemeleri.com/dinamik-el-aletleri-pmk36)
- [DS Diş Deposu Pensler](https://www.dismalzemeleri.com/pensler-pmk111)
- [DS Diş Deposu Cerrahi Ürünler](https://www.dismalzemeleri.com/cerrahi-urunler-pmk74)
- [DS Diş Deposu Protetik](https://www.dismalzemeleri.com/protetik-pmk156)
- [DS Diş Deposu Süturlar](https://www.dismalzemeleri.com/suturlar-pmk164)
- [DS Diş Deposu Öğrenci Malzemeleri](https://www.dismalzemeleri.com/ogrenci-malzemeleri-pmk95)
- [DS Diş Deposu Yedek Parça](https://www.dismalzemeleri.com/yedek-parca-pmk81)
- [DS Diş Deposu Blog — Aeratör nedir](https://www.dismalzemeleri.com/blog/ds-dis-deposu-aerator-nedir-pmb6)
- [DS Diş Deposu Facebook](https://www.facebook.com/dsdisdeposu/)
- [DisMalzemeleri.net (yan blog domain)](https://www.dismalzemeleri.net/)
- [DİŞSİAD — DS Tıbbi Cihazlar üye sayfası](https://www.dissiad.org.tr/uye/ds-tibbi-cihazlar)
- [DİŞSİAD — DİŞMAL Diş Deposu üye sayfası](https://dissiad.org.tr/uye/dismal-dis-deposu)
- [Klazify — dismalzemeleri.com profile](https://www.klazify.com/website/dismalzemeleri.com)
- [Raven Dental Anasayfa (referans)](https://ravendentalgroup.com/)
- [Raven Dental İletişim (referans)](https://ravendentalgroup.com/index.php?route=information/contact)
