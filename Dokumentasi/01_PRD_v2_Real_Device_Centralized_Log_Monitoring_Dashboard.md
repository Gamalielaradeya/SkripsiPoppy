# PRD v2.0
# Centralized Log Monitoring Dashboard

**Versi:** 2.0  
**Status:** Draft Final untuk Review Pengguna  
**Tanggal:** 2026-05-28  
**Jenis Dokumen:** Product Requirement Document  
**Nama Produk:** Centralized Log Monitoring Dashboard  
**Konteks:** Monitoring real-device untuk client Windows pengguna Accurate 5 dan server Firebird pada VPS Linux  
**Stack Utama:** Laravel, Blade, Tailwind CSS, MySQL/MariaDB, RSyslog, Windows Agent, Firebird 2.5, ZeroTier, Telegram Bot API  
**Target Implementasi:** 2 laptop Windows + 1 VPS Linux dalam jaringan privat ZeroTier  
**Target Pengguna:** Administrator IT PT. XYZ skala kecil  

---

## 1. Ringkasan Produk

**Centralized Log Monitoring Dashboard** adalah sistem monitoring terpusat berbasis web yang dirancang untuk membantu Administrator IT memantau kondisi perangkat Windows pengguna Accurate 5, konektivitas ke database Firebird pada VPS Linux, performa perangkat, aktivitas proses Accurate, audit trail database Accurate, alert Telegram, serta tindakan administrasi manual seperti Remote Desktop dan restart client.

Produk ini bukan lagi hanya dashboard raw log atau demo simulasi container. Versi 2.0 diarahkan sebagai sistem **real-device monitoring** pada lingkungan kecil yang menyerupai kebutuhan PT. XYZ, yaitu:

```text
2 laptop Windows pengguna Accurate 5
        ↓
Windows Monitoring Agent mengirim status/log terstruktur
        ↓
ZeroTier private network
        ↓
VPS Linux sebagai pusat monitoring dan database
        ↓
Laravel Dashboard + MySQL + RSyslog + Firebird + Telegram Alert
```

Produk ini tetap mempertahankan konsep utama penelitian, yaitu penggunaan **RSyslog** sebagai log collector terpusat. Namun data yang dikirim ke RSyslog tidak berupa seluruh Windows Event Log mentah, melainkan **custom monitoring log** yang sudah dipilih sesuai kebutuhan Administrator IT.

Fokus utama produk:

1. Monitoring device Windows dan user Windows aktif.
2. Monitoring koneksi laptop client ke server Firebird/Accurate pada VPS.
3. Monitoring performa device dan indikasi lambat/hang.
4. Monitoring proses Accurate 5 pada client Windows.
5. Audit trail aktivitas perubahan data Accurate melalui Firebird secara read-only.
6. Alert Telegram kontekstual yang memiliki target, evidence, impact, dan recommended action.
7. Remote administration manual berupa Remote Desktop dan restart client terkontrol.
8. Advanced Logs untuk investigasi teknis, bukan sebagai tampilan utama dashboard.

---

## 2. Latar Belakang Produk

PT. XYZ bergantung pada aplikasi Accurate 5 untuk aktivitas keuangan dan operasional. Dalam skenario operasional skala kecil, Accurate 5 digunakan pada beberapa laptop Windows, sedangkan database Accurate berbasis Firebird 2.5 ditempatkan pada server terpusat.

Masalah yang ingin diselesaikan:

1. Administrator IT sulit mengetahui laptop mana yang sedang aktif dan user Windows siapa yang sedang menggunakan device.
2. Administrator IT tidak memiliki tampilan cepat untuk melihat apakah laptop client dapat terhubung ke database Firebird.
3. Saat user mengeluhkan Accurate lambat atau koneksi database bermasalah, pengecekan masih dilakukan manual.
4. Performa device seperti CPU/RAM/disk tidak terlihat secara terpusat.
5. Proses Accurate 5 pada laptop client tidak termonitor.
6. Aktivitas audit trail Accurate belum tampil dalam dashboard monitoring yang mudah dibaca.
7. Alert yang hanya berbasis raw log terlalu generik dan tidak cukup membantu admin mengambil keputusan.
8. Administrator membutuhkan tindakan manual terkontrol seperti membuka Remote Desktop atau melakukan restart client, tetapi action tersebut harus tercatat.

Dengan produk ini, dashboard tidak hanya menjawab pertanyaan “ada log apa?”, tetapi menjawab pertanyaan yang lebih operasional:

```text
Laptop mana yang online?
User Windows siapa yang sedang aktif?
Apakah laptop client bisa konek ke Firebird?
Apakah Accurate 5 berjalan?
Apakah device mulai lambat?
Aktivitas perubahan data apa yang terjadi di Accurate?
Alert mana yang butuh tindakan segera?
Apa tindakan admin yang sudah dilakukan?
```

---

## 3. Tujuan Produk

Tujuan utama produk adalah menyediakan sistem monitoring terpusat yang relevan untuk Administrator IT pada lingkungan penggunaan Accurate 5 skala kecil.

Tujuan rinci:

