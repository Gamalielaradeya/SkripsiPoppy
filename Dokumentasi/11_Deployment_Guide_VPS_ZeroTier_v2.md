# 11 — Deployment Guide v2
# Centralized Log Monitoring Dashboard

**Versi:** 2.0  
**Status:** Draft Final untuk implementasi real-device  
**Project:** Centralized Log Monitoring Dashboard  
**Target Environment:** 2 laptop Windows Accurate 5 + 1 VPS Linux + ZeroTier  
**Stack:** Laravel, Blade, Tailwind CSS, Alpine.js, Chart.js, MySQL/MariaDB, RSyslog, Firebird 2.5, Windows Agent, Telegram Bot, ZeroTier  
**Tujuan Dokumen:** Menjelaskan langkah deployment sistem pada lingkungan real-device agar dapat digunakan sebagai acuan implementasi, pengujian, dan demo skripsi.

---

## 1. Tujuan Dokumen

Dokumen ini menjelaskan cara melakukan deployment **Centralized Log Monitoring Dashboard** versi real-device.

Berbeda dengan rancangan awal yang menggunakan `rsyslog-client` container sebagai simulasi, versi v2 diarahkan untuk memonitor perangkat nyata, yaitu dua laptop Windows yang menjalankan Accurate 5 dan satu VPS Linux sebagai pusat monitoring serta server database Accurate/Firebird.

Dokumen ini menjadi pedoman untuk:

1. Menyiapkan VPS Linux sebagai pusat monitoring.
2. Menghubungkan VPS dan laptop Windows melalui ZeroTier.
3. Menjalankan RSyslog Server di VPS.
4. Menjalankan Laravel Dashboard dan database monitoring.
5. Menyiapkan Firebird 2.5 dan database Accurate pada VPS.
6. Menjalankan Windows Agent pada laptop client.
7. Mengirim telemetry real-device ke RSyslog.
8. Melakukan parsing telemetry ke database monitoring.
9. Membaca audit trail Accurate dari Firebird secara read-only.
10. Mengaktifkan contextual Telegram alert.
11. Mengaktifkan fitur Remote Desktop dan Remote Restart manual terkontrol.

---

## 2. Prinsip Deployment v2

Deployment v2 mengikuti prinsip berikut:

| Prinsip | Penjelasan |
|---|---|
| Real-device first | Sistem harus memonitor laptop Windows nyata, bukan hanya data simulasi. |
| VPS-centered | VPS Linux menjadi pusat monitoring, database monitoring, RSyslog, dan Firebird/Accurate DB. |
| Private network | Koneksi laptop Windows ke VPS menggunakan ZeroTier agar terasa seperti satu jaringan lokal. |
| No hardcoded device | Device didaftarkan otomatis menggunakan `agent_id`, bukan nama device hardcoded. |
| RSyslog for monitoring | RSyslog hanya dipakai untuk menerima log/status/telemetry dari Windows Agent. |
| Accurate Audit direct | Audit trail Accurate dibaca langsung dari Firebird `AUDIT + USERS`, bukan lewat RSyslog. |
| Telegram mandatory | Telegram wajib digunakan sebagai contextual alert channel. |
| Manual remote action | Remote Desktop dan Restart Client adalah tindakan manual admin, bukan otomatis. |
| Raw log secondary | Raw log hanya untuk Advanced Logs, bukan fokus dashboard utama. |
| GitHub-based iteration | Project harus dikelola menggunakan GitHub agar update dapat dilakukan aman dan bertahap. |

---

## 3. Topologi Deployment

### 3.1 Komponen Utama

```text
[Windows Laptop 1]
- Accurate 5 Client
- Windows Agent
- ZeroTier Client
- RDP Enabled

[Windows Laptop 2]
- Accurate 5 Client
- Windows Agent
- ZeroTier Client
- RDP Enabled

        ↓ ZeroTier private network

[VPS Linux]
- ZeroTier Client
- RSyslog Server
- Laravel Dashboard
- MySQL/MariaDB Monitoring DB
- Firebird 2.5 / Accurate Database
- Accurate Audit Reader
- Telegram Alert Service
- Remote Command API
```

### 3.2 Diagram Network

```text
+---------------------------+              +---------------------------+
| Windows Laptop 1          |              | Windows Laptop 2          |
| Hostname: dynamic         |              | Hostname: dynamic         |
| Agent ID: UUID            |              | Agent ID: UUID            |
| Accurate 5                |              | Accurate 5                |
| Windows Agent             |              | Windows Agent             |
| ZeroTier IP: 10.x.x.11    |              | ZeroTier IP: 10.x.x.12    |
+-------------+-------------+              +-------------+-------------+
              \                                      /
               \                                    /
                \                                  /
                 v                                v
              +--------------------------------------+
              | ZeroTier Private Network             |
              +------------------+-------------------+
                                 |
                                 v
+----------------------------------------------------------------+
| VPS Linux                                                       |
| ZeroTier IP: 10.x.x.10                                          |
| Public IP: x.x.x.x                                              |
|                                                                |
| - RSyslog Server       : 514 TCP/UDP or mapped custom port       |
| - Laravel Dashboard    : HTTP/HTTPS                             |
| - Monitoring Database  : MySQL/MariaDB                          |
| - Firebird 2.5         : 3051                                   |
| - Telegram Service     : HTTPS outbound                         |
| - Remote Command API   : HTTPS                                  |
+----------------------------------------------------------------+
```

---

## 4. Target Perangkat

