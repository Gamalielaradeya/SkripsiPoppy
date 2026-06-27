# 12 — Real-Device Demonstration Script v2
# Centralized Log Monitoring Dashboard

**Versi:** 2.0  
**Nama Sistem:** Centralized Log Monitoring Dashboard  
**Target Implementasi:** Real-device monitoring untuk laptop Windows pengguna Accurate 5 pada lingkungan PT XYZ skala kecil  
**Stack:** VPS Linux, ZeroTier, RSyslog, Laravel, MySQL, Firebird 2.5, Windows Agent, Blade, Tailwind CSS, Alpine.js, Chart.js, Telegram Bot  
**Status:** Final untuk panduan AI Agent / Codex / developer / narasi sidang  

---

## 1. Tujuan Dokumen

Dokumen ini berisi skenario demonstrasi dan alur presentasi sistem **Centralized Log Monitoring Dashboard** versi real-device.

Berbeda dari versi awal yang menggunakan container `rsyslog-client` sebagai simulasi pengirim log, demonstrasi v2 ditujukan untuk memperlihatkan sistem yang berjalan pada perangkat nyata:

1. Dua laptop Windows sebagai client Accurate 5.
2. Satu VPS Linux sebagai pusat monitoring dan server database.
3. ZeroTier sebagai jaringan privat antar perangkat.
4. Windows Agent sebagai pengambil data kondisi laptop Windows.
5. RSyslog Server sebagai penerima log/status terstruktur dari Windows Agent.
6. Laravel sebagai dashboard, parser, alert engine, Telegram notifier, Accurate Audit Reader, dan remote command controller.
7. Firebird 2.5 pada VPS sebagai database Accurate.
8. Telegram sebagai media alert kontekstual.
9. Remote Desktop dan Remote Restart sebagai fitur tindakan manual terkontrol oleh administrator.

Dokumen ini dipakai untuk:

1. Menyiapkan presentasi/pengujian real-device.
2. Menjaga alur demo tetap fokus pada masalah skripsi.
3. Mencegah demo menampilkan data random yang tidak relevan.
4. Menjadi acuan Bab 4 bagian implementasi dan pengujian.
5. Menjadi acuan Codex agar membuat fitur yang bisa didemokan secara runtut.

---

## 2. Prinsip Demonstrasi v2

Demonstrasi v2 harus mengikuti prinsip berikut:

| Prinsip | Penjelasan |
|---|---|
| Real-device first | Sistem menunjukkan monitoring laptop Windows nyata, bukan dummy container sebagai fokus utama. |
| Office-like scenario | Skenario menggambarkan kondisi kantor kecil PT XYZ yang memakai Accurate 5. |
| Status-first | Dashboard harus menjawab status device, koneksi, performa, Accurate, dan audit trail. |
| Contextual alert | Alert harus memiliki target, bukti/evidence, dampak, dan saran tindakan. |
| No random raw log | Raw log boleh ditampilkan hanya di menu Advanced Logs, bukan sebagai dashboard utama. |
| Manual controlled action | Remote Desktop dan Restart Client hanya dilakukan oleh admin, bukan otomatis. |
| Audit-friendly | Tindakan admin dan data audit Accurate harus tercatat. |
| Fallback-aware | Jika fitur live tertentu gagal saat presentasi, sistem tetap bisa dijelaskan dengan data terakhir yang sudah tersimpan. |

---

## 3. Lingkungan Implementasi yang Didemokan

### 3.1 Komponen Perangkat

| Komponen | Peran |
|---|---|
| VPS Linux | Pusat monitoring, RSyslog Server, Laravel Dashboard, MySQL, Firebird, Accurate Audit Reader, Telegram Alert Service, Remote Command API. |
| Laptop Windows 1 | Client Accurate 5, menjalankan Windows Agent, terhubung ZeroTier, RDP enabled. |
| Laptop Windows 2 | Client Accurate 5, menjalankan Windows Agent, terhubung ZeroTier, RDP enabled. |
| Browser Admin | Mengakses dashboard Laravel dari VPS melalui IP/domain. |
| Telegram App/Web | Menerima contextual alert dari sistem. |

### 3.2 Jaringan

Seluruh komponen terhubung melalui ZeroTier private network.

Contoh ilustrasi:

```text
[Windows Laptop 1]
- Accurate 5
- Windows Agent
- ZeroTier
- RDP enabled

[Windows Laptop 2]
- Accurate 5
- Windows Agent
- ZeroTier
- RDP enabled

        ↓ private network ZeroTier

[VPS Linux]
- RSyslog Server
- Laravel Dashboard
- MySQL
- Firebird 2.5 / Accurate DB
- Accurate Audit Reader
- Telegram Alert Service
- Remote Command API
```

### 3.3 Catatan Nama Device

Nama seperti berikut hanya contoh dokumentasi:

```text
WIN-ACC-01
WIN-ACC-02
VPS-MONITOR
VPS-FIREBIRD
```

Implementasi tidak boleh hardcode nama device. Sistem harus menggunakan:

```text
agent_id      = identitas utama device
hostname      = hostname aktual dari Windows
device_label  = nama tampilan yang bisa diedit admin
ip_zerotier   = IP ZeroTier device
```

---

## 4. Fitur yang Wajib Terlihat Saat Demonstrasi

Demonstrasi dianggap berhasil jika fitur berikut dapat ditunjukkan:

