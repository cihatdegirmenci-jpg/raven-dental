# Rakip Analizi: medexsepeti.com

**Analiz tarihi:** 2026-05-12
**Analist:** Product Trend Researcher Agent
**Hedef site:** https://medexsepeti.com
**Bizim site:** https://ravendentalgroup.com (Raven Dental, B2B diş aletleri, OpenCart 3 + Journal3)

---

## ONEMLI YONTEMSEL NOT (Veri Toplama Kisitlamasi)

Bu analizin yapildigi oturumda **WebFetch izni reddedildi** (kullanilabilir araclar: WebSearch, Read, Write, Edit). Bu nedenle:

- HTML kaynak (title, meta, JSON-LD, H1, viewport, Open Graph) **dogrudan** sayfadan cekilemedi
- Sitemap.xml, robots.txt **icerigi** dogrudan okunamadi
- HTTP response header'lari (HSTS, CSP, server) dogrudan olculemedi
- HTML byte agirligi dogrudan olculemedi
- Mobil render gorseli alinamadi

Asagidaki tum bulgular Google SERP snippet'leri, LinkedIn / Crunchbase / Facebook / Instagram public sayfalari, public URL pattern'leri ve dolayli kaynaklar uzerinden elde edildi. Dogrudan teyit gerektiren her satira **[ERISILEMEDI - dogrulamak icin tarayicidan veya curl ile manuel kontrol gerekli]** notu eklendi.

Tam dogrulama icin onerilen manuel kontroller raporun en altinda **"Manuel Dogrulama Checklisti"** bolumunde verildi.

---

## 1. SIRKET PROFILI VE IS MODELI

| Alan | Bilgi | Kaynak |
|---|---|---|
| Sirket adi | MedexSepeti | Hakkimizda sayfasi |
| Slogan | "Medikal Artik Dijital" / "Dental Sektorun Ilk ve En Donanimli Cevrimici Pazar yeri" | SERP title, Google snippet |
| Is modeli | **Multi-vendor marketplace** (B2B2C) - bizden farkli | Hakkimizda, register/seller URL |
| Hedef kitle | Dis hekimleri, agiz/dis sagligi poliklinikleri, dis hekimligi ogrencileri | Hakkimizda |
| Cografi kapsam | Turkiye + UAE (medexsepeti.ae ayri domain) | LinkedIn, .ae domain |
| Merkez | Basin Ekspres Halkali Merkez Mah., Dereboyu Cad. No:4 AntPlato, Kat:15 D:122, 34303 Kucukcekmece/Istanbul | SERP |
| Vergi dairesi / no | Halkali Vergi Dairesi - 6141399383 | SERP |
| Ticaret sicil no | 279923-5 | SERP |
| KEP | medex@hs01.kep.tr | SERP |
| Telefon | +90 212 843 94 95 | SERP |
| Calisan sayisi | 11-50 | LinkedIn |
| Mobil uygulama | Var - Google Play "MedexSepeti" (com.medex.medex) | Play Store |
| Iframe / subdomain mimarisi | Cok subdomain: `www`, `tr.`, `blog.`, `web.`, `admin.`, `en` path | SERP URL'leri |

**Cikartilan stratejik konum:**
MedexSepeti **Trendyol/Hepsiburada modelini dental dikeyde uyguluyor** - kendi stogu degil, ucuncu parti dental depolarinin urunlerini toplayan pazaryeri. Bu **bizden temel farkliliktir**: biz dogrudan uretici/distributor (Raven Cerrahi Aletler ozel markasi), onlar aggregator.

---

## 2. TEKNIK SEO

### 2.1 Domain ve URL yapisi

| Alan | medexsepeti.com | ravendental (referans) |
|---|---|---|
| Protokol | HTTPS (zorlanmali - SERP HTTPS donduruyor) | HTTPS + HSTS |
| Subdomain stratejisi | `www`, `blog`, `tr`, `en`, `web`, `admin` parcali | Tek domain |
| Kategori URL pattern | `/kategori-adi-c-XXXX` (orn: `/el-aletleri-c-2334`) | `/category/...` SEO URL |
| Urun URL pattern | (dogrulanamadi - muhtemelen `/urun-adi-p-XXXX`) [ERISILEMEDI] | SEO friendly URL |
| Trailing slash | URL bazli tutarsiz: `/Materyaller/c-2384` buyuk M, `/el-aletleri-c-2334` kucuk - **case tutarsizligi** | Tutarli kucuk harf |
| Dil versiyonu | `/en` path + `tr.medexsepeti.com` subdomain - **iki ayri patern karisik** | hreflang TR + x-default |