### 4.1 VPS Linux

Spesifikasi VPS yang digunakan:

| Komponen | Rekomendasi |
|---|---:|
| CPU | 4 Core |
| RAM | 8 GB |
| Storage | Minimal 40 GB, disarankan 80 GB+ |
| OS | Ubuntu Server LTS / Debian-based Linux |
| Network | Public IP + ZeroTier IP |

Peran VPS:

1. Menjalankan RSyslog Server.
2. Menjalankan Laravel Dashboard.
3. Menyimpan database monitoring.
4. Menjalankan Firebird 2.5 untuk database Accurate.
5. Membaca audit trail Accurate.
6. Mengirim Telegram alert.
7. Menyediakan API untuk polling remote command oleh Windows Agent.

### 4.2 Windows Laptop Client

Spesifikasi laptop client:

| Komponen | Keterangan |
|---|---|
| OS | Windows 10/11 |
| Aplikasi | Accurate 5 Client |
| Network | ZeroTier Client aktif |
| Agent | Windows Agent monitoring |
| Remote Access | RDP aktif jika ingin digunakan |

Peran laptop client:

1. Menjalankan Accurate 5.
2. Mengirim heartbeat ke RSyslog.
3. Mengirim telemetry CPU/RAM/Disk.
4. Mengirim status koneksi Firebird.
5. Mengirim status `accurate.exe`.
6. Menerima remote command manual melalui API polling.

---

## 5. Port dan Protokol

### 5.1 Port VPS

| Port | Protokol | Akses | Fungsi |
|---:|---|---|---|
| 22 | TCP | Admin only | SSH ke VPS |
| 80 | TCP | Public/ZeroTier | HTTP dashboard, opsional redirect HTTPS |
| 443 | TCP | Public/ZeroTier | HTTPS dashboard dan API |
| 514 | TCP/UDP | ZeroTier only | Syslog dari Windows Agent, jika memakai port standar |
| 5514 | TCP/UDP | ZeroTier only | Alternatif syslog jika port 514 tidak dipakai |
| 3051 | TCP | ZeroTier only | Firebird database Accurate |
| 3306 | TCP | Localhost/internal only | MySQL/MariaDB monitoring DB |

### 5.2 Rekomendasi Keamanan Port

| Service | Rekomendasi |
|---|---|
| SSH | Batasi IP admin jika memungkinkan. |
| Dashboard | Gunakan HTTPS. |
| RSyslog | Hanya buka ke network ZeroTier. |
| Firebird | Jangan expose bebas ke publik; gunakan ZeroTier. |
| MySQL/MariaDB | Jangan expose publik. Bind local/internal saja. |
| Remote Command API | Wajib HTTPS dan token agent. |

---

## 6. Persiapan VPS

### 6.1 Update Sistem

```bash
sudo apt update
sudo apt upgrade -y
```

### 6.2 Install Paket Dasar

```bash
sudo apt install -y \
  curl \
  git \
  unzip \
  rsyslog \
  ufw \
  ca-certificates \
  gnupg \
  lsb-release
```

### 6.3 Install Web Stack

Ada dua opsi deployment Laravel:

| Opsi | Keterangan | Rekomendasi |
|---|---|---|
| Native Nginx + PHP-FPM | Lebih production-like, cocok di VPS. | Direkomendasikan |
| Docker Compose | Lebih mudah reproducible, tapi perlu konfigurasi volume dan permission. | Boleh jika tim nyaman Docker |

Untuk project ini, deployment yang disarankan adalah:

```text
VPS Native:
- Nginx
- PHP-FPM
- Composer
- MySQL/MariaDB
- RSyslog native
- Firebird native/teruji
```

Alasannya: VPS hanya satu server, dan real-device client akan mengirim log ke RSyslog VPS. Native deployment mengurangi overhead container dan lebih mudah mengakses service OS seperti RSyslog dan Firebird.

---

## 7. Instalasi PHP, Composer, dan Database Monitoring

### 7.1 Install PHP dan Extension

Contoh untuk PHP 8.2/8.3, sesuaikan dengan versi Laravel yang dipakai:

```bash
sudo apt install -y \
  php-cli \
  php-fpm \
  php-mysql \
  php-mbstring \
  php-xml \
  php-curl \
  php-zip \
  php-bcmath \
  php-tokenizer
```

Jika Accurate Audit Reader diimplementasikan dengan koneksi Firebird langsung dari PHP, extension Firebird/InterBase perlu dipastikan tersedia dan kompatibel. Jika extension PHP Firebird sulit disiapkan, gunakan worker terpisah berbasis Python atau command-line connector yang hasilnya disimpan ke MySQL monitoring.

### 7.2 Install Composer

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
rm composer-setup.php
```

### 7.3 Install MySQL/MariaDB

```bash
sudo apt install -y mariadb-server mariadb-client
sudo systemctl enable mariadb
sudo systemctl start mariadb
```

Buat database monitoring:

```sql
CREATE DATABASE centralized_log_monitoring CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'monitor_user'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON centralized_log_monitoring.* TO 'monitor_user'@'localhost';
FLUSH PRIVILEGES;
```

Catatan:

- Jangan expose MySQL ke internet publik.
- Simpan credential di `.env` Laravel.
- Gunakan password kuat.

---

## 8. Setup Project Laravel di VPS

### 8.1 Clone Repository

```bash
cd /var/www
sudo git clone <GITHUB_REPOSITORY_URL> centralized-log-monitoring
sudo chown -R $USER:www-data centralized-log-monitoring
cd centralized-log-monitoring
```

### 8.2 Checkout Branch

Untuk development:

```bash
git checkout develop
```

Untuk production/stable:

```bash
git checkout main
```

### 8.3 Install Dependency

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

### 8.4 Konfigurasi `.env`

Contoh `.env`:

```env
APP_NAME="Centralized Log Monitoring Dashboard"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://monitoring.example.com
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=centralized_log_monitoring
DB_USERNAME=monitor_user
DB_PASSWORD=CHANGE_ME_STRONG_PASSWORD

