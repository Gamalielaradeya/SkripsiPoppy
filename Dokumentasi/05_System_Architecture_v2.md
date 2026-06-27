# 05 — System Architecture v2
# Centralized Log Monitoring Dashboard

**Versi:** 2.0  
**Status:** Draft Final untuk implementasi awal dengan Codex / AI Agent  
**Nama Sistem:** Centralized Log Monitoring Dashboard  
**Jenis Dokumen:** System Architecture Document  
**Target Lingkungan:** Real-device monitoring untuk 2 laptop Windows pengguna Accurate 5 + 1 VPS Linux  
**Stack Utama:** Laravel, Blade, Tailwind CSS, MySQL, RSyslog, Windows Agent, Firebird 2.5, ZeroTier, Telegram Bot API  
**Dokumen Acuan:**  
1. `01_PRD_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md`  
2. `02_Accurate_Firebird_POC_Findings_v2.md`  
3. `03_SRS_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md`  
4. `04_Database_Design_v2.md`

---

# 1. Tujuan Dokumen

Dokumen ini menjelaskan arsitektur sistem **Centralized Log Monitoring Dashboard** versi real-device.

Dokumen ini dibuat agar Codex / AI Agent / developer memahami desain sistem secara menyeluruh sebelum masuk ke implementasi kode. Fokus utama dokumen ini adalah menjelaskan hubungan antar komponen, alur data, batasan teknis, jalur komunikasi, tanggung jawab setiap service, serta keputusan arsitektur yang wajib dipatuhi.

Dokumen ini juga memastikan implementasi tidak kembali menjadi dashboard raw log acak seperti versi lama, melainkan menjadi dashboard monitoring IT yang relevan untuk lingkungan PT. XYZ skala kecil dengan dua laptop Windows pengguna Accurate 5 dan satu VPS Linux sebagai pusat monitoring serta server database.

Dokumen ini mencakup:

1. Gambaran arsitektur real-device.
2. Komponen utama sistem.
3. Topologi jaringan VPS + ZeroTier.
4. Peran Windows Agent pada setiap laptop.
5. Peran RSyslog sebagai log/status collector.
6. Peran Laravel sebagai dashboard, parser, alert engine, audit reader, dan remote command API.
7. Integrasi Accurate Firebird Audit Reader.
8. Integrasi Telegram contextual alert.
9. Alur Remote Desktop dan Remote Restart manual.
10. Alur penyimpanan data ke MySQL.
11. Boundary antara monitoring, audit, alert, incident, dan remote action.
12. Security consideration dan acceptance criteria arsitektur.

---

# 2. Ringkasan Arsitektur

Sistem terdiri dari tiga kelompok besar:

1. **Windows Client Layer**  
   Dua laptop Windows yang menjalankan Accurate 5 dan Windows Monitoring Agent.

2. **Monitoring Server Layer**  
   VPS Linux yang menjalankan RSyslog Server, Laravel Dashboard, MySQL, Firebird Server, Accurate Audit Reader, Telegram Alert Service, dan Remote Command API.

3. **Administrator Layer**  
   Browser admin dan Telegram sebagai media pemantauan serta notifikasi.

Arsitektur tingkat tinggi:

```text
[Windows Laptop 1]
- Accurate 5 Client
- Windows Monitoring Agent
- ZeroTier
- RDP Enabled

[Windows Laptop 2]
- Accurate 5 Client
- Windows Monitoring Agent
- ZeroTier
- RDP Enabled

        | telemetry/status/log
        | syslog TCP/UDP through ZeroTier
        v

[VPS Linux]
- ZeroTier
- RSyslog Server
- Laravel Application
- MySQL Monitoring DB
- Firebird Server 2.5 / Accurate Database
- Accurate Audit Reader
- Telegram Alert Service
- Remote Command API

        | web dashboard
        | Telegram alert
        v

[Administrator IT]
- Browser Dashboard
- Telegram App
- Remote Desktop Client
```

Prinsip utama:

```text
Windows Agent -> RSyslog -> Laravel Parser -> MySQL -> Dashboard
Accurate Firebird -> Accurate Audit Reader -> MySQL -> Dashboard
Laravel Alert Engine -> Telegram Bot API -> Admin
Dashboard -> Remote Command API -> Windows Agent -> Windows Action
```

---

# 3. Perubahan Arsitektur dari v1 ke v2

Dokumen v1 menggunakan deployment simulasi satu laptop dengan Docker Compose. Komponen client berupa `rsyslog-client` container yang menghasilkan log simulasi.

Pada v2, target sistem berubah menjadi real-device:

| Area | v1 | v2 |
|---|---|---|
| Lingkungan | 1 laptop Docker Compose | 2 laptop Windows + 1 VPS Linux |
| Client | Container simulasi | Real laptop Windows |
| Data utama | Raw log simulasi | Device status, telemetry, Firebird connectivity, Accurate audit |
| RSyslog client | Linux container | Windows Agent pengirim syslog custom |
| Accurate | Hanya service status simulasi | Firebird database real di VPS + audit reader read-only |
| Dashboard | 4 card log + raw log | IT cockpit: devices, Accurate audit, incidents, remote actions |
| Alert | Berdasarkan severity log | Contextual alert dengan target, evidence, impact, recommended action |
| Telegram | Optional/simple alert | Wajib sebagai contextual proactive alert |
| Remote action | Tidak ada | RDP launcher dan restart manual controlled |
| Raw log | Menu utama All Logs | Advanced Logs untuk investigasi teknis |

V2 tetap mempertahankan RSyslog sebagai teknologi inti pengumpulan log/status, tetapi sumber datanya tidak lagi log random, melainkan log terstruktur yang dikirim oleh Windows Agent dan server checker.

---

# 4. Prinsip Arsitektur v2

| Prinsip | Penjelasan |
|---|---|
| Real-device first | Sistem harus memonitor laptop Windows nyata dan VPS Linux, bukan mengandalkan container simulasi sebagai fitur utama. |
| No hardcoded device | Device tidak boleh di-hardcode. Device didaftarkan otomatis berdasarkan `agent_id`, hostname, dan metadata dari agent. |
| Dashboard-oriented | Dashboard utama harus menampilkan informasi yang dibutuhkan admin IT, bukan raw log random. |
| RSyslog as collector | RSyslog bertugas menerima log/status/telemetry dari Windows Agent dan server checker. |
| Laravel as intelligence layer | Laravel bertugas parsing, menyimpan data, membuat alert, menampilkan dashboard, membaca audit Accurate, dan menyediakan remote command API. |
| Accurate audit direct-read | Accurate Audit Trail tidak lewat RSyslog, tetapi dibaca langsung secara read-only dari Firebird `AUDIT + USERS`. |
| Contextual alert | Alert harus memiliki target, source, evidence, impact, dan recommended action. |
| Telegram wajib | Telegram alert wajib sebagai notifikasi proaktif kontekstual. |
| Manual remote action | Remote Desktop dan Restart Client adalah tindakan manual admin, bukan otomatis. |
| Advanced logs only | Raw log tetap disimpan untuk investigasi, tetapi bukan fokus dashboard utama. |
| Safe iteration | Sistem boleh dikembangkan bertahap dengan GitHub, namun desain target tetap real-device. |

