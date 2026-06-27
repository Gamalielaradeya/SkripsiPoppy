# 10_Test_Plan_v2 — Real Device Centralized Log Monitoring Dashboard

**Versi:** 2.0  
**Nama Sistem:** Centralized Log Monitoring Dashboard  
**Target Implementasi:** Real-device monitoring untuk perangkat Windows pengguna Accurate 5 pada lingkungan PT XYZ skala kecil  
**Stack:** VPS Linux, ZeroTier, RSyslog, Laravel, Blade, Tailwind CSS, Alpine.js, Chart.js, MySQL, Firebird 2.5, Windows Agent, Telegram Bot  
**Status:** Final untuk panduan AI Agent / Codex sebelum implementasi dan pengujian  

---

## 1. Tujuan Dokumen

Dokumen ini menjelaskan rencana pengujian untuk memastikan **Centralized Log Monitoring Dashboard v2** berjalan sesuai kebutuhan real-device.

Berbeda dengan versi awal yang menggunakan simulasi `rsyslog-client` container, versi ini menguji perangkat nyata:

1. Dua laptop Windows yang menjalankan Accurate 5 dan Windows Agent.
2. Satu VPS Linux sebagai pusat monitoring, RSyslog server, Laravel dashboard, MySQL, Firebird server, Accurate database, Accurate Audit Reader, dan Telegram Alert Service.
3. ZeroTier sebagai jaringan privat agar VPS dan laptop Windows dapat berkomunikasi seperti satu jaringan internal.

Tujuan utama pengujian adalah memastikan sistem dapat:

1. Mendaftarkan device Windows secara otomatis tanpa hardcode.
2. Mengumpulkan telemetry device melalui Windows Agent.
3. Mengirim log terstruktur dari Windows Agent ke RSyslog Server.
4. Memproses log key=value menjadi data terstruktur di database.
5. Menampilkan status device, user Windows, koneksi Firebird, performa, dan status Accurate 5 secara rapi.
6. Membaca audit trail Accurate dari Firebird `AUDIT + USERS` secara read-only.
7. Membuat contextual alert yang memiliki target, evidence, impact, dan recommended action.
8. Mengirim Telegram alert secara kontekstual.
9. Menyediakan action manual terkontrol berupa Remote Desktop dan Remote Restart.
10. Menyimpan raw log hanya pada menu Advanced Logs, bukan sebagai fokus dashboard utama.

---

## 2. Prinsip Pengujian

Pengujian harus mengikuti prinsip berikut:

| Prinsip | Penjelasan |
|---|---|
| Real-device first | Pengujian utama menggunakan laptop Windows nyata dan VPS Linux, bukan data random sebagai sumber utama. |
| No hardcoded device | Nama device tidak boleh ditulis statis di kode. Device harus terdaftar berdasarkan `agent_id` dan metadata hostname. |
| Evidence-based alert | Setiap alert wajib memiliki target, sumber deteksi, evidence, impact, dan recommended action. |
| Monitoring, not automatic remediation | Sistem boleh menyediakan remote restart, tetapi hanya manual oleh admin, tidak otomatis. |
| Audit-safe | Accurate Audit Reader hanya membaca Firebird secara read-only. |
| Dashboard-first | Dashboard menampilkan status operasional, bukan raw log panjang. |
| Fallback-aware | Jika komponen seperti Telegram atau Firebird gagal, sistem harus menampilkan status error yang jelas. |
| Repeatable | Test case harus bisa diulang oleh pengembang, pembimbing, atau penguji. |

---

## 3. Ruang Lingkup Pengujian

### 3.1 In Scope

Pengujian mencakup:

1. VPS Linux setup.
2. ZeroTier connectivity.
3. RSyslog Server di VPS.
4. Windows Agent pada laptop Windows.
5. Device auto-registration.
6. Heartbeat dan telemetry device.
7. CPU, RAM, disk, uptime, and active user collection.
8. RDP status detection.
9. Firebird connectivity check dari device Windows ke VPS.
10. Accurate process detection (`accurate.exe`).
11. Server health check pada VPS.
12. Laravel parser untuk structured log key=value.
13. Database storage and anti-duplication.
14. Dashboard UI.
15. Devices page.
16. Device detail page.
17. Accurate Audit page.
18. Accurate Audit Reader direct read-only Firebird.
19. Incidents page.
20. Alerts page.
21. Contextual Telegram alert.
22. Remote Desktop launcher.
23. Remote Restart manual via Windows Agent polling API.
24. Remote Actions audit trail.
25. Advanced Logs.
26. Settings and threshold.
27. Security and permission checks.
28. End-to-end testing.
29. User acceptance testing for IT admin workflow.

### 3.2 Out of Scope

Hal berikut tidak diuji sebagai fitur utama v2:

1. Auto-blocking / IPS.
2. Auto-restart tanpa persetujuan admin.
3. SIEM enterprise.
4. ELK Stack.
5. Prometheus/Grafana.
6. Full Windows Event Log ingestion sebagai dashboard utama.
7. Audit anomaly spike detection tanpa rule yang jelas.
8. Multi-role kompleks selain admin.
9. Mobile app native.
10. Pengujian load enterprise skala besar.
11. Penetration testing lanjutan.
12. Recovery otomatis jika Accurate database corrupt.

---

## 4. Lingkungan Pengujian

### 4.1 Topologi Target

```text
[Windows Laptop 1]
- Windows 10/11
- Accurate 5 Client
- Windows Agent
- ZeroTier Client
- RDP enabled

[Windows Laptop 2]
- Windows 10/11
- Accurate 5 Client
- Windows Agent
- ZeroTier Client
- RDP enabled

        ↓ ZeroTier private network

[VPS Linux]
- RSyslog Server
- Laravel Dashboard
- MySQL Monitoring DB
- Firebird 2.5 Server
- Accurate database file
- Accurate Audit Reader
- Telegram Alert Service
- Remote Command API
```

