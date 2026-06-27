# 13 — Codex Implementation Brief v2
# Centralized Log Monitoring Dashboard

**Versi:** 2.0  
**Status:** Final untuk panduan Codex / AI Agent  
**Project:** Centralized Log Monitoring Dashboard  
**Target Implementasi:** Real-device environment, bukan simulasi random log  
**Stack Utama:** Laravel, Blade, Tailwind CSS, Alpine.js, Chart.js, MySQL, RSyslog, Firebird 2.5, Windows Agent, ZeroTier, Telegram Bot  
**Target Infrastruktur:** 2 laptop Windows Accurate 5 + 1 VPS Linux + ZeroTier private network  

---

## 1. Tujuan Dokumen

Dokumen ini adalah brief utama untuk Codex atau AI coding agent dalam membangun ulang project **Centralized Log Monitoring Dashboard** berdasarkan dokumen v2.

Dokumen ini menjelaskan:

1. Urutan dokumen yang wajib dibaca.
2. Tujuan implementasi final.
3. Batasan scope agar Codex tidak mengarang fitur.
4. Struktur repository yang disarankan.
5. Arsitektur modul aplikasi.
6. Urutan pengerjaan fase demi fase.
7. Standar coding Laravel.
8. Standar Windows Agent.
9. Standar parser RSyslog.
10. Standar Accurate Firebird Audit Reader.
11. Standar Telegram contextual alert.
12. Standar remote action manual.
13. Branching dan GitHub workflow.
14. Testing checklist per milestone.
15. Hal yang tidak boleh dilakukan Codex.

Dokumen ini tidak menggantikan PRD, SRS, database design, architecture, dan test plan. Dokumen ini berfungsi sebagai **execution guide** agar Codex tahu urutan kerja yang benar.

---

## 2. Dokumen Wajib Dibaca Codex

Sebelum mulai coding, Codex wajib membaca dokumen berikut secara berurutan:

```text
01_PRD_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md
02_Accurate_Firebird_POC_Findings_v2.md
03_SRS_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md
04_Database_Design_v2.md
05_System_Architecture_v2.md
06_Windows_Agent_and_RSyslog_Guide_v2.md
07_Detection_Rules_v2.md
08_UI_Design_System_v2.md
09_UI_Wireframe_v2.md
10_Test_Plan_v2.md
11_Deployment_Guide_VPS_ZeroTier_v2.md
12_Demo_Script_v2.md
13_Codex_Implementation_Brief.md
```

Jika terjadi konflik antar dokumen, gunakan prioritas berikut:

```text
1. 13_Codex_Implementation_Brief.md
2. 03_SRS_v2
3. 04_Database_Design_v2
4. 05_System_Architecture_v2
5. 07_Detection_Rules_v2
6. 02_Accurate_Firebird_POC_Findings_v2
7. 01_PRD_v2
8. Dokumen lainnya
```

---

## 3. Ringkasan Produk

**Centralized Log Monitoring Dashboard** adalah sistem monitoring berbasis web untuk membantu Administrator IT memantau kondisi perangkat Windows pengguna Accurate 5, koneksi ke database Firebird di VPS, performa device, status proses Accurate, audit trail Accurate, alert Telegram, dan tindakan remote manual.

Target environment bukan lagi simulasi 1 laptop Docker Compose, melainkan:

```text
[Windows Laptop 1]
- Accurate 5 Client
- Windows Agent
- ZeroTier
- RDP enabled

[Windows Laptop 2]
- Accurate 5 Client
- Windows Agent
- ZeroTier
- RDP enabled

        ↓ structured syslog + API polling

[VPS Linux]
- RSyslog Server
- Laravel Dashboard
- MySQL Monitoring Database
- Firebird 2.5 / Accurate Database
- Accurate Audit Reader
- Telegram Alert Service
- Remote Command API
- ZeroTier
```

---

## 4. Prinsip Implementasi Utama

Codex harus mengikuti prinsip berikut:

| Prinsip | Penjelasan |
|---|---|
| Real-device first | Sistem ditargetkan untuk 2 laptop Windows nyata dan VPS Linux, bukan simulasi log random. |
| No hardcoded device | Device tidak boleh ditentukan dengan hostname hardcode seperti `WIN-ACC-01`. |
| Agent-based telemetry | Data Windows dikirim oleh Windows Agent. Jangan memonitor Windows lewat WSL. |
| RSyslog as collector | RSyslog hanya menerima structured syslog dari agent dan server checker. |
| Firebird audit direct | Accurate Audit Trail dibaca langsung dari Firebird `AUDIT + USERS`, bukan lewat RSyslog. |
| Dashboard-first | Dashboard harus menampilkan kondisi IT penting, bukan raw log random. |
| Contextual alert | Alert wajib punya target, evidence, impact, dan recommended action. |
| Telegram required | Telegram alert adalah fitur wajib, bukan opsional sampingan. |
| Manual remote action | Remote Desktop dan Restart Client harus manual controlled, bukan otomatis. |
| Audit-friendly | Raw log tetap disimpan untuk Advanced Logs, tetapi bukan fokus dashboard. |
| Simple enough for skripsi | Kode harus mudah dijelaskan pada Bab 4 Implementasi dan Pengujian. |