RSYSLOG_REMOTE_LOG_PATH=/var/log/remote
PARSER_BATCH_LIMIT=500
PARSER_ANTI_DUPLICATE=true

AGENT_COMMAND_API_ENABLED=true
AGENT_COMMAND_POLLING_ENABLED=true
AGENT_TOKEN_HEADER=X-Agent-Token

TELEGRAM_ALERT_ENABLED=true
TELEGRAM_BOT_TOKEN=CHANGE_ME
TELEGRAM_CHAT_ID=CHANGE_ME
TELEGRAM_COOLDOWN_MINUTES=5

DEVICE_HEARTBEAT_WARNING_MINUTES=5
DEVICE_HEARTBEAT_CRITICAL_MINUTES=15
CPU_WARNING_THRESHOLD=80
CPU_CRITICAL_THRESHOLD=90
RAM_WARNING_THRESHOLD=85
DISK_WARNING_THRESHOLD=90
FIREBIRD_LATENCY_WARNING_MS=500
FIREBIRD_LATENCY_CRITICAL_MS=1000

FIREBIRD_AUDIT_ENABLED=true
FIREBIRD_HOST=127.0.0.1
FIREBIRD_PORT=3051
FIREBIRD_DATABASE_PATH=/path/to/accurate/database.fdb
FIREBIRD_USERNAME=GUEST_OR_READONLY_USER
FIREBIRD_PASSWORD=CHANGE_ME
FIREBIRD_SYNC_INTERVAL_SECONDS=30
```

Catatan penting:

1. `APP_KEY` harus diisi dengan `php artisan key:generate`.
2. `FIREBIRD_*` harus mengikuti POC dan akses read-only yang sudah berhasil diuji.
3. Jangan commit `.env` ke GitHub.
4. Jangan hardcode credential di source code.

### 8.5 Permission Laravel

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 8.6 Migration dan Seeder

```bash
php artisan migrate --seed
```

Seeder minimal harus membuat:

```text
- akun admin
- threshold default
- system settings default
```

---

## 9. Setup Nginx

### 9.1 Install Nginx

```bash
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

### 9.2 Konfigurasi Virtual Host

Contoh file:

```bash
sudo nano /etc/nginx/sites-available/centralized-log-monitoring
```

Isi contoh:

```nginx
server {
    listen 80;
    server_name monitoring.example.com _;

    root /var/www/centralized-log-monitoring/public;
    index index.php index.html;

    access_log /var/log/nginx/centralized-log-monitoring.access.log;
    error_log /var/log/nginx/centralized-log-monitoring.error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Catatan:

- Path `fastcgi_pass` harus disesuaikan dengan versi PHP di VPS, misalnya `/run/php/php8.2-fpm.sock`.
- Untuk production, gunakan HTTPS.

Aktifkan site:

```bash
sudo ln -s /etc/nginx/sites-available/centralized-log-monitoring /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 10. Setup ZeroTier

### 10.1 Tujuan ZeroTier

ZeroTier digunakan agar VPS dan dua laptop Windows berada dalam satu private network virtual.

Manfaat:

1. Laptop Windows dapat mengakses Firebird VPS melalui IP privat ZeroTier.
2. Windows Agent dapat mengirim syslog ke VPS tanpa expose port ke publik.
3. Dashboard dapat membuka RDP ke IP ZeroTier device.
4. Firebird port `3051` tidak perlu dibuka bebas ke internet.

### 10.2 Setup Umum

Langkah umum:

1. Buat akun ZeroTier.
2. Buat network baru.
3. Install ZeroTier client di VPS Linux.
4. Join VPS ke network.
5. Install ZeroTier client di Windows Laptop 1 dan 2.
6. Join kedua laptop ke network yang sama.
7. Approve semua member pada dashboard ZeroTier.
8. Catat IP ZeroTier masing-masing perangkat.

Contoh mapping:

| Device | Role | ZeroTier IP |
|---|---|---|
| VPS | Monitoring + Firebird | `10.147.20.10` |
| Laptop 1 | Accurate Client | `10.147.20.11` |
| Laptop 2 | Accurate Client | `10.147.20.12` |

Catatan:

- IP di atas hanya contoh dokumentasi.
- Program tidak boleh hardcode IP ini.
- IP dan nama device harus disimpan di database/settings.

### 10.3 Validasi Koneksi ZeroTier

Dari laptop Windows:

```powershell
ping 10.147.20.10
Test-NetConnection 10.147.20.10 -Port 3051
Test-NetConnection 10.147.20.10 -Port 5514
```

Dari VPS:

```bash
ping 10.147.20.11
ping 10.147.20.12
```

Expected result:

```text
- Ping antar device berhasil.
- Port Firebird 3051 dapat diakses dari laptop Windows.
- Port RSyslog 5514/514 dapat diakses dari laptop Windows.
```

---

## 11. Setup RSyslog Server di VPS

### 11.1 Buat Folder Remote Log