### 4.2 Perangkat

| Perangkat | Fungsi | Minimal |
|---|---|---|
| VPS Linux | Server monitoring dan database | 4 Core, 8 GB RAM |
| Laptop Windows 1 | Client Accurate 5 / Finance device 1 | Windows 10/11 |
| Laptop Windows 2 | Client Accurate 5 / Finance device 2 | Windows 10/11 |
| Smartphone / Telegram Desktop | Menerima Telegram alert | Telegram login |

### 4.3 Software VPS

| Software | Fungsi |
|---|---|
| Linux Server | OS VPS |
| Docker / Docker Compose atau native service | Deployment aplikasi |
| RSyslog | Menerima structured log dari Windows Agent |
| PHP + Laravel | Dashboard, parser, alert, API |
| MySQL | Database monitoring |
| Firebird 2.5 | Database Accurate |
| ZeroTier | Jaringan privat |
| Git | Version control |

### 4.4 Software Windows Client

| Software | Fungsi |
|---|---|
| Windows 10/11 | OS client |
| Accurate 5 | Aplikasi akuntansi client |
| Windows Agent | Mengirim telemetry ke RSyslog dan polling remote command |
| ZeroTier | Koneksi privat ke VPS |
| RDP | Remote Desktop dari admin |
| PowerShell / Python Runtime | Eksekusi Windows Agent sesuai implementasi |

---

## 5. Data Uji

### 5.1 Device Identity Data

Contoh data yang boleh dipakai dalam dokumentasi dan testing manual:

```text
Device Label     : Laptop Finance 1
Hostname         : DESKTOP-ABC123
Agent ID         : UUID generated by agent
ZeroTier IP      : 10.147.x.x
Windows User     : DESKTOP-ABC123\Finance
```

Catatan:

- `WIN-ACC-01` dan `WIN-ACC-02` hanya boleh menjadi contoh label dokumentasi.
- Program tidak boleh mengandalkan hostname tertentu.
- Identitas utama adalah `agent_id`.

### 5.2 Structured Log Sample — Heartbeat

```text
device-monitor event=heartbeat agent_id=AGENT-UUID hostname=DESKTOP-ABC123 device_label="Laptop Finance 1" windows_user="DESKTOP-ABC123\Finance" zerotier_ip=10.147.20.11 agent_status=online rdp_status=available uptime_minutes=1250
```

### 5.3 Structured Log Sample — Performance

```text
perf-monitor event=performance agent_id=AGENT-UUID hostname=DESKTOP-ABC123 cpu_percent=87 ram_percent=82 disk_percent=61 uptime_minutes=1250 status=warning
```

### 5.4 Structured Log Sample — Firebird Connectivity

```text
network-monitor event=firebird_connectivity agent_id=AGENT-UUID hostname=DESKTOP-ABC123 target_host=10.147.20.1 target_port=3051 status=connected latency_ms=38 packet_loss=0
```

```text
network-monitor event=firebird_connectivity agent_id=AGENT-UUID hostname=DESKTOP-ABC123 target_host=10.147.20.1 target_port=3051 status=timeout latency_ms=null packet_loss=100
```

### 5.5 Structured Log Sample — Accurate Process

```text
accurate-process-monitor event=accurate_process agent_id=AGENT-UUID hostname=DESKTOP-ABC123 process_name=accurate.exe status=running owner="DESKTOP-ABC123\Finance" process_id=4321 path="C:\Program Files\CPSSoft\Accurate5\accurate.exe"
```

```text
accurate-process-monitor event=accurate_process agent_id=AGENT-UUID hostname=DESKTOP-ABC123 process_name=accurate.exe status=not_running owner=null process_id=null path=null
```

### 5.6 Accurate Audit Test Data

Accurate Audit Reader mengambil data dari Firebird `AUDIT + USERS`.

Field minimal yang diharapkan masuk ke monitoring database:

```text
source_audit_id
activity_time
accurate_user_id
accurate_username
accurate_fullname
source_module
transaction_type
description
reference_no
invoice_no
app_version
status
```

Catatan:

- `LOGIN` tidak boleh digunakan sebagai sumber utama.
- `COMP_NAME` dan `IPADDRESS` tidak boleh diwajibkan karena bisa kosong.
- Accurate audit tidak dikirim lewat RSyslog.

---

## 6. Strategi Pengujian

| Jenis Pengujian | Tujuan |
|---|---|
| Connectivity Testing | Memastikan Windows client, VPS, ZeroTier, RSyslog, Firebird saling terhubung. |
| Functional Testing | Memastikan setiap fitur berjalan sesuai requirement. |
| Integration Testing | Memastikan alur Windows Agent → RSyslog → Parser → DB → Dashboard berjalan. |
| Database Testing | Memastikan data tersimpan benar, tidak duplikat, dan relasi konsisten. |
| UI Testing | Memastikan halaman menampilkan data yang benar dan mudah dipahami. |
| Alert Testing | Memastikan alert dibuat hanya dari rule jelas dan lengkap evidence. |
| Telegram Testing | Memastikan Telegram menerima pesan kontekstual. |
| Remote Action Testing | Memastikan RDP dan restart manual berjalan dan tercatat. |
| Security Testing | Memastikan credential aman, action manual, dan akses dashboard terlindungi. |
| UAT | Memastikan alur kerja IT admin masuk akal untuk kasus PT XYZ kecil. |

---

## 7. Prasyarat Sebelum Pengujian

### 7.1 Checklist VPS