1. Mengumpulkan monitoring log dari laptop Windows secara terpusat menggunakan RSyslog.
2. Menyimpan hasil monitoring ke database MySQL/MariaDB.
3. Menampilkan status device Windows secara real-time atau near-real-time.
4. Menampilkan user Windows aktif pada masing-masing device.
5. Menampilkan status konektivitas client Windows ke Firebird server pada VPS.
6. Menampilkan performa device seperti CPU, RAM, disk, uptime, dan indikasi lambat/hang.
7. Menampilkan status proses Accurate 5 pada masing-masing client Windows.
8. Mengambil audit trail Accurate dari database Firebird secara read-only.
9. Menampilkan audit trail Accurate secara rapi, dapat difilter, dan mudah dipahami.
10. Membuat alert yang spesifik, tidak ambigu, dan memiliki evidence.
11. Mengirim notifikasi Telegram untuk kondisi penting secara kontekstual.
12. Menyediakan fitur Remote Desktop launcher untuk device yang terdaftar.
13. Menyediakan fitur restart client secara manual, terkontrol, dan tercatat.
14. Menyediakan halaman Advanced Logs untuk investigasi teknis tanpa memenuhi dashboard utama dengan log mentah.
15. Mendukung iterasi pengembangan melalui GitHub agar sistem dapat terus diperbaiki tanpa kehilangan versi stabil.

---

## 4. Nilai Kebaruan / Novelty Produk

Produk ini memiliki kebaruan bukan hanya karena menampilkan monitoring dan Telegram, tetapi karena menggabungkan beberapa fungsi dalam satu dashboard yang fokus pada kebutuhan Accurate/Firebird di lingkungan real-device.

Novelty utama:

1. **Contextual Telegram Alert**  
   Notifikasi Telegram tidak hanya mengirim teks warning/critical, tetapi membawa informasi target, sumber deteksi, evidence, impact, dan recommended action.

2. **Real-Device Windows Monitoring berbasis RSyslog**  
   Sistem memonitor laptop Windows nyata melalui Windows Agent yang mengirim custom monitoring log ke RSyslog Server, bukan hanya simulasi container.

3. **Accurate Firebird Audit Trail Reader**  
   Sistem membaca audit trail dari database Accurate berbasis Firebird secara read-only, lalu menampilkannya pada dashboard web.

4. **Correlation-Oriented Incident View**  
   Sistem tidak hanya menampilkan log, tetapi menyusun insiden dari beberapa evidence seperti CPU tinggi, RAM tinggi, heartbeat terlambat, atau koneksi Firebird lambat.

5. **Manual Remote Administration**  
   Administrator dapat melakukan Remote Desktop dan restart client secara manual terkontrol dari dashboard, dengan pencatatan tindakan pada remote action log.

Novelty yang perlu ditegaskan:

```text
Telegram bukan sekadar channel notifikasi, tetapi bagian dari contextual alerting.
Accurate audit bukan sekadar cek koneksi database, tetapi membaca aktivitas perubahan data.
Remote restart bukan otomatis, tetapi manual terkontrol oleh admin.
RSyslog tetap menjadi collector utama untuk telemetry device dan log operasional.
```

---

## 5. Scope Sistem

### 5.1 In Scope

Fitur yang wajib masuk versi v2:

| Modul | Keterangan |
|---|---|
| Admin Authentication | Login, logout, proteksi halaman admin. |
| Device Registry | Device Windows terdaftar otomatis berdasarkan agent_id dan hostname. |
| Windows Monitoring Agent | Agent pada Windows mengambil data device dan mengirim log terstruktur. |
| RSyslog Server | Menerima custom monitoring log dari Windows Agent melalui jaringan ZeroTier. |
| Log Parser | Membaca log RSyslog, parsing format custom, menyimpan hasil ke database. |
| Device Dashboard | Menampilkan status device, user Windows, heartbeat, CPU/RAM/disk, Accurate process, koneksi Firebird. |
| Network & Firebird Connectivity | Menampilkan ping/latency dan TCP check ke Firebird port 3051. |
| Performance Monitoring | Menampilkan CPU, RAM, disk, uptime, dan status indikasi lambat/hang. |
| Accurate Process Monitoring | Mengecek apakah `accurate.exe` sedang berjalan pada client Windows. |
| Firebird Service Monitoring | Mengecek status service Firebird pada VPS Linux. |
| Accurate Audit Trail | Membaca audit trail dari Firebird secara read-only dan menampilkannya di dashboard. |
| Alerts | Membuat alert berbasis rule yang jelas, target jelas, dan evidence jelas. |
| Telegram Alert | Mengirim alert kontekstual ke Telegram. Wajib, bukan opsional. |
| Incidents | Menampilkan gabungan evidence/alert yang menjelaskan masalah operasional. |
| Remote Desktop Launcher | Membantu admin membuka RDP ke device melalui IP ZeroTier. |
| Remote Restart Client | Restart Windows client secara manual terkontrol melalui command yang diambil Windows Agent. |
| Remote Action Log | Mencatat action admin: RDP, ping, restart, hasil command. |
| Advanced Logs | Menampilkan log teknis mentah/hasil parser untuk investigasi, bukan dashboard utama. |
| Settings | Mengelola threshold, Telegram config, Firebird config, ZeroTier/IP device, remote action policy. |
| GitHub Workflow | Pengembangan memakai Git untuk versioning dan iterasi aman. |