```bash
sudo mkdir -p /var/log/remote
sudo chown syslog:adm /var/log/remote
sudo chmod 755 /var/log/remote
```

Jika Laravel parser berjalan sebagai user `www-data`, pastikan user tersebut dapat membaca `/var/log/remote`:

```bash
sudo usermod -aG adm www-data
```

Setelah itu restart PHP-FPM/Nginx jika perlu.

### 11.2 Konfigurasi RSyslog Listener

Buat file:

```bash
sudo nano /etc/rsyslog.d/10-centralized-monitoring.conf
```

Isi:

```conf
# ==========================================================
# Centralized Log Monitoring Dashboard v2
# RSyslog Server for real Windows Agent telemetry
# ==========================================================

module(load="imudp")
module(load="imtcp")

# Listen on standard syslog port if allowed
input(type="imudp" port="514" ruleset="remoteMonitoring")
input(type="imtcp" port="514" ruleset="remoteMonitoring")

# Optional custom port for safer/non-privileged deployment
input(type="imudp" port="5514" ruleset="remoteMonitoring")
input(type="imtcp" port="5514" ruleset="remoteMonitoring")

# One log file per sender hostname
$template RemoteHostFile,"/var/log/remote/%HOSTNAME%.log"

# Unified format for Laravel parser
$template RemoteLogFormat,"%timereported:::date-rfc3339% %HOSTNAME% %syslogtag%%msg%\n"

ruleset(name="remoteMonitoring") {
    action(type="omfile" dynaFile="RemoteHostFile" template="RemoteLogFormat" createDirs="on")
    action(type="omfile" file="/var/log/remote/all.log" template="RemoteLogFormat")
    stop
}
```

Restart RSyslog:

```bash
sudo systemctl restart rsyslog
sudo systemctl status rsyslog
```

### 11.3 Uji RSyslog dari VPS Lokal

```bash
logger -n 127.0.0.1 -P 5514 -T -t device-monitor "event=device_heartbeat agent_id=test hostname=vps-test status=online"
```

Cek log:

```bash
sudo tail -f /var/log/remote/all.log
```

Expected result:

```text
Log device-monitor muncul di /var/log/remote/all.log
```

### 11.4 Firewall untuk RSyslog

Jika memakai UFW:

```bash
sudo ufw allow from <ZEROTIER_SUBNET_OR_DEVICE_IP> to any port 5514 proto tcp
sudo ufw allow from <ZEROTIER_SUBNET_OR_DEVICE_IP> to any port 5514 proto udp
```

Contoh subnet tergantung network ZeroTier. Jangan gunakan contoh tanpa disesuaikan.

---

## 12. Setup Firebird 2.5 dan Database Accurate

### 12.1 Prinsip

Firebird yang digunakan harus sesuai dengan hasil POC Accurate 5, yaitu **Firebird 2.5**. Jangan upgrade sembarangan ke Firebird versi lain tanpa pengujian kompatibilitas Accurate.

Prinsip penting:

1. Database Accurate berada di VPS Linux.
2. Accurate 5 di Windows client mengakses database melalui IP ZeroTier VPS dan port `3051`.
3. Laravel Accurate Audit Reader membaca `AUDIT + USERS` menggunakan user read-only/guest yang sudah diuji.
4. Tabel `LOGIN` tidak boleh dijadikan sumber utama.
5. Field `COMP_NAME` dan `IPADDRESS` boleh kosong, sehingga tidak wajib untuk identifikasi device.

### 12.2 Path Database Accurate

Contoh:

```text
/opt/accurate-db/company.fdb
```

Catatan:

- Path di atas hanya contoh.
- Sesuaikan dengan hasil konfigurasi Firebird dan Accurate yang sudah berhasil diuji.
- Pastikan permission file database sesuai kebutuhan Firebird.

### 12.3 Akses dari Windows Accurate

Di laptop Windows, Accurate 5 diarahkan ke server database:

```text
Host/IP  : ZeroTier IP VPS
Port     : 3051
Database : path database Accurate di VPS
```

Contoh konseptual:

```text
10.147.20.10:/opt/accurate-db/company.fdb
```

Format path koneksi harus mengikuti format yang digunakan Accurate/Firebird dan hasil POC.

### 12.4 Validasi Port Firebird dari Windows

```powershell
Test-NetConnection 10.147.20.10 -Port 3051
```

Expected:

```text
TcpTestSucceeded : True
```

Jika gagal:

1. Pastikan ZeroTier aktif.
2. Pastikan Firebird service running.
3. Pastikan firewall VPS membuka port 3051 hanya untuk ZeroTier.
4. Pastikan Firebird listen pada interface yang benar.

---

## 13. Setup Accurate Audit Reader

### 13.1 Prinsip Audit Reader

Accurate Audit Reader adalah bagian Laravel/backend yang membaca audit trail Accurate secara read-only dari Firebird.

Alur:

```text
Laravel Scheduler
      ↓
accurate:audit-sync command
      ↓
Connect read-only to Firebird
      ↓
Query AUDIT + USERS
      ↓
Normalize data
      ↓
Insert/Update accurate_audit_events
      ↓
Dashboard Accurate Audit
```

### 13.2 Command yang Disarankan

```bash
php artisan accurate:audit-sync
```

Untuk scheduler:

```bash
php artisan schedule:work
```

Atau cron Laravel:

```cron
* * * * * cd /var/www/centralized-log-monitoring && php artisan schedule:run >> /dev/null 2>&1
```

