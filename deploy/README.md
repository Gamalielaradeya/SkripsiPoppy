# Deployment Guide - Milestone 12

This guide prepares Centralized Log Monitoring Dashboard for real-device UAT and thesis demonstration.

Target environment:

```text
2 Windows laptops with Accurate 5 + PowerShell Windows Agent
1 VPS Linux with Laravel, MySQL, RSyslog, Firebird 2.5, ZeroTier
Telegram Bot API for contextual alerts
```

Do not use this guide to expose Firebird, MySQL, or RSyslog publicly. Prefer ZeroTier-only access for monitoring traffic.

## 1. VPS Linux Setup Overview

Recommended server:

```text
Ubuntu Server LTS or Debian-based VPS
4 CPU cores
8 GB RAM
40 GB storage minimum, 80 GB preferred
Public IP for SSH/HTTPS
ZeroTier IP for private device traffic
```

Base packages:

```bash
sudo apt update
sudo apt upgrade -y
sudo apt install -y git curl unzip ca-certificates gnupg ufw rsyslog nginx mysql-server mysql-client
```

Useful directories:

```bash
sudo mkdir -p /var/www/centralized-log-monitoring
sudo mkdir -p /var/log/remote
sudo mkdir -p /var/backups/centralized-log-monitoring
sudo mkdir -p /opt/accurate-db
```

## 2. PHP and Laravel Requirements

Laravel 12 requires PHP 8.2 or newer.

Install common extensions:

```bash
sudo apt install -y \
  php-cli php-fpm php-mysql php-mbstring php-xml php-curl php-zip php-bcmath php-tokenizer
```

Install Composer:

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
rm composer-setup.php
```

Deploy app:

```bash
cd /var/www
sudo git clone <REPOSITORY_URL> centralized-log-monitoring
sudo chown -R $USER:www-data centralized-log-monitoring
cd centralized-log-monitoring
git checkout main
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force --seed
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If building assets on VPS:

```bash
npm ci
npm run build
```

Storage permissions:

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## 3. MySQL Setup

Enable service:

```bash
sudo systemctl enable mysql
sudo systemctl start mysql
```

Create monitoring database:

```sql
CREATE DATABASE centralized_log_monitoring CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'monitor_user'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON centralized_log_monitoring.* TO 'monitor_user'@'localhost';
FLUSH PRIVILEGES;
```

Set `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=centralized_log_monitoring
DB_USERNAME=monitor_user
DB_PASSWORD=CHANGE_ME_STRONG_PASSWORD
```

Do not expose port `3306` publicly.

## 4. Nginx Overview

Example site:

