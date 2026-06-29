# Centralized Log Monitoring Dashboard

Real-device IT monitoring dashboard for a small PT XYZ office using Accurate 5 on Windows clients.

The system monitors Windows laptops through a PowerShell Windows Agent, sends structured monitoring logs to RSyslog on a Linux server, parses those logs in Laravel, stores operational data in MySQL, reads Accurate audit trail directly from Firebird `AUDIT + USERS`, sends contextual Telegram alerts, correlates alerts into operational incidents, and supports manual Remote Desktop plus manual Restart Client actions.

This is not a random log demo. Raw logs stay in Advanced Logs; Dashboard focuses on device status, Windows user, Firebird connectivity, Accurate process status, telemetry, audit activity, alerts, incidents, and remote actions.

## Stack

- Laravel 12
- Laravel Blade
- Tailwind CSS 4
- Alpine.js
- MySQL for server deployment
- SQLite for local development
- RSyslog (port 5515, ZeroTier-only)
- Firebird 2.5.4 ACCURATE (native install, port 3051)
- PowerShell Windows Agent
- ZeroTier private network
- Telegram Bot API

Forbidden by project scope: React, Node.js backend, ELK, Grafana, Prometheus, enterprise SIEM tooling, automatic remediation, automatic restart.

## Server Setup (Production)

### OS & Services

- Ubuntu 24.04 LTS
- PHP 8.3 + Nginx + MySQL 8
- ZeroTier network ID: `e4da7455b2b688af`
- Server ZeroTier IP: `10.147.17.236`
- Server public IP: `160.187.211.208`

### Firebird Accurate (Native Install)

Firebird must be installed using the official CPSoft Accurate installer, NOT Docker or stock Firebird. Accurate 5 Windows client cannot connect to stock Firebird — it requires the custom build.

```bash
# Extract and install
tar -xzf FirebirdACCURATE-2.5.4.amd64.tar.gz
cd FirebirdACCURATE-2.5.4.amd64
# Fix dependency: symlink libncurses.so.5 -> libncurses.so.6
ln -sf /usr/lib/x86_64-linux-gnu/libncurses.so.6 /usr/lib/x86_64-linux-gnu/libncurses.so.5
./install.sh
```

Installed to `/opt/firebird/`. Config (`/opt/firebird/firebird.conf`):

```
RemoteServicePort = 3051
RemoteAuxPort = 3052
```

Key details:
- `security2.fdb` from installer has hardcoded SYSDBA password (not masterkey)
- User GUEST/guest works for app read-only access
- Database: `/opt/firebird/data/XYZ.GDB` (29MB, 239 Accurate tables)
- Restart: `/etc/init.d/firebird restart`
- Accurate 5 Windows client connects to `IP/3051` with database path `/opt/firebird/data/XYZ.GDB`

### RSyslog Configuration

RSyslog listens on port 5515 (TCP+UDP) and only accepts logs from ZeroTier IPs (`10.147.0.0/16`) to block internet scanners.

Config: `/etc/rsyslog.d/60-skripsi-poppy.conf`

```
module(load="imudp")
module(load="imtcp")

template(name="RemoteHostFile" type="string" string="/var/log/remote/%HOSTNAME%.log")

ruleset(name="skripsi_remote") {
    if ($fromhost-ip startswith '10.147.') or ($fromhost-ip == '127.0.0.1') then {
        action(type="omfile" dynaFile="RemoteHostFile")
        action(type="omfile" file="/var/log/remote/all.log")
    }
    stop
}

input(type="imudp" port="5515" ruleset="skripsi_remote")
input(type="imtcp" port="5515" ruleset="skripsi_remote")
```

Logs stored in `/var/log/remote/`:
- Per-device: `%IP%.log` (e.g., `10.147.17.181.log`)
- Combined: `all.log`

### Firewall (UFW)

