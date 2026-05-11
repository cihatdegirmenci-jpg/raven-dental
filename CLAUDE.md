# CLAUDE.md — Raven Dental Çalışma Kuralları

> Her oturumda **ilk iş bu dosyayı okumak**.
> Proje: ravendentalgroup.com — OpenCart 3.0.3.8 + Journal3 e-ticaret
> Hedef: SEO + güvenlik + performans iyileştirme, sonra KVM VPS'e taşıma

---

## ZORUNLU SIRA — Yeni İşe Başlamadan Önce

1. **`docs/00-QUICK-CONTEXT.md` oku** — nerede kaldığımızı hatırla
2. **`docs/09-LESSONS-LEARNED.md` oku** — geçmiş hataları tekrarlama
3. **`docs/12-ROADMAP.md` oku** — sıradaki iş ne
4. **İlgili spesifik doc'u oku** (ör. theme değişikliği = `04-THEME-STRUCTURE.md`)

İlk 3'ü atlamak YASAK. "Kısa zaman, sonra okurum" YOK.

---

## ASLA YAPILMAYACAKLAR (CANLI ÜRETİMDE)

### 1. Storage/modification klasörünü silme
**Sebep:** Journal3'ün modification engine cache'i burada — sildiğinde site sadece 3 KB HTML döndürür, kullanıcı admin'den manuel refresh yapmak zorunda kalır.

**Yasak:**
```bash
rm -rf /home/ravenden/storage/modification/*  # ASLA
```

**Cache temizleme yaparken:** SADECE `/home/ravenden/storage/cache/` klasörü içeriği silinir. Modification klasörüne dokunulmaz.

### 2. Tek seferlik destruct işlemleri onaysız
- `DROP TABLE`, `DROP DATABASE` — onay yokken YOK
- `TRUNCATE` — onay yokken YOK
- Toplu `DELETE` (> 100 satır) — onay yokken YOK
- `git push --force` — sadece ilk commit veya onaylı durumda
- `rm -rf` herhangi bir storage/ alt klasöründe — YOK

### 3. Şifre/Token chat'e yapıştırma
Hassas bilgiler:
- cPanel API token
- DB user şifresi  
- Admin şifresi
- SSH private key içeriği

Tümü `~/.config/raven/env` (chmod 600) dosyasında. Komutlar değişkenleri buradan okur, **chat çıktısına yazılmaz**.

Yanlışlıkla yazılırsa: **DERHAL rotate**.

### 4. Doğrudan üretim DB UPDATE/DELETE testsiz
Önce:
1. **Yerelde `~/raven-dental/code/`'da analiz et**
2. SQL'i `~/raven-dental/analysis/` altına yaz
3. SELECT ile etki sayısını gör (`SELECT COUNT(*) WHERE ...`)
4. Sonra UPDATE/DELETE çalıştır
5. Doğrulama: tekrar SELECT + dışarıdan HTML test

### 5. Theme dosyalarını "uçtan uca rewrite"
Tema dosyaları (`catalog/view/theme/journal3/*`) düzenlerken:
- ✅ Spesifik bir block'u değiştir (find + replace)
- ❌ Tüm dosyayı yeniden yazma
- ✅ Önce yedek al (`header.twig.bak-YYYYMMDD`)
- ✅ Değişiklik sonrası **modification refresh** gerekli olabilir (Journal3 cache'i bu dosyaları yeniden işler)

---

## GÜVENLİ ÇALIŞMA AKIŞI

### Bir değişiklik yapmadan önce:

```
1. Yerel kopyayı kontrol et (~/raven-dental/code/)
2. Hangi dosyalar etkilenecek? — sayı + yol listesi
3. Doc'a plan yaz (~/raven-dental/migration-plan/ veya docs/)
4. SQL/komut'u önce yorum satırı olarak hazırla
5. Yerel testi yap (mümkünse)
6. Tek bir değişikliği at, sonucu doğrula
7. docs/08-CHANGES-MADE.md'ye ekle
```

### Bir hata yaparsam:

```
1. PANIKLEMEDEN: kullanıcıya net bilgi ver — ne oldu, etki ne
2. Rollback yolunu söyle (yedekten dön / DB revert / vb.)
3. Hatayı docs/09-LESSONS-LEARNED.md'ye yaz
4. Aynı hatayı bir daha YAPMA
```

---

## DOCS CANLI TUTMA (ZORUNLU)

Her kod/DB/config değişikliğinin ardından aşağıdaki tabloyu kontrol et. Etkilenen doc varsa commit ÖNCESİ güncelle.

| Değişiklik | Güncellenecek doc |
|---|---|
| Yeni DB değişikliği | `03-DATABASE-SCHEMA.md` |
| Yeni tema/twig düzenleme | `04-THEME-STRUCTURE.md` |
| Yeni SEO ayarı/kural | `05-SEO-STATUS.md` |
| Yeni güvenlik düzeltmesi | `06-SECURITY-STATUS.md` |
| Performans değişikliği | `07-PERFORMANCE.md` |
| **Her dokunulan üretim** | `08-CHANGES-MADE.md` (zorunlu) |
| Yeni hata/ders | `09-LESSONS-LEARNED.md` |
| ROADMAP'te `[ ]` → `[x]` | `12-ROADMAP.md` |

---

## STOP CONDITIONS

Aşağıdaki durumlarda DUR ve kullanıcıya sor:

- Toplu DELETE/UPDATE > 100 satır
- `storage/`, `system/`, `vendor/` altında dosya silme
- Tema dosyası rewrite (replace değil)
- Admin/DB şifresi değişikliği
- `.htaccess` major rewrite
- VPS migration'da DNS switch
- "Belki şu işe yarar" tarzı tahminle değişiklik

---

## PROJE PROFİLİ

| | |
|---|---|
| **Domain** | ravendentalgroup.com |
| **Platform** | OpenCart 3.0.3.8 |
| **Tema** | Journal3 v3.1.12 |
| **Pazar** | TR B2B diş hekimliği aletleri |
| **Hosting (mevcut)** | NetInternet shared (host110) |
| **Hosting (hedef)** | NetInternet KVM VPS (henüz alınmadı) |
| **Diller** | TR (id=2), EN (id=1) |
| **Ürün** | 345 aktif |
| **Kategori** | 18 (4 ana + 14 alt) |
| **PHP** | 7.4.33 (EOL ⚠️ — VPS'te 8.2'ye çıkacak) |
| **MySQL** | 8.0.46 |
| **Web server** | LiteSpeed |
| **SSH** | YOK (shared paket, noshell account) |
| **API erişim** | cPanel API token (`~/.config/raven/env`) |

---

## CONTEXT (Projeyi Anlamak)

- B2B diş hekimliği aletleri satış sitesi (diş hekimleri, klinikler hedef)
- Branşlar: Diagnostik, Endodonti, Ortodonti, Protez, İmplantoloji, Periodonti, Cerrahi, Çekme (Extraction), Restorasyon, İşlem
- 4 ana kategori: El Aletleri, Elektronik, Sarf, Raven Cerrahi
- Özel ödeme modülü: QNB Pay (geliştirici: bolkarco — güvenlik review gerek)
- Site sahibi: Cihat Değirmenci (cihat.degirmenci@onla.com.tr)
- Onla şirketi altında — paralel başka projeler de var (Transverra: fintech)
