# 03 - Database Schema (Veritabanı Yapısı)

> **Toplam tablo:** 164 (OpenCart core + Journal3 + QNB Pay)
> **DB engine:** MySQL 8.0.46 (InnoDB)
> **Karakter seti:** utf8mb4 / utf8mb4_general_ci

## Tablo Kategorileri

### OpenCart Core (≈100 tablo)

#### Ürün & Kategori
| Tablo | Satır | Önemli Kolonlar |
|---|---|---|
| `oc_product` | 345 | product_id, model, sku, price, status, manufacturer_id |
| `oc_product_description` | 690 | product_id, language_id, name, description, **meta_title**, **meta_description**, meta_keyword |
| `oc_product_to_category` | 669 | product_id, category_id |
| `oc_product_to_store` | 345 | product_id, store_id |
| `oc_product_image` | ~ | product_id, image, sort_order |
| `oc_product_option` | ~ | product_id, option_id |
| `oc_category` | 18 | category_id, parent_id, image, sort_order, status |
| `oc_category_description` | 36 | category_id, language_id, name, description, **meta_title**, **meta_description**, meta_keyword |
| `oc_category_to_store` | 18 | category_id, store_id |
| `oc_category_path` | ~ | category_id, path_id (kategori hiyerarşi) |

#### SEO & URL
| Tablo | Satır | Önemli Kolonlar |
|---|---|---|
| **`oc_seo_url`** | 738 | seo_url_id, store_id, **language_id**, **query**, **keyword** |
| `oc_url_alias` | (legacy) | OpenCart 2.x kalıntısı, OC 3.x'te oc_seo_url'e migrate olmuş |

**oc_seo_url query formatı:**
```
'category_id=59'     → '/diagnostik-aletleri' (TR)
'category_id=59'     → '/diagnostics' (EN)
'product_id=454'     → '/implant-kemik-frezi-drill' (TR)
'information_id=4'   → '/hakkimizda' (TR)
'manufacturer_id=X'  → '/marka-adi'
```

#### Site Konfigürasyon
| Tablo | Satır | Önemli Kolonlar |
|---|---|---|
| **`oc_setting`** | 213 | setting_id, store_id, code, **key**, **value**, serialized |
| `oc_store` | 0 | store_id, name, url, ssl |
| `oc_language` | 2 | language_id (1=EN, 2=TR), code, status |
| `oc_currency` | 1 | currency_id, code (TRY) |
| `oc_extension` | 47 | type, code (yüklü modüller) |

**Önemli oc_setting key'leri:**
```sql
config_meta_title       -- Anasayfa <title>
config_meta_description -- Anasayfa meta description
config_meta_keyword     -- Anasayfa meta keywords
config_name             -- Site adı ("Raven Dental") — Journal3 H1 fallback
config_seo_url          -- "1" (SEO URL aktif)
config_theme            -- "journal3"
config_template         -- (varsayılan)
journal3_home_h1        -- ⚠️ Bizim eklediğimiz, etkisiz (Journal3 okumuyor)
```

#### Bilgi Sayfaları
| Tablo | Satır | Önemli Kolonlar |
|---|---|---|
| `oc_information` | 6 | information_id, bottom, sort_order, status |
| `oc_information_description` | 12 | information_id, language_id, title, description, meta_title, meta_description |

**Mevcut bilgi sayfaları:**
- id=3: Gizlilik Politikası
- id=4: Hakkımızda
- id=5: Şartlar ve Koşullar
- id=6: Teslimat Bilgisi
- id=7: Toptan Alışveriş
- id=8: Duyurular

#### Sipariş & Müşteri
| Tablo | Önemli Kolonlar |
|---|---|
| `oc_order` | order_id, customer_id, total, currency_code, payment_method, order_status_id |
| `oc_order_product` | order_id, product_id, quantity |
| `oc_customer` | customer_id, email, telephone, status |
| `oc_address` | address_id, customer_id, city, country_id |

⚠️ **KVKK uyumu:** Müşteri verisi dump'lanmadan önce anonim hale getirilmeli.

#### Manufacturer (Üreticiler)
| Tablo | Satır | Açıklama |
|---|---|---|
| `oc_manufacturer` | 0 | Bu oturumda 6 demo üretici (Apple, Sony vb.) silindi |

### Journal3 Tabloları (≈20 tablo)

