# 09 UI Wireframe v2
# Centralized Log Monitoring Dashboard

**Versi:** 2.0  
**Status:** Draft Final untuk AI Agent / Codex  
**Project:** Centralized Log Monitoring Dashboard  
**Target Implementasi:** Real-device monitoring untuk laptop Windows pengguna Accurate 5 dan VPS Linux  
**Stack UI:** Laravel Blade, Tailwind CSS, Alpine.js, Chart.js, Heroicons/Lucide Icons  
**Dokumen Terkait:**

1. `01_PRD_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md`
2. `02_Accurate_Firebird_POC_Findings_v2.md`
3. `03_SRS_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md`
4. `04_Database_Design_v2.md`
5. `05_System_Architecture_v2.md`
6. `06_Windows_Agent_and_RSyslog_Guide_v2.md`
7. `07_Detection_Rules_v2.md`
8. `08_UI_Design_System_v2.md`

---

## 1. Tujuan Dokumen

Dokumen ini menjelaskan rancangan wireframe halaman untuk aplikasi **Centralized Log Monitoring Dashboard** versi real-device.

Tujuan utama dokumen ini adalah memastikan Codex / AI Agent membangun UI yang:

1. Fokus pada kebutuhan Administrator IT.
2. Tidak menjadikan raw log sebagai informasi utama dashboard.
3. Menampilkan kondisi real device secara jelas.
4. Menampilkan status koneksi Accurate/Firebird secara mudah dipahami.
5. Menampilkan audit trail Accurate secara rapi dan dapat difilter.
6. Menampilkan alert dan incident yang punya target, evidence, impact, dan recommended action.
7. Menyediakan action manual seperti Remote Desktop dan Remote Restart dengan aman.
8. Konsisten dengan design system v2.

Dokumen ini bukan hanya wireframe visual, tetapi juga menjelaskan:

- tujuan setiap halaman,
- data yang tampil,
- komponen UI,
- empty state,
- filter,
- action,
- route Laravel,
- sumber data,
- dan hal yang tidak boleh dilakukan Codex.

---

## 2. Prinsip Umum Wireframe

Semua halaman harus mengikuti prinsip berikut:

| Prinsip | Penjelasan |
|---|---|
| Dashboard-first | Halaman utama langsung menjawab kondisi operasional, bukan menampilkan semua log |
| Target-specific | Semua alert/action harus jelas target device/server-nya |
| Evidence-based | Semua warning/critical harus punya bukti pengukuran |
| Compact but readable | Data teknis cukup padat, tetapi spacing tetap rapi |
| Actionable | Admin bisa melakukan aksi dari halaman yang relevan |
| Safe remote action | Restart wajib memakai konfirmasi dan audit log |
| No noise | Raw log hanya muncul pada Advanced Logs atau detail teknis |
| Real-device oriented | Tidak boleh menampilkan data dummy/random sebagai asumsi utama |

---

## 3. Struktur Navigasi Final

Sidebar final:

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

### 3.1 Route Utama

| Menu | Route Name | URL | Controller |
|---|---|---|---|
| Login | `login` | `/login` | `AuthController` |
| Dashboard | `dashboard.index` | `/dashboard` | `DashboardController` |
| Devices | `devices.index` | `/devices` | `DeviceController` |
| Device Detail | `devices.show` | `/devices/{device}` | `DeviceController` |
| Accurate Audit | `accurate-audits.index` | `/accurate-audits` | `AccurateAuditController` |
| Accurate Audit Detail | `accurate-audits.show` | `/accurate-audits/{audit}` | `AccurateAuditController` |
| Incidents | `incidents.index` | `/incidents` | `IncidentController` |
| Incident Detail | `incidents.show` | `/incidents/{incident}` | `IncidentController` |
| Alerts | `alerts.index` | `/alerts` | `AlertController` |
| Alert Detail | `alerts.show` | `/alerts/{alert}` | `AlertController` |
| Acknowledge Alert | `alerts.acknowledge` | `/alerts/{alert}/acknowledge` | `AlertController` |
| Resolve Alert | `alerts.resolve` | `/alerts/{alert}/resolve` | `AlertController` |
| Remote Actions | `remote-actions.index` | `/remote-actions` | `RemoteActionController` |
| Create Remote Action | `remote-actions.store` | `/remote-actions` | `RemoteActionController` |
| Advanced Logs | `advanced-logs.index` | `/advanced-logs` | `AdvancedLogController` |
| Advanced Log Detail | `advanced-logs.show` | `/advanced-logs/{log}` | `AdvancedLogController` |
| Settings | `settings.index` | `/settings` | `SettingController` |
| Update Settings | `settings.update` | `/settings` | `SettingController` |
| Logout | `logout` | `/logout` | `AuthController` |

---

## 4. Global Layout

Semua halaman setelah login menggunakan layout utama:

```text
resources/views/layouts/app.blade.php
```

### 4.1 Global Desktop Layout

```text
+--------------------------------------------------------------------------------+
| Topbar: Centralized Log Monitoring Dashboard        Open Alerts: 3     Admin    |
+----------------------+---------------------------------------------------------+
| Sidebar              | Page Header                                             |
|                      | Title                                                   |
| Dashboard            | Short description                                       |
| Devices              |                                                         |
| Accurate Audit       | Main Content                                            |
| Incidents            |                                                         |
| Alerts               |                                                         |
| Remote Actions       |                                                         |
| Advanced Logs        |                                                         |
| Settings             |                                                         |
+----------------------+---------------------------------------------------------+
```

### 4.2 Sidebar

Sidebar menampilkan:

```text
Centralized Log Monitoring
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

Ketentuan:

1. Menu aktif diberi highlight.
2. Sidebar gelap, content terang.
3. Icon boleh menggunakan Heroicons/Lucide.
4. Jangan memakai emoji sebagai icon utama.
5. Logout berada di bagian bawah sidebar.

### 4.3 Topbar

Topbar menampilkan:

| Elemen | Keterangan |
|---|---|
| App title | Centralized Log Monitoring Dashboard |
| Current time / refresh timestamp | Waktu refresh terakhir |
| Open alert badge | Jumlah alert open |
| Admin name | Nama admin login |
| Mobile sidebar toggle | Hanya mobile/tablet |

Contoh:

```text
Centralized Log Monitoring Dashboard        Last refresh: 14:32:08      Open Alerts: 3      Administrator
```

### 4.4 Page Header

Setiap halaman memiliki header:

```text
Title
Short description
Optional right action button
```

Contoh:

```text
Devices
Daftar laptop Windows pengguna Accurate 5 yang terhubung ke sistem monitoring.
[Refresh]
```

---

## 5. Halaman Login

### 5.1 Tujuan

Halaman login digunakan agar hanya Administrator IT yang dapat mengakses dashboard.

### 5.2 Route

```text
GET  /login
POST /login
```

### 5.3 Wireframe

```text
+--------------------------------------------------------------------------------+
|                                                                                |
|                         Centralized Log Monitoring                              |
|                              Dashboard                                          |
|                                                                                |
|                 +------------------------------------------+                   |
|                 | Email                                    |                   |
|                 +------------------------------------------+                   |
|                 | Password                                 |                   |
|                 +------------------------------------------+                   |
|                 | [ Login ]                                |                   |
|                 +------------------------------------------+                   |
|                                                                                |
|            Internal IT Monitoring for PT XYZ Accurate Environment               |
|                                                                                |
+--------------------------------------------------------------------------------+
```

### 5.4 Komponen

| Komponen | Keterangan |
|---|---|
| App name | Centralized Log Monitoring Dashboard |
| Subtitle | Internal IT Monitoring for PT XYZ Accurate Environment |
| Email input | Required |
| Password input | Required |
| Login button | Submit |
| Error alert | Tampil jika login gagal |

### 5.5 Validasi UI

| Kondisi | UI Response |
|---|---|
| Email kosong | Pesan error di bawah field |
| Password kosong | Pesan error di bawah field |
| Credential salah | Alert: Email atau password salah |
| Login berhasil | Redirect ke `/dashboard` |

### 5.6 Larangan

Codex tidak boleh:

1. Menampilkan link register publik.
2. Menampilkan forgot password jika belum ada fitur.
3. Membuat login terlalu dekoratif seperti landing page.

---

## 6. Halaman Dashboard

### 6.1 Tujuan

Dashboard adalah halaman utama yang menjawab kondisi operasional secara cepat.

Dashboard harus menjawab pertanyaan:

```text
Device mana yang online?
Siapa user Windows yang aktif?
Device mana yang bisa konek ke Firebird?
Accurate 5 sedang berjalan atau tidak?
Ada incident yang perlu tindakan?
Ada audit Accurate terbaru?
```

Dashboard **bukan** halaman raw log.

### 6.2 Route

```text
GET /dashboard
```

### 6.3 Sumber Data

| Komponen | Sumber Data |
|---|---|
| Summary cards | `devices`, `device_telemetries`, `network_checks`, `accurate_process_snapshots`, `accurate_audit_events`, `alerts`, `incidents` |
| Device health table | latest telemetry per device |
| Recent accurate audit | latest `accurate_audit_events` |
| Recent incidents | latest open incidents |
| Recent alerts | latest open alerts |
| Mini chart | telemetry/audit/alert aggregation |

### 6.4 Wireframe Desktop

```text
+--------------------------------------------------------------------------------+
| Dashboard                                                                      |
| Ringkasan kondisi device Accurate, koneksi Firebird, audit, dan incident.       |
|                                                                                |
| +------------------+ +------------------+ +------------------+ +--------------+ |
| | Device Online    | | Firebird OK      | | Accurate Active  | | Open Alert   | |
| | 2 / 2            | | 2 / 2 clients    | | 2 devices        | | 3            | |
| | NORMAL           | | NORMAL           | | NORMAL           | | WARNING      | |
| +------------------+ +------------------+ +------------------+ +--------------+ |
|                                                                                |
| +------------------+ +------------------+                                      |
| | Audit Today      | | Open Incidents   |                                      |
| | 35 events        | | 1 incident       |                                      |
| | INFO             | | WARNING          |                                      |
| +------------------+ +------------------+                                      |
|                                                                                |
| +--------------------------------------------------------------------------------+
| | Device Health                                                                 |
| | Device Label | User Windows | CPU | RAM | Firebird | Accurate | Status | Action |
| | Finance 1    | DESKTOP\FIN1 | 42% | 61% | OK       | Running  | Online | Detail |
| | Finance 2    | DESKTOP\FIN2 | 87% | 82% | Slow     | Running  | Warn   | Detail |
| +--------------------------------------------------------------------------------+
|                                                                                |
| +--------------------------------------------+ +-------------------------------+ |
| | Accurate Audit Realtime                    | | Recent Incidents              | |
| | Time | User | Activity | Reference        | | Time | Target | Summary       | |
| | ...                                        | | ...                           | |
| +--------------------------------------------+ +-------------------------------+ |
|                                                                                |
| +--------------------------------------------+ +-------------------------------+ |
| | Firebird Latency Trend                     | | Alert Summary                 | |
| | Chart.js small line/bar chart              | | INFO/WARN/ERROR/CRIT          | |
| +--------------------------------------------+ +-------------------------------+ |
+--------------------------------------------------------------------------------+
```

### 6.5 Summary Cards

Dashboard card wajib:

| Card | Value | Status Logic | Click Target |
|---|---|---|---|
| Device Online | `online_devices / total_devices` | warning jika ada device offline | `/devices` |
| Firebird Connectivity | `connected_clients / total_clients` | warning/error jika ada timeout | `/devices?filter=firebird` |
| Accurate Active | jumlah device dengan `accurate.exe` running | warning jika saat jam kerja ada device tanpa Accurate | `/devices?filter=accurate` |
| Open Alerts | jumlah alert open | status tertinggi alert open | `/alerts` |
| Audit Today | jumlah audit event hari ini | info kecuali ada audit alert | `/accurate-audits` |
| Open Incidents | jumlah incident open | status tertinggi incident open | `/incidents` |

### 6.6 Device Health Table

Kolom:

```text
Device Label
Hostname
Windows User
IP ZeroTier
CPU
RAM
Firebird
Accurate
Last Seen
Status
Action
```

Action:

```text
Detail
Remote Desktop
Restart
```

Catatan action:

1. Tombol `Remote Desktop` boleh muncul langsung di row.
2. Tombol `Restart` harus membuka confirmation modal.
3. Kalau device offline, tombol remote/restart disabled atau diberi status `Unavailable`.

### 6.7 Accurate Audit Realtime Panel

Kolom ringkas:

```text
Time
Accurate User
Activity
Reference
Status
```

Data yang tampil maksimal 5–10 event terbaru.

Tidak perlu menampilkan semua kolom audit di dashboard. Detail lengkap ada di halaman Accurate Audit.

### 6.8 Recent Incidents Panel

Kolom:

```text
Time
Target
Incident Type
Severity
Summary
Action
```

Action:

```text
View Detail
Acknowledge
```

### 6.9 Chart Dashboard

Chart boleh ada, tetapi tidak dominan.

Chart yang disarankan:

1. Firebird latency trend per device.
2. Alert summary berdasarkan severity.
3. Audit event count per hour.

Chart yang tidak perlu:

1. Grafik raw log terlalu besar.
2. Doughnut chart berlebihan.
3. Chart dekoratif tanpa keputusan operasional.

### 6.10 Empty State Dashboard

Jika belum ada device:

```text
Belum ada device terdaftar.
Pastikan Windows Agent sudah berjalan pada laptop client dan dapat mengirim telemetry ke RSyslog Server.
```

Jika belum ada audit:

```text
Belum ada audit Accurate yang tersinkronisasi.
Periksa konfigurasi Firebird Audit Reader pada Settings.
```

---

## 7. Halaman Devices

### 7.1 Tujuan

Halaman Devices menampilkan semua laptop Windows yang dimonitor.

Halaman ini digunakan admin untuk:

1. Melihat status semua client.
2. Melihat user Windows yang aktif.
3. Melihat koneksi Firebird dari tiap device.
4. Melihat performa device.
5. Membuka detail device.
6. Melakukan Remote Desktop.
7. Melakukan Remote Restart manual.

### 7.2 Route

```text
GET /devices
```

### 7.3 Wireframe

```text
+--------------------------------------------------------------------------------+
| Devices                                                     [Refresh]           |
| Daftar laptop Windows pengguna Accurate 5 yang dimonitor.                       |
|                                                                                |
| +------------------------------------------------------------------------------+
| | Filter                                                                       |
| | Search | Status | Firebird | Accurate | Agent | [Apply] [Reset]             |
| +------------------------------------------------------------------------------+
|                                                                                |
| +------------------------------------------------------------------------------+
| | Device | Hostname | User | IP ZeroTier | CPU | RAM | Firebird | Accurate |  |
| | Finance 1 | DESKTOP-A | DESKTOP-A\FIN1 | 10.x.x.11 | 42% | 61% | OK | Run | |
| | Finance 2 | DESKTOP-B | DESKTOP-B\FIN2 | 10.x.x.12 | 87% | 82% | Slow | Run |
| +------------------------------------------------------------------------------+
|                                                                                |
| Pagination                                                                     |
+--------------------------------------------------------------------------------+
```

### 7.4 Filter

| Filter | Type | Keterangan |
|---|---|---|
| Search | Text | Search label, hostname, user, IP |
| Status | Select | online, warning, offline, unknown |
| Agent Status | Select | active, inactive, outdated |
| Firebird Status | Select | connected, slow, timeout, refused |
| Accurate Status | Select | running, not_running, unknown |
| CPU Status | Select | normal, warning, critical |

### 7.5 Tabel Devices

Kolom:

| Kolom | Sumber | Keterangan |
|---|---|---|
| Device Label | `devices.device_label` | Label yang bisa diubah admin |
| Hostname | `devices.hostname` | Hostname asli Windows |
| Windows User | latest telemetry | User Windows aktif |
| IP ZeroTier | `devices.ip_zerotier` | IP private ZeroTier |
| Agent | `devices.agent_status` | Status agent |
| CPU | latest telemetry | CPU terakhir |
| RAM | latest telemetry | RAM terakhir |
| Firebird | latest network check | connected/slow/timeout |
| Accurate | latest process snapshot | running/not running |
| Last Seen | `devices.last_seen_at` | Waktu heartbeat terakhir |
| Status | computed | online/warning/offline |
| Actions | UI | Detail, RDP, Restart |

### 7.6 Row Status

| Kondisi | UI |
|---|---|
| Online normal | Badge NORMAL/ONLINE |
| CPU/RAM tinggi | Badge WARNING |
| Firebird timeout | Badge ERROR |
| Agent tidak heartbeat | Badge OFFLINE |
| Unknown new device | Badge UNKNOWN |

### 7.7 Actions

| Action | Behavior |
|---|---|
| Detail | Buka `/devices/{device}` |
| Remote Desktop | Generate/open RDP link/file atau tampilkan command `mstsc /v:<ip>` |
| Restart Client | Buka confirmation modal dan buat remote action |
| Ping Test | Opsional, buat remote action `PING_TEST` atau jalankan server-side check jika memungkinkan |

### 7.8 Empty State

```text
Belum ada device yang mengirim heartbeat.
Install dan jalankan Windows Agent pada laptop client, lalu pastikan koneksi ZeroTier dan RSyslog Server aktif.
```

---

## 8. Halaman Device Detail

### 8.1 Tujuan

Halaman Device Detail adalah pusat investigasi satu laptop Windows.

Halaman ini harus membantu admin menjawab:

```text
Device ini siapa?
User Windows aktif siapa?
Agent masih jalan?
Bisa konek ke Firebird?
Accurate.exe berjalan?
CPU/RAM/Disk normal?
Ada alert/incident terkait?
Action apa yang bisa dilakukan?
```

### 8.2 Route

```text
GET /devices/{device}
```

### 8.3 Wireframe

```text
+--------------------------------------------------------------------------------+
| Device Detail: Laptop Finance 2                                  [Back]        |
| Hostname DESKTOP-B | Agent ID xxxx | Last Seen 20 seconds ago                  |
|                                                                                |
| +----------------------------+ +----------------------------------------------+ |
| | Device Identity            | | Quick Actions                                | |
| | Label: Laptop Finance 2    | | [Remote Desktop] [Restart Client] [Ping]    | |
| | Hostname: DESKTOP-B        | | [View Logs]                                  | |
| | User: DESKTOP-B\FIN2       | +----------------------------------------------+ |
| | IP ZeroTier: 10.x.x.12     |                                                  |
| +----------------------------+                                                  |
|                                                                                |
| +----------------+ +----------------+ +----------------+ +------------------+   |
| | CPU 87% Warn   | | RAM 82% Warn   | | Disk 55% Normal| | Accurate Running |   |
| +----------------+ +----------------+ +----------------+ +------------------+   |
|                                                                                |
| +------------------------------------+ +---------------------------------------+ |
| | Firebird Connectivity              | | Accurate Process                      | |
| | Target: VPS:3051                   | | Process: accurate.exe                 | |
| | Status: Slow                       | | Owner: DESKTOP-B\FIN2                 | |
| | Latency: 650ms                     | | Path: C:\...\accurate.exe            | |
| +------------------------------------+ +---------------------------------------+ |
|                                                                                |
| +------------------------------------+ +---------------------------------------+ |
| | Recent Alerts                      | | Recent Remote Actions                 | |
| | ...                                | | ...                                   | |
| +------------------------------------+ +---------------------------------------+ |
|                                                                                |
| +--------------------------------------------------------------------------------+
| | Telemetry History                                                              |
| | Chart CPU/RAM/Firebird Latency                                                 |
| +--------------------------------------------------------------------------------+
```

### 8.4 Section: Device Identity

Field:

```text
Device Label
Hostname
Agent ID masked
Windows User
IP ZeroTier
OS Version
Agent Version
Last Seen
Last Boot Time
RDP Status
```

Catatan:

- `agent_id` jangan ditampilkan full jika sensitif; boleh masked.
- Device label bisa diedit oleh admin.

### 8.5 Section: Quick Actions

Tombol:

```text
Remote Desktop
Restart Client
Ping Test
View Advanced Logs
```

#### Remote Desktop Button

Behavior:

1. Jika IP ZeroTier tersedia dan RDP status available:
   - tampilkan modal dengan command:

```text
mstsc /v:<ip_zerotier>
```

   - atau generate file `.rdp`.
2. Catat action `OPEN_RDP` ke `remote_actions`.
3. Tidak perlu mengeksekusi RDP dari server.

#### Restart Client Button

Behavior:

1. Buka modal konfirmasi.
2. Admin wajib isi alasan.
3. Admin klik confirm.
4. Laravel membuat `remote_actions` dengan status `pending`.
5. Windows Agent polling API dan menjalankan restart.
6. Status berubah menjadi `executed` atau `failed`.

### 8.6 Restart Confirmation Modal

```text
+------------------------------------------------------+
| Confirm Restart Client                               |
|                                                      |
| Target Device : Laptop Finance 2                     |
| Hostname      : DESKTOP-B                            |
| User Active   : DESKTOP-B\FIN2                       |
| IP ZeroTier   : 10.x.x.12                            |
|                                                      |
| This action will restart the Windows client device.   |
| The action is manual, logged, and cannot be undone.   |
|                                                      |
| Reason: [______________________________________]      |
|                                                      |
| [Cancel]                              [Confirm]      |
+------------------------------------------------------+
```

Validation:

| Kondisi | Response |
|---|---|
| Reason kosong | Tombol confirm disabled atau tampil error |
| Device offline | Action tidak boleh dibuat atau diberi warning kuat |
| Admin confirm | Action tercatat di `remote_actions` |

### 8.7 Section: Telemetry Cards

Card:

```text
CPU
RAM
Disk
Uptime
Accurate Process
Firebird Connection
```

### 8.8 Section: Firebird Connectivity

Field:

```text
Target host
Target port 3051
Status
Latency ms
Last checked
Failure count
Evidence
```

Contoh evidence:

```text
tcp_connect_status=timeout, attempts=3, target=10.x.x.1:3051
```

### 8.9 Section: Accurate Process

Field:

```text
process_name = accurate.exe
status = running / not_running
owner_user
process_id
path
last_detected_at
```

### 8.10 Section: Recent Alerts

Menampilkan alert terkait device ini saja.

Kolom:

```text
Time
Severity
Title
Evidence Summary
Status
Action
```

### 8.11 Section: Recent Remote Actions

Kolom:

```text
Time
Admin
Action
Status
Reason
Result
```

---

## 9. Halaman Accurate Audit

### 9.1 Tujuan

Halaman Accurate Audit menampilkan audit trail aktivitas/perubahan data Accurate dari database Firebird.

Sumber data adalah **Accurate Audit Reader** yang membaca Firebird secara read-only, bukan RSyslog.

### 9.2 Route

```text
GET /accurate-audits
GET /accurate-audits/{audit}
```

### 9.3 Wireframe Index

```text
+--------------------------------------------------------------------------------+
| Accurate Audit                                                [Sync Now]        |
| Audit trail aktivitas Accurate dari Firebird AUDIT + USERS.                    |
|                                                                                |
| +------------------------------------------------------------------------------+
| | Filter                                                                       |
| | Date From | Date To | User | Source | Type | Keyword | [Apply] [Reset]       |
| +------------------------------------------------------------------------------+
|                                                                                |
| +------------------------------------------------------------------------------+
| | Time | Accurate User | Full Name | Source | Type | Reference | Description  |
| | 14:31| FINANCE01     | Finance 1 | SI     | UPDATE | SI-001    | ...         |
| +------------------------------------------------------------------------------+
|                                                                                |
| Pagination                                                                     |
+--------------------------------------------------------------------------------+
```

### 9.4 Summary Cards

Optional di bagian atas:

```text
Audit Events Today
Users Active Today
Update Events
Delete Events (jika field tersedia)
Last Sync Status
```

### 9.5 Filter

| Filter | Type | Keterangan |
|---|---|---|
| Date From | Date/time | waktu awal |
| Date To | Date/time | waktu akhir |
| Accurate User | Select | dari `accurate_username` |
| Source/Module | Select | dari field source/module |
| Transaction Type | Select | insert/update/delete/other jika tersedia |
| Reference | Text | invoice/ref no |
| Keyword | Text | search description |
| Severity/Flag | Select | optional jika audit event di-flag |

### 9.6 Tabel Accurate Audit

Kolom:

| Kolom | Keterangan |
|---|---|
| Activity Time | waktu dari AUDIT |
| Accurate User | username internal Accurate |
| Full Name | dari USERS jika tersedia |
| Source / Module | modul/sumber aktivitas |
| Transaction Type | tipe aktivitas jika tersedia |
| Reference No | invoice/ref jika tersedia |
| Description | deskripsi aktivitas |
| App Version | optional |
| Status | normal/flagged |
| Action | Detail |

### 9.7 Detail Accurate Audit

Wireframe:

```text
+--------------------------------------------------------------------------------+
| Accurate Audit Detail                                             [Back]        |
|                                                                                |
| +-----------------------------+------------------------------------------------+|
| | Activity Time               | 2026-05-28 14:31:00                            ||
| | Accurate User               | FINANCE01                                      ||
| | Full Name                   | Finance User 1                                 ||
| | Source / Module             | Sales Invoice                                  ||
| | Transaction Type            | UPDATE                                         ||
| | Reference No                | SI-001                                         ||
| | App Version                 | Accurate 5                                     ||
| +-----------------------------+------------------------------------------------+|
|                                                                                |
| Description                                                                    |
| +------------------------------------------------------------------------------+|
| | Perubahan transaksi penjualan ...                                             ||
| +------------------------------------------------------------------------------+|
|                                                                                |
| Raw Field Snapshot                                                              |
| +------------------------------------------------------------------------------+|
| | JSON / table key-value from source audit fields                               ||
| +------------------------------------------------------------------------------+|
+--------------------------------------------------------------------------------+
```

### 9.8 Empty State

```text
Belum ada data audit Accurate yang tersinkronisasi.
Pastikan konfigurasi Firebird host, database path, username read-only, dan sync schedule sudah benar.
```

### 9.9 Larangan

Codex tidak boleh:

1. Mengambil audit dari tabel `LOGIN` sebagai sumber utama.
2. Menganggap `COMP_NAME` dan `IPADDRESS` selalu tersedia.
3. Menampilkan IP/komputer sebagai fakta jika field kosong.
4. Menyambungkan username Accurate dari TCP process.
5. Membuat spike detection tanpa rule dan threshold jelas.

---

## 10. Halaman Incidents

### 10.1 Tujuan

Halaman Incidents menampilkan masalah operasional yang sudah dikorelasikan dari event/alert.

Incident berbeda dari alert.

```text
Alert    = peringatan spesifik dari satu kondisi.
Incident = rangkuman masalah operasional dari satu atau beberapa evidence.
```

### 10.2 Route

```text
GET /incidents
GET /incidents/{incident}
```

### 10.3 Wireframe Index

```text
+--------------------------------------------------------------------------------+
| Incidents                                                                      |
| Masalah operasional yang membutuhkan perhatian admin.                           |
|                                                                                |
| +------------------------------------------------------------------------------+
| | Filter                                                                       |
| | Status | Severity | Type | Target | Date | [Apply] [Reset]                  |
| +------------------------------------------------------------------------------+
|                                                                                |
| +------------------------------------------------------------------------------+
| | Time | Target | Type | Severity | Summary | Status | Action                 |
| | ...                                                                          |
| +------------------------------------------------------------------------------+
+--------------------------------------------------------------------------------+
```

### 10.4 Incident Types

| Type | Target | Contoh |
|---|---|---|
| DEVICE_SLOW | Device | Device lambat karena CPU/RAM tinggi + latency Firebird tinggi |
| DEVICE_OFFLINE | Device | Agent tidak heartbeat > threshold |
| FIREBIRD_CONNECTIVITY_PROBLEM | Device → VPS | Client gagal konek ke Firebird |
| FIREBIRD_SERVICE_DOWN | VPS | Service Firebird tidak aktif |
| ACCURATE_PROCESS_PROBLEM | Device | Accurate.exe tidak berjalan saat jam kerja |
| REMOTE_ACTION_FAILED | Device | Restart command gagal dieksekusi |

### 10.5 Tabel Incidents

Kolom:

```text
Detected At
Target
Incident Type
Severity
Summary
Evidence Count
Status
Action
```

Status:

```text
Open
Acknowledged
Resolved
```

Action:

```text
View Detail
Acknowledge
Resolve
Remote Desktop
Restart Client
```

### 10.6 Detail Incident

Wireframe:

```text
+--------------------------------------------------------------------------------+
| Incident Detail: Device terindikasi lambat                        [Back]        |
|                                                                                |
| +-----------------------------+------------------------------------------------+|
| | Target                      | Laptop Finance 2                               ||
| | Severity                    | WARNING                                        ||
| | Status                      | Open                                           ||
| | Detected At                 | 2026-05-28 14:31                               ||
| | Recommended Action          | Remote Desktop untuk pemeriksaan aplikasi      ||
| +-----------------------------+------------------------------------------------+|
|                                                                                |
| Evidence                                                                       |
| +------------------------------------------------------------------------------+|
| | CPU 87% selama 5 menit                                                        ||
| | RAM 82%                                                                       ||
| | Firebird latency 650ms                                                        ||
| +------------------------------------------------------------------------------+|
|                                                                                |
| Related Alerts                                                                 |
| +------------------------------------------------------------------------------+|
| | Time | Alert | Severity | Status                                             ||
| +------------------------------------------------------------------------------+|
|                                                                                |
| Actions                                                                        |
| [Acknowledge] [Resolve] [Remote Desktop] [Restart Client]                       |
+--------------------------------------------------------------------------------+
```

### 10.7 Empty State

```text
Tidak ada incident aktif.
Semua device dan service berada dalam kondisi normal berdasarkan telemetry terakhir.
```

---

## 11. Halaman Alerts

### 11.1 Tujuan

Halaman Alerts menampilkan peringatan kontekstual yang punya target dan evidence.

Alert tidak boleh berupa kalimat generik seperti:

```text
Firebird unreachable
Accurate process not running
Audit spike detected
```

Alert harus jelas:

```text
Target: siapa
Masalah: apa
Detected by: komponen mana
Evidence: bukti apa
Impact: dampaknya apa
Recommended action: apa yang disarankan
```

### 11.2 Route

```text
GET /alerts
GET /alerts/{alert}
POST /alerts/{alert}/acknowledge
POST /alerts/{alert}/resolve
```

### 11.3 Wireframe Index

```text
+--------------------------------------------------------------------------------+
| Alerts                                                                         |
| Peringatan kontekstual berbasis telemetry dan audit.                            |
|                                                                                |
| +------------------------------------------------------------------------------+
| | Filter                                                                       |
| | Status | Severity | Category | Target | Date | [Apply] [Reset]              |
| +------------------------------------------------------------------------------+
|                                                                                |
| +------------------------------------------------------------------------------+
| | Time | Severity | Target | Title | Evidence Summary | Status | Action       |
| | ...                                                                          |
| +------------------------------------------------------------------------------+
+--------------------------------------------------------------------------------+
```

### 11.4 Tabel Alerts

Kolom:

```text
Detected At
Severity
Target
Category
Title
Evidence Summary
Notification
Status
Action
```

Severity badge wajib konsisten:

```text
INFO
WARNING
ERROR
CRITICAL
```

### 11.5 Contoh Alert yang Benar

```text
WARNING - CPU tinggi pada Laptop Finance 2
Target: Laptop Finance 2
Evidence: CPU 87% selama 5 menit
```

```text
ERROR - Laptop Finance 1 gagal terhubung ke Firebird VPS:3051
Target: Laptop Finance 1 → VPS-FIREBIRD
Evidence: tcp_connect timeout 3 kali
```

```text
CRITICAL - Service Firebird pada VPS tidak aktif
Target: VPS-FIREBIRD
Evidence: service_status=inactive, port_3051=closed
```

### 11.6 Detail Alert

Wireframe:

```text
+--------------------------------------------------------------------------------+
| Alert Detail                                                      [Back]        |
|                                                                                |
| +-----------------------------+------------------------------------------------+|
| | Title                       | CPU tinggi pada Laptop Finance 2               ||
| | Severity                    | WARNING                                        ||
| | Target                      | Laptop Finance 2                               ||
| | Category                    | PERFORMANCE                                    ||
| | Detected By                 | Windows Agent                                  ||
| | Status                      | Open                                           ||
| | Detected At                 | 2026-05-28 14:31                               ||
| +-----------------------------+------------------------------------------------+|
|                                                                                |
| Evidence                                                                       |
| +------------------------------------------------------------------------------+|
| | cpu_percent=87, duration_minutes=5, threshold=80                              ||
| +------------------------------------------------------------------------------+|
|                                                                                |
| Impact                                                                         |
| +------------------------------------------------------------------------------+|
| | Device berpotensi lambat saat menjalankan Accurate 5.                         ||
| +------------------------------------------------------------------------------+|
|                                                                                |
| Recommended Action                                                             |
| +------------------------------------------------------------------------------+|
| | Gunakan Remote Desktop untuk memeriksa aplikasi berjalan.                     ||
| +------------------------------------------------------------------------------+|
|                                                                                |
| Notification History                                                           |
| +------------------------------------------------------------------------------+|
| | Telegram | sent | 2026-05-28 14:31                                           ||
| +------------------------------------------------------------------------------+|
|                                                                                |
| Actions                                                                        |
| [Acknowledge] [Resolve] [Remote Desktop] [Restart Client]                       |
+--------------------------------------------------------------------------------+
```

### 11.7 Empty State

```text
Tidak ada alert aktif.
Sistem belum mendeteksi kondisi warning, error, atau critical pada device dan service yang dimonitor.
```

---

## 12. Halaman Remote Actions

### 12.1 Tujuan

Halaman Remote Actions menampilkan riwayat semua tindakan manual admin terhadap device.

Remote action mencakup:

```text
OPEN_RDP
RESTART_CLIENT
PING_TEST
RESTART_AGENT
```

### 12.2 Route

```text
GET /remote-actions
POST /remote-actions
GET /remote-actions/{remoteAction}
```

### 12.3 Wireframe Index

```text
+--------------------------------------------------------------------------------+
| Remote Actions                                                                 |
| Riwayat tindakan remote manual oleh administrator.                              |
|                                                                                |
| +------------------------------------------------------------------------------+
| | Filter                                                                       |
| | Action Type | Status | Device | Admin | Date | [Apply] [Reset]              |
| +------------------------------------------------------------------------------+
|                                                                                |
| +------------------------------------------------------------------------------+
| | Time | Admin | Target Device | Action | Status | Reason | Result | Detail   |
| | ...                                                                          |
| +------------------------------------------------------------------------------+
+--------------------------------------------------------------------------------+
```

### 12.4 Tabel Remote Actions

Kolom:

```text
Requested At
Admin
Target Device
Action Type
Status
Reason
Executed At
Result Summary
Action
```

Status:

```text
pending
picked_up
executed
failed
cancelled
expired
```

### 12.5 Detail Remote Action

Wireframe:

```text
+--------------------------------------------------------------------------------+
| Remote Action Detail                                              [Back]        |
|                                                                                |
| +-----------------------------+------------------------------------------------+|
| | Action Type                 | RESTART_CLIENT                                 ||
| | Target Device               | Laptop Finance 2                               ||
| | Requested By                | Administrator                                  ||
| | Status                      | executed                                       ||
| | Requested At                | 2026-05-28 14:31                               ||
| | Picked Up At                | 2026-05-28 14:31:10                            ||
| | Executed At                 | 2026-05-28 14:31:15                            ||
| +-----------------------------+------------------------------------------------+|
|                                                                                |
| Reason                                                                         |
| +------------------------------------------------------------------------------+|
| | Device lambat dan perlu restart setelah pemeriksaan admin.                    ||
| +------------------------------------------------------------------------------+|
|                                                                                |
| Result Message                                                                 |
| +------------------------------------------------------------------------------+|
| | Restart command accepted by Windows Agent.                                    ||
| +------------------------------------------------------------------------------+|
+--------------------------------------------------------------------------------+
```

### 12.6 Empty State

```text
Belum ada tindakan remote yang dilakukan.
Remote Desktop dan Restart Client yang dilakukan admin akan tercatat di halaman ini.
```

---

## 13. Halaman Advanced Logs

### 13.1 Tujuan

Advanced Logs adalah tempat raw/parsed logs untuk investigasi teknis.

Halaman ini bukan dashboard utama.

### 13.2 Route

```text
GET /advanced-logs
GET /advanced-logs/{log}
```

### 13.3 Wireframe Index

```text
+--------------------------------------------------------------------------------+
| Advanced Logs                                                                  |
| Raw dan parsed logs dari RSyslog untuk investigasi teknis.                      |
|                                                                                |
| +------------------------------------------------------------------------------+
| | Filter                                                                       |
| | Date | Hostname | Source | Category | Severity | Keyword | [Apply] [Reset]   |
| +------------------------------------------------------------------------------+
|                                                                                |
| +------------------------------------------------------------------------------+
| | Time | Hostname | Source | Category | Severity | Parsed Message | Detail    |
| | ...                                                                          |
| +------------------------------------------------------------------------------+
+--------------------------------------------------------------------------------+
```

### 13.4 Tabel Advanced Logs

Kolom:

```text
Logged At
Hostname
Source / Tag
Category
Severity
Parsed Message
Raw Message Preview
Action
```

### 13.5 Detail Advanced Log

Field:

```text
ID
Logged At
Hostname
IP Address
Source / Tag
Category
Severity
Raw Message
Parsed Message
Source File
Hash
Created At
Related Alert
```

### 13.6 Larangan

Codex tidak boleh:

1. Menampilkan raw log sebagai tabel utama dashboard.
2. Menampilkan hash/source file di dashboard utama.
3. Membuat Advanced Logs menjadi halaman pertama setelah login.

---

## 14. Halaman Settings

### 14.1 Tujuan

Settings digunakan untuk konfigurasi sistem monitoring.

### 14.2 Route

```text
GET /settings
POST /settings
```

### 14.3 Wireframe

```text
+--------------------------------------------------------------------------------+
| Settings                                                                       |
| Konfigurasi threshold, Telegram, Firebird, Agent, dan Remote Actions.           |
|                                                                                |
| [General] [Agent] [Threshold] [Firebird] [Telegram] [Remote Action]             |
|                                                                                |
| +------------------------------------------------------------------------------+
| | Form content based on selected tab                                           |
| +------------------------------------------------------------------------------+
|                                                                                |
| [Save Settings]                                                                |
+--------------------------------------------------------------------------------+
```

### 14.4 Tabs

#### General

Field:

```text
App Name
Timezone
Dashboard refresh interval
```

#### Agent

Field:

```text
Heartbeat interval seconds
Agent offline warning minutes
Agent offline critical minutes
Allowed agent version
```

#### Threshold

Field:

```text
CPU warning threshold
CPU critical threshold
RAM warning threshold
Disk warning threshold
Firebird latency warning
Firebird latency critical
```

#### Firebird

Field:

```text
Firebird host
Firebird port
Database path
Read-only username
Read-only password
Sync interval
Last sync status
```

Password handling:

- Jangan tampilkan password asli.
- Jika field kosong saat update, password lama tetap dipakai.
- Tampilkan masked value.

#### Telegram

Field:

```text
Telegram enabled
Bot token
Chat ID
Minimum severity to notify
Cooldown minutes
Test Telegram button
```

#### Remote Action

Field:

```text
Remote restart enabled
Require restart reason
Command expiry minutes
Allow restart only when agent online
```

### 14.5 Validation

| Field | Validation |
|---|---|
| Threshold numeric | harus angka |
| Telegram token | required jika enabled |
| Firebird host | required |
| Firebird port | numeric |
| Restart reason | required jika remote restart enabled |

### 14.6 Empty/Disabled State

Jika Telegram disabled:

```text
Telegram alert sedang nonaktif. Alert tetap tersimpan di dashboard, tetapi tidak dikirim ke Telegram.
```

Jika Firebird belum dikonfigurasi:

```text
Firebird Audit Reader belum dikonfigurasi. Isi host, port, database path, dan user read-only untuk mulai sinkronisasi audit.
```

---

## 15. Mobile/Responsive Behavior

Walaupun target utama laptop/desktop, UI harus tetap usable di layar lebih kecil.

### 15.1 Desktop

- Sidebar fixed.
- Tabel full width.
- Card grid 4 columns jika ruang cukup.
- Detail page dua kolom.

### 15.2 Tablet

- Sidebar collapsible.
- Card grid 2 columns.
- Tabel horizontal scroll.
- Detail page satu/dua kolom tergantung lebar.

### 15.3 Mobile

- Sidebar hidden behind menu.
- Card satu kolom.
- Tabel scroll horizontal.
- Action button dipindahkan ke dropdown atau stacked buttons.

---

## 16. Badge, Status, dan Empty State

### 16.1 Status Badge

| Status | Label |
|---|---|
| Normal | NORMAL |
| Online | ONLINE |
| Warning | WARNING |
| Error | ERROR |
| Critical | CRITICAL |
| Offline | OFFLINE |
| Unknown | UNKNOWN |
| Pending | PENDING |
| Executed | EXECUTED |
| Failed | FAILED |

### 16.2 Severity Badge

| Severity | Keterangan |
|---|---|
| INFO | Aktivitas normal |
| WARNING | Perlu perhatian |
| ERROR | Gangguan nyata |
| CRITICAL | Gangguan serius |

### 16.3 Empty State Pattern

Semua empty state harus menjelaskan:

1. Data apa yang belum ada.
2. Kenapa kemungkinan belum ada.
3. Tindakan yang bisa dilakukan admin.

Contoh:

```text
Belum ada data telemetry.
Pastikan Windows Agent berjalan dan dapat mengirim log ke RSyslog Server melalui jaringan ZeroTier.
```

---

## 17. Component Recommendations

### 17.1 Blade Layout Files

```text
resources/views/layouts/app.blade.php
resources/views/layouts/guest.blade.php
```

### 17.2 Blade Components

```text
resources/views/components/sidebar.blade.php
resources/views/components/topbar.blade.php
resources/views/components/page-header.blade.php
resources/views/components/status-card.blade.php
resources/views/components/severity-badge.blade.php
resources/views/components/status-badge.blade.php
resources/views/components/data-table.blade.php
resources/views/components/filter-panel.blade.php
resources/views/components/empty-state.blade.php
resources/views/components/confirm-modal.blade.php
resources/views/components/remote-action-modal.blade.php
resources/views/components/evidence-list.blade.php
```

### 17.3 Page Views

```text
resources/views/auth/login.blade.php
resources/views/dashboard/index.blade.php
resources/views/devices/index.blade.php
resources/views/devices/show.blade.php
resources/views/accurate-audits/index.blade.php
resources/views/accurate-audits/show.blade.php
resources/views/incidents/index.blade.php
resources/views/incidents/show.blade.php
resources/views/alerts/index.blade.php
resources/views/alerts/show.blade.php
resources/views/remote-actions/index.blade.php
resources/views/remote-actions/show.blade.php
resources/views/advanced-logs/index.blade.php
resources/views/advanced-logs/show.blade.php
resources/views/settings/index.blade.php
```

---

## 18. Page Priority for Implementation

Codex harus membangun UI bertahap dengan urutan berikut:

```text
1. Layout global + login
2. Dashboard
3. Devices index
4. Device detail
5. Alerts
6. Remote Actions
7. Advanced Logs
8. Accurate Audit
9. Incidents
10. Settings
```

Alasan:

1. Layout dan login adalah fondasi.
2. Dashboard dan Devices membuktikan real-device monitoring.
3. Alerts dan Remote Actions mendukung tindakan admin.
4. Advanced Logs menjaga traceability.
5. Accurate Audit dan Incidents menambahkan novelty dan kedalaman sistem.
6. Settings bisa bertahap setelah struktur data stabil.

---

## 19. Hal yang Tidak Boleh Dilakukan Codex

Codex tidak boleh:

1. Mengubah stack UI ke React/Vue/Next.
2. Menggunakan template AdminLTE mentah.
3. Membuat dashboard utama sebagai raw log viewer.
4. Menampilkan data random/demo sebagai asumsi utama.
5. Meng-hardcode device seperti `WIN-ACC-01` di logic program.
6. Membuat alert tanpa target dan evidence.
7. Menampilkan `Firebird unreachable` tanpa menjelaskan target dan sumber deteksi.
8. Membuat audit spike detection tanpa threshold dan rule jelas.
9. Membuat tombol restart tanpa confirmation modal.
10. Menjalankan restart otomatis tanpa admin.
11. Menampilkan password Firebird atau Telegram token plaintext.
12. Menganggap `LOGIN` sebagai sumber utama audit Accurate.
13. Menganggap `COMP_NAME` dan `IPADDRESS` audit selalu tersedia.
14. Membuat UI terlalu dekoratif seperti SaaS landing page.
15. Membuat chart terlalu dominan dibanding status operasional.

---

## 20. Acceptance Criteria UI

UI dianggap sesuai jika memenuhi kriteria berikut:

| ID | Acceptance Criteria |
|---|---|
| UI-001 | Admin dapat login dan diarahkan ke `/dashboard` |
| UI-002 | Sidebar menampilkan menu final v2 |
| UI-003 | Dashboard menampilkan summary device, Firebird, Accurate, audit, alert, incident |
| UI-004 | Dashboard tidak menampilkan raw log sebagai elemen dominan |
| UI-005 | Devices page menampilkan device real berdasarkan database, bukan hardcode |
| UI-006 | Device detail menampilkan identity, telemetry, Firebird, Accurate process, alerts, actions |
| UI-007 | Remote Restart selalu menggunakan confirmation modal dan reason wajib |
| UI-008 | Accurate Audit page menampilkan audit trail dan filter |
| UI-009 | Alerts page menampilkan target, evidence, impact/recommended action pada detail |
| UI-010 | Incidents page membedakan incident dari alert |
| UI-011 | Remote Actions page mencatat semua action admin |
| UI-012 | Advanced Logs menjadi halaman teknis, bukan dashboard utama |
| UI-013 | Settings memiliki tab konfigurasi utama |
| UI-014 | Badge severity/status konsisten di semua halaman |
| UI-015 | Empty state informatif dan tidak membingungkan |
| UI-016 | UI responsive minimal untuk desktop dan tablet |

---

## 21. Ringkasan Final Wireframe

Menu final:

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

Fokus dashboard:

```text
Device online
Windows user aktif
Koneksi Firebird
Accurate process
Audit Accurate terbaru
Incident/alert yang actionable
```

Raw log:

```text
Hanya di Advanced Logs.
```

Remote action:

```text
Selalu manual, selalu dikonfirmasi, selalu dicatat.
```

Alert:

```text
Harus punya target, evidence, impact, dan recommended action.
```

Accurate audit:

```text
Dibaca dari Firebird AUDIT + USERS melalui Accurate Audit Reader, bukan dari RSyslog.
```

---

## 22. Catatan untuk Codex

Saat mengimplementasikan UI:

1. Mulai dari layout global dan komponen reusable.
2. Jangan langsung membuat semua halaman dengan HTML duplikat.
3. Buat component badge, card, table, modal, dan empty state.
4. Gunakan pagination untuk semua tabel besar.
5. Gunakan filter GET query agar URL dapat dibagikan.
6. Gunakan Alpine.js untuk modal restart dan dropdown.
7. Gunakan Chart.js hanya untuk grafik ringkas.
8. Semua halaman harus tetap bisa tampil meskipun database belum punya data.
9. Semua action berbahaya harus POST dan CSRF protected.
10. Jangan membuat endpoint/action yang belum dijelaskan dalam SRS/database design.