### 13.3 Strategi Incremental Sync

Audit Reader tidak boleh membaca seluruh tabel dari awal setiap kali jalan.

Gunakan tabel:

```text
accurate_audit_sync_states
accurate_audit_sync_runs
```

Strategi:

1. Simpan `last_audit_id` atau timestamp terakhir yang berhasil dibaca.
2. Pada sync berikutnya, ambil data setelah posisi tersebut.
3. Jika gagal, jangan update state.
4. Simpan error di `accurate_audit_sync_runs`.
5. Hindari duplicate dengan unique key, misalnya `source_id + audit_id`.

### 13.4 Larangan untuk Audit Reader

Codex/Developer tidak boleh:

1. Menulis data ke database Accurate.
2. Mengubah struktur database Accurate.
3. Menggunakan user SYSDBA untuk dashboard jika user read-only tersedia.
4. Menganggap tabel `LOGIN` berisi data login valid.
5. Menganggap `COMP_NAME` dan `IPADDRESS` selalu tersedia.
6. Mengambil Windows user dari tabel audit Accurate.
7. Membuat audit spike detection tanpa rule dan threshold eksplisit.

---

## 14. Setup Windows Agent

### 14.1 Lokasi Agent

Contoh folder:

```text
C:\CentralizedLogAgent\
```

Isi minimal:

```text
C:\CentralizedLogAgent\
├── agent.exe / agent.ps1 / agent.py
├── config.json
├── logs\
└── agent_id.txt
```

### 14.2 Config Agent

Contoh `config.json`:

```json
{
  "agent_id": "auto-generated-uuid",
  "device_label": "Laptop Finance 1",
  "rsyslog_host": "10.147.20.10",
  "rsyslog_port": 5514,
  "rsyslog_protocol": "tcp",
  "dashboard_api_url": "https://monitoring.example.com/api/agent",
  "agent_token": "CHANGE_ME_DEVICE_TOKEN",
  "firebird_host": "10.147.20.10",
  "firebird_port": 3051,
  "heartbeat_interval_seconds": 30,
  "telemetry_interval_seconds": 60,
  "command_poll_interval_seconds": 15,
  "accurate_process_name": "accurate.exe",
  "enable_remote_restart": true,
  "enable_rdp_status_check": true
}
```

### 14.3 Device Identity

Agent harus membuat `agent_id` unik pertama kali dijalankan.

Urutan:

```text
Agent start
↓
Cek agent_id.txt
↓
Jika tidak ada, generate UUID
↓
Simpan agent_id.txt
↓
Kirim heartbeat pertama
↓
Laravel auto-register device berdasarkan agent_id
```

Program tidak boleh hardcode device seperti `WIN-ACC-01`.

### 14.4 Data yang Dikirim Agent

#### Heartbeat

```text
device-monitor: event=device_heartbeat agent_id=<uuid> hostname=<hostname> device_label="Laptop Finance 1" windows_user="DESKTOP\Finance" ip_zerotier=10.147.20.11 agent_status=online rdp_status=available
```

#### Telemetry

```text
perf-monitor: event=device_telemetry agent_id=<uuid> hostname=<hostname> cpu=42 ram=61 disk=55 uptime_seconds=123456
```

#### Firebird Connectivity

```text
network-monitor: event=firebird_connectivity agent_id=<uuid> hostname=<hostname> target_host=10.147.20.10 target_port=3051 status=connected latency_ms=24 attempts=1
```

#### Accurate Process

```text
accurate-process-monitor: event=accurate_process agent_id=<uuid> hostname=<hostname> process_name=accurate.exe process_status=running process_owner="DESKTOP\Finance" process_id=1234
```

#### Remote Action Result

```text
remote-action-monitor: event=remote_action_result agent_id=<uuid> action_id=123 action_type=restart_client result=accepted message="Restart scheduled in 30 seconds"
```

---

## 15. Windows Agent sebagai Scheduled Task atau Service

### 15.1 Opsi Scheduled Task

Cocok untuk tahap awal karena sederhana.

Contoh konsep:

```powershell
schtasks /Create /TN "CentralizedLogAgent" /SC ONSTART /TR "powershell.exe -ExecutionPolicy Bypass -File C:\CentralizedLogAgent\agent.ps1" /RL HIGHEST
```

### 15.2 Opsi Windows Service

Cocok untuk versi lebih rapi.

Opsi:

1. Build agent menjadi `.exe`.
2. Daftarkan sebagai Windows Service.
3. Jalankan otomatis saat boot.

Untuk MVP real-device, Scheduled Task sudah cukup jika stabil.

---

## 16. Parser RSyslog di Laravel

### 16.1 Command Parser

Command:

```bash
php artisan rsyslog:parse
```

### 16.2 Path Log

```env
RSYSLOG_REMOTE_LOG_PATH=/var/log/remote
```

### 16.3 Format Parser

Parser harus mendukung format syslog:

```text
2026-05-28T10:00:00+07:00 HOSTNAME device-monitor: event=device_heartbeat agent_id=...
```

Parser harus memproses:

1. Timestamp.
2. Hostname.
3. Syslog tag/process.
4. Message.
5. Key-value pairs.
6. Event type.
7. Severity.
8. Category.
9. Raw message.
10. Hash anti-duplicate.

### 16.4 Mapping ke Database

