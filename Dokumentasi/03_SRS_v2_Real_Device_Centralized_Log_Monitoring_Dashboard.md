# 03 — SRS v2
# Centralized Log Monitoring Dashboard

**Versi:** 2.0  
**Status:** Draft Final untuk implementasi awal dengan Codex / AI Agent  
**Nama Sistem:** Centralized Log Monitoring Dashboard  
**Tipe Sistem:** Real-device IT monitoring dashboard berbasis RSyslog, Windows Agent, Accurate Firebird Audit Reader, dan Telegram Alert  
**Target Lingkungan:** 2 laptop Windows pengguna Accurate 5 + 1 VPS Linux sebagai pusat monitoring dan database/server  
**Stack Utama:** Laravel, Blade, Tailwind CSS, MySQL, RSyslog, Windows Agent, Firebird 2.5, ZeroTier, Telegram Bot API  
**Dokumen Acuan:**  
1. `01_PRD_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md`  
2. `02_Accurate_Firebird_POC_Findings_v2.md`  

---

# 1. Pendahuluan

## 1.1 Tujuan Dokumen

Dokumen Software Requirement Specification atau SRS v2 ini menjelaskan kebutuhan perangkat lunak untuk sistem **Centralized Log Monitoring Dashboard** versi real-device.

Dokumen ini menjadi acuan teknis untuk AI Agent, Codex, atau developer dalam membangun ulang sistem dari awal secara terstruktur, agar implementasi tidak keluar dari konteks skripsi dan tidak kembali menjadi dashboard raw log yang menampilkan data acak tanpa hubungan langsung dengan kebutuhan Administrator IT.

SRS v2 ini menurunkan PRD v2 menjadi kebutuhan fungsional, non-fungsional, kebutuhan data, alur sistem, batasan implementasi, dan acceptance criteria.

Dokumen ini juga mengunci beberapa keputusan penting:

1. Sistem menggunakan **real device**, bukan simulasi container client sebagai fokus utama.
2. Sistem memonitor **dua laptop Windows pengguna Accurate 5** dan **satu VPS Linux** sebagai server pusat.
3. RSyslog digunakan untuk menerima log/status/telemetry dari Windows Agent dan server checker.
4. Accurate Audit Trail dibaca langsung dari database Firebird secara read-only melalui tabel `AUDIT + USERS`, bukan melalui RSyslog.
5. Telegram Alert bersifat wajib dan menjadi bagian penting dari novelty sistem.
6. Remote Desktop dan Remote Restart tersedia sebagai tindakan manual terkontrol oleh admin.
7. Raw log tetap tersedia, tetapi hanya pada menu Advanced Logs, bukan sebagai fokus dashboard utama.

---

## 1.2 Ruang Lingkup Sistem

Sistem yang dibangun adalah dashboard monitoring IT untuk lingkungan PT. XYZ skala kecil yang terdiri dari:

```text
[Windows Laptop 1]
- Accurate 5 Client
- Windows Monitoring Agent
- ZeroTier
- RDP enabled

[Windows Laptop 2]
- Accurate 5 Client
- Windows Monitoring Agent
- ZeroTier
- RDP enabled

        ↓ log/status/telemetry melalui ZeroTier

[VPS Linux]
- RSyslog Server
- Laravel Dashboard
- MySQL
- Firebird Server 2.5 / Accurate Database
- Accurate Audit Reader
- Telegram Alert Service
- Remote Command API
```

Sistem berfungsi untuk membantu Administrator IT memantau:

1. Status device Windows.
2. User Windows yang sedang aktif.
3. Koneksi device ke server Firebird/Accurate.
4. Performa device seperti CPU, RAM, disk, uptime, dan indikasi tidak responsif.
5. Status proses Accurate 5 pada device Windows.
6. Status service penting di VPS seperti Firebird dan RSyslog.
7. Audit trail aktivitas Accurate dari tabel `AUDIT + USERS`.
8. Alert kontekstual melalui dashboard dan Telegram.
9. Tindakan remote manual seperti Remote Desktop dan Remote Restart.

---

## 1.3 Definisi Istilah

| Istilah | Definisi |
|---|---|
| Centralized Log Monitoring Dashboard | Sistem dashboard monitoring terpusat berbasis Laravel yang menampilkan status device, koneksi, performa, audit Accurate, alert, dan tindakan remote. |
| VPS Linux | Server Linux publik/private yang menjadi pusat monitoring, RSyslog, Laravel, MySQL, dan Firebird. |
| Windows Agent | Program atau script kecil yang berjalan pada laptop Windows untuk mengambil telemetry device dan mengirim log terstruktur ke RSyslog server. |
| RSyslog Server | Komponen di VPS yang menerima pesan log dari Windows Agent atau server checker melalui protokol syslog TCP/UDP. |
| Syslog Message | Pesan log berformat teks yang dikirim oleh agent ke RSyslog server. |
| Device | Laptop Windows client yang dimonitor oleh sistem. |
| Agent ID | Identitas unik agent pada satu device, digunakan agar sistem tidak bergantung pada hostname hardcoded. |
| Hostname | Nama komputer Windows yang terbaca dari OS. |
| Device Label | Nama tampilan yang dapat diubah admin, misalnya “Laptop Finance 1”. |
| ZeroTier | Jaringan virtual privat yang menghubungkan VPS dan laptop Windows agar seolah berada dalam satu jaringan lokal. |
| Firebird | Database server yang digunakan Accurate 5. Pada sistem ini Firebird 2.5 berjalan di VPS Linux. |
| Accurate 5 | Aplikasi akuntansi yang digunakan user Windows untuk mengakses database Accurate di Firebird. |
| Accurate Audit Reader | Modul Laravel untuk membaca audit trail dari database Firebird secara read-only. |
| AUDIT | Tabel Accurate yang menyimpan aktivitas/perubahan data user internal Accurate. |
| USERS | Tabel Accurate yang menyimpan master user internal Accurate. |
| LOGIN | Tabel yang secara struktur tersedia tetapi berdasarkan POC tidak dijadikan sumber utama karena kosong pada database uji. |
| Alert | Peringatan spesifik berdasarkan satu kondisi bermasalah, memiliki target, source, evidence, severity, dan recommended action. |
| Incident | Korelasi beberapa event/alert untuk menggambarkan masalah operasional yang lebih kontekstual. |
| Telegram Alert | Notifikasi proaktif ke Telegram admin yang berisi severity, target, evidence, impact, dan saran tindakan. |
| Remote Desktop | Tindakan manual admin untuk membuka sesi RDP ke device Windows. |
| Remote Restart | Tindakan manual admin untuk meminta Windows Agent melakukan restart device setelah konfirmasi. |
| Advanced Logs | Halaman untuk melihat raw log teknis dari RSyslog, bukan fokus dashboard utama. |

---

## 1.4 Prinsip Utama Sistem

Sistem harus mengikuti prinsip berikut:

| Prinsip | Penjelasan |
|---|---|
| Real-device first | Sistem dibangun untuk device Windows nyata dan VPS nyata, bukan simulasi random log sebagai fokus utama. |
| Dashboard-first | Halaman utama harus langsung menjawab kebutuhan admin: device mana online, siapa user-nya, koneksi Firebird bagaimana, Accurate berjalan atau tidak, dan ada alert apa. |
| Contextual alert | Alert tidak boleh generik. Setiap alert harus memiliki target, evidence, detected_by, impact, dan recommended action. |
| Non-intrusive Accurate audit | Modul Accurate Audit hanya membaca database Firebird secara read-only dan tidak mengubah struktur atau isi database Accurate. |
| No hardcoded device | Device tidak boleh di-hardcode sebagai `WIN-ACC-01` atau `WIN-ACC-02`. Nama tersebut hanya contoh dokumentasi. |
| Manual controlled action | Remote Desktop dan Remote Restart hanya dilakukan manual oleh admin, bukan otomatis oleh sistem. |
| Raw log as advanced view | Raw log tetap disimpan dan bisa ditelusuri, tetapi tidak menjadi pusat dashboard. |
| Sesuai scope skripsi | Sistem tetap merupakan monitoring dan alerting system, bukan IPS, SIEM enterprise, atau sistem auto-remediation kompleks. |

---

# 2. Deskripsi Umum Sistem

## 2.1 Perspektif Produk

