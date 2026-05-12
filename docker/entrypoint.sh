#!/bin/bash
set -e

# Storage subdirs (volume mount başlangıçta boş gelir)
mkdir -p /var/www/storage/cache \
         /var/www/storage/modification \
         /var/www/storage/logs \
         /var/www/storage/session \
         /var/www/storage/upload \
         /var/www/storage/download \
         /var/www/storage/backup

# Vendor — code/system/storage/vendor'dan kopyala (ilk seferde)
if [ ! -f /var/www/storage/vendor/autoload.php ] && [ -d /var/www/html/system/storage/vendor ]; then
    echo "[entrypoint] Vendor kopyalanıyor: code → volume"
    cp -r /var/www/html/system/storage/vendor /var/www/storage/
fi

# Ownership
chown -R www-data:www-data /var/www/storage
chmod -R 775 /var/www/storage

echo "[entrypoint] Storage hazır: $(ls /var/www/storage | tr '\n' ' ')"

exec "$@"
