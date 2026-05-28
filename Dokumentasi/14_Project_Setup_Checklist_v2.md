# 14 — Project Setup Checklist v2
# Centralized Log Monitoring Dashboard

**Versi:** 2.0  
**Status:** Final checklist sebelum mulai implementasi Codex  
**Project:** Centralized Log Monitoring Dashboard  
**Target Implementasi:** Real device, bukan simulasi random log  
**Environment Target:** 2 laptop Windows pengguna Accurate 5 + 1 VPS Linux + ZeroTier private network  
**Stack Utama:** Laravel, Blade, Tailwind CSS, Alpine.js, Chart.js, MySQL/MariaDB, RSyslog, Firebird 2.5, Windows Agent, Telegram Bot, ZeroTier, GitHub  

---

## 1. Tujuan Dokumen

Dokumen ini adalah checklist praktis sebelum pembangunan ulang project menggunakan Codex.

Dokumen ini dibuat agar developer/AI agent tidak langsung coding tanpa memastikan environment, akun, credential, device, network, dan keputusan teknis sudah siap.

Dokumen ini menjawab pertanyaan:

1. Apa saja yang harus disiapkan sebelum mulai coding?
2. Apa saja credential dan konfigurasi yang harus tersedia?
3. Apa saja port dan jaringan yang harus dibuka?
4. Apa saja data dari real device yang harus bisa dikirim?
5. Apa saja yang harus diuji sebelum fitur dianggap siap?
6. Apa saja informasi yang belum boleh diasumsikan oleh Codex?

Dokumen ini bukan pengganti PRD/SRS/Architecture. Dokumen ini adalah checklist operasional untuk memastikan project dapat dibangun dan diuji pada real device.

---

## 2. Prinsip Utama Setup

Project ini harus mengikuti prinsip berikut:

| Prinsip | Keterangan |
|---|---|
| Real-device first | Target utama adalah dua laptop Windows asli dan satu VPS Linux, bukan container client simulasi. |
| No hardcoded device | Device tidak boleh dikunci berdasarkan nama seperti `WIN-ACC-01`; nama hanya contoh dokumentasi. |
| Agent-based monitoring | Data Windows dikirim oleh Windows Agent yang berjalan di host Windows asli. |
| RSyslog sebagai collector | RSyslog menerima structured log dari Windows Agent dan menyimpannya untuk Laravel Parser. |
| Accurate Audit direct read-only | Audit trail Accurate dibaca langsung dari Firebird `AUDIT + USERS`, bukan lewat RSyslog. |
| Telegram wajib | Telegram Alert adalah fitur wajib, dengan format contextual alert. |
| Remote action manual | Remote restart hanya manual, pakai konfirmasi, alasan, dan audit log. |
| Advanced Logs bukan dashboard utama | Raw log hanya untuk investigasi teknis, bukan tampilan utama dashboard. |
| GitHub wajib | Semua perubahan project harus tersimpan di GitHub dengan workflow branch yang jelas. |

---

## 3. Dokumen yang Harus Dibaca Sebelum Setup

Sebelum mulai implementasi, Codex/developer wajib membaca dokumen berikut secara berurutan:

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
14_Project_Setup_Checklist_v2.md
```

Catatan:

- Dokumen v1 boleh dijadikan referensi sejarah, tetapi tidak boleh dijadikan sumber utama implementasi.
- Implementasi final mengikuti v2.
- Jika ada konflik antara v1 dan v2, gunakan v2.

---

## 4. Topologi Target yang Harus Disiapkan

Topologi implementasi:

```text
[Windows Laptop 1]
- Accurate 5 Client
- Windows Agent
- ZeroTier Client
- RDP enabled

[Windows Laptop 2]
- Accurate 5 Client
- Windows Agent
- ZeroTier Client
- RDP enabled

        ↓ lewat ZeroTier Private Network

