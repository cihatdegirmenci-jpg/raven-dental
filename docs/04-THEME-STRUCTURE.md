# 04 - Theme Structure (Journal3 İç Yapısı)

> Journal3 v3.1.12 — DigitalAtelier (premium OpenCart tema).
> 148 Twig template, 57 controller, 82 OCMOD modification.

---

## Üst Seviye Yapı

```
public_html/catalog/view/theme/journal3/
├── template/
│   ├── common/              ← Header, footer, breadcrumb, vb.
│   │   ├── header.twig      ← ⚠️ DÜZENLENDİ (H1 chain)
│   │   ├── footer.twig
│   │   ├── home.twig
│   │   ├── breadcrumb.twig
│   │   └── ...
│   ├── product/
│   │   ├── category.twig
│   │   ├── product.twig     ← Ürün detay sayfası
│   │   └── ...
│   ├── account/             ← Login, register, my account
│   ├── checkout/            ← Sepet, checkout
│   ├── information/         ← Bilgi sayfaları
│   └── journal3/            ← Journal3-özel template'ler
│       ├── headers/
│       │   ├── desktop/     ← Header type'ları (classic, modern, vb.)
│       │   └── mobile/
│       ├── product/
│       ├── blog/
│       └── ...
│
├── stylesheet/              ← CSS (compiled from less/scss)
├── icons/                   ← icomoon font (4 MB selection.json)
└── image/                   ← Tema görselleri

public_html/catalog/controller/journal3/  ← 57 controller
├── header.php
├── seo/
│   ├── meta_tags.php        ← og:title, twitter:card kaynak
│   ├── rich_snippets.php    ← Schema.org JSON-LD
│   └── ...
├── blog/                    ← Blog modülü
└── ...

public_html/system/library/journal3/  ← Framework
├── document.php
├── cache.php
├── build.php
├── browser.php
└── ...
```

## Önemli Dosyalar

### header.twig (catalog/view/theme/journal3/template/common/)
**Görev:** HTML `<head>` ve `<body>`'nin başlangıcı.

**Yapı:**
```twig
<!DOCTYPE html>
<html dir="..." lang="..." class="...">
<head>
  ...
  <title>{{ title }}</title>
  
  {% if description %}<meta name="description" content="{{ description }}" />{% endif %}
  {% if keywords %}<meta name="keywords" content="{{ keywords }}" />{% endif %}
  
  {# Journal3 controller'dan gelen Open Graph + Twitter Card #}
  {% for key, tag in j3.loadController('journal3/seo/meta_tags') %}
    <meta {{ tag.type }}="{{ key }}" content="{{ tag.content }}"/>
  {% endfor %}
  
  ...
  
  {# customCodeHeader injection — BURADA hreflang + custom schema #}
  {% if j3.settings.get('customCodeHeader') %}
    {{ j3.settings.get('customCodeHeader') }}
  {% endif %}
</head>
<body>
  {# H1 — BİZİM DÜZENLEMEMIZ #}
  {% set raven_h1 = j3.settings.get('journal3_home_h1') ?: (heading_title is defined and heading_title ? heading_title : title) %}
  {% if raven_h1 %}
    <h1 class="sr-only" ...>{{ raven_h1 }}</h1>
  {% endif %}
  
  ...
</body>
```

⚠️ **H1 sorunu:** `j3.settings.get('journal3_home_h1')` config_name'e fallback yapıyor, "Raven Dental" döndürüyor. Bizim ?: zinciri aktive olmuyor. **Nihai çözüm:** j3.settings'i bypass et, doğrudan koşula çevir.

**Önerilen düzeltme (henüz uygulanmadı):**
```twig
{# Always use page-specific heading or computed home text #}
{% if home is defined and home %}
  <h1 class="sr-only" ...>Diş Hekimliği Aletleri ve Cerrahi Ekipmanlar - Raven Dental</h1>
{% elseif heading_title is defined and heading_title %}
  <h1 class="sr-only" ...>{{ heading_title }}</h1>
{% else %}
  <h1 class="sr-only" ...>{{ title }}</h1>
{% endif %}
```

### journal3/seo/meta_tags.php (controller)
**Görev:** og:* ve twitter:* meta tag üretimi.

OG title kaynak:
```php
// Tahmini içerik (incelenecek):
$og_title = $this->config->get('config_name');  // ← "Raven Dental" buradan
// Olması gereken:
$og_title = $document->getTitle();  // veya meta_title
```

→ **Bu controller'ı incelemek gerek.** Code review TODO'da.

### journal3.ocmod.xml (system/)
**Görev:** OpenCart core dosyalarına Journal3 modifikasyonları uygulamak.

- 2175 satır
- 82 `<file path="...">` modifikasyonu
- Etkilenen dosyalar:
  - `system/library/cache/file.php`
  - `system/engine/front.php`, `router.php`
  - `system/library/template/twig.php`
  - `catalog/controller/*` (birçoğu)
  - `catalog/view/theme/*/template/*` (birçoğu)

**Modifikasyon türleri:**
- `<add position="replace">` — değiştir
- `<add position="before">` — önüne ekle
- `<add position="after">` — arkasına ekle
- `<search regex="true">` — regex match

**ÖRNEK (ilk modifikasyon):**
```xml
<file path="system/library/cache/file.php">
  <operation>
    <search><![CDATA[unlink($file);]]></search>
    <add position="replace"><![CDATA[@unlink($file);]]></add>
  </operation>
</file>
```
Sebep: cache silme warning'lerini bastırmak.

### modification.xml (system/)
**Görev:** OpenCart 3.x'in standart modification.xml — core require/include'ları modification function'la sarmak.

