# db/ — Database Dumps

> Yerel analiz için DB dump'ları. Hassas veriler git'e PUSH EDİLMEZ.

## Bu klasörde

| Dosya | Git'te? | İçerik |
|---|---|---|
| `schema.sql` | ✅ Evet | Sadece şema (CREATE TABLE) — 164 tablo, 64 KB |
| `seo_tables.sql` | ❌ Hayır (gitignore) | SEO tablolarının data dump'ı — 428 KB |
| `full.sql.gz` | ❌ Hayır (gitignore) | Tam dump (henüz alınmadı) |

## Neden `seo_tables.sql` gitignore?

Dump dosyasında **hassas veriler** var:
- `oc_setting` içinde `payment_qnbpay_token` (QNB Pay merchant token)
- (Olası) müşteri verileri eğer oc_customer dahil edildiyse
- Diğer entegrasyon API key'leri

## Dump nasıl alındı?

Üretim sunucuda **geçici PHP runner** (`/_r<random>.php`) deploy edildi, JSON RPC'ye `action=dump_schema` ve `action=dump_table` istekleri atıldı. Sonra runner silindi.

## Dump'ı yeniden almak

`docs/04-THEME-STRUCTURE.md` — "Geçici PHP Runner Pattern" bölümüne bak.

Veya cPanel UI'dan:
1. cPanel → phpMyAdmin
2. ravenden_1 DB → Export
3. Custom → Tabloları seç
4. SQL formatında indir

## schema.sql nasıl güncellenir

```bash
# Runner ile (yerel ortamda):
source ~/.config/raven/runner
curl -X POST -H "X-Runner-Token: $RUNNER_TOKEN" \
  --data "action=dump_schema" \
  "https://ravendentalgroup.com/$RUNNER_NAME" \
  | python3 -c "import sys,json; print(json.load(sys.stdin)['sql'])" \
  > db/schema.sql
```

## Önemli tablolar (DB özeti)

→ Detaylı: `docs/03-DATABASE-SCHEMA.md`

| Tablo | Satır | Açıklama |
|---|---|---|
| oc_setting | 213 | Site config (key/value) |
| oc_seo_url | 738 | SEO URL keyword'leri |
| oc_category | 18 | Kategoriler |
| oc_category_description | 36 | Kategori metinleri TR+EN |
| oc_product | 345 | Aktif ürünler |
| oc_product_description | 690 | Ürün metinleri TR+EN |
| oc_product_to_category | 669 | Ürün-kategori bağı |
| oc_information | 6 | Bilgi sayfaları |
| oc_information_description | 12 | Bilgi sayfası içerikleri |
| oc_journal3_setting | 56 | Journal3 tema ayarları |
| oc_language | 2 | TR (id=2) + EN (id=1) |
| oc_journal3_blog_post | ? | Blog yazıları (var, ama içerik üretilmemiş) |
| oc_order | ? | Siparişler (gerçek müşteri verisi) |
| oc_customer | ? | Müşteri kayıtları (KVKK!) |