Centralized Log Monitoring Dashboard merupakan aplikasi web berbasis Laravel yang dipasang pada VPS Linux. Sistem menerima data operasional dari laptop Windows melalui Windows Agent dan RSyslog, membaca audit trail Accurate dari Firebird, menyimpan data ke MySQL, menampilkan dashboard, dan mengirim Telegram Alert.

Alur sistem utama:

```text
Windows Agent
    ↓ kirim syslog custom
RSyslog Server di VPS
    ↓ tulis raw log
Laravel Parser
    ↓ parsing dan normalisasi
MySQL
    ↓
Dashboard + Alerts + Telegram
```

Alur Accurate Audit:

```text
Laravel Accurate Audit Reader
    ↓ koneksi read-only
Firebird Accurate Database
    ↓ query AUDIT + USERS
MySQL
    ↓
Accurate Audit Dashboard + Alert jika rule jelas
```

Alur remote action:

```text
Admin Dashboard
    ↓ klik action manual
Laravel Remote Command API
    ↓ simpan pending command
Windows Agent polling API
    ↓ execute command jika valid
Agent kirim result
    ↓
Remote Actions Audit Log
```

---

## 2.2 Lingkungan Operasional

### 2.2.1 VPS Linux

VPS Linux menjadi pusat sistem.

Komponen minimal pada VPS:

| Komponen | Fungsi |
|---|---|
| RSyslog Server | Menerima log/status/telemetry dari Windows Agent. |
| Laravel App | Dashboard, parser, alert, remote command API, audit reader. |
| MySQL | Database monitoring. |
| Firebird 2.5 | Database server Accurate 5. |
| ZeroTier client | Menghubungkan VPS dengan laptop Windows dalam jaringan privat. |
| Web server | Nginx/Apache atau Laravel server sesuai deployment. |
| Scheduler/Queue | Menjalankan parser, sync audit, alert check, dan command processing. |

### 2.2.2 Laptop Windows Client

Setiap laptop Windows memiliki:

| Komponen | Fungsi |
|---|---|
| Accurate 5 Client | Aplikasi yang digunakan user untuk mengakses database Accurate. |
| Windows Agent | Mengambil telemetry device dan mengirimnya ke RSyslog. |
| ZeroTier client | Menghubungkan laptop ke VPS dalam jaringan privat. |
| RDP enabled | Memungkinkan admin melakukan remote desktop. |
| Optional OpenSSH/WinRM | Tidak wajib jika remote action memakai agent polling API. |

### 2.2.3 Jaringan

Jaringan antar komponen menggunakan ZeroTier agar VPS dan laptop Windows dapat berkomunikasi seolah satu LAN.

Port yang digunakan secara konseptual:

| Port | Protokol | Digunakan Oleh | Keterangan |
|---:|---|---|---|
| 80/443 | HTTP/HTTPS | Browser → Laravel | Dashboard. |
| 514 atau 5514 | TCP/UDP Syslog | Windows Agent → RSyslog | Pengiriman log/telemetry. |
| 3051 | TCP | Accurate 5 → Firebird | Koneksi database Accurate. |
| 3389 | TCP | Admin → Windows Client | Remote Desktop. |
| 3306 | TCP | Laravel → MySQL | Internal VPS/local. |

Catatan:

- Port Firebird 3051 sebaiknya hanya tersedia melalui ZeroTier, bukan dibuka bebas ke publik.
- Port syslog dapat memakai 5514 di host jika port 514 membutuhkan privilege tambahan.

---

## 2.3 Karakteristik Pengguna

### 2.3.1 Administrator IT

Administrator IT adalah pengguna utama sistem.

Hak akses:

1. Login ke dashboard.
2. Melihat ringkasan kondisi device dan server.
3. Melihat device yang online/offline/warning.
4. Melihat Windows user yang sedang aktif pada tiap device.
5. Melihat status koneksi device ke Firebird.
6. Melihat performa CPU, RAM, disk, uptime, dan heartbeat.
7. Melihat apakah `accurate.exe` berjalan pada device.
8. Melihat audit trail Accurate.
9. Melihat alert dan incident.
10. Menerima Telegram Alert.
11. Melakukan Remote Desktop manual.
12. Melakukan Remote Restart manual dengan konfirmasi.
13. Melihat Advanced Logs untuk investigasi teknis.
14. Mengubah label device dan setting threshold.

### 2.3.2 User Finance / Pengguna Accurate

User Finance bukan pengguna dashboard utama.

User Finance hanya menjadi objek monitoring, misalnya:

1. Windows login user.
2. Pengguna Accurate yang melakukan aktivitas pada audit trail.
3. Device tempat Accurate dijalankan.

User Finance tidak perlu login ke dashboard.

---

## 2.4 Asumsi dan Dependensi

Sistem dibangun dengan asumsi:

1. VPS Linux sudah tersedia dan dapat diakses melalui SSH.
2. Dua laptop Windows tersedia dan dapat dipasang ZeroTier.
3. Accurate 5 dapat dikonfigurasi untuk mengakses database Firebird di VPS.
4. Firebird 2.5 berjalan di VPS dan menyimpan database Accurate.
5. User read-only atau guest untuk Accurate Audit Reader dapat dibuat/digunakan sesuai POC.
6. Windows Agent memiliki permission untuk membaca hostname, user aktif, CPU, RAM, disk, process list, service/RDP status, dan konektivitas ke Firebird.
7. Telegram Bot Token dan Chat ID tersedia.
8. Admin memahami bahwa Remote Restart adalah tindakan manual dan dapat menyebabkan pekerjaan user terganggu.
9. GitHub digunakan untuk version control agar pengembangan bisa dilakukan bertahap.

---

## 2.5 Batasan Sistem

Sistem memiliki batasan berikut:

1. Sistem bukan IPS dan tidak melakukan blocking otomatis.
2. Sistem tidak melakukan auto-restart device atau service tanpa klik manual admin.
3. Sistem tidak menggunakan ELK Stack, Grafana, Prometheus, atau SIEM enterprise.
4. Sistem tidak membaca semua Windows Event Log sebagai sumber utama dashboard.
5. Sistem tidak mengklaim membongkar mekanisme login internal Accurate secara langsung.
6. Sistem tidak menggunakan tabel `LOGIN` sebagai sumber utama username Accurate karena POC menunjukkan tabel tersebut kosong pada database uji.
7. Sistem tidak mengandalkan `COMP_NAME` dan `IPADDRESS` pada tabel `AUDIT` karena field tersebut dapat kosong.
8. Sistem tidak boleh melakukan write/update/delete ke database Accurate.
9. Sistem tidak boleh meng-hardcode device seperti `WIN-ACC-01` atau `WIN-ACC-02` di logic program.
10. Sistem tidak membuat alert berbasis “audit activity spike” pada MVP, kecuali nanti rule dan threshold sudah didefinisikan jelas.
11. Remote Desktop tidak menjamin berhasil jika device offline, RDP service mati, firewall memblokir, atau credential tidak valid.
12. Remote Restart hanya dapat berjalan jika Windows Agent aktif dan menerima command.

---

# 3. Gambaran Modul Sistem

## 3.1 Modul Utama

| Modul | Fungsi |
|---|---|
| Authentication | Login/logout admin dan proteksi dashboard. |
| Device Registry | Mendaftarkan device otomatis berdasarkan agent heartbeat. |
| Windows Agent Telemetry | Menerima dan memproses data dari agent Windows. |
| RSyslog Receiver | Menerima log/telemetry dari Windows Agent. |
| Log Parser | Membaca raw log RSyslog dan menyimpan data terstruktur. |
| Device Monitoring | Menampilkan status device, user, IP, heartbeat, dan agent. |
| Network Monitoring | Menampilkan status koneksi device ke Firebird/VPS. |
| Performance Monitoring | Menampilkan CPU, RAM, disk, uptime, dan indikasi hang. |
| Accurate Process Monitoring | Menampilkan status proses `accurate.exe` pada device. |
| Server Health Monitoring | Mengecek Firebird, RSyslog, Laravel, dan service penting di VPS. |
| Accurate Audit Reader | Membaca audit trail dari Firebird `AUDIT + USERS`. |
| Alerts | Membuat alert kontekstual berdasarkan rule yang jelas. |
| Incidents | Menggabungkan event/alert menjadi indikasi masalah operasional. |
| Telegram Notification | Mengirim alert kontekstual ke Telegram admin. |
| Remote Desktop Launcher | Membantu admin membuka RDP ke device. |
| Remote Restart | Mengirim perintah restart manual ke Windows Agent. |
| Remote Actions Audit | Mencatat seluruh tindakan admin. |
| Advanced Logs | Menampilkan raw/parsed log untuk investigasi teknis. |
| Settings | Mengatur threshold, Telegram, Firebird, dan monitoring interval. |