```text
[ ] VPS dapat diakses melalui SSH.
[ ] ZeroTier client aktif di VPS.
[ ] VPS sudah join ZeroTier network.
[ ] RSyslog service aktif dan listen TCP/UDP sesuai konfigurasi.
[ ] Laravel app dapat diakses dari browser.
[ ] MySQL aktif.
[ ] Firebird 2.5 aktif.
[ ] Database Accurate tersedia di VPS.
[ ] Laravel dapat connect ke monitoring DB.
[ ] Laravel Accurate Audit Reader dapat connect read-only ke Firebird.
[ ] Telegram bot token dan chat id tersedia.
```

### 7.2 Checklist Windows Client

```text
[ ] Laptop Windows aktif.
[ ] Accurate 5 terinstall.
[ ] Accurate dapat membuka database Firebird pada VPS via ZeroTier IP.
[ ] ZeroTier aktif dan join network yang sama dengan VPS.
[ ] Windows Agent terinstall/tersedia.
[ ] Windows Agent memiliki agent_id unik.
[ ] Windows Agent dapat mengirim log ke RSyslog VPS.
[ ] RDP aktif jika fitur Remote Desktop ingin diuji.
[ ] Agent dapat polling API remote command.
```

### 7.3 Checklist Dashboard

```text
[ ] Admin user tersedia.
[ ] Login dashboard berhasil.
[ ] Menu Dashboard tersedia.
[ ] Menu Devices tersedia.
[ ] Menu Accurate Audit tersedia.
[ ] Menu Incidents tersedia.
[ ] Menu Alerts tersedia.
[ ] Menu Remote Actions tersedia.
[ ] Menu Advanced Logs tersedia.
[ ] Menu Settings tersedia.
```

---

## 8. Test Case — VPS dan ZeroTier

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-ZT-001 | VPS join ZeroTier | Cek status ZeroTier di VPS | VPS terlihat online di ZeroTier network | Pending |
| TC-ZT-002 | Windows 1 join ZeroTier | Cek ZeroTier client pada laptop 1 | Laptop 1 terlihat online di ZeroTier network | Pending |
| TC-ZT-003 | Windows 2 join ZeroTier | Cek ZeroTier client pada laptop 2 | Laptop 2 terlihat online di ZeroTier network | Pending |
| TC-ZT-004 | Ping Windows → VPS | Dari laptop Windows ping ZeroTier IP VPS | Ping berhasil atau status reachable sesuai firewall policy | Pending |
| TC-ZT-005 | Ping VPS → Windows | Dari VPS ping ZeroTier IP Windows | Ping berhasil atau status reachable sesuai firewall policy | Pending |
| TC-ZT-006 | Access dashboard via ZeroTier | Buka dashboard dari laptop via ZeroTier IP VPS | Dashboard terbuka | Pending |

---

## 9. Test Case — RSyslog Server

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-RSYSLOG-001 | Service RSyslog aktif | Cek service RSyslog di VPS | Service running | Pending |
| TC-RSYSLOG-002 | Port syslog listen | Cek TCP/UDP port yang dipakai | Port terbuka pada interface ZeroTier/public sesuai konfigurasi | Pending |
| TC-RSYSLOG-003 | Terima log test manual | Kirim log test dari Windows Agent atau logger equivalent | Log masuk ke file RSyslog | Pending |
| TC-RSYSLOG-004 | Simpan log berdasarkan hostname | Cek folder remote log | File log berdasarkan hostname muncul | Pending |
| TC-RSYSLOG-005 | Log all aggregate | Cek file aggregate jika tersedia | all.log bertambah saat log masuk | Pending |
| TC-RSYSLOG-006 | Tidak crash saat banyak log | Kirim 50 log beruntun | Semua log diterima tanpa service crash | Pending |

---

## 10. Test Case — Windows Agent Identity dan Registration

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-AGENT-ID-001 | Generate agent_id | Jalankan agent pertama kali | Agent membuat agent_id unik | Pending |
| TC-AGENT-ID-002 | Persist agent_id | Restart agent | Agent menggunakan agent_id yang sama | Pending |
| TC-AGENT-ID-003 | Device auto-register | Agent kirim heartbeat pertama | Device tercatat otomatis di tabel devices | Pending |
| TC-AGENT-ID-004 | Tidak hardcode hostname | Ubah label device di dashboard | Display name berubah tanpa mengubah hostname asli | Pending |
| TC-AGENT-ID-005 | Duplicate prevention | Jalankan agent sama dua kali | Tidak membuat device duplikat berdasarkan agent_id | Pending |
| TC-AGENT-ID-006 | Hostname update | Hostname berubah setelah rename Windows | Device record update hostname terbaru, agent_id tetap sama | Pending |

---

## 11. Test Case — Heartbeat dan Device Status

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-HEART-001 | Heartbeat terkirim | Jalankan Windows Agent | Heartbeat masuk ke RSyslog dan database | Pending |
| TC-HEART-002 | Last seen update | Tunggu beberapa interval agent | `last_seen_at` device terupdate | Pending |
| TC-HEART-003 | Device online | Agent aktif | Status device menjadi online | Pending |
| TC-HEART-004 | Heartbeat missed warning | Stop agent > warning threshold | Alert warning heartbeat missed dibuat | Pending |
| TC-HEART-005 | Device offline critical | Stop agent > critical threshold | Device offline dan alert critical dibuat | Pending |
| TC-HEART-006 | Device back online | Nyalakan agent kembali | Status device kembali online dan dapat resolve alert manual/otomatis sesuai rule | Pending |

---

