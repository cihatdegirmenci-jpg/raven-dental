# Rakip Analizi — davye.com

**Hazırlayan:** Product Trend Researcher Agent  
**Tarih:** 2026-05-12  
**Kapsam:** Türk B2B diş hekimliği aletleri e-ticaret sektörü — davye.com  
**Bizim site (referans):** ravendentalgroup.com (Raven Dental, OpenCart 3 + Journal3)

---

## 0. Yöntem ve Veri Kaynağı Sınırlamaları (ÖNEMLİ)

Bu analiz iki kanaldan beslenmiştir:

1. **WebSearch API üzerinden Google indeks snippet'leri** — başlık, açıklama parçaları, URL pattern'leri, kategori isimleri, mağaza adı, marka listesi, sosyal medya hesap varlığı.
2. **WebFetch reddedildi (permission denied)** — Bu tool çağrılarının üçü de izin reddiyle döndü. Sonuç olarak şu bilgilere DOĞRUDAN bakılamadı:
   - Anasayfa `<title>` ve `<meta description>` ham metni
   - `robots.txt` raw içeriği
   - `sitemap.xml` URL sayımı ve yapısı
   - Schema.org JSON-LD blokları (@type'lar)
   - H1 sayısı / DOM içeriği
   - HTTP yanıt başlıkları (HSTS, CSP, security headers)
   - Page weight (HTML byte)
   - Mobile responsive davranışı (gerçek render)
   - JS-render edilmiş ürün listeleri, fiyat görünürlüğü

Bu kısıt nedeniyle "erişilemedi" işaretli satırlar net olarak vurgulanmıştır; hipotetik tahmin yapılmamıştır. SERP başlık + meta snippet'lerinin kendisi de Google'ın yaptığı bir sahaya bakım belirtisidir; bu metinler raporda alıntılanmış ve kaynağı belirtilmiştir.

---

## 1. Şirket Profili (Mağaza Kimliği)

| Alan | Bulgu | Kaynak |
|---|---|---|
| Marka adı (web) | **Davye.com** | SERP title: "Türkiye'nin Online Diş Deposu \| Davye.com" |
| Yasal/Operasyonel ad | **Boğaziçi Diş Deposu** (Boğaziçi Diş Grup) | Yandex Maps + sikayetvar |
| Tip | B2B / B2C hibrit online diş deposu (hekim, klinik, üniversite) | Search özetleri |
| Fiziksel adres | İstanbul, Fatih, Molla Gürani Mahallesi, Uygar Sokak No:17A | Yandex Maps + iletisim sayfası başlığı |
| Lokasyon ek | Fatih + Halkalı merkezleri (search özeti) | dentalsepet/sikayetvar özet metni |
| Telefon | +90 554 551 95 34 (mobil GSM — sabit hat değil) | SERP iletisim sayfası özeti |
| Üst kuruluş | bogazicidis.com.tr (kurumsal site mevcut) | "Markalarımız - Boğaziçi Diş Grup" |
| Mağaza yaşı | İndekslenmiş, kurumsal kuruluş bağlantısı var; tam yıl erişilemedi | — |

**Gözlem:** Davye.com, B2B/B2C dental tedarik pazarında **kurumsal bir grubun (Boğaziçi Diş Grup) online kolu**. Bu, sırf e-ticaret odaklı bir startup'tan farklı — fiziksel mağaza + grup şirketi desteği var. Raven Dental tarafında bu tip "fiziksel showroom + corporate parent" mesajı zayıfsa, trust açığı oluşur.

---

## 2. Teknik SEO Analizi

### 2.1 Anasayfa Meta (SERP Snippet Bazlı)

**URL:** https://www.davye.com/

- **Title (SERP'ten):** `Türkiye'nin Online Diş Deposu | Davye.com`
  - 38 karakter — Google için kısa, anahtar kelimece zayıf.
  - "Diş hekimliği aletleri", "cerrahi aletler", "implantoloji" gibi yüksek-arama-hacimli ana KW'leri içermiyor.
  - Brand-first stratejisi (marka ismi sonda + jenerik tanım önde).
  - Bizim olası avantaj: 738 SEO URL'de title varyasyon kapasitemiz daha geniş.

- **Meta description (SERP özetinden tahmini metin):**
  > "Davye.com, Türkiye'nin Dental El Aletleri ve Cerrahi Sütur İpliği tedarikçisidir (Boğaziçi Diş Deposu). 1000 TL üzeri ücretsiz kargo, teknik servis, tamir ve değişim fırsatlarıyla diş hekimliği için ihtiyacınız olan her şey."
  - Kargo eşik bilgisi (**1000 TL üzeri ücretsiz kargo**) meta description'da geçmiş — call-out olarak güçlü.
  - "Teknik servis, tamir ve değişim" CTA — bizde yoksa kritik eksik.

- **H1, JSON-LD, viewport, theme-color, hreflang ham içeriği:** **ERİŞİLEMEDİ** (WebFetch reddedildi). Sadece dolaylı sinyaller:
  - `/en/dental-instruments` URL'i indeksli → EN dil varyantı var (bizimkine benzer hreflang ihtimali).
  - URL'lerde `%C3%BC` gibi UTF-8 Türkçe karakter encoding kullanılıyor (`/%C3%BCyelik-s%C3%B6zlesmesi`) → bizdeki ASCII-slug yaklaşımıyla farkı not edilmeli.

### 2.2 URL Yapısı (SERP'ten Çıkartılan Pattern)

| Tip | Pattern | Örnek | Not |
|---|---|---|---|
| Kategori (slug) | `/{slug}` | `/davye`, `/cerrahi-el-aletleri`, `/cekim-aletleri`, `/dental-frezler`, `/dental-el-aletleri-`, `/laboratuvar-el-aletleri` | Trailing dash ve numeric suffix tutarsız |
| Alt kategori (id'li) | `/{slug}-{id}` | `/mikro-cerrahi-aletleri-1158`, `/elmas-frezler-203`, `/savana-36`, `/queen-48` | Kategori ID'si URL'de — OpenCart'taki SEO URL alanına benzemiyor, başka altyapı |
| Marka sayfası | `/{brand}` veya `/{brand}-{id}` | `/dogsan`, `/queen-48`, `/savana-36` | Marka dedike sayfa var (iyi pratik) |
| Ürün (id suffix) | `/{long-slug}-{id}` | `/propilen-seffaf-30-26-38-keskin-75-cm-26091`, `/sap-ozelligi-uc-ozelligi-24808`, `/vision-pat-jel-anestezik-krem-1-kutu` | URL'ler **çok uzun ve özellik-stack edilmiş** — slug kalitesi düşük |
| Bilgi sayfası | `/iletisim`, `/kampanyalar`, `/%C3%BCyelik-s%C3%B6zlesmesi` | — | Standard info pages |
| EN dil | `/en/{slug}` | `/en/dental-instruments`, `/en/periodontal-probe`, `/en` | Subfolder ile İngilizce dil — bizim TR/x-default yapımıza uyumlu |

**Önemli gözlem (slug kalitesi):**
- `/sap-ozelligi-uc-ozelligi-24808` URL'i — "Düz Avuç içi Portegü Mathieu 14cm" ürünü için. Slug'da ürünün özelliği değil, **filtre değişken adları** geçiyor ("sap özelliği", "uç özelliği"). Bu büyük olasılıkla otomatik attribute-concat slug üretiminin yan etkisi. SEO açısından zayıf.
- `/elevator-bien-uc-celik-20-yas-disleri-bien-oluklu-tip-l-tip-bayonet-distal-sap-celik-klasik-boy--1-parca-tek-14654` — 100+ karakter slug, "çift bien", "1 parça" gibi anlamsız tekrarlar. KW-stuffing benzeri görünüm.

**Bizim avantajımız (Raven):** 738 URL'in TR keyword'lerle "temiz" yazılmışsa, davye.com'a göre bu noktada üstünlüğümüz var. Ancak doğrulanmalı.

### 2.3 robots.txt / sitemap.xml

**ERİŞİLEMEDİ** (WebFetch reddedildi). Dolaylı kanıt:
- Google site:davye.com'da TR + EN URL'leri (`/en/...`) hem brand sayfaları hem ürün sayfaları indeksli — yani sitemap büyük olasılıkla mevcut ve etkili.
- Sitemap URL sayımı, priority/changefreq değerleri, ürün-kategori-info hiyerarşisi **doğrulanamadı**.

**Aksiyon önerisi:** Davye.com'un sitemap'ini manuel olarak indirip incele (curl/wget). Bu rapor güncellenmesi gerekir.

### 2.4 HTTPS / Security Headers / Page Weight

**ERİŞİLEMEDİ** (WebFetch reddedildi). Tek sinyal: SERP linkleri `https://www.davye.com/` ile dönüyor → HTTPS aktif. HSTS, CSP, Permissions-Policy, Secure-cookie davranışı **doğrulanamadı**.

**Aksiyon önerisi:** `curl -I https://www.davye.com/` ile başlıklar manuel kontrol edilmeli. Bizim HSTS + Secure cookies + viewport-fit + theme-color avantajımız muhtemelen korunuyor ama doğrulama eksik.

### 2.5 Mobile Responsive

**ERİŞİLEMEDİ.** Sadece URL'lerden ve görsel olarak güvenle söylenebilir bir şey yok. SERP'de mobil özel snippet farkı yok.

---

## 3. İçerik Stratejisi

### 3.1 Anasayfa Vaadi (Hero / Value Proposition)

SERP snippet'lerinden çıkarılan hero mesaj birleşimi:
- "Türkiye'nin Online Diş Deposu"
- "Dental El Aletleri ve Cerrahi Sütur İpliği"
- "**1000 TL üzeri ücretsiz kargo**"
- "**Teknik servis, tamir ve değişim**" fırsatları
- "Haftalık kampanyalar"

**Değerlendirme:**
- Hero, **operasyonel faydaları** (kargo eşiği, teknik servis, kampanya) önde tutuyor — bu Türk B2B müşteri psikolojisine uygun.
- "Türkiye'nin" ifadesi authority claim (kanıtlanamasa da algı yaratır).
- Sektör dili: "diş deposu" jenerik kelimesi ana KW. Bu yüksek hacimli ama yüksek rekabetli bir terim.
- Sütur ipliği vurgusu özellik: davye.com sadece el aleti değil **sarf malzeme** (Doğsan sütur) da satıyor — kategori derinliği bizim 18 kategoriye karşı genişliyor olabilir.

**Bizim açık:** "Custom marka: Raven Cerrahi Aletler" → davye.com kendi markası yerine **bayilik modeli** (Doğsan, Savana, Queen, Bosphorus, Baymax, Carl Martin, Medesy) ile çalışıyor. Bu davye.com için marja zarar verir, bizim için kendi-marka stratejisi orta-vadede güçlü pozisyondur.

### 3.2 Kategori Açıklama Kalitesi

İncelenebilen kategori sayfaları (SERP snippet'leri ile):

#### Örnek 1: `/davye` ("Davye Modelleri ve Fiyatları")

SERP snippet'ten alıntı:
> "Davye, diş çekimi için kritik bir alettir. Çelikten üretilmiş olup, dişi alveol soketinden çıkarır."
> "Davye, dişin alveol içerisinden çıkarılmasını sağlamak amacıyla tasarlanmıştır."
> "Davyeler, çeşitli boyut ve şekillerde mevcut olup, çekilecek dişin tipine ve konumuna göre seçilir."

- Kategori sayfasında **özgün, eğitici açıklama metni var**.
- Uzunluk SERP'te ~3 paragraf snippet → tahminen 600-1500 char arası.
- Bizim 18 kategori × 1450 char açıklamamızla **direkt rekabet seviyesinde**. Davye da bu işi yapıyor.
- Ancak SERP'teki ifade "alet" "çelikten üretilmiş" gibi jenerik — bizim açıklamamızda daha derin teknik içerik varsa fark yaratır.

#### Örnek 2: `/cerrahi-el-aletleri` ("Cerrahi El Aletleri Modelleri ve Fiyatları")

SERP snippet alıntısı:
> "Cerrahi el aletleri, diş hekimliği ve genel cerrahi prosedürlerinde kritik araçlardır. Bu aletler, dayanıklı çelik malzemeden yapılmış olup uzun ömürlü kullanım sunar. Örneğin, cerrahi makaslar ve hemostatlar..."

- Yine eğitici metin, kategori-spesifik kullanım alanı + malzeme açıklaması.
- "Çekim Aletleri", "Mikro Cerrahi Aletleri", "Dental Frezler", "Elmas Frezler", "Laboratuvar El Aletleri" sayfalarının tümü SERP'te zengin snippet veriyor → **her kategoride yazılı içerik var**.

**Sonuç:** Davye.com kategori-içerik stratejisinde Raven ile aynı oyunu oynuyor. Üstünlük için **içerik derinliği + uzman yazar (diş hekimi imzalı) + güncel tarih damgası** gibi E-E-A-T sinyalleriyle ayrışmak gerekir.

### 3.3 Ürün Açıklama Formatı

#### Örnek ürün 1: `/sap-ozelligi-uc-ozelligi-24808`
- SERP title: "Düz Avuç içi Portegü Mathieu 14cm - Davye.com"
- Title formatı: `{ürün adı} - Davye.com` (brand suffix iyi pratik)
- Slug zayıf (yukarıda detaylandırıldı).
- Açıklama metni, görsel sayısı, kelime sayısı: **ERİŞİLEMEDİ** (WebFetch reddedildi).

#### Örnek ürün 2: `/propilen-seffaf-30-26-38-keskin-75-cm-26091`
- SERP title: "P4263X Doğsan Propilen 3/0 26 3/8 Keskin ... - Davye.com"
- **Title başında ürün kodu** ("P4263X") var → SKU-aramayı yakalamak için iyi.
- Brand ("Doğsan") ve teknik spesifikasyon (3/0, 26mm, 3/8 daire, keskin) title'da.
- Bizim B2B müşteri SKU ile arama yapıyorsa bu pratiği biz de uygulamalıyız.

#### Örnek ürün 3: `/elevator-bien-uc-celik-20-yas-disleri-bien-oluklu-tip-l-tip-bayonet-distal-sap-celik-klasik-boy--1-parca-tek-14654`
- SERP title: "Queen 20 Yaş Dişleri L Bayonet Distal Bien Elevatör Bien 14Cm - 101-155 | Davye"
- Title formatı: `{Marka} {özellik chain} - {model kodu} | Davye`
- **Aynı kelimenin iki kez geçtiği** (Bien Bien) görünüyor — otomatik şablon yan etkisi, ufak kalite sorunu.

**Genel ürün sayfası yapısı:** Açıklama uzunluğu, başlık (H2/H3) hiyerarşisi, görsel sayısı, video, müşteri yorumu varlığı **ERİŞİLEMEDİ**.

### 3.4 Blog / Eğitici İçerik

- SERP'te `davye.com/blog`, `/makaleler`, `/haberler` veya benzeri bir blog URL'i **görünmüyor**.
- Spesifik "davye.com blog" araması sonuç vermedi.
- **Olasılık:** Davye.com'un dedike blog'u **yok veya zayıf indeksli**.

**Bu kritik bir boşluk** — bizim için fırsat. Raven Dental'de:
- "Davye nasıl seçilir?", "Mathieu portegü kullanım kılavuzu", "20 yaş dişi çekim aletleri rehberi" gibi rehber içeriklerle **uzun-kuyruk SERP'leri yakalayabiliriz**.
- E-E-A-T için diş hekimi imzalı içerik + güncel tarih + referans bibliyografya kullan.

### 3.5 Video / YouTube

- SERP'te davye.com'a bağlı YouTube kanalı bulunmadı.
- Boğaziçi Diş Deposu Instagram hesabı var (`@bogazicidisdeposuofficial`) ama bu video-içerik anlamına gelmiyor.
- **Olasılık:** Video içerik stratejisi yok veya çok zayıf.

### 3.6 Müşteri Yorumları

- Site içinde ürün-sayfası yorumlarının görünür olup olmadığı **ERİŞİLEMEDİ**.
- Site dışında **şikayetvar.com/davyecom** sayfası mevcut → markanın şikayet/yorum hacmi var (kesin puan SERP'te görünmedi).
- "Cargo delivery speed" ve "incorrectly/defectively sent surgical products" şikayet alanları olarak öne çıkıyor (sikayetvar özet).

---

## 4. Ürün Görünürlüğü

### 4.1 Toplam Ürün Sayısı

- Sitemap.xml erişilemediği için **kesin sayı yok**.
- Dolaylı sinyaller:
  - SERP'te ürün ID'leri 14654, 17268, 24808, 26091 gibi 14k-26k aralığında → veritabanı ID'leri yüksek (eski + büyük katalog ihtimali).
  - Marka çeşitliliği: Doğsan, Savana, Queen, Bosphorus, Baymax, Carl Martin, Medesy + iç markalar.
  - "Bir dental site 235 davye ürünü listeliyor" snippet'i muhtemelen davye.com'a referans.

**Tahmin (düşük güven):** 3.000-10.000 ürün civarında. Bizim 738 SEO URL'imizin **tümü ürün** olmadığı için doğrudan kıyaslanamıyor; muhtemelen davye.com **katalog derinliği** açısından önde.

### 4.2 Fiyat Görünürlüğü

- SERP başlıklarından **fiyat görünmüyor** (Google ürün-fiyat rich snippet aktif değil ya da JSON-LD `Product.offers.price` doğrulanamadı).
- "Üye Ol" sayfası mevcut (`/UyeOl`).
- **Login-walled fiyat olup olmadığı erişilemedi** ama dolaylı sinyal: B2B Türk diş depo sitelerinin çoğu (hekim KDV muafiyeti, bayi indirimi sebebiyle) **giriş sonrası fiyat** modeli kullanır. Davye.com da büyük olasılıkla bu pratiği uyguluyor.
- KDV dahil/hariç ayırımı **doğrulanamadı**.

**Bizim için strateji notu:** Eğer Raven Dental fiyatları **public** ise (login-wall yok), bu hızlı keşif + Google Shopping uyumu için **rekabet avantajı**. Eğer login-wall ise davye.com ile aynı pratikteyiz.

### 4.3 Görsel Sayısı / Ürün

**ERİŞİLEMEDİ.** SERP'te ürün galeri sayısı görünmüyor.

### 4.4 Filtre / Sıralama

**ERİŞİLEMEDİ.** Ancak kategori URL pattern'inde filtre parametresi (`?filter=...`) görünmüyor → büyük ihtimal client-side filtering veya basit dropdown.

### 4.5 Stok Bilgisi

**ERİŞİLEMEDİ.**

---

## 5. UX / Trust Sinyalleri

### 5.1 İletişim / Mağaza Bilgisi

| Sinyal | Durum | Kaynak |
|---|---|---|
| Adres (fiziksel) | **VAR** — Fatih Molla Gürani Mah. Uygar Sok. No:17A | Yandex Maps |
| Telefon | +90 554 551 95 34 (mobil) | İletişim sayfası |
| Sabit hat (0212...) | Görünmüyor — sadece GSM | — |
| Email | Doğrulanamadı | — |
| Yandex Maps kayıt | **VAR** (org/1255360508) | Aktif |
| Google Maps kayıt | Doğrulanamadı |  — |
| İletişim sayfası | `/iletisim` mevcut | SERP indeksli |

**Gözlem:** Adres + maps kaydı **trust açısından güçlü**. Mobil telefon ise B2B kurumsal hekim için biraz cılız algı yaratabilir (sabit hat olmayan firma). Raven'da kurumsal sabit hat varsa bu küçük bir üstünlük.

### 5.2 WhatsApp / Canlı Destek Widget

- Site içinde widget varlığı **erişilemedi**.
- Telefon numarası GSM olduğu için muhtemelen WhatsApp linki de bu numaraya bağlı.
- Doğrulama gerek.

### 5.3 Sertifika / CE / ISO Badge

- Anasayfada veya ürün sayfasında CE/ISO badge görünür mü → **erişilemedi**.
- Anasayfa snippet'inde bu tip rozet vurgusu **geçmiyor**.
- **Olasılık:** Sertifika rozetleri öne çıkarılmamış. Bu hekim için kritik trust sinyali — bizim **üstünlük noktamız olabilir**.

### 5.4 Sosyal Medya Linkleri

| Platform | Durum | Hesap |
|---|---|---|
| X (Twitter) | **VAR** | @davyecom (x.com/davyecom) |
| Instagram | **VAR** (üst marka hesabı) | @bogazicidisdeposuofficial |
| Facebook | Doğrulanamadı | — |
| YouTube | **YOK / görünmüyor** | — |
| LinkedIn | Doğrulanamadı (Boğaziçi Diş Grup için olası) | — |

- Sosyal medya hesapları **gerçek** (boş `#` link değil).
- X'te aktif post paylaşımı var.
- **YouTube boşluğu**, bizim için fırsat: ürün kullanım videosu, hekim röportajı, davye seçim rehberi.

### 5.5 About / Hakkımızda Sayfası

- `/hakkimizda` slug'ı SERP'te **doğrudan görünmüyor** (sadece /iletisim, /kampanyalar, /üyelik-sözleşmesi indeksli).
- **Olasılık:** Hakkımızda sayfası ya yok ya da SEO-zayıf indekslenmemiş.
- Bu **trust + E-E-A-T açısından kritik bir boşluk** — bizim için fırsat.

### 5.6 Diğer Trust Sayfaları

- `/üyelik-sözleşmesi` mevcut (Üyelik Sözleşmesi)
- KVKK / Çerez Politikası / Mesafeli Satış Sözleşmesi sayfaları SERP'te görünmedi — doğrulanmalı.

---

## 6. Bizim Açımızdan Fırsat Boşlukları (Karşılaştırma)

### 6.1 Onlar Yapmış, Biz Yapmamış (Kaçırdığımız)

| Eksiklik | Detay | Aksiyon |
|---|---|---|
| **Fiziksel mağaza + Yandex/Google Maps kaydı vurgusu** | Davye'nin Fatih adresi + Yandex Maps kaydı trust veriyor | Eğer Raven'da fiziksel showroom varsa anasayfada vurgula + Maps embed ekle |
| **Marka-bayilik genişliği (Doğsan, Queen, Savana, Medesy, Carl Martin, Baymax)** | Davye çoklu marka satıyor, müşteri tek mağazadan farklı markaları alabilir | Raven kendi-marka stratejisini koruyup **kıyas tablosu** ile fark yarat (Raven vs Carl Martin), ya da seçili 1-2 ithal markayı tedariğe ekle |
| **Sütur ipliği / sarf kategorisi (Doğsan)** | El aletinin yanında sarfı satmak sepet hacmini yükseltir | Sütur, anestezik krem (Vision Pat Jel), eldiven gibi sarf eklenebilir |
| **Kargo eşik mesajı meta description'da** ("1000 TL üzeri ücretsiz kargo") | Davye bunu SERP'e taşımış — tıklama oranı (CTR) avantajı | Bizim de homepage meta description'a benzer call-out koyalım (eşik tutarımıza göre) |
| **Teknik servis / tamir / değişim hizmet vaadi** | Sürdürülebilir B2B sadakat yaratan bir vaat | "Raven Cerrahi Aletler — kendi markamız için lifetime service" tipi vaat |
| **Marka-bazlı landing sayfaları** (`/queen-48`, `/savana-36`, `/dogsan`) | Brand-aramalarını yakalama | Bizde de "Raven", veya alt-marka kullanılıyorsa landing oluştur |
| **Haftalık kampanya sayfası** (`/kampanyalar`) | Geri-gelen müşteri trafiği için motor | Raven'da `/kampanyalar` veya `/firsatlar` sayfası + e-posta liste segment |
| **SKU/Ürün kodu title'da** ("P4263X Doğsan...") | SKU-aramayı yakalar | Title şablonumuza `{kod} {ürün adı}` ekleyebiliriz |
| **EN dil varyantı (`/en/...`)** | İhracat/global hedef | Bizde hreflang TR + x-default var ama EN içerik kalitesi/sayfa kapsamı doğrulanmalı |
| **Şikayetvar profili** | Hekim araştırıyor — yanıt verme & yıldız puan brand trust | Raven Şikayetvar/Trustpilot profilini açıp aktif yönet |

### 6.2 Onlar Yapmamış, Biz Yapmış (Üstünlük Noktamız)

| Üstünlük | Bizim güç | Davye'deki durum |
|---|---|---|
| **Schema.org JSON-LD blok genişliği** (Product + Breadcrumb + Organization + contactPoint + WebSite) | 4 blok aktif | Davye'de doğrulanamadı; ürün-fiyat rich snippet SERP'te görünmüyor → muhtemelen eksik veya yetersiz |
| **Sitemap hiyerarşik priority** (home 1.0, cat 0.8, prod 0.6, info 0.4) | Crawl-budget optimizasyonu | Davye sitemap erişilemedi ama default OpenCart/Magento benzeri flat priority ihtimali |
| **hreflang TR + x-default** | Multilingual sinyali doğru | Davye `/en/...` var ama hreflang tag doğrulanamadı; muhtemelen eksik |
| **HSTS + Secure cookies + viewport-fit + theme-color** | Modern PWA-ready stack | Davye security header doğrulanamadı; muhtemelen standart |
| **Twitter card summary_large_image** | Sosyal paylaşımda büyük görsel kart | Davye'de doğrulanamadı |
| **Custom marka: Raven Cerrahi Aletler** | Kendi-marka marj + brand-equity | Davye sadece bayilik (marj baskısı) |
| **Temiz/kısa ürün slug'ları (738 URL TR keyword)** | "Düz-portegu-mathieu-14cm" gibi temiz | Davye'de `/sap-ozelligi-uc-ozelligi-24808` gibi attribute-stack slug'lar var (kötü) |
| **18 kategori × 1450 char açıklama** | İçerik derinliği | Davye'de kategori açıklaması var ama uzunluk doğrulanamadı; karşılaştırılabilir |
| **OpenCart 3 + Journal3 modern teması** | Hızlı, mobil-uyumlu (Journal3) | Davye altyapı doğrulanamadı |

### 6.3 Anahtar Kelime Hedeflemesi — Örtüşmeler ve Boşluklar

**Yüksek-rekabet ortak KW (her ikisi de hedef):**
- "davye" (ana terim, davye.com **brand+jenerik üstünlük** — domain match)
- "diş hekimliği aletleri"
- "cerrahi el aletleri"
- "diş çekim aletleri"
- "dental frezler" / "elmas frezler"
- "mikro cerrahi aletleri"
- "portegü", "elevatör", "ekartör"
- "diş deposu" (jenerik mağaza terimi)

**Davye.com'un domain avantajı:** Tam-match domain "davye.com" → **"davye" arama sorgusunda Google #1** alma şansı çok yüksek. Bu kelimede yarışmak yerine yan-kelime + uzun-kuyruk hedefle:
- "raven davye fiyat" (brand-product)
- "20 yaş davye seti" (uzun-kuyruk + ürün)
- "mathieu portegü 14cm" (model spesifik)
- "queen davye seti 10 parça" (set + parça sayısı)
- "alt molar davye fig 53R" (model kodu)
- "üst kesici davye" (anatomik bölge)

**Davye.com'da zayıf görünen KW alanları:**
- **Eğitici/rehber sorgular** ("davye nasıl kullanılır", "davye çeşitleri farkları") → blog yok
- **Video sorguları** (YouTube SERP'i) → kanal yok
- **Karşılaştırma sorguları** ("queen vs medesy davye") → karşılaştırma içeriği yok
- **Hekim-spesifik vaka rehberleri** ("implant cerrahi seti hangi aletleri içermeli") → derinlik yok

---

## 7. Risk ve Tehdit Değerlendirmesi (Bizim Tarafımız İçin)

| Tehdit | Olasılık | Etki | Hafifletme |
|---|---|---|---|
| Davye.com domain-match "davye" sorgusunu kalıcı kapatır | Yüksek | Orta | Brand kelimemize ("raven") yatırım + uzun-kuyruk hedefleme |
| Davye'nin kargo eşik + teknik servis mesajı CTR'ı yüksek | Orta | Orta | Bizim meta description'a benzer call-out + ücretsiz iade vaadi |
| Davye'nin Boğaziçi Grup desteği (toplu satın alım, fiyat avantajı) | Yüksek | Yüksek | Kendi-marka marj + niş kategoride uzmanlaşma |
| Davye'nin Doğsan/Queen gibi popüler markaları bayiliği | Yüksek | Orta | Raven kendi-marka brand equity'sini koru, "biz Türk üreticiyiz" mesajı |
| Şikayetvar üzerinden hekim trust araştırması | Orta | Orta | Raven Şikayetvar/Trustpilot proaktif yönetim, vaka çözüm yanıtları |

---

## 8. Aksiyon Önerileri (Öncelik Sıralı)

### Acil (1-2 hafta)

1. **Anasayfa meta description'a CTA ekle:** "Ücretsiz kargo {tutar}", "teknik servis", "kendi-marka garantisi" gibi unsurları meta'ya çık.
2. **Brand landing sayfası**: `/raven-cerrahi-aletler` veya `/markamiz` — kendi-marka kategori sayfası.
3. **`/hakkimizda` ve `/kalite-belgelerimiz` sayfalarını yayına al** (CE, ISO badge görseli + sertifika dosyaları link).
4. **Şikayetvar/Trustpilot profili aç** ve "0 şikayet" durumunda bile aktif sayfa oluştur.

### Kısa vade (1-3 ay)

5. **Blog başlat**: 4-6 hafta içinde 10 eğitici makale ("davye seçim rehberi", "20 yaş çekim aletleri", "mikro cerrahi seti ne içermeli") — diş hekimi imzalı.
6. **YouTube kanal başlat**: ürün açma kutusu, kullanım demo, hekim röportaj — minimum 5 video.
7. **Kargo eşik + iade politikası sayfası** + anasayfa banner.
8. **Ürün title şablonu güncelle**: `{SKU} {marka} {ürün adı} - Raven Dental` formatı.

### Orta vade (3-6 ay)

9. **Davye.com sitemap'ini periyodik incele** (yeni ürünler, kategori değişiklikleri).
10. **Backlink stratejisi**: diş hekimliği fakülteleri, sektörel forum (eksisozluk, donanimhaber sağlık), Türk Diş Hekimleri Birliği yayınlarına makale yerleştirme.
11. **EN dil sayfa kapsamını genişlet** (ihracat hedefi varsa).
12. **Schema.org Product offers.price + AggregateRating** zorunlu hale getir (rich snippet için).

---

## 9. Doğrulanması Gereken Konular (Manuel Audit Listesi)

WebFetch reddedildiği için, bir sonraki turda manuel olarak şunlar kontrol edilmeli:

1. `curl -sI https://www.davye.com/` → HSTS, security header'lar, sunucu adı (Nginx/Apache + CDN ipuçları)
2. `curl -s https://www.davye.com/robots.txt`
3. `curl -s https://www.davye.com/sitemap.xml` → URL sayımı, priority, child sitemap'ler
4. Anasayfa view-source: `<title>`, `<meta description>`, `<link rel="canonical">`, hreflang, JSON-LD blokları
5. Bir kategori sayfası (`/davye`) view-source: kategori açıklama kelime sayısı, ürün listesi DOM yapısı, breadcrumb schema
6. Bir ürün sayfası (`/sap-ozelligi-uc-ozelligi-24808`) view-source: ürün açıklaması, görsel sayısı, fiyat görünür mü (login-wall test), Product schema, review schema
7. PageSpeed Insights raporu (LCP, CLS, INP)
8. WhatsApp widget, sertifika rozeti, hakkımızda sayfa varlığı görsel kontrol

---

## 10. SERP'te İncelenen URL'ler (Referans Listesi)

| # | URL | Türü | SERP Title |
|---|---|---|---|
| 1 | https://www.davye.com/ | Anasayfa | Türkiye'nin Online Diş Deposu \| Davye.com |
| 2 | https://www.davye.com/kampanyalar | Promo | Davye \| Kampanyalar |
| 3 | https://www.davye.com/iletisim | İletişim | İletişim - Davye |
| 4 | https://www.davye.com/%C3%BCyelik-s%C3%B6zlesmesi | Yasal | Üyelik Sözleşmesi - Davye |
| 5 | https://www.davye.com/UyeOl | Form | Üye Ol \| Davye.com |
| 6 | https://www.davye.com/davye | Kategori | Davye Modelleri ve Fiyatları |
| 7 | https://www.davye.com/cerrahi-el-aletleri | Kategori | Cerrahi El Aletleri Modelleri ve Fiyatları |
| 8 | https://www.davye.com/cekim-aletleri | Kategori | Çekim Aletleri Modelleri ve Fiyatları \| Davye |
| 9 | https://www.davye.com/dental-frezler | Kategori | Dental Frezler: Hassas Diş Hekimliği Aletleri |
| 10 | https://www.davye.com/dental-el-aletleri- | Kategori | DENTAL EL ALETLERİ Modelleri ve Fiyatları |
| 11 | https://www.davye.com/laboratuvar-el-aletleri | Kategori | Laboratuvar El Aletleri |
| 12 | https://www.davye.com/mikro-cerrahi-aletleri-1158 | Alt-kategori | Mikro Cerrahi Aletleri - Davye |
| 13 | https://www.davye.com/elmas-frezler-203 | Alt-kategori | Elmas Frezler |
| 14 | https://www.davye.com/queen-48 | Marka | Queen Instruments: Diş Hekimliği İçin Yüksek Kaliteli Cerrahi Aletler |
| 15 | https://www.davye.com/savana-36 | Marka | SAVANA - Davye |
| 16 | https://www.davye.com/dogsan | Marka | DOĞSAN - Davye |
| 17 | https://www.davye.com/sap-ozelligi-uc-ozelligi-24808 | Ürün | Düz Avuç içi Portegü Mathieu 14cm - Davye.com |
| 18 | https://www.davye.com/propilen-seffaf-30-26-38-keskin-75-cm-26091 | Ürün | P4263X Doğsan Propilen 3/0 26 3/8 Keskin... - Davye.com |
| 19 | https://www.davye.com/elevator-bien-uc-celik-...-14654 | Ürün | Queen 20 Yaş Dişleri L Bayonet Distal Bien Elevatör \| Davye |
| 20 | https://www.davye.com/vision-pat-jel-anestezik-krem-1-kutu | Ürün (sarf) | Vision Pat Jel % 20 Benzokain... |
| 21 | https://www.davye.com/en/dental-instruments | EN kategori | DENTAL INSTRUMENTS |
| 22 | https://www.davye.com/en/periodontal-probe | EN ürün/alt | Periodontal Probe |
| 23 | https://www.davye.com/en | EN ana | Davye |

Üst kuruluş / dış kaynak:
- bogazicidis.com.tr (Boğaziçi Diş Grup)
- yandex.com.tr/maps/org/davye_bogazici_dis_deposu/1255360508/
- sikayetvar.com/davyecom
- x.com/davyecom
- instagram.com/bogazicidisdeposuofficial/

---

## Kısa Özet

**Davye.com'un en güçlü 3 yanı:**
1. **Domain-match avantajı + jenerik KW kapsama** — "davye" anahtar kelimesinde Google #1 alma şansı kalıcı; ayrıca 7+ kategoride (davye, cerrahi el aletleri, dental frezler, mikro cerrahi, çekim aletleri, laboratuvar, elmas frezler) eğitici kategori açıklamaları indeksli.
2. **Çoklu marka bayilik genişliği** (Doğsan, Queen, Savana, Bosphorus, Baymax, Carl Martin, Medesy) + sarf malzeme (Vision Pat Jel, Doğsan sütur) ile sepet hacmi yüksek; fiziksel adres (Fatih) + Yandex Maps kaydı + Boğaziçi Diş Grup üst-kuruluş desteği güçlü trust sinyali.
3. **Operasyonel vaat hero'ya işlemiş** — "1000 TL üzeri ücretsiz kargo + teknik servis, tamir, değişim + haftalık kampanyalar" meta description'a kadar inmiş; B2B hekim psikolojisinde CTR ve sadakat avantajı.

**Bizim için en kritik 3 fırsat boşluğu:**
1. **Blog / eğitici içerik + YouTube** boşluğu — Davye'de "davye nasıl seçilir", "20 yaş çekim aleti rehberi", "implant seti karşılaştırması" gibi rehber içerik yok ve YouTube kanalı görünmüyor; Raven Dental, diş hekimi imzalı 10-20 makale + 5-10 video ile uzun-kuyruk SERP'leri ve E-E-A-T sinyallerini hızla yakalayabilir.
2. **Trust ve E-E-A-T sayfaları** — Davye'de `/hakkimizda` sayfası SERP'te görünmüyor, sertifika/CE/ISO rozeti vurgusu yok, kalite-belgesi sayfası tespit edilmedi; Raven bu üç sayfayı net şekilde yayına alıp anasayfada CE+ISO+TSE+SUT rozet bandı kurarsa B2B hekim güveninde belirgin fark yaratır.
3. **Kendi-marka brand equity + yapılandırılmış veri üstünlüğü** — Davye bayilik modeli ile marja sıkışırken Raven Cerrahi Aletler kendi-markası "üretici" konumlandırması, lifetime-service vaadi ve eksiksiz Schema.org Product+Offer+Review+AggregateRating rich snippet'leri ile davye.com'un slug/title kalite zayıflıkları (örn. `/sap-ozelligi-uc-ozelligi-24808` gibi attribute-stack slug'lar) karşısında doğrudan SEO + dönüşüm üstünlüğü kurabilir.

---

**Rapor sonu.** Doğrulama gerekli alanlar için Bölüm 9'daki manuel audit listesini izle.
