# RSyslog Remote Collector

Milestone 7 uses RSyslog only as a raw structured log collector. Windows Agent sends monitoring/status lines through ZeroTier, RSyslog writes files under `/var/log/remote`, and Laravel parses those files into `logs` for Advanced Logs.

## Install

```bash
sudo apt update
sudo apt install -y rsyslog
sudo mkdir -p /var/log/remote
sudo chown syslog:adm /var/log/remote
sudo chmod 755 /var/log/remote
sudo cp deploy/rsyslog/rsyslog.conf /etc/rsyslog.d/10-centralized-monitoring.conf
sudo rsyslogd -N1
sudo systemctl restart rsyslog
sudo systemctl status rsyslog --no-pager
```

Default listener is port `5514` for UDP and TCP. Port `514` is standard syslog, but it is privileged. To use `514`, change both `input(...) port="5514"` lines to `514`, validate with `sudo rsyslogd -N1`, then restart the system service as root.

## Firewall

Allow syslog only from the ZeroTier subnet or specific Windows client IPs.

```bash
sudo ufw allow from 10.0.0.0/8 to any port 5514 proto udp
sudo ufw allow from 10.0.0.0/8 to any port 5514 proto tcp
sudo ufw status
```

Adjust the CIDR to your ZeroTier network. Do not expose the syslog port publicly unless required and documented.

## Output

RSyslog writes:

```text
/var/log/remote/{hostname}.log
/var/log/remote/all.log
```

Laravel parser prefers `all.log` when it exists to avoid ingesting duplicate rows from both aggregate and per-host files.

Expected line shape:

```text
2026-05-29T10:15:00+07:00 HOSTNAME device-monitor: event_type=device_heartbeat agent_id=... hostname=... status=online
```

## Test Sender

From a Linux host:

```bash
logger -n 127.0.0.1 -P 5514 -d -t device-monitor 'event_type=device_heartbeat agent_id=test-agent hostname=test-host status=online'
sudo tail -n 20 /var/log/remote/all.log
```

For TCP:

```bash
logger -n 127.0.0.1 -P 5514 -T -t perf-monitor 'event_type=performance_status agent_id=test-agent hostname=test-host cpu_usage_percent=42 ram_usage_percent=61 disk_usage_percent=55'
```

## Laravel Parser

Set the parser path in Laravel:

```env
RSYSLOG_REMOTE_LOG_PATH=/var/log/remote
PARSER_BATCH_LIMIT=500
```

Run manually:

```bash
php artisan rsyslog:parse
```

Or parse one file:

```env
RSYSLOG_REMOTE_LOG_PATH=/var/log/remote/all.log
```

Then open Advanced Logs in the dashboard. Raw messages remain there and are not promoted to the main dashboard.
