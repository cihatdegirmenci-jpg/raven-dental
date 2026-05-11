# 03 — Performance Code Review (Raven Dental)

> **Tarih:** 2026-05-12
> **Kapsam:** Yerel kod kopyası (`/Users/ipci/raven-dental/code/`) statik analizi
> **Stack:** OpenCart 3.0.3.8 + Journal3 v3.1.12, PHP 7.4.33, MySQL 8.0.46, LiteSpeed (shared CloudLinux LVE)
> **NOT:** Bu rapor sadece **kodda görülen sorunları** içerir. Lighthouse / WebPageTest / ApacheBench ölçümleri sonraki faza ait.

## TL;DR — En Pahalı 5 Bulgu

| # | Severity | Bulgu | Tahmini Kazanç (sonra) |
|---|---|---|---|
| 1 | 🔴 | `oc_product` MyISAM + tek index (sadece PRIMARY) — kategori sayfası tablo taraması | ~200-500 ms ↓ |
| 2 | 🔴 | `ModelCatalogProduct::getProducts()` her sayfada N+1: 20 ürün = 1 + 20 query | ~150-300 ms ↓ |
| 3 | 🔴 | Twig `auto_reload=true` + `ArrayLoader` her render'da yeni Environment instance | ~50-200 ms ↓ |
| 4 | 🔴 | `Cache\File::__construct` her request'te `glob(DIR_CACHE.'cache.*')` ile **TÜM cache dosyalarını listeliyor** + expired olanları siliyor | ~10-100 ms ↓ (cache dolduğunda lineer büyür) |
| 5 | 🔴 | Tema build cache (`storage/cache/template/`) yok + ürün/kategori cache adaptörü `file` | Redis ile ~%30-50 toplam yanıt süresi ↓ |

---

## 1. Database Query Patternleri

### 1.1 🔴 KRİTİK: N+1 query — `ModelCatalogProduct::getProducts()`
**Dosya:** `catalog/model/catalog/product.php:59-207`
**Sorun:**
```php
public function getProducts($data = array()) {
    // ... uzun SELECT ...
    foreach ($query->rows as $result) {
        $product_data[$result['product_id']] = $this->getProduct($result['product_id']);  // ← her ürün için TEK BİR ek query
    }
    return $product_data;
}
```
`getProduct()` her seferinde 8 korelasyonlu subquery'li, 3-JOIN'li bir SELECT çalıştırıyor (`product.php:8`). 20 ürünlük kategori sayfası → **1 + 20 = 21 query**. 100 ürünlük listede 101 query.

`category.php` controller (`catalog/controller/product/category.php:163`) `getProducts()`'u aynı şekilde çağırıyor, sonra `getTotalProducts()` ile bir daha (line 161).

**Çözüm:**
- `getProducts()`'un ilk SELECT'ini Journal3'ün yaptığı gibi `WHERE p.product_id IN (...)` toplu sorgu haline getir (bkz. `catalog/model/journal3/product.php:27` — Journal3 zaten doğru yapıyor).
- Veya `ControllerProductCategory` route'unu Journal3 filter modeline yönlendir.
- `getTotalProducts()` + `getProducts()` aynı WHERE'i iki kez çalıştırıyor; SQL_CALC_FOUND_ROWS veya tek query + `count()` ile birleştirilebilir (PHP 8.x'te performans farkı az ama HTTP latency 1 RTT azalır).

**Tahmini kazanç:** TR shared'tan VPS'e geçişte tek başına ~150-300 ms.

---

### 1.2 🔴 KRİTİK: Korelasyonlu subquery'ler her satırda çalışıyor
**Dosya:** `catalog/model/catalog/product.php:8` (ve aynısı `model/journal3/product.php:27`, `model/journal3/filter.php:792-825`)

Tek `getProduct()` çağrısı içinde **8 ayrı korelasyonlu subquery** var:
1. `product_discount` → discount price (LIMIT 1 + ORDER BY)
2. `product_special` → special price (LIMIT 1 + ORDER BY)
3. `product_reward` → reward points
4. `stock_status` → status name
5. `weight_class_description` → unit
6. `length_class_description` → unit
7. `review r1` → AVG(rating)
8. `review r2` → COUNT(*) reviews

`product_special` ve `product_discount` üzerinde sadece `KEY product_id` var (composite yok), customer_group_id + date_start + date_end + priority filtreleri index taraması yapamıyor → her bir subquery için `Using filesort; Using temporary` muhtemel.

**Çözüm:**
- Composite index: `oc_product_special(product_id, customer_group_id, date_start, date_end, priority, price)`
- Composite index: `oc_product_discount(product_id, customer_group_id, quantity, date_start, date_end, priority, price)`
- Composite index: `oc_review(product_id, status)`
- Aynı korelasyonlu subquery'i iki kez tekrarlama → CTE veya view (MySQL 8.x mevcut)

**Tahmini kazanç:** Her ürün listesinde ~50-150 ms.

---

### 1.3 🔴 KRİTİK: `oc_product` üzerinde index eksikliği
**Dosya:** `db/schema.sql:CREATE TABLE oc_product`

```sql
CREATE TABLE `oc_product` (
  ...
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=455;
```