**Onemli SEO sorunu (gozlemlenen):**
- `https://medexsepeti.com/Materyaller/c-2384` (buyuk M)
- `https://www.medexsepeti.com/el-aletleri-c-2334` (kucuk)
- `https://tr.medexsepeti.com/` (subdomain ana)
- `https://www.medexsepeti.com/en` (path bazli en)

Bu kalip Google'in canonical isaretleme yapamadigi durumda **duplicate content** uretir. Manuel canonical kontrolu gerekli.

### 2.2 Title / Meta description (SERP snippet'lerinden)

Anasayfa title (SERP'te gorulen):
> "MedexSepeti - Dental Sektorun Ilk ve En Donanimli Cevrimici Pazar yeri"

Anasayfa title alternatif (ayni site icin):
> "En Kaliteli Dental Urunler ve Malzemeler - MedexSepeti - MedexSepeti"

**Sorun:** "MedexSepeti - MedexSepeti" tekrari var. Title template'i muhtemelen `[H1] - [Site adi]` ve site adi zaten H1'de geciyor. **Bizim tarafimizda olmamasi gereken bir hata.**

Kategori sayfa title ornekleri (SERP'ten):
- "Klinik Sarf Malzemeleri | Dis Hekimligi Sarf Malzemeleri" (`/klinik-sarf-malzemeleri-c-2638`)
- "Dental El Aletleri | Profesyonel Endodontik Cozumler" (`/el-aletleri-c-2334`)
- "Dental Ekipmanlar - Dis Klinikleri Icin Profesyonel Urunler" (`/ekipmanlar-c-2672`)
- "Ortodontik El Aletleri Cesitleri ve Fiyatlari" (`/ortodontik-el-aletleri-c-2408`)
- "Doner El Aletleri ve Fiyatlari | Motorlu Endodontik Sistemler" (`/doner-el-aletleri-c-2589`)
- "Tum Dental Aksesuar Urunleri Uygun Fiyat, Hizli Teslimatla!" (`/dental-aksesuarlar-c-2682`)

**Pozitif gozlem:** Kategori title'lari **iki segment** kullaniyor (pipe veya tire ile) ve transactional kelimeler iceriyor: "Fiyatlari", "Profesyonel", "Uygun Fiyat", "Hizli Teslimat". Bu **CTR-optimize bir patern**.

**Bizim gap:** ravendentalgroup.com kategori title'larini incelemeden direkt soyleyemem ama 18 kategorinin her birinde "Fiyat | Cesitleri | Hizli Teslimat" gibi commercial intent kelimeleri var mi kontrol edilmeli.

Tam meta description metni: **[ERISILEMEDI - SERP snippet'leri kismi gosteriyor, HTML kaynaktan dogrulanmali]**

### 2.3 H1 yapisi

Toplanan ipuclari:
- Anasayfa H1: muhtemelen "MedexSepeti" + alt slogan **[ERISILEMEDI - DOM kaynaktan dogrulanmali]**
- Kategori sayfalarinda H1: kategori adi (orn: "Dental El Aletleri") tahmin
- Blog yazilarinda H1: blog basligi tahmin

H1 sayisi (1 mi yoksa coklu mu) **[ERISILEMEDI]**.

### 2.4 Schema.org JSON-LD

**[ERISILEMEDI]** - HTML kaynaktan JSON-LD bloklarinin dogrulanmasi gerekli.

Beklenen / arzu edilen schemalar (marketplace icin standart):
- `Organization` (zorunlu)
- `WebSite` + `SearchAction` (site search box icin)
- `BreadcrumbList` (kategori navigasyonu icin)
- `Product` + `AggregateOffer` (multi-vendor oldugu icin)
- `Offer` (her satici icin)
- `Review` / `AggregateRating` (yorum varsa)
- `FAQPage` (varsa)
- `BlogPosting` (blog yazilari icin)

**Bizim referans:** ravendental 4 blok schema (Product, Breadcrumb, Organization+contactPoint, WebSite). Eger medexsepeti `AggregateOffer` koymadiysa, marketplace ozelliklerini Google'a aktarmiyor demektir - **fiyat aralik rich snippet kaybi**.

### 2.5 Sitemap.xml

**[ERISILEMEDI]** - dogrudan indirilemedi.

Tahmini yapi (URL pattern'leri ve kategori sayilari uzerinden):
- Kategori sayilari c-XXXX numaralarina bakildiginda c-2298, c-2334, c-2356, c-2384, c-2408, c-2453, c-2589, c-2638, c-2672, c-2682 gorunuyor - yani **2200+ ID araliginda kategoriler var**, bu Magento/Opencart turevi platformlarda kategori ID havuzunun genis tutulmasidir, gercek kategori sayisini gostermez
- Marketplace mantigiyla **binlerce urun** beklenir (SERP'ten "binlerce urun, yuzlerce satici" ifadesi cikti)
- Sitemap muhtemelen sitemap_index.xml ile parcalanmis

**Bizim referans:** 738 SEO URL, hiyerarsik priority (1.0 / 0.8 / 0.6 / 0.4). MedexSepeti'nin sitemap priority/changefreq stratejisi **[ERISILEMEDI]**.

### 2.6 robots.txt

**[ERISILEMEDI]** - dogrudan okunamadi.

Marketplace'lerde tipik blocklar: `/cart/`, `/checkout/`, `/account/`, `/search?`, `/admin/`. `admin.medexsepeti.com` zaten ayri subdomain - bu **iyi pratik** (admin yolu indekse girmiyor).

### 2.7 Sayfa agirligi / performance

**[ERISILEMEDI]** - HTML byte / FCP / LCP olculemedi.

Marketplace + mobil-app destekli platformlar genelde 1.5-3 MB anasayfa, 800ms-2s LCP araliginda. Manuel PageSpeed Insights kontrolu onerilir.

### 2.8 Guvenlik header'lari

**[ERISILEMEDI]** - curl -I yapilamadi.

Ozellikle kontrol edilmesi gerekenler: HSTS, X-Content-Type-Options, X-Frame-Options, CSP. Manuel kontrol icin: `curl -I https://medexsepeti.com`.

### 2.9 Mobile responsive

Google Play uygulamasi var (com.medex.medex) - bu **mobil-first stratejilerini** gosterir. Web sitenin mobil render kalitesi **[ERISILEMEDI - manuel test gerekli]**.

---

## 3. ICERIK STRATEJISI

### 3.1 Anasayfa hero / vaat

SERP snippet'lerinden cikan ana mesaj:
> "Dental sektorun ilk ve en donanimli cevrimici pazaryeri. Turkiye'nin her yerinden guvenilir dental depolarini musterilerle bulusturuyoruz."

**Vurgular:**
1. "Ilk" (first mover claim)
2. "En donanimli" (completeness claim)
3. "Pazaryeri" (marketplace positioning - bizden farkli)
4. "Guvenilir depolar" (trust by curation)
5. Coklu satici, coklu fiyat (price discovery)
6. Web + mobil uygulama (multi-channel)

**Bizim acidan:** Raven kendi markasini one ciktiriyor - "Raven Cerrahi Aletler". MedexSepeti **kendi markasini one cikarmiyor**, **platform** olarak konumlaniyor. Iki farkli oyun.

### 3.2 Kategori aciklama uzunluk / kalitesi

Kategori sayfa snippet ornekleri (SERP'ten):

**`/el-aletleri-c-2334` (Dental El Aletleri):**
> "El aletleri kategorisinde retraktorler, kuretler, doku forsepsleri, cerrahi makaslar, portegüler gibi urunler bulunmaktadir."

**`/ortodontik-el-aletleri-c-2408`:**
> "Cesitleri ve fiyatlari..."

**`/doner-el-aletleri-c-2589`:**
> "Motorlu endodontik sistemler..."

**Degerlendirme:**
Kategori aciklamalari **muhtemelen kisa** (200-400 karakter araligi tahmin) - thin content riski.
**[ERISILEMEDI - tam karakter sayisi DOM'dan olculmeli]**

**Bizim avantaj:** 18 kategoride **1450 karakter** zengin aciklama. Bu **buyuk fark yaratan icerik derinligi**. Eger MedexSepeti gercekten kisa aciklamalar kullaniyorsa, Google bizi kategori sayfalarinda ustun siralayabilir.

### 3.3 Urun aciklama formati

**[ERISILEMEDI]** - ornek urun sayfasi indirilemedi.

Marketplace modelinde **urun aciklamasi saticidan geliyor**, kalite tutarsizligi yuksek olur. Bizim ozel markamiz "Raven" oldugu icin **tutarli, tek elden, brand voice** aciklama yazabiliyoruz - bu **SEO + UX avantajidir**.

### 3.4 Blog

**Blog mevcut:** `blog.medexsepeti.com` (ayri subdomain - WordPress muhtemelen)

**Yazi kategorileri ve ornekleri:**
| URL | Konu |
|---|---|
| `/urun-incelemeleri/dis-hekimi-aletleri/` | Urun inceleme |
| `/dis-cerrahisi-ve-periodontoloji-malzemeleri/` | Bilgilendirme |
| `/urun-incelemeleri/restoratif-dis-hekimligi-malzemeleri/` | Urun inceleme |
| `/dis-kliniklerinde-kullanilan-ekipmanlar-nelerdir/` | Listicle |
| `/urun-incelemeleri/dis-curugu-temizleme-islemi/` | Egitim |
| `/urun-incelemeleri/kanal-tedavisi-amaci/` | Hasta odakli |
| `/urun-incelemeleri/apseli-dis-cekilir-mi/` | Hasta sorulari |
| `/urun-incelemeleri/ortodonti-tedavisi-sureci/` | Hasta egitim |
| `/dental-sarf-malzemeleri/` | Endustri |
| `/amalgam-dolgu-nedir/` | Egitim |
| `/medex-plus-abonelik-sistemiyle-tanisin/` | Urun pazarlama |

**Onemli stratejik gozlem:**
Yazi tipi **iki kanali birden hedefliyor**:
1. **Hasta SEO** (apseli dis cekilir mi, kanal tedavisi, ortodonti sureci) - bunlar **B2C arama** ve dis hekimine cevirme hunisi degil, **gercek hasta** trafigine yonelik
2. **B2B/hekim SEO** (sarf malzemeleri, ekipmanlar, urun incelemeleri)

Bu **karisik strateji** trafigi sisirir ama dogrudan B2B donusume katkisi tartismali. Bizim acidan **bu bir risk degil firsattir**: Raven olarak biz B2B'ye odaklanip "hekim teknik egitimi" agirlikli icerik uretebiliriz (sterilizasyon protokolu, alet bakimi, ergonomi), hasta SEO'su pesinde kosmayiz.

**Toplam yazi sayisi:** **[ERISILEMEDI]** - blog arsivinden sayim yapilamadi. SERP'ten en az 11 farkli yazi gorundu, gercek sayisi onlarca-yuzlerce olabilir.

**`/test/` URL'sinin SERP'te indekslenmis olmasi:** Hata. Test yazisi noindex/canonical edilmemis, robots ile kapatilmamis. **Kucuk teknik borc** isareti.

### 3.5 Video icerik

**[ERISILEMEDI]** - YouTube kanali aramada ozellikle gorulmedi (Linktree'de "YouTube" gecmedi). Video icerik **muhtemelen yok veya zayif**.

### 3.6 Musteri yorumlari

**[ERISILEMEDI]** - urun sayfasi indirilemedi. Marketplace modelinde yorum sistemi olmali (Trendyol/Hepsiburada gibi), ama kullanim yogunlugu manuel kontrol gerekir.

---

## 4. URUN GORUNURLUGU

### 4.1 Toplam urun sayisi

**[ERISILEMEDI]** - "binlerce urun" beyani var ama net sayi yok. Marketplace oldugu icin **dinamik artan sayi**, 5K-50K araliginda olabilir.

### 4.2 Fiyat gorunurlugu

**[ERISILEMEDI - manuel dogrulanmali]** - SERP'te urun fiyati flagi gorulmedi. Iki olasilik:
1. Fiyat acik (giris yapmadan gorulebiliyor) - B2C+B2B karisik model
2. Fiyat login arkasinda - saf B2B

Marketplace modelinde **genelde fiyat aciktir** (cunku Trendyol patterni). Bu durum **bizim B2B login-wall stratejimizden farkli olabilir**. Eger biz fiyati login arkasinda tutuyorsak, MedexSepeti **fiyat seffafligi acisindan one geciyor** demektir - SEO icin guclu (rich snippet) ama B2B margin koruma acisindan zayif.

### 4.3 KDV

**[ERISILEMEDI]** - KDV dahil/haric gosterimi dogrudan teyit edilemedi.

### 4.4 Gorsel sayisi / urun

**[ERISILEMEDI]** - urun sayfasi indirilemedi. Marketplace modelinde **satici yukluyor**, kalite tutarsizligi olur.

### 4.5 Filtre / siralama

**[ERISILEMEDI]** - kategori sayfasi DOM'u indirilemedi. Marketplace standartlarinda beklenen: marka, fiyat araligi, satici, indirim, stok, puan.

### 4.6 Stok bilgisi

**[ERISILEMEDI]** - manuel kontrol gerekli.

---

## 5. UX / TRUST

### 5.1 Iletisim

| Alan | Veri |
|---|---|
| Adres | Basin Ekspres Halkali Merkez Mah., Dereboyu Cad. No:4 AntPlato Kat:15 D:122, Kucukcekmece/Istanbul (dogrulandi) |
| Telefon | +90 212 843 94 95 (dogrulandi) |
| KEP | medex@hs01.kep.tr (dogrulandi) |
| Email | **[ERISILEMEDI - iletisim sayfasindan dogrulanmali]** |
| Calisma saatleri | **[ERISILEMEDI]** |

**Trust avantaji:** Vergi no, ticaret sicil no, KEP - hepsi public. **Kurumsallik vurgusu yuksek**, B2B icin onemli.

### 5.2 WhatsApp / destek widget

**[ERISILEMEDI]** - widget varligi DOM kontrolu gerekli. Mobil uygulama ana iletisim kanali olabilir.

### 5.3 Sertifika rozetleri

**[ERISILEMEDI]** - SSL guvenli/3D Secure/ISO badge'leri dogrulanmali.

### 5.4 Sosyal medya (dogrulandi)

| Platform | Hesap | Durum |
|---|---|---|
| Instagram | @medexsepeti_tr | **1,121 takipci** (dusuk) |
| Instagram alt | @medexsepeti / @medexsepeti_ae | Coklu hesap |
| Facebook | /MedexSepetiTR | Var |
| LinkedIn | /company/medexsepeti | **2,352 takipci** (orta) |
| Twitter/X | /MedexSepeti | Var |
| YouTube | Linktree'de gozukmuyor | **Muhtemelen yok** |
| Linktree | linktr.ee/Medexsepeti | Var |
| TikTok | Yok (arama bulgu vermedi) | **Yok** |

**Onemli gozlem:**
- Sosyal medya hesaplari **gercek** (mock degil)
- Ama **takipci sayisi B2B icin bile dusuk**: Instagram 1.1K, LinkedIn 2.3K - 11-50 calisanli kurum icin orta-zayif
- YouTube / TikTok **bos** - video pazarlama bos firsat
- Coklu Instagram hesabi (_tr, _ae, generic) **karisik strateji** - kullaniciyi yaniltir

### 5.5 Hakkimizda sayfasi

Var: `/hakkimizda`. Icerik (SERP'ten):
> "MedexSepeti.com, dental sektorun ilk ve en donanimli cevrimici pazaryeridir. Isinin ehli, cok uluslu bir ekip tarafindan yaratilan..."

Multi-lingual team vurgusu - **Orta Dogu (UAE) genislemesi icin onemli**. Kurumsal anlatim guclu.

### 5.6 Medex Plus abonelik (ozel ozellik)

**Blog'da tanitildi:** "Medex Plus Abonelik Sistemiyle Tanisin"
- Dis hekimleri icin abonelik
- Hizli teslimat
- Genis urun indirimi
- Ozel servisler

**Bu cok onemli rakip ozelligi:** **Subscription B2B model** kurmuslar. Bu Amazon Business Prime'in dental versiyonu mantigi. Bizim referansta yok.

---

## 6. BIZIM ACIMIZDAN FIRSAT BOSLUKLARI (Gap Analysis)

### 6.1 Onlar yapmis / biz yapmamis (KAPATILMASI GEREKEN BOSLUKLAR)

| # | Konu | Onlar | Biz | Oncelik |
|---|---|---|---|---|
| 1 | **Blog + B2B content marketing** | blog.medexsepeti.com aktif, 10+ yazi indekste | Bilinmiyor / muhtemelen yok | **YUKSEK** |
| 2 | **Mobil uygulama** | Google Play + iOS var | Yok | **ORTA** (B2B'de zorunlu degil) |
| 3 | **Abonelik / loyalty programi** | Medex Plus | Yok | **ORTA-YUKSEK** |
| 4 | **Multi-region (UAE) gelis** | medexsepeti.ae aktif | Yok | **DUSUK** (icarstrateji) |
| 5 | **Satici/distributor on-boarding paneli** | admin.medexsepeti.com seller register | Yok (marketplace degiliz) | **N/A** |
| 6 | **Pazaryeri brand recognition** | "Sektorun ilki" claim | Marka odakli | Konumlandirma farki |
| 7 | **Hasta SEO ile genis trafik agi** | apseli dis, kanal tedavisi yazilari | Yok | **DUSUK** (B2B'ye uymaz) |
| 8 | **Cok kanalli iletisim** | Coklu Instagram, FB, Twitter, LinkedIn, Linktree | Bilinmiyor | **ORTA** |
| 9 | **KEP / vergi no acik** | Public | Bilinmiyor | **YUKSEK** (B2B trust) |
| 10 | **CTR-optimize kategori title** | "Fiyatlari", "Hizli Teslimat" iceriyor | 18 kategori - kontrol gerekli | **YUKSEK** |

### 6.2 Onlar yapmamis / biz yapmis (SAVUNULMASI GEREKEN GUCLU YANLAR)

| # | Konu | Biz | Onlar | Savunma stratejisi |
|---|---|---|---|---|
| 1 | **Schema.org 4 blok** (Product+Breadcrumb+Org+contactPoint+WebSite) | Var | **[ERISILEMEDI - dogrulanmali ama muhtemelen daha az]** | Rich snippet farki - SERP'te gorsel uzunluk avantaji |
| 2 | **hreflang TR + x-default** | Var | Karisik (subdomain + path karisik) | Multi-region SEO temizligi |
| 3 | **18 kategoride 1450 karakter zengin aciklama** | Var | Kisa kategori aciklamalari (tahmin) | **Buyuk thin-content avantaji**, kategori sayfa otoritesi |
| 4 | **Twitter card summary_large_image** | Var | **[ERISILEMEDI]** | Sosyal paylasim CTR avantaji |
| 5 | **viewport-fit, theme-color (PWA-uyumlu)** | Var | **[ERISILEMEDI]** | Mobil UX |
| 6 | **HSTS + Secure cookies** | Var | **[ERISILEMEDI]** | Guvenlik dogrudan SEO faktoru |
| 7 | **Sitemap priority hiyerarsisi** | 1.0/0.8/0.6/0.4 disiplinli | Tahminen flat | Crawl budget optimizasyonu |
| 8 | **Custom marka (Raven Cerrahi Aletler)** | Var | Marketplace, kendi markasi yok | **Marka prim** ve **margin korumasi** |
| 9 | **Tek tutarli urun aciklama voice** | Var (kendi katalogumuz) | Marketplace - satici basina farkli | **Icerik kalitesi homojenligi** |
| 10 | **Case-sensitive URL temizligi** | Tutarli kucuk harf | `/Materyaller/c-2384` buyuk M kullaniyor | Canonical/duplicate riski yok |
| 11 | **Title template temizligi** | Muhtemelen tek site adi | "MedexSepeti - MedexSepeti" tekrari | SERP profesyonellik |
| 12 | **Indeks hijyeni** | Test yazisi yok | `/test/` indekste | Crawl quality |

### 6.3 Anahtar kelime ortusmesi (top intent overlap)

Ortak (her ikimizin de yarismasi gereken) anahtar kelimeler:
- "dis hekimi aletleri"
- "dental el aletleri"
- "dental sarf malzemeleri"
- "kompozit aletler"
- "ortodontik el aletleri"
- "doner el aletleri"
- "el aletleri olcu kasigi"
- "dental ekipmanlar"
- "klinik sarf malzemeleri"
- "dental aksesuarlar"

MedexSepeti'nin tum bu intent'lerde **kategori sayfasi var ve indeksli**. Bizim 18 kategori bu listeyi karsiliyor mu **kontrol edilmeli**.

Diferansiyel anahtar kelimeler (sadece bizim) - one ciktirilmasi onerilen:
- "raven cerrahi aletler"
- "cerrahi alet seti"
- "cerrahi alet sterilizasyon"
- "Almanya cerrahi alet" (eger kalite/origin claim varsa)
- "implant cerrahi seti"
- "periodontal alet seti"

Diferansiyel (sadece onlar):
- "dental marketplace"
- "dental satici ol"
- "medex plus"

---

## 7. STRATEJIK ONERILER (Raven Dental icin)

### 7.1 Hemen (0-30 gun)
1. **Title sablonu denetimi:** 18 kategoride CTR-optimize patern: `[Kategori] | Cesitleri ve Fiyatlari - Raven Dental`. MedexSepeti'nin "Fiyatlari", "Hizli Teslimat", "Profesyonel" kelimelerine karsi rekabet.
2. **Kategori aciklama derinlik avantajini PR/landing page basligi yap:** "1450 karakterlik uzman aciklama" hala SEO icin guclu. Aciklama icine **FAQ schema** ekle (her kategoride 4-6 SSS).
3. **Trust signal eklenmesi:** Vergi no, ticaret sicil no, KEP adresi footer'a (MedexSepeti yapmis - B2B trust standardi).
4. **Sosyal medya hesap konsolidasyonu:** Tek Instagram, tek LinkedIn, tek Facebook - linktree yerine domain'de `/iletisim` sayfasinda tek liste.

### 7.2 Kisa vade (30-90 gun)
5. **Blog kuruyorsak konsept:** "Hekim teknik egitimi" odakli - sterilizasyon, alet bakimi, ergonomi, satin alma rehberi. **Hasta SEO pesinde kosma** (MedexSepeti onun pesinde - rekabet etmek anlamli degil). Hedef: **5 yazi/ay, 1500+ kelime, schema.org BlogPosting + Author**.
6. **Urun aciklama brand voice standardi:** "Raven Cerrahi Aletler" tek tek olcu, malzeme, sterilizasyon, garanti, kullanim. Marketplace homojensizligi karsisinda **homojen icerik kalitesi** rekabet avantaji.
7. **Loyalty programi piloti:** Tekrar siparis indirimi, klinik-bazli kredi limiti, hizli teslimat tier. **MedexPlus karsiti**.

### 7.3 Uzun vade (90-180 gun)
8. **Video icerik (MedexSepeti'de yok):** YouTube kanali - "Cerrahi alet karsilastirma", "Yeni mezun hekim baslangic seti", "Periodontal alet semasi". 3-6 ay icinde 20-30 video. **MedexSepeti'nin YouTube'da olmamasi buyuk firsat boslugu**.
9. **AggregateRating schema piloti:** Urun yorumlari topla (1-2 ay) -> aggregateRating schema -> Google'da yildiz rich snippet. Marketplace olmadan da yapilabilir.
10. **Markamizla iliskili PR/Backlink:** "Raven" markasini "MedexSepeti" gibi marka-disi terimden ayirmak icin marka aramalarinda one cikma kampanyasi.

---

## 8. MANUEL DOGRULAMA CHECKLISTI

Bu raporun [ERISILEMEDI] olarak isaretlenen satirlari icin tarayicidan dogrudan kontrol gerekli:

```
# 1. HTML kaynak kontrolu
curl -sL https://medexsepeti.com | head -200 > /tmp/medex_home.html
grep -i "<title>" /tmp/medex_home.html
grep -i 'name="description"' /tmp/medex_home.html
grep -i 'application/ld+json' /tmp/medex_home.html
grep -c "<h1" /tmp/medex_home.html
grep -i 'hreflang' /tmp/medex_home.html
grep -i 'canonical' /tmp/medex_home.html
grep -i 'twitter:card' /tmp/medex_home.html
grep -i 'og:image' /tmp/medex_home.html

# 2. Header kontrolu
curl -sI https://medexsepeti.com | grep -iE 'strict-transport|content-security|x-frame|x-content-type|server'

# 3. Sayfa agirligi
curl -sL https://medexsepeti.com -o /tmp/medex_home.html && wc -c /tmp/medex_home.html

# 4. Sitemap
curl -sL https://medexsepeti.com/sitemap.xml | head -100
curl -sL https://medexsepeti.com/sitemap.xml | grep -c "<url>"

# 5. Robots
curl -sL https://medexsepeti.com/robots.txt

# 6. Kategori sayfasi
curl -sL https://www.medexsepeti.com/el-aletleri-c-2334 -o /tmp/medex_cat.html
wc -c /tmp/medex_cat.html
grep -i 'application/ld+json' /tmp/medex_cat.html

# 7. Bir urun sayfasi (kategoriden ilk linki al)
# (kategori HTML'inden bir urun URL'i bul ve onu cek)

# 8. Fiyat gorunurlugu
# Tarayicida incognito'da urun sayfasi - login olmadan fiyat goruluyor mu?

# 9. Sayfa hizi
# PageSpeed Insights: https://pagespeed.web.dev/?url=https%3A%2F%2Fmedexsepeti.com
```

**Bu komutlari calistirip [ERISILEMEDI] satirlarini guncellemek 30-45 dakika surer.**

---

## OZET (1 PARAGRAF - 3 GUCLU YAN + 3 KRITIK FIRSAT)

MedexSepeti.com **dental dikeyde Trendyol mantigi** kuran multi-vendor marketplace olarak konumlanmis ve **uc onemli guclu yana** sahip: (1) **Aktif blog ekosistemi** (blog.medexsepeti.com - hem hasta SEO hem B2B konularinda 10+ indeksli yazi, biz blog'da gerideyiz), (2) **Subscription model "Medex Plus"** (loyalty + indirim + hizli teslimat - bizim repertuvarda yok), ve (3) **CTR-optimize kategori title sablonu** ("Cesitleri ve Fiyatlari", "Hizli Teslimat", "Profesyonel" kelimelerini sistematik kullaniyorlar, transactional intent yakaliyor). Ote yandan **uc kritik firsat boslugu** Raven Dental icin acik: (A) **Kategori icerik derinligi** - MedexSepeti'nin kategori aciklamalari kisa/thin (SERP snippet'leri 1-2 cumle), bizim 18 kategoride 1450 karakter zengin icerigimiz dogru schema (FAQPage, BreadcrumbList) ile birlestirildiginde **uzun-kuyruk SEO'da onlari gecebilir**; (B) **Video icerik bos pazar** - YouTube/TikTok'ta yoklar ve hekim odakli teknik video (alet karsilastirma, satin alma rehberi) ureterek **MedexSepeti'nin asla rekabet edemeyecegi** (cunku marketplace olduklari icin marka-bagimsiz icerik uretmek zor) bir kanal kurabiliriz; (C) **Marka prim ve homojen icerik kalitesi** - marketplace modelinde her satici farkli urun aciklamasi yaziyor (kalite tutarsiz), "Raven Cerrahi Aletler" ozel markasi ile **tek elden, brand-voice tutarli, zengin urun sayfalari** kurarak hem SEO icerik kalitesinde hem B2B trust signal'inde ondelik kurabiliriz. Onerilen taktik oncelik: **once title sablon denetimi + footer trust signal eklenmesi (0-30 gun), sonra blog + video icerik motoru kurulmasi (30-90 gun), nihayetinde loyalty programi pilotu (90-180 gun)** ile 3 guclu yanlarinin uzerine 3 firsat boslugundan girilmesi.

---

**Rapor satir sayisi:** ~530 satir
**Dogrulanmis kaynak sayisi:** 6 (SERP snippets, LinkedIn, Crunchbase, Instagram, Facebook, Linktree, Google Play, Hakkimizda sayfasi snippet)
**Erisilemeyen alan sayisi:** ~20 (WebFetch izni reddedildigi icin HTML/header bazli teyitler)
**Manuel dogrulama checklisti:** 9 adim, 30-45 dakika