---

# 4. Kebutuhan Fungsional

Kebutuhan fungsional menggunakan format ID.

Format ID:

```text
FR-[MODUL]-[NOMOR]
```

Contoh:

```text
FR-AUTH-001
FR-DEVICE-001
FR-TELEGRAM-001
```

---

# 4.1 Authentication

## FR-AUTH-001 — Halaman Login

Sistem harus menyediakan halaman login untuk Administrator IT.

Input:

```text
email
password
```

Output:

```text
session login berhasil
atau pesan error jika gagal
```

Acceptance Criteria:

```text
Given admin membuka halaman login
When admin memasukkan email dan password valid
Then sistem mengarahkan admin ke halaman dashboard
```

---

## FR-AUTH-002 — Proteksi Route Dashboard

Sistem harus mencegah user yang belum login mengakses halaman dashboard dan fitur admin.

Protected route minimal:

```text
/dashboard
/devices
/devices/{id}
/accurate-audit
/incidents
/alerts
/remote-actions
/advanced-logs
/settings
```

Acceptance Criteria:

```text
Given user belum login
When user membuka /dashboard
Then sistem redirect ke /login
```

---

## FR-AUTH-003 — Logout

Sistem harus menyediakan fitur logout.

Acceptance Criteria:

```text
Given admin sudah login
When admin klik logout
Then session dihapus dan admin kembali ke halaman login
```

---

## FR-AUTH-004 — Seeder Admin Awal

Sistem harus menyediakan akun admin awal melalui seeder.

Data default boleh berupa:

```text
name: Administrator
email: admin@example.com
password: password
```

Catatan:

- Password harus disimpan dengan hashing Laravel.
- Credential default harus dapat diubah di dokumentasi deployment.

---

# 4.2 Device Registry

## FR-DEVICE-001 — Auto-register Device dari Agent

Sistem harus mendaftarkan device otomatis ketika Windows Agent pertama kali mengirim heartbeat.

Data minimal dari agent:

```text
agent_id
hostname
ip_address
ip_zerotier
windows_user
agent_version
sent_at
```

Jika `agent_id` belum ada, sistem membuat record device baru.

Acceptance Criteria:

```text
Given Windows Agent baru mengirim heartbeat
When parser memproses log tersebut
Then sistem membuat device baru di tabel devices
```

---

## FR-DEVICE-002 — Identitas Device Tidak Boleh Hardcoded

Sistem tidak boleh menggunakan hostname tertentu sebagai logic khusus.

Dilarang:

```php
if ($hostname === 'WIN-ACC-01') { ... }
```

Wajib:

```text
Gunakan agent_id sebagai identitas utama.
Gunakan hostname sebagai metadata.
Gunakan device_label sebagai nama tampilan yang dapat diubah admin.
```

Acceptance Criteria:

```text
Given device memiliki hostname berbeda dari contoh dokumentasi
When device mengirim heartbeat
Then sistem tetap dapat mendaftarkan dan menampilkan device tersebut
```

---

## FR-DEVICE-003 — Admin Dapat Mengubah Device Label

Admin harus dapat mengubah nama tampilan device.

Contoh:

```text
hostname asli: DESKTOP-ABC123
device label: Laptop Finance 1
```

Acceptance Criteria:

```text
Given admin membuka detail device
When admin mengubah label menjadi Laptop Finance 1
Then dashboard menampilkan label tersebut tanpa mengubah hostname asli
```

---

## FR-DEVICE-004 — Status Device Berdasarkan Heartbeat

Sistem harus menentukan status device berdasarkan waktu heartbeat terakhir.

Rule awal:

| Kondisi | Status |
|---|---|
| Last heartbeat <= 2 menit | Online |
| Last heartbeat > 2 menit dan <= 5 menit | Warning |
| Last heartbeat > 5 menit | Offline |
| Belum pernah heartbeat valid | Unknown |

Nilai threshold harus dapat diatur di Settings.

Acceptance Criteria:

```text
Given device terakhir heartbeat 6 menit lalu
When dashboard dibuka
Then device ditampilkan sebagai Offline
```

---

# 4.3 Windows Agent Telemetry

## FR-AGENT-001 — Agent Mengirim Heartbeat

Windows Agent harus mengirim heartbeat berkala ke RSyslog server.

Interval awal:

```text
60 detik
```

Isi heartbeat minimal:

```text
event_type=device_heartbeat
agent_id=<uuid>
hostname=<hostname>
windows_user=<domain\user>
ip_zerotier=<ip>
agent_version=<version>
status=online
sent_at=<timestamp>
```

Acceptance Criteria:

```text
Given Windows Agent berjalan
When interval heartbeat tercapai
Then agent mengirim log heartbeat ke RSyslog server
```

---

## FR-AGENT-002 — Agent Mengirim Performance Telemetry

Windows Agent harus mengirim data performa device.

Data minimal:

```text
event_type=performance_status
agent_id
hostname
cpu_percent
ram_percent
disk_percent
uptime_seconds
last_boot_at
sent_at
```

Acceptance Criteria:

```text
Given Agent berjalan
When telemetry dikirim
Then dashboard menampilkan CPU, RAM, disk, dan uptime terbaru
```

---

## FR-AGENT-003 — Agent Mengirim Network/Firebird Connectivity

Windows Agent harus melakukan pengecekan koneksi ke VPS/Firebird.

Target awal:

```text
VPS ZeroTier IP
Firebird port 3051
```

Data minimal:

```text
event_type=firebird_connectivity
agent_id
hostname
target_host
target_port=3051
ping_status
latency_ms
tcp_status
error_message
sent_at
```

Acceptance Criteria:

```text
Given Firebird port 3051 dapat diakses
When Agent melakukan check
Then sistem menyimpan firebird_status=connected
```

---

## FR-AGENT-004 — Agent Mengirim Accurate Process Status

Windows Agent harus mengecek apakah proses `accurate.exe` berjalan pada device.

Data minimal:

```text
event_type=accurate_process_status
agent_id
hostname
process_name=accurate.exe
process_status=running/not_running
process_owner=<windows_user_if_available>
process_path=<path_if_available>
sent_at
```

Acceptance Criteria:

```text
Given Accurate 5 sedang berjalan di Windows
When Agent mengecek process list
Then dashboard menampilkan Accurate status Running
```

---

## FR-AGENT-005 — Agent Mengirim RDP Status

Windows Agent harus mengecek status dasar Remote Desktop.

Data minimal:

```text
event_type=rdp_status
agent_id
hostname
rdp_service_status=running/stopped/unknown
rdp_port_status=open/closed/unknown
sent_at
```

Acceptance Criteria:

```text
Given RDP service aktif pada Windows
When Agent mengirim status
Then Device Detail menampilkan RDP Available
```

---

## FR-AGENT-006 — Agent Polling Remote Command

Windows Agent harus melakukan polling ke Laravel API untuk mengambil command pending.

Interval awal:

```text
30 detik
```

Endpoint konseptual:

```text
GET /api/agent/commands?agent_id=<uuid>
```

Acceptance Criteria:

```text
Given admin membuat command restart untuk device
When Agent polling command API
Then Agent menerima command tersebut jika agent_id cocok dan command masih pending
```

---

# 4.4 RSyslog Receiver dan Raw Log

## FR-RSYSLOG-001 — RSyslog Server Menerima Log dari Windows Agent

RSyslog Server di VPS harus menerima log dari Windows Agent melalui TCP dan/atau UDP.

Rekomendasi:

```text
TCP lebih diutamakan untuk stabilitas.
UDP boleh disediakan sebagai fallback.
```

Acceptance Criteria:

```text
Given Windows Agent mengirim pesan syslog
When RSyslog Server aktif
Then raw log tersimpan di file /var/log/remote/{hostname}.log atau all.log
```

---