```nginx
server {
    listen 80;
    server_name monitoring.example.com _;

    root /var/www/centralized-log-monitoring/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Validate and reload:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

Use HTTPS for demo/production. Remote command polling should use HTTPS when reachable outside localhost.

## 5. RSyslog Setup

Install provided config:

```bash
sudo mkdir -p /var/log/remote
sudo chown syslog:adm /var/log/remote
sudo chmod 755 /var/log/remote
sudo cp deploy/rsyslog/rsyslog.conf /etc/rsyslog.d/10-centralized-monitoring.conf
sudo rsyslogd -N1
sudo systemctl restart rsyslog
sudo systemctl status rsyslog --no-pager
```

Allow Laravel user to read logs:

```bash
sudo usermod -aG adm www-data
sudo systemctl restart php8.2-fpm
```

Default listener: TCP/UDP `5514`.

Firewall example:

```bash
sudo ufw allow from <ZEROTIER_SUBNET_OR_CLIENT_IP> to any port 5514 proto udp
sudo ufw allow from <ZEROTIER_SUBNET_OR_CLIENT_IP> to any port 5514 proto tcp
```

Test:

```bash
logger -n 127.0.0.1 -P 5514 -d -t device-monitor 'event_type=device_heartbeat agent_id=test-agent hostname=test-host status=online'
sudo tail -n 20 /var/log/remote/all.log
php artisan rsyslog:parse
```

## 6. ZeroTier Setup

1. Create ZeroTier network.
2. Install ZeroTier on VPS.
3. Join VPS to network.
4. Install ZeroTier on each Windows laptop.
5. Authorize all members in ZeroTier dashboard.
6. Record VPS and laptop ZeroTier IPs.

Validation from Windows:

```powershell
ping <VPS_ZEROTIER_IP>
Test-NetConnection <VPS_ZEROTIER_IP> -Port 5514
Test-NetConnection <VPS_ZEROTIER_IP> -Port 3051
```

Validation from VPS:

```bash
ping <LAPTOP_1_ZEROTIER_IP>
ping <LAPTOP_2_ZEROTIER_IP>
```

## 7. Firebird and pdo_firebird Notes

Accurate audit trail must come directly from Firebird `AUDIT + USERS`.

Rules:

- Use Firebird 2.5 compatibility for Accurate 5.
- Use read-only Firebird account for audit reader.
- Do not use `LOGIN` as primary source.
- Do not require `COMP_NAME` or `IPADDRESS`.
- Do not mutate Accurate database.
- Keep Firebird port `3051` limited to ZeroTier.

Laravel uses PDO Firebird when audit sync runs on the same PHP runtime:

```bash
php -m | grep -i firebird
php artisan monitoring:health
```

If `pdo_firebird` is missing, `monitoring:health` reports WARN, not FAIL. Install the extension only on the server that runs `accurate:audit-sync`.

Configure `.env`:

```dotenv
ACCURATE_AUDIT_ENABLED=true
ACCURATE_FIREBIRD_HOST=<VPS_ZEROTIER_IP_OR_LOCALHOST>
ACCURATE_FIREBIRD_PORT=3051
ACCURATE_FIREBIRD_DATABASE=/opt/accurate-db/company.fdb
ACCURATE_FIREBIRD_USERNAME=<READ_ONLY_USER>
ACCURATE_FIREBIRD_PASSWORD=<READ_ONLY_PASSWORD>
ACCURATE_FIREBIRD_CHARSET=NONE
ACCURATE_AUDIT_SYNC_LIMIT=100
```

Dry run first:

```bash
php artisan accurate:audit-sync --dry-run --limit=10
```

## 8. Laravel Scheduler and Cron

Current project commands:

```bash
php artisan rsyslog:parse
php artisan accurate:audit-sync
php artisan alerts:detect
php artisan monitoring:health
```

Recommended cron:

```cron
* * * * * cd /var/www/centralized-log-monitoring && php artisan schedule:run >> /dev/null 2>&1
```

If scheduler entries are not registered yet, run commands manually during UAT:

```bash
php artisan rsyslog:parse
php artisan accurate:audit-sync
php artisan alerts:detect
```

Do not create deployment automation that changes services without admin review.

## 9. Telegram Env Setup

Create bot through BotFather, get chat ID, then set:

```dotenv
TELEGRAM_ALERT_ENABLED=true
TELEGRAM_BOT_TOKEN=<BOT_TOKEN>
TELEGRAM_CHAT_ID=<CHAT_ID>
TELEGRAM_ALERT_COOLDOWN_MINUTES=5
```

Never commit token or chat ID. `monitoring:health` only reports present/missing.

## 10. Windows Agent Installation

1. Copy `windows-agent/` to Windows, for example `C:\CentralizedLogAgent`.
2. Copy `config.example.json` to `config.json`.
3. Set `api_base_url`, `syslog_host`, `firebird_host`, and feature toggles.
4. Run dry run.
5. Run real agent once.
6. Add Scheduled Task after first successful run.

Dry run:

```powershell
powershell.exe -ExecutionPolicy Bypass -File "C:\CentralizedLogAgent\agent.ps1" -ConfigPath "C:\CentralizedLogAgent\config.json" -DryRun
```

Real run:

```powershell
powershell.exe -ExecutionPolicy Bypass -File "C:\CentralizedLogAgent\agent.ps1" -ConfigPath "C:\CentralizedLogAgent\config.json"
```

Scheduled Task example is in [windows-agent/README.md](../windows-agent/README.md).

## 11. Real-Device Test Checklist

Use [Dokumentasi/16_Milestone_12_UAT_Checklist.md](../Dokumentasi/16_Milestone_12_UAT_Checklist.md).

Minimum acceptance:

```text
[ ] Two Windows devices registered without hardcoded names.
[ ] Heartbeat, CPU/RAM/Disk, Firebird connectivity, Accurate process status received.
[ ] RSyslog receives structured logs.
[ ] Parser stores structured data.
[ ] Accurate audit sync reads Firebird AUDIT + USERS.
[ ] Alerts have target, evidence, impact, recommended action.
[ ] Telegram sends contextual alert.
[ ] Remote Desktop action logs.
[ ] Restart Client requires confirmation and reason.
[ ] Advanced Logs contains raw structured logs.
```

## 12. Demo Readiness Checklist

```text
[ ] VPS online.
[ ] Dashboard reachable.
[ ] Admin login works.
[ ] ZeroTier online on VPS and Windows devices.
[ ] RSyslog running and receiving logs.
[ ] Windows Agent real run succeeds on both clients.
[ ] Devices show online.
[ ] Accurate 5 running on at least one client.
[ ] Firebird port 3051 reachable from clients.
[ ] Accurate audit sync succeeds.
[ ] Telegram configured and tested.
[ ] At least one contextual warning/critical alert from real test data.
[ ] Remote Desktop launcher demonstrated.
[ ] Restart Client flow demonstrated safely.
```

## 13. Verification Commands

```bash
php artisan monitoring:health
php artisan route:list --except-vendor
php artisan test
npm run build
php artisan rsyslog:parse
php artisan accurate:audit-sync --dry-run
php artisan alerts:detect
```

## 14. References

- [Production env checklist](PRODUCTION_ENV_CHECKLIST.md)
- [Service checklist](SERVICE_CHECKLIST.md)
- [Troubleshooting](TROUBLESHOOTING.md)
- [RSyslog guide](rsyslog/README.md)
