# Google Search Console Verification

## Durum: Bekleniyor

Site henüz Google Search Console'da mülk olarak kayıtlı değil.

## Adımlar

### Seçenek A: URL Prefix (hızlı, 5 dk)
1. Kullanıcı https://search.google.com/search-console/ → "Add property" → URL prefix
2. URL: `https://ravendentalgroup.com/`
3. Verification method: HTML tag → token kopyalanır
4. Token şuna benzer: `<meta name="google-site-verification" content="ABC123..." />`
5. Bu meta tag'i `oc_journal3_setting.customCodeHeader`'a ekle (insert_meta.py kullan)
6. Storage cache temizle + Google'da "Verify" bas

### Seçenek B: Domain Property (önerilen, ~30 dk DNS prop)
1. Kullanıcı GSC → "Add property" → Domain
2. Domain: `ravendentalgroup.com`
3. Verification method: DNS TXT
4. Token şuna benzer: `google-site-verification=ABC123...`
5. GoDaddy DNS panelinde:
   - Yeni TXT record ekle
   - Host: `@` (kök)
   - Value: `google-site-verification=ABC123...`
   - TTL: 1 saat
6. DNS yansıma 5-15 dk
7. GSC → "Verify" bas

## Hazır araç
- `insert_meta.py` — token verince customCodeHeader'a inject eder
- DNS adım için GoDaddy panel adresi: https://account.godaddy.com/products → Domain → DNS

## Verification sonrası

- Sitemap submit: `https://ravendentalgroup.com/index.php?route=extension/feed/google_sitemap`
- Coverage Report kontrolü (kaç URL indekslendi)
- URL Inspection ile özel sayfalar
- Bing Webmaster Tools'a da aynı şekilde ekle (TR pazarı için ek görünürlük)