## FR-RSYSLOG-002 — Format Log Harus Terstruktur

Agent harus mengirim pesan yang mudah diparse.

Format yang direkomendasikan:

```text
<timestamp> <hostname> <tag>: key=value key=value key=value
```

Contoh:

```text
2026-05-28T10:10:00+07:00 DESKTOP-ABC123 device-monitor: event_type=device_heartbeat agent_id=8f7... hostname=DESKTOP-ABC123 windows_user=DESKTOP-ABC123\Finance ip_zerotier=10.147.20.11 status=online
```

Acceptance Criteria:

```text
Given log masuk dengan format key=value
When parser membaca log
Then field event_type, agent_id, hostname, dan data lain berhasil diekstrak
```

---

## FR-RSYSLOG-003 — Raw Log Disimpan untuk Audit Teknis

Raw log dari RSyslog harus tetap disimpan agar dapat ditelusuri pada halaman Advanced Logs.

Data minimal raw log:

```text
logged_at
hostname
tag
raw_message
source_file
hash
```

Acceptance Criteria:

```text
Given Agent mengirim heartbeat
When log diproses
Then raw message tetap tersimpan di database logs
```

---

# 4.5 Log Parser

## FR-PARSER-001 — Parser Membaca File RSyslog

Laravel harus memiliki command parser untuk membaca file raw log dari RSyslog.

Nama command:

```bash
php artisan rsyslog:parse
```

Acceptance Criteria:

```text
Given file /var/log/remote/all.log memiliki baris baru
When command rsyslog:parse dijalankan
Then parser membaca baris baru tersebut
```

---

## FR-PARSER-002 — Parser Menggunakan Offset

Parser harus menyimpan posisi terakhir pembacaan file agar tidak membaca ulang seluruh file.

Tabel:

```text
parser_offsets
```

Field minimal:

```text
source_file
last_position
last_line_hash
last_parsed_at
```

Acceptance Criteria:

```text
Given parser sudah membaca file sampai posisi tertentu
When parser dijalankan lagi tanpa log baru
Then tidak ada data duplikat yang masuk
```

---

## FR-PARSER-003 — Parser Membuat Hash Anti-Duplikasi

Setiap raw log harus memiliki hash unik.

Hash dibuat dari kombinasi:

```text
source_file + logged_at + hostname + raw_message
```

Acceptance Criteria:

```text
Given log yang sama diproses dua kali
When hash sudah ada
Then log kedua tidak dimasukkan lagi
```

---

## FR-PARSER-004 — Parser Mengklasifikasikan Event Type

Parser harus mengklasifikasikan log berdasarkan `event_type`.

Event type awal:

| event_type | Modul |
|---|---|
| device_heartbeat | Device Monitoring |
| performance_status | Performance Monitoring |
| firebird_connectivity | Network Monitoring |
| accurate_process_status | Accurate Process Monitoring |
| rdp_status | Remote Desktop Status |
| server_service_status | Server Health Monitoring |
| remote_action_result | Remote Actions |
| unknown | Advanced Logs only |

Acceptance Criteria:

```text
Given raw log memiliki event_type=performance_status
When parser memproses log
Then data masuk ke tabel device_telemetry/performance terkait
```

---

# 4.6 Device Monitoring

## FR-DEVICEMON-001 — Dashboard Menampilkan Device Online

Dashboard harus menampilkan jumlah device online dibanding total device.

Contoh:

```text
Device Online: 2/2
```

Acceptance Criteria:

```text
Given dua device mengirim heartbeat aktif
When dashboard dibuka
Then card Device Online menampilkan 2/2
```

---

## FR-DEVICEMON-002 — Devices Page Menampilkan Daftar Device

Halaman Devices harus menampilkan tabel device.

Kolom minimal:

```text
Device Label
Hostname
Windows User
IP ZeroTier
Agent Status
Last Seen
RDP Status
Accurate Status
Firebird Status
CPU
RAM
Disk
Overall Status
Action
```

Acceptance Criteria:

```text
Given device sudah terdaftar
When admin membuka /devices
Then device tampil dalam tabel dengan status terbaru
```

---

## FR-DEVICEMON-003 — Device Detail Menampilkan Informasi Lengkap

Halaman Device Detail harus menampilkan:

1. Identitas device.
2. Windows user aktif.
3. IP ZeroTier.
4. Agent status.
5. Last heartbeat.
6. Performance terakhir.
7. Firebird connectivity terakhir.
8. Accurate process terakhir.
9. RDP status.
10. Recent alerts/incidents untuk device tersebut.
11. Tombol Remote Desktop dan Restart Client.

Acceptance Criteria:

```text
Given admin klik detail device
When halaman terbuka
Then seluruh informasi monitoring device tampil secara terstruktur
```

---

# 4.7 Network dan Firebird Connectivity

## FR-NET-001 — Agent Mengecek Ping ke VPS

Agent harus dapat mengecek apakah VPS dapat dijangkau.

Data:

```text
ping_status=ok/failed
latency_ms
packet_loss_optional
```

Acceptance Criteria:

```text
Given VPS ZeroTier IP reachable
When Agent melakukan ping check
Then dashboard menampilkan Ping OK dan latency
```

---

## FR-NET-002 — Agent Mengecek TCP Port Firebird 3051

Agent harus mengecek apakah port Firebird 3051 di VPS dapat diakses.

Status:

```text
connected
timeout
refused
unknown
```

Acceptance Criteria:

```text
Given port 3051 terbuka
When Agent melakukan tcp check
Then firebird_status menjadi Connected
```

---

## FR-NET-003 — Sistem Membuat Alert Jika Client Gagal Terhubung ke Firebird

Sistem harus membuat alert jika device gagal akses Firebird berdasarkan rule yang jelas.

Rule awal:

```text
Jika tcp_status=timeout atau refused sebanyak 3 kali berturut-turut dalam 5 menit
maka alert FIREBIRD_PORT_TIMEOUT dibuat dengan severity ERROR.
```

Format alert:

```text
Title: WIN-ACC device gagal terhubung ke Firebird VPS:3051
Target: Device → VPS-FIREBIRD
Detected by: Windows Agent
Evidence: tcp_status=timeout 3 kali dalam 5 menit
Recommended action: Cek koneksi ZeroTier, Firebird service, atau firewall VPS
```

Acceptance Criteria:

```text
Given WIN-ACC-02 gagal tcp check ke port 3051 sebanyak 3 kali
When AlertDetectionService berjalan
Then alert FIREBIRD_PORT_TIMEOUT dibuat untuk WIN-ACC-02
```

---

# 4.8 Performance Monitoring

## FR-PERF-001 — Sistem Menyimpan CPU, RAM, dan Disk

Sistem harus menyimpan telemetry performa device.

Data minimal:

```text
cpu_percent
ram_percent
disk_percent
uptime_seconds
last_boot_at
recorded_at
```

Acceptance Criteria:

```text
Given Agent mengirim performance_status
When parser memproses log
Then data performa tersimpan dan tampil di dashboard
```

---

## FR-PERF-002 — Alert CPU Tinggi

Rule awal:

| Kondisi | Severity |
|---|---|
| CPU >= 80% selama 3 telemetry berturut-turut | WARNING |
| CPU >= 90% selama 3 telemetry berturut-turut | CRITICAL |

Alert harus menyebut device dan evidence.

Contoh:

```text
WARNING - CPU tinggi pada Laptop Finance 2
Target: Laptop Finance 2 / DESKTOP-XYZ
Evidence: CPU 87%, 85%, 86% pada 3 telemetry terakhir
```

Acceptance Criteria:

```text
Given device mengirim CPU >= 80% tiga kali berturut-turut
When AlertDetectionService berjalan
Then alert CPU_HIGH dibuat untuk device tersebut
```

---

## FR-PERF-003 — Alert RAM Tinggi

Rule awal:

```text
RAM >= 85% selama 3 telemetry berturut-turut → WARNING
RAM >= 95% selama 3 telemetry berturut-turut → CRITICAL
```

Acceptance Criteria:

```text
Given RAM device >= 85% tiga kali berturut-turut
When AlertDetectionService berjalan
Then alert RAM_HIGH dibuat
```

---

## FR-PERF-004 — Indikasi Device Lambat / Hang Indicator

Sistem boleh membuat incident indikasi device lambat jika beberapa kondisi terjadi bersamaan.