## 12. Test Case — Performance Telemetry

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-PERF-001 | CPU telemetry | Agent kirim CPU usage | Data masuk ke `device_telemetries` | Pending |
| TC-PERF-002 | RAM telemetry | Agent kirim RAM usage | Data masuk ke `device_telemetries` | Pending |
| TC-PERF-003 | Disk telemetry | Agent kirim disk usage | Data masuk ke `device_telemetries` | Pending |
| TC-PERF-004 | CPU warning | Buat CPU >= warning threshold | Alert CPU warning dengan target device dan evidence dibuat | Pending |
| TC-PERF-005 | CPU critical | Buat CPU >= critical threshold | Alert CPU critical dengan evidence dibuat | Pending |
| TC-PERF-006 | RAM warning | RAM >= threshold | Alert RAM warning dibuat | Pending |
| TC-PERF-007 | Disk warning | Disk >= threshold | Alert disk warning dibuat | Pending |
| TC-PERF-008 | Dashboard update | Buka Dashboard dan Devices | CPU/RAM/Disk terbaru tampil sesuai data | Pending |

---

## 13. Test Case — Firebird Connectivity dari Windows Client

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-FB-CONN-001 | Port 3051 connected | Agent cek TCP ke ZeroTier IP VPS:3051 | Status connected masuk database | Pending |
| TC-FB-CONN-002 | Latency tercatat | Agent mengukur latency koneksi | `latency_ms` tersimpan | Pending |
| TC-FB-CONN-003 | Timeout terdeteksi | Simulasikan port tidak reachable | Alert `FIREBIRD_PORT_TIMEOUT` dibuat untuk target device → VPS | Pending |
| TC-FB-CONN-004 | Latency high | Buat latency melebihi threshold | Alert `FIREBIRD_LATENCY_HIGH` dibuat dengan evidence latency | Pending |
| TC-FB-CONN-005 | Device-specific problem | Hanya Windows 1 timeout, Windows 2 normal | Alert hanya menargetkan Windows 1, bukan menyimpulkan Firebird down | Pending |
| TC-FB-CONN-006 | Server-wide problem | Firebird service stop di VPS | Alert server health critical dibuat, dan device connectivity bisa ikut error | Pending |

---

## 14. Test Case — Accurate Process Detection

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-ACC-PROC-001 | Accurate running | Jalankan Accurate 5 di Windows | Agent mendeteksi `accurate.exe` running | Pending |
| TC-ACC-PROC-002 | Process owner | Cek process owner | Owner Windows user tersimpan bila tersedia | Pending |
| TC-ACC-PROC-003 | Accurate not running | Tutup Accurate 5 saat jam kerja | Status `not_running` tampil pada device | Pending |
| TC-ACC-PROC-004 | Alert not running | Accurate tidak berjalan saat rule mewajibkan alert | Alert target device dibuat dengan evidence `process not found` | Pending |
| TC-ACC-PROC-005 | Tidak false critical | Accurate tidak berjalan di luar jam kerja | Tidak membuat critical alert tanpa rule jelas | Pending |
| TC-ACC-PROC-006 | Dashboard status | Buka Dashboard/Devices | Accurate status tampil `Running`/`Not Running` per device | Pending |

---

## 15. Test Case — Server Health Check VPS

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-SERVER-001 | Firebird service running | Cek service Firebird di VPS | Status running masuk database | Pending |
| TC-SERVER-002 | Firebird port open | Cek port 3051 di VPS | Port open dicatat | Pending |
| TC-SERVER-003 | Firebird service stopped | Stop Firebird service | Alert `FIREBIRD_SERVICE_DOWN` critical target VPS dibuat | Pending |
| TC-SERVER-004 | RSyslog running | Cek RSyslog service | Status running dicatat | Pending |
| TC-SERVER-005 | DB monitoring running | Cek MySQL | Status running dicatat | Pending |
| TC-SERVER-006 | Laravel health | Cek endpoint health Laravel | Status healthy | Pending |

---

## 16. Test Case — Laravel Parser Key=Value

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-PARSER-001 | Parser membaca log baru | Jalankan parser/scheduler setelah log masuk | Log baru diproses | Pending |
| TC-PARSER-002 | Parse key=value | Masukkan structured log heartbeat | Field key=value terbaca benar | Pending |
| TC-PARSER-003 | Unknown key handling | Kirim key tambahan | Parser tidak crash dan raw log tetap tersimpan | Pending |
| TC-PARSER-004 | Timestamp parsing | Kirim log dengan timestamp RSyslog | `logged_at` benar | Pending |
| TC-PARSER-005 | Hostname parsing | Kirim log dari device berbeda | Hostname tersimpan sesuai sumber | Pending |
| TC-PARSER-006 | Anti-duplikasi | Jalankan parser ulang tanpa log baru | Tidak ada data dobel | Pending |
| TC-PARSER-007 | Parser offset | Tambahkan log baru setelah parser jalan | Parser hanya membaca log baru | Pending |
| TC-PARSER-008 | Malformed log | Kirim log tidak sesuai format | Masuk Advanced Logs sebagai malformed/system tanpa crash | Pending |

---

## 17. Test Case — Database Storage

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-DB-001 | Migration berhasil | Jalankan migration | Semua tabel v2 terbentuk | Pending |
| TC-DB-002 | Seeder admin | Jalankan seeder | Admin tersedia | Pending |
| TC-DB-003 | Device storage | Agent kirim heartbeat | Tabel `devices` terisi | Pending |
| TC-DB-004 | Telemetry storage | Agent kirim performance | Tabel `device_telemetries` terisi | Pending |
| TC-DB-005 | Network checks storage | Agent kirim Firebird check | Tabel `network_checks` terisi | Pending |
| TC-DB-006 | Accurate process storage | Agent kirim process status | Tabel `accurate_process_snapshots` terisi | Pending |
| TC-DB-007 | Logs storage | RSyslog raw parsed | Tabel `logs` terisi untuk Advanced Logs | Pending |
| TC-DB-008 | Alerts storage | Trigger warning/critical | Tabel `alerts` dan `alert_evidences` terisi | Pending |
| TC-DB-009 | Remote actions storage | Trigger remote action | Tabel `remote_actions` terisi | Pending |
| TC-DB-010 | Sensitive value safety | Simpan token/password | Tidak tersimpan plaintext jika design mewajibkan encryption/hashing | Pending |