**Tek index PRIMARY KEY**. Sık kullanılan kolonlar:
- `status` (her WHERE'de): index YOK
- `date_available` (her WHERE'de): index YOK
- `sort_order` (her ORDER BY'da): index YOK
- `manufacturer_id` (filter): index YOK
- `viewed` (popular sort): index YOK
- `price` (filter + sort): index YOK
- `quantity` (stock filter): index YOK

345 üründe lineer tarama çok yüksek maliyet değil (~ms), ama bu tablonun yapısı ölçeklenmiyor. 5000+ ürüne çıkıldığında 100-500 ms'lik query'ler oluşur.

**Çözüm:**
```sql
ALTER TABLE oc_product
  ADD INDEX idx_status_date (status, date_available),
  ADD INDEX idx_sort (sort_order),
  ADD INDEX idx_manufacturer (manufacturer_id),
  ADD INDEX idx_viewed (viewed DESC);
```

**Tahmini kazanç (5000 ürün senaryosu):** ~100-300 ms; mevcut 345 üründe ~10-50 ms.

---

### 1.4 🔴 KRİTİK: Tüm `oc_*` tabloları MyISAM (161 tablo)
**Dosya:** `db/schema.sql` (164 CREATE TABLE → 161 MyISAM, 3 InnoDB)

MyISAM:
- Table-level locking (yazma sırasında okumalar bloklanır)
- Crash recovery yok
- Foreign key yok
- MySQL 8.x'te legacy — InnoDB ile karşılaştırıldığında buffer pool, adaptive hash index, parallel reads kaybediliyor
- `COUNT(*)` MyISAM'de hızlı (cached) ama JOIN'li `COUNT(DISTINCT)` (örn. `getTotalProducts()`) yine tablo tarar

**Çözüm:**
```sql
-- oc_session, oc_cart, oc_customer_activity gibi yazma-yoğun tabloları öncelikli
ALTER TABLE oc_product ENGINE=InnoDB;
ALTER TABLE oc_product_description ENGINE=InnoDB;
ALTER TABLE oc_product_to_category ENGINE=InnoDB;
ALTER TABLE oc_product_to_store ENGINE=InnoDB;
ALTER TABLE oc_seo_url ENGINE=InnoDB;
ALTER TABLE oc_category ENGINE=InnoDB;
ALTER TABLE oc_category_description ENGINE=InnoDB;
-- ... vb. (toplu script ile)
```

**VPS sonrası önceliği:** Shared'da MariaDB tuning sınırlı, InnoDB buffer pool sizing yok. VPS'te `innodb_buffer_pool_size = 1G` + `innodb_io_capacity = 2000` ile birlikte ~%20-40 query süresi ↓.

**Tahmini kazanç:** Concurrent kullanıcıda 3-10× throughput artışı.

---

### 1.5 🟠 ORTA: `oc_seo_url` lookup'u her sayfada
**Dosya:** OCMOD'da `controller/startup/seo_url.php`'ye benzer route resolution (OpenCart default).

`oc_seo_url` üzerinde `KEY query` ve `KEY keyword` var ✓ — ama 744 kayıtta her sayfa yüklemesi için 2-3 lookup (URL parsing + canonical building). MyISAM'de OK ama InnoDB'ye geçişte aynı.

**Çözüm:** SEO URL map'ini `cache` library üzerinden cache'le (zaten OpenCart 3.1+ yapıyor — bu sürümde yok).

**Tahmini kazanç:** ~5-15 ms/sayfa.

---

### 1.6 🟠 ORTA: `getCategoryTree()` her sayfada full tree çekiyor
**Dosya:** `catalog/model/journal3/category.php:41-91`

```php
private function getCategoryTree($category_id) {
    if (static::$category_tree === null) {
        // ... TÜM kategorileri çek ...
        // + her kategori için product count subquery (config_product_count açıksa)
    }
}
```

Bu en azından **request başına bir kez** cache'liyor (`static::$category_tree`). `getSubcategories()` ise Journal3 cache'ine atıyor (line 102-110) — bu doğru. Ama: `static::$category_tree` request-local; yani bir request'te birden fazla layout pozisyonu (header_menu, side_menu, footer_menu, breadcrumb) çağrılırsa sadece bir kez geliyor — OK.

**Sorun:** `config_product_count = ON` ise her kategori için pahalı subquery (`COUNT(p.product_id)` + 3 JOIN). 18 kategoride bile, 18 subquery. Settings'ten kapatılabilir.

**Çözüm:**
- Admin'de `config_product_count` durumunu kontrol et — kapalıysa OK.
- Açıksa: cache'e ek olarak günlük cron ile materialized count tablosu.

**Tahmini kazanç (config_product_count açıksa):** ~20-50 ms.

---

### 1.7 🟠 ORTA: `getProductsCountdown()` — limitsiz sorgu
**Dosya:** `catalog/model/journal3/product.php:118-142`

```php
$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special ps WHERE ...");
foreach ($query->rows as $row) {
    $results[$row['product_id']] = date('D M d Y H:i:s O', strtotime($row['date_end']));
}
```

`product_special` tüm aktif kayıtları çekiyor (LIMIT yok), her ürün için sadece `date_end` lazımken `SELECT *` yapılıyor. 100+ özel ürün olursa gereksiz veri transferi.

**Çözüm:**
```sql
SELECT product_id, MAX(date_end) AS date_end FROM oc_product_special
WHERE customer_group_id = ? AND ... GROUP BY product_id
```

**Tahmini kazanç:** ~5-20 ms (kayıt sayısına göre).

---

### 1.8 🟠 ORTA: Search query — `pd.name LIKE '%word%'` index kullanamaz
**Dosya:** `catalog/model/catalog/product.php:109` ve `model/journal3/filter.php:992`

```php
$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
```

Leading wildcard → full table scan. `oc_product_description` üzerinde `KEY name` var ama `LIKE '%x%'` bu index'i kullanamaz.

Ayrıca `LCASE(p.model)`, `LCASE(p.sku)`, `LCASE(p.upc)`, `LCASE(p.ean)`, `LCASE(p.jan)`, `LCASE(p.isbn)`, `LCASE(p.mpn)` — fonksiyonel index olmadan tarama.

**Çözüm:**
- MySQL 8.0 generated columns + functional index:
  ```sql
  ALTER TABLE oc_product ADD COLUMN model_lower VARCHAR(64) GENERATED ALWAYS AS (LOWER(model)) STORED, ADD INDEX (model_lower);
  ```
- Veya FULLTEXT index: `ALTER TABLE oc_product_description ADD FULLTEXT(name, description);` → `MATCH(name) AGAINST (?)`
- Uzun vadeli: Meilisearch / Typesense / OpenSearch integration.

**Tahmini kazanç:** Aramada ~50-300 ms (mevcut 345 ürün düşük → ölçeklenince kritik).

---

### 1.9 🟡 DÜŞÜK: `updateViewed` her ürün sayfasında ayrı UPDATE
**Dosya:** `catalog/model/catalog/product.php:3-5` ve `catalog/controller/product/product.php:460`

```php
public function updateViewed($product_id) {
    $this->db->query("UPDATE ... SET viewed = (viewed + 1) WHERE product_id = ?");
}
```

Her ürün görüntülemede senkron UPDATE → MyISAM'de table-level lock + redo log yok ama I/O var. Yoğun trafik anında counter güncellemeleri kuyrukta birikir.

**Çözüm:**
- Async (session'da bekleyen counter, cron ile batch UPDATE)
- Veya Redis INCR + periyodik sync

**Tahmini kazanç:** ~2-10 ms/ürün + lock contention azalır.

---

### 1.10 🟡 DÜŞÜK: `Settings::getSettings()` — startup'ta tüm Journal3 ayarlarını çekiyor
**Dosya:** `catalog/model/journal3/settings.php:48-94`

Üç ardışık SELECT:
1. `oc_journal3_setting WHERE store_id = 0 OR store_id = X`
2. `oc_journal3_skin_setting WHERE skin_id = X`
3. `oc_journal3_variable` + `oc_journal3_style` (getVariables)

Her sayfa için. Cache yok (Journal3 cache class'ı `JOURNAL3_CACHE` constant'ına bakıyor — açıksa SuperCache).

**Çözüm:** Mevcut SuperCache (`Journal3\Cache`) ile zaten cache'lenebilir kontrol et — admin → System → Settings → Caching ON olmalı. Kapalıysa **bu tek başına büyük kazanç.**

**Tahmini kazanç:** Cache kapalıysa açınca ~20-80 ms/sayfa.

---

## 2. JavaScript Bundle / Asset Yükleme

### 2.1 🔴 KRİTİK: Frontend JS toplam — gerekenden 5× büyük

Frontend'e giden JS dosyaları (catalog tree, gerçek inenler):

| Dosya | Boyut | Önem |
|---|---|---|
| `theme/journal3/lib/jquery/jquery-3.3.1.js` | 272 KB | Min: 87 KB — neden `.min` değil? |
| `theme/journal3/lib/swiper/swiper.js` | 282 KB | Sadece carousel için — homepage'de olmasa lazy yüklenmeli |
| `theme/journal3/lib/vue/vue.js` | 289 KB | Hangi feature kullanıyor? Quickview için olabilir → ihtiyaç değilse kaldır |
| `theme/journal3/lib/masterslider/masterslider.js` | 268 KB | Slider lib (sadece slider olan sayfalarda) |
| `theme/journal3/lib/revolution/*.js` | 700+ KB toplam | Revolution slider (slider olmayan sayfalarda yüklemek gereksiz) |
| `theme/journal3/lib/layerslider/*.js` | 230 KB | Üçüncü slider lib |
| `theme/journal3/js/journal.js` | 50 KB | Core theme JS |
| `qnbpay/qnbpay-script.js` | **99 KB** | Sadece checkout'ta lazım |
| `qnbpay/qnbpay-imask.js` | **82 KB** | Sadece checkout'ta lazım |
| `jquery/swiper/js/swiper.jquery.umd.min.js` | 87 KB | Duplicate of theme swiper |
| `jquery/datetimepicker/moment/moment-with-locales.js` | 452 KB | Sadece date input olan sayfalar (ürün date_available form?) |
| `jquery/datetimepicker/moment/locales.js` | 324 KB | Tüm locale → sadece TR/EN gerek |
| `jquery/datetimepicker/moment/tests.js` | **3.7 MB** | Test dosyası prod'da! Sıfır kullanılıyor. |

**Bulgular:**

#### 2.1.a 🔴 Test dosyası canlıda
**Dosya:** `catalog/view/javascript/jquery/datetimepicker/moment/tests.js` (3.7 MB)
- Test dosyası prodüksiyon dizininde
- Yüklenmeyebilir ama erişilebilir → güvenlik + boşa yer
- Sil veya `.htaccess` ile blokla

#### 2.1.b 🔴 Üç ayrı slider kütüphanesi
- Revolution Slider (~700 KB), Master Slider (~270 KB), Layer Slider (~230 KB)
- Sadece bir tanesi aktif kullanılıyor olmalı (tema settings'den)
- Diğer ikisi dead code → fiziksel kaldırılırsa: ~1 MB disk + dolaylı browser cache miss riski

#### 2.1.c 🔴 jQuery iki kopya
- `catalog/view/javascript/jquery/jquery-2.1.1.min.js` (84 KB)
- `catalog/view/theme/journal3/lib/jquery/jquery-3.3.1.min.js` (87 KB)
- `catalog/view/theme/journal3/lib/jquery/jquery-2.1.1.min.js` (84 KB)
- Hangisi yükleniyor? `system/journal3.ocmod.xml` jQuery'i swap ediyor olabilir → kontrol et

#### 2.1.d 🔴 QNB Pay scriptleri her sayfada mı?
**Dosya:** `catalog/view/javascript/qnbpay/qnbpay-script.js` (99 KB) + `qnbpay-imask.js` (82 KB) = 181 KB

OpenCart'ta payment extension JS'leri genelde checkout sayfasında yüklenir. Doğrulanmalı — eğer header'a inject ediliyorsa anasayfada 181 KB boşa.

**Çözüm:** Önce dolayalı doğrulama:
```bash
curl -s https://ravendentalgroup.com/ | grep -o "qnbpay[^\"]*\.js"
```
Anasayfada bulunuyorsa → controller koşullu yükleme.

#### 2.1.e 🟠 Moment.js + tüm locale'ler
- `moment-with-locales.js` (452 KB) + `locales.js` (324 KB) = ~800 KB
- Sadece `tr` ve `en-gb` lazım
- `moment.min.js` (51 KB) + locale `tr.js` (~3 KB) = **53 KB** (15× küçük)

**Çözüm:** date picker init script'inde sadece kullanılan locale'leri ekle.

**Tahmini kazanç (yukarıdakilerin toplamı):**
- HTTP/2 multiplexing nedeniyle download süresi sabit kalsa da parse/eval CPU bedeli yüksek
- LCP üzerinde direkt etkisi: 200-600 ms (mobil, orta CPU)
- TBT (Total Blocking Time): 300-1000 ms

---

### 2.2 🟠 ORTA: Header'da defer kontrolü ama header script'leri yine de header'da
**Dosya:** `catalog/view/theme/journal3/template/common/header.twig:69-71`

```twig
{% for script in j3.document.getScripts('header', scripts) %}
<script src="{{ ... }}" {% if j3.settings.get('performanceJSDefer') %} defer {% endif %}></script>
{% endfor %}
```

Defer mevcut ✓ ama "performanceJSDefer" admin setting'ine bağlı — ON mu?
**Doğrula:** Sayfa HTML'inde `<script defer>` görünmeli.

Ayrıca `Journal3\Document::getScripts('footer')` (line 313, document.php) `common.js` ve `journal.js`'i footer'a ekliyor — bu doğru. Ama product page controller (`product.php:237-241`) magnific, datetimepicker, moment'i `addScript` ile header'a ekliyor (default position).

**Çözüm:**
- Product page'de `$this->document->addScript(..., 'footer')` ikinci parametre kullan (Journal3 sürümünde var, ama OpenCart stock document.php'de yok).
- En kötüsü: `defer` tüm scriptlerde ON.

---

### 2.3 🟠 ORTA: Inline `Journal` global object
**Dosya:** `catalog/view/theme/journal3/template/common/header.twig:38`

```twig
<script>window['Journal'] = {{ j3.document.getJs() | json_encode }};</script>
```

Bu inline script:
- CSP-friendly değil (`unsafe-inline` gerektirir)
- Production'da 20-50 KB (theme settings tüm flagleri JSON)
- HTML'de inline → gzip ratio iyi ama render-blocking değil (script tag inline)
- Çözüm: kritik olmayan kısımları `window.JournalLazy` olarak ayrı endpoint'ten async fetch et

**Tahmini kazanç:** ~5-10 KB HTML küçültme, CSP iyileştirme.

---

### 2.4 🟠 ORTA: Journal Minifier her request'te dosya I/O
**Dosya:** `system/library/journal3/minifier.php:57-99` (`minifyStyles`) ve `:101-137` (`minifyScripts`)

```php
$hash = static::hash($styles, 'css');  // MD5 over key list + JOURNAL3_BUILD
$file = static::$ASSETS_PATH . $hash;

if (!is_file($file) || static::DEBUG) {
    // Tüm CSS'leri tek tek oku, minify et, birleştir, yaz
}
```

**Mevcut durum:** İlk request'te tüm CSS/JS birleşip `catalog/view/theme/journal3/assets/{hash}.css|.js` olarak diske yazılıyor. Sonraki request'lerde `is_file($file)` doğrulanıyor → cache hit → tek `file_get_contents` veya doğrudan static URL serve.

Aslında bu **iyi tasarlanmış**. Ama:
- `is_file()` her request'te disk stat → OPcache `realpath_cache` ile hafifler (PHP 7.4 default 16K, 8.2'de 16K aynı ama LiteSpeed LSAPI ile process worker reuse iyi)
- Asset dosyası **production'da bir kez** üretilse `staticUrl(..., false)` ile direkt URL döndürülmesi mümkün → `is_file` çağrısı bile gerekmez

**Çözüm:** Journal3 admin → Performance Settings → CSS/JS Minify = ON kontrolü. Açıksa çalışıyor.

**Doğrula:**
```bash
ls /home/ravenden/public_html/catalog/view/theme/journal3/assets/ | head -5
# {hash}.css ve {hash}.js dosyaları olmalı
```

Boşsa minifier devre dışı → kazanç büyük (~80 ms HTML, ayrı CSS file count azalır).

---

### 2.5 🟡 DÜŞÜK: Source map dosyaları prod'da
**Dosya:** `catalog/view/theme/journal3/stylesheet/style.min.css.map` (355 KB)

Source map prod'da kalmamalı — boşa yer, devtools açıkken yüklenebilir (network panel'de). Ayrıca admin/journal.css.map ve journal.js.map da var.

**Çözüm:** `.htaccess` ile `*.map` 404 veya fiziksel sil.

---

## 3. CSS

### 3.1 🟡 DÜŞÜK: 149 CSS dosyası tema içinde
**Dosya:** `find catalog/view -name '*.css' | wc -l` → **149**

149 CSS dosyası olmak fiziksel sayım — hepsi yüklenmiyor. Journal3 Minifier (yukarıda 2.4) cache hit'inde **tek hash'lenmiş CSS** serve ediyor.

**Doğrulama gerekli:** `j3.document.getStyles()` sonucunda kaç `<link>` görünüyor?
```bash
curl -s https://ravendentalgroup.com/ | grep -o '<link [^>]*\.css[^>]*>' | wc -l
```
Beklenen: 1-3. Eğer 10+ ise minifier kapalı → 2.4'e bak.

### 3.2 🟠 ORTA: `style.min.css` 139 KB (tek dosya)
**Dosya:** `catalog/view/theme/journal3/stylesheet/style.min.css`

- Minify edilmiş **139 KB** — çok büyük (typical theme: 40-80 KB)
- Critical CSS extraction yok → render-blocking
- `<link media="all">` yerine `media="print" onload="this.media='all'"` tekniği uygulanabilir (non-critical CSS lazy)

**Çözüm:**
- Critical CSS extraction (Penthouse / Critters): above-the-fold için ~10-15 KB inline
- Geri kalan 124 KB lazy load
- Long term: SCSS kaynakları yok (`find` → 0 .scss) — kaynak SCSS dizini storage'a alınmış olabilir. Yeniden compile ihtimali Journal3 admin paneli üzerinden.

### 3.3 🟡 DÜŞÜK: Font Awesome çoklu kopya
- `lib/font-awesome/css/font-awesome.css` (37 KB) + `.min.css` (31 KB)
- `lib/revolution/fonts/font-awesome/css/font-awesome.css` (42 KB) + `.min.css` (29 KB)
- İcon klasörü `icons/style.css` (77 KB) ayrıca custom icon font

İki ayrı Font Awesome (Journal3 lib ve Revolution lib) — birleştir veya bir tanesini yükleme.

### 3.4 🟠 ORTA: Print stylesheet yok ama render-blocking var
header.twig:54'te `media="all"` — tüm media types için block. Print için ayrı `<link media="print">` mantıklı olabilir ama esas konu yukarıdaki critical CSS.

---

## 4. Image Handling

### 4.1 🔴 KRİTİK: `ModelToolImage::resize()` her çağrıda 3 I/O syscall
**Dosya:** `catalog/model/tool/image.php:3-48`

```php
public function resize($filename, $width, $height) {
    if (!is_file(DIR_IMAGE . $filename) || ...) return;        // ← stat()
    if (!is_file(DIR_IMAGE . $image_new) || filemtime(...) > filemtime(...)) {  // ← 2× stat() + 2× filemtime
        list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);  // ← image header read
        ...
    }
}
```

Kategori sayfasında 20 ürün × 2 imaj (thumb + popup) = 40 çağrı = 40 × 3-5 stat = ~120-200 syscall. PHP-FPM/LSAPI APCu opcache yardımıyla minimize edilebilir ama realpath_cache_size'a takılırsa filesystem trip.

Ayrıca `list($width_orig, $height_orig) = getimagesize(...)` — file already exists ise gereksiz.

**Çözüm:**
- Result'i request-içi cache (static array): aynı `(filename, width, height)` çağrısı bir request'te tek I/O.
- Long term: `<picture>` element + `srcset` ile responsive (zaten teorik altyapı var ama browser cache 1y olsa da).

**Tahmini kazanç:** Stat I/O ~5-15 ms/sayfa.

---

### 4.2 🔴 KRİTİK: GD ile resize — yavaş ve quality düşük
**Dosya:** `system/library/image.php:27-201`

OpenCart 3.x stock `Image` class:
- `imagecreatefromjpeg` / `imagecreatefrompng` ile resize → GD library
- ImageMagick desteği yok stock'ta — Journal3'te `Img::canOptimise()` var ama o admin tarafı
- WebP üretimi var (`imagewebp`) — class destekliyor ama OpenCart varsayılan akış JPEG/PNG üretiyor

**Mevcut durum:** `image/cache/X-WxH.jpg` üretiliyor (örn. `200x280h.png`). İlk request yavaş (50-300 ms tek imaj için), sonraki cache hit.

**Sorunlar:**
- WebP versiyonu otomatik üretilmiyor → modern tarayıcılarda eski format
- Quality 90 (sabit) — JPEG'de 75 yeterli, %30+ boyut tasarrufu
- AVIF desteği yok
- Resampling algorithm `imagecopyresampled` (bicubic) → ImageMagick'in Lanczos'undan daha düşük kalite

**Çözüm:**
- VPS sonrası: `system/library/image.php`'yi ImageMagick wrapper ile değiştir (mevcut OCMOD'la yapılabilir)
- WebP generator: dual-pass — `image/cache/X-WxH.jpg` + `image/cache/X-WxH.webp`
- `<picture>` markup theme'de

**Tahmini kazanç:** İlk yüklemede pahalı (resize) ama sonraki cache hit'lerde %30-50 daha az byte (WebP).

---

### 4.3 🟠 ORTA: Lazy loading flag ON ama markup mantığı kontrolden geçmeli
**Dosya:** `catalog/view/theme/journal3/template/journal3/product_card.twig:23-34`

```twig
{% if j3.settings.get('performanceLazyLoadImagesStatus') %}
  <img src="{{ dummy_image }}" data-src="{{ product.thumb }}" class="lazyload"/>
{% else %}
  <img src="{{ product.thumb }}" class="img-first"/>
{% endif %}
```

Lazy load var ✓ — Journal3 yaklaşımı (`data-src` + `lazyload` class + js library). Modern alternatif: native `loading="lazy"` attribute (Chrome 76+, Firefox 75+).

**İyileştirme:**
- Native `loading="lazy"` eklemek (hem fallback hem polyfill yedek)
- Hero image'lere `fetchpriority="high"` ekle (LCP candidate'i)
- `loading="lazy"` ile birlikte `decoding="async"` ekle