Rule awal untuk incident, bukan alert tunggal:

```text
CPU >= 85%
+ RAM >= 85%
+ Firebird latency tinggi atau tcp timeout
+ heartbeat terlambat
= Incident DEVICE_SLOW_INDICATION
```

Catatan:

- Ini masuk menu Incidents.
- Harus menyimpan evidence dari masing-masing kondisi.
- Tidak boleh langsung melakukan restart otomatis.

Acceptance Criteria:

```text
Given device mengalami CPU tinggi, RAM tinggi, dan Firebird latency tinggi
When IncidentCorrelationService berjalan
Then incident DEVICE_SLOW_INDICATION dibuat dengan evidence lengkap
```

---

# 4.9 Accurate Process Monitoring

## FR-ACCPROC-001 — Menampilkan Status Accurate.exe

Sistem harus menampilkan apakah `accurate.exe` berjalan pada setiap device Windows.

Status:

```text
running
not_running
unknown
```

Acceptance Criteria:

```text
Given accurate.exe berjalan pada device
When Agent mengirim process status
Then dashboard menampilkan Accurate Running
```

---

## FR-ACCPROC-002 — Alert Accurate Tidak Berjalan Saat Jam Kerja

Rule awal:

```text
Jika accurate.exe tidak berjalan saat jam kerja
DAN Windows user aktif terdeteksi
DAN device online
maka buat alert ACCURATE_PROCESS_NOT_DETECTED dengan severity WARNING.
```

Jam kerja default:

```text
Senin-Jumat 08:00-17:00
```

Jam kerja harus dapat dikonfigurasi di Settings.

Format alert:

```text
WARNING - Accurate 5 tidak berjalan pada Laptop Finance 2
Target: Laptop Finance 2 / DESKTOP-XYZ
Detected by: Windows Agent
Evidence: process accurate.exe tidak ditemukan, user=DESKTOP-XYZ\Finance
Recommended action: Remote Desktop untuk memeriksa aplikasi user
```

Acceptance Criteria:

```text
Given device online, user aktif, dan accurate.exe tidak berjalan pada jam kerja
When AlertDetectionService berjalan
Then alert ACCURATE_PROCESS_NOT_DETECTED dibuat
```

---

# 4.10 Server Health Monitoring

## FR-SERVER-001 — Sistem Mengecek Service Firebird di VPS

Sistem harus mengecek status service Firebird di VPS.

Data minimal:

```text
service_name=firebird
service_status=running/inactive/failed/unknown
port_3051_status=open/closed
checked_at
```

Pengecekan dapat dilakukan oleh Laravel command atau script server checker.

Acceptance Criteria:

```text
Given Firebird service aktif
When server checker berjalan
Then dashboard menampilkan Firebird Service Running
```

---

## FR-SERVER-002 — Alert Firebird Service Down

Rule awal:

```text
Jika service_status=inactive/failed atau port_3051_status=closed
maka buat alert FIREBIRD_SERVICE_DOWN dengan severity CRITICAL.
```

Format alert:

```text
CRITICAL - Service Firebird pada VPS tidak aktif
Target: VPS-FIREBIRD
Detected by: Server Health Checker
Evidence: service_status=inactive, port_3051=closed
Impact: Client Accurate tidak dapat mengakses database
Recommended action: Cek VPS atau restart service Firebird secara manual
```

Acceptance Criteria:

```text
Given Firebird service inactive
When ServerHealthChecker berjalan
Then alert FIREBIRD_SERVICE_DOWN dibuat
```

---

## FR-SERVER-003 — Sistem Mengecek RSyslog Server

Sistem harus mengecek apakah RSyslog server berjalan.

Acceptance Criteria:

```text
Given RSyslog service tidak berjalan
When server checker berjalan
Then dashboard menampilkan status RSyslog bermasalah dan alert dibuat
```

---

# 4.11 Accurate Audit Reader

## FR-AUDIT-001 — Accurate Audit Tidak Lewat RSyslog

Sistem harus membaca audit trail Accurate langsung dari Firebird secara read-only.

Dilarang:

```text
Mengirim audit trail Accurate melalui RSyslog sebagai sumber utama.
Mengambil username internal Accurate dari TCP connection.
Menggunakan tabel LOGIN sebagai sumber utama.
```

Wajib:

```text
Menggunakan query AUDIT + USERS sesuai dokumen POC.
```

Acceptance Criteria:

```text
Given Accurate Audit Reader berjalan
When data AUDIT tersedia
Then data audit masuk ke tabel accurate_audit_events tanpa melalui RSyslog
```

---

## FR-AUDIT-002 — Query Utama AUDIT + USERS

Sistem harus menggunakan relasi:

```text
AUDIT.USERID → USERS.USERID
```

Field minimal yang dibaca:

```text
AUDITID
MODIDATE AS ACTIVITY_TIME
USERNAME
FULLNAME
SOURCE
TRANSTYPE
TRANSDESCRIPTION
INVOICENO
COMP_NAME
IPADDRESS
APPVERSION
STATUS
```

Acceptance Criteria:

```text
Given tabel AUDIT dan USERS berisi data
When audit reader menjalankan query
Then username Accurate dan aktivitas user terbaca
```

---

## FR-AUDIT-003 — LOGIN Tidak Digunakan sebagai Sumber Utama

Sistem tidak boleh bergantung pada tabel `LOGIN` untuk membaca username internal Accurate.

Alasan:

```text
POC menunjukkan tabel LOGIN tersedia secara struktur tetapi kosong pada database uji.
```

Acceptance Criteria:

```text
Given tabel LOGIN kosong
When audit reader berjalan
Then sistem tetap dapat menampilkan audit berdasarkan AUDIT + USERS
```

---

## FR-AUDIT-004 — Field COMP_NAME dan IPADDRESS Tidak Wajib

Sistem tidak boleh menganggap `COMP_NAME` dan `IPADDRESS` pada tabel `AUDIT` selalu terisi.

Acceptance Criteria:

```text
Given COMP_NAME dan IPADDRESS kosong pada AUDIT
When data audit ditampilkan
Then dashboard tetap menampilkan data audit tanpa error
```

---

## FR-AUDIT-005 — Incremental Sync Audit

Audit Reader harus membaca data baru secara incremental.

Strategi awal:

```text
Simpan last_audit_id dan/atau last_activity_time.
Baca data AUDIT yang lebih baru dari posisi terakhir.
```

Tabel pendukung:

```text
accurate_audit_sync_state
```

Acceptance Criteria:

```text
Given audit reader sudah membaca sampai AUDITID 100
When audit reader berjalan lagi
Then hanya data setelah AUDITID 100 yang dibaca
```

---

## FR-AUDIT-006 — Accurate Audit Page

Sistem harus menyediakan halaman Accurate Audit.

Kolom tabel:

```text
Activity Time
Accurate Username
Full Name
Source / Module
Transaction Type
Transaction Description
Invoice No / Reference
App Version
Status
Action Detail
```

Filter:

```text
Tanggal mulai
Tanggal akhir
Username Accurate
Source / Module
Transaction Type
Keyword
```

Acceptance Criteria:

```text
Given audit events tersimpan
When admin membuka /accurate-audit
Then daftar audit tampil dengan filter yang dapat digunakan
```

---

## FR-AUDIT-007 — Alert Audit Hanya Jika Rule Jelas

Sistem boleh membuat alert dari audit Accurate hanya untuk rule yang eksplisit.

Rule awal yang diperbolehkan:

```text
Jika TRANSTYPE menunjukkan DELETE atau penghapusan transaksi
maka buat alert ACCURATE_AUDIT_DELETE dengan severity CRITICAL.
```

Catatan:

- Rule ini hanya aktif jika field `TRANSTYPE` atau `TRANSDESCRIPTION` benar-benar bisa mengindikasikan penghapusan.
- Jika tidak bisa dibuktikan dari data, rule harus disabled.
- Sistem tidak boleh membuat alert “audit activity spike detected” pada MVP.

Acceptance Criteria:

```text
Given audit event memiliki indikasi delete yang jelas
When audit alert detector berjalan
Then alert ACCURATE_AUDIT_DELETE dibuat dengan evidence event audit
```

---

# 4.12 Alerts

## FR-ALERT-001 — Alert Harus Kontekstual

