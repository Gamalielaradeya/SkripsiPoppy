# Migration Context — Centralized Log Monitoring Dashboard

Dokumen ini ditulis untuk AI assistant / engineer yang akan melanjutkan setup project di **VPS baru** (Ubuntu 24.04). Berisi context lengkap tentang state project di VPS lama, semua service yang harus dihidupkan, plus checklist migrasi. Baca dari atas ke bawah.

Tanggal context: Juni 2026
Owner: Gamaliel (mahasiswa skripsi, WIB)
Project: Centralized Log Monitoring Dashboard (skripsi)
Repo path saat ini: `/var/www/skripsi-poppy`

---

## 1. Ringkasan Project

Web dashboard buat monitor 2 laptop Windows yang pake Accurate 5. Flow utamanya:

```
Windows Agent (PowerShell) ── ZeroTier ──> RSyslog VPS ──> Laravel Parser ──> MySQL ──> Dashboard ──> Telegram Alert
                                                                ↑
                              Firebird 2.5 (Accurate audit) ────┘
```

**Stack final:**
- Laravel 12.61 + Blade + Tailwind 4 + Alpine.js + Chart.js
- PHP 8.3.6 (FPM, native install di host — bukan Docker)
- Nginx (native di host, port 80)
- MySQL 8.0 (Docker container `mysql-log-monitoring`, volume `project_mysql_data`, port 3306)
- Firebird 2.5 (Docker image `jacobalberty/firebird:2.5-sc`, volume `project_firebird_data`, port 3051→3050)
- RSyslog (native di host, UDP+TCP, port saat ini 5515 — boleh diubah bebas asal sinkron dengan Windows Agent config)
- ZeroTier (network `e4da7455b2b688af` "Poppy Zerotier", node lama IP `10.147.17.90`)
- Telegram Bot API (token + chat ID ada di `.env`)
- Composer 2.7.1, Node 22.22.2, npm 10.9.7

**Arsitektur penting:** Laravel + Nginx + PHP-FPM jalan **native di host**, bukan di Docker. Yang di Docker cuma database (MySQL) dan Firebird. RSyslog juga native di host (bukan container `rsyslog-server` di docker-compose lama — file `docker-compose.yml` lama sudah deprecated, abaikan).

---

## 2. State VPS Lama (snapshot Juni 2026)

### Yang AKTIF dan harus dipertahankan
| Service | Status | Detail |
|---|---|---|
| Nginx | running | port 80, config: `/etc/nginx/sites-available/skripsi-poppy` |
| PHP-FPM 8.3 | running | socket `/run/php/php8.3-fpm.sock` |
| MySQL 8.0 (Docker) | running healthy | container `mysql-log-monitoring`, DB `skripsi_poppy` |
| RSyslog | running | port 5515 UDP+TCP, ruleset `skripsi_remote` → `/var/log/remote/<host>.log` + `/var/log/remote/all.log` |
| ZeroTier | online | network `e4da7455b2b688af`, IP `10.147.17.90` |
| Cron `accurate:audit-sync` | running tiap 5 menit | gagal terus karena Firebird mati (lihat di bawah) |

### Yang MATI dan harus dihidupkan di VPS baru
| Service | Catatan |
|---|---|
| Firebird 2.5 (Docker) | container belum dijalankan ulang, file `XYZ.GDB` (~28 MB) masih aman di volume `project_firebird_data`. Cron audit-sync gagal karena ini. |
| Tailscale | logged out, gak dipake — skip kecuali user minta |

### Yang TIDAK PERLU dibawa
- `/root/project/` — folder copy lama Mei 2026, sudah stale, abaikan
- Container Docker `laravel-app`, `phpmyadmin`, `rsyslog-server` yang dulu di docker-compose — sekarang gak dipake, sudah pindah ke native host

---

## 3. File & Konfigurasi Penting

### Source code
- Lokasi: `/var/www/skripsi-poppy`
- Size: ~123 MB total (71 MB node_modules, 43 MB vendor, 4.6 MB storage)
- Owner storage & bootstrap/cache harus `www-data:www-data`
- `.env` ada di root project (jangan commit, sudah di-gitignore)