### 5.2 Out of Scope

Hal yang tidak masuk versi v2:

| Fitur | Alasan |
|---|---|
| Auto restart otomatis | Restart harus manual oleh admin agar aman. |
| Automatic blocking / IPS | Sistem difokuskan pada monitoring, alert, audit, dan action manual. |
| SIEM enterprise | Terlalu luas untuk scope skripsi. |
| ELK Stack / Grafana / Prometheus | Tidak digunakan agar scope tetap Laravel + RSyslog + MySQL. |
| Menampilkan semua Windows Event Log di dashboard utama | Terlalu noisy dan tidak relevan untuk kebutuhan IT harian. |
| Hardcoded device name | Device harus dinamis dan terdaftar melalui agent_id. |
| Audit activity spike detection tanpa rule jelas | Tidak dibuat dulu agar tidak menjadi fitur halusinatif. |
| Multi-role kompleks | Versi awal cukup role Administrator IT. |
| Mobile app native | Dashboard web sudah cukup. |
| Direct public Firebird exposure sebagai desain utama | Firebird diakses melalui jaringan privat ZeroTier. |
| Remote control penuh selain RDP | Sistem hanya menyediakan launcher RDP dan command restart terkontrol. |
| Write operation ke database Accurate | Accurate audit reader hanya read-only. |

---

## 6. Target Pengguna

### 6.1 Administrator IT

Administrator IT adalah pengguna utama sistem.

Hak akses:

1. Login ke dashboard.
2. Melihat ringkasan status device dan server.
3. Melihat daftar device Windows yang dimonitor.
4. Melihat detail device.
5. Melihat koneksi Firebird dari masing-masing device.
6. Melihat performa device.
7. Melihat status proses Accurate.
8. Melihat audit trail Accurate.
9. Melihat dan mengelola alert.
10. Melihat dan mengelola incident.
11. Membuka Remote Desktop ke device.
12. Melakukan restart client secara manual terkontrol.
13. Melihat riwayat remote action.
14. Melihat advanced logs untuk investigasi teknis.
15. Mengubah setting threshold dan konfigurasi notifikasi.

### 6.2 User Operasional / Finance

User operasional atau finance bukan pengguna utama dashboard, tetapi menjadi objek monitoring.

Contoh data yang berkaitan dengan user:

```text
Windows user aktif pada laptop
Accurate username dari audit trail
Aktivitas perubahan transaksi Accurate
```

User operasional tidak perlu login ke dashboard.

---

## 7. Lingkungan Implementasi Target

### 7.1 Perangkat

| Komponen | Jumlah | Fungsi |
|---|---:|---|
| Laptop Windows | 2 | Client Accurate 5 dan sumber telemetry device. |
| VPS Linux | 1 | Server monitoring, dashboard, database aplikasi, RSyslog, Firebird. |
| Smartphone / Telegram Client | 1 atau lebih | Menerima notifikasi alert Telegram. |

### 7.2 Software pada Laptop Windows

| Software | Fungsi |
|---|---|
| Windows 10/11 | OS client. |
| Accurate 5 | Aplikasi akuntansi yang digunakan user. |
| Windows Agent | Mengambil telemetry dan mengirim log ke RSyslog Server. |
| ZeroTier | Menghubungkan laptop ke VPS dalam jaringan privat. |
| Remote Desktop | Akses remote dari admin jika dibutuhkan. |

### 7.3 Software pada VPS Linux

| Software | Fungsi |
|---|---|
| Linux Server | Host utama sistem. |
| RSyslog Server | Menerima log/status dari Windows Agent. |
| Laravel Application | Dashboard, parser, alert logic, remote command API. |
| MySQL/MariaDB | Database dashboard monitoring. |
| Firebird 2.5 | Database Accurate. |
| Accurate Audit Reader | Modul Laravel untuk membaca AUDIT + USERS dari Firebird. |
| Telegram Bot API | Media notifikasi alert. |
| ZeroTier | Jaringan privat antar device. |
| Git | Version control dan deployment workflow. |

### 7.4 Jaringan

Jaringan target menggunakan ZeroTier agar laptop Windows dan VPS Linux dapat berkomunikasi seolah berada dalam satu jaringan lokal.

Contoh komunikasi:

```text
Windows Agent → VPS RSyslog Server
Windows Agent → VPS Laravel API untuk command polling
Accurate 5 Windows → VPS Firebird port 3051
Laravel Audit Reader → Firebird database
Admin Browser → Laravel Dashboard
Laravel Alert Service → Telegram Bot API
```

---

## 8. Prinsip Desain Produk

Produk harus mengikuti prinsip berikut:

| Prinsip | Penjelasan |
|---|---|
| Real-device first | Sistem diarahkan langsung untuk perangkat nyata, bukan simulasi random log. |
| Dashboard-first | Halaman utama menampilkan ringkasan kondisi yang dibutuhkan admin. |
| Contextual | Alert harus punya target, sumber deteksi, evidence, impact, dan recommended action. |
| Low-noise | Raw log tidak boleh memenuhi dashboard utama. |
| Audit-friendly | Semua log penting, alert, incident, dan remote action harus tercatat. |
| No hardcoded device | Device diidentifikasi dari agent_id/hostname dan dapat diberi label oleh admin. |
| Manual controlled action | Restart client dan action lain hanya berjalan setelah konfirmasi admin. |
| Read-only Accurate access | Modul Accurate Audit tidak boleh mengubah data Accurate. |
| Secure by design | Firebird dan service internal diakses melalui ZeroTier, bukan publik bebas. |
| Iterative development | Sistem dapat dikembangkan bertahap melalui GitHub tanpa mengubah arah produk. |

