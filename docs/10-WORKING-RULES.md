# 10 - Working Rules (Çalışma Kuralları)

> Üretim sitesine her dokunuştan önce. Hata yapmamak için.

---

## ANA İLKE

> "Önce yerelde planla, yerel testte doğrula, üretime tek seferde uygula, doğrula, dokümante et."

Aceleyle, "deneyelim ne olur" yapan biri değiliz. Yaptığımız her şeyin nasıl geri dönüleceğini önceden biliyoruz.

---

## ÇALIŞMA AKIŞI (HER DEĞİŞİKLİK İÇİN)

### Adım 1: Yerelde anlamak

```
~/raven-dental/code/ içinde:
- İlgili dosyayı oku (cat / Read)
- Etkilenen yerleri ara (grep -r)
- Mevcut DB durumunu kontrol et (~/raven-dental/db/seo_tables.sql)
```

### Adım 2: Planı doc'a yaz

```
~/raven-dental/analysis/<görev-adı>.md
İçerik:
- Amaç: ne yapacağım, neden
- Etkilenen dosyalar / DB tabloları
- Adımlar (numaralı)
- Rollback yöntemi
- Doğrulama testleri
```

### Adım 3: SQL/komutu hazırla

- SQL'i önce `--SELECT COUNT(...)` ile etki sayısını gör
- Birden fazla query'yi `BEGIN ... COMMIT` blok yapma (cPanel runner desteklemiyor)
- Komutta yorumlu olarak yedek bilgisi yaz

### Adım 4: Üretimde uygula (TEK COMMIT)

- Runner deploy → komutu çalıştır → runner sil
- Veya cPanel UI'dan manuel (kullanıcı yapar)
- Hata olursa: STOP, doğrulama yap, rollback'e geç

### Adım 5: Doğrulama

```bash
# Site sağlık
curl -sI https://ravendentalgroup.com/ → 200

# Spesifik test (değişikliğe göre)
curl -s ... | grep ...

# DB doğrulama (gerekirse runner ile)
SELECT ... WHERE ...
```

### Adım 6: Dokümante et

- `docs/08-CHANGES-MADE.md`'ye ekle (NE + NEDEN + NASIL + DOĞRULAMA)
- İlgili spesifik doc'u güncelle (`05-SEO-STATUS.md` vb.)
- ROADMAP'te `[ ]` → `[x]` işaretle
- Git commit + push

---

## TEHLİKE BÖLGELERİ — Her Zaman Dikkat

### A. Storage Klasörü
**Sadece şunu silebilirsin:**
- `storage/cache/*` ✓
- `storage/logs/*` ✓ (eski log)

**ASLA silme:**
- `storage/modification/*` ❌ (Journal3 OCMOD cache — siteyi kırar)
- `storage/upload/*` ❌ (kullanıcı yüklemeleri)
- `storage/session/*` ❌ (aktif oturumlar)
- `storage/download/*` ❌ (dijital ürün)