### .env keys yang wajib di-set di VPS baru
```
APP_URL=http://<IP-VPS-BARU>
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=skripsi_poppy
DB_USERNAME=skripsi_poppy_user
DB_PASSWORD=<dari backup>
TELEGRAM_BOT_TOKEN=<dari backup>
TELEGRAM_CHAT_ID=1390665213
ACCURATE_FIREBIRD_HOST=127.0.0.1
ACCURATE_FIREBIRD_PORT=3051
ACCURATE_FIREBIRD_DATABASE=/firebird/data/XYZ.GDB
ACCURATE_FIREBIRD_USERNAME=GUEST
ACCURATE_FIREBIRD_PASSWORD=<dari backup>
RSYSLOG_REMOTE_LOG_PATH=/var/log/remote
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

### Nginx config (`/etc/nginx/sites-available/skripsi-poppy`)
```nginx
server {
    listen 80;
    server_name <IP-VPS-BARU>;

    root /var/www/skripsi-poppy/public;
    index index.php index.html;

    access_log /var/log/nginx/skripsi-poppy-access.log;
    error_log  /var/log/nginx/skripsi-poppy-error.log;

    location / { try_files $uri $uri/ /index.php?$query_string; }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.ht { deny all; }
}
```
Symlink-kan ke `/etc/nginx/sites-enabled/`.

### RSyslog config (`/etc/rsyslog.d/60-skripsi-poppy.conf`)
```conf
module(load="imudp")
module(load="imtcp")

template(name="RemoteHostFile" type="string" string="/var/log/remote/%HOSTNAME%.log")

ruleset(name="skripsi_remote") {
    action(type="omfile" dynaFile="RemoteHostFile")
    action(type="omfile" file="/var/log/remote/all.log")
    stop
}

input(type="imudp" port="5515" ruleset="skripsi_remote")
input(type="imtcp" port="5515" ruleset="skripsi_remote")
```
**Port 5515 boleh diganti** sesuai kebutuhan (5514, 6514, dll) — yang penting konsisten dengan config Windows Agent. Pastikan `/var/log/remote/` ada dan writable oleh user syslog.

### Cron (`crontab -e` root)
```cron
*/5 * * * * cd /var/www/skripsi-poppy && flock -n /tmp/accurate-audit-sync.lock php artisan accurate:audit-sync >> storage/logs/accurate-audit-cron.log 2>&1
```
Cron lain (`rsyslog:parse`, `alerts:detect`) sekarang di-comment. Tanya user kalau mau dihidupkan — atau pakai `php artisan schedule:work` / queue worker sebagai gantinya.

### Artisan command custom yang dipakai
- `php artisan rsyslog:parse` — parse RSyslog remote log → tabel Laravel
- `php artisan accurate:audit-sync` — sync Firebird AUDIT + USERS → tabel `accurate_audit_events`
- `php artisan alerts:detect` — deteksi rule alert + kirim Telegram

---

## 4. Docker Containers yang Harus Diidupkan

Di VPS baru, jalankan dua container ini (gak pakai docker-compose lama, langsung `docker run` aja biar simpel):

### MySQL 8.0
```bash
docker run -d \
  --name mysql-log-monitoring \
  --restart unless-stopped \
  -p 127.0.0.1:3306:3306 \
  -e MYSQL_ROOT_PASSWORD='<dari backup>' \
  -e MYSQL_DATABASE=skripsi_poppy \
  -e MYSQL_USER=skripsi_poppy_user \
  -e MYSQL_PASSWORD='<dari backup>' \
  -v project_mysql_data:/var/lib/mysql \
  mysql:8.0
```
**Catatan:** bind ke `127.0.0.1` aja, jangan ke `0.0.0.0` (di VPS lama 0.0.0.0 — itu mistake, jangan dibawa). Volume `project_mysql_data` direstore dari backup volume VPS lama (lihat section restore).

### Firebird 2.5
```bash
docker run -d \
  --name firebird-server \
  --restart unless-stopped \
  -p 127.0.0.1:3051:3050 \
  -e ISC_PASSWORD=masterkey \
  -v project_firebird_data:/firebird/data \
  jacobalberty/firebird:2.5-sc