---

## 9. Arsitektur Produk Tingkat Tinggi

```text
[Windows Laptop 1]
- Accurate 5
- Windows Agent
- ZeroTier
- RDP

[Windows Laptop 2]
- Accurate 5
- Windows Agent
- ZeroTier
- RDP

        ↓ custom monitoring log / syslog
        ↓ command polling API
        ↓ koneksi Accurate ke Firebird

[ZeroTier Private Network]

        ↓

[VPS Linux]
- RSyslog Server
- Laravel Dashboard
- Laravel Parser / Scheduler
- MySQL/MariaDB Monitoring DB
- Firebird 2.5 Accurate DB
- Accurate Audit Reader
- Telegram Alert Service
- Remote Command API

        ↓

[Administrator IT]
- Browser Dashboard
- Telegram Alert
- Remote Desktop
```

---

## 10. Modul Sistem

### 10.1 Admin Authentication

Fungsi:

1. Menyediakan halaman login.
2. Memvalidasi email dan password admin.
3. Melindungi semua halaman dashboard.
4. Menyediakan logout.

Acceptance criteria:

```text
Given admin belum login
When admin membuka /dashboard
Then sistem mengarahkan admin ke /login
```

---

### 10.2 Device Registry

Fungsi:

1. Mendaftarkan device Windows pertama kali ketika agent mengirim heartbeat.
2. Menyimpan agent_id sebagai identitas utama.
3. Menyimpan hostname sebagai identitas teknis.
4. Menyediakan device_label agar admin bisa memberi nama ramah seperti “Laptop Finance 1”.
5. Menyimpan IP ZeroTier.
6. Menyimpan last_seen_at.
7. Menentukan status device: online, warning, offline, unknown.

Aturan identitas device:

```text
Primary identity : agent_id
Secondary        : hostname
Display name     : device_label
Network identity : ip_zerotier
```

Larangan:

```text
Program tidak boleh mengandalkan hostname hardcoded seperti WIN-ACC-01.
Nama WIN-ACC-01 dan WIN-ACC-02 hanya contoh dokumentasi.
```

---

### 10.3 Windows Monitoring Agent

Fungsi:

1. Berjalan di laptop Windows.
2. Mengambil hostname Windows.
3. Mengambil user Windows aktif.
4. Mengambil IP ZeroTier.
5. Mengambil CPU usage.
6. Mengambil RAM usage.
7. Mengambil disk usage.
8. Mengambil uptime dan last boot time.
9. Mengecek status `accurate.exe`.
10. Mengecek koneksi ke Firebird host dan port 3051.
11. Mengecek status RDP service.
12. Mengirim telemetry ke RSyslog Server dalam format custom log.
13. Melakukan polling ke Laravel API untuk command remote action.
14. Mengirim hasil eksekusi command kembali ke server.

Contoh custom log:

```text
device-monitor: agent_id=uuid hostname=DESKTOP-XYZ user=DESKTOP-XYZ\Finance ip_zerotier=10.10.10.21 status=online rdp=available
network-monitor: agent_id=uuid target=VPS-FIREBIRD port=3051 status=connected latency_ms=24
perf-monitor: agent_id=uuid cpu=42 ram=68 disk=55 uptime_minutes=450 status=normal
accurate-process-monitor: agent_id=uuid process=accurate.exe status=running user=DESKTOP-XYZ\Finance
```

---

### 10.4 RSyslog Server

Fungsi:

1. Menerima custom monitoring log dari Windows Agent.
2. Menerima log melalui TCP/UDP syslog.
3. Menyimpan raw log ke folder terpusat.
4. Menyediakan file raw log yang dibaca Laravel parser.

Catatan:

```text
RSyslog tidak mengambil data dari Windows.
Windows Agent yang mengambil data dan mengirimkannya ke RSyslog Server.
```

---

### 10.5 Log Parser

Fungsi:

1. Membaca raw log dari folder RSyslog.
2. Mengenali source/tag log: device-monitor, network-monitor, perf-monitor, accurate-process-monitor, service-monitor.
3. Memecah key-value log menjadi data terstruktur.
4. Menyimpan raw log ke Advanced Logs.
5. Mengupdate tabel device dan telemetry terkait.
6. Membuat alert jika rule terpenuhi.
7. Menjaga anti-duplikasi log.

Output minimal:

```text
logged_at
hostname
agent_id
source_tag
category
severity
raw_message
parsed_data_json
hash
```

---

### 10.6 Device Monitoring

Fungsi:

1. Menampilkan seluruh device Windows yang terdaftar.
2. Menampilkan status online/warning/offline.
3. Menampilkan user Windows aktif.
4. Menampilkan IP ZeroTier.
5. Menampilkan last seen.
6. Menampilkan agent status.
7. Menampilkan RDP status.
8. Menampilkan ringkasan performa dan koneksi Firebird.

