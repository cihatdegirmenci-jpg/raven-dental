# 11 - Migration Plan (VPS Taşıma Planı)

> NetInternet shared hosting → NetInternet KVM VDS (SSD VDS III önerilen).
> Hedef: 0-5 dakika downtime, temiz başlangıç.

## Hedef Sunucu Spec'i

**NetInternet SSD VDS III** ($17.50/ay ilk 3 ay, $35/ay sonra):
- 4 vCPU (8.0 GHz toplam)
- 8 GB RAM
- 80 GB RAID50 SSD
- 8 TB/ay trafik
- 1 IPv4
- İstanbul DC

## Hedef Yazılım Stack

```
Ubuntu 22.04 LTS
├── OpenLiteSpeed (veya Nginx) — web server
│   └── Let's Encrypt SSL
├── PHP 8.2-FPM
│   ├── OPcache
│   ├── opcache.jit
│   └── Required extensions: gd, mbstring, mysqli, pdo_mysql, curl, openssl, zip, intl, bcmath, xml
├── MariaDB 10.11
│   └── Tuned for OpenCart (innodb_buffer_pool, query_cache)
├── Redis 7
│   └── OpenCart cache backend
├── Composer 2
├── Certbot (Let's Encrypt auto-renewal)
├── UFW (firewall)
├── fail2ban (brute-force koruma)
├── Netdata (monitoring)
└── unattended-upgrades (security patches)
```

## Faz 0 — Hazırlık (VPS satın alınmadan ÖNCE)

- [x] Site yerelde (~/raven-dental/code/)
- [x] DB dump'lar yerelde (~/raven-dental/db/)
- [x] Dokümantasyon hazır
- [x] Hangi spec satın alınacak kararlı (VDS III)
- [ ] migration-plan/server-bootstrap.sh hazırla
- [ ] migration-plan/deploy.sh hazırla
- [ ] migration-plan/rollback.sh hazırla
- [ ] DNS planı (GoDaddy A record vs Cloudflare proxy)

## Faz 1 — VPS Satın Alma

### Kullanıcı yapacak:
1. NetInternet panel → SSD VDS III sipariş
2. Sipariş notuna yaz:
   ```
   OS: Ubuntu 22.04 LTS
   Panel: Boş / Panelsiz
   SSH: Port 22'de açık, root SSH erişimi
   Lokasyon: İstanbul
   ```
3. Kurulum sonrası mail'i bana yapıştır:
   - Sunucu IP
   - Root şifresi (1 kez kullanılacak, hemen rotate)
   - SSH port (eğer 22 değilse)

### Önerilen ek özellikler:
- [ ] Haftalık otomatik backup (varsa) — eklenebilir, opsiyonel
- [ ] Sanal güvenlik duvarı (NetInternet'in firewall'u varsa açık)

## Faz 2 — İlk Bağlantı + Hardening (1 saat)

### A. SSH bağlan ve şifreyi rotate et
```bash
# Yerel
ssh root@VPS_IP

# Sunucuda
passwd  # yeni şifre belirle
adduser raven  # non-root user
usermod -aG sudo raven
```

### B. SSH key auth + şifre devre dışı
```bash
# Yerel:
ssh-copy-id raven@VPS_IP

# Sunucuda:
nano /etc/ssh/sshd_config
# PermitRootLogin no
# PasswordAuthentication no
# PubkeyAuthentication yes
# AllowUsers raven

systemctl restart ssh
```

### C. UFW firewall
```bash
ufw default deny incoming
ufw default allow outgoing
ufw allow OpenSSH
ufw allow 80/tcp   # HTTP (Let's Encrypt için)
ufw allow 443/tcp  # HTTPS
ufw enable
```

### D. fail2ban
```bash
apt install -y fail2ban
systemctl enable --now fail2ban
# /etc/fail2ban/jail.local
[sshd]
maxretry = 3
bantime = 3600
```

### E. Auto-updates
```bash
apt install -y unattended-upgrades
dpkg-reconfigure -plow unattended-upgrades
```

### F. Hostname + timezone
```bash
hostnamectl set-hostname raven-dental
timedatectl set-timezone Europe/Istanbul
```

## Faz 3 — LAMP/LEMP Kurulumu (1 saat)

### Seçim: OpenLiteSpeed (önerilen — mevcut hosting LiteSpeed)
veya Nginx. OpenLiteSpeed avantajı: mevcut `.htaccess` çoğu kuralı çalışır.

### A. OpenLiteSpeed kurulum
```bash
wget -O - https://repo.litespeed.sh | bash
apt install openlitespeed lsphp82 lsphp82-mysql lsphp82-common lsphp82-curl lsphp82-imap lsphp82-imagick lsphp82-redis
```