### 4.4 🟡 DÜŞÜK: Placeholder image full-size
07-PERFORMANCE.md'de not edildi: "Image cache klasöründe çoğu resmin 200×280h.png placeholder hali var ama gerçek resim 1000×1000 büyüklükte". Bu, **orijinal resim olarak yüklenen** asset boyutuyla ilgili — DB veya disk'te `image/catalog/products/...` dizininde orijinaller. Cron veya admin batch optimize tool ile pre-shrink yapılabilir.

---

## 5. Cache Kullanımı

### 5.1 🔴 KRİTİK: `Cache\File::__construct` her request'te glob+expire scan
**Dosya:** `system/library/cache/file.php:6-25`

```php
public function __construct($expire = 3600) {
    $this->expire = $expire;
    if (!is_dir(DIR_CACHE)) @mkdir(DIR_CACHE, 0755, true);
    $files = glob(DIR_CACHE . 'cache.*');       // ← TÜM cache dosyalarını listele
    if ($files) {
        foreach ($files as $file) {
            $time = substr(strrchr($file, '.'), 1);
            if ($time < time()) {
                if (file_exists($file)) unlink($file);  // ← expired olanları sil
            }
        }
    }
}
```

**Sorunlar:**
1. Her request'te `glob()` çağrısı (binlerce cache dosyası birikmişse pahalı)
2. Cache eviction request-time'da yapılıyor — request latency'sine ekleniyor (cron işi olmalıydı)
3. `unlink()` her expired dosya için ayrı syscall