---

## 18. Test Case — Accurate Audit Reader

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-AA-001 | Connect Firebird read-only | Jalankan Accurate Audit Reader | Koneksi berhasil menggunakan user read-only/guest | Pending |
| TC-AA-002 | Query AUDIT + USERS | Jalankan query utama | Data audit dan username Accurate terbaca | Pending |
| TC-AA-003 | Tidak pakai LOGIN | Review query / log sync | Tidak ada dependency ke tabel LOGIN sebagai sumber utama | Pending |
| TC-AA-004 | Handle COMP_NAME kosong | Audit row dengan COMP_NAME null | Sync tetap berhasil | Pending |
| TC-AA-005 | Handle IPADDRESS kosong | Audit row dengan IPADDRESS null | Sync tetap berhasil | Pending |
| TC-AA-006 | Incremental sync | Jalankan sync dua kali | Run kedua hanya mengambil data baru | Pending |
| TC-AA-007 | Duplicate prevention | Jalankan sync ulang | Tidak ada duplicate `source_audit_id` | Pending |
| TC-AA-008 | Dashboard audit realtime | Buka Accurate Audit page | Data audit terbaru tampil | Pending |
| TC-AA-009 | Filter user Accurate | Filter by username | Data sesuai user | Pending |
| TC-AA-010 | Filter date | Filter by date range | Data sesuai rentang tanggal | Pending |
| TC-AA-011 | Firebird connection failure | Matikan Firebird sementara | Error ditangani dan status sync failed tercatat | Pending |
| TC-AA-012 | Read-only safety | Coba operasi write dari reader tidak ada | Reader hanya melakukan SELECT | Pending |

---

## 19. Test Case — Contextual Alerts

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-ALERT-001 | Alert CPU warning | CPU tinggi pada device | Alert punya target device, evidence CPU, impact, action | Pending |
| TC-ALERT-002 | Alert device offline | Stop agent > threshold | Alert target device dibuat dengan evidence last_seen | Pending |
| TC-ALERT-003 | Alert Firebird client timeout | Client gagal connect VPS:3051 | Alert target device → VPS dibuat | Pending |
| TC-ALERT-004 | Alert Firebird service down | Stop Firebird VPS | Alert target VPS dibuat, bukan target device random | Pending |
| TC-ALERT-005 | Alert Accurate not running | Accurate.exe hilang saat rule berlaku | Alert target device dibuat | Pending |
| TC-ALERT-006 | No vague alert | Review alert list | Tidak ada judul ambigu seperti “Firebird unreachable” tanpa target | Pending |
| TC-ALERT-007 | Anti duplicate | Trigger alert sama berulang | Alert tidak spam sesuai cooldown | Pending |
| TC-ALERT-008 | Acknowledge | Klik acknowledge | Status alert menjadi acknowledged | Pending |
| TC-ALERT-009 | Resolve | Klik resolve | Status alert menjadi resolved | Pending |
| TC-ALERT-010 | Evidence detail | Buka detail alert | Evidence fields tampil jelas | Pending |

---

## 20. Test Case — Incidents

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-INC-001 | Incident device slow | CPU tinggi + RAM tinggi + Firebird latency tinggi | Incident indikasi device lambat dibuat | Pending |
| TC-INC-002 | Incident evidence | Buka detail incident | Menampilkan alert/event pendukung | Pending |
| TC-INC-003 | Incident no overclaim | Hanya CPU tinggi saja | Tidak langsung menyimpulkan device hang jika rule belum lengkap | Pending |
| TC-INC-004 | Acknowledge incident | Klik acknowledge | Status updated | Pending |
| TC-INC-005 | Resolve incident | Klik resolve | Status resolved | Pending |
| TC-INC-006 | Action from incident | Klik Remote Desktop dari incident | Remote action tercatat | Pending |

---

## 21. Test Case — Telegram Contextual Alert

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-TG-001 | Telegram config valid | Isi bot token dan chat id | Test message berhasil | Pending |
| TC-TG-002 | CPU alert Telegram | Trigger CPU warning | Telegram berisi target, evidence, impact, action | Pending |
| TC-TG-003 | Firebird down Telegram | Stop Firebird VPS | Telegram menyebut target VPS dan evidence service/port | Pending |
| TC-TG-004 | Device offline Telegram | Stop agent | Telegram menyebut device, last_seen, recommended action | Pending |
| TC-TG-005 | Anti-spam Telegram | Trigger alert sama berulang | Telegram tidak spam sesuai cooldown | Pending |
| TC-TG-006 | Telegram disabled | Nonaktifkan Telegram | Alert tetap dibuat, notification status skipped/disabled | Pending |
| TC-TG-007 | Telegram failure | Pakai token salah | Alert tetap dibuat, notification status failed dengan error | Pending |

Contoh format Telegram yang diharapkan:

```text
[WARNING] CPU tinggi pada Laptop Finance 1

Target      : Laptop Finance 1
Hostname    : DESKTOP-ABC123
User        : DESKTOP-ABC123\Finance
Detected by : Windows Agent
Evidence    : CPU 87%, RAM 82%, 5 menit terakhir
Impact      : Device berpotensi lambat saat menggunakan Accurate
Action      : Cek Device Detail / Remote Desktop bila diperlukan
Time        : 2026-05-28 20:10
```

---

