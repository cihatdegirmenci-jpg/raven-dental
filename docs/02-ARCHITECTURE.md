# 02 - Architecture (Sistem Mimarisi)

## Üst Seviye Şema

```
                    ┌─────────────────────────────────┐
                    │   Kullanıcı (TR Diş Hekimi)     │
                    └────────────┬────────────────────┘
                                 │ HTTPS
                                 ▼
                    ┌─────────────────────────────────┐
                    │  GoDaddy DNS                    │
                    │  (NS27/NS28.DOMAINCONTROL.COM)  │
                    └────────────┬────────────────────┘
                                 │ A record → 95.173.190.138
                                 ▼
                    ┌─────────────────────────────────┐
                    │  NetInternet host110            │
                    │  Shared Hosting (CloudLinux LVE)│
                    │  LiteSpeed front-end            │
                    │  Apache 2.4 worker              │
                    └────────────┬────────────────────┘
                                 │
        ┌────────────────────────┼────────────────────────┐
        ▼                        ▼                        ▼
┌────────────────┐    ┌────────────────────┐    ┌──────────────────┐
│ public_html/   │    │ /home/ravenden/    │    │ MySQL 8.0.46     │
│  - admin/      │    │  storage/          │    │ ravenden_1 DB    │
│  - catalog/    │    │   cache/           │    │  - 164 tablo     │
│  - system/     │    │   modification/    │    │  - oc_setting    │
│  - image/      │    │   logs/            │    │  - oc_product    │
│  - .htaccess   │    │   session/         │    │  - oc_journal3_* │
└────────────────┘    └────────────────────┘    └──────────────────┘
```

## Teknik Stack

### Frontend
- **Template engine:** Twig (Journal3 default)
- **CSS:** Less compiled → CSS (Journal3 build sistemi)
- **JS:** jQuery + Bootstrap 3.4 + Journal3 custom
- **Görsel optimizasyon:** PHP GD (`image/cache/*`)
- **Görsel format:** JPG, PNG (henüz WebP yok)

### Backend
- **Framework:** OpenCart 3.0.3.8 (PHP)
- **PHP versiyon:** 7.4.33 (EOL, VPS'te 8.2'ye yükseltilecek)
- **DB driver:** PDO (`config.php` içinde `DB_DRIVER='pdo'`)
- **Session driver:** file-based (`storage/session/`)
- **Cache driver:** file-based (`storage/cache/`)
- **OCMOD engine:** OpenCart standart modification system

