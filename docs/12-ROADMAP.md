# 12 - Roadmap (Yapılacaklar)

> Öncelik sırasına göre. `[x]` = tamamlandı, `[ ]` = yapılacak, `[~]` = devam ediyor.
> Yeni iş eklerken öncelik seviyesine göre yerleştir.

---

## ✅ Faz 0: Acil Güvenlik & SEO Temeli (Tamamlandı)

- [x] Public admin.zip silindi (852 MB)
- [x] toptandetal/ + Arsiv.zip silindi  
- [x] error_log dosyaları silindi
- [x] Admin şifresi rotate (kullanıcı)
- [x] DB şifresi rotate (otomatik, 2 kez)
- [x] .htaccess hardening (security + perf headers)
- [x] robots.txt düzeltildi
- [x] Sitemap.xml aktif (369 URL)
- [x] 738 SEO URL keyword DB'ye yüklendi
- [x] OpenCart SEO URL rewrite kuralları
- [x] Anasayfa + 18 kategori meta yazıldı (TR)
- [x] ~330 ürün meta_title/description şablon
- [x] Demo manufacturer'lar silindi (6 adet)
- [x] hreflang TR/EN + zenginleştirilmiş Organization schema
- [x] Yerel kopya + DB dump + Git repo

---

## 🟡 Faz 1: Yerel Analiz & Dokümantasyon (Şu an — devam ediyor)

- [x] CLAUDE.md yazıldı
- [x] docs/00-QUICK-CONTEXT.md
- [x] docs/08-CHANGES-MADE.md
- [x] docs/09-LESSONS-LEARNED.md
- [x] docs/12-ROADMAP.md (bu dosya)
- [ ] docs/01-PROJECT-OVERVIEW.md — proje detay tanımı
- [ ] docs/02-ARCHITECTURE.md — sistem mimarisi diyagramı
- [ ] docs/03-DATABASE-SCHEMA.md — DB tabloları + kritik kolonlar
- [ ] docs/04-THEME-STRUCTURE.md — Journal3 internals, OCMOD detayı, header.twig haritası
- [ ] docs/05-SEO-STATUS.md — şu anki SEO durumu detaylı
- [ ] docs/06-SECURITY-STATUS.md — güvenlik analizi + QNB Pay review
- [ ] docs/07-PERFORMANCE.md — Lighthouse baseline + öneri
- [ ] docs/10-WORKING-RULES.md — günlük çalışma akışı
- [ ] docs/11-MIGRATION-PLAN.md — VPS taşıma plan
- [ ] docs/13-COMPETITOR-ANALYSIS.md — TR diş aleti pazarı
- [ ] docs/14-CONTENT-PLAN.md — meta + blog içerik takvimi
- [ ] analysis/file-tree.txt — proje dosya ağacı
- [ ] analysis/lighthouse-baseline.json — şu anki Lighthouse skoru
- [ ] analysis/seo-snapshot.json — şu anki SEO snapshot

---

## 🟠 Faz 2: Detaylı Yerel İnceleme (Faz 1 bitince)

### QNB Pay Modülü Güvenlik Audit
- [ ] `catalog/controller/extension/payment/qnbpay.php` (38 KB) review
- [ ] `system/library/qnbpay.php` (38 KB) review
- [ ] Webhook endpoint URL'i + HMAC doğrulama var mı?
- [ ] CSRF koruması — payment form'da token var mı?
- [ ] Input validation — kart bilgisi gönderme akışı
- [ ] IDOR — kullanıcı başkasının sipariş bilgilerine erişebilir mi?
- [ ] PoC'ler hazırla (bolkarco'nun iddialarını test et)
- [ ] Findings → docs/06-SECURITY-STATUS.md

