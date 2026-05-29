# Centralized Log Monitoring Dashboard

Laravel foundation for real-device IT monitoring dashboard for PT XYZ small office Accurate 5 environment.

Milestone 2B scope:

- Clean Laravel project scaffold.
- Blade, Tailwind CSS, Alpine.js, Chart.js dependency.
- Basic admin login/logout.
- Protected placeholder routes.
- IT operations cockpit base layout.
- Reusable status/severity badge and empty-state components.
- Database foundation for real-device monitoring tables.
- Remaining database foundation for contextual alerts, evidence, incidents, remote actions, and Accurate audit storage.
- Seeded threshold settings and non-secret system setting placeholders.

Windows Agent, RSyslog parser, Firebird audit reader, Telegram integration, remote restart, alert logic, random demo logs, and hardcoded device names are not implemented in this milestone.

## Stack

- Laravel 12
- Laravel Blade
- Tailwind CSS 4
- Alpine.js
- Chart.js
- SQLite for local development by default
- MySQL/MariaDB target for deployment

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

## Database Setup

Local development may use SQLite from `.env.example`. The target deployment database is MySQL or MariaDB.

For MySQL/MariaDB, set these values in `.env` before running migrations:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=centralized_log_monitoring
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

Then run:

```bash
php artisan migrate:fresh --seed
```

The seeders create only the admin user, threshold values, and placeholder system settings. They do not create fake devices, fake logs, fake monitoring data, or real secrets.

Milestone 2B adds these persistence tables without execution logic:

- `accurate_audit_sources`, `accurate_audit_events`, `accurate_audit_sync_states`, `accurate_audit_sync_runs`
- `alerts`, `alert_evidences`, `alert_notifications`
- `incidents`, `incident_alerts`
- `remote_actions`

Accurate audit rows are intended to come from Firebird `AUDIT + USERS`; `LOGIN` is not a primary audit source. `comp_name` and `ip_address` are nullable because the POC found those fields may be empty.

Remote actions support `OPEN_RDP`, `RESTART_CLIENT`, `PING_TEST`, and `RESTART_AGENT` as stored action types only. Execution and Windows Agent polling are not implemented yet.

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
