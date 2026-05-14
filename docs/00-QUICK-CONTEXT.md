# 00 - Quick Context (Son Durum)

> **Son güncelleme:** 2026-05-12
> Her session başında HER ZAMAN bunu oku.

## Site Şu An Çalışıyor mu?

✅ **EVET** — https://ravendentalgroup.com 200 OK, normal hâli (~462 KB)

Test URL'ler:
- `/` → 200 ✓
- `/sitemap.xml` → 200 (369 URL içeriyor) ✓
- `/diagnostik-aletleri` → 200 ✓ (TR temiz URL)
- `/admin/` → 200 ✓
- `/implant-kemik-frezi-drill` → 200 ✓ (ürün)

## Nerede Kaldık?

**Tamamlanan iş:**
1. ✅ Yerel kopya alındı (3565 dosya, ~70 MB)
2. ✅ DB dump alındı (schema + SEO tabloları)
3. ✅ Git repo başlatıldı + GitHub'a push
4. ✅ Dokümantasyon yapısı kuruldu
5. ✅ **QNB Pay security audit** + 3 patch hazırlandı (`analysis/qnb-patches/`)
6. ✅ **Theme OCMOD patch** hazırlandı (H1 + og:title, `analysis/theme-patches/`)
7. ✅ **4 paralel agent ile derin kod incelemesi** — 168 bulgu (`analysis/code-review/`)
8. ✅ **Code Review Executive Summary** — `docs/15-CODE-REVIEW-SUMMARY.md`
9. ✅ **6 rakip + sentez + 90-günlük aksiyon planı** (`analysis/competitors/`)
10. ✅ **Performance patches** 01-03 (indexes, InnoDB, Twig) — `analysis/performance-patches/`
11. ✅ **Security patches** C1, C3, C5 — `analysis/security-patches/`
12. ✅ **SEO patches** 04 (sitemap hierarchy) + 05 (18 kategori uzun TR açıklaması) — `analysis/seo-patches/`
13. ✅ **Footer sosyal medya temizliği** (FB, IG×2; Twitter kaldırıldı) + ödeme ikonları (sadece Visa+MC) + Organization schema `sameAs` (yerel Docker) — `analysis/seo-patches/06-footer-social-cleanup/`
14. ✅ **WhatsApp Business floating widget** (sağ-alt, +90 552 853 03 99) (yerel Docker) — `analysis/seo-patches/07-whatsapp-widget/`
15. ✅ **Header notice + cookie notification** modülleri kapatıldı (yerel Docker) — `analysis/seo-patches/08-disable-notice-modules/`
16. ✅ **18 kategori SEO açıklaması** yeniden yazıldı (üretici pozisyonu, marka isimleri yok) (yerel Docker) — `analysis/seo-patches/09-category-descriptions-rewrite/`
17. ✅ **og:title kategori/ürün** sayfalarında `meta_title` (OCMOD BLOK N + O) — `analysis/theme-patches/raven.ocmod.xml` v1.1
18. ✅ **Site geneli Türkçeleştirme** — 100+ İngilizce string TR'ye (yerel Docker) — `analysis/seo-patches/10-turkish-i18n/`
19. ✅ **Slider + banner alt text** Türkçe açıklamalarla dolduruldu (10 görsel) (yerel Docker) — `analysis/seo-patches/11-image-alts/`
20. ✅ **docs/16-GOOGLE-SEO-RULES.md** — Google'ın resmi SEO kuralları referans dokümanı (10 bölüm, 4600+ word, 2026-05-12 güncel)

**Şu an:**
- ✅ **TÜM SEO PATCH'LERİ ÜRETİME UYGULANDI (2026-05-12)** — bkz: O17 in `08-CHANGES-MADE.md`
- Patch script'leri `analysis/seo-patches/01-11`, deploy script'leri `deploy/`
- Production-ready hâlde, GSC + Bing + Yandex sitemap submit'ine hazır
- VPS henüz alınmadı (taşıma daha sonra)