## 22. Test Case — Dashboard UI

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-UI-DASH-001 | Dashboard load | Login dan buka `/dashboard` | Dashboard tampil tanpa error | Pending |
| TC-UI-DASH-002 | Summary cards | Cek summary cards | Device online, Firebird connected, Accurate active, alerts tampil | Pending |
| TC-UI-DASH-003 | Device table | Cek tabel device | Device tampil dengan user, CPU, Firebird, Accurate status | Pending |
| TC-UI-DASH-004 | Accurate audit preview | Cek audit realtime preview | Audit terbaru tampil ringkas | Pending |
| TC-UI-DASH-005 | Incident preview | Cek recent incidents | Incident terbaru tampil dengan severity | Pending |
| TC-UI-DASH-006 | No raw log dominance | Cek dashboard | Raw log panjang tidak menjadi fokus utama | Pending |
| TC-UI-DASH-007 | Responsive | Buka di layar laptop kecil | Layout tetap terbaca | Pending |

---

## 23. Test Case — Devices Page

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-UI-DEV-001 | List devices | Buka `/devices` | Semua device terdaftar tampil | Pending |
| TC-UI-DEV-002 | Search device | Cari device label/hostname | Hasil sesuai | Pending |
| TC-UI-DEV-003 | Filter status | Filter online/warning/offline | Hasil sesuai | Pending |
| TC-UI-DEV-004 | Device action buttons | Cek row action | Detail, Remote Desktop, Restart Client tersedia | Pending |
| TC-UI-DEV-005 | Offline action state | Device offline | Action yang tidak mungkin disabled atau beri warning | Pending |

---

## 24. Test Case — Device Detail Page

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-UI-DEVDET-001 | Detail device load | Klik device | Halaman detail tampil | Pending |
| TC-UI-DEVDET-002 | Identity section | Cek identity | Hostname, label, agent_id masked, Windows user tampil | Pending |
| TC-UI-DEVDET-003 | Connectivity section | Cek koneksi | Ping/Firebird status dan latency tampil | Pending |
| TC-UI-DEVDET-004 | Performance section | Cek performance | CPU/RAM/Disk/uptime tampil | Pending |
| TC-UI-DEVDET-005 | Accurate process section | Cek Accurate status | Running/not running tampil | Pending |
| TC-UI-DEVDET-006 | Remote buttons | Cek actions | Remote Desktop dan Restart Client selalu tersedia sesuai status | Pending |
| TC-UI-DEVDET-007 | Related alerts | Cek related alerts | Alert device terkait tampil | Pending |
| TC-UI-DEVDET-008 | Device logs | Klik View Logs | Mengarah ke Advanced Logs filtered by device | Pending |

---

## 25. Test Case — Accurate Audit Page

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-UI-AA-001 | Audit page load | Buka `/accurate-audit` | Tabel audit tampil | Pending |
| TC-UI-AA-002 | Filter user | Pilih Accurate username | Tabel terfilter | Pending |
| TC-UI-AA-003 | Filter date | Pilih tanggal | Tabel terfilter | Pending |
| TC-UI-AA-004 | Search keyword | Cari invoice/reference | Hasil sesuai | Pending |
| TC-UI-AA-005 | Detail audit | Klik detail | Detail audit tampil | Pending |
| TC-UI-AA-006 | Empty state | Tidak ada data | Pesan empty state jelas | Pending |
| TC-UI-AA-007 | Sync status | Cek last sync | Last sync dan status tampil | Pending |

---

## 26. Test Case — Alerts Page

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-UI-ALERT-001 | Alerts page load | Buka `/alerts` | Tabel alert tampil | Pending |
| TC-UI-ALERT-002 | Alert has target | Lihat alert | Target jelas: device atau VPS | Pending |
| TC-UI-ALERT-003 | Alert has evidence | Buka detail | Evidence tampil | Pending |
| TC-UI-ALERT-004 | Alert impact | Buka detail | Impact tampil | Pending |
| TC-UI-ALERT-005 | Recommended action | Buka detail | Recommended action tampil | Pending |
| TC-UI-ALERT-006 | Acknowledge action | Klik acknowledge | Status berubah | Pending |
| TC-UI-ALERT-007 | Resolve action | Klik resolve | Status berubah | Pending |
| TC-UI-ALERT-008 | Remote action shortcut | Alert device punya shortcut Remote Desktop | Remote action tercatat jika dipakai | Pending |

---

## 27. Test Case — Remote Desktop Launcher

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-RDP-001 | RDP status detected | Agent cek service RDP | RDP status tampil | Pending |
| TC-RDP-002 | Open RDP action | Klik Remote Desktop | Sistem membuat link/file/command RDP ke IP device | Pending |
| TC-RDP-003 | Action logged | Setelah klik Remote Desktop | `remote_actions` mencatat `OPEN_RDP` | Pending |
| TC-RDP-004 | Device offline handling | Klik RDP saat offline | UI memberi warning/disabled | Pending |
| TC-RDP-005 | No hidden auto action | Klik RDP | Sistem tidak menjalankan restart/command lain | Pending |

---

## 28. Test Case — Remote Restart Manual

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-RESTART-001 | Restart button visible | Buka Device Detail | Tombol Restart Client tersedia | Pending |
| TC-RESTART-002 | Confirmation modal | Klik Restart | Modal konfirmasi muncul | Pending |
| TC-RESTART-003 | Reason required | Submit tanpa alasan | Ditolak dan minta alasan | Pending |
| TC-RESTART-004 | Create command | Submit alasan valid | Remote action dibuat status pending | Pending |
| TC-RESTART-005 | Agent polling | Agent polling API | Agent menerima command restart | Pending |
| TC-RESTART-006 | Execute restart | Agent execute restart | Windows menjadwalkan restart sesuai implementasi | Pending |
| TC-RESTART-007 | Result reporting | Agent kirim hasil | Remote action berubah executed/success/failed | Pending |
| TC-RESTART-008 | Audit trail | Cek remote actions | Admin, target, reason, waktu, result tercatat | Pending |
| TC-RESTART-009 | No auto restart | Trigger critical alert | Tidak ada restart otomatis tanpa admin klik | Pending |
| TC-RESTART-010 | Unauthorized denied | User belum login/invalid token | Command tidak dibuat | Pending |