| Port | Protocol | Source | Purpose |
|------|----------|--------|---------|
| 22 | TCP | Anywhere | SSH |
| 80 | TCP | Anywhere | Web dashboard |
| 3051 | TCP | Anywhere | Firebird (Accurate 5 client) |
| 3052 | TCP | Anywhere | Firebird aux |
| 5515 | TCP+UDP | ZeroTier only | RSyslog (device logs) |
| 9993 | UDP | Anywhere | ZeroTier |

### Cron Jobs (every 1 minute)

```bash
*/1 * * * * cd /var/www/skripsi-poppy && flock -n /tmp/rsyslog-parse.lock php artisan rsyslog:parse >> storage/logs/rsyslog-parse-cron.log 2>&1
*/1 * * * * cd /var/www/skripsi-poppy && flock -n /tmp/alerts-detect.lock php artisan alerts:detect >> storage/logs/alerts-detect-cron.log 2>&1
*/1 * * * * cd /var/www/skripsi-poppy && flock -n /tmp/accurate-audit-sync.lock php artisan accurate:audit-sync >> storage/logs/accurate-audit-cron.log 2>&1
*/1 * * * * cd /var/www/skripsi-poppy && flock -n /tmp/incidents-correlate.lock php artisan incidents:correlate >> storage/logs/incidents-correlate-cron.log 2>&1
```

## Artisan Commands

### Automated (cron, every 1 minute)

| Command | Description |
|---------|-------------|
| `php artisan rsyslog:parse` | Parse remote syslog logs from `/var/log/remote/` into `log_entries` table. Tracks file offset to avoid duplicates. |
| `php artisan alerts:detect` | Detect contextual alerts from telemetry/logs (CPU high, RAM high, device offline, Firebird down, Accurate not running). Sends Telegram notification if configured. |
| `php artisan accurate:audit-sync` | Sync audit activity from Firebird `AUDIT + USERS` tables into `accurate_audit_events`. Converts timestamps from UTC to Asia/Jakarta. |
| `php artisan incidents:correlate` | Correlate open alerts into operational incidents (15-minute window, 6 correlation rules). |

### Manual (on-demand)

| Command | Description |
|---------|-------------|
| `php artisan monitoring:health` | Read-only deployment health check. Prints PASS/WARN/FAIL. Never exposes secrets. |
| `php artisan data:wipe --force` | Wipe ALL monitoring data (devices, alerts, incidents, telemetry, logs, parser offsets, sync states). Preserves: settings, accurate audit events, users. |
| `php artisan accurate:audit-sync --dry-run` | Preview audit sync without writing. |

## Local Development Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Default development admin:

```text
email: admin@example.com
password: password
```

Change this before any demo or production-like use.

Local `.env.example` defaults to SQLite. For MySQL:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=centralized_log_monitoring
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

Run:

```bash
php artisan migrate:fresh --seed
```

Seeders create only admin user, threshold settings, and safe placeholder system settings. They do not create fake devices, fake logs, fake monitoring data, or real secrets.

## VPS Deployment Overview

Detailed deployment docs live in [deploy/README.md](deploy/README.md).

High-level server setup:

1. Install Linux base packages, PHP 8.3 extensions (pdo_mysql, pdo_firebird), Composer, Node.js build tooling, Nginx, MySQL, RSyslog, ZeroTier.
2. Install Firebird Accurate 2.5.4 native (NOT Docker) from CPSoft installer.
3. Clone repository into `/var/www/skripsi-poppy`.
4. Configure `.env` from `.env.example` using production values only on server.
5. Run `composer install --no-dev --optimize-autoloader`.
6. Run `npm ci && npm run build` if assets are built on server.
7. Run `php artisan key:generate`, `php artisan migrate --force --seed`, `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`.
8. Configure Nginx to serve `public/`.
9. Configure RSyslog with ZeroTier IP filter.
10. Configure UFW firewall rules.
11. Set up cron jobs (4 commands, every 1 minute).
12. Configure ZeroTier, Firebird database, Telegram env, and Windows Agent clients.

