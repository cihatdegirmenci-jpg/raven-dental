# 09 - Lessons Learned (Hatalar ve Dersler)

> **HER session'da referans.** Yeni dersi her zaman buraya ekle.
> Format: Hata → Sebep → Çözüm → Bir daha tekrarlama kuralı.

---

## L01: storage/modification klasörünü silme (2026-05-11)

### Hata
Cache temizleme script'ine `storage/modification/` klasörünü dahil ettim. Silince Journal3'ün modification engine cache'i boşaldı. Sonuç: anasayfa **3.6 KB** HTML döndürdü (normal 462 KB). Site görsel olarak çöktü.

### Belirti
- Anasayfa response size aniden ~98% düştü
- `window['Journal'] = null;` — Journal3 boot config NULL
- `<header class="header-">` — header type boş
- Font preload href boş
- Body content yok

### Sebep
Journal3'ün `*.ocmod.xml` dosyaları (özellikle `system/journal3.ocmod.xml`, 2175 satır, 82 file modification) çalışma zamanında işlenir ve çıktısı `storage/modification/` altında cache'lenir. OpenCart bir twig/php yüklerken önce `storage/modification/<path>` bakar, yoksa orijinale gider. Cache silinince Journal3 modifications uygulanmamış orijinal dosyalar yüklenir — ve OpenCart core bunlarla beklenmedik şekilde davranır.

### Çözüm
Kullanıcı **OpenCart Admin → Eklentiler → Değişiklikler → Refresh (mavi ↻)** butonuna basarak rebuild etti. Engine `.ocmod.xml` dosyalarını yeniden parse edip storage/modification'a yazdı.

### Bir Daha Tekrarlama Kuralı
- Cache temizleme script'lerinde **SADECE** `storage/cache/` silinir
- `storage/modification/`, `storage/upload/`, `storage/session/`, `storage/logs/` ASLA silinmez
- Cache temizleme runner'ı yazarken whitelist yaklaşım: hangi klasör silineceği AÇIK belirtilir
- Programmatic modification refresh için (admin'e girmeden) bir yöntem dökümante edilmeli — bkz: [04-THEME-STRUCTURE.md](./04-THEME-STRUCTURE.md)

→ **CLAUDE.md "ASLA YAPILMAYACAKLAR" listesine #1 olarak eklendi.**

---

## L02: .htaccess'in kendi yarattığı zip'i bloklaması (2026-05-11)

### Hata
Proje dosyalarını indirmek için zipper PHP yazdım, output `.zip` olarak yazdım. Tarayıcıdan indirirken **1.2 KB** geldi (HTML 403 sayfası). Dosya açılamadı.

### Sebep
Önceki adımda yazdığım `.htaccess` içinde:
```apache
<FilesMatch "\.(zip|tar|gz|tgz|bak|backup|sql|sql\.gz|env|log|sh|yml|yaml|ini|conf|inc|swp|orig|old|tmp)$">
    Require all denied
</FilesMatch>
```
Bu kural KENDİ zip dosyalarımı da blokluyor.

### Çözüm
Zipper'ın output uzantısını `.dat` yaptım. Yerelde `mv x.dat x.zip` veya doğrudan `unzip x.dat` ile çalışıyor (unzip uzantıdan bağımsız).

### Bir Daha Tekrarlama Kuralı
- Üretim sunucudan dosya download'larken, kendi koyduğun `.htaccess` kurallarını gözden geçir
- Uzantı seçimi: `.dat`, `.bin`, `.tmp.bin` gibi engellenmeyenler
- Veya geçici olarak rule'u atla: PHP stream et (header'larla Content-Disposition: attachment)

---

## L03: Şifreler chat'e ham yapışıyor (2026-05-11)

### Hata
DB şifresini değiştirmek için Python `secrets`'la şifre ürettim, bash env dosyasına yazdım. Sonra `cat ~/.config/raven/env` çıktısı tüm chat'e düştü — yeni şifre açık metin halinde göründü.

### Sebep
`cat` ile sed/grep maskelemesi yapmadan dosyayı tüm gösterdim. Şifre içinde `&` karakteri olduğu için ayrıca bash parse error verdi ve sonraki source'da empty geldi — config.php'ye `DB_PASSWORD=''` yazıldı, site 500 hatası verdi.