---

# 5. Topologi Fisik

## 5.1 Komponen Perangkat

Lingkungan implementasi real-device terdiri dari:

| Perangkat | Peran | Keterangan |
|---|---|---|
| Laptop Windows 1 | Client Accurate | Menjalankan Accurate 5 dan Windows Agent. |
| Laptop Windows 2 | Client Accurate | Menjalankan Accurate 5 dan Windows Agent. |
| VPS Linux | Monitoring + database server | Menjalankan RSyslog, Laravel, MySQL, Firebird, dan ZeroTier. |
| Admin device | Pengakses dashboard | Bisa salah satu laptop Windows atau perangkat lain yang dapat akses dashboard. |

## 5.2 Topologi Jaringan

Semua perangkat dihubungkan melalui ZeroTier agar VPS dan laptop Windows berada dalam satu jaringan privat virtual.

```text
+-------------------------+        ZeroTier        +-------------------------+
| Windows Laptop 1        |----------------------->| VPS Linux               |
| - Accurate 5            |                        | - RSyslog               |
| - Windows Agent         |                        | - Laravel               |
| - RDP                   |                        | - MySQL         |
| - ZeroTier              |                        | - Firebird 2.5          |
+-------------------------+                        | - ZeroTier              |
                                                   +-------------------------+
+-------------------------+        ZeroTier                  ^
| Windows Laptop 2        |----------------------------------|
| - Accurate 5            |
| - Windows Agent         |
| - RDP                   |
| - ZeroTier              |
+-------------------------+

[Admin Browser] -> Dashboard URL via VPS public IP or ZeroTier IP
[Telegram Bot]  -> Internet HTTPS
```

## 5.3 Alamat dan Identitas

Nama seperti `WIN-ACC-01`, `WIN-ACC-02`, dan `VPS-MONITOR` hanya contoh dokumentasi.

Program tidak boleh mengandalkan nama tersebut secara hardcoded.

Identitas device yang benar:

```text
Primary identity : agent_id
Secondary        : hostname
Display label    : device_label
Network identity : ip_zerotier
```

Contoh data device:

```text
agent_id      : 4d79d2d1-08e4-4c59-82ab-57d8bc6e9a33
hostname      : DESKTOP-ABCD123
device_label  : Laptop Finance 1
ip_zerotier   : 10.147.20.11
windows_user  : DESKTOP-ABCD123\Finance
status        : online
```

---

# 6. Komponen Utama Sistem

## 6.1 Windows Monitoring Agent

Windows Monitoring Agent adalah program kecil yang berjalan pada setiap laptop Windows.

Tanggung jawab utama:

1. Mengambil identitas device.
2. Mengambil user Windows aktif.
3. Mengambil IP ZeroTier.
4. Mengambil metrik CPU, RAM, disk, uptime, dan last boot time.
5. Mengecek apakah `accurate.exe` berjalan.
6. Mengecek koneksi ke VPS dan Firebird port `3051`.
7. Mengecek status RDP service jika memungkinkan.
8. Mengirim log/status/telemetry ke RSyslog Server.
9. Melakukan polling remote command ke Laravel API.
10. Menjalankan remote action manual seperti restart client setelah ada command dari admin.
11. Mengirim hasil eksekusi remote action kembali ke server.

Agent dapat dibuat menggunakan:

```text
PowerShell script
atau Python script
atau executable service sederhana
```

Untuk implementasi awal, PowerShell atau Python lebih realistis karena mudah di-debug dan dijelaskan di skripsi.

## 6.2 RSyslog Server

RSyslog Server berjalan di VPS Linux.

Tanggung jawab utama:

1. Menerima syslog TCP/UDP dari Windows Agent.
2. Menerima status/log dari server checker jika ada.
3. Menyimpan raw log ke file berdasarkan hostname atau source.
4. Menyediakan raw log untuk dibaca Laravel Parser.
5. Tidak melakukan logic bisnis kompleks.

RSyslog bukan komponen yang mengambil data dari client. Modelnya adalah push dari agent/client ke server.

```text
Windows Agent -> send syslog -> RSyslog Server
```

Bukan:

```text
RSyslog Server -> request data -> Windows Client
```

## 6.3 Laravel Application

Laravel Application adalah pusat logic sistem.

Tanggung jawab utama:

1. Menyediakan dashboard web.
2. Menyediakan login admin.
3. Menjalankan parser raw log dari RSyslog.
4. Menyimpan telemetry device ke MySQL.
5. Menyimpan network check ke MySQL.
6. Menyimpan status process Accurate.
7. Membaca audit trail Accurate dari Firebird.
8. Membuat contextual alert.
9. Membuat incident berdasarkan korelasi alert/event.
10. Mengirim Telegram alert.
11. Menyediakan Remote Command API untuk Windows Agent.
12. Mencatat remote actions.
13. Menyediakan halaman Advanced Logs untuk investigasi.

Laravel bukan penerima syslog langsung. Syslog tetap diterima oleh RSyslog, lalu Laravel membaca hasil raw log.

## 6.4 MySQL Monitoring Database

Database monitoring menyimpan seluruh data aplikasi:

1. Users admin.
2. Devices.
3. Agent credentials.
4. Device telemetry.
5. Network checks.
6. Accurate process snapshots.
7. Server service checks.
8. Raw logs dan parsed logs.
9. Accurate audit sources/events/sync state.
10. Alerts dan evidence.
11. Incidents dan relasi alert.
12. Telegram notification history.
13. Remote actions.
14. Settings dan thresholds.

Database monitoring berbeda dari database Accurate Firebird.

## 6.5 Firebird Server / Accurate Database

Firebird Server 2.5 berjalan di VPS Linux dan menyimpan database Accurate 5.

Tanggung jawab utama:

1. Menyediakan database Accurate yang diakses oleh Accurate 5 Client di laptop Windows.
2. Menyediakan tabel internal seperti `AUDIT` dan `USERS` yang dibaca oleh Accurate Audit Reader.
3. Tidak boleh dimodifikasi strukturnya oleh sistem monitoring.

Sistem monitoring hanya boleh melakukan read-only query ke Firebird.

## 6.6 Accurate Audit Reader