Setiap alert wajib memiliki field:

```text
alert_code
title
severity
target_type
target_id
target_name
detected_by
evidence
impact
recommended_action
status
detected_at
```

Acceptance Criteria:

```text
Given alert dibuat
When admin membuka detail alert
Then target, evidence, impact, dan recommended action terlihat jelas
```

---

## FR-ALERT-002 — Alert Tidak Boleh Generik

Sistem tidak boleh membuat alert dengan judul ambigu seperti:

```text
Firebird unreachable
CPU high
Accurate not running
```

Wajib menyebut target:

```text
WIN-ACC-02 gagal terhubung ke Firebird VPS:3051
CPU tinggi pada Laptop Finance 2
Accurate 5 tidak berjalan pada DESKTOP-XYZ
```

Acceptance Criteria:

```text
Given alert muncul di dashboard
When admin membacanya
Then admin langsung mengetahui target device/server dan bukti masalahnya
```

---

## FR-ALERT-003 — Status Alert

Alert memiliki status:

```text
open
acknowledged
resolved
```

Acceptance Criteria:

```text
Given alert berstatus open
When admin klik acknowledge
Then status berubah menjadi acknowledged dan acknowledged_at terisi
```

---

## FR-ALERT-004 — Anti-Spam Alert

Sistem tidak boleh membuat alert yang sama berulang terlalu cepat.

Rule awal:

```text
alert_code + target_id + severity yang sama tidak dibuat ulang dalam 5 menit jika alert sebelumnya masih open.
```

Acceptance Criteria:

```text
Given alert CPU_HIGH untuk device X sudah open
When kondisi CPU tinggi masih berulang dalam 5 menit
Then sistem tidak membuat alert duplikat baru
```

---

# 4.13 Incidents

## FR-INCIDENT-001 — Incident Dibuat dari Korelasi Beberapa Evidence

Incident adalah hasil korelasi, bukan satu log tunggal.

Contoh incident:

```text
DEVICE_SLOW_INDICATION
```

Evidence:

```text
CPU tinggi
RAM tinggi
Firebird latency tinggi
heartbeat delay
```

Acceptance Criteria:

```text
Given beberapa alert/event terkait device yang sama terjadi dalam rentang waktu tertentu
When IncidentCorrelationService berjalan
Then incident dibuat dengan daftar evidence
```

---

## FR-INCIDENT-002 — Incident Page

Sistem harus menyediakan halaman Incidents.

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

Acceptance Criteria:

```text
Given incident dibuat
When admin membuka /incidents
Then incident tampil dengan summary dan status
```

---

# 4.14 Telegram Notification

## FR-TG-001 — Telegram Alert Wajib

Sistem harus menyediakan Telegram Alert sebagai fitur wajib.

Telegram bukan fitur opsional dalam scope v2.

Acceptance Criteria:

```text
Given alert critical dibuat
When Telegram aktif
Then sistem mengirim pesan Telegram ke admin
```

---

## FR-TG-002 — Format Telegram Harus Kontekstual

Format Telegram minimal:

```text
[SEVERITY] Judul Alert

Target      : ...
Detected by : ...
Evidence    : ...
Impact      : ...
Action      : ...
Time        : ...
Dashboard   : ...
```

Contoh:

```text
[CRITICAL] Service Firebird pada VPS tidak aktif

Target      : VPS-FIREBIRD
Detected by : Server Health Checker
Evidence    : service_status=inactive, port_3051=closed
Impact      : Client Accurate tidak dapat mengakses database
Action      : Cek VPS atau restart service Firebird secara manual
Time        : 2026-05-28 19:51
Dashboard   : https://domain/alerts/123
```

Acceptance Criteria:

```text
Given Telegram dikirim
When admin membaca pesan
Then admin mengetahui target, bukti, dampak, dan tindakan yang disarankan
```

---

## FR-TG-003 — Riwayat Pengiriman Telegram

Sistem harus menyimpan riwayat pengiriman Telegram.

Data minimal:

```text
alert_id
channel=telegram
recipient
message
status
sent_at
error_message
```

Acceptance Criteria:

```text
Given Telegram berhasil dikirim
When admin membuka detail alert
Then status pengiriman Telegram terlihat sebagai sent
```

---

## FR-TG-004 — Anti-Spam Telegram

Telegram tidak boleh mengirim pesan duplikat berlebihan.

Rule awal:

```text
Telegram untuk alert_code + target_id yang sama tidak dikirim ulang dalam 5 menit kecuali severity naik.
```

Acceptance Criteria:

```text
Given alert yang sama muncul berulang
When cooldown masih aktif
Then Telegram tidak dikirim ulang
```

---

# 4.15 Remote Desktop

## FR-RDP-001 — Tombol Remote Desktop Selalu Tersedia di Device Detail

Device Detail harus menyediakan tombol Remote Desktop.

Jika device online dan RDP available:

```text
Tombol aktif
```

Jika device offline atau RDP unknown:

```text
Tombol disabled atau tampil warning
```

Acceptance Criteria:

```text
Given device online dan rdp_status=available
When admin membuka detail device
Then tombol Remote Desktop aktif
```

---

## FR-RDP-002 — Remote Desktop Menggunakan IP ZeroTier

Remote Desktop harus menggunakan IP ZeroTier device sebagai target.

Contoh command:

```text
mstsc /v:10.147.20.11
```

Sistem dapat menyediakan:

1. Copy command.
2. Generate file `.rdp`.
3. Link instruksi manual.

Acceptance Criteria:

```text
Given device memiliki IP ZeroTier
When admin klik Remote Desktop
Then sistem menampilkan command atau file RDP dengan IP tersebut
```

---

## FR-RDP-003 — Aksi Remote Desktop Dicatat

Setiap klik Remote Desktop harus dicatat ke Remote Actions.

Data:

```text
action_type=OPEN_RDP
admin_id
device_id
status=initiated
requested_at
```

Acceptance Criteria:

```text
Given admin klik Remote Desktop
When action dibuat
Then record remote action tersimpan
```

---

# 4.16 Remote Restart

## FR-RESTART-001 — Restart Client Manual oleh Admin

Sistem harus menyediakan fitur Restart Client manual.

Syarat:

1. Admin sudah login.
2. Device terdaftar.
3. Agent device aktif atau terakhir reachable.
4. Admin mengisi alasan restart.
5. Admin menyetujui konfirmasi.

Acceptance Criteria:

```text
Given admin membuka detail device
When admin klik Restart Client dan mengisi alasan
Then sistem membuat pending remote command untuk device tersebut
```

---

## FR-RESTART-002 — Tidak Ada Auto Restart

Sistem tidak boleh melakukan restart otomatis berdasarkan alert atau incident.

Acceptance Criteria:

```text
Given device critical
When admin tidak klik Restart
Then sistem tidak mengirim command restart
```

---

## FR-RESTART-003 — Agent Mengeksekusi Restart Command

Windows Agent harus dapat mengambil command restart dari API.

Command konseptual:

```text
RESTART_CLIENT
```

Eksekusi Windows:

```powershell
shutdown /r /t 30 /c "Restart requested by IT Admin from Centralized Log Monitoring Dashboard"
```

Acceptance Criteria:

```text
Given Agent menerima command RESTART_CLIENT yang valid
When Agent mengeksekusi command
Then Windows menjadwalkan restart dan Agent mengirim result
```

---

## FR-RESTART-004 — Semua Restart Dicatat

Sistem harus mencatat semua remote restart.

Data minimal:

```text
admin_id
device_id
action_type=RESTART_CLIENT
reason
status
requested_at
executed_at
result_message
```

Acceptance Criteria:

```text
Given restart command dieksekusi
When Agent mengirim result
Then remote action status berubah menjadi executed/success/failed
```

---

# 4.17 Remote Actions Page

## FR-REMOTE-001 — Halaman Remote Actions

Sistem harus menyediakan halaman Remote Actions untuk melihat riwayat tindakan admin.

Kolom:

```text
Waktu
Admin
Target Device
Action Type
Reason
Status
Requested At
Executed At
Result
```

Filter:

```text
Tanggal
Device
Action Type
Status
Admin
```

Acceptance Criteria:

```text
Given ada action Remote Desktop atau Restart
When admin membuka /remote-actions
Then riwayat action tampil
```

---