Status device:

| Status | Kondisi |
|---|---|
| Online | Heartbeat masuk dalam batas normal. |
| Warning | Heartbeat terlambat atau ada metrik warning. |
| Offline | Tidak ada heartbeat melewati batas kritis. |
| Unknown | Device baru atau data belum lengkap. |

---

### 10.7 Network & Firebird Connectivity

Fungsi:

1. Mengecek konektivitas client ke VPS.
2. Mengecek port Firebird 3051.
3. Menyimpan latency.
4. Menyimpan status connected, timeout, refused, unreachable.
5. Membuat alert jika koneksi bermasalah.

Contoh status:

```text
WIN-ACC-01 → VPS-FIREBIRD:3051 = connected, latency 24ms
WIN-ACC-02 → VPS-FIREBIRD:3051 = timeout, retry 3x
```

Catatan penting:

```text
Jika satu device gagal konek Firebird, belum tentu Firebird service mati.
Jika Firebird service mati di VPS, dampaknya kemungkinan ke semua client.
```

---

### 10.8 Performance & Hang Indicator

Fungsi:

1. Menampilkan CPU usage.
2. Menampilkan RAM usage.
3. Menampilkan disk usage.
4. Menampilkan uptime.
5. Menampilkan heartbeat delay.
6. Menampilkan indikasi lambat/hang berdasarkan kombinasi metrik.

Contoh rule awal:

| Kondisi | Status |
|---|---|
| CPU < 80% | Normal |
| CPU >= 80% dan < 90% | Warning |
| CPU >= 90% | Critical |
| RAM >= 85% | Warning |
| Heartbeat terlambat + CPU/RAM tinggi | Indikasi lambat |
| Heartbeat hilang > threshold | Offline / potensi hang |

---

### 10.9 Accurate Process Monitoring

Fungsi:

1. Mengecek apakah `accurate.exe` berjalan pada client Windows.
2. Menyimpan status running/not_running.
3. Menyimpan user Windows pemilik proses jika tersedia.
4. Menampilkan status pada Device Detail dan Dashboard.
5. Membuat alert jika Accurate tidak berjalan pada jam kerja dan device/user aktif.

Severity awal:

| Kondisi | Severity |
|---|---|
| Accurate tidak berjalan di luar jam kerja | No alert / info only |
| Accurate tidak berjalan saat jam kerja dan user aktif | Warning |
| Accurate crash berulang jika dapat terdeteksi | Error |

---

### 10.10 Firebird Service Monitoring

Fungsi:

1. Mengecek status service Firebird di VPS.
2. Mengecek apakah port 3051 terbuka.
3. Menampilkan status pada Dashboard.
4. Membuat alert CRITICAL jika Firebird service mati.

Contoh alert:

```text
CRITICAL - Service Firebird pada VPS tidak aktif
Target     : VPS-FIREBIRD
Detected by: Server Health Checker
Evidence   : service_status=inactive, port_3051=closed
Impact     : Client Accurate tidak dapat mengakses database
Action     : Cek VPS / restart Firebird service
```

---

### 10.11 Accurate Audit Trail

Fungsi:

1. Menghubungkan Laravel ke database Firebird secara read-only.
2. Membaca tabel audit Accurate sesuai hasil POC.
3. Mengambil user Accurate melalui relasi audit ke tabel user.
4. Menyimpan hasil audit ke database monitoring.
5. Menampilkan audit trail dalam dashboard.
6. Menyediakan filter tanggal, username, source/modul, transaction type, keyword.

Prinsip:

```text
Accurate Audit Trail tidak dikirim lewat RSyslog.
Audit dibaca langsung dari Firebird menggunakan koneksi read-only.
Sistem tidak boleh melakukan update/delete/write ke database Accurate.
```

Data minimal:

```text
audit_id
activity_time
accurate_user_id
accurate_username
accurate_fullname
source_module
transaction_type
description
reference_no
app_version
status
raw_data_json
```

Catatan:

```text
Field yang tidak tersedia atau sering kosong tidak boleh dipaksa menjadi sumber utama.
Jika IP atau computer name tidak konsisten di AUDIT, tampilkan sebagai nullable.
```

---

### 10.12 Alerts

Fungsi:

1. Menampilkan daftar peringatan yang jelas dan actionable.
2. Setiap alert wajib memiliki target, detected_by, evidence, impact, dan recommended_action.
3. Alert tidak boleh berupa teks generik tanpa konteks.
4. Alert dapat dikirim ke Telegram.
5. Alert dapat diubah status menjadi acknowledged/resolved.

Format alert wajib:

```text
severity
target_type
target_name
title
detected_by
evidence
impact
recommended_action
status
detected_at
```

Contoh alert valid:

```text
WARNING - CPU tinggi pada Laptop Finance 2
Target     : Laptop Finance 2
Hostname   : DESKTOP-XYZ
Detected by: Windows Agent
Evidence   : CPU 87% selama 5 menit
Impact     : Accurate berpotensi lambat
Action     : Cek aplikasi berjalan atau gunakan Remote Desktop
```

Contoh alert tidak valid:

```text
WARNING - CPU tinggi
CRITICAL - Firebird unreachable
Accurate process not running
```