| Area | Fitur yang Ditunjukkan |
|---|---|
| Device Monitoring | Device auto-register, hostname, Windows user, IP ZeroTier, last seen, agent status. |
| Network Monitoring | Ping/latency ke VPS, status port Firebird 3051, status connected/timeout. |
| Performance Monitoring | CPU, RAM, Disk, uptime, indikasi lambat/hang. |
| Accurate Process | Deteksi `accurate.exe` running/tidak pada laptop Windows. |
| Accurate Audit Trail | Data perubahan aktivitas Accurate dari Firebird `AUDIT + USERS`. |
| Contextual Alert | Alert dengan target, evidence, impact, recommended action. |
| Telegram Alert | Alert terkirim ke Telegram dengan format kontekstual. |
| Remote Desktop | Admin dapat membuka/menghasilkan koneksi RDP ke device. |
| Remote Restart | Admin dapat membuat perintah restart manual dengan konfirmasi dan alasan. |
| Advanced Logs | Raw/structured log dapat dilihat untuk investigasi teknis. |
| Remote Actions | Riwayat tindakan admin tercatat. |

---

## 5. Hal yang Tidak Boleh Menjadi Fokus Demo

Berikut hal yang harus dihindari saat demonstrasi:

1. Menampilkan raw Windows Event Log random sebagai halaman utama.
2. Menonjolkan log seperti DistributedCOM, System noise, atau event OS yang tidak menjawab masalah skripsi.
3. Menampilkan alert tanpa target, tanpa evidence, atau tanpa sumber deteksi.
4. Mengatakan sistem melakukan auto restart.
5. Mengatakan sistem melakukan blocking otomatis seperti IPS.
6. Menggunakan tabel `LOGIN` Accurate sebagai sumber utama login/audit.
7. Mengklaim `COMP_NAME` dan `IPADDRESS` dari tabel `AUDIT` selalu tersedia.
8. Mengklaim audit spike detection jika belum ada rule dan perhitungan yang jelas.
9. Menggunakan data simulasi sebagai bukti utama jika real-device sudah tersedia.
10. Mengubah scope menjadi SIEM enterprise, ELK, Grafana, Prometheus, atau IDS/IPS.

---

## 6. Checklist Persiapan Sebelum Demonstrasi

### 6.1 Checklist VPS

```text
[ ] VPS dapat diakses melalui SSH.
[ ] ZeroTier aktif di VPS.
[ ] VPS sudah join network ZeroTier yang sama dengan laptop Windows.
[ ] RSyslog Server aktif dan listen TCP/UDP pada port syslog yang dipakai.
[ ] Laravel app aktif dan bisa diakses dari browser.
[ ] MySQL aktif.
[ ] Firebird 2.5 aktif.
[ ] Database Accurate dapat diakses dari Firebird.
[ ] Accurate Audit Reader dapat membaca AUDIT + USERS secara read-only.
[ ] Scheduler/queue Laravel aktif jika digunakan.
[ ] Telegram Bot Token dan Chat ID sudah dikonfigurasi.
[ ] Remote Command API aktif.
[ ] Firewall VPS mengizinkan port yang diperlukan hanya pada jalur aman/ZeroTier.
```

### 6.2 Checklist Laptop Windows

Untuk setiap laptop Windows:

```text
[ ] ZeroTier aktif dan terhubung ke network yang sama.
[ ] Laptop dapat ping IP ZeroTier VPS.
[ ] Accurate 5 terpasang.
[ ] Accurate dapat membuka database Firebird di VPS.
[ ] Windows Agent berjalan.
[ ] Windows Agent memiliki agent_id unik.
[ ] Windows Agent dapat mengirim heartbeat ke RSyslog Server.
[ ] Windows Agent dapat membaca Windows user aktif.
[ ] Windows Agent dapat membaca CPU/RAM/Disk.
[ ] Windows Agent dapat mengecek accurate.exe.
[ ] Windows Agent dapat mengecek koneksi Firebird port 3051.
[ ] RDP aktif jika fitur Remote Desktop akan ditunjukkan.
[ ] Remote command polling aktif jika restart manual akan ditunjukkan.
```

### 6.3 Checklist Dashboard

```text
[ ] Admin bisa login.
[ ] Dashboard menampilkan minimal 2 device.
[ ] Device page menampilkan device online.
[ ] Device Detail menampilkan telemetry real.
[ ] Accurate Audit menampilkan data audit.
[ ] Alerts memiliki minimal 1 contoh alert kontekstual.
[ ] Telegram alert berhasil terkirim.
[ ] Remote Actions menampilkan riwayat tindakan.
[ ] Advanced Logs menampilkan log terstruktur dari RSyslog.
[ ] Settings menampilkan threshold dan konfigurasi utama.
```

### 6.4 Checklist Browser Saat Presentasi

Sebaiknya siapkan tab browser:

```text
Tab 1: Dashboard
Tab 2: Devices
Tab 3: Device Detail salah satu laptop
Tab 4: Accurate Audit
Tab 5: Alerts
Tab 6: Remote Actions
Tab 7: Advanced Logs
Tab 8: Telegram Web/Desktop atau HP Telegram
```

### 6.5 Checklist Terminal

Siapkan terminal SSH ke VPS untuk memperlihatkan komponen teknis jika ditanya:

```bash
ssh user@vps
systemctl status rsyslog
systemctl status firebird*
sudo tail -f /var/log/remote/all.log
php artisan schedule:list
php artisan queue:work --once
```

Perintah yang dipakai harus disesuaikan dengan service name aktual di VPS.

