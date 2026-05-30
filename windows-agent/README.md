# Windows Agent PowerShell MVP

PowerShell agent for Centralized Log Monitoring Dashboard. Milestone 5 registered real Windows devices and sent heartbeat metadata. Milestone 6 adds real CPU, RAM, and disk telemetry to the heartbeat API payload. Milestone 7 adds optional structured syslog output to RSyslog for Advanced Logs. Milestone 8 adds Firebird TCP connectivity and Accurate process checks. Milestone 11 adds optional command polling for manual Restart Client actions.

## Scope

Implemented:

- Persistent `agent_id`
- Local JSON config
- Agent registration through `POST /api/agent/register`
- Local token storage after first registration
- Heartbeat through `POST /api/agent/heartbeat`
- Hostname, Windows user, local IPv4, ZeroTier IPv4, uptime, last boot time, RDP status, and agent version
- CPU usage percent from Windows performance counters/CIM
- RAM usage percent from Windows OS memory counters
- Disk usage percent for the configured drive, default `C:`
- Optional structured syslog messages for `device-monitor`, `perf-monitor`, and `heartbeat-monitor`
- Optional Firebird TCP connectivity check to configured host/port, default port `3051`
- Optional Accurate process detection for configured executable name, default `accurate.exe`
- Optional structured syslog messages for `network-monitor` and `accurate-process-monitor`
- Optional command polling through `GET /api/agent/commands/pending`
- Manual `RESTART_CLIENT` execution through `shutdown.exe /r /t <delay> /c "<reason>"`
- Command result reporting through `POST /api/agent/commands/{id}/result`
- `-DryRun` mode

Not implemented in this milestone:

- Firebird database login or query
- Accurate Firebird Audit Reader
- Telegram notifications
- Alert detection
- Arbitrary shell commands
- File transfer
- Screen sharing
- Auto restart from alerts

## Runtime Files

Default runtime path:

```text
C:\ProgramData\CentralizedLogMonitoring\
```

Files created by the agent:

```text
C:\ProgramData\CentralizedLogMonitoring\agent_id.txt
C:\ProgramData\CentralizedLogMonitoring\agent-token.txt
```

`agent-token.txt` stores the plaintext agent token locally because the Laravel API returns the token only once. Do not commit this file or paste its contents into logs, screenshots, or documentation.

## Setup

1. Copy this folder to the Windows client, for example:

```text
C:\ProgramData\CentralizedLogMonitoring\agent\
```

2. Copy the sample config:

```powershell
Copy-Item "C:\ProgramData\CentralizedLogMonitoring\agent\config.example.json" "C:\ProgramData\CentralizedLogMonitoring\agent\config.json"
```

3. Edit `config.json` and set `api_base_url` to the Laravel Agent API base URL:

```json
{
  "api_base_url": "http://10.147.20.5:8000/api/agent",
  "agent_version": "1.0.0",
  "runtime_path": "C:\\ProgramData\\CentralizedLogMonitoring",
  "monitored_drive": "C:",
  "rdp_port": 3389,
  "firebird_check_enabled": false,
  "firebird_host": "",
  "firebird_port": 3051,
  "firebird_timeout_seconds": 3,
  "accurate_process_check_enabled": false,
  "accurate_process_name": "accurate.exe",
  "request_timeout_seconds": 15,
  "command_poll_enabled": false,
  "command_poll_interval_seconds": 30,
  "restart_delay_seconds": 30,
  "syslog_enabled": false,
  "syslog_host": "10.147.20.5",
  "syslog_port": 5514,
  "syslog_protocol": "udp",
  "syslog_app_name": "centralized-monitoring-agent"
}
```

Use the VPS ZeroTier IP or production HTTPS URL when available.

`monitored_drive` controls which Windows logical disk is reported as `disk_usage_percent`. Use `C:` unless the Accurate workstation stores its main working data on another local drive.

Set `firebird_check_enabled` to `true` only after `firebird_host` is set to the Firebird server IP/host reachable from the Windows client, usually the VPS ZeroTier IP. The agent only performs a TCP connect test to `firebird_host:firebird_port`; it does not log in to Firebird, query the database, or read Accurate audit tables.

Set `accurate_process_check_enabled` to `true` to detect the configured Windows process. Owner and path may be null if Windows permissions block access.

Set `syslog_enabled` to `true` only after the VPS RSyslog receiver is ready. Use the VPS ZeroTier IP for `syslog_host`. The PowerShell MVP supports UDP syslog only, so keep `syslog_protocol` as `udp`.

Syslog messages do not include the local agent token or any API secret.

Set `command_poll_enabled` to `true` only after the dashboard API is reachable and the device is registered. The agent only supports `RESTART_CLIENT`; unsupported command types are ignored. Remote Desktop does not use command polling because RDP is launched from the admin dashboard/client.

`restart_delay_seconds` controls the Windows shutdown delay. Keep a short delay such as `30` seconds for real-device UAT so the agent can report the result before Windows restarts.

## Dry Run

Dry run creates or loads local `agent_id`, collects Windows data, prints register and heartbeat payloads, and sends no HTTP requests.

```powershell
powershell.exe -ExecutionPolicy Bypass -File "C:\ProgramData\CentralizedLogMonitoring\agent\agent.ps1" -ConfigPath "C:\ProgramData\CentralizedLogMonitoring\agent\config.json" -DryRun
```

Token value is never printed. Dry run reports only whether a local token file exists.

The dry-run heartbeat payload should include real telemetry when Windows exposes it:

```text
cpu_usage_percent
ram_usage_percent
disk_usage_percent
uptime_seconds
last_boot_at
firebird_check
accurate_process
```

If Windows cannot provide a metric, the agent sends `null` for that field. The Laravel API stores the null and does not invent `0` or random values.