[VPS Linux]
- RSyslog Server
- Laravel Dashboard
- MySQL/MariaDB Monitoring Database
- Firebird 2.5 Server / Accurate Database
- Accurate Audit Reader
- Telegram Alert Service
- Remote Command API
```

Target komunikasi:

```text
Windows Agent → RSyslog VPS
Windows Agent → Laravel API VPS
Laravel → MySQL/MariaDB
Laravel → Firebird Accurate DB
Accurate 5 Windows → Firebird VPS
Laravel → Telegram Bot API
Admin Browser → Laravel Dashboard
Admin Device → RDP ke Windows Client
```

---

## 5. Checklist Akun dan Akses

## 5.1 VPS

| Item | Status | Catatan |
|---|---|---|
| VPS sudah aktif | `[ ]` | Minimal 4 core / 8 GB RAM sesuai rencana. |
| Bisa SSH ke VPS | `[ ]` | Gunakan key-based SSH jika memungkinkan. |
| User sudo tersedia | `[ ]` | Jangan gunakan root untuk semua proses development harian. |
| OS VPS diketahui | `[ ]` | Rekomendasi Ubuntu Server LTS. |
| IP public VPS dicatat | `[ ]` | Untuk SSH dan fallback akses. |
| ZeroTier IP VPS dicatat | `[ ]` | Digunakan untuk komunikasi privat. |
| Storage cukup | `[ ]` | Minimal 30–50 GB kosong untuk project, database, log, backup. |

Data yang harus dicatat:

```text
VPS_PUBLIC_IP=
VPS_ZEROTIER_IP=
VPS_OS=
VPS_SSH_USER=
VPS_PROJECT_PATH=/var/www/centralized-log-monitoring
```

---

## 5.2 GitHub

| Item | Status | Catatan |
|---|---|---|
| Repository GitHub dibuat | `[ ]` | Private repository direkomendasikan. |
| Local Git terhubung remote | `[ ]` | `git remote -v` harus benar. |
| Branch `main` tersedia | `[ ]` | Untuk stable version. |
| Branch `develop` tersedia | `[ ]` | Untuk active development. |
| Proteksi branch dipertimbangkan | `[ ]` | Minimal jangan push sembarangan ke `main`. |
| `.gitignore` benar | `[ ]` | Jangan commit `.env`, credential, database file asli. |

Branch workflow:

```text
main      = stable/release
 develop  = active development