```
File `XYZ.GDB` (~28 MB) ada di dalam volume. Setelah restore volume + naikin container, test koneksi:
```bash
nc -zv 127.0.0.1 3051
docker exec -it firebird-server isql-fb -u GUEST -p '<password>' /firebird/data/XYZ.GDB
```

**Penting tentang Firebird 2.5 di Ubuntu 24.04:** Ubuntu 24.04 repo cuma punya Firebird 3.0 (FB2.5 EOL Maret 2021). Tapi karena kita pake Docker image `jacobalberty/firebird:2.5-sc`, ini gak ngaruh — versi OS host bebas. **Jangan upgrade ke Firebird 3.0** karena file `.GDB` FB2.5 gak kompatibel binary-nya dan harus `gbak backup → restore` dulu kalau mau pindah versi.

---

## 5. Migrasi: Step-by-Step di VPS Baru

Asumsi: VPS baru Ubuntu 24.04 fresh, akses root, sudah punya backup tarball dari VPS lama (lihat `backup-vps.sh` di project root).

### 5.1 Install base packages
```bash
apt update && apt upgrade -y
apt install -y software-properties-common ca-certificates curl gnupg lsb-release ufw unzip git

# PHP 8.3 (Ubuntu 24.04 default repo sudah punya, gak perlu ondrej)
apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml \
               php8.3-curl php8.3-zip php8.3-bcmath php8.3-intl php8.3-gd \
               php8.3-firebird php8.3-sqlite3 php8.3-readline

# Nginx
apt install -y nginx

# RSyslog (default sudah ada di Ubuntu)
systemctl enable rsyslog

# Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Node.js 22 (untuk build Vite)
curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
apt install -y nodejs

# Docker
curl -fsSL https://get.docker.com | sh
systemctl enable --now docker

# ZeroTier
curl -s https://install.zerotier.com | bash
systemctl enable --now zerotier-one
zerotier-cli join e4da7455b2b688af
# Lalu approve node baru di https://my.zerotier.com (akun owner)
```

### 5.2 Restore source code & data
```bash
# Upload tarball backup ke /tmp lalu extract
mkdir -p /var/www && cd /var/www
tar -xzf /tmp/skripsi-poppy-backup.tar.gz   # menghasilkan skripsi-poppy/ + dumps/

# Restore MySQL volume
docker volume create project_mysql_data
docker run --rm -v project_mysql_data:/target -v /tmp:/backup alpine \
  sh -c "cd /target && tar -xzf /backup/mysql_volume.tar.gz"

# Restore Firebird volume (file .GDB)
docker volume create project_firebird_data
docker run --rm -v project_firebird_data:/target -v /tmp:/backup alpine \
  sh -c "cd /target && tar -xzf /backup/firebird_volume.tar.gz"

# Naikin container MySQL & Firebird (lihat perintah di section 4)
```

Kalau backup pakai mysqldump (bukan volume tar), restore-nya:
```bash
docker exec -i mysql-log-monitoring mysql -uroot -p<root_pw> skripsi_poppy < /tmp/skripsi_poppy.sql
```

### 5.3 Laravel setup
```bash
cd /var/www/skripsi-poppy
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

# Restore .env dari backup, lalu edit APP_URL ke IP VPS baru
# Verifikasi APP_KEY masih ada (kalau hilang: php artisan key:generate)

composer install --no-dev --optimize-autoloader
npm ci && npm run build

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force   # cuma kalau ada migration baru; biasanya nggak perlu kalau DB sudah direstore
```

### 5.4 Nginx + RSyslog
```bash
# Copy nginx config dari backup, ganti server_name ke IP VPS baru
cp /tmp/skripsi-poppy.nginx /etc/nginx/sites-available/skripsi-poppy
sed -i "s|server_name .*|server_name $(curl -s ifconfig.me);|" /etc/nginx/sites-available/skripsi-poppy
ln -sf /etc/nginx/sites-available/skripsi-poppy /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx

# RSyslog
mkdir -p /var/log/remote && chown syslog:adm /var/log/remote
cp /tmp/60-skripsi-poppy.conf /etc/rsyslog.d/60-skripsi-poppy.conf
systemctl restart rsyslog

