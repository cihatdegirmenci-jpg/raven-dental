# Raven Dental — Yerel Analiz ve Migration Planı

**Site:** https://ravendentalgroup.com
**Platform:** OpenCart 3.0.3.8 + Journal3 Theme
**Sahip:** Raven Dental Group (Türkiye, B2B diş hekimliği aletleri)
**Bu repo:** Üretim sitesinin yerel kopyası + analiz + yeniden mimari planı.

---

## Bu repo neden var?

Site üretimde "shared hosting" üzerinde çalışıyor (NetInternet). API erişimi var, SSH yok. Üretim üzerinde doğrudan değişiklik yaparken birkaç hata yapıldı (cache klasörü yanlışlıkla silindi, modification refresh gerekti). Daha güvenli iş akışı için:

1. **Tüm proje yerelde** (`code/`)
2. **DB dump yerelde** (`db/`)
3. **Her şey markdown ile belgelenmiş** (`docs/`)
4. **Tüm düzeltmeler önce yerelde planlanır, sonra üretime uygulanır**
5. **Sonunda yeni VPS'e tertemiz taşınır**

---

## Klasör yapısı

```
raven-dental/
├── code/                  ← OpenCart kodu (theme, custom controller, XML mods)
│   ├── admin/             (custom OpenCart admin dosyaları)
│   ├── catalog/           (frontend - theme, controller, model, language)
│   ├── system/            (framework + Journal3 core)
│   ├── image/             (sadece logo + small assets — product images yok)
│   ├── config.php.SAMPLE  (config örneği, gerçek şifre yok)
│   ├── robots.txt         (bizim düzelttiğimiz)
│   └── _dotfile_htaccess  (bizim hardening yaptığımız .htaccess)
│
├── db/                    ← MySQL dump'ları
│   ├── schema.sql         (sadece şema, veri yok)
│   ├── seo_tables.sql     (oc_seo_url, oc_category_description, oc_product_description)
│   └── full.sql.gz        (tam dump - git'e push edilmez)
│
├── docs/                  ← Tüm analiz ve plan dokümanları
│   ├── 00-overview.md           Proje üst seviye özeti
│   ├── 01-arsitektur.md         Mimari haritası
│   ├── 02-tema-yapisi.md        Journal3 yapısı
│   ├── 03-db-semasi.md          DB şema referansı
│   ├── 04-mevcut-seo.md         Mevcut SEO durumu (bu oturumda iyileştirilen)
│   ├── 05-yapilan-degisiklikler.md  Bu oturumdaki tüm değişikliklerin listesi
│   ├── 06-guvenlik-durumu.md    Güvenlik analizi
│   ├── 07-performans.md         Lighthouse analiz / öneriler
│   ├── 08-rakip-analizi.md      Türk diş hekimliği aletleri rakipleri
│   ├── 09-roadmap.md            Yapılacaklar (öncelik sırasıyla)
│   └── 10-migration-plan.md     NetInternet VPS'e taşıma planı
│
├── analysis/              ← Otomatik üretilen raporlar, scriptler
│   ├── file-tree.txt
│   ├── lighthouse-baseline.json
│   └── seo-snapshot.json
│
└── migration-plan/        ← VPS taşıma için hazırlıklar
    ├── server-bootstrap.sh    (yeni VPS kurulum scripti)
    ├── deploy.sh              (taşıma scripti)
    └── rollback.sh            (geri dönüş scripti)
```

---

## Bu oturuma kadar yapılanlar (özet)

✅ robots.txt düzeltildi (User-agent satırı eklendi, Sitemap referansı eklendi)
✅ .htaccess hardening (security headers, gzip, browser cache, file protection)
✅ 1.3 GB hassas dosya temizlendi (admin.zip, Arsiv.zip, error_log)
✅ Admin şifresi değiştirildi
✅ DB şifresi rotate edildi
✅ OpenCart SEO URL'leri aktive edildi (yöntem: .htaccess rewrite + DB)
✅ 738 SEO URL keyword eklendi (369 entity × 2 dil)
✅ Sitemap.xml çalışır hale getirildi (369 URL)
✅ Anasayfa + 18 kategori için TR meta title/description yazıldı
✅ 6 demo manufacturer (Apple, Canon, vb.) silindi
✅ Journal3 customCodeHeader'a hreflang TR/EN + zenginleştirilmiş Organization schema eklendi
✅ Tüm aktif ürünlere (~283) şablon meta_description yazıldı

⏭️ Henüz yapılmadı: H1 görünür, GA4 kurulumu, Lighthouse benchmark, rakip analizi, VPS taşıma

---

## Çalışma modeli

1. Yerelde analiz et + dokümante et
2. Her büyük değişiklik için MD dosyasında plan yaz
3. Değişikliği önce git branch'inde dene
4. Onaylanırsa üretime uygula (API ile)
5. Komutları docs/'a not düş
6. **Hata yaparsam:** sadece yerel etkileyebilir, üretim güvende

---

## Üretim sitesine erişim

`~/.config/raven/env` dosyasında saklı (git'e dahil değil):
- cPanel API token
- DB user şifresi
- PHP runner detayları

**Asla bu repoya işlenmemeli.**
