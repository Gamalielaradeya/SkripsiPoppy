# Troubleshooting Guide

This guide is for real-device UAT. It avoids fake production data and keeps secrets out of logs/screenshots.

## 1. `monitoring:health` Shows FAIL

Run:

```bash
php artisan monitoring:health
```

Common causes:

```text
Database connection failed:
- `.env` DB values wrong.
- MariaDB/MySQL not running.
- Migration DB user has no permission.

RSyslog path not readable:
- `/var/log/remote` permission wrong.
- PHP-FPM user not in `adm` group.
- Path differs from `RSYSLOG_REMOTE_LOG_PATH`.

Command missing:
- Autoload/cache stale.
- Deployment did not pull latest code.
```

Fix:

```bash
php artisan optimize:clear
composer dump-autoload
sudo usermod -aG adm www-data
sudo systemctl restart php8.2-fpm
```

## 2. Device Does Not Appear

Check Windows:

```powershell
powershell.exe -ExecutionPolicy Bypass -File "C:\CentralizedLogAgent\agent.ps1" -ConfigPath "C:\CentralizedLogAgent\config.json" -DryRun
Test-NetConnection <DASHBOARD_HOST> -Port 443
Test-NetConnection <VPS_ZEROTIER_IP> -Port 5514
```

Check VPS:

```bash
sudo tail -n 50 /var/log/remote/all.log
php artisan rsyslog:parse
```

Likely causes:

```text
- Agent API URL wrong.
- Agent token mismatch after local runtime deletion.
- ZeroTier disconnected.
- RSyslog firewall blocked.
- Parser not run yet.
```

Do not create fake device rows. Fix agent registration or parser flow.

## 3. Heartbeat Works but Telemetry Empty

Check dry-run payload contains:

```text
cpu_usage_percent
ram_usage_percent
disk_usage_percent
uptime_seconds
last_boot_at
```

Likely causes:

```text
- Windows counters unavailable or permission-limited.
- `monitored_drive` wrong.
- Old agent config not copied from latest example.
```

The app must keep nulls as null. Do not replace with random `0` or demo values.

## 4. RSyslog Receives Logs but Dashboard Does Not Update

Run:

```bash
php artisan rsyslog:parse --path=/var/log/remote --limit=500
php artisan route:list --except-vendor
```

Check DB tables:

```text
logs
parser_offsets
parser_runs
devices
device_telemetries
network_checks
accurate_process_snapshots
```

Likely causes:

```text
- Message is not structured key=value.
- Missing `agent_id`.
- Parser offset already advanced during test.
- Laravel cannot read `/var/log/remote`.
```

## 5. Firebird TCP Check Fails

From Windows:

```powershell
Test-NetConnection <VPS_ZEROTIER_IP> -Port 3051
```

From VPS:

```bash
sudo systemctl status firebird* --no-pager
sudo ss -tulpn | grep 3051
sudo ufw status
```

Likely causes:

```text
- Firebird service stopped.
- Firewall blocks ZeroTier client.
- Wrong Firebird port.
- ZeroTier route/member not authorized.
```

Do not assume one client timeout means server down. Compare with other client checks and server service status.

## 6. Accurate Process Status Missing

Check Windows Agent config:

```json
"accurate_process_check_enabled": true,
"accurate_process_name": "accurate.exe"
```

Likely causes:

```text
- Accurate 5 not running.
- Process name differs.
- PowerShell lacks permission to read owner/path.
```

Owner/path may be null. This is acceptable and must not fail heartbeat.

## 7. Accurate Audit Does Not Sync

Run:

```bash
php artisan accurate:audit-sync --dry-run --limit=10
php artisan monitoring:health
```

Likely causes:

```text
- `pdo_firebird` missing.
- Firebird credential wrong.
- Database path wrong.
- Read-only user lacks SELECT.
- Table/column names differ from POC.
- `ACCURATE_AUDIT_ENABLED=false`.
```

Rules:

```text
- Do not write to Accurate database.
- Do not use `LOGIN` as main source.
- Do not require `COMP_NAME` or `IPADDRESS`.
- Preserve sync failure in monitoring DB when command runs real sync.
```

## 8. Alerts Not Created

Run:

```bash
php artisan alerts:detect
```

Check:

```text
- Device has latest heartbeat/telemetry.
- Network check exists for Firebird.
- Accurate process snapshot exists.
- Thresholds exist from seeders.
- Existing alert cooldown not blocking duplicate.
```

Alert must have:

```text
target
detected_by
evidence
impact
recommended_action
```

Do not create vague alerts manually to make the demo look busy.

## 9. Telegram Does Not Send

Check:

```bash
php artisan monitoring:health
```

Likely causes:

```text
- `TELEGRAM_ALERT_ENABLED=false`.
- Bot token missing/wrong.
- Chat ID missing/wrong.
- VPS cannot reach Telegram API.
- Cooldown active.
```

Inspect `alert_notifications` status. Do not print token or chat ID.

## 10. Remote Desktop Fails

Check:

```text
- Device has ZeroTier IP or local IP.
- Windows RDP enabled.
- Windows firewall allows RDP on ZeroTier interface.
- Admin has Windows credentials.
- Device online/reachable.
```

Dashboard only launches/generates RDP target and logs `OPEN_RDP`. It does not bypass Windows RDP permissions.

## 11. Remote Restart Does Not Execute

Check:

```text
- Restart action exists and status is pending.
- Reason was provided.
- Agent `command_poll_enabled=true`.
- Agent API URL reachable.
- Agent token matches same `agent_id`.
- Command not expired.
- Windows process has permission to run shutdown.
```

Run agent once manually:

```powershell
powershell.exe -ExecutionPolicy Bypass -File "C:\CentralizedLogAgent\agent.ps1" -ConfigPath "C:\CentralizedLogAgent\config.json"
```

Remote restart must never be triggered automatically by an alert.

## 12. Demo Fallback

If live component fails during thesis demo:

```text
ZeroTier issue:
- Show stored real-device data from prior successful test.

Telegram issue:
- Show alert detail and alert_notifications failed/sent record.

RDP issue:
- Show launcher and explain Windows firewall/RDP dependency.

Restart risk:
- Show pending command and audit trail without executing real restart.

Firebird stop too risky:
- Show prior critical alert/evidence from real test result.
```

Fallback must be honest. Do not claim fake data as live real-device proof.