### Theme Code Review
- [ ] Header.twig H1 sorunu nihai çözüm (j3.settings bypass)
- [ ] og:title sorunu — Journal3 meta_tags controller'a bak
- [ ] Lazy loading eklenebilir mi (Journal3 image macro'su)
- [ ] Twitter card uyumsuz boyut düzeltme
- [ ] Görsel alt text tutarlılık (39 görsel, 9'u boş)

### DB Optimization
- [ ] Index review — yavaş sorgu var mı (slow log)
- [ ] oc_session tablo şişmiş mi (eski session'lar)
- [ ] oc_customer + oc_address — gerçek müşteri sayısı?
- [ ] oc_order — son 30 gün sipariş analizi

---

## 🟢 Faz 3: Lighthouse + Rakip Analizi

- [ ] Lighthouse desktop baseline al (anasayfa)
- [ ] Lighthouse mobile baseline al (kritik — Google mobile-first index)
- [ ] LCP, CLS, FID/INP skorları kayda al
- [ ] Bekleyen hızlandırma fırsatları listele
- [ ] Rakip analizi: TR diş aleti satıcıları
  - dentaltedarik.com, ucalsis.com, dentaltop.com, dentamed.com.tr, vb.
  - Onların: title yapısı, schema kullanımı, fiyatlama, vitrin
  - docs/13-COMPETITOR-ANALYSIS.md'ye yaz

---

## 🔵 Faz 4: VPS Migration

- [ ] NetInternet SSD VDS III satın al ($17.50/ay önerilen)
- [ ] Sipariş notunda: Ubuntu 22.04 LTS, panelsiz, root SSH port 22
- [ ] Server bilgilerini al, ~/.config/raven/env'e ekle
- [ ] Yeni VPS'e SSH ile bağlan
- [ ] Server hardening (ufw, fail2ban, auto-updates, SSH key only)
- [ ] LEMP kurulum (Nginx veya OpenLiteSpeed + MariaDB + PHP 8.2 + Redis + Composer)
- [ ] OpenCart için optimize ayarları (php-fpm pool, MariaDB tuning, OPcache, Redis)
- [ ] Test domain üzerinden taşıma denemesi (subdomain'le)
- [ ] migration-plan/server-bootstrap.sh script
- [ ] migration-plan/deploy.sh script
- [ ] Mevcut site rsync ile yeni VPS'e kopyala
- [ ] DB mysqldump + import
- [ ] config.php yeni server'a göre güncelle
- [ ] SSL: Let's Encrypt + certbot
- [ ] Test: tüm URL'ler 200 OK?
- [ ] DNS switch: GoDaddy A record yeni IP'ye
- [ ] DNS yansıma sonrası yeni site canlı
- [ ] Eski shared hosting 7-10 gün paralel tut (geri dönüş için)
- [ ] Eski hosting iptali

---

## 🟣 Faz 5: VPS Sonrası Optimizasyon (Yeni Sunucuda)

### Performans
- [ ] OPcache + Redis cache OpenCart için aktif
- [ ] Image optimization: imagemagick + WebP üretimi
- [ ] HTTP/3 + Brotli compression
- [ ] CDN değerlendirme (Cloudflare ücretsiz veya BunnyCDN $5/ay)

### Monitoring
- [ ] Netdata kurulum
- [ ] Uptime Kuma (kendi domain'inde)
- [ ] Email alert kuralları

### Backup
- [ ] Günlük tam backup script
- [ ] Uzak depo: S3-uyumlu (Hetzner Storage Box, Backblaze B2)
- [ ] Restore test (yeni instance'ta dump'tan)

### Süreklilik
- [ ] CI/CD: GitHub → VPS deploy script (manuel veya otomatik)
- [ ] Cron: OpenCart cache sweep, sitemap regen
- [ ] Log rotation
- [ ] Otomatik security update + restart

---

## ⏭️ Faz 6: SEO Sonraki Adımlar

- [ ] Google Analytics 4 kur (kullanıcı kararı: sonra)
- [ ] Google Search Console doğrulama + sitemap submit
- [ ] Bing Webmaster Tools
- [ ] Yandex Webmaster (TR pazar için ek)
- [ ] Schema.org Product enrichment (review aggregateRating, brand, gtin/mpn doldur)
- [ ] Schema.org BreadcrumbList kategori sayfasına da eklenmeli (şu an sadece ürün)
- [ ] OG image özelleştirme (her sayfa için)
- [ ] LocalBusiness schema (adres + harita varsa)
- [ ] Blog'a başla — `/journal3/blog` aktif ama içerik yok
  - "Endodonti aletleri seçimi" tarzı 10 makale
  - Her makale → uzun-kuyruk anahtar kelime hedefler

---

## 🌐 Faz 7: İçerik & Pazarlama

- [ ] Ürün açıklamaları (description) dolduralım (şu an 0/18 kategori description)
- [ ] Ürün resimleri optimize (WebP, lazy load, retina @2x)
- [ ] Müşteri yorumları aktivasyonu
- [ ] Blog editör akışı (kullanıcı arabirimi)
- [ ] Newsletter integrasyon (Mailchimp/Sendinblue)
- [ ] WhatsApp Business entegrasyon
- [ ] Sosyal medya hesapları (yoksa) — Facebook, Instagram

---

## Önceliklendirme Mantığı

- 🔴 Acil: Üretim down riski veya yasal/güvenlik
- 🟠 Yüksek: SEO/UX'i kazandıracak büyük etki
- 🟡 Orta: Önemli ama kritik değil
- 🟢 Düşük: Nice-to-have

Bu doc her session'da `00-QUICK-CONTEXT.md` ile birlikte okunur.