---

## 29. Test Case — Advanced Logs

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-LOGS-001 | Advanced Logs load | Buka `/advanced-logs` | Log tampil | Pending |
| TC-LOGS-002 | Filter by device | Filter device | Hasil sesuai device | Pending |
| TC-LOGS-003 | Filter category | Filter category | Hasil sesuai | Pending |
| TC-LOGS-004 | Search raw message | Cari keyword | Hasil sesuai | Pending |
| TC-LOGS-005 | Detail raw log | Klik detail | Raw message dan parsed fields tampil | Pending |
| TC-LOGS-006 | Not dashboard focus | Buka dashboard | Raw logs tidak mendominasi dashboard | Pending |

---

## 30. Test Case — Settings

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-SET-001 | Settings load | Buka `/settings` | Settings tampil | Pending |
| TC-SET-002 | CPU threshold update | Ubah CPU warning threshold | Rule alert memakai threshold baru | Pending |
| TC-SET-003 | Heartbeat threshold update | Ubah offline threshold | Device status mengikuti threshold baru | Pending |
| TC-SET-004 | Telegram setting update | Ubah token/chat id | Test Telegram sesuai setting baru | Pending |
| TC-SET-005 | Firebird setting view | Cek Firebird settings | Sensitive value dimasked | Pending |
| TC-SET-006 | Remote restart toggle | Disable remote restart | Tombol restart disabled atau tidak bisa submit | Pending |

---

## 31. Security Test Cases

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-SEC-001 | Dashboard auth | Akses dashboard tanpa login | Redirect login | Pending |
| TC-SEC-002 | API auth | Call remote command API tanpa token | Ditolak | Pending |
| TC-SEC-003 | Agent auth | Agent polling dengan invalid token | Ditolak | Pending |
| TC-SEC-004 | Sensitive masking | Buka settings/log | Token/password tidak tampil plaintext | Pending |
| TC-SEC-005 | Read-only audit | Accurate Audit Reader | Tidak ada write query ke Firebird | Pending |
| TC-SEC-006 | Restart requires reason | Restart tanpa alasan | Ditolak | Pending |
| TC-SEC-007 | Restart requires admin | Non-admin/tidak login | Ditolak | Pending |
| TC-SEC-008 | CSRF forms | Submit form tanpa CSRF | Ditolak sesuai Laravel security | Pending |

---

## 32. Error Handling Test Cases

| ID | Test Case | Langkah Pengujian | Expected Result | Status |
|---|---|---|---|---|
| TC-ERR-001 | RSyslog down | Matikan RSyslog sementara | Agent error tercatat/log lokal, dashboard menampilkan data terakhir | Pending |
| TC-ERR-002 | MySQL down | Matikan DB monitoring | Laravel menampilkan error terkontrol | Pending |
| TC-ERR-003 | Firebird down | Matikan Firebird | Alert server critical dan Audit Reader failed run tercatat | Pending |
| TC-ERR-004 | ZeroTier disconnect | Disconnect device | Device heartbeat missed/offline sesuai threshold | Pending |
| TC-ERR-005 | Telegram API failed | Token salah/internet putus | Alert tetap dibuat, notification failed | Pending |
| TC-ERR-006 | Malformed agent log | Kirim log rusak | Parser tidak crash, log masuk Advanced Logs atau skipped dengan reason | Pending |
| TC-ERR-007 | Agent command failed | Restart command gagal | Remote action status failed dan result message tampil | Pending |

---

## 33. End-to-End Test Scenarios

### 33.1 E2E — Device Online to Dashboard

| Item | Description |
|---|---|
| ID | E2E-001 |
| Scenario | Windows device mengirim heartbeat dan muncul di dashboard |
| Steps | Start Windows Agent → Agent kirim heartbeat → RSyslog terima → Parser proses → DB update → Dashboard dibuka |
| Expected Result | Device tampil online dengan hostname, label, user Windows, ZeroTier IP, last seen |

### 33.2 E2E — Firebird Connectivity Problem

| Item | Description |
|---|---|
| ID | E2E-002 |
| Scenario | Satu client gagal connect ke Firebird |
| Steps | Ganggu koneksi Windows 1 ke port 3051 → Agent kirim timeout → Parser proses → Alert dibuat → Telegram terkirim |
| Expected Result | Alert menargetkan Windows 1 → VPS Firebird, bukan menyimpulkan semua device down |

### 33.3 E2E — Firebird Service Down

| Item | Description |
|---|---|
| ID | E2E-003 |
| Scenario | Firebird service di VPS mati |
| Steps | Stop Firebird service → Server health checker mendeteksi inactive → Alert critical dibuat → Telegram terkirim |
| Expected Result | Alert target VPS-Firebird, evidence service inactive dan port closed, impact menjelaskan Accurate client bisa terganggu |

### 33.4 E2E — Accurate Audit Trail Update

| Item | Description |
|---|---|
| ID | E2E-004 |
| Scenario | User Accurate melakukan perubahan data dan muncul di dashboard |
| Steps | User melakukan update transaksi di Accurate → Firebird AUDIT terisi → Accurate Audit Reader sync → MySQL monitoring terisi → UI audit refresh |
| Expected Result | Accurate Audit page menampilkan user Accurate, waktu aktivitas, modul/source, jenis aktivitas, deskripsi/reference jika tersedia |

### 33.5 E2E — Remote Restart Manual

