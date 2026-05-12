# Rakip Analizi: dentalpiyasa.com

> **Hazırlayan:** Product Trend Researcher Agent
> **Tarih:** 2026-05-12
> **Kaynak siteyle karşılaştırma:** ravendentalgroup.com (Raven Dental, OpenCart 3 + Journal3)
> **Sektör:** Türk B2B Diş Hekimliği Aletleri / Sarf / Ekipman E-ticaret

---

## 0. Yönetici Özeti (TL;DR)

Dentalpiyasa.com, 2021'de kurulan, **Türkiye'nin ilk ve en büyük dental pazaryeri** pozisyonunu agresif şekilde sahiplenmiş, **Ekim 2024'te 5 milyon USD değerleme üzerinden Pre-Series A köprü turu** kapatmış, yatırımcıları arasında Sharks & Partners (Mehmet Çelikol, Ersin Nazalı) ve Arvato CEO'su Umur Özkal'ın bulunduğu ölçekli bir platform. **150.000+ ürünü** diş hekimine ulaştırdığını, **Türkiye'deki her 5 diş hekiminden 1'inin** platformu kullandığını, **orta-büyük dental depoların %85'inin** satış yaptığını beyan ediyor. Bu, klasik bir "tek-marka B2B perakende" sitesi değil; **multi-vendor marketplace** (komisyon modeli, EK-1 ile yönetilen güvenli alışveriş sistemi).

Bizim sitemiz (ravendentalgroup.com) ise dar odaklı, kendi markalı **"Raven Cerrahi Aletler"** üretici-perakende modeli. Bu, doğrudan rakip değil; **niş premium oyuncu vs. yatay marketplace** çatışması. Aşağıda her bir kategoride detaylandırılmıştır.

**Erişim notu:** Bu analiz, ortam kısıtlamaları nedeniyle WebFetch yerine WebSearch ile yapılan araştırma + Google'ın kamuya açık arama sonuçlarındaki snippet/metadata verilerine dayanmaktadır. Robots.txt, sitemap.xml, HTTP başlıkları, ham HTML gibi alanlar **doğrudan erişilemedi** ve aşağıda açıkça işaretlenmiştir.

---

## 1. Teknik SEO Analizi

### 1.1 URL Yapısı

Tespit edilen URL pattern'i: **flat / kök seviyede slug** (kategoriler `dentalpiyasa.com/kategori-slug`, ürünler `dentalpiyasa.com/urun-slug`). Bu, klasik OpenCart `index.php?route=...` veya alt klasör (`/category/`, `/product/`) modelinden farklı, agresif bir flat-URL stratejisi.

