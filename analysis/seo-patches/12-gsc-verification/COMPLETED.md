# GSC Verification — TAMAM (2026-05-12)

Domain property doğrulandı, DNS TXT yöntemi.

## Aktif Verification

```
Type: TXT
Host: @ (ravendentalgroup.com)
Value: google-site-verification=BsHIv3gSngmRwlPgP9xZ2L-0QwrtotgzPdloavm-o1w
TTL: 3600
```

## Doğrulama
```bash
dig +short ravendentalgroup.com TXT
# → "google-site-verification=BsHIv3gSngmRwlPgP9xZ2L-0QwrtotgzPdloavm-o1w"

dig @8.8.8.8 +short ravendentalgroup.com TXT
# Google'ın resolver'ından da görünür ✓
```

## Sıradaki adımlar (GSC içinde, kullanıcı eylemi)
1. ✅ Property added
2. ✅ DNS TXT verified
3. [ ] Sitemap submit: `https://ravendentalgroup.com/index.php?route=extension/feed/google_sitemap`
4. [ ] URL Inspection: home, top 3 category, 1-2 product
5. [ ] 24-48 saat sonra: Coverage Report kontrol et
6. [ ] Performance > Search results: birkaç gün sonra trafik datası gelir
7. [ ] Enhancements > Core Web Vitals (mobile + desktop)
8. [ ] Enhancements > Structured Data (Product, Organization, BreadcrumbList)