| Event | Tabel Utama | Tabel Pendukung |
|---|---|---|
| `device_heartbeat` | `devices` | `logs` |
| `device_telemetry` | `device_telemetries` | `logs` |
| `firebird_connectivity` | `network_checks` | `logs` |
| `accurate_process` | `accurate_process_snapshots` | `logs` |
| `server_service_check` | `server_service_checks` | `logs` |
| `remote_action_result` | `remote_actions` | `logs` |

---

## 17. Laravel Scheduler dan Background Jobs

### 17.1 Scheduler Command

Laravel scheduler harus menjalankan:

```text
rsyslog:parse
accurate:audit-sync
alerts:evaluate
incidents:correlate
```

Contoh cron:

```cron
* * * * * cd /var/www/centralized-log-monitoring && php artisan schedule:run >> /dev/null 2>&1
```

### 17.2 Interval yang Disarankan

| Job | Interval |
|---|---:|
| `rsyslog:parse` | setiap 1 menit |
| `accurate:audit-sync` | setiap 1 menit atau 30 detik jika aman |
| `alerts:evaluate` | setiap 1 menit |
| `incidents:correlate` | setiap 1 menit |
| cleanup old parser runs | harian |

---

## 18. Setup Telegram Alert

### 18.1 Environment

```env
TELEGRAM_ALERT_ENABLED=true
TELEGRAM_BOT_TOKEN=CHANGE_ME
TELEGRAM_CHAT_ID=CHANGE_ME
TELEGRAM_COOLDOWN_MINUTES=5
```

### 18.2 Format Alert Telegram

Telegram tidak boleh mengirim pesan generik.

Format wajib:

```text
[SEVERITY] Judul Alert

Target      : <target name>
Detected by : <source>
Evidence    : <evidence fields>
Impact      : <operational impact>
Action      : <recommended action>
Time        : <timestamp>

Dashboard: <url detail alert>
```

Contoh:

```text
[ERROR] WIN-ACC-01 gagal terhubung ke Firebird VPS:3051

Target      : WIN-ACC-01 → VPS-FIREBIRD
Detected by : Windows Agent
Evidence    : tcp_connect timeout 3 kali, port=3051
Impact      : Accurate 5 pada device tersebut berpotensi gagal membuka database
Action      : Cek koneksi ZeroTier atau gunakan Remote Desktop ke WIN-ACC-01
Time        : 2026-05-28 10:15:00

Dashboard: https://monitoring.example.com/alerts/123
```

### 18.3 Cooldown

Sistem harus mencegah spam Telegram:

```text
Alert dengan target + rule_code + severity sama tidak dikirim ulang dalam cooldown window.
```

Default:

```text
5 menit
```

---

## 19. Remote Desktop Deployment

### 19.1 Prinsip

Remote Desktop bukan proses otomatis. Dashboard hanya menyediakan launcher untuk admin.

Data yang dibutuhkan:

```text
device.ip_zerotier
device.rdp_status
device.device_label
```

### 19.2 Action UI

Di halaman Device Detail:

```text
[Remote Desktop]
```

Jika RDP available:

- tampilkan command:

```text
mstsc /v:<ip_zerotier>
```

- atau generate file `.rdp`.

Jika RDP unavailable:

- tombol disabled atau tampilkan warning.

### 19.3 Remote Desktop tidak lewat RSyslog

RSyslog tidak boleh digunakan untuk membuka RDP. RSyslog hanya menerima status/log.

---

## 20. Remote Restart Deployment

### 20.1 Prinsip

Remote Restart adalah fitur manual terkontrol.

Alur:

```text
Admin klik Restart Client
↓
Modal konfirmasi muncul
↓
Admin wajib isi alasan
↓
Laravel membuat remote action status=pending
↓
Windows Agent polling command API
↓
Agent menerima command restart
↓
Agent menjalankan restart Windows
↓
Agent mengirim result
↓
Dashboard update remote action status
```

### 20.2 API yang Dibutuhkan

Endpoint konseptual:

```text
POST /remote-actions/{device}/restart
GET  /api/agent/commands
POST /api/agent/commands/{id}/result
```

### 20.3 Data Remote Action

```text
admin_id
device_id
action_type=restart_client
reason
status=pending|delivered|executed|failed|cancelled
requested_at
delivered_at
executed_at
result_message
```

### 20.4 Command Restart Windows

Agent dapat menjalankan:

```powershell
shutdown /r /t 30 /c "Restart requested by IT admin from Centralized Log Monitoring Dashboard"
```

Catatan:

1. Command hanya dijalankan jika `enable_remote_restart=true`.
2. Agent token harus valid.
3. Semua action harus dicatat.
4. Tidak boleh ada auto-restart dari alert.

---

## 21. Firewall dan Security Hardening

### 21.1 UFW Basic

Contoh konsep:

```bash
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
```

Untuk ZeroTier services:

```bash
sudo ufw allow from <ZEROTIER_SUBNET> to any port 5514
sudo ufw allow from <ZEROTIER_SUBNET> to any port 3051 proto tcp
```

Jangan copy tanpa mengganti `<ZEROTIER_SUBNET>`.

### 21.2 Security Checklist

```text
[ ] SSH aman dan password kuat / key-based login
[ ] Dashboard menggunakan HTTPS
[ ] MySQL/MariaDB tidak expose publik
[ ] Firebird hanya lewat ZeroTier
[ ] RSyslog hanya menerima dari ZeroTier
[ ] Agent memakai token unik
[ ] Remote command API memakai HTTPS
[ ] .env tidak masuk GitHub
[ ] Credential Firebird tidak hardcode
[ ] Remote restart wajib alasan dan konfirmasi
```

