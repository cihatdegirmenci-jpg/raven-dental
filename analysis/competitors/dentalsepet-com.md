# Rakip Analizi: dentalsepet.com

**Analiz tarihi:** 2026-05-12
**Analist:** Product Trend Researcher Agent
**Bizim site:** ravendentalgroup.com (Raven Dental — B2B dis aletleri, OpenCart 3 + Journal3)
**Rakip site:** https://www.dentalsepet.com
**Veri yontemi:** WebSearch (Google index uzerinden) — *WebFetch izni reddedildigi icin dogrudan HTML/header/curl yapilamadi. Asagida "erisilemedi" olarak isaretlenen alanlar dogrudan HTTP cevabi gerektirir.*

---

## 0. Yonetici Ozeti

Dentalsepet.com, 2006/2009'dan beri faaliyette olan, Eskisehir merkezli koklu bir B2B dis depo e-ticaret oyuncusudur. **Buyuk envanter** (9.300+ indirimli urun, 2.134 el aleti, 30+ ust kategori) ve **markalasmis tedarikci pozisyonu** (Tibbi Cihaz Satis Ruhsatli, DISSIAD uyesi, 14+ yil) en guclu yanlaridir. Buna karsin **fiyat-login duvari**, **uretici/ozel marka eksikligi** (yalnizca distribütör/reseller), **goreceli olarak zayif sosyal medya etkilesimi** (Instagram 4.7K, Facebook 232 like), **icerik pazarlamasi zayifligi** (blog yok / nadir, video kanali aktif degil) ve **musteri sikayetleri** (KVKK ihlali iddiasi, siparis reddi, urun kalitesi) belirgin zaafiyetlerdir. Raven Dental, **kendi markali cerrahi alet uretimi**, **schema.org zenginligi**, **hreflang TR + x-default cok-pazar hazirlici**, ve **B2B + B2C esnek modeli** ile dentalsepet'in pure-distribütör modeli karsisinda nis ama derin bir konumlanma yapabilir.

---

## 1. Sirket ve Marka Profili

| Alan | Veri | Kaynak |
|---|---|---|
| Yasal Unvan | Dentalsepet Dis Malzemeleri Medikal Ith. Ihr. San. Tic. Ltd. Sti. | DISSIAD uye sayfasi |
| Kurulus | 2006 (kurulus), 2009 (online faaliyet) | Hakkimizda, LinkedIn |
| Sektorel pozisyon | "Turkiye'nin en koklu / en kapsamli online dis deposu" iddiasi | dentalsepet.com basligi |
| Merkez | Hosnudiye Mah. Bayrak Sk. No:12, Dentalsepet Plaza, 26130 Tepebasi / Eskisehir | Iletisim sayfasi |
| Ticaret Sicil No | 25002 | Hakkimizda |
| Ruhsat | Tibbi Cihazlar Satis Ruhsatli | Hakkimizda |
| Telefon | 0554 363 87 21 / (0222) 230 84 ... | Iletisim / 3. parti rehber |
| E-posta | info@dentalsepet.com | Iletisim |
| Birlik | DISSIAD uyesi | dissiad.org.tr |
| Hukuk durumu | E-ticaret.gov.tr kayitli (siteprofil mevcut) | eticaret.gov.tr |