Accurate Audit Reader adalah modul Laravel yang membaca audit trail dari Firebird.

Tanggung jawab utama:

1. Koneksi read-only ke database Firebird.
2. Query tabel `AUDIT + USERS`.
3. Tidak memakai tabel `LOGIN` sebagai sumber utama.
4. Menyimpan hasil query ke tabel `accurate_audit_events` di MySQL.
5. Menjalankan incremental sync berdasarkan `AUDITID` atau `MODIDATE`.
6. Menghindari duplikasi audit event.
7. Menyediakan data untuk halaman Accurate Audit dan Dashboard.

Query utama mengacu pada dokumen POC:

```sql
SELECT FIRST 100
    a.AUDITID,
    a.MODIDATE AS ACTIVITY_TIME,
    u.USERNAME AS ACCURATE_USERNAME,
    u.FULLNAME AS ACCURATE_FULLNAME,
    a.SOURCE,
    a.TRANSTYPE,
    a.TRANSDESCRIPTION,
    a.INVOICENO,
    a.COMP_NAME,
    a.IPADDRESS,
    a.APPVERSION,
    a.STATUS
FROM AUDIT a
LEFT JOIN USERS u
    ON a.USERID = u.USERID
ORDER BY a.MODIDATE DESC;
```

## 6.7 Telegram Alert Service

Telegram Alert Service adalah modul Laravel yang mengirim notifikasi ke Telegram.

Tanggung jawab utama:

1. Menerima alert yang sudah dibuat oleh Alert Engine.
2. Membuat format pesan kontekstual.
3. Mengirim pesan melalui Telegram Bot API.
4. Menyimpan status pengiriman ke `alert_notifications`.
5. Mencegah spam alert dengan cooldown.
6. Menyertakan target, evidence, impact, dan recommended action.

Telegram wajib ada pada sistem v2 dan menjadi bagian novelty.

## 6.8 Remote Command API

Remote Command API adalah endpoint Laravel yang dipanggil Windows Agent untuk mengambil command.

Tanggung jawab utama:

1. Menyimpan remote command yang dibuat admin.
2. Mengizinkan agent melakukan polling command.
3. Mengembalikan command yang pending untuk device tertentu.
4. Menerima hasil eksekusi command dari agent.
5. Menyimpan semua action ke `remote_actions`.

Remote command bukan dikirim lewat RSyslog.

## 6.9 Dashboard Web

Dashboard Web adalah antarmuka utama admin.

Menu utama:

```text
Dashboard
Devices
Accurate Audit
Incidents
Remote Actions
Alerts
Advanced Logs
Settings
```

Dashboard utama tidak boleh menampilkan raw log sebagai fokus utama. Raw log hanya tersedia pada Advanced Logs.

---

# 7. Boundary dan Tanggung Jawab Komponen

## 7.1 Boundary RSyslog

RSyslog bertanggung jawab untuk:

```text
Menerima log/status/telemetry
Menyimpan raw log
Menjaga sentralisasi log
```

RSyslog tidak bertanggung jawab untuk:

```text
Membaca Firebird Audit
Membuat remote restart
Membuat keputusan incident kompleks
Mengirim command ke Windows Agent
Mengambil data aktif dari Windows client
```

## 7.2 Boundary Windows Agent

Windows Agent bertanggung jawab untuk:

```text
Mengambil data Windows host
Mengirim telemetry ke RSyslog
Mengecek koneksi ke VPS/Firebird
Mengecek process Accurate
Polling remote command
Menjalankan action manual yang disetujui admin
```

Windows Agent tidak bertanggung jawab untuk:

```text
Membaca tabel AUDIT + USERS dari Firebird
Menentukan incident global
Mengirim Telegram langsung
Mengubah database Accurate
```

## 7.3 Boundary Laravel

Laravel bertanggung jawab untuk:

```text
Dashboard
Parser
Alert Engine
Incident Engine
Telegram Service
Accurate Audit Reader
Remote Command API
Settings
```

Laravel tidak bertanggung jawab untuk:

```text
Menjadi syslog server langsung
Melakukan auto-blocking
Melakukan auto-restart tanpa admin
Mengubah struktur database Accurate
```

## 7.4 Boundary Accurate Audit Reader

Accurate Audit Reader bertanggung jawab untuk:

```text
Read-only Firebird query
Sync AUDIT + USERS
Simpan event audit ke MySQL
```

Tidak boleh:

```text
Mengubah data Accurate
Mengubah struktur Firebird
Menggunakan LOGIN sebagai sumber utama
Menganggap COMP_NAME/IPADDRESS selalu terisi
```

---

# 8. Jalur Komunikasi Sistem

## 8.1 Windows Agent ke RSyslog Server

```text
Protocol : Syslog TCP atau UDP
Port     : 514 atau port mapping khusus jika diperlukan
Network  : ZeroTier private network
Payload  : structured key=value message
```

Contoh pesan:

```text
device-monitor: agent_id=4d79 host=DESKTOP-ABCD user=DESKTOP-ABCD\Finance ip_zerotier=10.147.20.11 status=online rdp=available uptime_minutes=430
perf-monitor: agent_id=4d79 host=DESKTOP-ABCD cpu=42 ram=61 disk=55 status=normal
network-monitor: agent_id=4d79 host=DESKTOP-ABCD target=vps-firebird port=3051 status=connected latency_ms=24
accurate-process-monitor: agent_id=4d79 host=DESKTOP-ABCD process=accurate.exe status=running owner=DESKTOP-ABCD\Finance path="C:\Program Files (x86)\CPSSoft\ACCURATE5 Enterprise\accurate.exe"
```

## 8.2 RSyslog Server ke Laravel Parser

```text
RSyslog writes raw log -> /var/log/remote/*.log
Laravel parser reads new lines -> parses key=value -> stores to MySQL
```

Parser harus menggunakan offset agar tidak membaca ulang file dari awal.

## 8.3 Laravel ke Firebird

```text
Laravel Accurate Audit Reader -> Firebird host:3051 -> query AUDIT + USERS
```

Koneksi wajib read-only.

## 8.4 Accurate 5 Client ke Firebird

```text
Accurate 5 on Windows -> ZeroTier IP VPS:3051 -> Firebird Database
```

Koneksi ini adalah koneksi aplikasi Accurate, bukan koneksi dashboard.

## 8.5 Laravel ke Telegram

```text
Laravel TelegramService -> HTTPS Telegram Bot API -> Telegram Chat Admin
```

## 8.6 Dashboard ke Windows Remote Desktop

```text
Admin click Remote Desktop -> Browser opens/downloads .rdp file -> Admin runs mstsc -> Connect to Windows ZeroTier IP
```

Dashboard tidak perlu membuat koneksi RDP langsung dari server.