---

## 5. Scope Implementasi

## 5.1 In Scope

Fitur yang wajib dibangun:

1. Login admin.
2. Dashboard utama IT operations cockpit.
3. Device auto-registration dari Windows Agent.
4. Windows Agent telemetry:
   - heartbeat,
   - Windows user,
   - IP ZeroTier,
   - CPU,
   - RAM,
   - disk,
   - uptime,
   - RDP status,
   - Accurate process status,
   - Firebird connectivity.
5. RSyslog Server di VPS.
6. Laravel parser untuk structured key=value syslog.
7. Device list dan Device Detail.
8. Network & Firebird connectivity monitoring.
9. Performance & hang indicator.
10. Accurate process monitoring.
11. Server health monitoring untuk VPS/Firebird.
12. Accurate Audit Reader dari Firebird `AUDIT + USERS`.
13. Accurate Audit page.
14. Contextual Alerts.
15. Telegram contextual alert.
16. Incidents.
17. Remote Desktop launcher.
18. Remote Restart Client manual melalui agent command polling.
19. Remote Actions audit log.
20. Advanced Logs.
21. Settings.
22. Test cases sesuai Test Plan v2.
23. Deployment guide compatibility untuk VPS + ZeroTier.

## 5.2 Out of Scope

Jangan implementasikan fitur berikut kecuali diminta eksplisit setelah MVP stabil:

1. Auto restart tanpa approval admin.
2. Auto blocking / IPS.
3. SIEM enterprise.
4. ELK Stack.
5. Grafana.
6. Prometheus.
7. React frontend.
8. Node.js backend.
9. Windows Event Log bulk ingestion sebagai dashboard utama.
10. Audit activity spike detection tanpa rule jelas.
11. Machine learning anomaly detection.
12. Multi-role kompleks.
13. Public Firebird exposure tanpa ZeroTier/security note.
14. Device detection berdasarkan hardcoded hostname.
15. Query Accurate `LOGIN` sebagai sumber utama login user.

---

## 6. Stack Teknologi Final

| Layer | Teknologi | Catatan |
|---|---|---|
| Backend | Laravel | Dashboard, API, parser, scheduler, alert, command queue. |
| Frontend | Laravel Blade | Tidak menggunakan React. |
| Styling | Tailwind CSS | Custom dashboard, AdminLTE-inspired, bukan template mentah. |
| Interaction | Alpine.js | Modal, dropdown, filter, confirm action. |
| Chart | Chart.js | Ringkasan sederhana, tidak berlebihan. |
| Database Monitoring | MySQL | Menyimpan device, telemetry, logs, alerts, audit events. |
| Log Collector | RSyslog | Menerima structured syslog dari Windows Agent dan server checker. |
| Accurate DB | Firebird 2.5 | Database Accurate di VPS. |
| Network | ZeroTier | Private network antara laptop dan VPS. |
| Windows Agent | PowerShell atau Python | Disarankan Python untuk maintainability, PowerShell bisa untuk prototype. |
| Notification | Telegram Bot API | Wajib untuk contextual alert. |
| Version Control | GitHub | main, develop, feature branches. |

---

## 7. Rekomendasi Struktur Repository

