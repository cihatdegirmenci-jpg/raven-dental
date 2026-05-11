# QNB Pay — Güvenlik Yamaları

> **Durum:** Yerelde HAZIRLANDI, **üretime UYGULANMADI**.
> Her yama bağımsız test edilip onaylandıktan sonra production'a deploy edilebilir.

## Yama Listesi

| # | Severity | Bulgu | Yama dosyası | Durum |
|---|---|---|---|---|
| 01 | 🔴 CRITICAL | validation() hash kontrolü devre dışı | [01-validation-hash-fix.md](./01-validation-hash-fix.md) | ✅ Hazır |
| 02 | 🔴 CRITICAL | deletemycard() HTTP_REFERER XSS | [02-deletemycard-xss-fix.md](./02-deletemycard-xss-fix.md) | ✅ Hazır |
| 03 | 🟠 HIGH | recurringCancel() boş method | [03-recurring-cancel-fix.md](./03-recurring-cancel-fix.md) | ⚠️ İskelet (QNB API spec gerek) |

## Uygulama Stratejisi

### Ön Koşul
- [ ] **VPS'e taşıma tamamlanmış olsun** — yeni VPS'te test ortamı kolay (test.ravendentalgroup.com)
- [ ] Veya en azından **yerel OpenCart kurulumu** ile test (sandbox QNB)
- [ ] Mevcut `qnbpay.php` (controller ve library) **yedeklenmiş** (`.bak-YYYYMMDD`)

### Sıralı Uygulama (öneri)

1. **Yamayı yerelde uygula** (`analysis/qnb-patches/patched/` → `code/` over-write)
2. **OpenCart admin → Modifications → Refresh**
3. **Test:** Bir sandbox sipariş aç, ödeme akışı tamamla
4. **Smoke:** 200 OK, hata yok, kart bilgisi doğru gidiyor
5. **Saldırı PoC:** Aynı patch'in çözdüğü saldırıyı dene — başarısız mı?
6. **Üretime deploy:** rsync veya File Manager ile

### Geri Dönüş Planı

Her yama için yedek dosyası (`qnbpay.php.bak-YYYYMMDD`) kullanılır:
```bash
# VPS'te
cp catalog/controller/extension/payment/qnbpay.php.bak-YYYYMMDD \
   catalog/controller/extension/payment/qnbpay.php
# OCMOD refresh
```

## Çakışan/Bağımlı Dosyalar

Her yama TEK bir method'u etkiliyor — birbirinden bağımsız uygulanabilir. Çakışma yok.

| Yama | Etkilediği Method | Dosya |
|---|---|---|
| 01 | `validation()` | catalog/controller/extension/payment/qnbpay.php |
| 02 | `deletemycard()` | catalog/controller/extension/payment/qnbpay.php |
| 03 | `recurringCancel()` | catalog/controller/extension/payment/qnbpay.php |

## Tam Patched Dosyaları

`analysis/qnb-patches/patched/` altında **3 yama uygulanmış** versiyonlar:
- `patched/catalog/controller/extension/payment/qnbpay.php`

İstersen tek tek de uygulayabilirsin (her .md dosyasında diff'i var).

## Bolkarco'ya Sunulacak Versiyon

bolkarco'ya bu yamaları gönderirken:
- Tüm 3 yamayı tek email/PR'da paylaş
- "QNB resmi modülünden gelen miras açıklar" açıklaması ekle
- Onun "DÜZELTME 1-3" düzeltmelerine atıfta bulun (saygı)
- Soru sor: "QNB Pay'in resmi modülü neden hashControl=0 ile geliyor? Bilinen bir kısıtlama mı?"

## Kapsam Dışı

Bu yama paketi **3 kritik açığı** kapatıyor. Geriye kalan:
- 🟠 HIGH #4 Webhook token URL'de (HMAC header'a geçiş) — QNB tasarımına bağlı
- 🟠 HIGH #6 Webhook recurring trust — küçük edit
- 🟠 HIGH #7 paymentid `random_int()` — küçük edit
- 🟠 HIGH #8 deletemycard IDOR — QNB API doğrulamasına bağlı
- 🟡 MEDIUM #9 CSRF token — sistem geneli
- 🟡 MEDIUM #10 cardOwner sanitize — view'lerde
- 🟡 MEDIUM #11 debug mode hardening

Bunlar Faz 2-ileri zamanda yapılacak.