## 8.7 Dashboard ke Remote Restart

```text
Admin click Restart Client
Laravel creates remote_action with status=pending
Windows Agent polls Remote Command API
Agent executes restart if command valid
Agent reports result
Laravel updates remote_actions
```

---

# 9. Alur Data Utama

## 9.1 Alur Device Registration

```text
Windows Agent starts
      ↓
Generate or read persistent agent_id
      ↓
Collect hostname, IP ZeroTier, Windows user, OS info
      ↓
Send device-monitor log to RSyslog
      ↓
RSyslog writes raw log
      ↓
Laravel Parser reads log
      ↓
If agent_id not found: create device
If agent_id found: update device metadata
      ↓
Update last_seen_at and status
```

Catatan:

1. `agent_id` harus disimpan secara persisten di client agar tidak berubah setiap agent restart.
2. `hostname` boleh berubah, tetapi `agent_id` menjadi identitas utama.
3. Admin dapat mengganti `device_label` dari dashboard.

## 9.2 Alur Device Telemetry

```text
Windows Agent collects CPU/RAM/Disk/Uptime
      ↓
Send perf-monitor syslog
      ↓
RSyslog writes raw log
      ↓
Laravel Parser parses key=value
      ↓
Insert device_telemetries
      ↓
Update latest status on devices
      ↓
Alert Engine checks threshold
      ↓
Dashboard displays latest telemetry
```

## 9.3 Alur Network / Firebird Connectivity

```text
Windows Agent tests ping to VPS
Windows Agent tests TCP connect to Firebird port 3051
Windows Agent measures latency
      ↓
Send network-monitor syslog
      ↓
Laravel Parser stores network_checks
      ↓
Alert Engine evaluates connection status
      ↓
Dashboard shows Firebird connectivity per device
```

Jenis hasil koneksi:

```text
connected
timeout
refused
unreachable
slow
unknown
```

## 9.4 Alur Accurate Process Monitoring

```text
Windows Agent checks process list
      ↓
Find accurate.exe or not
      ↓
Read owner/path if possible
      ↓
Send accurate-process-monitor syslog
      ↓
Laravel Parser stores accurate_process_snapshots
      ↓
Dashboard shows Accurate running/not running per device
      ↓
Alert Engine creates alert only if rule is met
```

Catatan:

`accurate.exe` tidak berjalan belum tentu masalah jika di luar jam kerja atau device tidak sedang dipakai. Oleh karena itu severity harus mengikuti rule yang jelas.

## 9.5 Alur Server Service Check

```text
Server Health Checker runs on VPS
      ↓
Check Firebird service
Check RSyslog service
Check MySQL service
Check Laravel web service
      ↓
Send service-monitor log or write direct through Laravel command
      ↓
Store server_service_checks
      ↓
Alert Engine evaluates status
```

Service penting awal:

```text
firebird
rsyslog
mysql
laravel_web
scheduler
```

## 9.6 Alur Accurate Audit Trail

```text
Laravel Scheduler
      ↓
Run accurate:audit-sync
      ↓
Read sync state from accurate_audit_sync_states
      ↓
Connect read-only to Firebird
      ↓
Query AUDIT + USERS
      ↓
Map Firebird fields to MySQL fields
      ↓
Check duplicate by audit_id/source/hash
      ↓
Insert accurate_audit_events
      ↓
Update sync state
      ↓
Dashboard shows Accurate Audit
```

Alur ini tidak melewati RSyslog.

## 9.7 Alur Alert Creation

```text
New telemetry/network/process/service/audit event
      ↓
Alert Engine evaluates detection rules
      ↓
If rule matched:
  create alert with target, evidence, impact, recommended_action
      ↓
Create alert_evidences
      ↓
If Telegram enabled and cooldown passed:
  send Telegram notification
      ↓
Store alert_notifications
```

Alert tidak boleh dibuat tanpa target dan evidence.

## 9.8 Alur Incident Correlation

```text
Multiple alerts/events within time window
      ↓
Incident Engine evaluates correlation rules
      ↓
Create or update incident
      ↓
Attach related alerts to incident
      ↓
Dashboard shows incident summary
```

Untuk MVP, incident correlation dibuat sederhana dan berbasis rule eksplisit.

Contoh:

```text
CPU high + RAM high + Firebird latency high on same device within 5 minutes
= Device performance degradation incident
```

Jangan membuat rule abstrak seperti `audit activity spike` kecuali threshold-nya didefinisikan jelas.

## 9.9 Alur Telegram Notification

```text
Alert created
      ↓
TelegramService builds contextual message
      ↓
Check cooldown / anti-spam
      ↓
Send message to Telegram Bot API
      ↓
Save delivery status
```

Format pesan wajib memuat:

```text
Severity
Title
Target
Detected by
Evidence
Impact
Recommended action
Time
Dashboard link if available
```

Contoh:

```text
[WARNING] CPU tinggi pada Laptop Finance 2

Target     : Laptop Finance 2
Hostname   : DESKTOP-XYZ
User       : DESKTOP-XYZ\Finance
Detected by: Windows Agent
Evidence   : CPU 87%, RAM 82%, 5 menit terakhir
Impact     : Device berpotensi lambat saat menggunakan Accurate
Action     : Gunakan Remote Desktop atau cek aplikasi berjalan
Time       : 2026-05-28 19:51
```

## 9.10 Alur Remote Desktop

```text
Admin opens Device Detail
      ↓
Click Remote Desktop
      ↓
Laravel records remote_action type=OPEN_RDP
      ↓
Laravel generates .rdp content or mstsc instruction
      ↓
Admin opens RDP using Windows Remote Desktop Client
```

Remote Desktop tidak dieksekusi oleh server. Admin tetap membuka RDP dari perangkatnya.

## 9.11 Alur Remote Restart

```text
Admin opens Device Detail
      ↓
Click Restart Client
      ↓
Modal confirmation appears
      ↓
Admin enters reason
      ↓
Laravel creates remote_action status=pending
      ↓
Windows Agent polls API
      ↓
Agent receives RESTART_CLIENT command
      ↓
Agent validates command and token
      ↓
Agent executes shutdown /r /t 30 or configured command
      ↓
Agent reports accepted/executed/failed
      ↓
Laravel updates remote_action status
```

Catatan wajib:

1. Tidak ada auto restart.
2. Restart hanya dilakukan setelah admin klik dan konfirmasi.
3. Semua action harus dicatat.
4. Agent hanya menjalankan command untuk device sendiri.
5. Remote restart dapat dinonaktifkan melalui Settings.

---

# 10. Diagram Arsitektur Detail

## 10.1 Component Diagram