---

## 7. Narasi Pembukaan Presentasi

Contoh narasi:

> Sistem yang dibangun adalah Centralized Log Monitoring Dashboard untuk membantu Administrator IT memantau perangkat pengguna Accurate 5 pada lingkungan PT XYZ skala kecil. Sistem ini menggunakan VPS Linux sebagai pusat monitoring, dua laptop Windows sebagai client Accurate, ZeroTier sebagai jaringan privat, RSyslog sebagai log collector, Laravel sebagai dashboard dan alert engine, serta Firebird sebagai database Accurate.

Lanjutkan:

> Berbeda dari monitoring log mentah biasa, sistem ini tidak menampilkan seluruh log Windows secara random di halaman utama. Dashboard difokuskan pada informasi yang dibutuhkan IT, yaitu status device, user Windows aktif, koneksi ke database Firebird, performa device, status proses Accurate, audit trail perubahan data Accurate, contextual alert Telegram, dan tindakan manual seperti Remote Desktop serta Restart Client.

Lanjutkan:

> Sistem ini tetap bersifat monitoring dan alert. Sistem tidak melakukan auto blocking dan tidak melakukan auto restart. Tindakan restart hanya dapat dilakukan secara manual oleh admin melalui dashboard dan seluruh tindakan dicatat sebagai audit tindakan.

---

## 8. Alur Presentasi Utama

Estimasi durasi presentasi teknis: 12–20 menit.

| Tahap | Durasi | Fokus |
|---|---:|---|
| 1. Pembukaan | 1–2 menit | Masalah PT XYZ dan tujuan sistem. |
| 2. Arsitektur | 2 menit | VPS, Windows client, ZeroTier, RSyslog, Laravel, Firebird. |
| 3. Login Dashboard | 1 menit | Proteksi akses admin. |
| 4. Dashboard Overview | 3 menit | Ringkasan device, Firebird, Accurate, audit, alert. |
| 5. Devices & Detail | 3 menit | Device real, Windows user, CPU/RAM/Disk, connectivity. |
| 6. Accurate Audit | 3 menit | Audit trail dari Firebird AUDIT + USERS. |
| 7. Contextual Alert + Telegram | 3 menit | Alert berbasis target/evidence dan notifikasi Telegram. |
| 8. Remote Action | 3 menit | RDP launcher dan restart manual dengan konfirmasi. |
| 9. Advanced Logs | 1 menit | Raw log sebagai investigasi teknis, bukan dashboard utama. |
| 10. Penutup | 1 menit | Manfaat dan batasan sistem. |

---

## 9. Demo 1 — Menjelaskan Arsitektur Real-Device

Tampilkan diagram atau jelaskan teks berikut:

```text
Windows Laptop 1 + Agent + Accurate 5
Windows Laptop 2 + Agent + Accurate 5
        ↓
ZeroTier Private Network
        ↓
VPS Linux
├── RSyslog Server
├── Laravel Dashboard
├── MySQL
├── Firebird Accurate DB
├── Accurate Audit Reader
├── Telegram Alert Service
└── Remote Command API
```

Narasi:

> Laptop Windows berperan sebagai perangkat pengguna Accurate. Masing-masing laptop menjalankan Windows Agent yang mengambil data kondisi device, seperti user Windows aktif, CPU, RAM, disk, status Accurate, dan koneksi ke Firebird. Data tersebut dikirim dalam format log terstruktur ke RSyslog Server pada VPS. Laravel kemudian membaca log tersebut, memprosesnya menjadi data terstruktur, dan menampilkannya di dashboard.

Poin penting:

```text
- RSyslog hanya menerima log/status, bukan menjalankan remote action.
- Accurate Audit Trail tidak dikirim lewat RSyslog.
- Accurate Audit Trail dibaca langsung oleh Laravel dari Firebird secara read-only.
- Remote Desktop dan Restart Client menggunakan jalur action/API/agent, bukan RSyslog.
```

---

## 10. Demo 2 — Login Admin

Langkah:

1. Buka URL dashboard.
2. Login menggunakan akun admin.
3. Tunjukkan bahwa halaman dashboard tidak dapat dibuka tanpa login.

Narasi:

> Dashboard hanya dapat diakses oleh Administrator IT. Hal ini penting karena data yang ditampilkan mencakup status perangkat, aktivitas Accurate, serta fitur tindakan remote.

Expected result:

```text
Admin berhasil login dan diarahkan ke halaman Dashboard.
```

---

## 11. Demo 3 — Dashboard Overview

Buka halaman Dashboard.

Elemen yang perlu ditunjukkan:

```text
- Total device online
- Firebird connected clients
- Accurate running clients
- Audit events today
- Open alerts / incidents
- Device health table
- Latest Accurate audit
- Recent contextual alerts
```

Narasi:

> Halaman utama tidak difokuskan pada log mentah. Dashboard dirancang sebagai cockpit monitoring IT, sehingga admin langsung melihat kondisi perangkat, koneksi database Accurate, performa device, audit aktivitas Accurate, dan alert yang perlu ditindaklanjuti.

Contoh data yang diharapkan:

```text
Device Online        : 2/2
Firebird Connected   : 2/2
Accurate Running     : 2/2
Audit Events Today   : 35
Open Alerts          : 1
```

Poin yang harus ditekankan:

```text
- Data berasal dari device real.
- Device tidak hardcode.
- Device muncul karena agent mengirim heartbeat.
- Raw log tidak ditampilkan sebagai fokus utama dashboard.
```