Türkçe yorum (`Cache klasörü kontrolü`) — bu dosya **modifiye edilmiş** OpenCart 3.x stock'tan. Stock OpenCart'ta da bu pattern var ama bu sürümde ek `is_dir` + `mkdir` kontrolleri eklenmiş.

**Çözüm:**
- Eviction'ı cron'a taşı (`/usr/bin/find /home/ravenden/storage/cache -name 'cache.*' -mmin +60 -delete`)
- Constructor'da glob'u kaldır
- Cache dosyalarını birden fazla subdirectory'e böl (hash prefix)

**Tahmini kazanç:** Cache dolduğunda ~10-100 ms/request (lineer büyür).

### 5.2 🔴 KRİTİK: Production cache adaptörü `file`
**Dosya:** `system/config/default.php:48`
```php
$_['cache_engine'] = 'file';
```

Mevcut adapter `file`. Redis adapter (`system/library/cache/redis.php`) **mevcut** ama aktif değil. APCu de mevcut.

VPS'te Redis'e geçince:
- `unlink/glob` overhead'i sıfır
- Memory-resident → ~µs cache lookup
- Pub/sub ile multi-process invalidation

**Çözüm:**
```php
// public_html/config.php (custom override)
$_['cache_engine'] = 'redis';
```
ve `system/config/default.php`'de:
```php
$_['cache_hostname'] = '127.0.0.1';
$_['cache_port'] = 6379;
```

**Tahmini kazanç:** Cache hit latency 5-20 ms → <1 ms. Toplam %15-25 yanıt süresi.