No deployment script in this repo mutates a server automatically. Apply commands manually and verify each service.

## Windows Agent Quick Start

Read [windows-agent/README.md](windows-agent/README.md) before installing.

On each Windows client:

```powershell
Copy-Item "C:\ProgramData\CentralizedLogMonitoring\agent\config.example.json" "C:\ProgramData\CentralizedLogMonitoring\agent\config.json"
notepad "C:\ProgramData\CentralizedLogMonitoring\agent\config.json"
```

Set safe local values:

```json
{
  "api_base_url": "http://160.187.211.208/api/agent",
  "runtime_path": "C:\\ProgramData\\CentralizedLogMonitoring",
  "syslog_enabled": true,
  "syslog_host": "160.187.211.208",
  "syslog_port": 5515,
  "firebird_check_enabled": true,
  "firebird_host": "10.147.17.236",
  "firebird_port": 3051,
  "accurate_process_check_enabled": true,
  "accurate_process_name": "accurate.exe",
  "command_poll_enabled": false
}
```

Run agent:

```powershell
powershell.exe -ExecutionPolicy Bypass -File "C:\ProgramData\CentralizedLogMonitoring\agent\agent.ps1" -ConfigPath "C:\ProgramData\CentralizedLogMonitoring\agent\config.json"
```

The agent sends 5 log types per run via syslog:
- `device-monitor`: heartbeat, hostname, Windows user, IP, RDP status
- `perf-monitor`: CPU%, RAM%, disk%
- `heartbeat-monitor`: uptime, last boot time
- `network-monitor`: Firebird TCP connectivity, latency
- `accurate-process-monitor`: accurate.exe running status, PID, owner, path

## Accurate 5 Client Connection

On Windows laptop with Accurate 5 installed:
1. Open Accurate 5
2. Select Open Database → Mesin Lain (Other Machine)
3. Server Name: `160.187.211.208/3051`
4. File Name: `/opt/firebird/data/XYZ.GDB`
5. Click OK

Firebird native Accurate build handles authentication automatically (hardcoded SYSDBA credential). No manual user/password input needed in Accurate 5.

## Data Wipe

```bash
php artisan data:wipe --force
```

Clears: devices, alerts, incidents, telemetry, log entries, remote actions, agent credentials, audit sync runs/states, parser offsets/runs.
Preserves: settings, accurate audit events (242+), users.

After wipe, also clear log files:

```bash
rm -f /var/log/remote/*.log
systemctl restart rsyslog
```

## Security Notes

- Do not commit `.env`.
- Do not commit real Telegram token.
- Do not commit Firebird credentials.
- Do not commit agent tokens.
- Do not commit VPS keys.
- RSyslog port 5515 filtered to ZeroTier IPs only (blocks internet scanners).
- Firebird port 3051 open for Accurate 5 client access.
- Accurate audit reader is read-only using GUEST/guest, queries `AUDIT + USERS` tables only.
- Remote restart is manual, confirmed, reasoned, and audited.
- No fake production data or random demo seed data.

## Milestone Summary

Completed:

1. Laravel Foundation and Auth
2. Database Foundation
3. Agent Registration API
4. UI Foundation and Hallmark Design Pass
5. Windows Agent PowerShell MVP
6. Device Telemetry Real
7. RSyslog Structured Log Pipeline
8. Firebird Connectivity and Accurate Process Monitoring
9. Accurate Firebird Audit Reader
10. Contextual Alerts and Telegram
11. Remote Desktop and Remote Restart Manual
12. Deployment, Testing, Real-Device UAT

## Documentation

- [Deployment guide](deploy/README.md)
- [Production env checklist](deploy/PRODUCTION_ENV_CHECKLIST.md)
- [Service checklist](deploy/SERVICE_CHECKLIST.md)
- [Troubleshooting](deploy/TROUBLESHOOTING.md)
- [Real-device UAT checklist](Dokumentasi/16_Milestone_12_UAT_Checklist.md)