---

## 12. Demo 4 — Devices Page

Buka halaman Devices.

Tunjukkan kolom:

```text
Device Label
Hostname
Agent ID
Windows User
IP ZeroTier
Last Seen
Agent Status
RDP Status
Accurate Status
Firebird Status
CPU/RAM/Disk
Status
Actions
```

Narasi:

> Halaman Devices menampilkan perangkat Windows yang terdaftar otomatis ketika Windows Agent mengirim heartbeat pertama. Sistem menggunakan agent_id sebagai identitas utama, sehingga tidak bergantung pada nama device yang di-hardcode.

Expected result:

```text
Dua laptop Windows muncul sebagai device aktif.
Masing-masing memiliki hostname, Windows user, IP ZeroTier, dan status agent.
```

Action yang dapat ditunjukkan:

```text
- Klik Detail
- Klik Ping Test bila tersedia
- Klik Remote Desktop
- Klik Restart Client, tetapi jangan langsung eksekusi tanpa konfirmasi
```

---

## 13. Demo 5 — Device Detail

Buka detail salah satu device.

Bagian yang perlu ditunjukkan:

### 13.1 Identity

```text
Device Label
Hostname
Agent ID
Windows User
IP ZeroTier
OS Version
Agent Version
Last Seen
```

### 13.2 Connectivity

```text
Ping to VPS
Firebird Host
Firebird Port 3051
TCP Connect Status
Latency
Last Check Time
```

### 13.3 Performance

```text
CPU Usage
RAM Usage
Disk Usage
Uptime
Last Boot
Hang Indicator
```

### 13.4 Accurate Process

```text
accurate.exe running / not detected
Process owner
Process path
Last detected time
```

### 13.5 Actions

```text
Remote Desktop
Restart Client
View Logs
View Alerts
```

Narasi:

> Device Detail digunakan admin untuk investigasi satu laptop. Informasi yang ditampilkan adalah data yang relevan untuk masalah operasional: siapa user yang sedang memakai laptop, apakah koneksi ke database Accurate berjalan, apakah CPU/RAM tinggi, dan apakah Accurate sedang berjalan.

Expected result:

```text
Device detail menampilkan data terbaru dari Windows Agent.
```

---

## 14. Demo 6 — Windows Agent Mengirim Log Terstruktur ke RSyslog

Jika ingin menunjukkan sisi teknis, buka terminal VPS:

```bash
sudo tail -f /var/log/remote/all.log
```

Contoh log yang seharusnya terlihat:

```text
device_heartbeat agent_id=... hostname=DESKTOP-ABC windows_user=DESKTOP-ABC\Finance ip_zerotier=10.x.x.x status=online rdp=available
perf_snapshot agent_id=... hostname=DESKTOP-ABC cpu=42 ram=61 disk=55 uptime_seconds=12345
network_check agent_id=... hostname=DESKTOP-ABC target=firebird-vps port=3051 status=connected latency_ms=18
accurate_process agent_id=... hostname=DESKTOP-ABC process=accurate.exe status=running owner=DESKTOP-ABC\Finance
```

Narasi:

> Windows Agent mengambil data dari Windows host asli, kemudian mengirim log terstruktur ke RSyslog Server. Format key=value dipilih agar parser Laravel lebih mudah mengubah log menjadi data terstruktur.

Poin penting:

```text
- RSyslog tidak mengambil data dari client.
- Client/agent yang mengirim data ke server.
- RSyslog hanya menerima dan menyimpan.
- Parser Laravel memproses log menjadi tabel monitoring.
```

---

## 15. Demo 7 — Network dan Firebird Connectivity

Pada Dashboard atau Device Detail, tunjukkan status koneksi Firebird:

```text
WIN-ACC-01 → VPS-FIREBIRD:3051 = Connected, latency 18 ms
WIN-ACC-02 → VPS-FIREBIRD:3051 = Connected, latency 24 ms
```

Narasi:

> Karena Accurate 5 pada laptop Windows harus terhubung ke database Firebird di VPS, sistem memantau konektivitas dari sisi client. Jika salah satu laptop gagal mengakses port Firebird, sistem dapat membedakan apakah masalah terjadi pada satu client atau pada service Firebird di VPS.

Contoh alert yang benar jika terjadi masalah satu client:

```text
ERROR - WIN-ACC-02 gagal terhubung ke Firebird VPS:3051
Target     : WIN-ACC-02 → VPS-FIREBIRD
Detected by: Windows Agent
Evidence   : tcp_connect timeout 3 kali
Impact     : Accurate pada device tersebut berpotensi tidak bisa membuka database
Action     : Cek koneksi ZeroTier atau lakukan Remote Desktop ke device
```

Contoh alert yang benar jika service Firebird di VPS mati:

```text
CRITICAL - Service Firebird pada VPS tidak aktif
Target     : VPS-FIREBIRD
Detected by: Server Health Checker
Evidence   : service_status=inactive, port_3051=closed
Impact     : Semua client Accurate berpotensi gagal mengakses database
Action     : Cek VPS dan restart service Firebird bila diperlukan
```

---

## 16. Demo 8 — Accurate Process Monitoring

Pada Devices atau Device Detail, tunjukkan status Accurate:

```text
Accurate Status: Running
Process        : accurate.exe
Owner          : DESKTOP-ABC\Finance
```

Narasi:

> Windows Agent juga memantau apakah Accurate 5 sedang berjalan pada laptop client. Data ini membantu admin membedakan apakah masalah berasal dari aplikasi Accurate yang tidak berjalan, koneksi Firebird, atau performa device.

Jika ingin memicu kondisi warning:

1. Tutup Accurate 5 pada salah satu laptop.
2. Tunggu Windows Agent mengirim snapshot berikutnya.
3. Cek dashboard/alert.

Expected alert:

```text
WARNING - Accurate 5 tidak berjalan pada Laptop Finance 2
Target     : Laptop Finance 2
Detected by: Windows Agent
Evidence   : process accurate.exe tidak ditemukan, user=DESKTOP-XYZ\Finance
Impact     : User tidak sedang menjalankan Accurate atau aplikasi tertutup/crash
Action     : Hubungi user atau lakukan Remote Desktop bila diperlukan
```

Catatan:

```text
Severity tidak boleh otomatis CRITICAL hanya karena accurate.exe tidak berjalan.
Jika di luar jam kerja, kondisi ini dapat dianggap status biasa dan tidak perlu alert.
```

---

## 17. Demo 9 — Accurate Audit Trail

Buka halaman Accurate Audit.

Tunjukkan data:

```text
Waktu Aktivitas
Accurate Username
Full Name
Source / Module
Transaction Type
Description
Reference / Invoice No
App Version
Status
```

Narasi:

> Accurate Audit Trail tidak dikirim melalui RSyslog. Data ini dibaca langsung dari database Firebird secara read-only menggunakan tabel AUDIT dan USERS. Username internal Accurate diperoleh dari relasi AUDIT.USERID ke USERS.USERID. Tabel LOGIN tidak digunakan sebagai sumber utama karena pada POC tabel tersebut tidak berisi data yang dapat diandalkan.

Tunjukkan filter:

```text
Tanggal
Username Accurate
Module / Source
Transaction Type
Keyword
```

Expected result:

```text
Aktivitas perubahan data Accurate tampil dalam tabel audit.
Admin dapat memfilter berdasarkan user atau jenis aktivitas.
```

Contoh narasi novelty:

> Dengan fitur ini, sistem tidak hanya memantau kondisi teknis device dan koneksi, tetapi juga menyediakan visibilitas terhadap aktivitas perubahan data pada aplikasi bisnis Accurate.

---

## 18. Demo 10 — Contextual Alerts

Buka halaman Alerts.

Tunjukkan bahwa setiap alert memiliki:

```text
Title
Target
Target Type
Severity
Detected By
Evidence
Impact
Recommended Action
Status
Notification Status
```

Contoh alert yang baik:

```text
WARNING - CPU tinggi pada Laptop Finance 2
Target     : Laptop Finance 2
Detected by: Windows Agent
Evidence   : CPU 87% selama 5 menit, RAM 82%
Impact     : Device berpotensi lambat saat menjalankan Accurate
Action     : Remote Desktop untuk investigasi atau restart manual bila diperlukan
```

Narasi:

> Alert pada sistem ini tidak dibuat sebagai teks generik. Setiap alert harus memiliki target dan bukti. Dengan begitu admin dapat memahami perangkat mana yang bermasalah, penyebab terdeteksi dari mana, dan tindakan apa yang disarankan.

Poin penting:

```text
- Tidak boleh ada alert ngambang seperti Firebird unreachable tanpa target.
- Tidak boleh ada alert audit spike tanpa perhitungan yang jelas.
- Alert harus punya evidence dan recommended action.
```

---

## 19. Demo 11 — Telegram Contextual Alert

Trigger alert yang aman, misalnya:

1. Naikkan CPU dengan tool ringan atau test script terkontrol.
2. Tutup Accurate 5 sementara pada salah satu laptop.
3. Matikan koneksi ZeroTier sementara pada salah satu laptop jika aman.
4. Gunakan test alert dari dashboard jika tersedia.

Format Telegram yang diharapkan:

```text
[WARNING] CPU tinggi pada Laptop Finance 2

Target      : Laptop Finance 2
Hostname    : DESKTOP-XYZ
Detected by : Windows Agent
Evidence    : CPU 87%, RAM 82%, durasi 5 menit
Impact      : Device berpotensi lambat saat menjalankan Accurate
Action      : Remote Desktop / Restart Client jika diperlukan
Time        : 2026-05-28 19:51
```

Narasi:

> Telegram digunakan sebagai contextual proactive alert. Admin tidak perlu membuka dashboard terus-menerus, karena sistem dapat mengirim peringatan yang sudah berisi target, bukti, dampak, dan saran tindakan.

Expected result:

```text
Telegram menerima pesan alert dengan format kontekstual.
Riwayat pengiriman tercatat di alert_notifications.
```

---

## 20. Demo 12 — Incidents

Buka halaman Incidents jika sudah tersedia.

Tunjukkan contoh incident:

```text
WIN-ACC-02 terindikasi lambat
Severity: WARNING
Evidence:
- CPU 87% selama 5 menit
- RAM 82%
- Firebird latency 650 ms
Recommended Action:
- Remote Desktop untuk investigasi
- Restart Client jika user menyetujui atau jika admin perlu tindakan pemulihan
```

Narasi:

> Incident adalah hasil korelasi beberapa event atau alert. Jika alert adalah kondisi spesifik, incident menjelaskan masalah operasional secara lebih utuh, misalnya indikasi device lambat karena CPU tinggi dan koneksi Firebird melambat.

Catatan:

```text
Incident boleh dibuat jika rule korelasinya jelas.
Jangan membuat incident abstrak tanpa evidence.
```

---

## 21. Demo 13 — Remote Desktop

Buka Device Detail.

Klik tombol Remote Desktop.

Expected behavior:

1. Sistem membuat RDP launcher atau instruksi RDP ke IP ZeroTier device.
2. Remote action dicatat sebagai `OPEN_RDP`.
3. Admin dapat membuka koneksi RDP jika Windows client mengizinkan.

Contoh tampilan:

```text
Remote Desktop Target
Device       : Laptop Finance 2
Hostname     : DESKTOP-XYZ
IP ZeroTier  : 10.x.x.x
Command      : mstsc /v:10.x.x.x
```

Narasi:

> Remote Desktop adalah fitur investigasi manual. Tombol ini tersedia di Device Detail dan tidak harus menunggu alert. Jika device online dan RDP tersedia, admin dapat melakukan pengecekan langsung ke laptop client.

Catatan penting:

```text
- RDP tidak dikirim melalui RSyslog.
- RDP membutuhkan Windows client mengaktifkan Remote Desktop.
- Jika device offline, tombol dapat disabled atau diberi warning.
```

---

## 22. Demo 14 — Remote Restart Manual

Buka Device Detail.

Klik Restart Client.

Modal konfirmasi wajib muncul.

Isi modal minimal:

```text
Target Device
Hostname
Windows User terakhir
IP ZeroTier
Warning bahwa device akan restart
Input alasan restart
Checkbox konfirmasi
Tombol Cancel
Tombol Confirm Restart
```

Alur:

```text
Admin klik Restart Client
↓
Modal konfirmasi muncul
↓
Admin isi alasan
↓
Sistem membuat remote command berstatus pending
↓
Windows Agent polling command
↓
Agent menjalankan restart Windows
↓
Agent mengirim result
↓
Remote Actions mencatat status
```

Narasi:

> Restart Client adalah tindakan manual terkontrol. Sistem tidak melakukan auto restart. Admin harus melakukan konfirmasi dan mengisi alasan. Seluruh tindakan dicatat agar dapat diaudit.

Expected result:

```text
Remote action tercatat di halaman Remote Actions.
Status berubah dari pending menjadi sent/executed/failed sesuai hasil agent.
```

Catatan saat presentasi:

```text
Jika tidak ingin benar-benar merestart laptop saat sidang, tunjukkan sampai tahap command pending atau gunakan device yang aman untuk direstart.
Jika melakukan restart real, pastikan data pekerjaan user sudah aman.
```

---

## 23. Demo 15 — Remote Actions Log

Buka halaman Remote Actions.

Tunjukkan kolom:

```text
Time
Admin
Device
Action Type
Status
Reason
Command Payload
Result Message
Requested At
Executed At
```

Narasi:

> Setiap tindakan admin, seperti membuka RDP atau melakukan restart, dicatat di Remote Actions. Ini penting agar sistem tidak hanya melakukan monitoring, tetapi juga memiliki jejak audit terhadap tindakan administratif.

Expected result:

```text
Action OPEN_RDP dan RESTART_CLIENT tercatat dengan target dan statusnya.
```

---

## 24. Demo 16 — Advanced Logs

Buka halaman Advanced Logs.

Tunjukkan filter:

```text
Tanggal
Hostname
Agent ID
Event Type
Category
Severity
Keyword
```

Narasi:

> Advanced Logs digunakan untuk investigasi teknis. Raw log tetap disimpan agar admin dapat menelusuri data asli, tetapi tidak dijadikan fokus dashboard utama karena tidak semua log mentah relevan untuk kebutuhan operasional.

Expected result:

```text
Raw/structured log dari Windows Agent dan RSyslog dapat difilter.
```

---

## 25. Demo 17 — Settings

Buka Settings.

Tunjukkan konfigurasi:

```text
General settings
Device heartbeat threshold
CPU/RAM/Disk threshold
Firebird host dan port
Accurate audit sync interval
Telegram settings
Remote action settings
```

Narasi:

> Settings digunakan untuk mengatur threshold dasar, seperti batas CPU warning, batas offline device, latency Firebird, serta konfigurasi Telegram. Dengan ini sistem dapat disesuaikan dengan kebutuhan operasional PT XYZ.

Expected result:

```text
Admin dapat melihat dan mengubah konfigurasi sesuai hak akses.
```

---

## 26. Skenario Gangguan yang Bisa Didemokan

### 26.1 Skenario Device Lambat

Tujuan:

```text
Menunjukkan korelasi performa device dengan indikasi lambat.
```

Langkah:

1. Jalankan beban CPU ringan pada laptop Windows.
2. Tunggu Windows Agent mengirim telemetry.
3. Dashboard menunjukkan CPU tinggi.
4. Alert muncul jika threshold terpenuhi.
5. Telegram menerima alert.

Expected alert:

```text
WARNING - CPU tinggi pada Laptop Finance 2
```

### 26.2 Skenario Koneksi Firebird Bermasalah dari Satu Client

Tujuan:

```text
Menunjukkan bahwa sistem dapat membedakan masalah koneksi satu device.
```

Langkah:

1. Putuskan koneksi ZeroTier pada salah satu laptop atau blok koneksi ke port Firebird secara sementara.
2. Tunggu network check agent.
3. Dashboard menunjukkan Firebird connection error untuk device tersebut.
4. Alert muncul dengan target device tersebut.

Expected alert:

```text
ERROR - WIN-ACC-02 gagal terhubung ke Firebird VPS:3051
```

### 26.3 Skenario Accurate Ditutup

Tujuan:

```text
Menunjukkan monitoring process Accurate.
```

Langkah:

1. Tutup Accurate 5 pada salah satu laptop.
2. Tunggu accurate process check.
3. Dashboard menunjukkan Accurate Not Running pada device tersebut.
4. Alert warning muncul jika dalam jam kerja.

Expected alert:

```text
WARNING - Accurate 5 tidak berjalan pada Laptop Finance 2
```

### 26.4 Skenario Firebird Service Down

Tujuan:

```text
Menunjukkan deteksi masalah database server.
```

Langkah:

1. Stop service Firebird di VPS hanya jika aman.
2. Server Health Checker mendeteksi service inactive dan port 3051 closed.
3. Client network check juga mulai gagal.
4. Sistem membuat alert critical target VPS-FIREBIRD.
5. Telegram menerima alert critical.

Expected alert:

```text
CRITICAL - Service Firebird pada VPS tidak aktif
```

Catatan:

```text
Lakukan hanya jika tidak mengganggu database aktif.
Untuk sidang, boleh jelaskan berdasarkan hasil test yang sudah direkam/screenshot jika tidak ingin mengambil risiko.
```

### 26.5 Skenario Accurate Audit Trail

Tujuan:

```text
Menunjukkan audit aktivitas Accurate.
```

Langkah:

1. Login Accurate dari laptop Windows.
2. Lakukan perubahan data yang aman pada database uji, misalnya update data dummy.
3. Accurate Audit Reader melakukan sync.
4. Halaman Accurate Audit menampilkan aktivitas baru.

Expected result:

```text
Audit event baru muncul dengan username Accurate dan detail aktivitas.
```

---

## 27. Data yang Harus Disiapkan Sebelum Presentasi

Agar presentasi lancar, siapkan data berikut:

```text
[ ] Minimal 2 device terdaftar.
[ ] Minimal 1 device menunjukkan Accurate Running.
[ ] Minimal 1 network check Firebird Connected.
[ ] Minimal 1 telemetry CPU/RAM/Disk untuk tiap device.
[ ] Minimal 5 audit event Accurate.
[ ] Minimal 1 alert warning.
[ ] Minimal 1 alert critical, boleh dari test sebelumnya.
[ ] Minimal 1 Telegram notification history.
[ ] Minimal 1 Remote Action OPEN_RDP.
[ ] Minimal 1 Remote Action RESTART_CLIENT, boleh status pending/executed/test.
```

Catatan:

```text
Data ini bukan data simulasi random.
Data boleh berasal dari real test sebelumnya yang sudah tersimpan di database monitoring.
```

---

## 28. Troubleshooting Saat Demonstrasi

### 28.1 Device Tidak Muncul di Dashboard

Cek:

```text
- Windows Agent berjalan?
- Agent config benar?
- ZeroTier connected?
- RSyslog VPS menerima log?
- Parser Laravel berjalan?
- Database devices terisi?
```

Command VPS:

```bash
sudo tail -f /var/log/remote/all.log
```

### 28.2 Log Masuk ke RSyslog tapi Dashboard Tidak Update

Cek:

```text
- Parser command/scheduler berjalan?
- Format key=value valid?
- parser_offsets tidak stuck?
- Database insert error?
```

Command:

```bash
php artisan rsyslog:parse
php artisan schedule:run
```

### 28.3 Accurate Audit Tidak Muncul

Cek:

```text
- Firebird service aktif?
- Credential read-only valid?
- Path database Accurate benar?
- Query AUDIT + USERS berhasil?
- Sync state terakhir tidak melompat?
- Tabel AUDIT memang memiliki data baru?
```

### 28.4 Telegram Tidak Terkirim

Cek:

```text
- TELEGRAM_ALERT_ENABLED=true?
- Bot token valid?
- Chat ID valid?
- VPS bisa akses internet?
- Cooldown/anti-duplicate tidak memblokir?
- alert_notifications status failed/pending?
```

### 28.5 Remote Desktop Gagal

Cek:

```text
- RDP enabled di Windows?
- Firewall Windows mengizinkan RDP?
- IP ZeroTier benar?
- User credential RDP valid?
- Device online?
```

### 28.6 Remote Restart Tidak Jalan

Cek:

```text
- Remote action dibuat?
- Windows Agent polling API?
- Agent punya izin menjalankan shutdown?
- Device online?
- Command status berubah dari pending?
- Result message tercatat?
```

---

## 29. Fallback Plan

Karena sistem memakai real-device, beberapa komponen dapat tergantung jaringan atau perangkat. Siapkan fallback berikut:

| Risiko | Fallback |
|---|---|
| Internet/ZeroTier bermasalah | Gunakan screenshot/video pendek dari test sebelumnya, lalu tunjukkan data tersimpan di database. |
| Telegram gagal | Tunjukkan alert di dashboard dan riwayat failed/sent di alert_notifications. |
| RDP gagal | Tunjukkan RDP launcher dan jelaskan syarat Windows RDP/firewall. |
| Restart tidak ingin dilakukan saat sidang | Tunjukkan command pending dan audit trail remote action. |
| Accurate audit live gagal | Tunjukkan data audit hasil sync sebelumnya dan jelaskan query read-only. |
| Firebird service stop terlalu berisiko | Gunakan alert critical yang sudah direkam dari pengujian sebelumnya. |