```text
centralized-log-monitoring/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── ParseRsyslogCommand.php
│   │       ├── SyncAccurateAuditCommand.php
│   │       ├── CheckServerHealthCommand.php
│   │       ├── EvaluateAlertsCommand.php
│   │       └── ExpireStaleDevicesCommand.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── DeviceController.php
│   │   │   ├── AccurateAuditController.php
│   │   │   ├── IncidentController.php
│   │   │   ├── AlertController.php
│   │   │   ├── RemoteActionController.php
│   │   │   ├── AdvancedLogController.php
│   │   │   ├── SettingController.php
│   │   │   └── AgentApiController.php
│   │   │
│   │   └── Middleware/
│   │
│   ├── Models/
│   │   ├── Device.php
│   │   ├── AgentCredential.php
│   │   ├── DeviceTelemetry.php
│   │   ├── NetworkCheck.php
│   │   ├── AccurateProcessSnapshot.php
│   │   ├── ServerServiceCheck.php
│   │   ├── Log.php
│   │   ├── AccurateAuditSource.php
│   │   ├── AccurateAuditEvent.php
│   │   ├── AccurateAuditSyncState.php
│   │   ├── Alert.php
│   │   ├── AlertEvidence.php
│   │   ├── AlertNotification.php
│   │   ├── Incident.php
│   │   ├── IncidentAlert.php
│   │   ├── RemoteAction.php
│   │   ├── ThresholdSetting.php
│   │   └── SystemSetting.php
│   │
│   └── Services/
│       ├── Rsyslog/
│       │   ├── StructuredLogParserService.php
│       │   ├── RsyslogFileReaderService.php
│       │   └── ParserOffsetService.php
│       ├── Devices/
│       │   ├── DeviceRegistrationService.php
│       │   ├── DeviceTelemetryService.php
│       │   └── DeviceStatusService.php
│       ├── Accurate/
│       │   ├── FirebirdConnectionService.php
│       │   ├── AccurateAuditReaderService.php
│       │   ├── AccurateAuditMapperService.php
│       │   └── AccurateAuditSyncService.php
│       ├── Alerts/
│       │   ├── AlertRuleEngine.php
│       │   ├── ContextualAlertService.php
│       │   ├── IncidentCorrelationService.php
│       │   └── TelegramAlertService.php
│       ├── RemoteActions/
│       │   ├── RemoteActionService.php
│       │   └── AgentCommandService.php
│       └── ServerHealth/
│           └── ServerHealthCheckService.php
│
├── database/
│   ├── migrations/
│   └── seeders/
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── components/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── devices/
│   │   ├── accurate-audit/
│   │   ├── incidents/
│   │   ├── alerts/
│   │   ├── remote-actions/
│   │   ├── advanced-logs/
│   │   └── settings/
│   ├── css/
│   └── js/
│
├── routes/
│   ├── web.php
│   └── api.php
│
├── windows-agent/
│   ├── README.md
│   ├── agent.py
│   ├── config.example.json
│   ├── install-service.ps1
│   ├── uninstall-service.ps1
│   └── requirements.txt
│
├── deploy/
│   ├── rsyslog/
│   │   └── rsyslog-server.conf
│   ├── systemd/
│   ├── nginx/
│   └── scripts/
│
├── docs/
│   └── v2/
│
├── AGENTS.md
├── composer.json
├── package.json
├── .env.example
└── README.md
```

---

## 8. AGENTS.md Wajib Dibuat

Buat file `AGENTS.md` di root repository agar Codex selalu mengikuti aturan project.

Contoh isi wajib:

```md
# AGENTS.md

## Project
Centralized Log Monitoring Dashboard untuk real-device Windows Accurate 5 + VPS Linux.

## Stack wajib
- Laravel
- Blade
- Tailwind CSS
- Alpine.js
- Chart.js
- MySQL
- RSyslog
- Firebird 2.5
- Windows Agent
- Telegram Bot

## Larangan
- Jangan pakai React.
- Jangan pakai Node.js backend.
- Jangan pakai ELK, Grafana, Prometheus, atau SIEM kompleks.
- Jangan tampilkan raw Windows Event Log sebagai dashboard utama.
- Jangan hardcode device seperti WIN-ACC-01.
- Jangan ambil Accurate login dari tabel LOGIN sebagai sumber utama.
- Jangan auto restart device.
- Jangan buat alert tanpa target dan evidence.

## Arsitektur wajib
Windows Agent -> RSyslog Server -> Laravel Parser -> MySQL -> Dashboard -> Telegram.
Accurate Audit Reader -> Firebird AUDIT + USERS -> MySQL -> Dashboard.
Remote Actions -> Laravel API -> Windows Agent polling -> manual execution.

## UI
UI harus berupa IT operations cockpit, clean, professional, status-first.

## Command penting
- php artisan rsyslog:parse
- php artisan accurate:audit-sync
- php artisan server:health-check
- php artisan alerts:evaluate
```

---

## 9. Milestone Implementasi

Pengerjaan wajib dilakukan bertahap. Jangan membangun semua fitur sekaligus.

---

# Milestone 0 — Repository Setup

## Tujuan

Menyiapkan project Laravel bersih, GitHub workflow, struktur folder, dan dokumentasi dasar.

## Task

1. Buat repository GitHub.
2. Buat branch:

```text
main
develop
```

3. Buat Laravel project.
4. Setup Tailwind CSS.
5. Setup Alpine.js.
6. Setup Chart.js.
7. Buat `AGENTS.md`.
8. Copy dokumen v2 ke folder `docs/v2/`.
9. Buat `.env.example`.
10. Buat README awal.

## Acceptance Criteria

```text
- Laravel dapat dijalankan lokal.
- Login page placeholder tampil.
- Tailwind berhasil build.
- AGENTS.md tersedia.
- docs/v2 berisi dokumen panduan.
- Tidak ada React/Node backend.
```

---

# Milestone 1 — Auth, Layout, dan UI Shell

## Tujuan

Membuat kerangka UI aplikasi sesuai design system v2.

## Task

1. Buat login admin.
2. Buat middleware auth.
3. Buat layout utama:
   - sidebar,
   - topbar,
   - content wrapper.
4. Buat menu:

```text
Dashboard
Devices
Accurate Audit
Incidents
Alerts
Remote Actions
Advanced Logs
Settings
Logout
```

5. Buat component:
   - status badge,
   - severity badge,
   - status card,
   - data table,
   - empty state,
   - confirmation modal.