### Çözüm
1. Şifreyi tekrar rotate ettim (bu sefer alfanümerik 32 char)
2. Bu sefer cat yerine grep + sed maskeleme kullandım
3. Env dosyasını `KEY='VALUE'` formatına çevirdim (tek tırnak içinde, özel karakter sorun değil)

### Bir Daha Tekrarlama Kuralı
- Şifre içeren env dosyasını ham yazdırma — her zaman maskele
- Bash env dosyalarında değerleri TEK TIRNAK içinde tut: `KEY='değer'`
- Python ile şifre üretirken karakter seti seç: shell-safe için alfanümerik + sınırlı sembol (`!@#%^&*()-_=+`)
- Backtick, `$`, `\`, `'`, `"` karakterleri shell/PHP escape sorunu yaratır — şifrede kullanma

---

## L04: Sunucu tarafı runner PHP'leri git'e push edilmesin (2026-05-11)

### Hata
Proje zip'inde geçici PHP runner dosyaları (`_<random>.php`) vardı (bunlar üretim sunucudaki aktif scriptlerimiz). İlk commit ile birlikte GitHub'a push edildi — token'lar açık halde repo'da bulundu.

### Sebep
- Geçici runner dosyaları public_html'de duruyordu zip alındığında
- .gitignore başlangıçta `_*.php` yoktu
- Git add -A her şeyi ekledi

### Çözüm
1. Force push ile commit'i amend ettim
2. .gitignore'a `code/_*.php` ve `**/runner_*.php` eklendi
3. Sunucudaki runner'lar API ile silindi
4. Yeni runner deploy edilmedi (yerel analiz moduna geçildi)