veya **Nginx + PHP-FPM:**
```bash
apt install -y nginx php8.2-fpm php8.2-mysql php8.2-gd php8.2-curl php8.2-zip php8.2-xml php8.2-mbstring php8.2-intl php8.2-bcmath php8.2-redis php8.2-imagick
```

### B. MariaDB
```bash
apt install -y mariadb-server
mysql_secure_installation

# Konfigürasyon
nano /etc/mysql/mariadb.conf.d/50-server.cnf
# innodb_buffer_pool_size = 2G  (8 GB RAM'in 1/4'ü)
# query_cache_type = 1
# max_connections = 100

systemctl restart mariadb
```

### C. Redis
```bash
apt install -y redis
sed -i 's/# maxmemory.*/maxmemory 512mb/' /etc/redis/redis.conf
sed -i 's/# maxmemory-policy.*/maxmemory-policy allkeys-lru/' /etc/redis/redis.conf
systemctl restart redis
```

### D. PHP tuning
```bash
nano /etc/php/8.2/fpm/php.ini
# memory_limit = 256M
# upload_max_filesize = 64M
# post_max_size = 64M
# max_execution_time = 60
# opcache.enable=1
# opcache.memory_consumption=256
# opcache.max_accelerated_files=20000
# opcache.jit=1255
# opcache.jit_buffer_size=128M

systemctl restart php8.2-fpm
```

### E. Certbot (SSL)
```bash
apt install -y certbot python3-certbot-nginx  # veya -openlitespeed
# DNS yansımadan sonra:
certbot --nginx -d ravendentalgroup.com -d www.ravendentalgroup.com
```

## Faz 4 — Kod Taşıma (30 dakika)

### A. DB import
```bash
# Yerel
mysqldump'ı al (eğer sıfırdan değilse, schema + data)
# Veya phpMyAdmin → Export Custom → ZIP

# VPS'te
mysql -u root -p < raven_full.sql
```

veya **rsync + DB ayrı:**
```bash
# Yerel makineden (veya eski sunucudan)
rsync -avz --progress ~/raven-dental/code/ raven@VPS_IP:/var/www/raven/
```

### B. Dosya izinleri
```bash
chown -R raven:www-data /var/www/raven
find /var/www/raven -type d -exec chmod 755 {} \;
find /var/www/raven -type f -exec chmod 644 {} \;

# Yazılabilir alanlar
chmod -R 775 /var/www/raven/image
chmod -R 775 /var/www/raven/storage
```

### C. config.php — yeni VPS için
```php
// public_html/config.php
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'raven_user');
define('DB_PASSWORD', 'YENI_VPS_DB_SIFRE');
define('DB_DATABASE', 'raven_db');
define('DB_PORT', '3306');
define('DB_DRIVER', 'mysqli');  // veya 'pdo'
define('DB_PREFIX', 'oc_');

define('HTTP_SERVER', 'https://ravendentalgroup.com/');
define('HTTPS_SERVER', 'https://ravendentalgroup.com/');

define('DIR_APPLICATION', '/var/www/raven/catalog/');
define('DIR_SYSTEM', '/var/www/raven/system/');
// ... yolları VPS'e göre güncelle
```

### D. OCMOD refresh
```bash
# OpenCart admin'e gir, refresh modifications
# Veya CLI ile (eğer extension var):
php /var/www/raven/cli.php modification refresh
```

### E. Cache temizle
```bash
rm -rf /var/www/raven/storage/cache/*
```

## Faz 5 — Test (1 saat)

### A. Geçici test subdomain'i
- GoDaddy'de: `test.ravendentalgroup.com` A record → yeni VPS IP
- 30-60 dakika yansıma
- VPS'te OpenLiteSpeed/Nginx: ServerName test.ravendentalgroup.com
- SSL: `certbot --nginx -d test.ravendentalgroup.com`

### B. Test checklist
- [ ] `/` → 200
- [ ] `/sitemap.xml` → 200
- [ ] `/diagnostik-aletleri` → 200
- [ ] `/admin/` → 200, login çalışıyor
- [ ] `/index.php?route=account/login` → form çalışıyor
- [ ] Cart eklemek çalışıyor
- [ ] Checkout flow (QNB Pay sandbox) çalışıyor
- [ ] Email gönderim (test sipariş)
- [ ] Cron jobs (sitemap update, vb.) çalışıyor
- [ ] Lighthouse skoru (mobil + desktop)
- [ ] Page speed insights

### C. Performans baseline (yeni VPS)
```bash
# Yerel
npx unlighthouse --site https://test.ravendentalgroup.com
```