## Acceptance Criteria

```text
- Admin bisa login dan logout.
- Route dashboard dilindungi auth.
- Sidebar dan topbar tampil rapi.
- Menu final tersedia.
- Tidak ada raw log random di dashboard.
```

---

# Milestone 2 — Database Migration Core

## Tujuan

Membuat tabel core sesuai database design v2.

## Task

Buat migration untuk:

```text
users
devices
agent_credentials
device_telemetries
network_checks
accurate_process_snapshots
server_service_checks
logs
parser_offsets
parser_runs
alerts
alert_evidences
alert_notifications
incidents
incident_alerts
remote_actions
threshold_settings
system_settings
```

Accurate audit tables bisa masuk milestone 6 jika ingin dipisah.

## Acceptance Criteria

```text
- php artisan migrate berhasil.
- Semua tabel core terbentuk.
- Seeder admin tersedia.
- Seeder threshold default tersedia.
- Device tidak hardcode.
```

---

# Milestone 3 — RSyslog Server dan Parser Key=Value

## Tujuan

Membuat Laravel dapat membaca structured syslog dari RSyslog Server.

## Task

1. Setup RSyslog Server di VPS.
2. Simpan log ke `/var/log/remote/{hostname}.log`.
3. Laravel punya command:

```bash
php artisan rsyslog:parse
```

4. Parser membaca format key=value seperti:

```text
device-monitor: event=device_heartbeat agent_id=... hostname=... windows_user=... ip_zerotier=... status=online
perf-monitor: event=device_telemetry agent_id=... cpu=42 ram=61 disk=55 uptime_seconds=1234
network-monitor: event=firebird_connectivity agent_id=... target_host=10.0.0.1 target_port=3051 status=connected latency_ms=24
accurate-monitor: event=accurate_process agent_id=... process=accurate.exe status=running owner=...
```

5. Simpan raw log ke `logs` untuk Advanced Logs.
6. Mapping event ke tabel khusus:
   - heartbeat → devices,
   - telemetry → device_telemetries,
   - network → network_checks,
   - accurate process → accurate_process_snapshots.
7. Gunakan parser offset.
8. Gunakan hash anti-duplikasi.

## Acceptance Criteria

```text
- RSyslog menerima log dari test sender.
- Parser membaca log baru saja.
- Data masuk logs dan tabel domain.
- Parser tidak insert duplikat.
- Device auto-register berdasarkan agent_id.
```

---

# Milestone 4 — Windows Agent MVP

## Tujuan

Membuat Windows Agent pertama yang berjalan di laptop Windows dan mengirim data real ke RSyslog VPS.

## Task

Agent harus mengirim:

1. Heartbeat.
2. Hostname.
3. Windows user.
4. IP ZeroTier.
5. CPU usage.
6. RAM usage.
7. Disk usage.
8. RDP status.
9. Accurate process status.
10. Firebird connectivity to VPS:3051.

## Pilihan Implementasi

Disarankan Python:

```text
windows-agent/agent.py
windows-agent/config.example.json
windows-agent/install-service.ps1
```

Config contoh:

```json
{
  "agent_id": "generated-uuid",
  "device_label": "Laptop Finance 1",
  "rsyslog_host": "<zerotier-vps-ip>",
  "rsyslog_port": 514,
  "rsyslog_protocol": "tcp",
  "api_base_url": "http://<zerotier-vps-ip>/api/agent",
  "api_token": "agent-token",
  "firebird_host": "<zerotier-vps-ip>",
  "firebird_port": 3051,
  "interval_seconds": 30
}
```

## Acceptance Criteria

```text
- Agent berjalan di Windows laptop.
- Agent mengambil data Windows asli, bukan WSL.
- Agent mengirim heartbeat ke RSyslog VPS.
- Dashboard menampilkan device online.
- Device memakai agent_id, bukan hardcoded hostname.
```

---

# Milestone 5 — Devices dan Dashboard Real Data

## Tujuan

Membuat dashboard dan halaman devices memakai data real dari Windows Agent.

## Task

1. Dashboard summary:
   - device online,
   - Firebird connected,
   - Accurate active,
   - open alerts,
   - audit events today placeholder jika audit belum selesai.
2. Device Health Table.
3. Devices index page.
4. Device Detail page.
5. Chart sederhana:
   - CPU trend per device,
   - latest telemetry.
6. Action buttons:
   - Remote Desktop,
   - Restart Client,
   - Ping Test,
   - View Logs.

## Acceptance Criteria

```text
- Dashboard tidak kosong jika agent aktif.
- Devices page menampilkan laptop real.
- Device Detail menampilkan telemetry terbaru.
- Tombol remote action tampil tapi restart belum harus eksekusi dulu.
- Raw log tetap berada di Advanced Logs.
```

---

# Milestone 6 — Alert Rule Engine dan Telegram Contextual Alert

## Tujuan