# 4.18 Dashboard

## FR-DASH-001 — Dashboard Menampilkan Ringkasan Real-Device

Dashboard utama harus menampilkan summary:

```text
Device Online
Firebird Connected
Accurate Running
Open Alerts
Open Incidents
Audit Events Today
```

Acceptance Criteria:

```text
Given data monitoring tersedia
When admin membuka /dashboard
Then summary real-device tampil tanpa raw log dominan
```

---

## FR-DASH-002 — Dashboard Menampilkan Device Health Table

Dashboard harus menampilkan tabel ringkas device.

Kolom:

```text
Device Label
Windows User
Status
CPU
RAM
Firebird
Accurate
Last Seen
Action
```

Acceptance Criteria:

```text
Given dua device terdaftar
When dashboard dibuka
Then keduanya tampil di Device Health Table
```

---

## FR-DASH-003 — Dashboard Menampilkan Accurate Audit Realtime

Dashboard harus menampilkan beberapa audit Accurate terbaru.

Kolom ringkas:

```text
Activity Time
Accurate User
Activity / Description
Invoice / Reference
```

Acceptance Criteria:

```text
Given audit events tersimpan
When dashboard dibuka
Then audit terbaru tampil secara ringkas
```

---

## FR-DASH-004 — Dashboard Tidak Menampilkan Raw Log sebagai Fokus Utama

Dashboard utama tidak boleh dipenuhi raw logs panjang.

Raw log hanya boleh muncul sebagai:

```text
jumlah ringkasan
link ke Advanced Logs
latest event ringkas jika relevan
```

Acceptance Criteria:

```text
Given raw logs banyak
When dashboard dibuka
Then dashboard tetap fokus ke status device, koneksi, performance, audit, alert, dan incident
```

---

# 4.19 Accurate Audit Page

## FR-ACCAUDIT-PAGE-001 — Daftar Audit Accurate

Halaman Accurate Audit harus menampilkan audit trail dari Firebird.

Route konseptual:

```text
/accurate-audit
```

Kolom:

```text
Activity Time
Accurate Username
Full Name
Source
Transaction Type
Description
Invoice No
App Version
Status
Action
```

Acceptance Criteria:

```text
Given Accurate Audit Reader berhasil sync data
When admin membuka /accurate-audit
Then data audit tampil dalam tabel
```

---

## FR-ACCAUDIT-PAGE-002 — Detail Audit Accurate

Admin dapat membuka detail audit.

Detail:

```text
Audit ID
Activity Time
Accurate Username
Full Name
Source
Transaction Type
Transaction Description
Invoice No
Comp Name jika ada
IP Address jika ada
App Version
Status
Raw payload / stored fields
```

Acceptance Criteria:

```text
Given admin klik detail audit
When halaman detail terbuka
Then seluruh field audit yang tersimpan tampil
```

---

# 4.20 Advanced Logs

## FR-LOGS-001 — Advanced Logs Menampilkan Raw/Parsed Logs

Sistem harus menyediakan halaman Advanced Logs.

Route konseptual:

```text
/advanced-logs
```

Kolom:

```text
Logged At
Hostname
Tag
Event Type
Severity
Category
Raw Message
Parsed Message
Source File
Action
```

Acceptance Criteria:

```text
Given raw logs tersimpan
When admin membuka /advanced-logs
Then log tampil dengan filter
```

---

## FR-LOGS-002 — Filter Advanced Logs

Filter:

```text
Tanggal mulai
Tanggal akhir
Hostname
Event Type
Severity
Keyword
```

Acceptance Criteria:

```text
Given admin memilih event_type=performance_status
When filter diterapkan
Then hanya log performance yang tampil
```

---

# 4.21 Settings

## FR-SETTINGS-001 — Settings Monitoring Threshold

Admin harus dapat mengatur threshold:

```text
heartbeat_warning_minutes
heartbeat_offline_minutes
cpu_warning_percent
cpu_critical_percent
ram_warning_percent
ram_critical_percent
firebird_latency_warning_ms
firebird_latency_critical_ms
alert_cooldown_minutes
telegram_cooldown_minutes
```

Acceptance Criteria:

```text
Given admin mengubah CPU warning threshold menjadi 85
When telemetry CPU 82 masuk
Then alert CPU_HIGH tidak dibuat
```

---

## FR-SETTINGS-002 — Settings Telegram

Admin harus dapat mengatur Telegram:

```text
telegram_enabled
telegram_bot_token
telegram_chat_id
telegram_send_warning
telegram_send_error
telegram_send_critical
```

Acceptance Criteria:

```text
Given Telegram enabled dan token valid
When alert critical dibuat
Then Telegram terkirim
```

---

## FR-SETTINGS-003 — Settings Firebird Audit Reader

Admin/developer harus dapat mengatur konfigurasi Firebird melalui env/config, bukan hardcoded.

Setting:

```text
FIREBIRD_HOST
FIREBIRD_PORT
FIREBIRD_DATABASE_PATH
FIREBIRD_USERNAME
FIREBIRD_PASSWORD
FIREBIRD_SYNC_INTERVAL
```

Acceptance Criteria:

```text
Given env Firebird dikonfigurasi
When audit reader berjalan
Then koneksi Firebird menggunakan env tersebut
```

---

# 5. Kebutuhan Data

Bagian ini menjelaskan data utama yang wajib disiapkan pada database design v2. Struktur tabel detail akan dijelaskan pada `04_Database_Design_v2.md`.

## 5.1 Tabel Utama

| Tabel | Fungsi |
|---|---|
| users | Akun admin. |
| devices | Master device Windows. |
| device_heartbeats | Riwayat heartbeat device. |
| device_telemetry | Riwayat CPU/RAM/disk/uptime. |
| device_network_checks | Riwayat ping dan Firebird connectivity. |
| device_process_statuses | Riwayat status `accurate.exe`. |
| server_service_statuses | Status service VPS seperti Firebird dan RSyslog. |
| logs | Raw/parsed logs dari RSyslog untuk Advanced Logs. |
| parser_offsets | Posisi pembacaan file raw log. |
| parser_runs | Riwayat eksekusi parser. |
| accurate_audit_events | Data audit Accurate hasil query Firebird. |
| accurate_audit_sync_states | Posisi sync terakhir audit reader. |
| alerts | Alert kontekstual. |
| incidents | Incident hasil korelasi. |
| incident_evidences | Evidence incident. |
| alert_notifications | Riwayat Telegram. |
| remote_actions | Riwayat RDP/restart/action admin. |
| remote_commands | Command pending untuk agent. |
| settings | Threshold dan konfigurasi. |

---

## 5.2 Field Penting Device

```text
id
agent_id
hostname
device_label
ip_address
ip_zerotier
windows_user
agent_version
agent_status
rdp_status
accurate_status
firebird_status
overall_status
last_seen_at
created_at
updated_at
```

---

## 5.3 Field Penting Alert

```text
id
alert_code
severity
title
description
target_type
target_id
target_name
detected_by
evidence_json
impact
recommended_action
status
detected_at
acknowledged_at
resolved_at
created_at
updated_at
```

---

## 5.4 Field Penting Accurate Audit

```text
id
source_audit_id
activity_time
accurate_username
accurate_fullname
source
transaction_type
transaction_description
invoice_no
comp_name
ip_address
app_version
status
raw_payload
synced_at
created_at
updated_at
```

---

# 6. Kebutuhan Antarmuka Pengguna

Detail wireframe lengkap akan dibuat pada dokumen `08_UI_Wireframe_v2.md`.

Namun SRS ini mengunci halaman utama:

```text
/login
/dashboard
/devices
/devices/{id}
/accurate-audit
/accurate-audit/{id}
/incidents
/alerts
/alerts/{id}
/remote-actions
/advanced-logs
/settings
```

## 6.1 Sidebar Menu

Menu sidebar final:

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

## 6.2 Prinsip UI

1. Clean.
2. Professional.
3. Status-oriented.
4. Dashboard-first.
5. Tidak ramai dengan raw log panjang.
6. Setiap status memiliki badge dan warna konsisten.
7. Setiap alert harus bisa dipahami tanpa membuka raw log terlebih dahulu.

---

# 7. Kebutuhan Non-Fungsional

## NFR-001 — Performance

Dashboard harus dapat dibuka dalam waktu wajar untuk jumlah device kecil.