feature/* = fitur baru
fix/*     = perbaikan bug
```

Perintah awal:

```bash
git init
git add .
git commit -m "Initial project setup"
git branch -M main
git remote add origin <GITHUB_REPO_URL>
git push -u origin main

git checkout -b develop
git push -u origin develop
```

---

## 5.3 ZeroTier

| Item | Status | Catatan |
|---|---|---|
| Akun ZeroTier tersedia | `[ ]` | Bisa pakai akun personal. |
| Network ZeroTier dibuat | `[ ]` | Private network untuk VPS dan laptop Windows. |
| VPS join ZeroTier | `[ ]` | VPS harus muncul sebagai member. |
| Laptop 1 join ZeroTier | `[ ]` | Windows client 1. |
| Laptop 2 join ZeroTier | `[ ]` | Windows client 2. |
| Semua member authorized | `[ ]` | Di panel ZeroTier. |
| IP ZeroTier tiap device dicatat | `[ ]` | Digunakan untuk Firebird, dashboard, RDP, agent. |
| Ping antar device berhasil | `[ ]` | VPS ↔ Laptop 1, VPS ↔ Laptop 2. |

Data yang harus dicatat:

```text
ZEROTIER_NETWORK_ID=
VPS_ZT_IP=
LAPTOP_1_ZT_IP=
LAPTOP_2_ZT_IP=
```

Test awal:

```bash
# Dari VPS
ping <LAPTOP_1_ZT_IP>
ping <LAPTOP_2_ZT_IP>

# Dari Windows
ping <VPS_ZT_IP>
```

Expected result:

```text
Semua device dapat saling ping melalui IP ZeroTier.
```

---

## 5.4 Telegram Bot

| Item | Status | Catatan |
|---|---|---|
| Bot Telegram dibuat via BotFather | `[ ]` | Simpan token secara aman. |
| Chat ID penerima diketahui | `[ ]` | Bisa personal chat atau group. |
| Bot bisa kirim pesan test | `[ ]` | Wajib sebelum integrasi alert. |
| Token tidak di-commit ke GitHub | `[ ]` | Simpan di `.env`. |

Data `.env`:

```env
TELEGRAM_ALERT_ENABLED=true
TELEGRAM_BOT_TOKEN=
TELEGRAM_CHAT_ID=
```

Format test pesan manual:

```text
[TEST] Centralized Log Monitoring Dashboard
Telegram integration is ready.
```

Acceptance criteria:

```text
Sistem dapat mengirim pesan test ke Telegram sebelum alert real diaktifkan.
```

---

## 6. Checklist VPS Linux

## 6.1 Paket Dasar

Paket yang harus tersedia:

| Paket | Fungsi | Status |
|---|---|---|
| git | Clone/push repository | `[ ]` |
| curl | Download/test endpoint | `[ ]` |
| unzip | Extract dependency | `[ ]` |
| rsyslog | Log collector | `[ ]` |
| nginx/apache | Web server Laravel | `[ ]` |
| php | Runtime Laravel | `[ ]` |
| composer | Dependency PHP | `[ ]` |
| mysql/mariadb | Monitoring DB | `[ ]` |
| firebird-server | Accurate DB server | `[ ]` |
| zerotier-one | Private network | `[ ]` |
| supervisor | Queue/scheduler/agent worker | `[ ]` |

Contoh instalasi dasar Ubuntu:

```bash
sudo apt update
sudo apt install -y git curl unzip rsyslog supervisor
```

Catatan:

- Versi detail PHP, MariaDB/MySQL, dan Firebird disesuaikan dengan compatibility Accurate/Firebird 2.5.
- Jangan menganggap Firebird versi terbaru otomatis compatible dengan database Accurate 5.

---

## 6.2 Direktori Project di VPS

Direktori yang disarankan:

```text
/var/www/centralized-log-monitoring
/var/log/remote
/var/backups/centralized-log-monitoring
/opt/accurate-db
```

Checklist:

| Direktori | Status | Fungsi |
|---|---|---|
| `/var/www/centralized-log-monitoring` | `[ ]` | Source Laravel. |
| `/var/log/remote` | `[ ]` | Raw log RSyslog dari Windows Agent. |
| `/var/backups/centralized-log-monitoring` | `[ ]` | Backup database/log/config. |
| `/opt/accurate-db` | `[ ]` | Lokasi database Accurate jika dipilih. |

Permission awal:

```bash
sudo mkdir -p /var/www/centralized-log-monitoring
sudo mkdir -p /var/log/remote
sudo mkdir -p /var/backups/centralized-log-monitoring
sudo mkdir -p /opt/accurate-db
```

---

## 6.3 Firewall VPS

Port yang perlu dipertimbangkan:

| Port | Scope | Fungsi | Catatan |
|---:|---|---|---|
| 22 | Public/Restricted | SSH | Batasi jika memungkinkan. |
| 80/443 | Public | Dashboard web | Jika pakai domain/SSL. |
| 8000 | Temporary | Laravel dev server | Tidak untuk production final. |
| 514 TCP/UDP | ZeroTier only | RSyslog | Lebih aman hanya via ZeroTier. |
| 3051 | ZeroTier only | Firebird Accurate | Jangan diekspos publik jika bisa. |
| 3306 | Localhost only | MySQL/MariaDB | Jangan public. |

Rekomendasi:

```text
- SSH boleh public tetapi gunakan password kuat/key.
- RSyslog hanya menerima dari ZeroTier IP laptop.
- Firebird hanya menerima dari ZeroTier IP laptop dan localhost/VPS.
- MySQL/MariaDB tidak dibuka ke publik.
```

Checklist:

| Item | Status |
|---|---|
| Firewall aktif | `[ ]` |
| Port SSH dapat diakses | `[ ]` |
| Port dashboard dapat diakses admin | `[ ]` |
| Port RSyslog dapat diakses dari laptop via ZeroTier | `[ ]` |
| Port Firebird dapat diakses dari laptop via ZeroTier | `[ ]` |
| MySQL tidak terbuka publik | `[ ]` |

---

## 7. Checklist Firebird / Accurate Database

## 7.1 Penempatan Database Accurate

Keputusan yang dikunci:

```text
Database Accurate / Firebird berada di VPS Linux.
Accurate 5 pada laptop Windows mengakses database tersebut melalui ZeroTier IP VPS.
```

Checklist:

| Item | Status | Catatan |
|---|---|---|
| Firebird server terpasang di VPS | `[ ]` | Target compatibility Firebird 2.5. |
| Port Firebird aktif | `[ ]` | Umumnya 3051 sesuai POC kamu. |
| Database Accurate tersedia | `[ ]` | File `.FDB`/database sesuai setup Accurate. |
| Accurate 5 Windows bisa connect ke VPS | `[ ]` | Lewat ZeroTier IP. |
| Backup database dibuat sebelum percobaan | `[ ]` | Wajib. |

Data yang harus dicatat:

```text
FIREBIRD_HOST=<VPS_ZT_IP>
FIREBIRD_PORT=3051
FIREBIRD_DATABASE_PATH=
FIREBIRD_AUDIT_USERNAME=
FIREBIRD_AUDIT_PASSWORD=
```

---

## 7.2 User Read-only untuk Audit Reader

Keputusan:

```text
Accurate Audit Reader wajib memakai user read-only / guest khusus.
Tidak boleh memakai akun admin database untuk aplikasi monitoring harian.
```

Checklist:

| Item | Status |
|---|---|
| User Firebird read-only tersedia | `[ ]` |
| User hanya bisa SELECT tabel audit relevan | `[ ]` |
| Credential disimpan di `.env` | `[ ]` |
| Credential tidak di-commit ke GitHub | `[ ]` |
| Query `AUDIT + USERS` berhasil | `[ ]` |

Catatan penting:

```text
- Tabel LOGIN tidak boleh dijadikan sumber utama.
- COMP_NAME dan IPADDRESS di AUDIT tidak wajib karena bisa kosong.
- Username internal Accurate harus berasal dari relasi AUDIT.USERID → USERS.USERID sesuai POC.
```

---

## 7.3 Query POC yang Harus Dikonfirmasi

Sebelum coding sync penuh, developer wajib menguji query read-only ke Firebird.

Checklist query:

| Query | Status | Tujuan |
|---|---|---|
| SELECT sample dari AUDIT | `[ ]` | Memastikan tabel audit terbaca. |
| SELECT sample dari USERS | `[ ]` | Memastikan user Accurate terbaca. |
| JOIN AUDIT + USERS | `[ ]` | Memetakan user internal Accurate. |
| Sort berdasarkan timestamp/audit id | `[ ]` | Menentukan incremental sync. |
| Cek field nullable | `[ ]` | COMP_NAME/IPADDRESS bisa kosong. |

Output minimal yang harus bisa didapat:

```text
audit_id / primary key
activity_time
userid
username Accurate
fullname Accurate jika ada
source/module jika ada
transaction type jika ada
description
reference/invoice jika ada
app version jika ada
status jika ada
```

Jika nama kolom berbeda dari asumsi, dokumentasikan hasil real sebelum coding.

---

## 8. Checklist Laptop Windows Client

## 8.1 Device Fisik

Target minimal:

```text
Laptop Windows 1 = pengguna Accurate 5 pertama
Laptop Windows 2 = pengguna Accurate 5 kedua
```

Checklist per laptop:

| Item | Laptop 1 | Laptop 2 | Catatan |
|---|---|---|---|
| Windows aktif dan stabil | `[ ]` | `[ ]` | Windows 10/11. |
| Accurate 5 terinstall | `[ ]` | `[ ]` | Client aplikasi. |
| ZeroTier terinstall | `[ ]` | `[ ]` | Join network yang sama. |
| Bisa ping VPS ZT IP | `[ ]` | `[ ]` | Network ready. |
| Bisa akses Firebird VPS:3051 | `[ ]` | `[ ]` | Accurate DB connectivity. |
| RDP enabled | `[ ]` | `[ ]` | Untuk remote desktop. |
| Windows Agent bisa jalan | `[ ]` | `[ ]` | PowerShell/Python/service. |

Data yang dicatat otomatis oleh agent:

```text
agent_id
hostname
windows_user
ip_zerotier
os_version
agent_version
last_seen
```

---

## 8.2 Accurate 5 Client

Checklist:

| Item | Laptop 1 | Laptop 2 |
|---|---|---|
| Accurate bisa dibuka | `[ ]` | `[ ]` |
| Accurate bisa connect ke database VPS | `[ ]` | `[ ]` |
| `accurate.exe` terlihat di process list saat berjalan | `[ ]` | `[ ]` |
| User Accurate bisa melakukan aktivitas uji | `[ ]` | `[ ]` |
| Aktivitas muncul di tabel AUDIT | `[ ]` | `[ ]` |

Catatan:

```text
Windows Agent hanya mendeteksi process accurate.exe dan Windows user.
Windows Agent tidak boleh mengarang username internal Accurate.
Username internal Accurate berasal dari Accurate Audit Reader di VPS.
```

---

## 8.3 RDP

Checklist:

| Item | Laptop 1 | Laptop 2 |
|---|---|---|
| Remote Desktop diaktifkan | `[ ]` | `[ ]` |
| User Windows boleh login via RDP | `[ ]` | `[ ]` |
| Firewall Windows mengizinkan RDP via ZeroTier | `[ ]` | `[ ]` |
| Admin bisa test `mstsc /v:<ZT_IP>` | `[ ]` | `[ ]` |
| Dashboard bisa generate/open RDP link | `[ ]` | `[ ]` |

Catatan:

```text
Remote Desktop launcher tidak sama dengan remote restart.
Remote Desktop cukup menyediakan link/file .rdp atau instruksi mstsc.
```

---

## 8.4 Remote Restart

Keputusan:

```text
Remote restart harus real-device jika memungkinkan.
Tetap manual terkontrol, bukan otomatis.
```

Checklist:

| Item | Laptop 1 | Laptop 2 |
|---|---|---|
| Agent bisa polling Laravel API | `[ ]` | `[ ]` |
| Agent bisa menerima command pending | `[ ]` | `[ ]` |
| Agent bisa report command result | `[ ]` | `[ ]` |
| Restart command diuji dengan konfirmasi | `[ ]` | `[ ]` |
| Remote action tercatat di dashboard | `[ ]` | `[ ]` |

Command Windows yang mungkin digunakan:

```powershell
shutdown /r /t 30 /c "Restart requested by Centralized Log Monitoring Dashboard"
```

Syarat UI:

```text
Admin wajib menekan tombol Restart Client.
Sistem wajib menampilkan confirmation modal.
Admin wajib mengisi alasan.
Sistem wajib mencatat admin, target device, waktu, alasan, status, dan result.
```

Tidak boleh:

```text
- Auto restart berdasarkan alert.
- Restart tanpa alasan.
- Restart tanpa audit log.
- Restart device yang belum terdaftar.
```

---

## 9. Checklist Windows Agent

## 9.1 Bahasa Implementasi Agent

Opsi yang diperbolehkan:

```text
PowerShell script
Python script
Windows service sederhana
```

MVP yang direkomendasikan:

```text
Mulai dari PowerShell atau Python script terjadwal.
Setelah stabil, dapat dijadikan Windows service.
```

Checklist:

| Item | Status |
|---|---|
| Agent memiliki config file lokal | `[ ]` |
| Agent memiliki agent_id permanen | `[ ]` |
| Agent dapat kirim syslog ke RSyslog VPS | `[ ]` |
| Agent dapat polling API command | `[ ]` |
| Agent dapat mengirim heartbeat | `[ ]` |
| Agent dapat mengambil CPU/RAM/Disk | `[ ]` |
| Agent dapat cek Firebird connectivity | `[ ]` |
| Agent dapat cek accurate.exe | `[ ]` |
| Agent dapat cek RDP status | `[ ]` |
| Agent dapat report remote action result | `[ ]` |

---

## 9.2 Config Agent

File config agent yang disarankan:

```json
{
  "agent_id": "generated-uuid",
  "device_label": "Laptop Finance 1",
  "rsyslog_host": "<VPS_ZT_IP>",
  "rsyslog_port": 514,
  "api_base_url": "https://<dashboard-domain-or-vps>/api/agent",
  "api_token": "agent-secret-token",
  "firebird_host": "<VPS_ZT_IP>",
  "firebird_port": 3051,
  "heartbeat_interval_seconds": 30,
  "telemetry_interval_seconds": 60,
  "command_poll_interval_seconds": 15
}
```

Catatan:

```text
agent_id tidak boleh berubah setiap agent restart.
api_token harus unik per agent.
Config tidak boleh berisi credential Firebird audit reader.
```

---

## 9.3 Format Structured Log dari Agent

Format utama:

```text
<event_type>: key=value key=value key=value
```

Contoh heartbeat:

```text
device-heartbeat: agent_id=abc123 hostname=DESKTOP-01 device_label="Laptop Finance 1" windows_user="DESKTOP-01\\Finance" ip_zerotier=10.10.10.11 agent_status=online rdp_status=available
```

Contoh telemetry:

```text
performance-telemetry: agent_id=abc123 hostname=DESKTOP-01 cpu=42 ram=68 disk=55 uptime_minutes=240
```

Contoh Firebird check:

```text
firebird-connectivity: agent_id=abc123 hostname=DESKTOP-01 target_host=10.10.10.5 target_port=3051 status=connected latency_ms=23 attempts=1
```

Contoh Accurate process:

```text
accurate-process: agent_id=abc123 hostname=DESKTOP-01 process=accurate.exe status=running owner="DESKTOP-01\\Finance" pid=5420
```

Contoh remote action result:

```text
remote-action-result: agent_id=abc123 action_id=55 action_type=RESTART_CLIENT status=accepted result="restart scheduled in 30 seconds"
```

---

## 10. Checklist Laravel Application

## 10.1 Environment Variable Laravel

`.env` minimal:

```env
APP_NAME="Centralized Log Monitoring Dashboard"
APP_ENV=production
APP_DEBUG=false
APP_URL=

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=centralized_log_monitoring
DB_USERNAME=
DB_PASSWORD=

RSYSLOG_REMOTE_LOG_PATH=/var/log/remote
PARSER_BATCH_LIMIT=500

TELEGRAM_ALERT_ENABLED=true
TELEGRAM_BOT_TOKEN=
TELEGRAM_CHAT_ID=

FIREBIRD_AUDIT_ENABLED=true
FIREBIRD_HOST=127.0.0.1
FIREBIRD_PORT=3051
FIREBIRD_DATABASE_PATH=
FIREBIRD_AUDIT_USERNAME=
FIREBIRD_AUDIT_PASSWORD=
FIREBIRD_AUDIT_SYNC_INTERVAL_SECONDS=60

AGENT_API_ENABLED=true
REMOTE_RESTART_ENABLED=true
REMOTE_RESTART_REQUIRE_REASON=true
```

Checklist:

| Item | Status |
|---|---|
| `.env.example` dibuat tanpa secret | `[ ]` |
| `.env` production tidak di-commit | `[ ]` |
| `APP_KEY` generated | `[ ]` |
| DB connection berhasil | `[ ]` |
| Telegram test berhasil | `[ ]` |
| RSyslog path readable | `[ ]` |
| Firebird audit connection berhasil | `[ ]` |

---

## 10.2 Artisan Commands

Command yang harus tersedia:

| Command | Fungsi | Status |
|---|---|---|
| `php artisan rsyslog:parse` | Parse structured log dari `/var/log/remote` | `[ ]` |
| `php artisan accurate:audit-sync` | Sync AUDIT + USERS dari Firebird | `[ ]` |
| `php artisan alerts:evaluate` | Evaluasi alert/incident jika dipisah | `[ ]` |
| `php artisan telegram:test` | Test Telegram | `[ ]` |
| `php artisan devices:mark-offline` | Mark device warning/offline berdasarkan heartbeat | `[ ]` |

Catatan:

```text
Command parser v2 harus membaca structured key=value.
Tidak boleh hanya mengandalkan keyword random seperti v1.
```

---

## 11. Checklist RSyslog Server

## 11.1 Konfigurasi RSyslog VPS

Requirement:

```text
RSyslog server di VPS menerima log TCP/UDP dari Windows Agent melalui ZeroTier.
Log disimpan per hostname dan juga all.log.
Laravel Parser membaca file tersebut.
```

Checklist:

| Item | Status |
|---|---|
| RSyslog service running | `[ ]` |
| TCP input aktif | `[ ]` |
| UDP input aktif jika dipakai | `[ ]` |
| `/var/log/remote` dibuat | `[ ]` |
| Log dari Laptop 1 masuk | `[ ]` |
| Log dari Laptop 2 masuk | `[ ]` |
| File per-host terbentuk | `[ ]` |
| `all.log` terbentuk | `[ ]` |
| Laravel user bisa baca log | `[ ]` |

Expected file:

```text
/var/log/remote/DESKTOP-01.log
/var/log/remote/DESKTOP-02.log
/var/log/remote/all.log
```

---

## 12. Checklist Database Monitoring

Tabel dari `04_Database_Design_v2.md` harus dibuat melalui Laravel migration.

Checklist migration utama:

| Tabel | Status |
|---|---|
| users | `[ ]` |
| devices | `[ ]` |
| agent_credentials | `[ ]` |
| device_telemetries | `[ ]` |
| network_checks | `[ ]` |
| accurate_process_snapshots | `[ ]` |
| server_service_checks | `[ ]` |
| logs | `[ ]` |
| parser_offsets | `[ ]` |
| parser_runs | `[ ]` |
| accurate_audit_sources | `[ ]` |
| accurate_audit_events | `[ ]` |
| accurate_audit_sync_states | `[ ]` |
| accurate_audit_sync_runs | `[ ]` |
| alerts | `[ ]` |
| alert_evidences | `[ ]` |
| alert_notifications | `[ ]` |
| incidents | `[ ]` |
| incident_alerts | `[ ]` |
| remote_actions | `[ ]` |
| threshold_settings | `[ ]` |
| system_settings | `[ ]` |

Seed data minimal:

| Data | Status |
|---|---|
| Admin user | `[ ]` |
| Threshold default | `[ ]` |
| System settings default | `[ ]` |
| Telegram disabled/enabled setting | `[ ]` |
| Accurate audit source placeholder | `[ ]` |

---

## 13. Checklist Contextual Alert

Alert tidak boleh dibuat tanpa target dan evidence.

Field wajib alert:

```text
severity
target_type
target_id / target_name
title
description
evidence_summary
impact
recommended_action
detected_by
detected_at
status
```

Checklist:

| Alert Rule | Status |
|---|---|
| Device heartbeat missed | `[ ]` |
| Device offline | `[ ]` |
| CPU high | `[ ]` |
| CPU critical | `[ ]` |
| RAM high | `[ ]` |
| Disk high | `[ ]` |
| Firebird port timeout from device | `[ ]` |
| Firebird latency high from device | `[ ]` |
| Firebird service down on VPS | `[ ]` |
| Accurate process not detected | `[ ]` |
| Accurate audit delete/update important if supported | `[ ]` |

Tidak boleh:

```text
- Firebird unreachable tanpa menyebut target dan sumber deteksi.
- Accurate process not running tanpa menyebut device.
- Audit spike detected tanpa rule statistik yang jelas.
- Telegram message tanpa evidence.
```

---

## 14. Checklist Telegram Contextual Alert

Format Telegram wajib contextual.

Template:

```text
[{{ severity }}] {{ title }}

Target     : {{ target_name }}
Detected by: {{ detected_by }}
Evidence   : {{ evidence_summary }}
Impact     : {{ impact }}
Action     : {{ recommended_action }}
Time       : {{ detected_at }}

Dashboard  : {{ alert_url }}
```

Checklist:

| Item | Status |
|---|---|
| Telegram test command tersedia | `[ ]` |
| Telegram alert terkirim untuk CPU high | `[ ]` |
| Telegram alert terkirim untuk Firebird timeout | `[ ]` |
| Telegram alert terkirim untuk Firebird service down | `[ ]` |
| Telegram mencantumkan target | `[ ]` |
| Telegram mencantumkan evidence | `[ ]` |
| Telegram mencantumkan recommended action | `[ ]` |
| Anti-spam/cooldown aktif | `[ ]` |

---

## 15. Checklist UI Pages

Halaman final:

| Page | Route | Status |
|---|---|---|
| Login | `/login` | `[ ]` |
| Dashboard | `/dashboard` | `[ ]` |
| Devices | `/devices` | `[ ]` |
| Device Detail | `/devices/{id}` | `[ ]` |
| Accurate Audit | `/accurate-audit` | `[ ]` |
| Incidents | `/incidents` | `[ ]` |
| Alerts | `/alerts` | `[ ]` |
| Alert Detail | `/alerts/{id}` | `[ ]` |
| Remote Actions | `/remote-actions` | `[ ]` |
| Advanced Logs | `/advanced-logs` | `[ ]` |
| Settings | `/settings` | `[ ]` |

UI rules:

```text
- Dashboard bukan raw log viewer.
- Raw log hanya tampil di Advanced Logs.
- Devices page harus fokus ke status device real.
- Device Detail harus punya Remote Desktop dan Restart Client.
- Accurate Audit harus fokus ke aktivitas Accurate dari Firebird.
- Alerts harus menampilkan target + evidence + impact + action.
```

---

## 16. Checklist Sebelum Codex Mulai Coding

Sebelum prompt implementasi diberikan ke Codex, pastikan:

| Item | Status |
|---|---|
| Semua dokumen v2 tersedia di repo `/docs` | `[ ]` |
| `AGENTS.md` tersedia di root repo | `[ ]` |
| GitHub repo sudah siap | `[ ]` |
| Branch `develop` sudah dibuat | `[ ]` |
| VPS bisa diakses SSH | `[ ]` |
| ZeroTier network sudah aktif | `[ ]` |
| Dua laptop Windows join ZeroTier | `[ ]` |
| Telegram bot siap | `[ ]` |
| Firebird/Accurate DB path diketahui | `[ ]` |
| Credential read-only Firebird tersedia | `[ ]` |
| Keputusan no hardcode device dipahami | `[ ]` |
| Keputusan no simulation-first dipahami | `[ ]` |

---

## 17. Suggested AGENTS.md Checklist

Root repo harus memiliki `AGENTS.md`.

Isi minimal:

```md
# AGENTS.md

## Project
Centralized Log Monitoring Dashboard untuk monitoring real-device Windows Accurate 5 pada PT XYZ skala kecil.

## Stack
- Laravel
- Blade
- Tailwind CSS
- Alpine.js
- Chart.js
- MySQL/MariaDB
- RSyslog
- Firebird 2.5
- Windows Agent
- Telegram Bot
- ZeroTier

## Mandatory Architecture
Windows Agent -> RSyslog VPS -> Laravel Parser -> MySQL -> Dashboard -> Telegram Alert.
Accurate Audit Reader -> Firebird AUDIT + USERS -> MySQL -> Dashboard.
Remote Actions -> Laravel API -> Windows Agent polling -> Windows command.

## Do Not
- Do not hardcode device names.
- Do not use WSL as primary Windows monitoring source.
- Do not treat LOGIN table as primary Accurate login source.
- Do not show raw Windows Event Log noise on dashboard.
- Do not auto restart devices.
- Do not create alert without target and evidence.
- Do not introduce React, ELK, Grafana, Prometheus, or SIEM stack.
```

---

## 18. Risk Checklist

| Risiko | Mitigasi | Status |
|---|---|---|
| Firebird connection gagal dari Accurate | Pastikan port, firewall, ZeroTier, path DB benar | `[ ]` |
| Firebird audit query berbeda dari asumsi | Dokumentasikan hasil query real sebelum coding final | `[ ]` |
| Windows Agent tidak jalan sebagai service | Mulai dari script manual/scheduled task dulu | `[ ]` |
| RDP gagal | Pastikan Windows edition, firewall, user permission | `[ ]` |
| Remote restart terlalu berisiko | Gunakan confirmation modal + reason + test pada device non-kritis | `[ ]` |
| Telegram spam | Terapkan cooldown/anti-duplicate | `[ ]` |
| Dashboard lambat karena log banyak | Pagination, index, advanced logs dipisah | `[ ]` |
| Credential bocor ke GitHub | `.env` di-gitignore, gunakan `.env.example` | `[ ]` |
| VPS firewall terlalu terbuka | Batasi Firebird/RSyslog ke ZeroTier | `[ ]` |

---

## 19. Milestone Readiness Gate

## 19.1 Gate 0 — Project Ready

Syarat:

```text
[ ] Repo GitHub siap
[ ] Dokumen v2 masuk repo
[ ] AGENTS.md dibuat
[ ] VPS SSH ready
[ ] ZeroTier ready
[ ] Telegram ready
```

Jika belum lengkap, jangan mulai coding besar.

---

## 19.2 Gate 1 — Core Web Ready

Syarat:

```text
[ ] Laravel install
[ ] Auth admin
[ ] Layout UI
[ ] Database migration
[ ] Seeder admin
[ ] Dashboard placeholder
```

---

## 19.3 Gate 2 — Real Device Monitoring Ready

Syarat:

```text
[ ] Windows Agent kirim heartbeat
[ ] RSyslog menerima log laptop 1 dan 2
[ ] Parser menyimpan device
[ ] Devices page tampil real data
[ ] CPU/RAM/Disk tampil
[ ] Firebird connectivity tampil
[ ] Accurate process tampil
```

---

## 19.4 Gate 3 — Accurate Audit Ready

Syarat:

```text
[ ] Firebird read-only connection berhasil
[ ] Query AUDIT + USERS berhasil
[ ] Audit sync incremental
[ ] Accurate Audit page tampil
[ ] Filter audit jalan
```

---

## 19.5 Gate 4 — Alert & Telegram Ready

Syarat:

```text
[ ] Detection rules aktif
[ ] Alert punya target/evidence
[ ] Telegram alert terkirim
[ ] Cooldown aktif
[ ] Alert detail tampil rapi
```

---

## 19.6 Gate 5 — Remote Action Ready

Syarat:

```text
[ ] Remote Desktop launcher tersedia
[ ] Restart command dibuat manual
[ ] Agent polling command
[ ] Agent report result
[ ] Remote Actions page mencatat tindakan
```

---

## 20. Open Questions yang Harus Diisi Saat Setup

Beberapa hal hanya bisa dipastikan saat environment real tersedia.

| Pertanyaan | Jawaban Real |
|---|---|
| OS VPS final apa? |  |
| Firebird versi final apa? |  |
| Port Firebird benar 3051 atau berbeda? |  |
| Path database Accurate di VPS apa? |  |
| Nama tabel AUDIT dan USERS persis apa? |  |
| Primary key AUDIT apa? |  |
| Field timestamp AUDIT apa? |  |
| Field transaction type ada atau tidak? |  |
| Field invoice/reference ada atau tidak? |  |
| RDP dari admin ke laptop lewat ZeroTier berhasil? |  |
| Agent akan dibuat PowerShell atau Python? |  |
| Dashboard pakai domain atau IP dulu? |  |
| SSL langsung dipakai atau belakangan? |  |

Catatan:

```text
Jika jawaban real berbeda dari asumsi dokumen, update dokumen teknis sebelum meminta Codex melanjutkan implementasi fitur terkait.
```

---

## 21. Acceptance Criteria Checklist Ini

Checklist ini dianggap selesai jika:

```text
[ ] Semua akun dan environment utama siap.
[ ] VPS, ZeroTier, laptop Windows, Telegram, Firebird sudah memiliki data konfigurasi.
[ ] Semua credential sensitif disimpan aman dan tidak masuk GitHub.
[ ] Codex memiliki dokumen v2 dan AGENTS.md sebelum coding.
[ ] Tidak ada keputusan penting yang masih ambigu untuk MVP.
```

---

## 22. Ringkasan Eksekusi

Urutan setup praktis:

```text
1. Siapkan GitHub repo.
2. Masukkan semua dokumen v2 ke /docs.
3. Buat AGENTS.md.
4. Siapkan VPS dan SSH.
5. Install ZeroTier di VPS dan 2 laptop Windows.
6. Pastikan semua device bisa ping via ZeroTier.
7. Siapkan Telegram bot dan chat ID.
8. Siapkan Firebird/Accurate DB di VPS.
9. Pastikan Accurate 5 di Windows bisa connect ke DB VPS.
10. Siapkan credential read-only Firebird untuk Audit Reader.
11. Baru mulai implementasi Laravel + Windows Agent dengan Codex.
```