```text
+----------------------------------------------------------------------------------+
| Windows Laptop 1                                                                 |
|                                                                                  |
|  +------------------+     +------------------------+     +---------------------+  |
|  | Accurate 5       |     | Windows Agent          |     | ZeroTier            |  |
|  | Client           |     | - telemetry collector  |     | Private network     |  |
|  +------------------+     | - process checker      |     +---------------------+  |
|          |                | - network checker      |              |             |
|          | Firebird 3051  | - command polling      |              |             |
+----------|----------------+------------------------+--------------|-------------+
           |                                                       |
           |                                                       |
           v                                                       v
+----------------------------------------------------------------------------------+
| VPS Linux                                                                         |
|                                                                                  |
| +-------------------+       +---------------------+       +--------------------+ |
| | Firebird Server   |<----->| Accurate Audit      |------>| MySQL      | |
| | Accurate DB       |       | Reader Laravel      |       | Monitoring DB      | |
| +-------------------+       +---------------------+       +--------------------+ |
|          ^                                                           ^           |
|          |                                                           |           |
| +-------------------+       +---------------------+                  |           |
| | RSyslog Server    |------>| Laravel Parser      |------------------+           |
| | /var/log/remote   |       | Alert Engine        |                              |
| +-------------------+       | Incident Engine     |                              |
|          ^                  | Remote Command API  |                              |
|          |                  | Dashboard Web       |                              |
|          |                  +---------------------+                              |
|          |                            |                                         |
|          |                            v                                         |
|          |                  +---------------------+                              |
|          |                  | Telegram Service    |-----------------------> Telegram
|          |                  +---------------------+                              |
+----------|-----------------------------------------------------------------------+
           |
           |
+----------|-----------------------------------------------------------------------+
| Windows Laptop 2                                                                 |
| Same structure as Laptop 1                                                       |
+----------------------------------------------------------------------------------+
```

## 10.2 Data Flow Diagram

```text
[Windows Agent]
   | device/performance/network/process status
   v
[RSyslog Server]
   | raw log files
   v
[Laravel Parser]
   | structured monitoring data
   v
[MySQL]
   | query
   v
[Dashboard]

[Firebird Accurate DB]
   | read-only AUDIT + USERS
   v
[Accurate Audit Reader]
   | audit events
   v
[MySQL]
   | query
   v
[Accurate Audit Page]

[Alert Engine]
   | contextual alert
   v
[Telegram Service]
   | HTTPS
   v
[Telegram Admin]

[Dashboard]
   | create remote action
   v
[Remote Command API]
   | polling
   v
[Windows Agent]
   | execute manual command
   v
[Remote Action Result]
```

---

# 11. Sequence Diagram

## 11.1 Sequence — Device Telemetry Masuk ke Dashboard

```text
Windows Agent      RSyslog Server      Laravel Parser       MySQL         Dashboard
     |                   |                    |                |              |
     | collect telemetry |                    |                |              |
     |------------------>|                    |                |              |
     | syslog message    |                    |                |              |
     |                   | write raw log      |                |              |
     |                   |------------------->|                |              |
     |                   |                    | parse new log  |              |
     |                   |                    |--------------->|              |
     |                   |                    | insert/update  |              |
     |                   |                    |--------------->|              |
     |                   |                    |                | query latest |
     |                   |                    |                |<-------------|
     |                   |                    |                | return data  |
     |                   |                    |                |------------->|
```

## 11.2 Sequence — Accurate Audit Sync

```text
Laravel Scheduler      Accurate Audit Reader      Firebird DB      MySQL       Dashboard
       |                         |                    |             |             |
       | run sync command         |                    |             |             |
       |------------------------>|                    |             |             |
       |                         | read sync state     |             |             |
       |                         |--------------------------------->|             |
       |                         | query AUDIT + USERS |             |             |
       |                         |------------------->|             |             |
       |                         | return rows         |             |             |
       |                         |<-------------------|             |             |
       |                         | insert audit events |             |             |
       |                         |--------------------------------->|             |
       |                         | update sync state   |             |             |
       |                         |--------------------------------->|             |
       |                         |                    |             | query audit |
       |                         |                    |             |<------------|
       |                         |                    |             | return data |
       |                         |                    |             |------------>|
```

## 11.3 Sequence — Alert Telegram

```text
Parser/Audit Reader      Alert Engine      MySQL      Telegram Service      Telegram API
       |                      |             |                |                  |
       | new event             |             |                |                  |
       |--------------------->|             |                |                  |
       |                      | evaluate    |                |                  |
       |                      | create alert|                |                  |
       |                      |------------>|                |                  |
       |                      | evidence    |                |                  |
       |                      |------------>|                |                  |
       |                      | request send|                |                  |
       |                      |---------------------------->|                  |
       |                      |             |                | send message     |
       |                      |             |                |----------------->|
       |                      |             |                | response         |
       |                      |             |                |<-----------------|
       |                      |             | save status     |                  |
       |                      |             |<---------------|                  |
```

## 11.4 Sequence — Remote Restart Manual

```text
Admin       Dashboard       Laravel API       MySQL        Windows Agent       Windows OS
  |             |               |              |                |                |
  | click restart               |              |                |                |
  |------------>|               |              |                |                |
  | confirm + reason            |              |                |                |
  |------------>| create action |              |                |                |
  |             |-------------->| save pending |                |                |
  |             |               |------------->|                |                |
  |             |               |              | poll command   |                |
  |             |               |<------------------------------|                |
  |             |               | return cmd    |                |                |
  |             |               |------------------------------>|                |
  |             |               |              | execute restart|                |
  |             |               |              |------------------------------->|
  |             |               |              | report status  |                |
  |             |               |<------------------------------|                |
  |             |               | update result |                |                |
  |             |               |------------->|                |                |
```

---

# 12. Data Architecture

## 12.1 Data Group

Data sistem dibagi menjadi beberapa kelompok:

| Kelompok Data | Tabel Utama | Sumber |
|---|---|---|
| User admin | `users` | Laravel Auth |
| Device identity | `devices`, `agent_credentials` | Windows Agent |
| Telemetry | `device_telemetries` | Windows Agent via RSyslog |
| Network check | `network_checks` | Windows Agent via RSyslog |
| Accurate process | `accurate_process_snapshots` | Windows Agent via RSyslog |
| Service check | `server_service_checks` | VPS checker / Laravel command |
| Raw logs | `logs` | RSyslog parser |
| Accurate audit | `accurate_audit_events` | Firebird AUDIT + USERS |
| Alert | `alerts`, `alert_evidences` | Alert Engine |
| Notification | `alert_notifications` | Telegram Service |
| Incident | `incidents`, `incident_alerts` | Incident Engine |
| Remote action | `remote_actions` | Dashboard + Windows Agent |
| Settings | `threshold_settings`, `system_settings` | Admin Settings |