**Raven Dental ile karsilastirma:** Raven Dental kendi imalat + ic-dis ticaret modelinde (tendata kayitlarinda Cin ve Pakistan'a ihracat goruluyor), oysa dentalsepet pure-distribütor. **Raven'in "uretici markasi" konumlanmasi, dentalsepet'in "supermarket" konumlanmasindan farkli ve premium-friendly bir konumlanma firsati saglar.**

---

## 2. Teknik SEO Analizi

### 2.1 URL Yapisi

Dentalsepet kategori URL'leri ortak bir desen tasiyor:

```
/cerrahi-el-aletleri-pmk58
/el-aletleri-pmk131
/ortodontik-el-aletleri-pmk804
/kompozit-el-aletleri-pmk763
/klinik-cihazlar-pmk245
/laboratuar-cihazlari-pmk282
/implant-cerrahi-aletleri-pmk769
/diger-kanal-aletleri-pmk202
/ogrenci-malzemeleri-pmk745
/basliklar-pmk22
/klinik-setler-baslik-pmk38
/dezenfeksiyon-sterilizasyon-pmk80
/belirsiz-kategori-pmk40       <-- (!) sızıntı, anomali
/separeler-pmk305
```

Statik sayfalar `-pml4` / `-pmm23` / `-pmm11` / `-pmm2` desenleri:

```
/uyelik-hakkinda-pml4
/fatura-ve-teslim-bilgileri-pmm23
/online-destek-pmm11
/hangi-kargo-sirketlerini-kullanabilirim--pmm2
```

Urun URL desenleri:

```
/meisinger-cerrahi-kesim-frezi-pmu14760
```

Sayfalama: `?syf=3` (ornek: `cerrahi-el-aletleri-pmk58?syf=3`)

**Gozlemler:**
- `pmk` (kategori), `pmu` (urun), `pml` (legal/policy), `pmm` (musteri hizmetleri) ayrik prefix'ler — temiz mantik.
- `belirsiz-kategori-pmk40` *halen Google'da indekslenmis* — bu kategori adi reddedilmesi gereken bir prod/staging sizmasidir. SEO sinyali olarak zayiftir, ayrica brand-trust kirar.
- Sayfalama parametresi `?syf=` semantik degil; `canonical` veya `rel="next/prev"` davranisi *erisilemedi*.
- Cift tire `kullanabilirim--pmm2` URL'sinde sintaks hatasi var (mukerrer "-").

**Raven Dental avantaji:** 738 SEO URL'in tamami insan-okur (`/cerrahi-cimbiz-sk-1234`) ve Journal3 tarafindan slug-temizleme yapildigi icin `pmk/pmu` numerik kuyruklarsiz. **Bu, ham keyword-density acisindan Raven lehine net bir avantajdir.**

### 2.2 Title / Meta / H1

Google SERP'de gozlenen title patternleri:

| URL | SERP title |
|---|---|
| / | "Dentalsepet.com - Turkiyenin Dis Deposu" |
| /el-aletleri-pmk131 | "Dis Hekimligi El Aletleri - Dentalsepet.com" |
| /cerrahi-el-aletleri-pmk58 | "Cerrahi El Aletleri Cesitleri ve Fiyatlari - Dentalsepet.com" |
| /ortodontik-el-aletleri-pmk804 | "Ortodontik El Aletleri Cesitleri ve Fiyatlari - Dentalsepet.com" |
| /klinik-cihazlar-pmk245 | "Dis Hekimligi Klinik Cihazlari > Dentalsepet.com" |
| /hakkimizda | "Hakkimizda « Dentalsepet.com - Turkiyenin Dis Deposu" |

**Pattern:** `{Kategori} Cesitleri ve Fiyatlari - Dentalsepet.com`. **Iyi yon:** "Cesitleri ve Fiyatlari" long-tail catchall'i ile ek arama hacmi yakaliyor (Turkce e-ticarete tipik). **Kotu yon:** ayrac tutarsizligi (`«`, `-`, `>` farkli sayfalarda farkli). Marka odaginda — kategori adi her zaman onde.

Meta description ve H1 dogrudan dogrulanamadi (WebFetch reddedildi). **erisilemedi.**

**Raven Dental avantaji:** Tek tutarli ayrac (Journal3 SEO template'inde), schema.org WebSite NameSearchAction blogu ile zenginlestirilmis. Dentalsepet'te bu tutarsizlik kucuk ama gozlemlenebilir bir signal-of-quality kaybi.

### 2.3 Schema.org / Structured Data

Erisilemedi (dogrudan HTML inceleme yapilmadi). Google SERP'de zengin sonuc (rich result) emaresi *gorulmedi*: "Cesitleri ve Fiyatlari" tipi siyah-beyaz mavi-link sonucu. Bu, kategori sayfalarinda `ItemList` veya `Product+AggregateOffer` schema'sinin **muhtemelen eksik veya yarim** oldugunu duşundurur. Urun sayfalarinda fiyat yildizli yorum yok (login-gated cunku) — bu, `Product+Offer+AggregateRating` schema'sinin tetiklenmedigine isaret eder.

**Raven Dental avantaji:** Product+Breadcrumb+Organization+contactPoint+WebSite (4 blok) tam stack mevcut. Bu, dentalsepet'e gore *net teknik SEO ustunlugumuzdur*.

### 2.4 Sitemap / Robots / Indekslenme

Dogrudan erisilemedi. **Endirekt sinyaller (Google'da gorunen URL'ler):**

- Kategori sayfalari (pmk) — index'te
- Urun sayfalari (pmu) — index'te (Meisinger frez ornegi)
- Statik (pml/pmm) — index'te
- "belirsiz-kategori-pmk40" — istenmeyen index'te kalmis (noindex yok)
- `?syf=3` paginate sayfalari — index'te (canonical yetersizligi sinyali)

**Tahmini sitemap durumu:** OpenCart-benzeri/eski PHP custom yapida olabilir; multi-sitemap (kategori, urun, statik ayrik) hiyerarşi yok gibi gorunuyor. *Dogrulamak icin: `/sitemap.xml`, `/robots.txt` curl gerekir — erisilemedi.*

**Raven Dental avantaji:** Yapilandirilmis sitemap hiyerarşisi (kategori/urun/blog/static) zaten mevcut. Dentalsepet bu konuda hizli kazanim alabilirdi, almamis.

### 2.5 Security Headers / HTTPS

Erisilemedi. **Sinyaller:**
- Site `https://www.dentalsepet.com` uzerinden servis ediliyor (TLS evet).
- HSTS / X-Frame-Options / CSP / Referrer-Policy / Secure cookies dogrulanamadi.
- Mobil uygulamasi var (Google Play + iOS) — token yonetiminde API endpoint'i muhtemelen mevcut, ancak bunlar incelenmedi.

**Raven Dental avantaji:** HSTS + Secure cookies + theme-color + viewport-fit zaten konfigure. Dentalsepet'in bunlari uygulayip uygulamadigi *erisilemedi*, ancak SERP-only sinyaller (markup eksigi) altyapi modernligi konusunda soru isareti uyandiriyor.

### 2.6 Mobile / Performance

- **Native mobil uygulama:** Var (Android: `com.dentalsepet.dentalsepetmobileappp`, iOS: appbrain "Dental Sepet +"). Bu, dentalsepet'in **acik bir UX ustunlugudur**. Tekrarlayan musteri (her klinik haftalik sarf siparisi) icin uygulama-ici hiz, kayitli sepet, push bildirim onemli.
- **Web mobile responsivlik:** Dogrulanamadi ama 14+ yillik altyapi varsayimiyla muhtemelen mobile-friendly ama "mobile-first" degil.
- **PageSpeed / LCP / CLS / INP:** *erisilemedi* (Google PSI calistirilamadi).

**Raven Dental aciği:** Native mobil uygulama yok. **Bu, dentalsepet'in en somut UX silahidir.**

---

## 3. Icerik Stratejisi

### 3.1 Ana Sayfa

Erisilemedi dogrudan, ancak SERP basligi ve Google snippet'larindan rekonstruksiyon:

- Slogan: "Turkiyenin Dis Deposu"
- Hero muhtemelen kampanyali urun karuseli (hero-as-promo, hero-as-storytelling degil)
- "Indirimli urunler" ve "Kampanyali urunler" ozel landing'leri var (`/indirimli-urunler`, `/kampanyali-urunler`) — fiyat-promosyon odakli e-ticaret kalibi
- Haftalik one cikan urunler bolumu var (search snippet)
- Bulten abonelik (e-posta haber grubu) cagrisi var
- Marka hikayesi anasayfada ust katmanda degil (hakkimizda alt link)

### 3.2 Kategori Aciklamalari

SERP snippet'lari kategori sayfalarinda urun listelemenin ustunde ya cok kisa ya cok jenerik metin oldugunu gosteriyor (ornek: "Cerrahi El Aletleri 20 urun"). **1000+ karakter editoryal aciklama emaresi yok.**

**Raven Dental avantaji:** 18 kategorinin her birinde 1450 char editoryal aciklama, semantik keyword dagilimi ile. **Bu, dogrudan organik trafik avantajidir** — Google "Cerrahi El Aletleri nedir / nasil temizlenir / hangi durumda kullanilir" tarzi long-tail sorgularda Raven'i one cikartabilir.

### 3.3 Urun Aciklamalari

Ornek urun: `meisinger-cerrahi-kesim-frezi-pmu14760` — tek satır, marka + jenerik aciklama. Editoryal derinlik (kullanim alani, materyal, sterilizasyon protokol, kalibrasyon, garanti, video) goruntulenemedi ama SERP snippet'i bu yonde zayif.

### 3.4 Blog / Haber

**Yok ya da kayda deger ozellikte degil.** Arama `dentalsepet.com blog haberler` aramasinda site-ici blog URL'i index'lenmiyor. Bulten cikariyorlar ama e-posta-only formatta — SEO degeri sifir.

**Raven Dental firsati:** Blog acilimi (saglik personeli odakli "Aletler nasil sterilize edilir, hangi cerrahi alet hangi prosedurde, ISO/EN normlari, kalibrasyon SSS") **dentalsepet'in 14+ yillik domain authority'sine ragmen kapatamayacagi bir bilgi-arz bosluguna saldirma firsati**.

### 3.5 Video

YouTube kanali var (`UCrWGvrEhvHS0HwlBiAhSybg`) ve Dailymotion `dentalsepet` profili gorulur. Aktiflik ve abone sayisi dogrulanamadi; ancak SERP'de video rich result'lari listelenmedi → kanal aktif degil veya minimal icerikli.

### 3.6 Yorumlar / Reviews

- Site-ici yorum ozelligi: SERP'de "uyeler urun yorumu yazabilir" beyani var ama urun sayfasi snippet'larinda yildiz/rating zengin sonucu **goruntulenmedi** → ya aktif degil ya schema'siz.
- Site-disi yorum (sikayetvar): KVKK ihlali iddiasi, siparis reddi (Profilaksi orneği), urun kalitesi (dis fircasi).
- Eksisozluk: bagimsiz tartisma var.

**Raven Dental avantaji:** Yorum schema'si Product schema'ya bagli ise (Journal3 destekler) AggregateRating SERP'de gosterilebilir. **Yorumlar konusunda hizli ustunluk alinabilir.**

---

## 4. Urun Gorunurlugu

### 4.1 Toplam Urun ve Kategori

| Metrik | Deger |
|---|---|
| El aletleri ust kategorisi | 2.134 urun |
| Cerrahi el aletleri | 20 urun |
| Ortodontik el aletleri | 75 urun |
| Kompozit el aletleri | 73 urun |
| Indirimli urunler (toplam) | 9.322 |
| Kampanyali urunler | 498 |
| Ust kategori sayisi | ~30+ (Akriller, Anestezi, Basliklar, Beyazlatma, Cerrahi, Cocuk/Profilaksi, Detertraj-Profilaksi, Dezenfeksiyon-Sterilizasyon, Dis Uniti, Disler, Dolgular, El Aletleri, Eldivenler, Frezler, Hassasiyet Gidericiler, Hasta Bilgilendirme, Hekim Onlukleri, Implant, Kaide/Simantasyon, Kanal Tedavisi, Kanama Durdurucular, Kitap/Dergi/CD, Koruyucu Ekipman, Klinik Aksesuar, Klinik Cihazlar, Laboratuvar, Matrix/Kama, Muayenehane Dekorasyonu, Muayenehane Temizligi, Ortodonti, Ogrenci Malzemeleri, Periodontoloji + alt kategoriler) |

**Skala karsilastirmasi:** Dentalsepet ~10K SKU range'inde, Raven Dental 738 URL ile *cok daha dar ama derin* nis takip ediyor.

### 4.2 Fiyat / Login Duvari

**Onaylandi:** Urun fiyatlari yalnizca uye girisinden sonra goruluyor (Sikayetvar yorumlarinda dogrulanmis: "fiyatlari gormek icin uye oldum, kisisel veri istendi, WhatsApp ile cocugun okulunu sordular").

**Etkileri:**
- Pro: B2B sade fiyatlandirma, dis-hekimi-disi musterilere fiyat sizdirma yok, kategori-bazli ozel fiyat segmentasyonu mumkun.
- Con: Google'in `Product+Offer` schema'sini tetikleyemiyor — **SERP'de fiyat gosterimi yok, Merchant Center / Shopping kanali kapali** (veya feed-only). Bu, organik trafikten conversion'a giden funnel'da soguma yapar.
- Con: Sikayetvar'da KVKK ihlali iddiasi (uyelik formundan toplanan veriler WhatsApp tacizine donusmus). **Trust signali kaybi.**

**Raven Dental stratejisi:** B2B/B2C dual-model + opsiyonel "login icin daha iyi fiyat" hybrid. Public fiyat + login indirimi = en iyi her iki dunya.

### 4.3 Gorsel

Dogrudan inceleme yapilmadi. Pmu kodlarinin numerik olusu sugerests Image filename'ler `image/cache/...` benzeri OpenCart benzeri yapida olabilir. Urun gorsel sayisi/ZOOM/360 dogrulanmadi.

### 4.4 Filtre / Faceted Search

Erisilemedi. Kategori bazli yapida muhtemel marka filtresi (Meisinger orneklerinden) ve fiyat filtresi vardir; **gelismis attribute filtreleme** (paslanmaz celik tipi, eğri/duz, EN/ISO sertifika filtresi) emaresi gorulmedi.

### 4.5 Stok

Login arkasinda olabilir. SERP'de "stokta" gostergesi gorulmedi.

---

## 5. UX ve Trust Sinyalleri

| Trust Sinyali | Dentalsepet | Raven Dental |
|---|---|---|
| Fiziksel adres acik | Evet (Eskisehir Plaza acik adres) | Var |
| Telefon | 0554 ... + sabit hat (0222) | Var |
| WhatsApp | Var (musteri hizmetleri) | Var (dogrulanmali) |
| Tibbi Cihaz Satis Ruhsati | Vurguluyor | TODO: vurgulanmali |
| DISSIAD uyesi | Evet, badge gorulur | Evet, ravendental.dissiad.org.tr profili var |
| Eticaret.gov.tr profil | Evet (siteprofil/F1F206BA557D...) | TODO: dogrulanmali |
| Hakkimizda | Var, "2006'dan beri" hikayesi | Var |
| KVKK / Aydinlatma | URL var ama sikayetvar'da ihlal iddiasi | Schema.org Organization mevcut |
| Native mobil uygulama | Android + iOS | Yok (firsat) |
| Sosyal medya: Instagram | 4.715 takipci | TODO: dogrulanmali |
| Sosyal medya: Facebook | 232 like (zayif) | TODO |
| Sosyal medya: LinkedIn | Sirket sayfasi var | TODO |
| Sosyal medya: YouTube | Kanal var, aktiflik dusuk | TODO |
| Sikayetvar score | Negatif sinyaller var (siparis reddi, KVKK, kalite) | Bilinmiyor |
| Eksisozluk basligi | Var (5487788) | Var mi: kontrol |
| Hipokratist saglik ansiklopedi atif | Var | Yok |

### 5.1 Musteri Sikayetleri Detayli

Sikayetvar'da en kritik 3 sikayet:

1. **KVKK ihlali:** "Fiyatlari gormek icin uye oldum, kisisel veri verdim. Sirket WhatsApp'tan iletisim kurup cocugumun hangi okula gittigini sordu. KVKK uyariyla yaklasinca asagilayici dile basvurdular, sonra engellediler." Bu, **B2B'de marka-ruhuna toksik bir iddiadir**.
2. **Siparis reddi (Profilaksi):** Hekim olmayan musterilere kisitli urunler satilmiyor — *pratik olarak dogru tibbi-cihaz mevzuati* ancak iletisim yontemi UX-zayifligi yaratiyor. Banka komisyonunu iade etmediklerini iddia ediyorlar.
3. **Urun kalitesi:** 430 TL'lik dis fircasinin kullanilir kullanilmaz kirilan kil iddiasi (Mayis 2024).

**Strajik onem:** Dentalsepet'in lider pazar konumuna ragmen "premium hizmet" konumlanmasi zayif. Raven Dental icin **"sertifikali, dogrudan uretici, transparent" konumlanmasi** dogal bir farklilastirma noktasidir.

### 5.2 Sosyal Medya Sinyalleri (Etkilesim)

| Platform | Takipci/Like | Yorum |
|---|---|---|
| Instagram | 4.715 takipci, 4.356 takip, 216 post | **Takip oranlari nahos** (4.7K takipci'ye karsi 4.3K takip — "follow-back" pattern, sahte/dusuk-kalite buyume sinyali) |
| Facebook | 232 like | **Cok zayif** — pasif/eski sayfa |
| LinkedIn | Sirket sayfasi var | Aktiflik dogrulanmadi |
| YouTube | Kanal var | Aktiflik dogrulanmadi |
| Dailymotion | Profil var | Aktiflik dogrulanmadi |

**Sonuc:** Dentalsepet sosyal medyada lideri degil. **Bu, Raven Dental icin Instagram + LinkedIn + YouTube ucgeninde "uretici-perspektifi" (atolyeden uretim videolari, urun kalibrasyonu, malzeme bilimi) ile asma firsatidir.**

---

## 6. Bizim Acimizdan Firsat Bosluklari (Gap Analysis)

### 6.1 Bizim KACIRDIGIMIZ (Dentalsepet'te VAR, Raven Dental'da YOK ya da Zayif)

| Boslugumuz | Etki | Aksiyon |
|---|---|---|
| **Native mobil uygulama (iOS + Android)** | Tekrarlayan B2B musterinin sepet/list/push deneyimi | OpenCart 3 + Journal3 icin React Native veya PWA ile hafif uygulama. 6 ay roadmap. |
| **Ust kategori cesitliligi** (30+ ust kategori, 2K+ el aleti SKU) | Genis musterinin tek-noktadan alim yapamamasi | Konsumable (frez, dolgu, anestezi, eldiven) urun seritleri eklenebilir; pure cerrahi-uretici konumlanmadan vazgecmeden "Raven + secilmis tedarikciler" hybrid. |
| **Indirimli/Kampanyali ozel landing sayfalari** | Promo trafiginin yapilandirilmamis olmasi | `/indirimli-urunler` ve `/kampanyali-urunler` benzeri Raven landing sayfalari + schema.org `OfferCatalog`. |
| **Bulten abone tabani** | Email pazarlama listesi yoklugu | Newsletter opt-in CTA ana sayfaya + KVKK uyumlu cift-onay. |
| **Eskisehir lojistik merkezi vurgu** | Dentalsepet "merkezde plaza" dilini kullaniyor | Raven'in uretim/lojistik adresi acik vurgu ile ozellestirilmeli. |
| **DISSIAD + Tibbi Cihaz Ruhsati badge'leri ust seritte** | Trust badge'in gorunurlugu | Header'a "Tibbi Cihaz Satis Ruhsatli + DISSIAD Uyesi" rozetleri. |
| **Cimri / Hepsiburada / pazaryeri varligi** | 3. parti karsilastirma trafigi | Raven'in Hepsiburada Premium / Trendyol B2B / N11 magaza acmasi (eger marka tutarliligi korunabilirse). |
| **"Hipokratist" gibi saglik ansiklopedisi atifları** | 3. parti backlink + autorite | Tip portallarinda (Medikal Akademi, Acil Tip, Saglik Aktuel) icerik isbirligi. |

### 6.2 Bizim USTUN YANLARIMIZ (Raven Dental'da VAR, Dentalsepet'te YOK ya da Zayif)

| Ustunlugumuz | Dentalsepet durumu | Stratejik kullanim |
|---|---|---|
| **738 SEO URL'in insan-okur slugu** | pmk/pmu numerik kuyruklari var (`-pmk58`) | Bu zaten organik avantaj. Vurgu yapilmali (case study, vaka). |
| **18 kategori 1450 char editoryal aciklama** | Kategori sayfalarinda jenerik veya kisa metinler | Long-tail TR keyword'lerinde Raven daha cabuk siralanir. |
| **Schema.org Product + Breadcrumb + Organization + contactPoint + WebSite (4 blok)** | Schema.org zenginligi dogrulanmadi, rich result yok | Google'a "yapilandirilmis veri zenginligi" sinyali — Raven'in long-term SEO yatirimi cok ileri. |
| **hreflang TR + x-default** | Dogrulanmadi, muhtemelen yok | Cok-pazar genislemesi (EN, AR, RU) icin Raven'in altyapisi hazir. |
| **Twitter card summary_large_image** | Dogrulanmadi | Sosyal paylasimda gorsel-zengin onizleme. |
| **viewport-fit + theme-color + Sitemap hiyerarsi** | Tahmini eski altyapi | Modern UI/UX sinyalleri Google'a iyiniyet gosterir. |
| **Public fiyat (B2C+B2B dual)** | Login-gated | Raven icin dual-pricing (genel + uyeye ozel indirim) hybrid yapilabilir; Google Shopping'e public fiyat acan ilk konvansiyonel Turk B2B markasi olabilir. |
| **Uretici-marka (Raven Cerrahi Aletler)** | Dentalsepet salt distribütor | Premium pozisyon + ihracat (Cin, Pakistan zaten var) + dogrudan klinik satisi. |
| **HSTS + Secure cookies + HTTPS modern** | Dogrulanmadi | Audit/Pentest sonuclari Raven blog'da yayinlanabilir (B2B alici icin trust). |
| **OpenCart 3 + Journal3 modern stack** | Tahmini eski/custom altyapi | Raven daha hizli iterasyon yapabilir. |

### 6.3 Kelime Ortusmesi (Keyword Overlap)

Asagidaki anchor kelimeler hem dentalsepet hem raven icin **iki-yonlu rekabette** olacaktir:

| Anchor keyword | Dentalsepet SERP pozisyonu (tahmin) | Raven onerisi |
|---|---|---|
| "cerrahi el aletleri" | top-3 | Editoryal genislik + alt-keyword: "cerrahi el aletleri seti", "cerrahi el aletleri sterilizasyon", "cerrahi el aletleri ISO" |
| "dis hekimligi el aletleri" | top-3 | Long-tail: "dis hekimligi el aletleri toptan", "dis hekimligi el aletleri uretici" |
| "ortodontik el aletleri" | top-5 | "ortodontik forseps", "ortodontik kompozit aleti" |
| "kompozit el aletleri" | top-5 | "kompozit yerlestirme aleti", "kompozit semer aleti" |
| "klinik cihazlar" | rekabetci | "dis uniti", "kompresor", "otoklav" (cihaz konusunda Raven nis cikabilir) |
| "implant cerrahi aletleri" | rekabetci | "implant osteotomi seti", "implant sirketi vs urun" |
| "frez" / "elmas frez" | rekabetci | Long-tail: "elmas frez fiyat", "elmas frez Meisinger karsilastirma" |

**Strateji:** Generic ("cerrahi el aletleri") icin dentalsepet'i domain authority + uzun yas avantajiyla kisa vadede gecmek zor. **Ama long-tail / informational keyword'lerde (blog + 1450 char editoryal aciklama) Raven 6-12 ayda ust-3 cikabilir.**

### 6.4 Sosyal/Trust Asimetrisi

Dentalsepet'in sosyal medya kaslari **mevcut sirkete oranla zayif** (4.7K IG, 232 FB). Raven Dental, agresif IG content + LinkedIn thought leadership ile 12 ay icinde benzer veya ustun takipci-etkilesim'e ulaşabilir.

**Raven icerik temalari onerisi:**
1. "Atolyeden uretim" video serisi (paslanmaz celikten cerrahi cimbiz nasil iglenir)
2. "Hekim hekime" referans / vaka sunumu
3. ISO/EN sertifika belgelerinin transparent paylasimi
4. Kalibrasyon ve sterilizasyon protokol PDF'leri (lead magnet)
5. Long-form blog: "30 cerrahi alet, hangi prosedurde, neden, nasil temizlenir"

---

## 7. Tehditler ve Riskler

### 7.1 Dentalsepet'in Bize Yapabilecegi (Defansif)

- Kendi uretici markasini cikarmasi (private label) — orta vadeli risk.
- Native uygulama ile kazandigi musteri loyalty'sini agresif fiyat-promosyon ile derinlestirmesi.
- 30+ kategorideki SEO ust pozisyonlarini blog + structured data ile guclendirmesi.
- Pazaryeri (Hepsiburada, Cimri) entegrasyonunu derinlestirmesi.

### 7.2 Dentalsepet'in Acik Riskleri (Bizim Lehimize)

- KVKK ihlali iddiasi — kurumsal alici (zincir klinik, devlet hastanesi) icin red-flag.
- Siparis reddi politikasi — tibbi-cihaz mevzuati hakli ama UX iletisimi zayif.
- Sosyal medya etkilesimi dusuk (Facebook 232 like — neredeyse "ihmal").
- Schema.org / rich result ihmali — Google trafik kaybi.
- Native uygulama disinda 2010'lu yillarin altyapi sinyali (URL `pmk/pmu` paterni).
- Pure-distribütor — uretici migrasyonu/private-label icin yatirim+sertifika+tedarik zinciri kurmasi 2-3 yil surer.

---

## 8. Onerilen Aksiyon Listesi (Raven Dental)

**0-3 ay (hizli kazanim):**
1. Schema.org `Product+Offer+AggregateRating` urun sayfalarinda tam yayilim — rich result + trafik artisi.
2. "Tibbi Cihaz Satis Ruhsatli + DISSIAD Uyesi + Sertifika" trust badge'leri header'a.
3. Cerrahi/Ortodontik/Kompozit long-tail icin 10-12 blog yazisi (1500+ kelime).
4. Native uygulama icin PWA + Add-to-Homescreen aktivasyonu (kisa vadeli kop-soyun).
5. Eticaret.gov.tr profil aktivasyonu / vurgu.

**3-6 ay (orta vadeli):**
6. React Native veya Flutter ile native uygulama MVP (sepet, push, hizli yeniden-siparis, fiyat takibi).
7. Hepsiburada Premium / Trendyol B2B magaza pilot.
8. KVKK aydinlatma metni yenileme — sikayetvar'daki dentalsepet hatasini kullanma firsati (transparent ofansif iletisim).
9. Newsletter + WhatsApp Business catalog entegrasyonu.

**6-12 ay (uzun vadeli):**
10. EN sertifika / ISO 13485 / CE Marking dokumantasyonu ile uluslararasi hreflang aktivasyonu (hreflang TR + x-default zaten var; EN ekle).
11. Saglik portallarinda guest-post / icerik isbirligi (Hipokratist'in dentalsepet referansini cevirebiliriz).
12. "Hekim hekime" referans programi — Raven Cerrahi Aletler kullanan zincir kliniklerden vaka-sunumu.

---

## 9. Metodoloji Notu ve Veri Bosluklari

### 9.1 Erisim Kisitlamasi

WebFetch tool izni reddedildi (`Permission to use WebFetch has been denied`). Bu nedenle:

- HTML kaynak kodu (meta tag, schema, header) dogrudan goruntulenmedi.
- `robots.txt`, `sitemap.xml`, security header'lar dogrudan dogrulanamadi.
- PageSpeed Insights / Lighthouse / GTmetrix sonuclari yok.

### 9.2 Veri Kaynaklari

Analiz Google Web Search (Google SERP snippet'i + 3. parti veritabani — DISSIAD, eticaret.gov.tr, sikayetvar, eksisozluk, LinkedIn, Instagram, Facebook, Google Play, App Store) uzerinden yapildi.

### 9.3 Erisilemedi Olarak Isaretlenmis Alanlar

- Meta description tam metni
- H1 tag icerigi (sayfa bazinda)
- Schema.org JSON-LD blogu tam icerik
- sitemap.xml URL sayisi
- robots.txt direktifleri
- HTTP response header (HSTS, CSP, X-Frame-Options, Referrer-Policy)
- HTML weight (bytes)
- Mobile Friendly test sonucu
- Core Web Vitals (LCP, INP, CLS)
- Image alt-text yapisi
- Filtre/faceted-search UI detayi
- Tam urun gorseli sayisi / 360 / zoom
- Stok gosterimi (var/yok)
- Sepet UX detayi (siparis-sayfasi UI)
- KVKK aydinlatma metni tam icerik
- Schema markup test sonucu

**Bu alanlar icin onerilen takip:** Yerel curl/Playwright/Selenium ile direkt fetch. Veya WebFetch yetkisi acik bir baska oturum.

---

## 10. Sonuc ve Stratejik Tavsiye

Dentalsepet.com **olcek lideri** (envanter genisligi, sektorel taninma, yas-otoritesi) ancak **kalite / trust / icerik / modern teknik SEO** acilarinda asilabilir bos alanlar birakmaktadir. Raven Dental, **uretici-marka konumlanmasini koruyarak**, fiyat-public + schema-zenginligi + native uygulama + blog/video icerik atagini kombine ederek 12-18 ay icinde **niş ama yuksek-marjli ust segmentte** lider olabilir. Bu strateji, dentalsepet'le dogrudan SKU-savasina girmek yerine, paralel ama daha derin bir kalite-eksenli kanal acmayi onerir.

---

# Bitirme Ozeti

**Dentalsepet'in 3 guclu yani:**
1. **Olcek ve envanter genisligi** — 9.300+ indirimli urun, 2.134 el aleti, 30+ ust kategori.
2. **Yas-otoritesi ve sektorel taninma** — 2006'dan beri, "Turkiyenin Dis Deposu" brand-recall, DISSIAD uyesi, Tibbi Cihaz Ruhsatli, eticaret.gov.tr kayitli.
3. **Native mobil uygulama (iOS + Android)** — tekrarlayan B2B musteri icin somut UX silahi.

**Raven Dental icin 3 kritik firsat boslugu:**
1. **Schema.org + rich result + insan-okur URL ustunlugu** — Raven'in mevcut altyapisi dentalsepet'in `pmk/pmu` numerik URL'leri ve schema-eksigine karsi *organik trafikte hizli ust-siralama* saglar; ozellikle long-tail editoryal keyword'lerde 6-12 ay icinde top-3 erisilebilir.
2. **Trust + icerik bosluğu** — dentalsepet'in KVKK ihlali iddiasi, login-gated fiyat ve zayif sosyal medya, Raven icin "transparent uretici, sertifikali, public-fiyat, atolyeden video" konumlanmasiyla derin bir farklilastirma firsati yaratir.
3. **Native uygulama acigi (kendi yanimizda)** — bu, dentalsepet'in yegane somut UX ustunlugu olarak kapatilmasi gereken birinci-oncelik teknik borc; PWA ile 0-3 ay, React Native ile 6 ay icinde paritye ulasilabilir.

---

**Kaynaklar:**
- [Dentalsepet.com Ana Sayfa](https://www.dentalsepet.com/)
- [Dentalsepet El Aletleri Kategori (PMK131, 2.134 urun)](https://www.dentalsepet.com/el-aletleri-pmk131)
- [Cerrahi El Aletleri (PMK58)](https://www.dentalsepet.com/cerrahi-el-aletleri-pmk58)
- [Ortodontik El Aletleri (PMK804)](https://www.dentalsepet.com/ortodontik-el-aletleri-pmk804)
- [Kompozit El Aletleri (PMK763)](https://www.dentalsepet.com/kompozit-el-aletleri-pmk763)
- [Klinik Cihazlar (PMK245)](https://www.dentalsepet.com/klinik-cihazlar-pmk245)
- [Indirimli Urunler (9.322)](https://www.dentalsepet.com/indirimli-urunler)
- [Kampanyali Urunler (498)](https://www.dentalsepet.com/kampanyali-urunler)
- [Hakkimizda](https://www.dentalsepet.com/hakkimizda)
- [Iletisim](https://www.dentalsepet.com/iletisim)
- [Uyelik Hakkinda](https://www.dentalsepet.com/uyelik-hakkinda-pml4)
- [Musteri Hizmetleri](https://www.dentalsepet.com/musteri-hizmetleri)
- [Sikayetvar Profili](https://www.sikayetvar.com/dentalsepetcom)
- [Sikayetvar KVKK Ihlali Iddiasi](https://www.sikayetvar.com/dentalsepetcom/kisisel-veri-guvenligi-ihlali-ve-saygisiz-musteri-hizmetine-sikayet)
- [Sikayetvar Siparis Reddi](https://www.sikayetvar.com/dentalsepetcom/dentalsepetcom-siparis-reddi)
- [DISSIAD Dentalsepet Uye Profili](https://www.dissiad.org.tr/uye/dentalsepet)
- [eticaret.gov.tr Site Profili](https://www.eticaret.gov.tr/siteprofil/F1F206BA557D471A961A556B9CD13A94/wwwdentalsepetcom)
- [LinkedIn Sirket Sayfasi](https://tr.linkedin.com/company/dentalsepet-com)
- [Instagram (@dentalsepet)](https://www.instagram.com/dentalsepet/)
- [Facebook Sayfasi](https://www.facebook.com/DENTALSEPETTT/)
- [YouTube Kanali](https://www.youtube.com/channel/UCrWGvrEhvHS0HwlBiAhSybg)
- [Google Play Mobile App](https://play.google.com/store/apps/details?id=com.dentalsepet.dentalsepetmobileappp)
- [iOS Mobile App](https://www.appbrain.com/appstore/dental-sepet/ios-1483828845)
- [Cimri Dentalsepet Fiyat Indexi](https://www.cimri.com/dentalsepet)
- [Hepsiburada Dentalsepet Magaza](https://www.hepsiburada.com/dentalsepet)
- [Eksisozluk Dentalsepet Basligi](https://eksisozluk.com/dentalsepet-com--5487788)
- [Hipokratist Saglik Ansiklopedi Atifi](https://hipokratist.com/dental-sepet/)
- [Raven Dental DISSIAD Profili](https://www.dissiad.org.tr/uye/raven-dental-1)
- [Raven Dental Tendata IhracatKayit](https://www.tendata.com/en/supplier/raven-dental-ic-ve-dis-ticaret-limited-sirketi-TURI1c855c5c99a87a738b1dd816114fd1b9.html)