Target awal:

```text
2-10 device
ribuan log per hari
puluhan-ratusan audit event per hari
```

---

## NFR-002 — Reliability

Sistem harus tetap berjalan walaupun:

1. Salah satu device Windows offline.
2. Agent berhenti mengirim heartbeat.
3. Telegram gagal dikirim.
4. Firebird Audit Reader gagal connect sementara.
5. RSyslog menerima log tidak dikenal.

Sistem harus mencatat error tanpa menghentikan aplikasi utama.

---

## NFR-003 — Security

Kebutuhan keamanan:

1. Dashboard hanya dapat diakses admin login.
2. Credential database tidak boleh hardcoded di source code.
3. Firebird access untuk audit harus read-only.
4. Remote restart wajib konfirmasi dan alasan.
5. Remote command harus hanya diterima oleh agent dengan agent_id yang valid.
6. Telegram token disimpan di environment/config, bukan hardcoded.
7. Port Firebird sebaiknya hanya melalui ZeroTier.

---

## NFR-004 — Maintainability

Kode harus modular:

```text
DeviceService
TelemetryParserService
AlertDetectionService
IncidentCorrelationService
TelegramService
RemoteCommandService
AccurateAuditReaderService
ServerHealthCheckService
```

Setiap service harus mudah diuji dan tidak mencampur logic UI dengan logic parsing/detection.

---

## NFR-005 — Auditability

Sistem harus menyimpan:

1. Raw log.
2. Parsed event.
3. Alert evidence.
4. Telegram notification history.
5. Remote action history.
6. Accurate audit raw payload.

Tujuannya agar admin dapat menelusuri dasar munculnya alert atau incident.

---

## NFR-006 — No Hardcoded Runtime Data

Tidak boleh hardcode:

```text
nama device
IP device
IP VPS
credential Firebird
Telegram token
chat id
threshold
jam kerja
```

Semua harus dari:

```text
.env
settings table
admin configuration
agent registration
```

---

# 8. Kebutuhan Integrasi

## 8.1 Integrasi Windows Agent → RSyslog

Windows Agent mengirim log custom ke RSyslog.

Minimal event:

```text
device_heartbeat
performance_status
firebird_connectivity
accurate_process_status
rdp_status
remote_action_result
```

---

## 8.2 Integrasi Laravel → Firebird

Laravel Accurate Audit Reader mengakses Firebird.

Prinsip:

```text
read-only
query AUDIT + USERS
incremental sync
error tolerant
no schema modification
```

---

## 8.3 Integrasi Laravel → Telegram

Laravel mengirim Telegram saat alert memenuhi rule.

Wajib:

```text
contextual message
cooldown
notification history
error handling
```

---

## 8.4 Integrasi Laravel → Windows Agent Command API

Windows Agent mengambil command dari Laravel API.

Prinsip:

```text
agent polling
command scoped by agent_id
command status tracked
no command if disabled
manual admin only
```

---

# 9. Kriteria Penerimaan Global

Sistem dianggap memenuhi SRS v2 jika:

1. Admin dapat login ke dashboard.
2. Dua laptop Windows nyata dapat terdaftar otomatis melalui Agent.
3. Dashboard menampilkan status device real, bukan data random.
4. Device page menampilkan Windows user, IP ZeroTier, heartbeat, CPU, RAM, disk, Firebird connectivity, dan Accurate process.
5. RSyslog server menerima log dari Windows Agent.
6. Parser menyimpan raw log dan parsed data tanpa duplikasi.
7. Accurate Audit Reader membaca data dari Firebird `AUDIT + USERS` secara read-only.
8. Accurate Audit page menampilkan aktivitas Accurate terbaru.
9. Alert yang muncul selalu memiliki target dan evidence.
10. Telegram mengirim alert kontekstual.
11. Remote Desktop action dapat dibuat/dicatat.
12. Remote Restart manual dapat dibuat, diterima agent, dieksekusi, dan dicatat.
13. Advanced Logs tersedia untuk investigasi teknis.
14. Tidak ada logic hardcoded terhadap device tertentu.
15. Sistem tidak melakukan auto blocking atau auto restart.

---

# 10. Prioritas Implementasi

Untuk Codex/developer, implementasi harus dilakukan bertahap.

## Phase 1 — Core Laravel dan Database

Target:

```text
Laravel app
Auth
Layout dashboard
Migrations core
Seeder admin
GitHub repo setup
```

Modul:

```text
users
devices
logs
alerts
settings
```

---

## Phase 2 — RSyslog dan Parser

Target:

```text
RSyslog server di VPS
raw log path
parser command php artisan rsyslog:parse
parser offset
anti-duplicate hash
```

---

## Phase 3 — Windows Agent Basic

Target:

```text
agent_id
heartbeat
hostname
windows_user
ip_zerotier
performance telemetry
firebird connectivity
accurate process status
```

---

## Phase 4 — Device Dashboard

Target:

```text
Dashboard summary
Devices page
Device detail
Advanced Logs
```

---

## Phase 5 — Alerts dan Telegram

Target:

```text
AlertDetectionService
Contextual alert format
TelegramService
alert notification history
cooldown
```

---

## Phase 6 — Accurate Audit Reader

Target:

```text
Firebird connection
AUDIT + USERS query
incremental sync
accurate_audit_events table
Accurate Audit page
```

---

## Phase 7 — Remote Actions

Target:

```text
Remote Desktop launcher
Remote actions log
Remote command API
Agent command polling
Restart Client manual
```

---

## Phase 8 — Incidents dan Polish

Target:

```text
Incident correlation
UI polish
Settings page
Testing
Demo script real-device
```

---

# 11. Hal yang Tidak Boleh Dilakukan AI Agent / Codex

AI Agent / Codex tidak boleh:

1. Mengubah stack utama menjadi React/Node backend tanpa instruksi.
2. Mengganti RSyslog dengan ELK/Grafana/Prometheus.
3. Membuat client simulasi sebagai fokus utama sistem v2.
4. Membuat dashboard raw log sebagai halaman utama.
5. Mengambil username internal Accurate dari TCP connection.
6. Menggunakan tabel `LOGIN` sebagai sumber utama audit.
7. Menganggap `COMP_NAME` dan `IPADDRESS` selalu terisi.
8. Mengubah database Accurate atau membuat trigger/tabel baru di database Accurate.
9. Mengirim remote restart otomatis berdasarkan alert.
10. Membuat alert tanpa target dan evidence.
11. Membuat “audit activity spike detected” tanpa definisi threshold dan perhitungan.
12. Meng-hardcode device name, IP, Telegram token, credential, atau threshold.

---

# 12. Ringkasan Requirement Utama

| Area | Requirement Utama |
|---|---|
| Device | Auto-register dari Windows Agent, tanpa hardcode. |
| RSyslog | Menerima log terstruktur dari Windows Agent. |
| Parser | Parse key=value, simpan raw log, anti-duplikasi. |
| Dashboard | Fokus status real-device, bukan raw log. |
| Network | Cek ping dan Firebird port 3051 dari tiap device. |
| Performance | CPU, RAM, disk, uptime, heartbeat. |
| Accurate Process | Cek `accurate.exe` pada tiap Windows client. |
| Accurate Audit | Direct read-only Firebird `AUDIT + USERS`. |
| Alerts | Kontekstual, target jelas, evidence wajib. |
| Telegram | Wajib, contextual proactive alert. |
| Remote Desktop | Manual action, memakai IP ZeroTier. |
| Remote Restart | Manual controlled, lewat agent polling API. |
| Advanced Logs | Raw/parsed logs untuk investigasi teknis. |

---

# 13. Penutup

SRS v2 ini menjadi spesifikasi teknis awal untuk membangun ulang **Centralized Log Monitoring Dashboard** berbasis real device.

Fokus utama sistem adalah membantu Administrator IT memahami kondisi operasional device Windows pengguna Accurate 5, koneksi ke Firebird di VPS, performa device, audit trail Accurate, dan alert penting secara cepat dan kontekstual.

Dokumen ini harus digunakan bersama PRD v2 dan POC Accurate Firebird agar implementasi Codex tetap grounded, tidak halusinasi, dan tidak kembali menjadi dashboard raw log yang tidak relevan dengan kebutuhan IT PT. XYZ.

