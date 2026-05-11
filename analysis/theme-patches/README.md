# Theme Patches — H1 ve OG Title Düzeltmeleri

> OCMOD XML formatında — Journal3 update'ine karşı dirençli.
> Admin → Extensions → Modifications → Refresh deyince devreye girer.

## Sorunlar

### 1. og:title hâlâ "Raven Dental"
**Kaynak:** `catalog/controller/journal3/seo.php:261`
```php
'title' => $this->config->get('config_name'),  // ← Raven Dental
```

Anasayfada og:title = config_name kullanılıyor (config_meta_title değil).

**Fix:** `config_meta_title` öncelik, yoksa `config_name`.

### 2. H1 sr-only hâlâ "Raven Dental"
**Kaynak:** `catalog/view/theme/journal3/template/common/header.twig`
```twig
{% set raven_h1 = j3.settings.get('journal3_home_h1') ?: ... %}
```

`j3.settings.get('journal3_home_h1')` Journal3 fallback chain ile config_name dönüyor — ?: tetiklenmiyor.

**Fix:** j3.settings bypass — doğrudan heading_title veya title kullan.

## Çözüm Stratejisi — OCMOD XML

`system/raven.ocmod.xml` dosyası oluşturulur. Bu dosya OpenCart modification engine tarafından `journal3.ocmod.xml` ile birlikte parse edilir, hedef dosyalara değişiklikler uygulanır.

**Avantajları:**
- Theme/Journal3 update'leri orijinal dosyaları değiştirir → bizim XML değişikliklerimiz storage/modification altında kalır, etkilenmez
- Tek yerden yönetim
- Admin'den refresh ile yenilenir

**Uygulama Adımları:**
1. `system/raven.ocmod.xml` dosyasını sunucuya yükle
2. Admin → Extensions → Modifications → Refresh (mavi ↻)
3. Doğrulama: HTML kontrolü, H1 ve og:title yeni değerleri gösteriyor mu

## Patched File

`raven.ocmod.xml` — bu klasördeki XML dosyası

## Test Senaryoları

### Pozitif test
```bash
curl -sL https://ravendentalgroup.com/ | grep -E '<h1|og:title'
# Beklenen:
#   <h1 class="sr-only" ...>Diş Hekimliği Aletleri ve Cerrahi Ekipmanlar - Raven Dental</h1>
#   <meta property="og:title" content="Diş Hekimliği Aletleri ve Cerrahi Ekipmanlar | Raven Dental">
```

### Kategori sayfası
```bash
curl -sL https://ravendentalgroup.com/diagnostik-aletleri | grep -E '<h1|og:title'
# Beklenen:
#   <h1>Diagnostik Diş Aletleri - Muayene ve Tanı Setleri</h1>
#   <meta property="og:title" content="DIAGNOSTICS">  (kategori adı — OpenCart default)
```

### Rollback
```bash
# system/raven.ocmod.xml dosyasını sil
# Admin → Modifications → Refresh
# Eski davranışa geri döner
```

## Risk

| Risk | Olasılık | Etki |
|---|---|---|
| OCMOD XML syntax hatası → modification refresh hata verir | Düşük | Düşük (admin error gösterir, site etkilenmez) |
| Search string'i ileride değişirse OCMOD patch uygulanmaz | Orta (Journal3 update) | Düşük (XML rebuild işe yaramaz, eski davranış döner) |
| Heading_title undefined olduğu sayfalarda H1 boş kalır | Düşük | Düşük (else fallback ile garanti) |

## Faz 2 — Daha İyi Çözüm

Bu OCMOD geçici. Uzun vadeli:
- OpenCart'ın `controller/common/header.php` veya `controller/journal3/header.php` controller'ına özel logic eklenebilir
- Tema seviyesinde `customCodeFooter` ile JavaScript injection (H1'i DOM'da değiştir) — daha kötü pratik, **YAPMA**