Membuat alert berbasis rule v2 yang jelas target dan evidence-nya, lalu mengirim Telegram.

## Task

1. Buat `AlertRuleEngine`.
2. Implementasikan rule:
   - device missed heartbeat,
   - device offline,
   - CPU high,
   - CPU critical,
   - RAM high,
   - disk high,
   - Firebird latency high,
   - Firebird port timeout,
   - Accurate process not detected during work hours.
3. Buat `alert_evidences`.
4. Buat cooldown / anti-duplicate.
5. Buat Telegram service.
6. Format Telegram wajib contextual:

```text
[WARNING] CPU tinggi pada Laptop Finance 2

Target     : Laptop Finance 2
Hostname   : DESKTOP-XYZ
User       : DESKTOP-XYZ\Finance
Evidence   : CPU 87%, RAM 82%, durasi 5 menit
Impact     : Device dapat mengalami perlambatan saat input transaksi Accurate
Action     : Cek melalui Remote Desktop atau restart manual bila diperlukan
Time       : 2026-05-28 19:51
```

## Acceptance Criteria

```text
- Alert tidak boleh ngambang.
- Alert punya target_type dan target_id.
- Alert punya evidence.
- Telegram terkirim saat rule terpenuhi.
- Telegram tidak spam berulang tanpa cooldown.
```

---

# Milestone 7 — Accurate Firebird Audit Reader

## Tujuan

Membaca audit trail Accurate dari Firebird `AUDIT + USERS` sesuai POC, bukan dari tabel `LOGIN`.

## Task

1. Buat tabel:
   - accurate_audit_sources,
   - accurate_audit_events,
   - accurate_audit_sync_states,
   - accurate_audit_sync_runs.
2. Buat konfigurasi source Firebird:
   - host,
   - port,
   - database path,
   - username read-only,
   - password encrypted/secured.
3. Buat command:

```bash
php artisan accurate:audit-sync
```

4. Query `AUDIT + USERS`.
5. Jangan gunakan `LOGIN` sebagai sumber utama.
6. Jangan wajibkan `COMP_NAME` atau `IPADDRESS`.
7. Implement incremental sync:
   - berdasarkan audit id atau timestamp,
   - simpan state.
8. Mapping ke `accurate_audit_events`.
9. Buat halaman Accurate Audit.
10. Buat filter:
   - tanggal,
   - username Accurate,
   - source/module,
   - transaction type,
   - keyword.

## Acceptance Criteria

```text
- Sync Firebird berhasil read-only.
- Audit event masuk MySQL.
- Username Accurate muncul dari AUDIT + USERS.
- LOGIN tidak dipakai.
- COMP_NAME/IPADDRESS boleh null.
- Dashboard menampilkan audit terbaru.
```

---

# Milestone 8 — Incidents

## Tujuan

Membuat incident dari gabungan alert/event, bukan sekadar raw log.

## Task

1. Buat `IncidentCorrelationService`.
2. Implementasikan rule awal:

```text
Device Slow Indicator:
CPU >= 85% + RAM >= 80% + Firebird latency tinggi

Device Possibly Unresponsive:
heartbeat delayed + CPU/RAM tinggi terakhir

Firebird Service Problem:
server health service inactive + multiple clients port timeout
```

3. Incidents harus punya:
   - target,
   - summary,
   - severity,
   - evidence,
   - linked alerts,
   - recommended action.
4. Halaman Incidents.
5. Incident detail.
6. Acknowledge dan resolve.

## Acceptance Criteria

```text
- Incident tidak dibuat tanpa evidence.
- Incident menjelaskan masalah operasional.
- Incident bisa dilihat dan diselesaikan admin.
```

---

# Milestone 9 — Remote Desktop dan Remote Restart Manual

## Tujuan

Membuat admin bisa melakukan tindakan remote manual dari dashboard.

## Remote Desktop

Implementasi awal:

1. Device menyimpan `ip_zerotier`.
2. Dashboard menyediakan tombol Remote Desktop.
3. Tombol menghasilkan command/link:

```text
mstsc /v:<ip_zerotier>
```

4. Simpan action `OPEN_RDP` ke `remote_actions`.

## Remote Restart

Alur wajib:

```text
Admin klik Restart Client
↓
Modal konfirmasi muncul
↓
Admin wajib isi reason
↓
Laravel membuat remote_actions status=pending
↓
Windows Agent polling /api/agent/commands
↓
Agent menerima command restart
↓
Agent menjalankan shutdown /r /t 30
↓
Agent kirim result ke API
↓
Remote action status updated
```

## Acceptance Criteria

```text
- Tidak ada auto restart.
- Restart wajib reason.
- Restart wajib dicatat.
- Agent hanya menerima command untuk agent_id miliknya.
- Device offline tidak bisa restart.
- Remote action result tampil di dashboard.
```

---

# Milestone 10 — Advanced Logs, Settings, Polish, dan UAT

## Tujuan

Merapikan aplikasi agar siap diuji dan dipresentasikan.

## Task