| Item | Description |
|---|---|
| ID | E2E-005 |
| Scenario | Admin melakukan restart manual pada device |
| Steps | Admin buka Device Detail → klik Restart Client → isi alasan → Agent polling command → Agent execute restart → result dikirim |
| Expected Result | Restart hanya terjadi setelah admin konfirmasi, remote action tercatat lengkap |

---

## 34. User Acceptance Test — IT Admin Workflow

### UAT-001 — Monitoring Harian

| Item | Description |
|---|---|
| Tujuan | IT admin dapat membuka dashboard dan memahami kondisi seluruh device Accurate |
| Langkah | Login → Dashboard → cek summary → cek device table → cek recent audit → cek incidents |
| Expected | Admin dapat mengetahui device online, user aktif, Firebird connected, Accurate running, dan alert aktif tanpa membuka raw log |

### UAT-002 — Investigasi Device Lambat

| Item | Description |
|---|---|
| Tujuan | IT admin dapat menginvestigasi device yang terindikasi lambat |
| Langkah | Buka Incidents → pilih device slow → buka Device Detail → cek CPU/RAM/Firebird latency → klik Remote Desktop bila perlu |
| Expected | Evidence jelas dan action tersedia |

### UAT-003 — Investigasi Aktivitas Accurate

| Item | Description |
|---|---|
| Tujuan | IT admin dapat melihat perubahan data Accurate berdasarkan audit trail |
| Langkah | Buka Accurate Audit → filter user/tanggal → buka detail audit |
| Expected | User Accurate dan aktivitas terlihat tanpa query manual ke Firebird |

### UAT-004 — Respons Alert Telegram

| Item | Description |
|---|---|
| Tujuan | IT admin menerima alert penting melalui Telegram dan bisa memahami target masalah |
| Langkah | Trigger alert → baca Telegram → buka dashboard/detail alert |
| Expected | Telegram tidak ambigu, berisi target, evidence, impact, recommended action |

---

## 35. Acceptance Criteria Global

Sistem dianggap lulus pengujian minimum jika:

```text
[ ] Minimal 2 laptop Windows berhasil terdaftar sebagai device tanpa hardcode.
[ ] Windows Agent mengirim heartbeat, performance, Firebird connectivity, dan Accurate process status.
[ ] RSyslog VPS menerima log dari kedua device.
[ ] Laravel parser menyimpan data terstruktur ke database.
[ ] Dashboard menampilkan status device, Windows user, Firebird connectivity, performance, Accurate process, alert, dan audit preview.
[ ] Accurate Audit Reader membaca Firebird AUDIT + USERS secara read-only.
[ ] Tabel LOGIN tidak menjadi sumber utama.
[ ] Alert yang muncul memiliki target, evidence, impact, dan recommended action.
[ ] Telegram alert terkirim untuk warning/critical penting.
[ ] Remote Desktop action tersedia dan tercatat.
[ ] Remote Restart manual berjalan atau minimal command flow berjalan end-to-end dengan confirmation + reason + audit log.
[ ] Raw log hanya menjadi menu Advanced Logs, bukan fokus dashboard.
[ ] Tidak ada auto restart.
[ ] Tidak ada hardcoded device name di kode.
[ ] Tidak ada alert ambigu seperti “Firebird unreachable” tanpa target.
```

---

## 36. Demo Readiness Checklist

Sebelum presentasi atau pengujian di depan pembimbing/penguji, pastikan:

```text
[ ] VPS aktif.
[ ] ZeroTier aktif pada VPS dan kedua laptop.
[ ] Dashboard bisa dibuka dari laptop.
[ ] Dua Windows Agent aktif.
[ ] Dua device tampil online.
[ ] Accurate 5 berjalan minimal pada satu laptop.
[ ] Firebird database di VPS dapat diakses Accurate 5.
[ ] Accurate Audit Reader berhasil sync.
[ ] Ada minimal 5 data audit Accurate.
[ ] Telegram test berhasil.
[ ] Ada minimal 1 alert warning yang jelas.
[ ] Ada minimal 1 alert critical yang jelas.
[ ] Remote Desktop launcher bisa ditunjukkan.
[ ] Remote Restart manual flow bisa ditunjukkan dengan aman.
[ ] GitHub repository sudah menyimpan versi stabil terakhir.
```

---

## 37. Catatan untuk Codex / AI Agent

Saat mengimplementasikan test atau membuat automated test, AI Agent wajib mengikuti aturan berikut:

1. Jangan membuat test yang bergantung pada hostname hardcoded seperti `WIN-ACC-01`.
2. Jangan menggunakan tabel `LOGIN` sebagai sumber utama Accurate audit.
3. Jangan membuat alert tanpa target dan evidence.
4. Jangan membuat auto restart.
5. Jangan menjadikan raw Windows Event Log sebagai fokus dashboard.
6. Jangan menganggap semua Firebird timeout berarti Firebird service down.
7. Bedakan masalah:
   - Client tidak bisa connect ke Firebird.
   - Firebird service di VPS mati.
   - Device offline.
   - Accurate.exe tidak berjalan.
8. Telegram message harus contextual, bukan sekadar judul alert.
9. Remote action harus selalu tercatat.
10. Semua test yang memakai credential harus menggunakan environment variable atau secret yang aman.

---

## 38. Ringkasan

Dokumen Test Plan v2 ini menjadi pedoman pengujian untuk memastikan sistem benar-benar sesuai dengan arah baru:

```text
Real Windows device
+ VPS Linux
+ ZeroTier private network
+ RSyslog structured monitoring
+ Laravel dashboard
+ Firebird Accurate audit reader
+ contextual Telegram alert
+ manual remote administration
```

Fokus pengujian bukan lagi membuktikan bahwa log random dapat tampil, melainkan membuktikan bahwa IT admin dapat memahami kondisi operasional perangkat Accurate secara cepat, terarah, dan dapat ditindaklanjuti.
