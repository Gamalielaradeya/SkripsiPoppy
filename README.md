# Centralized Log Monitoring Dashboard

Real-device IT monitoring dashboard for a small PT XYZ office using Accurate 5 on Windows clients.

The system monitors Windows laptops through a PowerShell Windows Agent, sends structured monitoring logs to RSyslog on a VPS, parses those logs in Laravel, stores operational data in MySQL, reads Accurate audit trail directly from Firebird `AUDIT + USERS`, sends contextual Telegram alerts, and supports manual Remote Desktop plus manual Restart Client actions.

This is not a random log demo. Raw logs stay in Advanced Logs; Dashboard focuses on device status, Windows user, Firebird connectivity, Accurate process status, telemetry, audit activity, alerts, incidents, and remote actions.

## Stack

- Laravel 12
- Laravel Blade
- Tailwind CSS 4
- Alpine.js
- Chart.js
- MySQL for VPS deployment
- SQLite for local development
- RSyslog
- Firebird 2.5 for Accurate database
- PowerShell Windows Agent
- ZeroTier private network
- Telegram Bot API

Forbidden by project scope: React, Node.js backend, ELK, Grafana, Prometheus, enterprise SIEM tooling, automatic remediation, automatic restart.

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

High-level VPS setup:

1. Install Linux base packages, PHP extensions, Composer, Node.js build tooling if building assets on VPS, Nginx, MySQL, RSyslog, ZeroTier, and Firebird 2.5.
2. Clone repository into `/var/www/centralized-log-monitoring`.
3. Configure `.env` from [.env.example](.env.example) using production values only on server.
4. Run `composer install --no-dev --optimize-autoloader`.
5. Run `npm ci && npm run build` if assets are built on VPS.
6. Run `php artisan key:generate`, `php artisan migrate --force --seed`, `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`.
7. Configure Nginx to serve `public/`.
8. Configure RSyslog using [deploy/rsyslog/rsyslog.conf](deploy/rsyslog/rsyslog.conf).
9. Configure Laravel scheduler cron.
10. Configure ZeroTier, Firebird/pdo_firebird, Telegram env, and Windows Agent clients.

No deployment script in this repo mutates a server automatically. Apply commands manually and verify each service.

## Windows Agent Quick Start

Read [windows-agent/README.md](windows-agent/README.md) before installing.

On each Windows client:

```powershell
Copy-Item "C:\CentralizedLogAgent\config.example.json" "C:\CentralizedLogAgent\config.json"
notepad "C:\CentralizedLogAgent\config.json"
```

Set safe local values:

```json
{
  "api_base_url": "https://monitoring.example.com/api/agent",
  "runtime_path": "C:\\ProgramData\\CentralizedLogMonitoring",
  "syslog_enabled": true,
  "syslog_host": "<VPS_ZEROTIER_IP>",
  "syslog_port": 5514,
  "firebird_check_enabled": true,
  "firebird_host": "<VPS_ZEROTIER_IP>",
  "firebird_port": 3051,
  "accurate_process_check_enabled": true,
  "accurate_process_name": "accurate.exe",
  "command_poll_enabled": true
}
```

Dry run:

```powershell
powershell.exe -ExecutionPolicy Bypass -File "C:\CentralizedLogAgent\agent.ps1" -ConfigPath "C:\CentralizedLogAgent\config.json" -DryRun
```

Real run:

```powershell
powershell.exe -ExecutionPolicy Bypass -File "C:\CentralizedLogAgent\agent.ps1" -ConfigPath "C:\CentralizedLogAgent\config.json"
```

The local files `windows-agent/config.json` and `windows-agent/.runtime/*` must never be committed. Agent token values must not be printed, pasted, or screenshotted.

## Common Commands

```bash
php artisan test
php artisan route:list --except-vendor
npm run build
php artisan monitoring:health
php artisan rsyslog:parse
php artisan accurate:audit-sync --dry-run
php artisan accurate:audit-sync
php artisan alerts:detect
php artisan schedule:run
php artisan serve
```

RSyslog parser:

```bash
php artisan rsyslog:parse --path=/var/log/remote --limit=500
```

Accurate audit sync:

```bash
php artisan accurate:audit-sync --limit=100
```

Alert detection:

```bash
php artisan alerts:detect
```

Health check:

```bash
php artisan monitoring:health
```

`monitoring:health` is read-only and prints only PASS/WARN/FAIL status. It never prints Telegram token, Firebird password, DB password, or agent token.

## Demo Flow

1. Start Laravel/Nginx and confirm login.
2. Confirm ZeroTier is online on VPS and Windows clients.
3. Run Windows Agent dry-run on one client and show real payload shape without tokens.
4. Run Windows Agent real mode on both clients.
5. Confirm RSyslog receives structured lines in `/var/log/remote/all.log`.
6. Run `php artisan rsyslog:parse`.
7. Open Dashboard and Devices; verify real devices, heartbeat, Windows user, CPU/RAM/Disk, Firebird TCP status, Accurate process status.
8. Run `php artisan accurate:audit-sync` and open Accurate Audit.
9. Run `php artisan alerts:detect` and open Alerts.
10. Confirm Telegram contextual notification for a safe warning or prior real test alert.
11. Show Remote Desktop launcher from Device Detail.
12. Show Restart Client modal, required reason, pending command, agent polling result, and Remote Actions audit log.
13. Open Advanced Logs to show raw structured logs are available for investigation but not dashboard focus.

## Security Notes

- Do not commit `.env`.
- Do not commit real Telegram token.
- Do not commit Firebird credentials.
- Do not commit agent tokens.
- Do not commit VPS keys.
- Keep Firebird and RSyslog reachable only through ZeroTier where possible.
- Accurate audit reader must be read-only and use `AUDIT + USERS`, not `LOGIN`.
- Remote restart must be manual, confirmed, reasoned, and audited.
- No fake production data or random demo seed data.

## Milestone 12 Docs

- [Deployment guide](deploy/README.md)
- [Production env checklist](deploy/PRODUCTION_ENV_CHECKLIST.md)
- [Service checklist](deploy/SERVICE_CHECKLIST.md)
- [Troubleshooting](deploy/TROUBLESHOOTING.md)
- [Real-device UAT checklist](Dokumentasi/16_Milestone_12_UAT_Checklist.md)