## Faz 6 — DNS Switch (Production Switch)

### Cloudflare ile (önerilen)
1. CF hesabı aç, domain ekle
2. CF nameserver'larını GoDaddy'de güncelle
3. CF'de A record:
   - `ravendentalgroup.com` → eski IP (geçici)
   - `www` → eski IP (geçici)
4. DNS yansıma 24-48 saat
5. **Geçiş zamanı:** CF'de A record'u yeni VPS IP'sine değiştir (anlık)
6. CF Proxy: AÇ (turuncu bulut)
7. SSL: Full (strict)
8. Page Rules:
   - `/admin/*` → Cache Level: Bypass
   - `/index.php*` → Cache Level: Bypass
9. **VPS UFW:** Sadece CF IP'lerine 80/443 aç (güvenlik)

### Cloudflare olmadan
1. GoDaddy'de A record:
   - `ravendentalgroup.com` → yeni VPS IP
   - `www` → yeni VPS IP
2. TTL 5 dakika (önceden düşür)
3. Yansıma 5-30 dakika
4. Eski hosting paralel açık dur (1 hafta)

## Faz 7 — Switch Sonrası (1 hafta)

### Gün 1
- [ ] Tüm trafiğin yeni VPS'e gittiğini doğrula (eski hosting access_log boş)
- [ ] OpenCart admin'den son sipariş kontrol
- [ ] Cron job çalışıyor mu test

### Gün 1-7
- [ ] Eski hosting'i paralel tut (geri dönüş için)
- [ ] Yeni VPS'i izle (Netdata + uptime)
- [ ] Backup script çalışıyor mu

### Gün 7+
- [ ] Eski NetInternet shared hosting'i iptal et / iade
- [ ] Eski hosting'ten kalan yedek dosyaları yerelde sakla

## Rollback Senaryosu

Eğer yeni VPS'te ciddi sorun çıkarsa:

### Hızlı (DNS revert)
```
GoDaddy/CF panelinde A record'u eski IP'ye geri al:
ravendentalgroup.com → 95.173.190.138 (eski shared)
```
Yansıma 5-30 dakika, sonra eski site canlı (sanki hiçbir şey olmamış gibi).

### Önemli not
- Eski shared hosting **switch tarihinden 1 hafta sonra** iptal edilmeli
- Bu süre rollback güvencesi
- Switch sonrası eski hosting'e sipariş gelmemeli (DNS değişti) ama emniyet için kontrol

## Cron Job'lar (Yeni VPS'te)

```cron
# /var/spool/cron/crontabs/raven
# OpenCart cleanup
0 3 * * * /usr/bin/php /var/www/raven/cli.php cron > /dev/null 2>&1

# Backup
0 2 * * * /home/raven/bin/backup.sh

# SSL renewal (certbot otomatik yapar zaten)
# Log rotation
```

## Maliyet Karşılaştırma

| | Mevcut Shared | Yeni VPS |
|---|---|---|
| Aylık | ₺300-500 | ₺550-1100 (kur'a göre $17.50-$35) |
| Performans | Düşük | 5-10× |
| SSH | Yok | Var |
| Yönetim | NetInternet | Ben (otomatik) + sen (manuel) |
| Backup | Haftalık | Günlük (otomatik) |
| SSL | Otomatik | Let's Encrypt otomatik |

## Migration Sonrası Avantajlar

✅ **PHP 8.2** (mevcut 7.4'ten 30% daha hızlı + güvenlik patches)
✅ **OPcache JIT** (PHP'yi daha da hızlandırır)
✅ **Redis cache** (DB query cache → çok hızlı)
✅ **MariaDB tuning** (kendi RAM'ine göre)
✅ **HTTP/3** (Cloudflare ile)
✅ **WebP image generation** (imagick + cron)
✅ **Tam shell erişim** (cron, composer, git, vb.)
✅ **CDN** (Cloudflare ücretsiz)
✅ **Daily backup → S3** (felaket kurtarma)
✅ **fail2ban + UFW** (brute force koruma)
✅ **Modification refresh CLI'dan** (admin gerekmez)

## Hazırlanacak Script'ler (analysis/migration-plan/ klasörü)

- [ ] `server-bootstrap.sh` — Faz 2+3 otomasyonu (root olarak çalışacak)
- [ ] `deploy.sh` — Faz 4 otomasyonu (eski sunucudan rsync + DB import)
- [ ] `rollback.sh` — Acil rollback (DNS API ile A record değiştir)
- [ ] `backup.sh` — Günlük backup (DB + dosyalar → uzak depo)
- [ ] `monitor.sh` — Sağlık kontrol cron (5 dk'da bir, hata varsa email)