---

### 10.13 Telegram Alert

Fungsi:

1. Mengirim alert penting ke Telegram.
2. Telegram wajib ada pada produk v2.
3. Telegram menjadi bagian novelty sebagai contextual proactive alert.
4. Telegram tidak boleh hanya mengirim pesan singkat generik.
5. Telegram memakai anti-spam/cooldown.

Format pesan:

```text
[SEVERITY] Judul Alert

Target     : ...
Device     : ...
Detected by: ...
Evidence   : ...
Impact     : ...
Action     : ...
Time       : ...
Dashboard  : ...
```

Anti-spam awal:

```text
Alert dengan target, type, dan severity yang sama tidak dikirim ulang dalam 5 menit,
kecuali severity naik atau status berubah menjadi critical.
```

---

### 10.14 Incidents

Fungsi:

1. Menampilkan masalah operasional berdasarkan korelasi beberapa evidence.
2. Incident berbeda dari alert.
3. Incident membantu admin memahami konteks masalah.

Contoh incident:

```text
WIN-ACC-02 terindikasi lambat
Evidence:
- CPU 87% selama 5 menit
- RAM 86%
- Firebird latency 650ms
- User aktif DESKTOP-XYZ\Finance
Recommended action:
- Remote Desktop ke device
- Tutup aplikasi berat
- Restart client jika diperlukan
```

Catatan:

```text
Audit activity spike detection tidak masuk MVP kecuali rule sudah jelas.
```

---

### 10.15 Remote Desktop Launcher

Fungsi:

1. Menyediakan tombol Remote Desktop pada Device Detail.
2. Tombol selalu tersedia untuk device terdaftar.
3. Jika device offline, tombol disabled atau menampilkan warning.
4. Sistem dapat menghasilkan command atau file `.rdp` berdasarkan IP ZeroTier.
5. Action dicatat ke remote_actions.

Contoh:

```text
mstsc /v:10.10.10.21
```

---

### 10.16 Remote Restart Client

Fungsi:

1. Admin dapat melakukan restart client Windows dari dashboard.
2. Restart hanya manual, tidak otomatis.
3. Admin wajib melakukan konfirmasi.
4. Admin wajib mengisi alasan.
5. Laravel membuat pending command.
6. Windows Agent mengambil command melalui polling API.
7. Windows Agent menjalankan restart.
8. Hasil command dikirim kembali ke server.
9. Semua action dicatat.

Alur:

```text
Admin klik Restart Client
        ↓
Modal konfirmasi + alasan
        ↓
Laravel membuat remote command status=pending
        ↓
Windows Agent polling command
        ↓
Agent menjalankan shutdown /r /t 30
        ↓
Agent mengirim result
        ↓
Dashboard menampilkan status action
```

Larangan:

```text
Sistem tidak boleh melakukan auto restart tanpa admin.
Sistem tidak boleh mengirim command restart melalui RSyslog.
```

---

### 10.17 Advanced Logs

Fungsi:

1. Menampilkan raw log dan parsed log untuk investigasi teknis.
2. Menyediakan filter tanggal, hostname, category, severity, keyword.
3. Tidak menjadi pusat tampilan dashboard utama.
4. Menyimpan raw message sebagai audit teknis.

Contoh data:

```text
Waktu
Hostname
Agent ID
Source Tag
Category
Severity
Raw Message
Parsed Data
Source File
```

---

### 10.18 Settings

Fungsi:

1. Mengatur threshold monitoring.
2. Mengatur Telegram Bot Token dan Chat ID.
3. Mengatur Firebird host, port, database path, dan credential read-only.
4. Mengatur heartbeat interval.
5. Mengatur offline threshold.
6. Mengatur remote action policy.
7. Mengatur working hour untuk rule Accurate process.
8. Mengatur label device.

Contoh setting:

```text
CPU warning threshold = 80
CPU critical threshold = 90
RAM warning threshold = 85
Heartbeat warning minutes = 5
Heartbeat critical minutes = 15
Firebird port = 3051
Telegram alert enabled = true
Remote restart enabled = true
Restart requires reason = true
```

---

## 11. Struktur Menu Produk

Menu final:

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

### 11.1 Dashboard

Dashboard menampilkan ringkasan:

```text
Device online
Firebird connection status
Accurate active clients
Open alerts
Open incidents
Recent Accurate Audit
Device Health Table
Recent Incidents
```

Dashboard tidak boleh didominasi raw log.

### 11.2 Devices

Menampilkan daftar device Windows dan action.

### 11.3 Device Detail

Menampilkan detail satu device, termasuk action Remote Desktop dan Restart Client.

### 11.4 Accurate Audit

Menampilkan audit trail Accurate dari Firebird.

### 11.5 Incidents

Menampilkan korelasi masalah operasional.

### 11.6 Remote Actions

Menampilkan riwayat tindakan admin.

### 11.7 Alerts

Menampilkan alert spesifik dan actionable.

### 11.8 Advanced Logs

Menampilkan raw log/parsed log untuk investigasi teknis.

### 11.9 Settings

Mengatur konfigurasi sistem.

---

## 12. Data Source Mapping

