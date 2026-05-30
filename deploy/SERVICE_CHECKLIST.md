# Service Checklist

Use this during VPS setup, UAT, and demo day. Commands are inspection commands unless explicitly marked as start/reload.

## VPS Base

```bash
hostnamectl
df -h
free -h
uptime
```

Checklist:

```text
[ ] VPS reachable by SSH.
[ ] Disk has enough free space.
[ ] Timezone correct.
[ ] Non-root sudo user available.
```

## Web Server and PHP

```bash
sudo systemctl status nginx --no-pager
sudo systemctl status php8.2-fpm --no-pager
php -v
php -m
```

Checklist:

```text
[ ] Nginx running.
[ ] PHP-FPM running.
[ ] PHP version is 8.2 or newer.
[ ] Required PHP extensions installed.
[ ] Nginx points to Laravel `public/`.
```

Reload after config change:

```bash
sudo nginx -t
sudo systemctl reload nginx
sudo systemctl restart php8.2-fpm
```

## Laravel App

```bash
cd /var/www/centralized-log-monitoring
php artisan about
php artisan monitoring:health
php artisan route:list --except-vendor
php artisan migrate:status
```

Checklist:

```text
[ ] Laravel boots.
[ ] Database reachable.
[ ] `monitoring:health` has no FAIL.
[ ] Routes visible.
[ ] Migrations applied.
[ ] Admin login works.
```

## MariaDB or MySQL

```bash
sudo systemctl status mariadb --no-pager
sudo ss -tulpn | grep 3306
mysql -u <MONITORING_DB_USER> -p -e "SELECT 1"
```

Checklist:

```text
[ ] Database service running.
[ ] Monitoring database exists.
[ ] DB bound to localhost/internal network.
[ ] No public exposure unless intentionally secured.
```

## RSyslog

```bash
sudo rsyslogd -N1
sudo systemctl status rsyslog --no-pager
sudo ss -tulpn | grep 5514
sudo ls -lah /var/log/remote
sudo tail -n 20 /var/log/remote/all.log
```

Checklist:

```text
[ ] RSyslog config validates.
[ ] RSyslog running.
[ ] TCP/UDP listener active on intended port.
[ ] `/var/log/remote` exists.
[ ] `all.log` receives structured Windows Agent lines.
[ ] Laravel user can read log path.
```

## ZeroTier

```bash
sudo zerotier-cli status
sudo zerotier-cli listnetworks
ping <WINDOWS_CLIENT_ZEROTIER_IP>
```

Checklist:

```text
[ ] VPS joined ZeroTier network.
[ ] VPS authorized in ZeroTier dashboard.
[ ] Windows clients authorized.
[ ] Ping or service-level connectivity works.
```

## Firebird

Service name differs by OS/package.

```bash
sudo systemctl status firebird* --no-pager
sudo ss -tulpn | grep 3051
php -m | grep -i firebird
```

Checklist:

```text
[ ] Firebird 2.5 compatible service running.
[ ] Port 3051 reachable through ZeroTier.
[ ] Accurate database path known.
[ ] Backup procedure known before touching `.fdb`.
[ ] `pdo_firebird` installed on server that runs audit sync, or missing driver documented.
```

## Laravel Commands

```bash
php artisan rsyslog:parse
php artisan accurate:audit-sync --dry-run --limit=10
php artisan accurate:audit-sync --limit=100
php artisan alerts:detect
php artisan monitoring:health
```

Checklist:

```text
[ ] Parser reads new structured logs.
[ ] Audit dry-run maps rows without writes.
[ ] Audit sync writes monitoring DB only.
[ ] Alert detection creates contextual alerts only with target/evidence.
[ ] Health command does not print secrets.
```

## Scheduler or Manual UAT Loop

Recommended cron:

```cron
* * * * * cd /var/www/centralized-log-monitoring && php artisan schedule:run >> /dev/null 2>&1
```

Manual loop for UAT if scheduler not configured:

```bash
php artisan rsyslog:parse
php artisan accurate:audit-sync
php artisan alerts:detect
```

Checklist:

```text
[ ] Cron installed for production-like run, or manual UAT loop documented.
[ ] Commands tested one by one before scheduling.
```

## Windows Agent

On Windows:

```powershell
Test-NetConnection <VPS_ZEROTIER_IP> -Port 5514
Test-NetConnection <VPS_ZEROTIER_IP> -Port 3051
powershell.exe -ExecutionPolicy Bypass -File "C:\CentralizedLogAgent\agent.ps1" -ConfigPath "C:\CentralizedLogAgent\config.json" -DryRun
powershell.exe -ExecutionPolicy Bypass -File "C:\CentralizedLogAgent\agent.ps1" -ConfigPath "C:\CentralizedLogAgent\config.json"
```

Checklist:

```text
[ ] Agent dry-run succeeds.
[ ] Agent real run registers device.
[ ] Token file stays local only.
[ ] Heartbeat visible in dashboard.
[ ] Syslog visible on VPS.
[ ] Command polling works only for authorized device.
```

## Telegram

Checklist:

```text
[ ] `TELEGRAM_ALERT_ENABLED=true`.
[ ] Token and chat ID present in `.env`.
[ ] Alert notification sends contextual message.
[ ] Failed send records safe error without token.
[ ] Cooldown prevents spam.
```

## Remote Actions

Checklist:

```text
[ ] Remote Desktop launcher creates `OPEN_RDP` audit row.
[ ] RDP target uses ZeroTier IP or safe fallback.
[ ] Restart Client requires modal confirmation.
[ ] Restart Client requires reason.
[ ] Pending command visible.
[ ] Agent picks up only its own pending restart.
[ ] Agent reports success/failure.
[ ] No alert triggers restart automatically.
```