## 12.2 Write Path

```text
Windows Agent telemetry
  -> RSyslog raw log
  -> Laravel Parser
  -> MySQL structured tables

Firebird audit
  -> Laravel Accurate Audit Reader
  -> MySQL accurate_audit_events

Admin action
  -> Laravel RemoteActionController
  -> MySQL remote_actions
  -> Windows Agent polling
  -> MySQL remote_actions result
```

## 12.3 Read Path

```text
Dashboard
  -> Query latest devices
  -> Query latest telemetry per device
  -> Query latest network checks per device
  -> Query latest Accurate process snapshots
  -> Query latest Accurate audit events
  -> Query active alerts/incidents
```

## 12.4 Raw Log vs Structured Data

Raw log tetap disimpan, tetapi dashboard utama harus memakai structured data.

| Data | Dipakai Dashboard Utama? | Dipakai Advanced Logs? |
|---|---|---|
| Raw syslog message | Tidak dominan | Ya |
| Parsed device telemetry | Ya | Opsional |
| Parsed network check | Ya | Opsional |
| Accurate audit event | Ya | Tidak sebagai raw log |
| Alert evidence | Ya | Ya |

---

# 13. Detection Architecture

## 13.1 Event, Alert, Incident

Sistem membedakan tiga lapisan:

```text
Event    = data masuk, misalnya telemetry, network check, audit event, service check
Alert    = peringatan spesifik dari satu rule
Incident = gabungan beberapa alert/event yang menjelaskan masalah operasional
```

Contoh:

```text
Event:
WIN-ACC-02 cpu=87 ram=82 firebird_latency=650ms

Alert:
WARNING - CPU tinggi pada WIN-ACC-02

Incident:
WIN-ACC-02 terindikasi lambat karena CPU tinggi + RAM tinggi + koneksi Firebird lambat
```

## 13.2 Alert Requirement

Setiap alert wajib memiliki:

```text
target_type
target_id
target_name
source_type
source_name
severity
title
description
impact
recommended_action
evidence
status
detected_at
```

Alert tidak boleh berupa kalimat generik tanpa konteks.

Contoh yang tidak boleh:

```text
CRITICAL - Firebird unreachable
WARNING - Accurate process not running
INFO - Audit activity spike detected
```

Contoh yang benar:

```text
ERROR - WIN-ACC-01 gagal terhubung ke Firebird VPS:3051
Target     : WIN-ACC-01 → VPS-FIREBIRD
Detected by: Windows Agent
Evidence   : tcp_connect timeout 3 kali
Impact     : Accurate pada device tersebut tidak dapat membuka database
Action     : Cek koneksi ZeroTier atau status Firebird dari dashboard
```

## 13.3 Detection Rule Examples

| Rule | Target | Source | Condition | Severity |
|---|---|---|---|---|
| DEVICE_HEARTBEAT_MISSED | Device | Scheduler | last_seen > 5 menit | WARNING |
| DEVICE_OFFLINE | Device | Scheduler | last_seen > 15 menit | CRITICAL |
| CPU_HIGH | Device | Telemetry | CPU >= 80% selama window tertentu | WARNING |
| CPU_CRITICAL | Device | Telemetry | CPU >= 90% selama window tertentu | CRITICAL |
| RAM_HIGH | Device | Telemetry | RAM >= 85% | WARNING |
| FIREBIRD_PORT_TIMEOUT | Device → VPS | Network check | tcp connect 3051 timeout | ERROR |
| FIREBIRD_LATENCY_HIGH | Device → VPS | Network check | latency > threshold | WARNING |
| FIREBIRD_SERVICE_DOWN | VPS | Server checker | service inactive / port closed | CRITICAL |
| ACCURATE_PROCESS_NOT_DETECTED | Device | Process check | accurate.exe tidak ditemukan saat jam kerja | WARNING |
| ACCURATE_AUDIT_DELETE | Accurate Audit | Firebird Audit | jika TRANSTYPE/description menunjukkan delete | CRITICAL |

Rule detail dan threshold akan dikunci pada dokumen khusus `06_Detection_Rules_v2.md`.

---

# 14. Remote Administration Architecture

## 14.1 Remote Desktop

Remote Desktop adalah fitur bantu admin untuk masuk ke device Windows.

Desain:

1. Dashboard menampilkan tombol Remote Desktop pada device detail.
2. Tombol tetap tersedia selama device memiliki IP ZeroTier dan RDP status available.
3. Dashboard mencatat action `OPEN_RDP`.
4. Dashboard dapat membuat file `.rdp` atau instruksi `mstsc /v:<ip_zerotier>`.
5. Koneksi RDP dilakukan dari perangkat admin, bukan dari server Laravel.

## 14.2 Remote Restart

Remote Restart adalah tindakan manual terkontrol.

Desain:

1. Tombol Restart tersedia pada Device Detail.
2. Admin harus login.
3. Admin harus konfirmasi.
4. Admin harus mengisi alasan.
5. Laravel membuat `remote_actions` dengan status `pending`.
6. Windows Agent melakukan polling.
7. Agent menjalankan command jika valid.
8. Agent melaporkan hasil.
9. Laravel menyimpan status akhir.

Status remote action:

```text
pending
picked_up
executed
failed
cancelled
expired
```

## 14.3 Larangan Remote Action

Tidak boleh:

1. Auto restart tanpa admin.
2. Restart berdasarkan alert otomatis.
3. Menjalankan command dari RSyslog.
4. Menjalankan command pada device yang tidak sesuai agent_id.
5. Menyimpan credential Windows admin plaintext.

---

# 15. Deployment Architecture

## 15.1 VPS Linux

VPS Linux menjadi pusat sistem.

Komponen pada VPS:

```text
- ZeroTier client
- RSyslog Server
- Web server / PHP runtime untuk Laravel
- MySQL
- Firebird Server 2.5
- Laravel scheduler / queue worker
- Telegram outbound HTTPS
```

Implementasi bisa menggunakan Docker Compose untuk sebagian service atau native install. Namun karena Firebird dan Accurate database perlu stabil, deployment final harus diputuskan pada dokumen `09_Deployment_Guide_VPS_ZeroTier_v2.md`.

## 15.2 Windows Clients

Setiap laptop Windows harus memiliki:

```text
- Accurate 5 Client
- ZeroTier client
- Windows Monitoring Agent
- RDP enabled jika fitur remote desktop digunakan
- Agent config berisi VPS endpoint, syslog target, API token, dan agent_id
```

## 15.3 Network Ports

Port yang digunakan:

| Port | Protokol | Arah | Fungsi |
|---:|---|---|---|
| 22 | TCP | Admin -> VPS | SSH maintenance |
| 80/443 | TCP | Admin -> VPS | Dashboard web |
| 514 atau custom | TCP/UDP | Windows Agent -> VPS | Syslog ke RSyslog |
| 3051 | TCP | Windows Accurate -> VPS | Firebird database |
| 3306 | TCP | Laravel -> MySQL local/internal | Monitoring DB |
| 3389 | TCP | Admin -> Windows client | RDP via ZeroTier |

Catatan:

1. Firebird port `3051` sebaiknya hanya dapat diakses melalui ZeroTier.
2. RDP sebaiknya hanya melalui ZeroTier.
3. MySQL tidak perlu dibuka publik.
4. Telegram menggunakan HTTPS outbound dari VPS.

---

# 16. Security Considerations

## 16.1 Network Security

1. Gunakan ZeroTier untuk komunikasi antar device.
2. Batasi port Firebird agar tidak terbuka bebas ke publik jika memungkinkan.
3. Batasi RDP hanya pada jaringan ZeroTier.
4. Gunakan firewall VPS untuk membatasi port yang tidak diperlukan.

## 16.2 Agent Security

1. Setiap agent harus memiliki `agent_id` unik.
2. Setiap agent harus memiliki token/API key.
3. Agent hanya boleh mengambil command untuk dirinya sendiri.
4. Agent harus menyimpan config secara aman.
5. Agent harus mencatat command execution result.

## 16.3 Firebird Security

1. Accurate Audit Reader menggunakan user read-only.
2. Jangan mengubah struktur database Accurate.
3. Jangan menulis data ke tabel Accurate.
4. Jangan menjadikan tabel `LOGIN` sebagai sumber utama.
5. Jangan mengasumsikan `COMP_NAME` dan `IPADDRESS` selalu terisi.

## 16.4 Dashboard Security

1. Dashboard wajib login admin.
2. Route remote action wajib authentication.
3. Remote restart wajib konfirmasi dan alasan.
4. Semua remote action wajib dicatat.
5. Jangan tampilkan credential sensitif di UI.

## 16.5 Telegram Security

1. Token Telegram disimpan di environment variable.
2. Chat ID disimpan sebagai setting aman.
3. Jangan kirim credential atau data sensitif penuh ke Telegram.
4. Telegram message harus informatif tetapi tidak membocorkan password/path sensitif yang tidak perlu.

---

# 17. Error Handling Architecture

## 17.1 Windows Agent Gagal Kirim Syslog

Jika agent gagal mengirim syslog:

1. Agent dapat mencoba ulang.
2. Agent dapat menyimpan buffer lokal sementara.
3. Dashboard akan mendeteksi device heartbeat missed jika tidak ada log masuk.

## 17.2 RSyslog Server Down

Jika RSyslog down:

1. Server checker membuat alert `RSYSLOG_SERVICE_DOWN` jika bisa dijalankan dari Laravel/native checker.
2. Windows Agent tidak dapat mengirim log.
3. Device akan terlihat offline jika heartbeat berhenti.

## 17.3 Parser Error

Jika parser error:

1. Simpan record ke `parser_runs` dengan status failed.
2. Jangan menghapus raw log.
3. Parser dapat dilanjutkan setelah error diperbaiki.

## 17.4 Firebird Connection Error

Jika Accurate Audit Reader gagal connect ke Firebird:

1. Simpan sync run failed.
2. Buat alert `ACCURATE_AUDIT_SYNC_FAILED` jika error berulang.
3. Dashboard menampilkan last sync failed.
4. Jangan menganggap audit event kosong sebagai tidak ada aktivitas jika koneksi gagal.

## 17.5 Telegram Error

Jika Telegram gagal:

1. Simpan `alert_notifications.status = failed`.
2. Simpan error message.
3. Alert tetap muncul di dashboard.
4. Sistem dapat retry sesuai setting.

## 17.6 Remote Action Error

Jika remote action gagal:

1. Update `remote_actions.status = failed`.
2. Simpan result_message.
3. Tampilkan ke admin.
4. Jangan retry restart otomatis tanpa admin.

---

# 18. Scheduler dan Background Job

Sistem membutuhkan beberapa proses berkala.

| Job | Frekuensi Awal | Fungsi |
|---|---:|---|
| `rsyslog:parse` | 10-30 detik | Membaca raw log baru dari RSyslog. |
| `accurate:audit-sync` | 30-60 detik | Membaca audit Firebird `AUDIT + USERS`. |
| `devices:evaluate-status` | 1 menit | Menentukan online/warning/offline berdasarkan last_seen. |
| `alerts:evaluate` | Event-based / scheduler | Membuat alert dari telemetry/check terbaru. |
| `incidents:correlate` | 1 menit | Membuat incident dari alert/event terkait. |
| `server:health-check` | 1 menit | Mengecek service di VPS. |
| `remote-actions:expire` | 1 menit | Mengubah pending command yang terlalu lama menjadi expired. |

Nama command final dapat disesuaikan pada SRS dan implementasi, tetapi Codex harus menjaga nama yang mudah dipahami.

---

# 19. UI Architecture

## 19.1 Menu Utama

```text
Dashboard
Devices
Accurate Audit
Incidents
Remote Actions
Alerts
Advanced Logs
Settings
Logout
```

## 19.2 Dashboard Utama

Dashboard utama harus menampilkan:

1. Summary device online.
2. Summary Firebird connectivity.
3. Summary Accurate process running.
4. Summary audit events today.
5. Summary active alerts/incidents.
6. Device Health table.
7. Accurate Audit latest events.
8. Recent contextual alerts.
9. Recent incidents.

Dashboard utama tidak boleh menampilkan raw log panjang sebagai fokus utama.

## 19.3 Devices Page

Menampilkan daftar device Windows.

Data utama:

```text
Device label
Hostname
Windows user
IP ZeroTier
Agent status
RDP status
Accurate status
Firebird connection
CPU/RAM/Disk
Last seen
Action buttons
```

## 19.4 Accurate Audit Page

Menampilkan audit trail Accurate dari Firebird.

Data utama:

```text
Activity time
Accurate username
Full name
Source/module
Transaction type
Description
Invoice/reference
App version
Status
```

## 19.5 Alerts Page

Menampilkan alert dengan target dan evidence.

Data utama:

```text
Severity
Title
Target
Detected by
Evidence summary
Impact
Recommended action
Status
Detected at
```

## 19.6 Advanced Logs Page

Menampilkan raw dan parsed logs untuk investigasi teknis.

Data utama:

```text
Time
Hostname
Source/tag
Category
Severity
Raw message
Parsed message
Source file
```

---

# 20. Implementation Phasing

Walaupun target sistem adalah real-device, implementasi dapat dilakukan bertahap agar stabil.