**Sıradaki (action-plan'dan):**
1. AggregateRating schema (önce gerçek müşteri yorumu akışı kurulmalı — şimdi sahte rating ekleyemeyiz)
2. İlk blog makalesi: "Endodonti Eğesi Seçimi" (5K aylık arama)
3. Glossary altyapısı (`/sozluk`, 20 başlangıç terim)
4. H1/og:title journal3 fallback sorunu (header.twig hardcode)
5. Lighthouse baseline + VPS migration

→ Detaylı plan: [12-ROADMAP.md](./12-ROADMAP.md)

## Üretim Erişim Durumu

| Erişim | Var mı | Yöntem |
|---|---|---|
| cPanel UI | ✅ | NetInternet SSO ile |
| cPanel API | ✅ | API token (`~/.config/raven/env`) |
| SSH | ❌ | Shared paket, noshell account |
| FTP | ✅ port 21 | Plaintext, kullanmadık |
| DB (phpMyAdmin) | ✅ cPanel'den | Manuel |
| DB (programmatic) | ✅ PHP runner | Geçici script, kullandıktan sonra sil |
| OpenCart admin | ✅ | https://ravendentalgroup.com/admin/ |
| GitHub repo | ✅ | https://github.com/cihatdegirmenci-jpg/raven-dental |

## Şu An Aktif Hassas Bilgi

`~/.config/raven/env` dosyasında (yerel, 600 izinli):
- cPanel API token
- DB user şifresi (alfanümerik 32 char)
- (Aktif runner varsa) runner name + token

`~/.ssh/raven_id_rsa` (kullanılmadı, SSH bloklu çünkü)

## Bu Oturumda Yapılan Ana Değişiklikler

→ Tam liste: [08-CHANGES-MADE.md](./08-CHANGES-MADE.md)

Kısa özet:
- 🔒 admin.zip (852 MB) + toptandetal/ + error_log silindi
- 🔒 admin + DB şifreleri rotate
- 🔒 .htaccess hardening (gzip, cache, headers, file protection, OpenCart rewrite)
- 🔍 robots.txt fix
- 🔍 738 SEO URL keyword (TR+EN, 369 entity × 2 dil)
- 🔍 Sitemap.xml aktif (369 URL)
- 🔍 Anasayfa + 18 kategori meta yazıldı
- 🔍 ~283 ürün meta_description şablonu
- 🔍 6 demo manufacturer silindi
- 🔍 Journal3 customCodeHeader: hreflang + Organization schema
- ⚠️ Header.twig'e H1 fallback chain yazıldı (henüz tam çalışmıyor — j3.settings.get config_name fallback yapıyor)

## Açık Kalan Sorunlar

| | Detay |
|---|---|
| ✅ **H1 düzeldi** | Anasayfa ve tüm kategori/ürün sayfalarında meta_title değeri H1 olarak render ediliyor (2026-05-12, çözüldü) |
| ✅ **og:title düzeldi** | Anasayfa + kategori + ürün → meta_title öncelikli. OCMOD BLOK N + O ile (2026-05-12, çözüldü) |
| ✅ **GSC doğrulandı** (2026-05-12) | Domain property, DNS TXT yöntemi. Sıradaki: sitemap submit + URL inspection |
| ⚠️ **GA4 yok** | Kullanıcının kararı: "sonra bakarız" — GSC yapıldı, GA4 hâlâ açık |
| ⚠️ **QNB Pay güvenlik review** | bolkarco'nun raporladığı: CSRF, webhook güvenliği, IDOR şüphesi (rolled into 06-SECURITY-STATUS.md) |
| ⚠️ **Türkçe alt text** | 39 görselin 9'unda alt boş, kalanı dosya adı |

## Aktif Çalışma Modu

**🟢 Yerel analiz + dokümantasyon modu**

Üretime dokunmuyoruz. Tüm planlamayı `~/raven-dental/` içinde yapıyoruz. Sonunda VPS taşıma adımıyla tertemiz uygulayacağız.
