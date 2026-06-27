# 16 - Milestone 12 UAT Checklist

Milestone 12 validates deployment, testing, and real-device UAT for Centralized Log Monitoring Dashboard.

Target:

```text
2 Windows laptops running Accurate 5
1 VPS Linux
ZeroTier private network
RSyslog structured monitoring logs
Laravel dashboard
MySQL monitoring database
Firebird 2.5 Accurate database
Telegram contextual alerts
Manual Remote Desktop and Restart Client actions
```

Use real devices where possible. Do not seed fake production data or random demo logs.

## 1. Environment Identity

Fill during UAT. Do not write credentials here.

```text
UAT date:
Tester:
VPS OS:
VPS public access method:
VPS ZeroTier IP:
Laptop 1 label:
Laptop 1 hostname:
Laptop 1 ZeroTier IP:
Laptop 2 label:
Laptop 2 hostname:
Laptop 2 ZeroTier IP:
Dashboard URL:
Firebird host:
Firebird port:
Accurate DB test database:
Telegram receiver type: personal/group
```

## 2. Preflight

```text
[ ] Repository on expected branch or release tag.
[ ] `.env` configured on VPS without committing secrets.
[ ] `php artisan monitoring:health` has no FAIL.
[ ] `php artisan route:list --except-vendor` passes.
[ ] `php artisan test` passes locally or on staging.
[ ] `npm run build` passes.
[ ] Admin can login.
[ ] Dashboard is reachable from admin browser.
```

Evidence:

```text
Notes:
Screenshot/file reference:
Status: PASS / WARN / FAIL
```

## 3. VPS and Services

```text
[ ] VPS reachable by SSH.
[ ] Nginx or web server running.
[ ] PHP-FPM running.
[ ] MySQL running.
[ ] RSyslog running.
[ ] `/var/log/remote` exists.
[ ] Laravel can read `/var/log/remote`.
[ ] ZeroTier running on VPS.
[ ] Firebird 2.5 compatible service running.
[ ] Firebird port 3051 open only on intended network.
[ ] Laravel scheduler cron configured, or manual UAT command loop documented.
```

Commands:

```bash
php artisan monitoring:health
sudo systemctl status rsyslog --no-pager
sudo tail -n 20 /var/log/remote/all.log
```

Expected:

```text
Services active, RSyslog path readable, no secret printed.
```

Status: PASS / WARN / FAIL

## 4. ZeroTier Connectivity

```text
[ ] VPS joined ZeroTier network.
[ ] Laptop 1 joined and authorized.
[ ] Laptop 2 joined and authorized.
[ ] Laptop 1 can reach VPS ZeroTier IP.
[ ] Laptop 2 can reach VPS ZeroTier IP.
[ ] VPS can reach laptop ZeroTier IPs or service-level checks are documented if ping blocked.
```

Windows commands:

```powershell
ping <VPS_ZEROTIER_IP>
Test-NetConnection <VPS_ZEROTIER_IP> -Port 5514
Test-NetConnection <VPS_ZEROTIER_IP> -Port 3051
```

Expected:

```text
RSyslog and Firebird ports reachable from Windows clients through ZeroTier.
```

Status: PASS / WARN / FAIL

## 5. Windows Agent Registration

Per laptop:

```text
[ ] `config.json` exists only on local Windows client.
[ ] Runtime path configured.
[ ] Dry-run succeeds without HTTP writes.
[ ] Dry-run does not print agent token.
[ ] Real run registers device.
[ ] `agent_id` persists across reruns.
[ ] Device appears in dashboard.
[ ] Re-running agent does not create duplicate device.
```

Commands:

```powershell
powershell.exe -ExecutionPolicy Bypass -File "C:\CentralizedLogAgent\agent.ps1" -ConfigPath "C:\CentralizedLogAgent\config.json" -DryRun
powershell.exe -ExecutionPolicy Bypass -File "C:\CentralizedLogAgent\agent.ps1" -ConfigPath "C:\CentralizedLogAgent\config.json"
```

Expected:

```text
Device identity uses `agent_id`; hostname is metadata; device label can be edited.
```

Status: PASS / WARN / FAIL

## 6. Heartbeat

```text
[ ] Heartbeat submitted through API.
[ ] Device `last_seen_at` updates.
[ ] Device status becomes online.
[ ] Windows user appears when available.
[ ] Local IP or ZeroTier IP appears when available.
[ ] Agent version appears.
[ ] RDP status appears.
```

Expected:

```text
Dashboard and Devices page show latest real heartbeat metadata.
```

Status: PASS / WARN / FAIL

## 7. CPU/RAM/Disk Telemetry

```text
[ ] CPU usage submitted.
[ ] RAM usage submitted.
[ ] Disk usage submitted for configured drive.
[ ] Uptime submitted.
[ ] Last boot time submitted.
[ ] Null metrics stay null when Windows cannot provide them.
[ ] Dashboard and Device Detail show latest telemetry.
```

Expected:

```text
No random fallback values. Values are real or null.
```

Status: PASS / WARN / FAIL

## 8. RSyslog Structured Logs

```text
[ ] Agent syslog enabled.
[ ] RSyslog receives `device-monitor` line.
[ ] RSyslog receives `perf-monitor` line.
[ ] RSyslog receives `heartbeat-monitor` line.
[ ] RSyslog receives `network-monitor` line when Firebird check enabled.
[ ] RSyslog receives `accurate-process-monitor` line when Accurate check enabled.
[ ] `php artisan rsyslog:parse` stores raw lines in Advanced Logs.
[ ] Parser prevents duplicate rows on repeated runs.
```

Commands:

```bash
sudo tail -n 50 /var/log/remote/all.log
php artisan rsyslog:parse
```