### B. Theme Dosyaları
- Düzenleme öncesi yedek al: `<dosya>.bak-YYYYMMDD`
- Replace ile değiştir (rewrite YOK)
- Modification refresh **gerekebilir** (admin'den)

### C. DB Şifre Değişimi
- Şifreyi önce yerelde sakla (`~/.config/raven/env`)
- DB user şifresini değiştir
- **HEMEN ARDINDAN** config.php (her ikisi: root + admin) güncelle
- Test: site 200 OK mu?
- Sorun varsa: DB user şifresini ESKİ değere geri set + config geri al

### D. .htaccess
- Mutlaka yedek (`.htaccess.bak-YYYYMMDD`)
- LiteSpeed/Apache 500 verirse: hemen yedeği geri yükle
- Test komutu: `curl -sI / | head -3`

### E. Toplu DB Update
- 100+ satırlık UPDATE/DELETE → kullanıcıya sor
- WHERE koşulu yazmadan UPDATE/DELETE = OTOMATIK YASAK
- LIMIT yoksa pilot test yap

---

## KOMUTLAR

### Runner deploy (geçici PHP)

```bash
# Yeni token + isim üret
RUNNER_NAME="_r$(python3 -c 'import secrets; print(secrets.token_hex(10))').php"
RUNNER_TOKEN=$(python3 -c 'import secrets; print(secrets.token_urlsafe(28))')
```

→ Runner template: bkz [04-THEME-STRUCTURE.md "Geçici PHP Runner Pattern"]

### Runner sil (kullanım sonrası)
```bash
# Sunucudan
curl -X POST "https://${HOST}:2083/json-api/cpanel?..." \
  --data "op=unlink&sourcefiles=/home/ravenden/public_html/$RUNNER_NAME"

# Yerel state'den
rm -f ~/.config/raven/runner
```

### Cache temizleme (SADECE cache, modification DEĞİL)

```php
// PHP runner içinde:
$dir = '/home/ravenden/storage/cache/';  // <-- TEK BU
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $f) {
    if ($f->isFile()) @unlink($f->getRealPath());
}
// /home/ravenden/storage/modification/  ❌ ASLA
```

### Modification refresh (kullanıcı yapar)
```
1. OpenCart Admin'e gir
2. Eklentiler → Değişiklikler (Modifications)
3. Sağ üst → mavi yenile (↻) butonu
4. Storage/modification yeniden inşa edilir
```

---

## ANTI-PATTERN'LAR

| ❌ Yapma | ✅ Yap |
|---|---|
| "Belki şu işe yarar" | "Test et, kanıtla" |
| Çok satırlı PHP'yi heredoc'a koy | Write tool ile dosyaya yaz |
| Aceleyle force push | Pre-commit'te hassas dosya kontrolü |
| Şifreyi cat ile bastır | grep + sed ile maskele |
| 5 değişikliği aynı commit'te | Her mantıksal grup ayrı commit |
| "Cache temizle" deyip her şey sil | Sadece `storage/cache/` |
| Theme rewrite | Spesifik block replace |
| Lokal test atla | Lokalde önce dene |

---

## ACIL DURUM — Site Kırıldı

### Adım 1: Tespit
```bash
SIZE=$(curl -sSL https://ravendentalgroup.com/ | wc -c)
echo "Boyut: $SIZE byte"  # < 50000 = ÇOK KÜÇÜK = sorun var
```

### Adım 2: Hangi katman bozuk?
```bash
# .htaccess sorunu mu?
curl -sI https://ravendentalgroup.com/  # 500? → .htaccess yedek

# Modification mı?
curl -s https://ravendentalgroup.com/ | grep -c "data-jv="  # 0? → modification cache yok
```

### Adım 3: Hızlı rollback
- **.htaccess bozuk:** Yedek dosyadan geri yükle (API ile)
- **storage/modification boş:** Kullanıcıya "admin → modifications → refresh" söyle
- **config.php DB şifre uyumsuz:** config.php'yi düzelt (env'deki gerçek şifreyle)
- **OpenCart admin login olmuyor:** cPanel'den admin user şifre reset (oc_user tablosunda BCRYPT hash güncelle)

### Adım 4: Sonra dokümante et
- `09-LESSONS-LEARNED.md`'ye yeni `L0X` ekle
- `CLAUDE.md` "ASLA YAPILMAYACAK" listesine güncelle

---

## OTURUM SONU PROTOKOLÜ

Her oturumun sonunda:
1. ✅ Geçici PHP runner'ları sunucudan silinmiş mi?
2. ✅ `08-CHANGES-MADE.md` bu oturumun değişiklikleriyle güncel mi?
3. ✅ ROADMAP'te `[ ]` → `[x]` işaretler doğru mu?
4. ✅ Yeni hata varsa `09-LESSONS-LEARNED.md`'de mi?
5. ✅ Git commit + push yapıldı mı?
6. ✅ `00-QUICK-CONTEXT.md` "şimdi" satırı güncel mi?

---

## ÇALIŞMAYAN HER ŞEYİ KAYDET

"İşe yaramadı, deneme tahtası" tarzı işler de `09-LESSONS-LEARNED.md`'ye yazılır.
Bir sonraki session aynı yola girmesin.

Örnek: "j3.settings.get('journal3_home_h1') oc_setting'i okumuyor — oc_journal3_setting kullan" gibi.