## Storage/Modification — Önemli Konsept

```
public_html/system/journal3.ocmod.xml
            │
            │ OpenCart modification engine
            │ (admin → Extensions → Modifications → Refresh)
            ▼
storage/modification/
  ├── system/        ← Modified core files
  ├── catalog/       ← Modified controller/view files
  └── admin/         ← Modified admin files

[Runtime]
OpenCart bir dosya yüklerken:
  modification($path) → storage/modification/$path varsa ONU yükle,
                        yoksa ORIJINAL $path'i yükle
```

⚠️ **ASLA `storage/modification/` silme** — Journal3 patches kaybolur, site bozulur.
✅ **Refresh:** OpenCart Admin → Extensions → Modifications → mavi ↻ butonu.

## Journal3 Setting Sistemleri

Journal3'ün **3 setting sistemi** var:

### 1. oc_setting (OpenCart standart)
- Genel OpenCart ayarları (config_name, config_meta_title vb.)
- Journal3 bunu **fallback** olarak kullanır (örn. H1)

### 2. oc_journal3_setting
- Journal3'e özel ayarlar
- 56 satır
- setting_group: `general`, `seo`, `blog`, `custom_code`, `active_skin`, `dashboard`

**Önemli setting'ler:**
| Group | Name | Bizim için |
|---|---|---|
| custom_code | customCodeHeader | ⭐ hreflang + Org schema buraya ekledik |
| custom_code | customCodeFooter | (boş — GA4 buraya gelecek) |
| custom_code | customCSS | (boş) |
| custom_code | customJS | (boş) |
| seo | seoOpenGraphTagsStatus | "true" |
| seo | seoTwitterCardsStatus | "true" |
| seo | seoGoogleRichSnippetsStatus | "true" |
| seo | seoTwitterCardsTwitterUser | "" |

### 3. oc_journal3_skin_setting
- Aktif skin'e bağlı ayarlar
- skin_id=1
- Çoğu visual: renkler, font, layout

## Theme Edit Yapma Kuralları

### Spesifik edit
```bash
# 1. Önce yedek
cp header.twig header.twig.bak-YYYYMMDD

# 2. Spesifik block'u replace et (rewrite YOK)
python3 -c "
content = open('header.twig').read()
old = '...exact block...'
new = '...new block...'
assert old in content, 'eski block bulunamadı'
open('header.twig', 'w').write(content.replace(old, new))
"

# 3. Modification refresh GEREKLİ olabilir
# admin → Extensions → Modifications → Refresh
```

### CustomCodeHeader/Footer ekleme (theme dosyası dokunmadan)
```sql
UPDATE oc_journal3_setting 
SET setting_value = 'HTML İÇERİĞİ' 
WHERE setting_group='custom_code' 
  AND setting_name='customCodeHeader';
```
Avantajı: theme update gelse bile kaybolmaz, modification refresh gerekmez.

## Geçici PHP Runner Pattern (DB Erişim)

Production'a SSH yokken DB sorgu çalıştırmak için:

```php
<?php
// _r<random>.php (geçici, kullanıp sil)
$expected = '<32-char-secret-token>';
if (!hash_equals($expected, $_SERVER['HTTP_X_RUNNER_TOKEN'] ?? '')) {
    http_response_code(403); exit('forbidden');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }

require __DIR__ . '/config.php';
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=".DB_HOSTNAME.";dbname=".DB_DATABASE.";charset=utf8mb4", 
                   DB_USERNAME, DB_PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Throwable $e) { http_response_code(500); exit(json_encode(['e'=>$e->getMessage()])); }

$action = $_POST['action'] ?? '';
if ($action === 'query') {
    try {
        $s = $pdo->query($_POST['sql']);
        if ($s && $s->columnCount() > 0) 
            echo json_encode(['ok'=>1, 'd'=>$s->fetchAll(PDO::FETCH_ASSOC)], JSON_UNESCAPED_UNICODE);
        else 
            echo json_encode(['ok'=>1, 'd'=>['affected' => $s ? $s->rowCount() : 0]]);
    } catch (Throwable $e) {
        echo json_encode(['ok'=>0, 'e'=>$e->getMessage()]);
    }
}
```

**Kullanım:**
```bash
# Deploy
RUNNER_NAME="_r$(python3 -c 'import secrets; print(secrets.token_hex(10))').php"
RUNNER_TOKEN=$(python3 -c 'import secrets; print(secrets.token_urlsafe(28))')
# ... PHP'ye token göm, cPanel API ile yükle ...

# Query
curl -X POST -H "X-Runner-Token: $RUNNER_TOKEN" \
  --data-urlencode "action=query" \
  --data-urlencode "sql=SELECT ..." \
  "https://ravendentalgroup.com/$RUNNER_NAME"

# Sil (her zaman!)
curl -X POST "https://.../json-api/cpanel?...&op=unlink&sourcefiles=/home/.../$RUNNER_NAME"
```

⚠️ **Asla runner'ı sunucuda BIRAKMA.** Token leak = DB execute risk.

## Tema Build Sistemi (Journal3)

Journal3'ün kendine has build sistemi var:
- `system/library/journal3/build.php`
- Less/Scss → CSS compilation
- JS bundling
- Admin'den "Rebuild" butonu

**Ne zaman tetiklenir:**
- Tema setting değişimi (admin'den)
- Skin değiştirildiğinde
- Manuel rebuild butonu

**Çıktı:**
- `catalog/view/theme/journal3/stylesheet/*.css` (compiled)
- `admin/view/javascript/journal3/dist/journal.js` (bundled, 4.8 MB)