## Phase 1 — Core Laravel dan Database

1. Scaffold Laravel.
2. Auth admin.
3. Layout dashboard.
4. Migration database v2.
5. Seeder admin dan threshold default.

## Phase 2 — RSyslog dan Windows Agent Basic

1. RSyslog server di VPS.
2. Windows Agent basic heartbeat.
3. Parser key=value.
4. Auto-register device.
5. Devices page menampilkan real device.

## Phase 3 — Telemetry dan Connectivity

1. CPU/RAM/Disk.
2. Firebird port check.
3. Accurate process check.
4. Dashboard device health.

## Phase 4 — Accurate Audit Reader

1. Firebird connection read-only.
2. Query `AUDIT + USERS`.
3. Incremental sync.
4. Accurate Audit page.

## Phase 5 — Alert dan Telegram

1. Contextual Alert Engine.
2. Evidence table.
3. Telegram message format.
4. Cooldown / anti-spam.

## Phase 6 — Remote Administration

1. RDP launcher.
2. Remote action log.
3. Agent command polling.
4. Manual restart with confirmation.

## Phase 7 — Incident Correlation dan Polish

1. Incident rule sederhana.
2. Dashboard polish.
3. Test plan.
4. Deployment guide.
5. Demo script real-device.

Catatan:

Phase ini bukan berarti membuat data demo random. Sejak Phase 2, data yang dikejar adalah real device.

---

# 21. Git dan Development Workflow

Rekomendasi workflow:

```text
main        = stable version
develop     = active development
feature/*   = fitur spesifik
fix/*       = bugfix
```

Contoh branch:

```text
feature/database-v2
feature/windows-agent-heartbeat
feature/rsyslog-parser
feature/accurate-audit-reader
feature/contextual-alerts
feature/remote-actions
```

Setiap fitur sebaiknya memiliki:

1. Commit jelas.
2. Catatan perubahan.
3. Minimal manual test.
4. Tidak langsung mengubah fitur lain yang tidak terkait.

---

# 22. Hal yang Tidak Boleh Dilakukan Codex / AI Agent

Codex / AI Agent tidak boleh:

1. Mengubah sistem menjadi simulasi-only.
2. Membuat device hardcoded seperti `if hostname == WIN-ACC-01`.
3. Menggunakan WSL sebagai cara utama membaca kondisi Windows host.
4. Mengirim semua Windows Event Log random sebagai fokus dashboard.
5. Menjadikan raw log sebagai komponen utama Dashboard.
6. Menggunakan tabel `LOGIN` Accurate sebagai sumber utama username internal.
7. Mengasumsikan `COMP_NAME` dan `IPADDRESS` di `AUDIT` selalu terisi.
8. Membaca atau menulis database Accurate tanpa mode read-only untuk audit.
9. Membuat alert tanpa target dan evidence.
10. Membuat `audit activity spike detected` tanpa rule dan threshold yang eksplisit.
11. Melakukan auto restart berdasarkan alert.
12. Mengirim remote command lewat RSyslog.
13. Menyimpan credential sensitif plaintext.
14. Mengganti stack utama menjadi React/Node.js backend/ELK/Grafana/Prometheus tanpa instruksi eksplisit.
15. Menghapus kebutuhan Telegram.

---

# 23. Acceptance Criteria Arsitektur

Arsitektur dianggap sesuai jika memenuhi kriteria berikut:

| ID | Acceptance Criteria |
|---|---|
| ARCH-001 | Dua laptop Windows dapat mengirim heartbeat real ke VPS melalui Windows Agent. |
| ARCH-002 | RSyslog Server menerima log/status dari Windows Agent dan menyimpan raw log. |
| ARCH-003 | Laravel Parser membaca raw log dan menyimpan data terstruktur ke MySQL. |
| ARCH-004 | Device terdaftar otomatis berdasarkan `agent_id`, bukan hardcoded hostname. |
| ARCH-005 | Dashboard menampilkan device real beserta user Windows, status agent, IP ZeroTier, CPU/RAM/Disk, dan last seen. |
| ARCH-006 | Windows Agent mengirim hasil cek koneksi ke Firebird port 3051. |
| ARCH-007 | Accurate process status diambil dari Windows Agent dan tampil di dashboard. |
| ARCH-008 | Accurate Audit Reader membaca Firebird `AUDIT + USERS` secara read-only. |
| ARCH-009 | Tabel `LOGIN` tidak dipakai sebagai sumber utama audit. |
| ARCH-010 | Telegram mengirim contextual alert yang memuat target, evidence, impact, dan recommended action. |
| ARCH-011 | Remote Desktop tersedia sebagai action manual dari device detail. |
| ARCH-012 | Remote Restart hanya berjalan setelah admin konfirmasi dan tercatat di remote actions. |
| ARCH-013 | Raw log tersedia pada Advanced Logs, bukan fokus dashboard utama. |
| ARCH-014 | Firebird database ditempatkan pada VPS Linux dan diakses client melalui ZeroTier. |
| ARCH-015 | Tidak ada auto-blocking atau auto-restart. |

---

# 24. Ringkasan Akhir

Arsitektur v2 mengubah sistem dari dashboard log simulasi menjadi **dashboard monitoring real-device** untuk lingkungan PT. XYZ skala kecil.

Desain final:

```text
Windows Laptop + Accurate 5 + Windows Agent
        ↓
ZeroTier Private Network
        ↓
VPS Linux
        ├── RSyslog Server
        ├── Laravel Dashboard
        ├── MySQL Monitoring DB
        ├── Firebird Server / Accurate DB
        ├── Accurate Audit Reader
        ├── Telegram Alert Service
        └── Remote Command API
```

Prinsip utama:

1. RSyslog tetap dipakai sebagai collector log/status.
2. Windows Agent mengirim telemetry real dari Windows host.
3. Accurate Audit Trail dibaca langsung dari Firebird `AUDIT + USERS` secara read-only.
4. Dashboard fokus ke device, koneksi Firebird, performa, Accurate process, audit trail, alert, incident, dan remote action.
5. Telegram wajib sebagai contextual proactive alert.
6. Remote Desktop dan Remote Restart adalah fitur manual terkontrol.
7. Raw log hanya untuk Advanced Logs.

Dokumen berikutnya yang disarankan setelah ini adalah:

```text
06_Windows_Agent_and_RSyslog_Guide_v2.md
```

atau jika ingin mengunci rule deteksi terlebih dahulu:

```text
06_Detection_Rules_v2.md
```

Rekomendasi urutan berikutnya: buat **06_Windows_Agent_and_RSyslog_Guide_v2.md** terlebih dahulu, karena komponen real-device paling krusial adalah Windows Agent yang mengirim data ke RSyslog.
