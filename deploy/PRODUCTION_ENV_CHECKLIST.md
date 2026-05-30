# Production Environment Checklist

Use this as `.env` review guide. Do not copy real values into this file. Do not commit production `.env`.

## App

```dotenv
APP_NAME="Centralized Log Monitoring Dashboard"
APP_ENV=production
APP_KEY=<generated-by-php-artisan-key-generate>
APP_DEBUG=false
APP_URL=https://monitoring.example.com
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
APP_FALLBACK_LOCALE=en
```

Checklist:

```text
[ ] APP_KEY generated on server.
[ ] APP_DEBUG=false.
[ ] APP_URL points to final domain or VPS URL.
[ ] Timezone matches demo/report context.
```

## Logging and Queue

```dotenv
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=warning
QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
```

Checklist:

```text
[ ] Logs do not expose secrets.
[ ] Queue driver chosen intentionally.
[ ] `php artisan queue:table` migration exists through Laravel default jobs table.
```

## Database

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=centralized_log_monitoring
DB_USERNAME=<MONITORING_DB_USER>
DB_PASSWORD=<MONITORING_DB_PASSWORD>
```

Checklist:

```text
[ ] Monitoring DB exists.
[ ] DB user has access only to monitoring DB.
[ ] MySQL/MariaDB is not exposed publicly.
[ ] `php artisan migrate --force --seed` completed.
```

## RSyslog Parser

```dotenv
RSYSLOG_REMOTE_LOG_PATH=/var/log/remote
PARSER_BATCH_LIMIT=500
```

Checklist:

```text
[ ] RSyslog writes to /var/log/remote.
[ ] Laravel/PHP user can read path.
[ ] Parser batch limit is positive.
[ ] `php artisan rsyslog:parse` works.
```

## Telegram

```dotenv
TELEGRAM_ALERT_ENABLED=true
TELEGRAM_BOT_TOKEN=<TELEGRAM_BOT_TOKEN>
TELEGRAM_CHAT_ID=<TELEGRAM_CHAT_ID>
TELEGRAM_ALERT_COOLDOWN_MINUTES=5
```

Checklist:

```text
[ ] Bot token stored only in production `.env`.
[ ] Chat ID stored only in production `.env`.
[ ] Bot can send a test message.
[ ] Alerts page records notification status.
[ ] No token appears in logs, screenshots, README, or terminal output.
```

## Accurate Firebird Audit

```dotenv
ACCURATE_AUDIT_ENABLED=true
ACCURATE_FIREBIRD_HOST=<FIREBIRD_HOST_OR_VPS_ZEROTIER_IP>
ACCURATE_FIREBIRD_PORT=3051
ACCURATE_FIREBIRD_DATABASE=<FIREBIRD_DATABASE_PATH>
ACCURATE_FIREBIRD_USERNAME=<READ_ONLY_USER>
ACCURATE_FIREBIRD_PASSWORD=<READ_ONLY_PASSWORD>
ACCURATE_FIREBIRD_CHARSET=NONE
ACCURATE_AUDIT_SYNC_LIMIT=100
```

Checklist:

```text
[ ] Firebird version compatible with Accurate 5.
[ ] User is read-only if possible.
[ ] Query reads `AUDIT + USERS`.
[ ] `LOGIN` is not used as primary source.
[ ] `COMP_NAME` and `IPADDRESS` are allowed to be null.
[ ] `php artisan accurate:audit-sync --dry-run` succeeds before real sync.
```

## Agent and Remote Actions

```dotenv
AGENT_API_ENABLED=true
REMOTE_RESTART_ENABLED=true
REMOTE_RESTART_REQUIRE_REASON=true
REMOTE_ACTION_COMMAND_EXPIRY_MINUTES=10
REMOTE_RESTART_DELAY_SECONDS=30
```

Checklist:

```text
[ ] HTTPS enabled for API where possible.
[ ] Agent token values are never printed.
[ ] Restart requires confirmation and reason.
[ ] Restart is never automatic from alert detection.
[ ] Remote action result reporting works.
```

## Final Preflight

Run:

```bash
php artisan monitoring:health
php artisan route:list --except-vendor
php artisan test
npm run build
```

Expected:

```text
[ ] No FAIL from `monitoring:health`.
[ ] WARN items understood and documented.
[ ] Routes available.
[ ] Tests pass.
[ ] Frontend build passes.
```