Poin penting:

```text
Fallback tidak boleh mengubah klaim sistem.
Jika fitur live gagal, jelaskan kondisi teknis dengan jujur dan tunjukkan bukti pengujian sebelumnya.
```

---

## 30. Narasi Penutup

Contoh narasi:

> Berdasarkan demonstrasi, sistem Centralized Log Monitoring Dashboard mampu memantau perangkat Windows pengguna Accurate 5 secara terpusat melalui VPS Linux. Sistem dapat menampilkan status device, user Windows, koneksi Firebird, performa device, status Accurate, dan audit trail Accurate secara lebih terstruktur. Selain itu, sistem menyediakan contextual alert melalui Telegram serta fitur tindakan manual terkontrol berupa Remote Desktop dan Restart Client. Dengan demikian, administrator IT dapat melakukan monitoring dan investigasi lebih cepat dibandingkan pengecekan manual.

Tambahkan batasan:

> Sistem ini tetap dibatasi sebagai monitoring dan alerting system. Sistem tidak melakukan auto blocking dan tidak melakukan auto restart. Tindakan restart hanya dilakukan secara manual oleh admin dan dicatat sebagai remote action.

---

## 31. Mapping Demo ke Masalah Skripsi

| Masalah di PT XYZ | Bagian Demo yang Menjawab |
|---|---|
| Monitoring manual dan insidental | Dashboard, Devices, Alerts, Telegram. |
| Perlambatan akses saat jam sibuk | Performance monitoring, Firebird latency, Incident device lambat. |
| Device/server tidak responsif | Heartbeat, CPU/RAM/Disk, hang indicator, remote restart manual. |
| Koneksi database Accurate terputus | Firebird connectivity check, Firebird service health, contextual alert. |
| Sulit melacak aktivitas Accurate | Accurate Audit Trail dari Firebird AUDIT + USERS. |
| Admin butuh respons cepat | Telegram alert, Remote Desktop, Remote Restart manual. |
| Log mentah terlalu banyak dan tidak fokus | Dashboard status-oriented dan Advanced Logs untuk investigasi. |

---

## 32. Acceptance Criteria Demonstrasi

Demonstrasi dinyatakan berhasil jika memenuhi kriteria berikut:

```text
[ ] Admin dapat login ke dashboard.
[ ] Minimal dua device Windows real muncul di halaman Devices.
[ ] Device memiliki agent_id, hostname, Windows user, IP ZeroTier, dan last seen.
[ ] Telemetry CPU/RAM/Disk tampil untuk device.
[ ] Status koneksi Firebird tampil untuk device.
[ ] Status accurate.exe tampil untuk device.
[ ] Accurate Audit menampilkan data dari Firebird AUDIT + USERS.
[ ] Alert tampil dengan target, evidence, impact, dan recommended action.
[ ] Telegram menerima minimal satu contextual alert.
[ ] Remote Desktop launcher dapat digunakan atau ditunjukkan.
[ ] Remote Restart manual memiliki confirmation modal dan reason.
[ ] Remote Actions mencatat tindakan admin.
[ ] Advanced Logs tersedia untuk investigasi teknis.
[ ] Tidak ada dashboard utama yang didominasi raw log random.
```

---

## 33. Instruksi Khusus untuk AI Agent / Codex

Saat mengimplementasikan fitur yang mendukung demo ini, AI Agent / Codex wajib mengikuti aturan berikut:

1. Jangan membuat demo berbasis random log sebagai fokus utama.
2. Jangan hardcode `WIN-ACC-01` atau `WIN-ACC-02` di logic aplikasi.
3. Gunakan `agent_id` sebagai identitas utama device.
4. Jadikan `device_label` sebagai nama tampilan yang dapat diubah admin.
5. Jangan menampilkan raw log sebagai dashboard utama.
6. Jangan membuat alert tanpa target dan evidence.
7. Jangan membuat audit spike detection tanpa rule eksplisit.
8. Jangan menggunakan tabel `LOGIN` Accurate sebagai sumber utama.
9. Jangan mengirim Accurate Audit Trail lewat RSyslog.
10. Gunakan RSyslog hanya untuk telemetry/status/log terstruktur.
11. Gunakan direct read-only Firebird query untuk Accurate Audit Reader.
12. Jangan membuat auto restart.
13. Restart client harus manual, konfirmasi, alasan wajib, dan tercatat.
14. Remote action tidak boleh lewat RSyslog.
15. Telegram wajib mendukung contextual alert.
16. UI harus mengikuti IT operations cockpit, bukan SaaS marketing dashboard.

---

## 34. Dokumen Terkait

Dokumen ini harus dibaca bersama:

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
```

---

## 35. Ringkasan Akhir

Demonstrasi v2 harus memperlihatkan sistem yang benar-benar relevan untuk IT admin PT XYZ skala kecil:

```text
2 laptop Windows pengguna Accurate
↓
Windows Agent mengirim telemetry real
↓
RSyslog Server di VPS menerima log/status
↓
Laravel memproses data ke MySQL
↓
Dashboard menampilkan device, koneksi, performa, Accurate, audit, alert
↓
Telegram mengirim contextual alert
↓
Admin dapat melakukan Remote Desktop atau Restart Client secara manual terkontrol
```

Fokus utama bukan jumlah log, tetapi **kemampuan sistem membantu admin memahami kondisi device, koneksi database Accurate, performa, audit perubahan data, dan respons tindakan secara cepat serta terstruktur**.
