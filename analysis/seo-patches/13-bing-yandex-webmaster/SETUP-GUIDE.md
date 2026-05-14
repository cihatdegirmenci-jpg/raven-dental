# Bing Webmaster Tools + Yandex Webmaster Setup

## Neden iki ekstra?

- **Bing**: TR pazarında %3-5 arama payı + Microsoft Copilot/ChatGPT-Bing aramaları (LLM trafiği için kritik)
- **Yandex**: TR'de %1-3 ama Rusya/Türk dijital alanı için ek görünürlük

## Bing Webmaster Tools

### Yöntem 1 — GSC'den import (en hızlı, 1 dk)
1. https://www.bing.com/webmasters/ → "Sign in"
2. Microsoft hesabı ile giriş (cihat.degirmenci@onla.com.tr veya kişisel Outlook hesabı; Microsoft Live ile birleşmiş olabilir)
3. "Import sites from Google Search Console" → Google hesabını authorize et → ravendentalgroup.com seç → tek tıkla import edilir
4. Bing doğrulama otomatik aktarılır — DNS gerekmez

### Yöntem 2 — Manuel DNS TXT
1. "Add site" → URL: `https://ravendentalgroup.com/`
2. Verify method: DNS → token ver (örn. `BingSiteAuth.xml`'in içeriği bir token)
3. GoDaddy DNS panelinde yeni TXT eklenir:
   ```
   Type: TXT
   Host: @
   Value: bingverifications=ABCxxx...
   TTL: 3600
   ```
4. Bing'de "Verify"

### Sonrasında
- Sitemap submit (aynı URL: `https://ravendentalgroup.com/index.php?route=extension/feed/google_sitemap`)
- Bing'in **IndexNow** protokolünü aktive et — anlık indeksleme (Bing + Yandex desteklenir)

## Yandex Webmaster

1. https://webmaster.yandex.com/ → Sign up (Yandex hesabı / email ile)
2. "Add site" → URL
3. Verify: DNS TXT (önerilen) veya HTML meta tag
4. Token formatı: `yandex-verification: 1234567890abcdef` (DNS) veya meta tag
5. GoDaddy'de TXT ekle:
   ```
   Type: TXT
   Host: @  
   Value: yandex-verification: 1234567890abcdef
   ```
   (NOT: yandex `=` değil `:` kullanır — formatta dikkat)
6. "Verify"

### Sonrasında
- Sitemap submit
- Yandex'in geolocation seçimi: Türkiye

## IndexNow Protocol (anlık indeksleme)

Bing + Yandex destekler. OpenCart için extension yok ama basit cron veya manuel ping ile çağrılabilir.

Endpoint: `https://api.indexnow.org/indexnow?url={URL}&key={KEY}`

OpenCart için: yeni ürün eklendiğinde / kategori güncellendiğinde admin'den manuel ping at, sonra otomasyon eklenir.

## Karşılaştırma checklist

| Engine | Hesap | Sitemap | IndexNow | Geo (TR) |
|---|---|---|---|---|
| Google | ✅ 2026-05-12 | [ ] Bekliyor | ❌ Destek yok | (auto) |
| Bing | [ ] Bekliyor | [ ] Bekliyor | ✅ Destek var | (set TR) |
| Yandex | [ ] Bekliyor | [ ] Bekliyor | ✅ Destek var | (set TR) |

## Sonraki adım

Kullanıcı:
1. Bing Webmaster'a giriş yap, GSC import ile 1 dk'da kurulum
2. Yandex Webmaster'a kayıt + DNS TXT
3. Token gerekirse bana ver, customCodeHeader'a meta inject edebilirim ama DNS TXT zaten kuruludur, manuel ekleyebilirsin