| Modul | Sumber Data | Lewat RSyslog? | Catatan |
|---|---|---:|---|
| Device Status | Windows Agent | Ya | Heartbeat, hostname, user, IP. |
| CPU/RAM/Disk | Windows Agent | Ya | Dikirim periodik. |
| Firebird Connectivity | Windows Agent | Ya | Client test ke VPS:3051. |
| Accurate Process | Windows Agent | Ya | Cek process accurate.exe. |
| RDP Status | Windows Agent | Ya | Cek service/port RDP jika memungkinkan. |
| Firebird Service | VPS Server Checker | Bisa langsung / RSyslog | Cek service di server. |
| Accurate Audit Trail | Firebird AUDIT + USERS | Tidak | Direct read-only. |
| Alerts | Alert Engine | Tidak langsung | Dibuat dari telemetry/audit/rule. |
| Telegram | Telegram Service | Tidak | Kirim alert. |
| Remote Desktop | Dashboard launcher | Tidak | Action dicatat. |
| Restart Client | Laravel API + Windows Agent polling | Tidak | Manual controlled action. |
| Advanced Logs | RSyslog raw logs | Ya | Untuk investigasi. |

---

## 13. Alert Policy

Alert harus mengikuti aturan berikut:

1. Harus memiliki target yang jelas.
2. Harus memiliki sumber deteksi.
3. Harus memiliki evidence.
4. Harus memiliki impact.
5. Harus memiliki recommended action.
6. Tidak boleh berupa kalimat generik.
7. Tidak boleh membuat alert untuk kondisi normal.
8. Tidak boleh spam Telegram.
9. Severity harus berdasarkan rule/threshold.
10. Alert dapat berubah status menjadi open, acknowledged, resolved.

Contoh alert rule awal:

| Code | Target | Condition | Severity |
|---|---|---|---|
| DEVICE_HEARTBEAT_MISSED | Device | last_seen > 5 menit | WARNING |
| DEVICE_OFFLINE | Device | last_seen > 15 menit | CRITICAL |
| CPU_HIGH | Device | CPU >= 80% selama 5 menit | WARNING |
| CPU_CRITICAL | Device | CPU >= 90% selama 3 menit | CRITICAL |
| RAM_HIGH | Device | RAM >= 85% | WARNING |
| FIREBIRD_PORT_TIMEOUT | Device → VPS | TCP 3051 timeout 3 kali | ERROR |
| FIREBIRD_LATENCY_HIGH | Device → VPS | latency > threshold | WARNING |
| FIREBIRD_SERVICE_DOWN | VPS | service inactive atau port closed | CRITICAL |
| ACCURATE_PROCESS_NOT_DETECTED | Device | accurate.exe tidak berjalan saat jam kerja | WARNING |
| ACCURATE_AUDIT_DELETE | Accurate Audit | aktivitas delete jika field tersedia | CRITICAL |

---

## 14. Success Metrics

Produk dianggap berhasil jika:

1. Dua laptop Windows dapat mengirim heartbeat ke VPS.
2. Dashboard menampilkan dua device dengan status akurat.
3. Dashboard menampilkan user Windows aktif dari masing-masing device.
4. Dashboard menampilkan CPU/RAM/disk masing-masing device.
5. Dashboard menampilkan status koneksi client ke Firebird VPS:3051.
6. Dashboard menampilkan status proses Accurate 5.
7. Accurate Audit menampilkan data audit dari Firebird secara read-only.
8. Alert terbentuk dengan target dan evidence yang jelas.
9. Telegram menerima alert kontekstual.
10. Remote Desktop launcher dapat digunakan untuk device yang online.
11. Restart client dapat dibuat manual, diterima agent, dieksekusi, dan tercatat.
12. Raw log tetap dapat dilihat di Advanced Logs.
13. Tidak ada device yang hardcoded dalam logic program.

---

## 15. Acceptance Criteria Produk

### AC-001 — Device Real Terdaftar Otomatis

```text
Given Windows Agent berjalan pada laptop client
When agent mengirim heartbeat pertama kali
Then sistem membuat record device berdasarkan agent_id dan hostname
And device muncul pada halaman Devices
```

### AC-002 — Dashboard Menampilkan Device Real

```text
Given dua laptop Windows mengirim telemetry
When admin membuka Dashboard
Then sistem menampilkan 2 device online dengan data CPU/RAM/disk dan user Windows
```

### AC-003 — Koneksi Firebird Terdeteksi

```text
Given Accurate client harus terhubung ke Firebird pada VPS
When agent melakukan TCP check ke port 3051
Then dashboard menampilkan status connected/timeout/refused beserta latency
```

### AC-004 — Accurate Process Terdeteksi

```text
Given Accurate 5 berjalan pada laptop Windows
When agent membaca process list
Then dashboard menampilkan accurate.exe sebagai running pada device tersebut
```

### AC-005 — Accurate Audit Terbaca

```text
Given database Firebird Accurate memiliki data audit
When Accurate Audit Reader berjalan
Then data audit disimpan ke database monitoring dan tampil di halaman Accurate Audit
```

### AC-006 — Alert Memiliki Evidence

```text
Given CPU device melebihi threshold
When alert dibuat
Then alert harus memiliki target device, value CPU, waktu deteksi, dan recommended action
```