If Firebird check is disabled or `firebird_host` is empty, the agent skips that check and does not fail heartbeat. If Accurate process check is disabled, the agent skips that check and does not fail heartbeat.

When `syslog_enabled=true`, dry run also prints the structured syslog lines and does not send them:

```text
device-monitor: event_type=device_heartbeat agent_id=... hostname=... status=online
perf-monitor: event_type=performance_status agent_id=... cpu_usage_percent=...
heartbeat-monitor: event_type=heartbeat_status agent_id=... uptime_seconds=...
network-monitor: event_type=firebird_connectivity agent_id=... target_host=... target_port=3051 tcp_status=connected latency_ms=...
accurate-process-monitor: event_type=accurate_process agent_id=... process_name=accurate.exe process_status=running
```

When `command_poll_enabled=true`, dry run prints the pending-command URL it would call. It does not fetch commands, does not report results, and does not restart Windows.

## Real Run

Run once manually:

```powershell
powershell.exe -ExecutionPolicy Bypass -File "C:\ProgramData\CentralizedLogMonitoring\agent\agent.ps1" -ConfigPath "C:\ProgramData\CentralizedLogMonitoring\agent\config.json"
```

First real run registers the agent if `agent-token.txt` is missing. Later runs reuse the stored token and send heartbeat.

If syslog is enabled, the same run sends structured UDP syslog messages to the configured RSyslog host after the heartbeat API succeeds.

If command polling is enabled, the same run polls for pending commands after heartbeat/syslog. Only dashboard-created `RESTART_CLIENT` commands for the same authenticated `agent_id` can be returned by the API. The agent schedules restart with:

```powershell
shutdown.exe /r /t <delay> /c "<reason>"
```

No SSH, WinRM, RSyslog command delivery, stored Windows credentials, or arbitrary command execution is used.

## Scheduled Task Example

Create a scheduled task that runs every minute:

```powershell
$action = New-ScheduledTaskAction `
  -Execute "powershell.exe" `
  -Argument "-ExecutionPolicy Bypass -File `"C:\ProgramData\CentralizedLogMonitoring\agent\agent.ps1`" -ConfigPath `"C:\ProgramData\CentralizedLogMonitoring\agent\config.json`""

$trigger = New-ScheduledTaskTrigger -Once -At (Get-Date).AddMinutes(1) `
  -RepetitionInterval (New-TimeSpan -Minutes 1) `
  -RepetitionDuration (New-TimeSpan -Days 3650)

Register-ScheduledTask `
  -TaskName "Centralized Log Monitoring Agent" `
  -Action $action `
  -Trigger $trigger `
  -Description "Sends device heartbeat to Centralized Log Monitoring Dashboard" `
  -RunLevel Highest
```

## Manual Verification Steps

1. Confirm Laravel API route exists:

```powershell
php artisan route:list --path=api/agent
```

2. On Windows client, run dry run and confirm payload contains:

```text
agent_id
hostname
windows_user
ip_local
zerotier_ip
cpu_usage_percent
ram_usage_percent
disk_usage_percent
uptime_seconds
last_boot_at
rdp_status
agent_version
```

3. Run real command once.

4. Confirm local files exist:

```powershell
Test-Path "C:\ProgramData\CentralizedLogMonitoring\agent_id.txt"
Test-Path "C:\ProgramData\CentralizedLogMonitoring\agent-token.txt"
```

5. In Laravel database or Devices page, confirm a device exists with matching `agent_id`, hostname, user, IP metadata, online status, and latest heartbeat.

6. Run real command again and confirm the API does not return or print a new token.

7. Rename Windows hostname only if safe for the test environment, then rerun agent and confirm device identity remains based on `agent_id`.

8. If syslog is enabled, confirm RSyslog receives raw lines:

```powershell
Get-Content "\\<vps-share-if-available>\remote\all.log" -Tail 20
```

Or on the VPS:

```bash
sudo tail -n 20 /var/log/remote/all.log
php artisan rsyslog:parse
```

9. If Firebird check is enabled, confirm the heartbeat payload includes `firebird_check.target_host`, `firebird_check.target_port`, `firebird_check.tcp_status`, and `firebird_check.tcp_latency_ms` when connected.

10. If Accurate process check is enabled, confirm the heartbeat payload includes `accurate_process.process_status` and, when available, PID, owner, and path.

11. If command polling is enabled, create a manual Restart Client action from Device Detail, run the agent once, and confirm the Remote Action changes from `pending` to `picked_up`, then `succeeded` or `failed`.

## Troubleshooting

- If registration succeeds but `agent-token.txt` is deleted, the server may not return another token for the same `agent_id`. Regenerate server credential or restore the local token file.
- If ZeroTier IP is empty, check that ZeroTier is installed, joined to the network, and the adapter name or description contains `ZeroTier`.
- If RDP status is `unavailable`, check `TermService`, Windows firewall, and whether port `3389` is listening.
- If heartbeat fails with unauthorized, verify that `agent-token.txt` belongs to the same `agent_id.txt`.
- If syslog sending fails, verify ZeroTier connectivity, `syslog_host`, firewall rules, and that RSyslog listens on `5514/udp`.
- If Firebird check is skipped, verify `firebird_check_enabled=true` and `firebird_host` is not empty.
- If Firebird status is `timeout` or `failed`, verify ZeroTier, firewall, and Firebird port access from the Windows client.
- If Accurate owner or path is empty, run PowerShell with enough permission or accept null fields; the agent does not fail heartbeat for access denied process metadata.
- If command polling returns no commands, verify the remote action is `pending`, not expired, and belongs to the same registered `agent_id`.
- If restart command fails, check Windows policy/permissions; the agent reports the error message to the result API without printing token values.