Expected:

```text
Advanced Logs shows structured raw messages; Dashboard does not become raw log viewer.
```

Status: PASS / WARN / FAIL

## 9. Firebird TCP Connectivity

```text
[ ] Agent has `firebird_check_enabled=true`.
[ ] Agent target host is VPS/Firebird ZeroTier IP.
[ ] Agent target port is 3051 unless configured otherwise.
[ ] Connected state stored when Firebird reachable.
[ ] Timeout/failed state stored when unreachable test is performed safely.
[ ] Device-specific failure does not imply global Firebird down.
```

Expected:

```text
Device Detail shows latest Firebird TCP status and latency/evidence.
```

Status: PASS / WARN / FAIL

## 10. Accurate Process Detection

```text
[ ] Agent has `accurate_process_check_enabled=true`.
[ ] Accurate 5 running is detected as running.
[ ] Accurate closed is detected as not running or unavailable.
[ ] Process owner/path captured when Windows permits.
[ ] Null owner/path accepted when permissions block access.
```

Expected:

```text
Device and Dashboard show Accurate status per real device.
```

Status: PASS / WARN / FAIL

## 11. Accurate Audit Sync from Firebird

```text
[ ] Firebird read-only credential configured in `.env`.
[ ] `pdo_firebird` installed on sync server or missing driver documented.
[ ] `php artisan accurate:audit-sync --dry-run` succeeds.
[ ] `php artisan accurate:audit-sync` imports events.
[ ] Accurate Audit page shows events.
[ ] Filter by date/user/keyword works where data exists.
[ ] `LOGIN` is not used as primary source.
[ ] `COMP_NAME` and `IPADDRESS` may be empty without sync failure.
```

Expected:

```text
Audit data comes from Firebird `AUDIT + USERS`, not RSyslog.
```

Status: PASS / WARN / FAIL

## 12. Contextual Alert Detection

```text
[ ] `php artisan alerts:detect` runs.
[ ] Heartbeat missed/offline alert can be created from real condition or prior test data.
[ ] CPU/RAM/Disk alert can be created from real telemetry threshold.
[ ] Firebird connectivity alert uses network check evidence.
[ ] Accurate process alert uses process snapshot evidence.
[ ] Alert title includes target context.
[ ] Alert detail includes evidence, impact, recommended action.
[ ] Duplicate alerts respect cooldown.
```

Expected:

```text
No vague alert such as "Firebird unreachable" without target/evidence.
```

Status: PASS / WARN / FAIL

## 13. Telegram Notification

```text
[ ] Telegram enabled in `.env`.
[ ] Bot token and chat ID present only in `.env`.
[ ] Contextual alert sends Telegram notification.
[ ] Message includes severity, target, detected by, evidence, impact, recommended action, time, dashboard link if available.
[ ] Failure records safe error without token.
[ ] Cooldown prevents spam.
```

Expected:

```text
Telegram message is actionable, not a raw one-line log.
```

Status: PASS / WARN / FAIL

## 14. Remote Desktop Launcher

```text
[ ] Device has ZeroTier IP or safe fallback IP.
[ ] RDP status visible.
[ ] Admin clicks Remote Desktop from Device Detail or Devices.
[ ] `OPEN_RDP` remote action logged.
[ ] `.rdp` file or mstsc target can be used by admin.
[ ] No restart or command polling occurs from RDP action.
```

Expected:

```text
Remote Desktop remains manual admin action and is audited.
```

Status: PASS / WARN / FAIL

## 15. Manual Remote Restart Flow

```text
[ ] Restart button visible for registered device.
[ ] Confirmation modal appears.
[ ] Reason is required.
[ ] Submit without reason is rejected.
[ ] Confirmed action creates pending `RESTART_CLIENT`.
[ ] Agent polling returns only command for same authorized `agent_id`.
[ ] Agent schedules restart only when enabled.
[ ] Agent reports result.
[ ] Remote Actions page shows status/result.
[ ] No alert auto-triggers restart.
```

Expected:

```text
Restart is manual, confirmed, reasoned, token-authorized, and audited.
```

Status: PASS / WARN / FAIL

## 16. Page Checks

Advanced Logs:

```text
[ ] `/advanced-logs` loads.
[ ] Raw structured logs visible.
[ ] Filters/search work where data exists.
```

Accurate Audit:

```text
[ ] `/accurate-audit` loads.
[ ] Imported audit events visible.
[ ] Detail page loads.
```

Alerts:

```text
[ ] `/alerts` loads.
[ ] Alert detail shows target/evidence/impact/action.
[ ] Acknowledge/resolve works.
```

Remote Actions:

```text
[ ] `/remote-actions` loads.
[ ] `OPEN_RDP` and `RESTART_CLIENT` actions visible after tests.
[ ] Detail page shows reason/result/status.
```

Status: PASS / WARN / FAIL

## 17. Demo Readiness

Before thesis demo:

```text
[ ] VPS online.
[ ] Dashboard reachable.
[ ] Admin login works.
[ ] Two real Windows devices registered.
[ ] Both devices show heartbeat.
[ ] At least one device shows Accurate running.
[ ] Firebird TCP connected from clients.
[ ] Accurate audit events imported.
[ ] At least one contextual alert available.
[ ] Telegram notification available.
[ ] Remote Desktop launcher tested.
[ ] Restart Client flow tested safely or shown as pending command.
[ ] Advanced Logs contain real structured logs.
[ ] No real secrets visible in screenshots or terminal.
```

## 18. Final Result

```text
Overall UAT status: PASS / PASS WITH NOTES / FAIL
Failed items:
Risk notes:
Fallback evidence:
Reviewer signature:
Date:
```