### Tema: Journal3 v3.1.12
- 148 Twig template
- 57 catalog/controller/journal3/*.php controller
- 82 file modification (`system/journal3.ocmod.xml`)
- Setting storage: `oc_journal3_setting`, `oc_journal3_skin_setting`
- Blog modülü: kendi tabloları (`oc_journal3_blog_*`)

### Ödeme Modülü: QNB Pay (özel)
- `catalog/controller/extension/payment/qnbpay.php` (38 KB)
- `system/library/qnbpay.php` (38 KB)
- JS form processing: `qnbpay.js`, `qnbpay-script.js`, `qnbpay-imask.js`
- Geliştirici: bolkarco (3. taraf)
- ⚠️ Güvenlik review beklemede (CSRF, webhook, IDOR — bkz [06-SECURITY-STATUS.md](./06-SECURITY-STATUS.md))

## URL Routing

### SEO URL Akışı
```
İstek: https://ravendentalgroup.com/diagnostik-aletleri
        │
        ▼
Apache/.htaccess RewriteRule:
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^([^?]*) index.php?_route_=$1 [L,QSA]
        │
        ▼
index.php → /catalog/controller/startup/seo_url.php
        │
        ▼
oc_seo_url lookup: keyword='diagnostik-aletleri' AND language_id=2
  → query='category_id=59'
        │
        ▼
OpenCart router: route=product/category, category_id=59
        │
        ▼
CategoryController → render template → HTML response
```

### Özel Routelar
| URL | Hedef |
|---|---|
| `/sitemap.xml` | `index.php?route=extension/feed/google_sitemap` |
| `/googlebase.xml` | `index.php?route=extension/feed/google_base` |
| `/admin/` | OpenCart admin panel |
| `/journal3/blog` | Journal3 blog modülü |

## Önbellek (Cache) Katmanları

```
[Browser cache]      ← 1 yıl (CSS/JS/image, .htaccess Expires)
       │ miss
       ▼
[LiteSpeed cache]    ← Yok (HTML için bypass)
       │
       ▼
[OpenCart storage/cache]  ← DB query cache, language, currency
       │
       ▼
[Journal3 cache]     ← Theme + module render cache
       │
       ▼
[storage/modification] ← OCMOD compiled files (ÖNEMLİ — silme)
       │
       ▼
[Database query]    ← Son katman
```

### Cache lifetime
- Browser: 1 yıl (CSS/JS/image), 0 (HTML)
- OpenCart cache: değişken (en uzun ~1 hafta)
- OCMOD: Theme refresh ile yeniden inşa edilir

## Veri Akışı: Ürün Listesi Sayfası

```
1. Browser → /diagnostik-aletleri
2. .htaccess → index.php?_route_=diagnostik-aletleri
3. OpenCart startup → seo_url lookup → category_id=59
4. controller/product/category.php çağrılır
5. Journal3 OCMOD modifications uygulanır (storage/modification)
6. model/catalog/category.php → oc_category, oc_category_description
7. model/catalog/product.php → oc_product (WHERE category_id=59)
8. Tüm veri → view/template/journal3/template/product/category.twig
9. Header.twig render → schema.org JSON-LD injection (Journal3 controller)
10. customCodeHeader injection (hreflang + custom Org schema)
11. HTML → gzip → browser
```

## Dosya Sistem Yapısı (Özet)

```
/home/ravenden/
├── public_html/             ← Web kök
│   ├── admin/               ← OpenCart admin (PHP + view)
│   ├── catalog/             ← Frontend (controller + model + view)
│   │   └── view/theme/
│   │       ├── default/     ← Vanilla OpenCart tema (fallback)
│   │       └── journal3/    ← Aktif tema
│   ├── system/              ← Framework
│   │   ├── library/journal3/  ← Journal3 framework
│   │   ├── journal3.ocmod.xml ← 82 modification (2175 satır)
│   │   └── modification.xml   ← Core OCMOD wrapper
│   ├── image/               ← Görseller
│   ├── index.php            ← Entry point
│   ├── config.php           ← DB + paths (✗ git'te yok)
│   ├── .htaccess            ← Apache rewrite + güvenlik
│   ├── robots.txt           ← SEO
│   └── php.ini              ← PHP override (varsa)
│
├── storage/                 ← Yazılabilir runtime
│   ├── cache/               ← OpenCart cache (silinebilir)
│   ├── modification/        ← OCMOD compiled (⚠️ SILMEZ)
│   ├── logs/                ← OpenCart log
│   ├── session/             ← Aktif kullanıcı oturumları
│   ├── upload/              ← Müşteri yüklemeleri
│   └── download/            ← Dijital ürün dosyaları
│
└── heimloo.com/             ← Boş klasör (başka marka için ayrılmış)
```

## Dış Bağımlılıklar

| Bağımlılık | Yer |
|---|---|
| Google Fonts | `<link preconnect>` header.twig'de |
| jQuery 1.x | OpenCart vendor |
| Bootstrap 3.4 | OpenCart vendor |
| QNB Pay gateway | API endpoint'i `system/library/qnbpay.php` içinde |
| (Yok) Google Analytics | Henüz kurulmadı |
| (Yok) Google Search Console | Henüz kurulmadı |
| (Yok) CDN | LiteSpeed cache var ama CDN değil |

## Hedef Mimari (VPS Migration Sonrası)

```
                ┌──────────────────────────────────┐
                │ NetInternet KVM VDS III          │
                │ (4 vCPU / 8 GB / 80 GB SSD)      │
                │ Ubuntu 22.04 LTS                 │
                │                                  │
                │  ┌─────────────────────────────┐ │
                │  │ Nginx OR OpenLiteSpeed      │ │
                │  │  + Let's Encrypt SSL        │ │
                │  └────────┬────────────────────┘ │
                │           │                      │
                │  ┌────────▼────────────────────┐ │
                │  │ PHP 8.2 + OPcache + JIT     │ │
                │  └────────┬────────────────────┘ │
                │           │                      │
                │  ┌────────▼────────────────────┐ │
                │  │ MariaDB 10.11               │ │
                │  └─────────────────────────────┘ │
                │  ┌─────────────────────────────┐ │
                │  │ Redis (cache backend)       │ │
                │  └─────────────────────────────┘ │
                │  ┌─────────────────────────────┐ │
                │  │ certbot (SSL renew)         │ │
                │  │ ufw (firewall)              │ │
                │  │ fail2ban (brute-force)      │ │
                │  │ Netdata (monitoring)        │ │
                │  └─────────────────────────────┘ │
                └──────────────────────────────────┘
```

Detay: [11-MIGRATION-PLAN.md](./11-MIGRATION-PLAN.md)
