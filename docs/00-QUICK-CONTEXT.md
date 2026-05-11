# 00 - Quick Context (Son Durum)

> **Son güncelleme:** 2026-05-11
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

**Şu an:**
- Yerel analiz tamamlandı
- Tüm bulgular dökümante edildi
- VPS henüz alınmadı, patch'ler henüz üretime uygulanmadı

**Sıradaki:**
1. Frontend agent'ın 13-fix OCMOD'unu `analysis/theme-patches/` ile birleştir
2. Acil 5 Critical bulgu için patch'ler hazırla
3. VPS satın al → test ortamı kur → tüm patch'leri sırayla test et
4. Üretime aktar
5. Lighthouse + rakip analizi (sonra)

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
| ⚠️ **H1 hâlâ "Raven Dental"** | Journal3 j3.settings.get config_name fallback yapıyor, header.twig edit'i çalıştı ama Journal3'ün kendi mantığı override ediyor. Çözüm: header.twig'i daha agresif değiştir veya direkt `<h1>` literal yaz |
| ⚠️ **og:title "Raven Dental"** | Journal3 controller config_name'i kullanıyor, meta_title değil. İzlenecek: `system/library/journal3/opencart/document.php` veya `controller/journal3/seo/meta_tags.php` |
| ⚠️ **GA4 + GSC yok** | Kullanıcının kararı: "sonra bakarız" |
| ⚠️ **QNB Pay güvenlik review** | bolkarco'nun raporladığı: CSRF, webhook güvenliği, IDOR şüphesi (rolled into 06-SECURITY-STATUS.md) |
| ⚠️ **Türkçe alt text** | 39 görselin 9'unda alt boş, kalanı dosya adı |

## Aktif Çalışma Modu

**🟢 Yerel analiz + dokümantasyon modu**

Üretime dokunmuyoruz. Tüm planlamayı `~/raven-dental/` içinde yapıyoruz. Sonunda VPS taşıma adımıyla tertemiz uygulayacağız.