### 5.3 🔴 KRİTİK: Manuel cache temizleme noktaları yok ama Journal3 cache hassas
**Dosya:** `system/library/journal3/cache.php` (SuperCache wrapper)

Journal3 cache anahtarı (line 99-115):
```php
sprintf("%s_%s_s%d_l%d_c%s_c%d_g%d_a%dw_%d_%s",
    md5(Host)..10, device, store_id, language_id, currency_id, customer, customer_group_id, webp, admin, JOURNAL3_BUILD)
```

Cache key 10+ boyutlu — `device` (phone/tablet/desktop), customer_group_id, webp variant vs. → her bir kombinasyon ayrı cache entry.

Mevcut 345 ürün × ~10 farklı cache combination = ~3500 cache entry üretimi mümkün. SuperCache file backend ise glob/unlink overhead'i 5.1'deki gibi büyür.

**Çözüm:** SuperCache config (eğer ayrı adapter) Redis'e taşı.

### 5.4 🟠 ORTA: `getLatestProducts` / `getPopularProducts` / `getBestSellerProducts` cache'li ama N+1 var
**Dosya:** `catalog/model/catalog/product.php:259-307`

İyi: her biri `$this->cache->get(...)` ile cache'leniyor (cache key in language_id + store_id + customer_group_id + limit).

Kötü: cache miss durumunda hala N+1:
```php
foreach ($query->rows as $result) {
    $product_data[$result['product_id']] = $this->getProduct($result['product_id']);
}
```

Cache miss → 1 + 20 query. İlk visitor pahalı, sonrakiler ucuz. Ama cache 1 saatlik → her saat başı miss.

**Çözüm:** Aynı 1.1'deki gibi toplu `WHERE product_id IN (...)`.

---

## 6. Twig Template Rendering

### 6.1 🔴 KRİTİK: Twig Environment her render'da yeniden oluşturuluyor
**Dosya:** `system/library/template/twig.php:10-40`

```php
public function render($filename, $code = '') {
    if (!$code) {
        $file = DIR_TEMPLATE . $filename . '.twig';
        $code = file_get_contents($file);
    }

    $config = array(
        'autoescape'  => false,
        'debug'       => false,
        'auto_reload' => true,   // ← her template stat check
        'cache'       => DIR_CACHE . 'template/'
    );

    $loader = new \Twig\Loader\ArrayLoader(array($filename . '.twig' => $code));
    $twig = new \Twig\Environment($loader, $config);
    return $twig->render($filename . '.twig', $this->data);
}
```

**Sorunlar:**

1. **`new \Twig\Environment` her render** — anasayfada 30+ `loadController` çağrısı her biri ayrı `view()` → ayrı Environment, ayrı extension registration, ayrı parser.
2. **`ArrayLoader` kullanılıyor** → Twig'in built-in `FilesystemLoader` cache mantığı çalışmıyor.
3. **`auto_reload => true`** + ArrayLoader → her render'da cache key MD5 hesaplama + filemtime check. ArrayLoader'da filemtime yok → cache her zaman re-write
4. **Cache key:** ArrayLoader filename'i kullanıyor (`$filename . '.twig'`) → eğer aynı filename farklı koşullarda farklı içerik veriyorsa (modification engine!) cache yanlış vurabilir veya her zaman miss

5. **Modification engine entegrasyonu** — Modification XML'i Twig render'a `DIR_MODIFICATION` kontrolünü ekliyor (system/modification.xml:13-21). Her render'da:
   - `is_file(DIR_MODIFICATION . 'admin/view/template/' . $filename . '.twig')` stat
   - `is_file(DIR_MODIFICATION . 'catalog/view/theme/' . $filename . '.twig')` stat
   - `is_file($file)` stat
   - `file_get_contents($code)`
   - Twig parse
   - Twig render

**Çözüm:**
1. Twig Environment'i tek sefer construct et + registry'e koy
2. `FilesystemLoader` kullan (gerçek dosya tabanlı cache)
3. `auto_reload => false` production'da
4. Modification engine entegrasyonunu CompilerPass benzeri bir Twig extension ile yap (run-once)

**Tahmini kazanç:** ~50-200 ms/sayfa (loadController sayısına bağlı).

### 6.2 🔴 KRİTİK: `template_cache = false`
**Dosya:** `system/config/default.php:50` ve `catalog/controller/startup/startup.php:44`

```php
$this->config->set('template_cache', $this->config->get('developer_theme'));
```

Yani admin'de "Developer Settings → Theme Cache OFF" durumundayken `template_cache = false`. Sonra `twig.php`'ye `$cache` parametresi geçilmiyor → her zaman cache miss + recompile.

Aslında `template_cache` parametresi sadece adapter'a iletilebilir ama mevcut `Template\Twig::render($filename, $code = '')`'da `$cache` argümanı yok. Yani **disable edilen veya enable edilen template cache zaten flow'da yok** — Twig'in kendi `cache` (`DIR_CACHE.'template/'`) yolu kullanılıyor.

**Çözüm:** Admin → System → Settings → Server → Developer → "Use OCMOD cache" ve "Theme cache" durumlarını ON yap (sadece dev'de OFF). Ayrıca Twig wrapper'a singleton + true filesystem loader uygula.

### 6.3 🟠 ORTA: Template'lerde `j3.settings.get()` toplam çağrı sayısı yüksek
**Dosya:** `catalog/view/theme/journal3/template/journal3/product_card.twig` ve `product/product.twig`

| Template | `settings.get(`/`j3.settings` çağrı sayısı |
|---|---|
| `common/header.twig` | 25 |
| `journal3/product_card.twig` | 20 → **× her ürün** (20 ürün = 400 çağrı) |
| `product/product.twig` | ~10 |

Her `settings.get('foo')` → `Arr::get($this->settings, 'foo')` → `array_key_exists` chain. Ucuz ama 400× × ~µs = ~1-3 ms cumulative.

**Çözüm:** product_card.twig'in `{% set ... = j3.settings.get(...) %}` ile loop başında değişkene al → loop içinde değişken kullan.

### 6.4 🟠 ORTA: `j3.loadController(...)` Twig içinde
**Dosya:** `header.twig:31, 40, 150, 154, 167` ve `category.twig:10, 124`

Twig template'leri runtime'da PHP controller'lar çağırıyor:
- `j3.loadController('journal3/seo/meta_tags')`
- `j3.loadController('journal3/mql')`
- `j3.loadController('journal3/layout', 'popup')`
- `j3.loadController('journal3/layout', 'header_notice')`
- `j3.loadController('journal3/layout', 'fullscreen_slider')`
- `j3.loadController('journal3/seo/rich_snippets', breadcrumbs)`

Her biri **ayrı event trigger** (controller/{route}/before + after — system/engine/loader.php:41,52). Event::trigger her trigger için `data` listesinde `preg_match` döngüsü (system/engine/event.php:55-65) — n adet event registered ise her trigger O(n).

**Çözüm:** Düşük öncelik — modification engine olmayan kısımları PHP controller'da `data` array'ine doldurup template'e geçir.

### 6.5 🟡 DÜŞÜK: 1.6 KB / 6.7 KB `header.twig` modif backup dosyası

Repo'da `header.twig.bak-20260511` (yedek) duruyor. **Otoload'a etki yok** ama:
- Üretimde yedek dosya production'da olmamalı
- Git ignore edilmemiş — `.gitignore` `*.bak-*` eklenmeli

---

## 7. OCMOD Modifications (`system/journal3.ocmod.xml`)

### 7.1 🟠 ORTA: 82 file modification + 110 operation
**Dosya:** `system/journal3.ocmod.xml` — 2175 satır, 95 KB

| Metric | Değer |
|---|---|
| `<file>` element | 82 |
| `<operation>` element | 110 |
| Unique file paths | 50 |
| `<add>` blokları | 123 |
| `<search>` blokları | 123 |

**Modifikasyon çalışma şekli:**
1. Admin → Extensions → Modifications → "Refresh" → tüm OCMOD XML'leri parse + apply → `storage/modification/` altında modifiye edilmiş PHP dosyaları üretiliyor.
2. Runtime'da `modification($filename)` fonksiyonu (system/startup.php:49) `DIR_MODIFICATION` altında varyantı arıyor, varsa onu yüklüyor.

**Runtime impact:** Compiled mod cache disk'te. `is_file()` ile lookup → ucuz (~µs, opcache realpath_cache'le birlikte). **Refresh** anı pahalı (admin tarafı).

