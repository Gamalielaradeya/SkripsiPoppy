# Centralized Log Monitoring Dashboard

Laravel foundation for real-device IT monitoring dashboard for PT XYZ small office Accurate 5 environment.

Milestone 1 scope only:

- Clean Laravel project scaffold.
- Blade, Tailwind CSS, Alpine.js, Chart.js dependency.
- Basic admin login/logout.
- Protected placeholder routes.
- IT operations cockpit base layout.
- Reusable status/severity badge and empty-state components.

No monitoring tables, Windows Agent, RSyslog parser, Firebird audit reader, Telegram integration, remote restart, random demo logs, or hardcoded device names are implemented in this milestone.

## Stack

- Laravel 12
- Laravel Blade
- Tailwind CSS 4
- Alpine.js
- Chart.js
- SQLite for local auth bootstrap by default
- MySQL/MariaDB target for later monitoring database milestones

## Documentation

Primary project documents are in `Dokumentasi/`. Read `AGENTS.md` before implementing new work.

## Local Setup

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

Change this credential before production/demo use.

## Development Commands

```bash
php artisan route:list
php artisan test
npm run build
php artisan serve
```

## Current Routes

Protected routes:

```text
/dashboard
/devices
/devices/{id}
/accurate-audit
/accurate-audit/{id}
/incidents
/incidents/{id}
/alerts
/alerts/{id}
/remote-actions
/remote-actions/{id}
/advanced-logs
/advanced-logs/{id}
/settings
```

## Security Notes

Do not commit `.env`, Firebird credentials, Telegram token, ZeroTier secrets, agent tokens, VPS keys, or real production data.