# Firewall
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 5515/udp   # ganti kalau port RSyslog berubah
ufw allow 5515/tcp
ufw allow 9993/udp   # ZeroTier
ufw enable
```

### 5.5 Cron
```bash
crontab -e
# Tambahkan:
*/5 * * * * cd /var/www/skripsi-poppy && flock -n /tmp/accurate-audit-sync.lock php artisan accurate:audit-sync >> storage/logs/accurate-audit-cron.log 2>&1
```

### 5.6 Update Windows Agent di setiap PC client
File config Windows Agent (`windows-agent/config.example.json` jadi referensi) — di setiap PC, ganti:
- `rsyslog_host` → IP VPS baru (atau IP ZeroTier `10.147.17.x` kalau pakai ZeroTier path)
- `rsyslog_port` → port baru kalau berubah dari 5515

Restart agent PowerShell di tiap PC.

---

## 6. Verification Checklist

Setelah semua up, jalankan ini di VPS baru:

```bash
# Web
curl -I http://localhost/                        # expect 302 → /dashboard
curl -L http://localhost/dashboard | head -20

# DB
docker exec -it mysql-log-monitoring mysql -uskripsi_poppy_user -p skripsi_poppy -e "SHOW TABLES;"

# Firebird
nc -zv 127.0.0.1 3051
cd /var/www/skripsi-poppy && php artisan accurate:audit-sync   # expect success log

# RSyslog
ss -tulnp | grep 5515
echo "<14>test from local" | nc -u -w1 127.0.0.1 5515
ls -la /var/log/remote/

# Cron
tail -f /var/www/skripsi-poppy/storage/logs/accurate-audit-cron.log   # tunggu 5 menit

# ZeroTier
zerotier-cli info        # expect ONLINE
zerotier-cli listnetworks  # expect OK, IP 10.147.17.x

# Telegram (manual): trigger alert dari dashboard atau lewat tinker
php artisan tinker
>>> app(\App\Services\TelegramAlertService::class)->send('test from new VPS');
```

---

## 7. Catatan Penting / Pitfalls

1. **MySQL bind localhost saja.** Di VPS lama kebablasan bind ke `0.0.0.0:3306` — jangan ulangi. Pakai `127.0.0.1:3306:3306` di `docker run`.

2. **Storage permissions.** Kalau dashboard error "permission denied" di `storage/logs`, ulang `chown -R www-data:www-data storage bootstrap/cache`.

3. **Firebird container `jacobalberty/firebird:2.5-sc`** — image lama tapi stabil. Image tag jangan diubah, karena file `XYZ.GDB` format FB2.5.

4. **PHP extension `php8.3-firebird`** wajib di-install — dipakai sama Laravel buat connect ke Accurate audit. Verifikasi: `php -m | grep -i firebird` harus nongol `PDO_Firebird`.

5. **APP_KEY di .env** jangan di-regenerate kalau DB direstore dari backup — semua session/encrypted column bakal rusak. Pakai key yang sama persis.

6. **Cron rsyslog:parse vs queue.** Sekarang cron parse di-comment. Kalau dashboard menunjukkan "Advanced Logs kosong" padahal RSyslog terima log, hidupkan cron itu atau jalankan manual.

7. **ZeroTier node baru wajib di-approve manual** di my.zerotier.com. Tanpa approve, statusnya `ACCESS_DENIED` walaupun service-nya jalan.

8. **Telegram chat ID `1390665213`** itu ID user Gamaliel — jangan ubah kecuali dia minta.

---

## 8. Kontak & Konvensi

- User: Gamaliel, WIB, casual Indonesian, suka direct
- Project: skripsi (thesis), gak ada production traffic — boleh agak agresif eksperimennya
- Hermes Agent ada di VPS lama sebagai systemd service `hermes-gateway.service` — kalau user mau lanjut pakai Hermes di VPS baru, install ulang dengan `hermes setup`

---

## 9. Yang Belum Pasti / Decision Pending

- [ ] IP VPS baru (belum diketahui saat dokumen ini ditulis)
- [ ] Port RSyslog final (5515 default, boleh diubah)
- [ ] Apakah cron `rsyslog:parse` dan `alerts:detect` mau dihidupkan otomatis atau manual
- [ ] Tailscale: skip kecuali user minta
- [ ] Domain → IP mapping (kalau ada domain, update A record ke IP baru)

Tanya user dulu sebelum eksekusi item di atas.