**Sorun:**
- 50 unique file modifiye → her `require_once(modification($file))` çağrısında 1 `is_file()` syscall
- Storage/modification klasörü silinirse site 3KB'a düşüyor (CLAUDE.md L01)
- OPcache `validate_timestamps` AÇIK ise her request'te `filemtime` kontrolü tüm modify edilmiş PHP'ler için

**Çözüm:**
- VPS'te `opcache.validate_timestamps = 0` + deploy script'inde `opcache_reset()` → modification file timestamp'i sorulmaz.
- 82 modifikasyondan core olanlar Twig render path, cache adapter, image resize — birleştirilebilir patches.

**Tahmini kazanç:** opcache validate_timestamps kapalıyken ~10-30 ms/sayfa.

### 7.2 🟡 DÜŞÜK: Modifikasyon engine ek hop'lar
Modification engine her `require_once(modification(DIR_SYSTEM.'engine/action.php'))` çağrısında bir `is_file()` ek check. Engine bootstrap = 8 dosya = 8 ek `is_file`. realpath_cache hit'inde sorun yok.

---

## 8. Tema Setting Yüklemesi (Journal3 Settings)

### 8.1 🟠 ORTA: Journal3 settings load — startup'ta 4 SELECT
**Dosya:** `catalog/model/journal3/settings.php` + `system/library/journal3/settings.php`

Her startup'ta (eğer cache miss veya admin):
- `oc_journal3_setting` (global + store-specific) → bir SELECT
- `oc_journal3_skin_setting` (skin_id'ye göre) → bir SELECT
- `oc_journal3_variable` → bir SELECT
- `oc_journal3_style` → bir SELECT

**Cache var ✓:** `Journal3\Cache` üzerinden SuperCache cache'leniyor (eğer `JOURNAL3_CACHE` define edilmişse). Ama:
- `static $config` 10 boyutlu cache key → çok sayıda variant
- File backend ise her variant ayrı dosya

**Çözüm:** SuperCache Redis backend'e geçtiğinde sorun çözülür.

### 8.2 🟡 DÜŞÜK: `Settings::all()` ve `getWith()` PHP-level her sayfa
Hesaplanmış settings PHP array olarak request boyunca tutuluyor (Settings::$settings) — OK.

`Arr::get()` (nested key support — `'price.min'` gibi) explode + walk → her çağrıda ~µs. Sık çağrılan kısımlar için memoization makul.

---

## 9. OpenCart 3.x Bilinen Yavaşlık Noktaları

### 9.1 🔴 KRİTİK: Event::register içinde her seferinde array_multisort
**Dosya:** `system/engine/event.php:38-49`

```php
public function register($trigger, Action $action, $priority = 0) {
    $this->data[] = array(...);
    $sort_order = array();
    foreach ($this->data as $key => $value) {
        $sort_order[$key] = $value['priority'];
    }
    array_multisort($sort_order, SORT_ASC, $this->data);  // ← her register'da O(n log n)
}
```

`framework.php:58-62` startup'ta `action_event` config'inden register ediliyor (Journal3 muhtemelen 30-50 event register ediyor). 50 register × O(50 log 50) = ~280 işlem × her register = ~14000 işlem her request startup'ta. Önemsiz ama gereksiz.

**Çözüm:** Tüm registration tamamlandıktan sonra tek sefer sort.

### 9.2 🟠 ORTA: Event::trigger için preg_match her trigger'da
**Dosya:** `system/engine/event.php:55-65`

```php
public function trigger($event, array $args = array()) {
    foreach ($this->data as $value) {
        if (preg_match('/^' . str_replace(array('\*', '\?'), array('.*', '.'), preg_quote($value['trigger'], '/')) . '/', $event)) {
            $result = $value['action']->execute($this->registry, $args);
            ...
        }
    }
}
```

Her trigger için tüm `$this->data` listesinde regex match. Loader.php 10 trigger noktası × her view/controller/model çağrısı × 30+ event registered = binlerce regex.

**Çözüm:**
- Trigger string'leri prefix tree'de tut
- Wildcard'sız trigger'lar için exact match map

**Tahmini kazanç:** ~5-30 ms/sayfa (event sayısına göre).

### 9.3 🟠 ORTA: Registry magic-method based DI
**Dosya:** `system/engine/registry.php`

OpenCart 3.x'in `$this->config`, `$this->db`, `$this->customer` gibi controller access'leri Registry'nin magic getter'ı üzerinden. PHP 8.x'te yavaş değil ama loop içinde milyonlarca call olmaz — sorun değil.

### 9.4 🟠 ORTA: Loader::model'da Proxy + callback wrapping
**Dosya:** `system/engine/loader.php:79-87`

```php
$proxy = new Proxy();
foreach (get_class_methods($class) as $method) {
    $proxy->{$method} = $this->callback($this->registry, $route . '/' . $method);
}
```

Her model load'da get_class_methods + foreach + closure creation. Closure'lar before/after event trigger yapıyor → her model method call 2 event trigger ekstra (model/route/method/before + after).

**Çözüm:** Eski tartışma — runkit yerine native sınıf kullanımı OC 4'te düzeltildi. OC 3'te kalıyor.

### 9.5 🟠 ORTA: HTML kısa kapanma + tek output buffer
**Dosya:** `system/library/response.php` ve framework.php:172 `$response->output()`

Stock OpenCart 3.x tek HTML buffer'ı sonunda output ediyor — TTFB üzerinde etkisi var. Streaming output yok. Bu OC 3.x'in mimari kısıtı.

---

## 10. PHP 7.4 → 8.2 + JIT Geçiş Kazanım Tahmini

### 10.1 Genel PHP 8 vs 7.4 mikrobenchmark literatürü
- Symfony, WordPress, Magento 2 benchmark'ları: **%10-30** yanıt süresi iyileşmesi
- OpenCart 3.x özellikle string/array heavy → PHP 8.x'in `array_*`, JIT, type-strict optimizasyon yardımları doğrudan etkili

### 10.2 OPcache JIT
- PHP 7.4 → 8.2 JIT (`opcache.jit=tracing`, `opcache.jit_buffer_size=256M`)
- Loop-heavy kod (Twig template parser, Event::trigger regex döngüleri) → **%15-25** ek kazanç
- I/O bound query'ler için JIT etkisi düşük (zaten DB'de zaman)

### 10.3 Tahminlerin Toplamı (Raven Dental özelinde)

| Kategori | Mevcut (~ms) | PHP 8.2 + JIT | Redis | InnoDB + index | Toplam |
|---|---|---|---|---|---|
| DB queries (kategori sayfa) | 250-400 | 250-400 | 230-360 | **120-200** | **120-200** |
| PHP processing (Twig, event) | 200-400 | **140-280** | 140-280 | 140-280 | 140-280 |
| File cache I/O | 30-100 | 30-100 | **<5** | <5 | <5 |
| Twig render (loadController × N) | 80-200 | 60-140 | 60-140 | 60-140 | **20-60** (singleton fix) |
| Asset minify/serve | 10-50 | 8-40 | 8-40 | 8-40 | 8-40 |
| **TOPLAM TTFB tahmini** | **600-1200 ms** | **490-960** | **440-820** | **310-660** | **~250-500 ms** |