### AC-007 — Telegram Mengirim Alert Kontekstual

```text
Given alert critical dibuat
When Telegram alert aktif
Then sistem mengirim pesan Telegram berisi target, evidence, impact, dan action
```

### AC-008 — Remote Desktop Launcher Tersedia

```text
Given device memiliki IP ZeroTier
When admin membuka Device Detail
Then tombol Remote Desktop tersedia dan menghasilkan target RDP ke IP device
```

### AC-009 — Restart Client Manual

```text
Given admin ingin restart client
When admin klik Restart dan mengisi alasan
Then sistem membuat pending command
And agent mengambil command tersebut
And hasil action tersimpan di remote_actions
```

### AC-010 — Advanced Logs Tidak Menjadi Fokus Dashboard

```text
Given banyak raw log masuk
When admin membuka Dashboard
Then dashboard tetap menampilkan ringkasan operasional
And raw log hanya ditampilkan pada menu Advanced Logs
```

---

## 16. Risiko Produk dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Agent Windows mati | Device terlihat offline | Alert heartbeat missed/offline. |
| ZeroTier tidak tersambung | Client tidak bisa komunikasi ke VPS | Tampilkan status connectivity dan dokumentasi setup. |
| Firebird credential salah | Audit reader gagal | Settings test connection dan error log. |
| Firebird DB terkunci/akses terbatas | Audit tidak terbaca | Read-only user dan retry mechanism. |
| Telegram token salah | Alert tidak terkirim | Simpan status failed di notification log. |
| Restart client berbahaya | Data user bisa hilang | Wajib konfirmasi, alasan, dan manual only. |
| Log terlalu banyak | Dashboard lambat | Advanced Logs pagination dan parser batching. |
| Device hostname berubah | Device dobel | Gunakan agent_id sebagai primary identity. |
| Alert terlalu banyak | Admin terganggu | Cooldown/anti-spam dan severity policy. |

---

## 17. GitHub dan Iterasi Pengembangan

Pengembangan produk harus memakai GitHub agar aman dikembangkan bertahap.

Branch yang disarankan:

```text
main        = versi stabil
work/dev    = development aktif
feature/*   = fitur baru
fix/*       = perbaikan bug
```

Prinsip:

1. Setiap fitur besar dibuat dalam branch terpisah.
2. Perubahan penting harus dicommit dengan pesan jelas.
3. Versi stabil harus bisa dideploy ulang ke VPS.
4. Dokumentasi v2 menjadi acuan utama Codex dan developer.
5. Source lama tidak dijadikan fondasi jika sudah dianggap tidak rapi.

---

## 18. Prioritas Implementasi

Walaupun produk ditargetkan real-device, implementasi tetap dilakukan bertahap agar stabil.

### Phase 1 — Core Foundation

1. Laravel project skeleton.
2. Auth admin.
3. Layout dashboard.
4. Database migration core.
5. Device registry.
6. RSyslog server di VPS.
7. Basic parser untuk custom log.

### Phase 2 — Real Windows Agent

1. Agent kirim heartbeat.
2. Agent kirim hostname/user/IP ZeroTier.
3. Agent kirim CPU/RAM/disk.
4. Agent kirim status Accurate process.
5. Agent kirim status Firebird connectivity.

### Phase 3 — Dashboard Operasional

1. Dashboard summary.
2. Devices page.
3. Device detail.
4. Advanced Logs.
5. Basic alerts.

### Phase 4 — Telegram Contextual Alert

1. Alert engine dengan target/evidence.
2. Telegram message formatter.
3. Cooldown/anti-spam.
4. Notification history.

### Phase 5 — Accurate Audit Trail

1. Firebird connection read-only.
2. Audit reader.
3. Sync offset.
4. Accurate Audit page.
5. Audit detail/filter.

### Phase 6 — Remote Administration

1. RDP launcher.
2. Remote command table.
3. Agent command polling.
4. Restart client manual.
5. Remote action audit log.

---

## 19. Batasan Bahasa dan Istilah UI

UI sebaiknya memakai bahasa Indonesia yang jelas untuk admin.

Contoh label:

| Istilah Teknis | Label UI yang Disarankan |
|---|---|
| Heartbeat | Status Agent / Last Seen |
| Firebird Connectivity | Koneksi Database Accurate |
| Accurate Process | Status Aplikasi Accurate |
| Incident | Indikasi Masalah |
| Advanced Logs | Log Teknis |
| Remote Actions | Riwayat Tindakan Remote |
| Evidence | Bukti Deteksi |
| Recommended Action | Saran Tindakan |

---

## 20. Kesimpulan PRD

PRD v2 ini mengunci arah produk sebagai **Centralized Log Monitoring Dashboard** untuk monitoring real-device pada lingkungan Accurate 5 skala kecil. Produk tidak lagi berfokus pada log random atau simulasi container, tetapi pada kebutuhan nyata Administrator IT:

```text
melihat device aktif,
melihat user Windows,
melihat koneksi ke Firebird,
melihat performa device,
melihat status Accurate,
melihat audit trail Accurate,
menerima alert Telegram kontekstual,
dan melakukan remote administration manual.
```

Dokumen ini menjadi dasar untuk dokumen berikutnya:

```text
02_SRS_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md
```