---

## 22. GitHub Workflow

### 22.1 Branch Strategy

```text
main        = versi stabil
velop       = active development
feature/*   = fitur baru
fix/*       = perbaikan bug
release/*   = persiapan release
```

Catatan: gunakan `develop`, bukan `velop`. Baris di atas harus diterapkan sebagai:

```text
main
velop  ❌ jangan dipakai
develop ✅ gunakan ini
feature/*
fix/*
release/*
```

### 22.2 Workflow Development

```text
1. Buat issue/task
2. Buat branch feature
3. Implementasi
4. Test lokal/VPS staging
5. Commit kecil dan jelas
6. Push ke GitHub
7. Pull request ke develop
8. Review
9. Merge
10. Deploy ke VPS
```

### 22.3 Commit Message

Contoh:

```text
feat(agent): add firebird connectivity telemetry
feat(alert): add contextual telegram notification
fix(parser): handle quoted key-value message
chore(deploy): update nginx config example
```

---

## 23. Deployment Update ke VPS

### 23.1 Pull Update

```bash
cd /var/www/centralized-log-monitoring
git pull origin main
```

### 23.2 Install Dependency Jika Ada Perubahan

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika menggunakan Vite/Tailwind build di VPS:

```bash
npm install
npm run build
```

Atau build asset di CI/local lalu deploy hasil build sesuai strategi project.

### 23.3 Restart Service

```bash
sudo systemctl reload nginx
sudo systemctl restart php-fpm
sudo systemctl restart rsyslog
```

Sesuaikan nama service PHP-FPM, misalnya `php8.2-fpm`.

---

## 24. Validasi End-to-End Setelah Deployment

### 24.1 Checklist VPS

```text
[ ] Dashboard bisa dibuka
[ ] Login admin berhasil
[ ] Database monitoring terkoneksi
[ ] RSyslog running
[ ] /var/log/remote bisa ditulis RSyslog
[ ] Laravel bisa membaca /var/log/remote
[ ] Scheduler aktif
[ ] Telegram terkirim
[ ] Firebird service running
[ ] Audit Reader bisa query AUDIT + USERS
```

### 24.2 Checklist Windows Laptop

```text
[ ] ZeroTier aktif
[ ] Bisa ping VPS ZeroTier IP
[ ] Bisa Test-NetConnection ke port 3051
[ ] Bisa Test-NetConnection ke port 5514/514
[ ] Accurate 5 bisa membuka database di VPS
[ ] Windows Agent running
[ ] Agent mengirim heartbeat
[ ] Agent mengirim telemetry
[ ] Agent mengirim Accurate process status
[ ] Agent bisa polling command API
```

### 24.3 Checklist Dashboard

```text
[ ] Device otomatis terdaftar
[ ] Device status online
[ ] Windows user tampil
[ ] CPU/RAM/Disk tampil
[ ] Firebird connectivity tampil
[ ] Accurate process status tampil
[ ] Accurate Audit tampil
[ ] Alerts contextual tampil
[ ] Telegram alert berisi target/evidence
[ ] Remote Desktop launcher tersedia
[ ] Remote Restart membutuhkan konfirmasi
[ ] Advanced Logs berisi raw log
```

---

## 25. Troubleshooting

### 25.1 Device Tidak Muncul di Dashboard

Kemungkinan:

1. Windows Agent tidak running.
2. ZeroTier tidak aktif.
3. Agent salah `rsyslog_host` atau port.
4. Firewall VPS menolak syslog.
5. RSyslog tidak running.
6. Parser Laravel belum jalan.

Cek:

```bash
sudo systemctl status rsyslog
sudo tail -f /var/log/remote/all.log
php artisan rsyslog:parse
```

Di Windows:

```powershell
Test-NetConnection <VPS_ZEROTIER_IP> -Port 5514
```

### 25.2 Raw Log Masuk tapi Device Tidak Update

Kemungkinan:

1. Format key=value salah.
2. `agent_id` kosong.
3. Parser tidak mengenali `event`.
4. Mapping event ke tabel belum benar.

Cek:

```bash
php artisan rsyslog:parse --verbose
```

Lihat tabel:

```text
logs
parser_runs
parser_offsets
devices
```

### 25.3 Accurate Tidak Bisa Connect ke Firebird

Kemungkinan:

1. Firebird service mati.
2. Port 3051 belum terbuka untuk ZeroTier.
3. Path database salah.
4. Credential Accurate salah.
5. Versi Firebird tidak kompatibel.
6. File database permission bermasalah.

Cek dari Windows:

```powershell
Test-NetConnection <VPS_ZEROTIER_IP> -Port 3051
```

Cek dari VPS:

```bash
sudo systemctl status firebird*
sudo ss -tulpn | grep 3051
```

### 25.4 Accurate Audit Tidak Tampil

Kemungkinan:

1. Firebird credential audit reader salah.
2. Query `AUDIT + USERS` gagal.
3. Sync state salah.
4. Tabel AUDIT kosong.
5. Field yang diasumsikan tidak ada di database tersebut.

Cek:

```bash
php artisan accurate:audit-sync --verbose
```

Lihat tabel:

```text
accurate_audit_sources
accurate_audit_events
accurate_audit_sync_states
accurate_audit_sync_runs
```

### 25.5 Telegram Tidak Terkirim

Kemungkinan:

1. Token salah.
2. Chat ID salah.
3. `TELEGRAM_ALERT_ENABLED=false`.
4. VPS tidak bisa akses internet outbound.
5. Alert cooldown masih aktif.