1. Advanced Logs dengan filter.
2. Settings:
   - threshold CPU/RAM/disk,
   - heartbeat threshold,
   - Firebird host/port,
   - Telegram setting,
   - remote action enable/disable.
3. UI polish sesuai design system.
4. Empty states.
5. Error states.
6. Pagination.
7. Search/filter.
8. UAT checklist.
9. README deployment.
10. Final demo script alignment.

## Acceptance Criteria

```text
- Semua menu final bekerja.
- UI rapi dan tidak generik.
- Dashboard fokus pada device/Accurate/incident.
- Test plan v2 bisa dijalankan.
```

---

## 10. Route Web yang Disarankan

```php
// Auth
GET  /login
POST /login
POST /logout

// Dashboard
GET /dashboard

// Devices
GET  /devices
GET  /devices/{device}
POST /devices/{device}/ping
POST /devices/{device}/remote-desktop
POST /devices/{device}/restart

// Accurate Audit
GET /accurate-audit
GET /accurate-audit/{event}

// Incidents
GET  /incidents
GET  /incidents/{incident}
POST /incidents/{incident}/acknowledge
POST /incidents/{incident}/resolve

// Alerts
GET  /alerts
GET  /alerts/{alert}
POST /alerts/{alert}/acknowledge
POST /alerts/{alert}/resolve

// Remote Actions
GET /remote-actions
GET /remote-actions/{remoteAction}

// Advanced Logs
GET /advanced-logs
GET /advanced-logs/{log}

// Settings
GET  /settings
POST /settings
```

---

## 11. Agent API Route yang Disarankan

```php
// Agent command polling
GET  /api/agent/commands
POST /api/agent/commands/{remoteAction}/result

// Optional direct heartbeat API if needed later
POST /api/agent/heartbeat
```

Catatan:

```text
Telemetry utama tetap lewat RSyslog.
API dipakai untuk command polling dan action result.
```

---

## 12. Artisan Command yang Disarankan

```bash
php artisan rsyslog:parse
php artisan accurate:audit-sync
php artisan server:health-check
php artisan alerts:evaluate
php artisan incidents:correlate
php artisan devices:expire-stale
```

Scheduler Laravel:

```php
$schedule->command('rsyslog:parse')->everyMinute();
$schedule->command('alerts:evaluate')->everyMinute();
$schedule->command('incidents:correlate')->everyFiveMinutes();
$schedule->command('devices:expire-stale')->everyMinute();
$schedule->command('server:health-check')->everyMinute();
$schedule->command('accurate:audit-sync')->everyMinute();
```

---

## 13. Parser Structured Log Format

Codex harus memakai format key=value agar mudah diparse.

Contoh heartbeat:

```text
device-monitor: event=device_heartbeat agent_id=5c0b hostname=DESKTOP-ABC device_label="Laptop Finance 1" windows_user="DESKTOP-ABC\\Finance" ip_zerotier=10.147.20.11 rdp_status=available agent_version=1.0 status=online
```

Contoh telemetry:

```text
perf-monitor: event=device_telemetry agent_id=5c0b cpu=42 ram=61 disk=55 uptime_seconds=123456 status=normal
```

Contoh Firebird connectivity:

```text
network-monitor: event=firebird_connectivity agent_id=5c0b target_host=10.147.20.1 target_port=3051 status=connected latency_ms=24 packet_loss=0
```

Contoh Accurate process:

```text
accurate-monitor: event=accurate_process agent_id=5c0b process=accurate.exe status=running owner="DESKTOP-ABC\\Finance" path="C:\\Program Files\\CPSSoft\\Accurate5\\accurate.exe"
```

Contoh remote action result:

```text
remote-action-monitor: event=remote_action_result agent_id=5c0b action_id=123 action_type=RESTART_CLIENT status=accepted message="Restart scheduled in 30 seconds"
```

Parser wajib:

```text
- membaca tag/source
- membaca event
- membaca key=value
- menyimpan raw_message
- menyimpan parsed_message
- menyimpan hash
- mapping ke tabel domain
- menolak/skip log malformed dengan status parser run yang jelas
```

---

## 14. Accurate Firebird Audit Reader Rules

Codex wajib mengikuti POC:

```text
- Accurate 5 menggunakan Firebird 2.5.
- Audit trail dibaca dari AUDIT + USERS.
- Username internal Accurate berasal dari relasi AUDIT.USERID -> USERS.USERID.
- Tabel LOGIN tidak dipakai sebagai sumber utama karena pada POC kosong.
- COMP_NAME dan IPADDRESS pada AUDIT boleh null/kosong.
- IP/device aktif tidak disimpulkan dari AUDIT; gunakan Windows Agent.
- Akses Firebird harus read-only.
- Jangan hardcode credential Firebird.
```

Contoh pseudo-query harus disesuaikan dengan nama kolom aktual dari POC:

```sql
SELECT
    A.ID AS audit_id,
    A.USERID AS user_id,
    U.USERNAME AS accurate_username,
    U.FULLNAME AS accurate_fullname,
    A.ACTION_TIME AS activity_time,
    A.SOURCE AS source_module,
    A.TRANS_TYPE AS transaction_type,
    A.DESCRIPTION AS description,
    A.INVOICE_NO AS reference_no,
    A.APP_VERSION AS app_version,
    A.COMP_NAME AS comp_name,
    A.IPADDRESS AS ip_address
FROM AUDIT A
LEFT JOIN USERS U ON U.USERID = A.USERID
WHERE A.ID > :last_audit_id
ORDER BY A.ID ASC
```

Jika nama kolom aktual berbeda, Codex harus membuat mapping configurable, bukan mengarang field baru tanpa dasar.

---

## 15. Contextual Alert Requirements

Setiap alert wajib punya minimal:

```text
title
severity
target_type
target_id
target_name
category
source
detected_by
description
impact
recommended_action
status
detected_at
```

Setiap alert wajib punya evidence di `alert_evidences`:

```text
key
value
unit
operator
threshold
observed_at
```

Contoh alert valid:

```text
WARNING - CPU tinggi pada Laptop Finance 2
Target: Laptop Finance 2
Detected by: Windows Agent
Evidence: cpu=87%, threshold=80%, duration=5 minutes
Impact: Device dapat mengalami perlambatan saat input transaksi Accurate
Recommended action: Cek melalui Remote Desktop atau restart manual bila diperlukan
```

Contoh alert tidak valid:

```text
WARNING - CPU tinggi
Firebird unreachable
Audit activity spike detected
Accurate process not running
```

Alert tidak valid karena tidak punya target/evidence/impact/rule jelas.

---

## 16. Telegram Requirements

Telegram wajib aktif pada production/demo real-device.

Format minimal:

```text
[SEVERITY] Alert Title

Target     : ...
Source     : ...
Evidence   : ...
Impact     : ...
Action     : ...
Time       : ...
Dashboard  : ...
```

Telegram tidak boleh hanya mengirim raw message.

Anti-spam:

```text
Alert dengan target + rule + severity yang sama tidak dikirim ulang dalam cooldown window.
Default cooldown: 5 menit.
```

---

## 17. Remote Action Requirements

Remote action adalah fitur manual controlled.

Remote action types:

```text
PING_TEST
OPEN_RDP
RESTART_CLIENT
RESTART_AGENT
```

Remote restart wajib:

```text
- dilakukan oleh admin login,
- menggunakan modal konfirmasi,
- wajib reason,
- dicatat ke remote_actions,
- dieksekusi oleh Windows Agent melalui polling API,
- tidak dikirim lewat RSyslog,
- tidak otomatis berdasarkan alert.
```

---

## 18. UI Implementation Rules

Codex harus mengikuti `08_UI_Design_System_v2.md` dan `09_UI_Wireframe_v2.md`.

Prinsip UI:

```text
IT operations cockpit
clean
professional
status-first
data-dense but readable
not a raw log viewer
```

Dashboard wajib menampilkan:

```text
- Device online summary
- Firebird connectivity summary
- Accurate active summary
- Open alerts/incidents summary
- Device health table
- Recent Accurate Audit
- Recent contextual alerts/incidents
```

Dashboard tidak boleh didominasi:

```text
- raw log panjang
- source_file
- hash
- Windows Event Log noise
- chart berlebihan
```

---

## 19. GitHub Workflow

Branch:

```text
main      = stable release
 develop  = active integration
feature/* = fitur spesifik
fix/*     = perbaikan bug
```

Workflow:

```text
1. Buat issue/task kecil.
2. Buat branch feature dari develop.
3. Implementasi.
4. Jalankan test manual/automated.
5. Commit dengan pesan jelas.
6. Pull request ke develop.
7. Merge ke main hanya saat milestone stabil.
```

Contoh branch:

```text
feature/auth-layout
feature/database-core
feature/rsyslog-parser
feature/windows-agent-mvp
feature/contextual-alerts
feature/accurate-audit-reader
feature/remote-actions
```

Commit style:

```text
feat: add device telemetry migration
feat: implement structured rsyslog parser
fix: prevent duplicate alert notification
chore: add AGENTS project rules
```

---

## 20. Testing Before Moving Milestone

Sebelum lanjut milestone, Codex harus memastikan:

```text
- migration berjalan,
- route tidak error,
- UI tidak blank,
- parser tidak insert duplikat,
- alert tidak dibuat tanpa evidence,
- Telegram tidak spam,
- Windows Agent config tidak hardcode device,
- remote restart tidak auto-trigger,
- Accurate audit tidak memakai LOGIN sebagai sumber utama.
```

Gunakan `10_Test_Plan_v2.md` sebagai acuan test lengkap.

---

## 21. Hal yang Tidak Boleh Dilakukan Codex

Codex dilarang:

1. Menggunakan React sebagai frontend.
2. Menggunakan Node.js sebagai backend.
3. Mengubah aplikasi menjadi SIEM enterprise.
4. Menggunakan ELK/Grafana/Prometheus.
5. Menjadikan raw Windows Event Log sebagai dashboard utama.
6. Membuat device hardcoded.
7. Membuat alert tanpa target.
8. Membuat alert tanpa evidence.
9. Membuat alert “Audit activity spike detected” tanpa rule perhitungan yang disetujui.
10. Menggunakan tabel `LOGIN` Accurate sebagai sumber utama.
11. Menganggap `COMP_NAME` dan `IPADDRESS` Accurate selalu ada.
12. Menjalankan remote restart otomatis berdasarkan alert.
13. Mengirim remote command lewat RSyslog.
14. Menyimpan password Firebird atau agent token secara plaintext jika bisa dihindari.
15. Membuka Firebird publik tanpa catatan keamanan.
16. Mengganti ZeroTier dengan asumsi lain tanpa konfirmasi.
17. Menghilangkan Telegram karena Telegram adalah fitur wajib.
18. Membuat dashboard dengan data random agar terlihat ramai.
19. Membuat UI generik tanpa mengikuti design system.
20. Menghapus raw_message dari logs karena raw log tetap dibutuhkan untuk Advanced Logs.

---

## 22. Definition of Done MVP

MVP dianggap selesai jika:

```text
1. Admin bisa login.
2. VPS menjalankan Laravel, database monitoring, dan RSyslog.
3. Dua laptop Windows menjalankan Windows Agent.
4. Device auto-register tanpa hardcode.
5. Dashboard menampilkan device real.
6. Dashboard menampilkan CPU/RAM/disk real.
7. Dashboard menampilkan Firebird connectivity real.
8. Dashboard menampilkan Accurate.exe status real.
9. Alert contextual dibuat dengan evidence.
10. Telegram mengirim alert contextual.
11. Accurate audit reader membaca AUDIT + USERS dari Firebird.
12. Accurate Audit page menampilkan audit trail.
13. Remote Desktop launcher tersedia.
14. Remote Restart manual tercatat dan dapat dieksekusi oleh agent.
15. Advanced Logs tersedia untuk raw log.
16. Test Plan v2 skenario utama lulus.
```

---

## 23. Recommended First Prompt for Codex

Gunakan prompt awal ini saat memulai implementasi:

```text
Saya ingin membangun ulang project Centralized Log Monitoring Dashboard dari nol berdasarkan docs/v2.

Baca dokumen berikut terlebih dahulu:
1. 01_PRD_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md
2. 02_Accurate_Firebird_POC_Findings_v2.md
3. 03_SRS_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md
4. 04_Database_Design_v2.md
5. 05_System_Architecture_v2.md
6. 06_Windows_Agent_and_RSyslog_Guide_v2.md
7. 07_Detection_Rules_v2.md
8. 08_UI_Design_System_v2.md
9. 09_UI_Wireframe_v2.md
10. 10_Test_Plan_v2.md
11. 11_Deployment_Guide_VPS_ZeroTier_v2.md
12. 12_Demo_Script_v2.md
13. 13_Codex_Implementation_Brief.md

Bangun project bertahap, jangan semua sekaligus.
Mulai dari Milestone 0 dan Milestone 1:
- setup Laravel bersih,
- Tailwind,
- Alpine,
- Chart.js,
- AGENTS.md,
- auth admin,
- layout utama,
- menu final,
- komponen UI dasar.

Larangan:
- Jangan pakai React.
- Jangan pakai Node backend.
- Jangan hardcode device.
- Jangan buat raw log sebagai dashboard utama.
- Jangan implementasi Accurate Audit sebelum POC document dibaca.
- Jangan buat alert tanpa target dan evidence.

Setelah selesai, jelaskan file yang dibuat/diubah dan cara menjalankannya.
```

---

## 24. Catatan untuk Skripsi

Implementasi harus mudah dijelaskan dalam Bab 4:

```text
1. Konfigurasi VPS dan ZeroTier.
2. Konfigurasi RSyslog Server.
3. Implementasi Windows Agent.
4. Implementasi parser Laravel.
5. Implementasi dashboard.
6. Implementasi Accurate Audit Reader.
7. Implementasi Telegram alert.
8. Implementasi remote action manual.
9. Pengujian real-device.
```

Jangan membuat implementasi terlalu kompleks sampai sulit dijelaskan.

---

## 25. Penutup

Dokumen ini adalah execution guide untuk Codex. Tujuan utamanya adalah memastikan implementasi berjalan terstruktur, sesuai scope skripsi, dan tidak kembali menjadi dashboard log random.

Target akhir bukan sekadar aplikasi yang “bisa menampilkan log”, tetapi sistem monitoring IT skala kecil yang menampilkan kondisi real device pengguna Accurate, koneksi ke Firebird, performa device, audit trail Accurate, alert Telegram yang kontekstual, dan tindakan remote manual yang tercatat.

