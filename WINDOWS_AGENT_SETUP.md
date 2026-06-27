# Windows Agent Setup Guide — Quick Start

**Versi:** 1.0
**Tanggal:** 27 Juni 2026
**Server:** VPS `160.187.211.208`

Dokumen ini panduan ringkas instalasi Windows Agent di laptop client Windows (Accurate 5) agar terkoneksi ke server monitoring.

---

## 1. Prasyarat

### Di laptop client:
- Windows 10 atau Windows 11
- PowerShell 5.1 atau lebih baru (bawaan Windows)
- ZeroTier terinstall
- Akses internet

### Di server (VPS):
- Agent API sudah aktif (`AGENT_API_ENABLED=true` di Settings)
- Firebird Docker bind ke `0.0.0.0:3051`
- ZeroTier network sudah running

---

## 2. ZeroTier Setup

### Install ZeroTier di laptop:
1. Download dari https://www.zerotier.com/download/
2. Install dan jalankan

### Join network:
```
zerotier-cli join e4da7455b2b688af
```

### Approve node di VPS:
Setelah laptop join, minta admin VPS untuk approve:
```bash
zerotier-cli listnetworks
# cek node ID, lalu:
zerotier-cli approve <node_id>
```

### Verifikasi IP ZeroTier laptop:
```
zerotier-cli listnetworks
# harus muncul IP dalam range 10.147.17.x
```

---

## 3. Install Windows Agent

### Download / salin file agent:
Copy 3 file berikut ke folder `C:\ProgramData\CentralizedLogMonitoring\agent\`:

| File | Keterangan |
|------|------------|
| `agent.ps1` | Script PowerShell agent |
| `config.json` | Konfigurasi agent |
| `agent_id.txt` | File persistent ID (auto-generated saat first run) |

File agent ada di repo: `windows-agent/agent.ps1`

### Buat folder:
```powershell
New-Item -ItemType Directory -Force -Path "C:\ProgramData\CentralizedLogMonitoring\agent"
```

---

## 4. Konfigurasi config.json

Buat file `C:\ProgramData\CentralizedLogMonitoring\agent\config.json`:

```json
{
  "api_base_url": "http://10.147.17.236/api/agent",
  "agent_version": "1.0.0",
  "runtime_path": "C:\\ProgramData\\CentralizedLogMonitoring",
  "monitored_drive": "C:",
  "rdp_port": 3389,
  "firebird_check_enabled": true,
  "firebird_host": "10.147.17.236",
  "firebird_port": 3051,
  "firebird_timeout_seconds": 5,
  "accurate_process_check_enabled": true,
  "accurate_process_name": "accurate.exe",
  "request_timeout_seconds": 15,
  "command_poll_enabled": true,
  "command_poll_interval_seconds": 30,
  "restart_delay_seconds": 30,
  "syslog_enabled": true,
  "syslog_host": "10.147.17.236",
  "syslog_port": 5515,
  "syslog_protocol": "udp",
  "syslog_app_name": "centralized-monitoring-agent"
}
```

**Key fields:**
| Field | Value | Keterangan |
|-------|-------|------------|
| `api_base_url` | `http://10.147.17.236/api/agent` | API server via ZeroTier |
| `firebird_host` | `10.147.17.236` | Server Firebird via ZeroTier |
| `firebird_port` | `3051` | Port Firebird |
| `syslog_host` | `10.147.17.236` | RSyslog server via ZeroTier |
| `syslog_port` | `5515` | Port RSyslog UDP |

---

## 5. Menjalankan Agent

### First run (registrasi):
```powershell
powershell.exe -ExecutionPolicy Bypass -File "C:\ProgramData\CentralizedLogMonitoring\agent\agent.ps1" -ConfigPath "C:\ProgramData\CentralizedLogMonitoring\agent\config.json"
```

Output sukses:
```
[2026-06-27T20:46:07] Heartbeat sent. Status: heartbeat_received
[2026-06-27T20:46:08] Syslog messages sent over UDP to 10.147.17.236:5515.
[2026-06-27T20:46:08] Command polling completed. No pending commands.
```

### Setelah first run:
- `agent_id.txt` dibuat otomatis
- Token disimpan lokal
- Device muncul di dashboard
- Heartbeat akan dikirim setiap kali agent dijalankan

---

## 6. Menjadwalkan Agent (auto-run)

### Via Task Scheduler (setiap 5 menit):
```powershell
$action = New-ScheduledTaskAction -Execute "powershell.exe" `
  -Argument '-ExecutionPolicy Bypass -File "C:\ProgramData\CentralizedLogMonitoring\agent\agent.ps1" -ConfigPath "C:\ProgramData\CentralizedLogMonitoring\agent\config.json"'
$trigger = New-ScheduledTaskTrigger -Once -At (Get-Date) -RepetitionInterval (New-TimeSpan -Minutes 5) -RepetitionDuration (New-TimeSpan -Days 365)
Register-ScheduledTask -TaskName "CentralizedLogMonitoring-Agent" -Action $action -Trigger $trigger -RunLevel Highest
```

Atau bisa pakai Task Scheduler GUI untuk konfigurasi trigger "Repeat every 5 minutes".

---

## 7. Verifikasi

### Di dashboard:
1. Buka `http://160.187.211.208`
2. Login sebagai admin
3. Buka halaman **Devices** → device laptop harus muncul
4. Cek status:
   - Device Online ✅
   - Firebird OK (connected/timeout/slow)
   - Accurate Active (running/not_running)
5. Buka **Alerts** → alert detection akan otomatis deteksi kondisi

### Di laptop (dry-run test):
```powershell
powershell.exe -ExecutionPolicy Bypass -File "C:\ProgramData\CentralizedLogMonitoring\agent\agent.ps1" -ConfigPath "C:\ProgramData\CentralizedLogMonitoring\agent\config.json" -DryRun
```

---

## 8. Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Heartbeat 500 error | Cek `AGENT_API_ENABLED=true` di Settings |
| Firebird timeout | Cek ZeroTier koneksi + Firebird bind di VPS (`0.0.0.0:3051`) |
| Agent ID gak muncul | Hapus `agent_id.txt` dan `token.json`, restart agent |
| Command polling gagal | Agent akan tetap mengirim heartbeat — polling optional |
| RSyslog gak terima log | Cek UDP port 5515 di VPS, cek firewall |