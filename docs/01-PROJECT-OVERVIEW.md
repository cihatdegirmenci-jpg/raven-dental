# 00 - Proje Genel Görünüm

## Site profili

| | |
|---|---|
| **Domain** | ravendentalgroup.com |
| **Sektör** | B2B Diş Hekimliği Aletleri ve Cerrahi Ekipmanlar |
| **Pazar** | Türkiye + sınırlı uluslararası |
| **Diller** | Türkçe (birincil), İngilizce |
| **Para birimi** | TRY |
| **Platform** | OpenCart 3.0.3.8 |
| **Tema** | Journal3 v3.1.12 |
| **Ürün sayısı** | 345 aktif ürün |
| **Kategori sayısı** | 18 (4 ana + 14 alt) |
| **Bilgi sayfası** | 6 |

## Hosting (mevcut)

| | |
|---|---|
| **Sağlayıcı** | NetInternet (ni.net.tr) |
| **Sunucu** | host110.netinternet.com.tr |
| **IP** | 95.173.190.138 (paylaşımlı) |
| **Plan** | Profesyonel (shared, CloudLinux LVE) |
| **Web server** | LiteSpeed |
| **PHP** | 7.4.33 (EOL ⚠️) |
| **MySQL** | 8.0.46 |
| **Apache** | 2.4.67 (LiteSpeed front-end için) |
| **Disk** | 20 GB |
| **SSH** | YOK ❌ (noshell account) |

## Domain & DNS

| | |
|---|---|
| **Registrar** | GoDaddy |
| **Nameservers** | NS27/NS28.DOMAINCONTROL.COM (GoDaddy) |
| **DNS yönetimi** | GoDaddy paneli üzerinden |

## Diğer notlar

- Aynı hosting hesabında `heimloo.com/` adlı boş bir klasör var — başka marka için ayrılmış, henüz site yok.
- Yapılan ödeme modülü entegrasyonu: **QNB Pay** (bolkarco geliştiriciye yaptırılmış)
- Bolkarco daha önce güvenlik tespitleri raporlamış (CSRF, webhook, IDOR endişeleri) — detay docs/06-guvenlik-durumu.md'de

## Hedefler (kullanıcı tarafından bu oturumda belirlendi)

1. **SEO** — Anahtar kelime hedefleme, temiz URL, meta veriler ✓ (büyük kısmı yapıldı)
2. **Performans** — Sayfa hızı iyileştirme (kısmen yapıldı, Lighthouse henüz yok)
3. **Rakip analizi** — TR pazarındaki rakip diş aleti satıcıları (henüz yok)
4. **VPS migration** — Shared hosting'den NetInternet KVM VPS'e taşıma (planlama aşamasında)
5. **GA4 + GSC** — Sonra yapılacak

## Bu oturumda yapılan tüm değişiklikler

→ Detay: [05-yapilan-degisiklikler.md](05-yapilan-degisiklikler.md)