Örnekler:
- `dentalpiyasa.com/cerrahi-el-aletleri` — kategori
- `dentalpiyasa.com/ortodonti-el-aletleri` — kategori
- `dentalpiyasa.com/kemik-forcepsi` — alt kategori
- `dentalpiyasa.com/elavator` — alt kategori (yazım: "elavator", muhtemelen kasıtlı, kullanıcıların yaptığı tipo'yu yakalamak için, çünkü doğru yazılım "elevatör")
- `dentalpiyasa.com/periost-elevatoru` — alt kategori
- `dentalpiyasa.com/penset` — alt kategori
- `dentalpiyasa.com/implant-motorlari` — kategori
- `dentalpiyasa.com/nexobio-t-cem-implant-yapistiricisi` — ürün
- `dentalpiyasa.com/sd-implant-ortu-seti` — ürün
- `dentalpiyasa.com/sozluk` — sözlük landing
- `dentalpiyasa.com/sozluk/loupe` — sözlük detay
- `dentalpiyasa.com/sozluk/demirbas-ekipman` — sözlük detay
- `dentalpiyasa.com/iletisim` — bilgi sayfası
- `dentalpiyasa.com/sikca-sorulan-sorular/kampanya` — alt-SSS
- `dentalpiyasa.com/hekim-uyelik-sozlesmesi` — sözleşme

**Değerlendirme:** Flat-URL + Türkçe slug + tipo-yakalama (elavator) stratejisi **agresif long-tail SEO** sinyali. URL'lerde marka adı veya brand ek yok (örn. `/woodpecker-implant-x-isikli-fizyodispanser`) — bu, marka + ürün kombinasyon long-tail için iyi.

**Bizimle karşılaştırma:** Raven 738 SEO URL'sini OpenCart standart `/index.php?route=product/product&path=X&product_id=Y` rewriting ile yapıyor olabilir veya tam slug. Eğer biz hâlâ klasik OpenCart URL yapısı kullanıyorsak, Dentalpiyasa **net avantajlı** flat URL'de.

### 1.2 Title / Meta Description Pattern'i

Search snippet'lerinden çıkarılan title pattern'leri:

| Sayfa Tipi | Tespit Edilen Title Pattern |
|---|---|
| Anasayfa | **"Diş Hekimleri ve Diş Depolarına Özel Türkiye'nin İlk ve En Büyük Pazaryeri - Dentalpiyasa"** |
| Kategori | **"{Kategori Adı} Fiyatları ve Çeşitleri - Dentalpiyasa"** (ör: "Kemik Forcepsi Fiyatları ve Çeşitleri - Dentalpiyasa") |
| Ürün | **"{Ürün Tam Adı} - Dentalpiyasa"** |
| Sözlük | **"{Terim} - Dentalpiyasa"** veya genel marka title |

**Analiz:**
- Kategori title **"Fiyatları ve Çeşitleri"** sonek'i = klasik Türk e-ticaret SEO (Trendyol, Hepsiburada, n11 ile aynı pattern). Bu, **commercial intent** keyword'lerini hedefler ("kemik forcepsi fiyatları" gibi).
- Sonek **" - Dentalpiyasa"** brand recognition için.
- Anasayfa title'da **"İlk ve En Büyük"** superlative + value proposition iki dilim ("Diş Hekimleri ve Diş Depoları") = audience targeting.

**Meta description:** Search snippet'larından doğrudan görülemedi; ancak Schema dışı, klasik HTML meta tag formatı muhtemel. Erişilemedi.

**Bizimle karşılaştırma:** Bizde de Türkçe keyword title var. Ama Dentalpiyasa'nın **"X Fiyatları ve Çeşitleri"** pattern'i Türkiye'de **dominant search query** tipini hedefliyor; Raven'da bu yapı varsa kalsın, yoksa eklenmeli.

### 1.3 H1 Hiyerarşi

Snippet'lerdeki sayfa başlıkları, muhtemelen H1'lerin title ile büyük oranda örtüştüğünü gösteriyor:
- Anasayfa H1 muhtemelen "Türkiye'nin İlk ve En Büyük Diş Hekimi Pazaryeri" benzeri
- Kategori H1 = "Kemik Forcepsi" (sadece kategori adı), title'daki "Fiyatları ve Çeşitleri" ise H1 dışı.
- Ürün H1 = ürün tam adı.

**Erişilemedi:** Tam H1/H2 yapısı için canlı HTML lazım.

### 1.4 Schema.org Yapılandırılmış Veri

**Erişilemedi:** Doğrudan view-source erişimi yok. Ancak Dentalpiyasa'nın Google'da rich snippet (yıldız, fiyat) almasından dolayı **muhtemelen Product schema + Offer + AggregateRating** kullanıyor. Pazaryeri yapısı gereği BreadcrumbList ve Organization de muhtemel. WebSite + SearchAction (sitelinks searchbox) için iddialı bir marka olarak büyük olasılıkla mevcut.

**Bizim avantajımız (referansta belirttiğiniz):** Schema.org Product + Breadcrumb + Organization + contactPoint + WebSite — **4 blok**. Bu, **bir markalı tek-vendor mağazada** Dentalpiyasa'dan görece daha disiplinli olabilir. Onlar marketplace olarak vendor-bazlı Offer schema'sı kullanmak zorunda; bizimki üretici/perakende olarak **daha temiz Product schema** sunabilir.

### 1.5 Sitemap.xml

**Erişilemedi:** `https://dentalpiyasa.com/sitemap.xml` doğrudan fetch edilemedi. Ancak Google indexlemesindeki URL çeşitliliği ve "150.000+ ürün" beyanı, sitemap'in **çok büyük** ve muhtemelen **sitemap index** + alt sitemap'lere (sitemap-products.xml, sitemap-categories.xml, sitemap-sozluk.xml) bölünmüş olduğunu kuvvetle düşündürüyor.

Tahmini URL hacmi (yan-veri kaynaklarından):
- **Ürün:** 150.000+ (resmi açıklama: "150 binden fazla ürünü diş hekimlerine ulaştırdı")
- **Kategori + Alt kategori:** ~150-300 (gözlemlenen: Cerrahi El Aletleri, Ortodonti El Aletleri, Tedavi El Aletleri, Diğer El Aletleri, Diğer Cerrahi Aletler, Penset, Periost Elevatörü, Elavatör, Kemik Forcepsi, İmplant Motorları, İmplantoloji, Artikülatörler, Laboratuvar Yardımcı Ürünler, Aksesuar, Setler, Pinler & Postlar, Tedavi Edici Ürünler, Ortodonti & Pedodonti, Best Dental, Piyasemen — sadece 20 örnek; gerçek sayı çok daha yüksek)
- **Sözlük:** A-Z dental terminoloji, muhtemelen 200-500+ entry
- **Bilgi sayfaları:** SSS (kampanya, ödeme, kargo vs.), iletişim, sözleşmeler

**Bizimle karşılaştırma:** Bizim 738 SEO URL'imiz, Dentalpiyasa'nın 150.000+ ürün URL'i karşısında **iki büyüklük mertebesi (×200)** geride. Ama bu karşılaştırma yanıltıcı; biz tek-marka üretici, onlar multi-vendor pazaryeri. **Anlamlı karşılaştırma: 18 kategori 1450 char açıklama** — bunda Dentalpiyasa'dan açıkça daha iyiyiz (aşağıda detayda).

### 1.6 Robots.txt

**Erişilemedi.** Tahmini içerik: Sepet, hesap, üye paneli, login, depo paneli (`/depo`), arama sonuçları (`/search`) muhtemel Disallow; Sitemap referansı bulunması olası. Pazaryeri olarak vendor profillerinde noindex stratejisi de mümkün.

### 1.7 Sayfa Ağırlığı (HTML Weight / Performans)

**Erişilemedi.** Ancak modern Türk pazaryerleri (özellikle Trendyol/Hepsiburada benzeri kullanıcı deneyimi sunmaya çalışan platformlar) genellikle:
- 1.5-3 MB initial page weight (lazy-loading'siz)
- React/Next.js veya Laravel + Vue benzeri stack
- Çok sayıda 3rd-party script (analytics, chatbot, retargeting piksel)

Dentalpiyasa, mobil uygulama paketleri (com.dentalpiyasa.mobileapp ve com.dentalpiyasa.depo) ile birlikte muhtemelen **bir API-driven backend + SSR/CSR hibrit frontend** kullanıyor. Pure SSR OpenCart 3 + Journal3 olan bizden büyük olasılıkla **daha ağır frontend, ama daha hızlı API**'ye sahip.

### 1.8 Güvenlik Başlıkları

**Erişilemedi.** Pazaryeri olduğu, kredi kartı kabul ettiği, mobil uygulamaları olduğu için **HTTPS zorunlu, HSTS muhtemel, Secure Cookie muhtemel**. PCI DSS uyumluluğu iddiası search'te doğrudan görülmedi ama ödeme entegrasyonları (Iyzico/Payten/Garanti Sanal POS vb. tahmin) PCI-DSS zinciri sağlıyor olacaktır.

**Bizim avantajımız:** HTTPS, HSTS, Secure cookies zaten var; Dentalpiyasa ile bu noktada eşitiz veya hafif öndeyiz (Journal3 üzerinde sıkılaştırma yapıldıysa).

### 1.9 Mobil Optimizasyon

- **Responsive web:** Dentalpiyasa'nın mobil uygulama varlığı, web mobil deneyimini muhtemelen "yeterli" seviyede tutuyor; agresif optimize değil çünkü kullanıcı app'e yönlendiriliyor.
- **Native apps:** **Hekim uygulaması** (Google Play `com.dentalpiyasa.mobileapp`, App Store id6742653846, iOS 13+) + **Depo paneli uygulaması** (`com.dentalpiyasa.depo`) = **çift uygulama** stratejisi. Bu, bizim sahip olmadığımız büyük bir UX/retention avantajı.
- **Viewport, theme-color, viewport-fit:** Erişilemedi, ama mobil app'i olan bir platform için web zaten mobil-first olmak zorunda.

**Bizim avantajımız:** viewport-fit + theme-color web meta'larında biz disiplinliyiz. Ama **native app yok** — bu kritik açık.

### 1.10 hreflang ve Uluslararasılaşma

Dentalpiyasa **yalnızca Türkçe / TR pazarı** odaklı; en azından `dentalpiyasa.com/en/` veya `/ar/` gibi alternatif dil URL'i Google sonuçlarında görülmedi. hreflang TR-only veya yok.

**Bizim avantajımız:** hreflang TR + x-default — bu küçük bir technical SEO disiplini puanı. Ama biz de TR-only operasyondaysak operasyonel etki sıfır; **gelecek genişleme** için altyapı hazır olması anlamlıdır.

---

## 2. İçerik Stratejisi Analizi

### 2.1 Anasayfa Hero

Title'dan ve search snippet'lerinden çıkarsanan mesajlaşma:
- **Birincil claim:** "Türkiye'nin İlk ve En Büyük Pazaryeri" — yıllar boyu agresif tekrarlanan slogan
- **Hedef kitle:** Diş hekimleri + diş depoları (çift-taraf)
- **Tonalite:** Profesyonel, B2B, fiyat-rekabet odaklı
- **Tahmini hero unsurlar:**
  - Arama kutusu (genellikle "150.000+ üründe arayın" tarzı placeholder)
  - Kampanya banner (DP10 ilk alışveriş kuponu — 2.500 TL'ye kadar)
  - Kategori shortcut grid
  - Popüler markalar (Bilim İlaç, Best Dental vb. — search'te `dentalpiyasa.com/best-dental` marka sayfası tespit edildi)

**Erişilemedi:** Tam hero görselleri, slider kompozisyonu.

### 2.2 Kategori Sayfası İçerik Stratejisi

**Tespit edilen:** Kategori title formatı **"{X} Fiyatları ve Çeşitleri"** — bu, **transactional intent** yakalama amaçlı.

Kategori açıklama uzunluğu: Search snippet'ları kısa görünüyor ("Cerrahi El Aletleri ... scalpel, scalpel tips, injectors, forceps, curettes, carpule needles, surgical scissors, retractors, tweezers, chisels, suction devices, brushes..." gibi listemsi 1-2 paragraf). Bu, **bizim 1450 karakterlik zengin açıklama**larımızın altında.

**Bizim açık avantajımız:** 18 kategorimizde 1450 char zengin SEO açıklama → Dentalpiyasa kategori açıklamaları muhtemelen 300-600 char arası.

### 2.3 Ürün Sayfası İçerik

Search'te görülen ürün başlıkları çok detaylı (örn: "Nexobio T-Cem İmplant Yapıştırıcısı", "Woodpecker Implant X Işıklı Fizyodispanser", "Dega Me Ca 20:1 202c12 İmplant Anguldurva"). Snippet'lerde görülen ürün özet metinleri:
- Ürünün ne işe yaradığı (1-2 cümle)
- Marka adı
- Bazı teknik özellikler (gücü, uyumluluğu, materyali)

Pazaryeri yapısı gereği **ürün açıklamasını yükleyen vendor**. Bu, kalite varyansına yol açar — bazı ürünler zengin, bazıları çorak.

**Bizim avantajımız:** Tek-vendor (kendimiz) olarak **tutarlı, marka-disiplinli, optimize edilmiş** ürün açıklaması yazabiliriz.

### 2.4 Blog / İçerik Pazarlama

Search sonuçlarında **doğrudan bir `/blog/` URL'i tespit edilmedi**. Ancak **`/sozluk/`** (Dental Terimler Sözlüğü) çok güçlü bir SEO içerik silahı olarak konumlandırılmış:

- A-Z dental terminolojisi
- Her terim ayrı bir URL (örn: `/sozluk/loupe`, `/sozluk/demirbas-ekipman`)
- Genel dental hekim öğrenci ve profesyonel için referans
- Long-tail "X nedir", "X nasıl kullanılır" sorgu intent'ini yakalar

**Bizim açımızdan kritik gap:** Bizim blog/sözlük/eğitim içerik üretimi yok (referansta belirtilmedi en azından). Dentalpiyasa'nın `/sozluk` yaklaşımı, **bilgi-amaçlı (informational) sorgu trafiğini** kendi marka domain'ine çekiyor; biz commercial-only.

### 2.5 Video İçerik

Search sonuçlarında Dentalpiyasa'nın **Facebook video paylaşımı** tespit edildi ("Dentalpiyasa Mobil Uygulamasını İndir" tanıtım videosu). YouTube kanal varlığı search'te ön plana çıkmadı; var olsa bile aktif değil veya küçük.

**Erişilemedi:** YouTube kanal abone sayısı, yıllık video frekansı.

### 2.6 Yorumlar / Sosyal Kanıt

Pazaryeri yapısı gereği **ürün-bazlı yorumlama sistemi** muhtemel (Trendyol modeli). Şikayetvar'da Dentalpiyasa profili **100/100 puanı, 1 değerlendirme, 1 çözülen şikayet** ("Serhat" - "sonuçtan memnun") — bu görece **temiz bir itibar** sinyali, ama **küçük örneklem**.

**Bizim açımızdan:** OpenCart standard review sistemi varsa kullanılmalı; "kendi sitemizde yorum sayısı" niceliksel olarak Dentalpiyasa'nın yanında sönük kalır ama **kalite + uzman doktor tanıklığı** ile fark yaratılabilir.

---

## 3. Ürün Görünürlüğü

### 3.1 Toplam Ürün Sayısı

- **Resmi beyan:** "150.000+ ürünü diş hekimlerine ulaştırdı" (kümülatif satılan/listelenen, tam canlı SKU değil)
- **Sektör beyanı:** "Dental sektör ~80.000 farklı ürün tipine sahip" — Dentalpiyasa'nın iddialı bir kapsama oranı olduğunu söylüyor
- **Vendor sayısı:** Türkiye'deki **orta-büyük dental depoların %85'i**

Buna karşı:
- **Tek tek alt kategorilerde tespit edilen örnek ürün sayıları:** Elavatör 488, Periost Elevatörü 109, Kemik Forcepsi 71 (Bu sayılar `WebSearch` sonucu olarak doğrulandı — bunlar **alt-kategori başına aktif listing sayısı**, gerçek SKU.)
- Tek bir alt kategoride 488 SKU = canlı katalog **çok büyük**.

### 3.2 Fiyat Görünürlüğü (Login Wall?)

**Tespit:** Search snippet'leri kategori sayfa başlığında **"Fiyatları"** kelimesini açık şekilde kullanıyor. **"Fiyatı görmek için giriş yapın"** tarzı bir login-wall **yok** veya en azından guest user'a görünür durumda. Bu, **agresif şeffaf fiyat** pozisyonu.

İlave kanıt:
- "Tüm fiyatlara KDV dahildir" beyanı
- "Aynı ürünün tüm depolardaki fiyatlarını karşılaştırma" özelliği
- DP10 kupon kodu fiyat üzerine uygulanıyor → fiyat kullanıcıya açık

**Stratejik anlam:** Bu, **B2B sektöründe sıra dışı** bir karar. Geleneksel dental depolar genelde fiyatı sakla → diş hekimine telefonla aç. Dentalpiyasa **B2C-vari fiyat şeffaflığı** ile dental sektörü disrupte ediyor.

**Bizimle karşılaştırma:** Eğer Raven'da fiyatı login-wall'un arkasında saklıyorsak, **kritik bir stratejik soru**: rakibimiz kapalı, biz şeffaf mı? Şeffaf, biz daha şeffaf mı? Bu, dönüşüm oranını doğrudan etkiler.

### 3.3 Ürün Görseli

Marketplace yapısı → vendor-yüklemeli görsel → **kalite varyansı yüksek**. Bazı ürünler profesyonel ürün fotoğrafı, bazıları cep telefonuyla çekilmiş stoğa-ait fotoğraf.

**Bizim avantajımız:** Tek-vendor disiplini ile **tutarlı, beyaz-zemin, multi-açı, video** sunabiliriz.

### 3.4 Filtreleme / Faceted Search

Marketplace standartı: marka, fiyat aralığı, satıcı, ortalama puan, kampanya, stok durumu filtreleri muhtemel. "Aynı ürünün depolardaki fiyat karşılaştırması" özelliği = **price-comparison overlay** (Idealo / Cimri tarzı, dental sektörü için unique).

**Erişilemedi:** Tam filtre listesi.

### 3.5 Stok Durumu

Pazaryeri yapısı → vendor başına stok bilgisi gösterimi muhtemel. "Kargo süresi" tahminleri ve "Stok limiti dahilinde kampanya" beyanları stok-aware UI'ye işaret ediyor.

---

## 4. UX / Trust Sinyalleri

### 4.1 İletişim ve Adres

**Tam bilgi tespit edildi** (`/iletisim` sayfasından):

- **Şirket:** Dentalpiyasa Elektronik Ticaret ve Bilişim Hizmetleri Anonim Şirketi
- **Adres:** Reşitpaşa Mahallesi, Katar Caddesi, **İTÜ Arı Teknokent 4 Binası**, No: 2/50 İç Kapı No:6, Sarıyer / İstanbul
- **Telefon:** 0 850 346 3368 (4 hane kısa kod görünümlü 0850 hat)
- **E-posta:** info@dentalpiyasa.com
- **Çalışma saatleri:** Haftaiçi 09:00-19:00, Cumartesi 09:00-13:00
- **VKN:** 2920641326
- **Ticaret Sicil No:** 94629-5
- **Mensubu olduğu sektörel kuruluş:** İstanbul Ticaret Odası

**Trust sinyali analizi:** İTÜ Arı Teknokent **çok güçlü** bir trust + innovation sinyali — Türk girişim ekosistemindeki en prestijli kuluçka adresi. AŞ statüsü + ticaret sicil + VKN açık paylaşımı **ETBİS / e-ticaret mevzuatına tam uyum** demek.

### 4.2 WhatsApp

Search'te **WhatsApp numarası doğrudan ön plana çıkmadı**. 0850 numarası canlı destek için işaretlenmiş; muhtemelen **canlı chat widget** (search snippet'i: "canlı destek butonumuzdan veya 0 850 346 3368 hattımızdan arayarak detaylı bilgi alabilirsiniz") aktif. WhatsApp Business **muhtemel ama doğrulanmadı**.

**Bizim açımızdan:** Eğer Raven'da görünür bir WhatsApp Business kanalı varsa bu **niş B2B müşteri ilişkisinde** Dentalpiyasa'dan üstünlüktür (büyük platformlar genelde toplu canlı-chat tercih eder).

### 4.3 Sertifika ve Akreditasyon

- **ETBİS** kaydı muhtemel (yasal zorunlu)
- **İstanbul Ticaret Odası** üyeliği açık beyan
- **PCI-DSS** ödeme entegratör seviyesinde otomatik
- **CE / TSE / Sağlık Bakanlığı UTS** gibi tıbbi cihaz uygunluk sertifikalarına atıf **yapılmadı** (search'te). Pazaryeri olarak bu sorumluluğu vendor'a yüklüyor olabilir: "Tüm satıcılarımız sahte ürün satması durumunda yasal sorumluluk taşır" beyanı bunu destekliyor.

**Bizim potansiyel avantajımız:** Eğer Raven olarak **TSE, CE, ISO 13485** gibi tıbbi cihaz/cerrahi alet sertifikalarımızı **görsel rozet** olarak göstereblirsek, multi-vendor pazaryerinin yapısal olarak sunamayacağı **brand-level kalite garantisi** sinyali veririz.

### 4.4 Sosyal Medya

- **Instagram:** `@dentalpiyasa` aktif (search'te doğrulandı)
- **LinkedIn:** Company page mevcut (`linkedin.com/company/dentalpiyasa` — search'te doğrulandı, takipçi sayısı tespit edilemedi)
- **Facebook:** Aktif (mobil app tanıtım videosu paylaşıldığı doğrulandı)
- **YouTube:** Doğrulanmadı (search'te ön plana çıkmadı)
- **TikTok / X:** Tespit edilmedi

**Bizim açımızdan:** Eğer Raven'da bu kanallarda boşluk varsa, **özellikle Instagram + LinkedIn** öncelikli olmalı.

### 4.5 Hakkımızda / About

`/hakkimizda` benzeri bir sayfa search'te doğrudan listelenmedi, ama medya kaynaklarından çıkan **şirket hikâyesi**:

- **Kuruluş:** 2021
- **Kurucu ortak:** Mustafa Kemal Bilgiç
- **Misyon:** "Bir dental ürün, diş deposundan çıkıp Anadolu'daki bir diş hekimine ulaşana kadar ortalama 5 kez el değiştirmekte. Bu durum, diş hekimlerinin maliyetini arttırarak, hastaların ağız ve diş sağlığı hizmetlerine ulaşımını zorlaştırıyor."
- **Çözüm:** Aracısız doğrudan depo-hekim eşleştirme
- **Büyüme:** 1 yılda 10× büyüme
- **AI yatırımı:** "Hekimlere tedavi süreçlerinde doğru ürünü seçmelerini sağlayacak yapay zekâ asistanı geliştirme" planı

**Trust sinyali:** Bu hikâye **net, sayısal, problem-çözüm-formatında**. Webrazzi/eGirişim'de düzenli haber çıkıyor olması yatırımcı/medya algısını besliyor.

**Bizim açımızdan:** Raven'ın "Cerrahi Aletler" odaklı, üretici/marka hikâyesi farklı bir frame'de güçlü olabilir — **"Türk imalatı premium cerrahi alet"** vs. **"toplam aracı yok pazaryeri"** = ortogonal pozisyonlar.

### 4.6 Ödeme Yöntemleri

- **Kredi kartı:** Var (taksitli olduğu, banka entegrasyonu olduğu çıkarsanabilir)
- **DP10 ilk alışveriş kupon kodu:** 2.500 TL üst limit, turuncu indirim etiketli ürünlerde geçersiz
- **Kapıda ödeme:** Doğrudan doğrulanmadı, ancak çoğu B2C-vari Türk e-ticaret modelinde standart
- **Havale/EFT/Açık hesap kredi:** Pazaryeri yapısı + kurumsal hekim/depo müşterisi düşünüldüğünde muhtemel ama doğrulanmadı

### 4.7 Kargo ve Teslimat

- **14 gün iade garantisi** ile kalite güvencesi
- "Ücretsiz iade kargo" (satılabilir kaldığı sürece)
- "Hızlı teslimat" iddiası — net süre verilmemiş
- Yatırım stratejisi: "Lojistik kaynaklı sorunları çözmek" → mevcut bir zayıflığın kabulü

---

## 5. Bizim Açımızdan Fırsat Boşlukları (Raven Dental için)

### 5.1 Kaçırdığımız (Onlarda Var, Bizde Yok / Zayıf)

#### A. Sözlük / Glossary İçerik Silahı
`/sozluk` ve `/sozluk/{terim}` yapısı. **A-Z dental terminoloji**, her terim ayrı URL.
- **Etki:** Informational long-tail trafiği (sektör + öğrenci + asistan hekim) doğrudan domain'e gelir.
- **Maliyet:** 200-500 terim × 300-500 char açıklama = bir uzmanın 2-3 haftalık iş yükü.
- **Öneri:** `ravendentalgroup.com/sozluk` veya `/terimler` altında, **özellikle cerrahi alet odaklı** glossary (forseps, elevatör, küret, retraktör, raspatör, hook, ron jör, periost elevatörü, kemik makası, çekme penseti). Bizim kategori expertise'imiz ile **niche-but-deep** yaklaşım.

#### B. Native Mobil Uygulamalar (Çift Uygulama Stratejisi)
**Hekim uygulaması** + **Depo paneli uygulaması** ayrımı = role-based UX. Bizde **app yok**.
- **Etki:** Retention, push notification, tekrar satın alma, mobil teklif istek akışı.
- **Maliyet:** Yüksek (3-6 ay React Native veya Flutter geliştirme).
- **Alternatif:** **PWA (Progressive Web App)** ile Journal3 üzerinde hızlı kazanım (manifest.json, service worker, add-to-homescreen) — 1-2 haftalık iş.

#### C. Şeffaf Fiyatlandırma
"Fiyatları ve Çeşitleri" pattern + login-wall'suz fiyat gösterimi.
- **Etki:** Google'da fiyat rich snippet, organik commercial intent yakalama.
- **Aksiyon:** Eğer Raven'da fiyat şu an login-wall arkasındaysa, **stratejik karar** verilmeli. Premium-marka pozisyonu için "Teklif İste" akışı işe yarayabilir ama o zaman da "Fiyatları ve Çeşitleri" SEO query'lerinden trafik çekemeyiz.

#### D. AggregateRating / Yorum Hacmi
Marketplace ölçeği ile binlerce ürün yorumu → AggregateRating rich snippet → Google sonuçlarında **yıldız + yorum sayısı**.
- **Etki:** CTR avantajı.
- **Aksiyon:** Raven kendi review akışını **aktif promote** etmeli (sipariş sonrası e-posta + WhatsApp). Az ama gerçek, uzman-doktor onaylı yorumlar.

#### E. Sektörel Otorite / Medya PR
Webrazzi, eGirişim, Hibya, Swipeline, Antalya Körfez gibi medyada düzenli yer alma = backlink domain authority.
- **Etki:** Domain Rating, brand mention, referral traffic.
- **Aksiyon:** Raven'ın "Türk imalat cerrahi alet" hikâyesini sektör medyasında konumlandır (Dünya Gazetesi sektör ekleri, Capital, dental B2B medyası, ihracat hikâyesi varsa Ekonomim/Bloomberg HT).

#### F. AI / Akıllı Ürün Önerisi
Dentalpiyasa "yapay zekâ asistanı" yatırımı duyuruyor. Bu, **2026 trend** olacak.
- **Aksiyon:** OpenCart 3 + Journal3 üzerinde **"Ürün ailesi karşılaştırma" + "Cerrahi prosedüre göre alet seti öneri"** modülü → niş ama derin AI use case.

#### G. Çift-Taraf Marketplace Mantığı
Şu an doğrudan rakip değil, çünkü model farklı. **Ama:** Raven kendi cerrahi alet markasını **Dentalpiyasa içinde vendor olarak** listeletmeli mi? Bu, dağıtım kanalı + brand visibility için kritik bir stratejik karar.
- **Aksiyon:** Dentalpiyasa vendor başvurusu yap, komisyon oranını öğren, ROI hesabı çıkar. Listing yapıp **kendi sitemize backlink** + **brand awareness** alabiliriz.

### 5.2 Bizim Üstün Olduğumuz (Bizde Var, Onlarda Yok / Zayıf)

#### A. Kategori Açıklama Derinliği
Bizim **18 kategori × 1450 char zengin açıklama** → Dentalpiyasa kategori açıklamalarının yaklaşık 2-3 katı.
- **Aksiyon:** Bu avantajı **agresif tekrar pazarlama**, sitemap önceliği high, kategori H1+intro birinci scroll'da görünür.

#### B. Tutarlı Schema.org Disiplini
Product + Breadcrumb + Organization + contactPoint + WebSite — **4 blok**. Marketplace yapıları offer-array, multi-seller karmaşıklığı ile bu disiplini koruyamaz.
- **Aksiyon:** Schema'ya **ImageObject + VideoObject + HowTo** (cerrahi alet kullanım rehberi) ekleyerek farkı genişlet.

#### C. hreflang TR + x-default
İhracat ve uluslararası satışa altyapı hazır. Dentalpiyasa hâlâ TR-only.
- **Aksiyon:** EN/AR/RU subfolder + ürün catalog çevirisi → MENA ve BDT pazarlarına açılım.

#### D. Marka Tutarlılığı (Raven Cerrahi Aletler)
Tek-vendor + kendi marka = **brand equity** birikimi. Dentalpiyasa'nın 150.000+ ürünü vendor karmaşası içinde marka değil "stok".
- **Aksiyon:** "Raven" markasını ürün adı + meta + Schema brand alanında **agresif tekrar**.

#### E. Niş Derinlik
Cerrahi el aletleri özelinde 738 SEO URL, Dentalpiyasa'nın aynı kategorideki listing'inden muhtemelen daha **derin keyword coverage**. Onların 488 elavatör listing'i geniş ama vendor varyasyonu; bizimkiler kuratlı.

#### F. UX Sadeliği
Marketplace'in vendor switch'i, multi-cart, çoklu kargo karmaşası yok. Tek-vendor checkout = düşük friction.

### 5.3 Kelime Örtüşmesi (Keyword Overlap)

Doğrulanmış örtüşen anchor-kategoriler:
- **cerrahi el aletleri** (`dentalpiyasa.com/cerrahi-el-aletleri`)
- **ortodonti el aletleri**
- **tedavi el aletleri**
- **diğer el aletleri**
- **diğer cerrahi aletler**
- **penset**
- **periost elevatörü**
- **elavatör / elevatör** (tipo varyasyonu)
- **kemik forcepsi**
- **artikülatörler**
- **setler**
- **aksesuar**

Olası ürün-seviye örtüşme: forseps modelleri, küret, raspatör, periost elevatörü, hook, ron jör, retraktör, scaler, kemik makası, ekartör, anestezi enjektörü.

**Stratejik aksiyon:**
- Bu **head-term** Türkçe keyword'lerde Dentalpiyasa ölçeği avantajı yenilemesi zor.
- Biz **modifier'lı long-tail** ile rekabet etmeli: "raven elevatör", "alman çelik forseps", "premium cerrahi alet seti", "implant cerrahi alet set fiyatları", "{prosedür adı} alet seti".
- Marka-modifier'sız ana terimde Dentalpiyasa zaten organik birinci sıralarda olacak; biz **ürün özelliği + uzmanlık** modifier'ları ile niş trafik.

### 5.4 Stratejik Pozisyonlama Önerisi

**Üç-katmanlı strateji:**

1. **Defansif:** Sözlük + native app + AggregateRating ile teknik gap'i kapat
2. **Ofansif:** Marka hikâyesi (Türk imalat premium cerrahi alet) + ihracat hreflang + uzman-doktor video içerik
3. **Hibrit:** Dentalpiyasa'da vendor olarak listele, kendi sitemize **mid-funnel** trafik çek; uzun vadede SEO + retention kendi site'de büyüsün

---

## 6. Sayısal Özet Tablosu

| Boyut | Dentalpiyasa | Raven Dental |
|---|---|---|
| Kuruluş | 2021 | (referansta belirtilmedi) |
| Model | Multi-vendor marketplace | Tek-vendor üretici/perakende |
| Toplam ürün | 150.000+ (kümülatif) | ~ (referansta yok) |
| Indexlenebilir SEO URL | Tahmini 150.000+ | 738 |
| Kategori (kök) | ~20-30 (gözlem) | 18 |
| Kategori açıklama uzunluğu | ~300-600 char (tahmini) | **1450 char** |
| Kategori title pattern | "X Fiyatları ve Çeşitleri - Dentalpiyasa" | (referansta yok, varsa benzeri olmalı) |
| Schema bloğu | Product+Offer (vendor) muhtemel | **4 blok** (Product+Breadcrumb+Org+ContactPoint+WebSite) |
| Sitemap | Erişilemedi (tahmini büyük + index'li) | Hiyerarşik (referansta) |
| Robots.txt | Erişilemedi | (referansta yok) |
| HTTPS / HSTS / Secure cookie | Erişilemedi (muhtemel evet) | **Evet** |
| hreflang | Yok / TR-only | **TR + x-default** |
| Mobil web | Responsive (tahmini) | viewport-fit + theme-color (referansta) |
| Native iOS app | **Evet** (id6742653846) | **Yok** |
| Native Android app | **Evet** (`com.dentalpiyasa.mobileapp`) | **Yok** |
| Vendor paneli app | **Evet** (`com.dentalpiyasa.depo`) | N/A |
| Sözlük / Glossary | **Evet** (`/sozluk`) | **Yok** |
| Blog | Tespit edilmedi | (referansta yok) |
| Yorum / Review | Marketplace ölçeği (binlerce muhtemel) | OpenCart standart (varsa) |
| WhatsApp | Doğrulanmadı | (referansta yok) |
| Sosyal medya | IG + LinkedIn + FB (YouTube zayıf) | (referansta yok) |
| Yatırım | 5M USD pre-Series A (Ekim 2024) | N/A |
| Şehir / Ofis | İstanbul İTÜ Arı Teknokent | (referansta yok) |
| Telefon | 0850 346 3368 | (referansta yok) |
| Fiyat şeffaflığı | **Açık** (login-wall yok) | (referansta yok) |
| Marka | Pazaryeri (vendor markaları) | **Raven Cerrahi Aletler** (kendi marka) |
| AI ürün önerisi | Yatırım açıklandı (yol haritasında) | (referansta yok) |

---

## 7. Erişilemedi / Doğrulanamadı Notları

Aşağıdaki noktalar **WebFetch ortam kısıtlamaları** nedeniyle doğrudan doğrulanamadı; sadece WebSearch snippet/metadata + 3. parti haber kaynakları kullanıldı:

- Doğrudan HTML kaynak kod (title tag tam içeriği, meta description, OG tag, Twitter card, viewport, theme-color)
- `robots.txt` ham içeriği
- `sitemap.xml` URL sayısı ve hiyerarşisi
- HTTP yanıt başlıkları (Strict-Transport-Security, Content-Security-Policy, X-Content-Type-Options, Referrer-Policy)
- SSL sertifika detayı (issuer, expiry)
- Sayfa HTML weight, request sayısı, LCP/FID/CLS Core Web Vitals
- Schema.org JSON-LD blok yapısı (varlığı muhtemel, içeriği doğrulanmadı)
- Tam kategori sayısı, vendor sayısı
- Backlink profili (Ahrefs/SEMrush DR/UR/Referring Domains)
- SimilarWeb trafik hacmi, organic vs. direct dağılımı
- Google Trends marka aram trendi
- YouTube kanal varlığı ve abone sayısı
- WhatsApp Business numarası
- Tam ödeme yöntemleri (kapıda ödeme, açık hesap)
- Sertifika rozetleri (TSE, CE, ISO, ETBİS rozeti)

Bunlar, kullanıcının canlı tarayıcısı / curl yetkisi olan bir ortamda **15 dakikalık ek tarama** ile doğrulanabilir.

---

## 8. Kaynaklar

- [Dentalpiyasa Anasayfa](https://dentalpiyasa.com/)
- [Dentalpiyasa İletişim](https://dentalpiyasa.com/iletisim)
- [Dentalpiyasa Sözlük](https://dentalpiyasa.com/sozluk)
- [Dentalpiyasa Hekim Üyelik Sözleşmesi](https://dentalpiyasa.com/hekim-uyelik-sozlesmesi)
- [Dentalpiyasa Cerrahi El Aletleri](https://dentalpiyasa.com/cerrahi-el-aletleri)
- [Dentalpiyasa Kemik Forcepsi](https://dentalpiyasa.com/kemik-forcepsi)
- [Dentalpiyasa Elavatör](https://dentalpiyasa.com/elavator)
- [Dentalpiyasa Periost Elevatörü](https://dentalpiyasa.com/periost-elevatoru)
- [Dentalpiyasa Penset](https://dentalpiyasa.com/penset)
- [Dentalpiyasa Ortodonti El Aletleri](https://dentalpiyasa.com/ortodonti-el-aletleri)
- [Dentalpiyasa İmplantoloji](https://dentalpiyasa.com/implantoloji)
- [Dentalpiyasa İmplant Motorları](https://dentalpiyasa.com/implant-motorlari)
- [Dentalpiyasa SSS - Kampanya](https://dentalpiyasa.com/sikca-sorulan-sorular/kampanya)
- [Dentalpiyasa Sözlük - Loupe](https://dentalpiyasa.com/sozluk/loupe)
- [Dentalpiyasa LinkedIn](https://www.linkedin.com/company/dentalpiyasa/)
- [Dentalpiyasa Instagram](https://www.instagram.com/dentalpiyasa/)
- [Dentalpiyasa Android App](https://play.google.com/store/apps/details?id=com.dentalpiyasa.mobileapp&hl=en_US)
- [Dentalpiyasa iOS App](https://apps.apple.com/tr/app/dentalpiyasa/id6742653846)
- [Dentalpiyasa Depo Paneli Android App](https://play.google.com/store/apps/details?id=com.dentalpiyasa.depo&hl=tr)
- [Webrazzi - 5M USD Değerleme Yatırım Haberi](https://webrazzi.com/2024/10/14/dentalpiyasacom-5-milyon-dolar-degerleme-ile-yatirim-aldi/)
- [eGirişim - Yatırım Haberi](https://egirisim.com/2024/10/14/dental-urun-pazaryeri-dentalpiyasa-5-milyon-dolar-degerleme-uzerinden-yatirim-aldi/)
- [Hibya Haber Ajansı - Dentalpiyasa Maliyet Azaltma](https://hibya.com/dentalpiyasacom-dis-hekimlerini-ve-depolari-bulusturarak-maliyetleri-azaltiyor-395883)
- [Swipeline - Yatırım Haberi](https://swipeline.co/dental-urun-pazaryeri-dentalpiyasa-5-milyon-dolar-degerlemeyle-yatirim-aldi/)
- [Şikayetvar - DentalPiyasa Profili](https://www.sikayetvar.com/dentalpiyasa)

---

*Rapor sonu. Analiz, ortam kısıtlamaları (WebFetch erişimsiz) nedeniyle WebSearch + 3. parti medya snippet'leri ile sınırlı; canlı browser doğrulaması önerilir.*