Hedef <250 ms TTFB için: yukarıdakilerin **tümü** + Cloudflare cache_everything page rule.

### 10.4 PHP 7.4 EOL riski (kod kaynaklı değil ama not)
- PHP 7.4 — Nov 2022 EOL
- Güvenlik patch yok → açık kod yolu
- Composer packages PHP 8+ talep ediyor → uzun vadeli sıkışma
- VPS migration'da Direkt 8.2 hedefi

---

## Quick Wins (1 saat içinde uygulanabilir, kod tarafında, VPS gerek yok)

| # | İş | Etki | Risk |
|---|---|---|---|
| QW1 | `catalog/view/javascript/jquery/datetimepicker/moment/tests.js` (3.7 MB) **sil** | Disk + güvenlik | Sıfır (test dosyası) |
| QW2 | Tüm `*.map` (style.min.css.map, journal.js.map vb.) prod'dan sil | ~700 KB disk | Sıfır |
| QW3 | Admin → Journal3 → Performance → **CSS Minify ON, JS Minify ON, JS Defer ON, Lazy Load ON** | LCP -200-500 ms | Düşük (admin setting) |
| QW4 | Admin → Journal3 → Performance → **Async Fonts ON, Swap Fonts ON** | LCP ~100 ms | Düşük |
| QW5 | `system/library/cache/file.php` constructor'daki glob+unlink döngüsünü kaldır (eviction'ı cron'a taşı) | ~10-50 ms/req | Orta (cache büyür → cron şart) |
| QW6 | `oc_product` üzerine 4 index ekle (status/date, sort, manufacturer, viewed) | ~50 ms/kategori sayfası | Düşük (online ALTER, table küçük) |
| QW7 | `oc_product_special`, `oc_product_discount`, `oc_review` composite index | ~50-100 ms/ürün listesi | Düşük |
| QW8 | `Admin → System → Settings → Server → Output Compression = ON` (zaten Apache gzip var, OpenCart-level kontrol) | -10-30% HTML transfer | Sıfır |
| QW9 | `oc_event` tablosunda **disabled** kayıt varsa STATUS=0'a çek (Event::trigger'ı kısaltır) | ~5-10 ms/req | Düşük |
| QW10 | Tema header.twig'de inline `<script>window['Journal'] = {...}</script>` küçültme (sadece gerekli flag'leri JSON) | ~10-30 KB HTML | Düşük (test gerek) |
| QW11 | `product_card.twig` içinde loop dışına `{% set prefix_namepos = j3.settings.getIn(...) %}` çıkar | ~5 ms/listede | Düşük |
| QW12 | `<.htaccess>` → `Header set Cache-Control "public, max-age=31536000, immutable"` for `*.css/*.js/*.woff*` | İkinci ziyaret cache | Sıfır (zaten 1y var) |

### Quick Win uygulama sırası önerisi
1. QW3 + QW4 + QW8 (admin panel) → 5 dakika, doğrudan etki
2. QW1 + QW2 → güvenlik + temizlik
3. QW6 + QW7 → DB index (online)
4. QW5 + QW9 → cache I/O
5. QW10 + QW11 → tema mikro

---

## VPS Sonrası (PHP 8.2 + Redis + OPcache JIT bağımlı)