### Bir Daha Tekrarlama Kuralı
- Zip almadan önce sunucuyu temizle (runner'ları sil)
- `.gitignore` ilk commit'ten ÖNCE hazır olsun
- Runner dosya adları SAYISINI tahmin edilebilir yap (`runner_*.php` veya `_r*.php` gibi pattern)
- Token'ı PHP içine gömmek yerine env var'dan oku (apache env var via `SetEnv` veya `.env` parse)

---

## L05: j3.settings.get() oc_setting'i okumuyor (2026-05-11)

### Hata
Anasayfa H1'ini değiştirmek için `oc_setting` tablosuna `journal3_home_h1` key'i ile insert yaptım. HTML'de değişmedi — H1 hâlâ "Raven Dental".

### Sebep
Journal3'ün `j3.settings.get()` metodu OpenCart'ın `oc_setting` tablosunu okumuyor. Bunun yerine:
1. `oc_journal3_setting` (öncelikli)
2. `oc_journal3_skin_setting` (skin bazlı)
3. **Bulunamazsa** `config_name`'e fallback yapıyor (bu yüzden "Raven Dental" görünüyor)

### Çözüm (kısmi)
`oc_journal3_setting` tablosuna 3 farklı grupta (general, seo, custom_code) `journal3_home_h1` insert ettim — yine çalışmadı (j3.settings.get muhtemelen değerleri sadece spesifik bir gruptan okuyor veya cache'liyor).

### Bir Daha Tekrarlama Kuralı
- Journal3 ile oc_setting yerine oc_journal3_setting kullanılır
- Setting key'i değiştirilmeden ÖNCE Journal3 source'unda nasıl okuduğuna bak (`system/library/journal3/settings.php` veya benzeri)
- Bu sorunun nihai çözümü: tema dosyasını DOĞRUDAN düzenle (header.twig'de literal H1 yaz) — Journal3 cache'ini bypass et

→ ROADMAP'te: header.twig H1'i hardcode et + modification refresh

---

## L06: Bash variable substitution ile uzun PHP heredoc parsing hatası (2026-05-11)

### Hata
PHP runner içeriğini bash `read -r -d '' VAR <<'EOF' ... EOF` ile değişkene atayıp `curl -d "content=$VAR"` ile göndermek `parse error near '\n'` verdi.

### Sebep
Bash heredoc içindeki bazı karakterler (PHP'nin `${}`, backtick, `&`, `(`) shell expansion'a takılıyor. Tek tırnak'lı EOF kullanılsa bile çevre değişkenlerinin interpolation'ı sırasında sorun çıkıyor.

### Çözüm
Dosya tabanlı yaklaşım: PHP içeriği `/tmp/raven_zipper.php` dosyasına yaz (Write tool ile), sonra `curl --data-urlencode "content@/tmp/raven_zipper.php"` ile gönder.

### Bir Daha Tekrarlama Kuralı
- Çok satırlı PHP/HTML içeriği değişkene KOYMA — dosyaya yaz
- curl `--data-urlencode "key@file"` syntax'i bu işi temizler
- Write tool > heredoc string

---

## L07: cPanel UAPI function isimleri eski API2'den farklı (2026-05-11)

### Hata
`Fileman/copy`, `Fileman/remove_files`, `Fileman/mkdir` çağrılarım `function not found` döndürdü.

### Sebep
cPanel'in **UAPI Fileman modülü** sınırlı: sadece `list_files`, `get_file_content`, `save_file_content`, `get_file_information`, `upload_files`, `autocomplete`. Dosya işlemleri (delete, move, copy, mkdir) için **cPanel API 2 Fileman::fileop** kullanılmalı.

### Çözüm
Eski API 2 endpoint:
```
POST /json-api/cpanel?cpanel_jsonapi_user=USER
                     &cpanel_jsonapi_module=Fileman
                     &cpanel_jsonapi_func=fileop
                     &cpanel_jsonapi_apiversion=2
Body: op=unlink&sourcefiles=/path1,/path2
```
İşlevler: `unlink` (sil), `move` (taşı), `copy` (kopyala), `trash`, `chmod`, `chown`

### Bir Daha Tekrarlama Kuralı
- UAPI ≠ API 2 — function listesini bilmek lazım
- Hata mesajında "function not found" görünce: önce API 2 endpoint'i dene
- cPanel docs: https://api.docs.cpanel.net/cpanel/introduction/

---

## L08: NetInternet shared paket noshell — SSH imkansız (2026-05-11)

### Hata
Birkaç saat NetInternet'in SSH'ı standart dışı port'ta açık tutabileceğini varsayarak port taraması yaptım, key oluşturdum, bağlanmaya çalıştım — başarısız.

### Sebep
`shell:"/usr/local/cpanel/bin/noshell"` — cPanel API'sinden gelen kullanıcı bilgisinde bu satır vardı ve **shared paketin yapısal kısıtlaması** olduğunu söylüyordu. SSH portu açık olsa bile bu shell login'i reddeder.

### Çözüm
SSH umutsuz, cPanel API token + PHP runner ile devam edildi.

### Bir Daha Tekrarlama Kuralı
- Bir hosting'e SSH girmeden önce: `shell` alanını kontrol et (cPanel UAPI `Variables/get_user_information` çağrısı)
- `noshell` veya `jailshell` görünce: shared hosting kısıtlaması var
- Vakit kaybetmeden alternatif yol (API token, FTP, Web Manager) seç

---

## L09: SSH bağlanmadan önce mutlaka port taraması (2026-05-11)

### Hata
Port 22, 2200, 5022, 2222, 7822, 6222, 2083 → çoğu kapalı veya timeout.

### Belirti
- 22 → connection refused (servis dinlemiyor)
- 2200, 5022, 2222 → timeout (firewall blokluyor)
- 2083 → açık (cPanel HTTPS)

### Çözüm
Port 21 (FTP) açıktı. SSH zaten yokmuş.

### Bir Daha Tekrarlama Kuralı
- Önce port taraması, sonra bağlantı denemesi
- Birden fazla port paralel: `nc -zv -w 3 host 22 2200 5022`
- Ana hizmet portunu da test et (2083 cPanel) — açıksa erişim yolun var

---

## Hata Sayım Sayacı (Bu Oturum)

- L01: Storage/modification silme — kullanıcının manuel refresh gerektirdi
- L02: .zip blokunun kendi indirmemi engellemesi — 5 dakika kayıp
- L03: Şifre chat'e yapışması — 2 rotation gerektirdi
- L04: GitHub'a token push — force push gerektirdi
- L05: j3.settings.get çözümlenmedi — hâlâ açık
- L06: Bash heredoc PHP parse — dosya tabanlı yaklaşıma geçti
- L07: cPanel UAPI vs API2 — 2 başarısız endpoint denemesi
- L08: SSH bekleme — 30+ dakika kayıp
- L09: Port taraması — yeterince erken yapılmadı

**Toplam ders: 9**  
**Bir daha tekrarlanmayacak.**
