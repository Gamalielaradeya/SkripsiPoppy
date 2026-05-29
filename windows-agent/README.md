# Windows Agent PowerShell MVP

PowerShell agent for Centralized Log Monitoring Dashboard. Milestone 5 registered real Windows devices and sent heartbeat metadata. Milestone 6 adds real CPU, RAM, and disk telemetry to the heartbeat API payload.

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
- `-DryRun` mode

Not implemented in this milestone:

- RSyslog sending
- Firebird connectivity checks
- Accurate process detection
- Telegram notifications
- Alert detection
- Command polling
- Remote restart

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
  "request_timeout_seconds": 15
}
```

Use the VPS ZeroTier IP or production HTTPS URL when available.

`monitored_drive` controls which Windows logical disk is reported as `disk_usage_percent`. Use `C:` unless the Accurate workstation stores its main working data on another local drive.

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
```

If Windows cannot provide a metric, the agent sends `null` for that field. The Laravel API stores the null and does not invent `0` or random values.

## Real Run

Run once manually:

```powershell
powershell.exe -ExecutionPolicy Bypass -File "C:\ProgramData\CentralizedLogMonitoring\agent\agent.ps1" -ConfigPath "C:\ProgramData\CentralizedLogMonitoring\agent\config.json"
```

First real run registers the agent if `agent-token.txt` is missing. Later runs reuse the stored token and send heartbeat.

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

## Troubleshooting

- If registration succeeds but `agent-token.txt` is deleted, the server may not return another token for the same `agent_id`. Regenerate server credential or restore the local token file.
- If ZeroTier IP is empty, check that ZeroTier is installed, joined to the network, and the adapter name or description contains `ZeroTier`.
- If RDP status is `unavailable`, check `TermService`, Windows firewall, and whether port `3389` is listening.
- If heartbeat fails with unauthorized, verify that `agent-token.txt` belongs to the same `agent_id.txt`.