### V1. PHP 8.2 + OPcache JIT
**Adımlar:**
- VPS'te PHP 8.2 + opcache JIT (`opcache.jit_buffer_size=256M`, `opcache.jit=tracing`)
- `opcache.validate_timestamps=0` (deploy script'inde `opcache_reset()`)
- `opcache.memory_consumption=512M` (büyük codebase)
- `opcache.max_accelerated_files=20000` (Twig compiled templates + PHP)
- `realpath_cache_size=4M`, `realpath_cache_ttl=600`

**Tahmini:** %15-30 PHP execution time

### V2. Redis cache backend (OpenCart + Journal3)
- `system/library/cache/redis.php` zaten var → enable:
  ```php
  $_['cache_engine'] = 'redis';
  $_['cache_hostname'] = '127.0.0.1';
  $_['cache_port'] = 6379;
  ```
- Journal3 SuperCache → SuperCache Redis driver
- Session storage da Redis'e (`session_engine = 'redis'` — yeni adapter yazılması gerekir; OC default `db`)

**Tahmini:** Cache hit latency 10-50 ms → <1 ms

### V3. MySQL → MariaDB veya MySQL 8.x InnoDB tuning
- 161 MyISAM tablo → InnoDB
- `innodb_buffer_pool_size = 1G` (VPS RAM'in %50-70'i)
- `innodb_io_capacity = 2000` (SSD)
- `query_cache_type = OFF` (MySQL 8.x'te yok zaten)
- `innodb_flush_log_at_trx_commit = 2` (durability vs perf trade-off)

**Tahmini:** %20-40 query süresi + 3-10× concurrent throughput

### V4. Twig FilesystemLoader + singleton
Mevcut twig.php'yi rewrite:
```php
class Twig {
    private static $env;
    public function render($filename, $code = '') {
        if (self::$env === null) {
            $loader = new \Twig\Loader\FilesystemLoader(DIR_TEMPLATE);
            self::$env = new \Twig\Environment($loader, [
                'cache' => DIR_CACHE . 'template/',
                'auto_reload' => false,  // prod
                'autoescape' => false,
            ]);
        }
        return self::$env->render($filename . '.twig', $this->data);
    }
}
```
**Risk:** OCMOD modification XML'de Twig load mantığı patch'leniyor — uyumluluk testi.

**Tahmini:** %30-60 Twig render süresi (loadController × N senaryosunda büyük)

### V5. ImageMagick + WebP/AVIF generator
- `system/library/image.php` → ImageMagick wrapper (Imagick PHP extension)
- Background queue: yeni ürün eklenince WebP variant otomatik üret
- `<picture>` markup theme'de (modification ile)

**Tahmini:** %30-50 image byte transfer

### V6. Tema asset modernization
- jQuery 3.x sadece (2.x kaldır)
- Slider lib'lerinden bir tanesi (Revolution / Swiper / Master / Layer) — sadece kullanılan kalsın
- Vue.js gereksizse kaldır (Quickview alternatif `<dialog>` element)
- Moment.js → `Intl.DateTimeFormat` veya `date-fns/tr` (51 KB → 5 KB)

**Tahmini:** ~500 KB JS bundle azaltma → mobile parse time -200-400 ms

### V7. HTTP/2 Push veya Resource Hints
- `<link rel="preload" as="style" href="...">` critical CSS için
- `<link rel="preload" as="font" href="...">` ana font için (mevcut header.twig:19 ✓)
- HTTP/2 Server Push (LiteSpeed destekli ama Chrome push'u kaldırdı 2022) — yerine `103 Early Hints`

### V8. Database query monitor + slow log
- VPS'te `slow_query_log = ON`, `long_query_time = 0.1`
- Production'da APM tool (mevcut sentry zaten loglıyor — DSN constant var bkz header.twig:35)

---

## Detaylı Bulgular Özet Tablosu

| # | Severity | Dosya:Satır | Kategori | Tahmini Kazanç |
|---|---|---|---|---|
| 1.1 | 🔴 | `catalog/model/catalog/product.php:202-204` | N+1 query | 150-300 ms |
| 1.2 | 🔴 | `catalog/model/catalog/product.php:8`, `model/journal3/filter.php:792-825` | Korelasyonlu subquery × 8 | 50-150 ms |
| 1.3 | 🔴 | `db/schema.sql` oc_product | Eksik index | 10-300 ms (skalalı) |
| 1.4 | 🔴 | `db/schema.sql` 161 MyISAM tablo | Engine | %20-40 query süresi |
| 1.5 | 🟠 | `oc_seo_url` lookup | URL resolution | 5-15 ms |
| 1.6 | 🟠 | `catalog/model/journal3/category.php:52-64` | Subquery × kategori | 20-50 ms |
| 1.7 | 🟠 | `catalog/model/journal3/product.php:132` | Limitsiz SELECT * | 5-20 ms |
| 1.8 | 🟠 | `catalog/model/catalog/product.php:109`, `filter.php:992` | LIKE '%x%' | 50-300 ms (skalalı) |
| 1.9 | 🟡 | `catalog/model/catalog/product.php:3-5` | Sync UPDATE viewed | 2-10 ms |
| 1.10 | 🟡 | `catalog/model/journal3/settings.php` | Settings query × 4 | 20-80 ms (cache yoksa) |
| 2.1.a | 🔴 | `catalog/view/javascript/jquery/datetimepicker/moment/tests.js` | 3.7 MB test dosyası | Disk + güvenlik |
| 2.1.b | 🔴 | `theme/journal3/lib/{revolution,masterslider,layerslider}/` | 3× slider lib | 1 MB disk |
| 2.1.c | 🔴 | İki jQuery kopyası | Duplicate | 85 KB |
| 2.1.d | 🔴 | `view/javascript/qnbpay/*.js` 181 KB | Her sayfa yüklenirse | 181 KB ↓ checkout-only |
| 2.1.e | 🟠 | Moment + tüm locale 800 KB | İhtiyaç fazlası | 750 KB ↓ |
| 2.2 | 🟠 | `header.twig:69-71` | Defer kontrolü gerek | 50-150 ms LCP |
| 2.3 | 🟠 | `header.twig:38` inline Journal | CSP + boyut | 10-30 KB HTML |
| 2.4 | 🟠 | `system/library/journal3/minifier.php` | Minifier off ise | 80 ms |
| 2.5 | 🟡 | `*.css.map`, `*.js.map` prod | Boşa yer | 700 KB |
| 3.1 | 🟡 | 149 CSS dosya | Minifier sayesinde tek bundle | Doğrulama |
| 3.2 | 🟠 | `style.min.css` 139 KB | Critical CSS yok | 100-200 ms LCP |
| 3.3 | 🟡 | Font Awesome 2 kopya | Duplicate | 40 KB |
| 4.1 | 🔴 | `catalog/model/tool/image.php:3-48` | 3 syscall × resize | 5-15 ms |
| 4.2 | 🔴 | `system/library/image.php` | GD + JPEG quality 90 | %30 byte WebP geçişle |
| 4.3 | 🟠 | `product_card.twig:23-34` | Native lazy + fetchpriority | LCP iyileştirme |
| 5.1 | 🔴 | `system/library/cache/file.php:6-25` | glob+unlink her __construct | 10-100 ms |
| 5.2 | 🔴 | `system/config/default.php:48` | cache_engine='file' | %15-25 (Redis) |
| 5.3 | 🔴 | `system/library/journal3/cache.php:98-115` | 10-boyutlu cache key | File backend'de kötü |
| 5.4 | 🟠 | `catalog/model/catalog/product.php:259-307` | Cache var ama N+1 | 1 saatte bir miss |
| 6.1 | 🔴 | `system/library/template/twig.php:10-40` | Environment her render | 50-200 ms |
| 6.2 | 🔴 | `system/config/default.php:50` + startup.php:44 | template_cache=false | ek 50 ms |
| 6.3 | 🟠 | `product_card.twig` × 20 ürün | settings.get loop | 1-3 ms |
| 6.4 | 🟠 | `header.twig:31, 40, 150, 154, 167` | loadController × N | 30-100 ms |
| 6.5 | 🟡 | `header.twig.bak-20260511` | Backup dosya prod | Hijyen |
| 7.1 | 🟠 | `system/journal3.ocmod.xml` 82 mod | OCMOD scan | 10-30 ms (opcache off) |
| 7.2 | 🟡 | `startup.php:90-97` 8× modification() | is_file × N | µs |
| 8.1 | 🟠 | `catalog/model/journal3/settings.php` 4 SELECT | Cache miss'te | 20-80 ms |
| 8.2 | 🟡 | `Settings::all()` / Arr::get | PHP overhead | <5 ms |
| 9.1 | 🔴 | `system/engine/event.php:38-49` | array_multisort × register | 5-20 ms startup |
| 9.2 | 🟠 | `system/engine/event.php:55-65` | preg_match × event count | 5-30 ms |
| 9.3 | 🟠 | `system/engine/registry.php` magic getter | Minimal | <1 ms |
| 9.4 | 🟠 | `system/engine/loader.php:79-87` | Proxy + closure | <5 ms |
| 9.5 | 🟠 | `framework.php:172` tek output buffer | TTFB | Mimari kısıt |

---

## Lighthouse Skor Tahmini (Mevcut + Düzeltmelerle)

### Sadece Quick Wins uygulansa (VPS gerek yok)

| Metric | Mevcut tahmini | QW sonrası tahmini |
|---|---|---|
| Performance | 35-55 | **55-70** |
| LCP | ~3-5s | ~2.5-3.5s |
| TBT | 800-1500 ms | 400-800 ms |
| CLS | ~0.1-0.3 | ~0.1-0.2 |

### Quick Wins + VPS (PHP 8.2 + Redis + InnoDB)

| Metric | Mevcut | + VPS |
|---|---|---|
| Performance | 35-55 | **75-85** |
| LCP | ~3-5s | **<2.0s** |
| TBT | 800-1500 ms | **<200 ms** |
| TTFB | 600-1200 ms | **<300 ms** |

### + Image WebP + Critical CSS + JS bundle modernization

| Metric | + asset reform |
|---|---|
| Performance | **85-95** |
| LCP | **<1.5s** |
| TBT | **<100 ms** |

---

## Ek Notlar

### Kod kaynaklı OLMAYAN ama önemli faktörler
- **Shared CloudLinux LVE limitleri:** PHP-FPM worker sınırı, CPU throttle → kod ne kadar optimize olsa da concurrency düşük kalır
- **NetInternet shared host:** TR'den TTFB tahmini 600-1200 ms — shared paketle alt sınır
- **Cloudflare yok:** Static asset CDN olarak Cloudflare Free + page rule cache → kod değişikliği olmadan %30-50 perceived perf

### Risk değerlendirmesi
Bu raporda önerilen değişikliklerin **çoğunluğu canlıda dokunmadan** (sadece admin paneli ayarları + .htaccess + Twig wrapper override) uygulanabilir. **Yüksek risk** olan:
- Cache engine 'file' → 'redis' (config.php değişikliği — rollback 1 dakika)
- MyISAM → InnoDB (online ALTER ama büyük tablolarda lock)
- system/library/template/twig.php rewrite (OCMOD compatibility test gerekir)
- system/library/cache/file.php rewrite (basit ama OpenCart core dosyası)

VPS'e geçtikten sonra staging environment'ta önce dene → production'a deploy.

### Doğrulama (Bu rapora göre değişiklik yaparsa)
```bash
# DB index doğrulama
mysql -e "SHOW INDEX FROM oc_product;"

# Asset minify doğrulama
ls -la /home/ravenden/public_html/catalog/view/theme/journal3/assets/

# Cache adaptör doğrulama
grep cache_engine /home/ravenden/public_html/config.php

# CSS render-blocking sayısı
curl -s https://ravendentalgroup.com/ | grep -oE '<link[^>]*rel="stylesheet"' | wc -l

# JS sayısı  
curl -s https://ravendentalgroup.com/ | grep -oE '<script[^>]*src="[^"]*"' | wc -l

# Total inline JS boyutu
curl -s https://ravendentalgroup.com/ | grep -oE '<script>[^<]*</script>' | wc -c
```

---

**Hazırlayan:** Performance Code Review
**Önceki adım:** Yerel kod kopyasının analizi
**Sonraki adım:** Lighthouse baseline ölçümü → bu rapora göre öncelik sırasıyla quick wins uygula → ölçümle doğrula