Cek:

```text
alert_notifications
system log Laravel
```

### 25.6 Remote Restart Tidak Jalan

Kemungkinan:

1. Device offline.
2. Agent tidak polling command API.
3. Agent token salah.
4. `enable_remote_restart=false`.
5. Agent tidak punya privilege restart.
6. Firewall/HTTPS API bermasalah.

Cek:

```text
remote_actions.status
remote_actions.result_message
agent local logs
```

---

## 26. Rollback Plan

Jika deployment update bermasalah:

1. Checkout commit sebelumnya:

```bash
git log --oneline
git checkout <previous_commit>
```

2. Jalankan ulang cache:

```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. Restart service:

```bash
sudo systemctl reload nginx
sudo systemctl restart php-fpm
```

4. Jika migration menyebabkan perubahan database besar, restore dari backup.

---

## 27. Backup Plan

### 27.1 Backup Database Monitoring

```bash
mysqldump -u monitor_user -p centralized_log_monitoring > backup_monitoring_$(date +%F).sql
```

### 27.2 Backup Accurate Database

Backup database Accurate harus mengikuti prosedur Firebird/Accurate yang aman. Jangan copy file `.fdb` saat database aktif tanpa prosedur backup yang benar.

Rekomendasi:

```text
Gunakan mekanisme backup Firebird yang sesuai dan sudah diuji.
```

### 27.3 Backup File Konfigurasi

Backup:

```text
.env
/etc/rsyslog.d/10-centralized-monitoring.conf
Nginx site config
Windows Agent config.json per device
```

Jangan upload credential ke repository publik.

---

## 28. Deployment Acceptance Criteria

Deployment dianggap berhasil jika:

| ID | Acceptance Criteria |
|---|---|
| DEP-001 | VPS dapat diakses melalui SSH. |
| DEP-002 | VPS dan laptop Windows berada dalam network ZeroTier yang sama. |
| DEP-003 | Dashboard Laravel dapat dibuka melalui browser. |
| DEP-004 | Admin dapat login. |
| DEP-005 | RSyslog menerima log dari Windows Agent. |
| DEP-006 | Parser membaca log dan mengisi database monitoring. |
| DEP-007 | Device Windows auto-register tanpa hardcode. |
| DEP-008 | Dashboard menampilkan device online. |
| DEP-009 | CPU/RAM/Disk real-device tampil. |
| DEP-010 | Firebird connectivity dari device tampil. |
| DEP-011 | Accurate process status tampil. |
| DEP-012 | Firebird database di VPS dapat diakses Accurate 5. |
| DEP-013 | Accurate Audit Reader membaca `AUDIT + USERS`. |
| DEP-014 | Accurate Audit page menampilkan aktivitas. |
| DEP-015 | Contextual alert dibuat dengan target dan evidence. |
| DEP-016 | Telegram alert terkirim dengan format contextual. |
| DEP-017 | Remote Desktop launcher tersedia untuk device reachable. |
| DEP-018 | Remote Restart manual membutuhkan konfirmasi dan alasan. |
| DEP-019 | Remote action tercatat di database. |
| DEP-020 | Raw log tersedia di Advanced Logs, bukan dashboard utama. |

---

## 29. Larangan untuk AI Agent / Codex

AI Agent / Codex tidak boleh:

1. Mengembalikan deployment ke 1 laptop simulasi sebagai target utama.
2. Membuat `rsyslog-client` container sebagai sumber data utama real-device.
3. Menggunakan WSL sebagai monitoring utama Windows host.
4. Meng-hardcode nama device seperti `WIN-ACC-01` dalam logic program.
5. Mengirim semua Windows Event Log mentah sebagai dashboard utama.
6. Membuat alert tanpa target, detected_by, evidence, impact, dan recommended_action.
7. Membuat Telegram alert generik.
8. Menggunakan RSyslog untuk remote action.
9. Membuat auto restart otomatis berdasarkan alert.
10. Menggunakan tabel `LOGIN` sebagai sumber utama audit Accurate.
11. Menganggap `COMP_NAME` dan `IPADDRESS` selalu tersedia di audit Accurate.
12. Menulis data ke database Accurate.
13. Mengubah struktur database Accurate.
14. Menyimpan credential Firebird/Telegram/agent token di source code.
15. Mengekspos Firebird dan MySQL bebas ke publik.

---

## 30. Ringkasan Deployment

Deployment v2 menggunakan lingkungan real-device:

```text
2 Windows Laptop Accurate 5
        ↓ Windows Agent + ZeroTier
VPS Linux
        ├── RSyslog Server
        ├── Laravel Dashboard
        ├── MySQL/MariaDB Monitoring DB
        ├── Firebird Accurate DB
        ├── Accurate Audit Reader
        ├── Telegram Alert Service
        └── Remote Command API
```

Sistem ini tidak lagi berfokus pada log simulasi, tetapi pada monitoring nyata yang dibutuhkan IT admin:

1. Device mana yang online.
2. User Windows siapa yang aktif.
3. Apakah client bisa konek ke Firebird.
4. Apakah Accurate berjalan di device.
5. Bagaimana kondisi CPU/RAM/Disk.
6. Aktivitas audit Accurate apa yang terjadi.
7. Alert apa yang perlu ditindaklanjuti.
8. Remote action apa yang dilakukan admin.

Deployment dianggap berhasil jika seluruh alur real-device berjalan dari Windows Agent sampai dashboard dan Telegram.