| Tablo | Önemli Kolonlar |
|---|---|
| **`oc_journal3_setting`** | store_id, **setting_group**, **setting_name**, setting_value, serialized |
| `oc_journal3_skin_setting` | skin_id, setting_name, setting_value |
| `oc_journal3_blog_post` | post_id, status, date_added |
| `oc_journal3_blog_post_description` | post_id, language_id, title, description, content |
| `oc_journal3_blog_category` | category_id, status |
| `oc_journal3_module` | module_id, journal_module, settings (JSON) |
| `oc_journal3_layout` | layout_id, journal_layout |
| `oc_journal3_message` | (form submission'lar) |
| `oc_journal3_newsletter` | email, date |

**Önemli oc_journal3_setting setting_group'ları:**
- `general` — Genel ayarlar
- `seo` — Open Graph + Rich Snippets ayarları
- `blog` — Blog modülü
- `custom_code` — **customCodeHeader, customCodeFooter, customCSS, customJS**
- `active_skin` — Aktif skin id
- `dashboard` — Admin dashboard

**customCodeHeader şu an içerir** (bizim eklediğimiz):
```html
hreflang TR/EN/x-default
Organization schema with contactPoint
```

### QNB Pay Modülü Tabloları
| Tablo | Açıklama |
|---|---|
| (Muhtemelen) `oc_qnbpay_transactions` | İşlem logları (var mı kontrol et) |
| Custom tablolar | bolkarco'nun eklediği — `db/schema.sql`'de ara |

## Index Stratejisi

OpenCart varsayılan index'leri vardır:
- Primary key: her tablo
- Foreign key: ilişkili kolonlarda
- Unique: email, code gibi alanlarda

**oc_seo_url** üzerinde:
- PRI: seo_url_id
- MUL: query (lookup)
- MUL: keyword (reverse lookup)

⚠️ **Multi-column index gerekebilir:** `(language_id, keyword)` — sık sorgu

## Migration / Schema Değişikliği Yapma

OpenCart **manual migration** modelinde:
- Yeni kolon eklerken: `ALTER TABLE ... ADD COLUMN`
- DB schema değişiklikleri OCMOD ile gelir (varsa)
- Custom field için OpenCart'ın "Custom Fields" özelliği (admin'den)

## Backup Stratejisi (Mevcut)

- NetInternet shared paket: haftalık otomatik (sağlayıcı yapıyor)
- Manuel: cPanel → Backup → Full Account
- Yerel: cPanel → phpMyAdmin → Export

## Backup Stratejisi (VPS sonrası)

- Günlük tam DB dump → S3 uyumlu uzak depo
- 30 gün retention
- Restore test ayda 1 kez

## Sık Kullanılan Sorgular

### Tüm kategori sayısı + meta dolu/boş oranı
```sql
SELECT 
  COUNT(*) AS toplam,
  SUM(CASE WHEN meta_title <> '' THEN 1 ELSE 0 END) AS title_dolu,
  SUM(CASE WHEN meta_description <> '' THEN 1 ELSE 0 END) AS desc_dolu,
  SUM(CASE WHEN description <> '' AND LENGTH(description) > 100 THEN 1 ELSE 0 END) AS long_desc_dolu
FROM oc_category_description WHERE language_id=2;
```

### Ürün SEO durum check
```sql
SELECT 
  COUNT(DISTINCT product_id) AS urun_sayisi,
  SUM(CASE WHEN meta_title <> '' THEN 1 ELSE 0 END) AS meta_title,
  SUM(CASE WHEN meta_description <> '' THEN 1 ELSE 0 END) AS meta_desc
FROM oc_product_description WHERE language_id=2;
```

### Eksik SEO URL keyword'ü olan ürünler
```sql
SELECT p.product_id, pd.name
FROM oc_product p
JOIN oc_product_description pd ON p.product_id = pd.product_id
LEFT JOIN oc_seo_url s ON s.query = CONCAT('product_id=', p.product_id) AND s.language_id = 2
WHERE p.status = 1 AND pd.language_id = 2 AND s.seo_url_id IS NULL;
```

### Sitemap için ürün listesi (Google Sitemap feed'i gibi)
```sql
SELECT p.product_id, p.date_modified, pd.name
FROM oc_product p
JOIN oc_product_description pd ON p.product_id = pd.product_id
WHERE p.status = 1 AND pd.language_id = 2
ORDER BY p.date_modified DESC;
```
