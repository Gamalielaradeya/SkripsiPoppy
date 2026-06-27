# 02 — Accurate Firebird POC Findings v2
# Centralized Log Monitoring Dashboard

**Versi:** 2.0  
**Status:** Draft Final untuk Review Pengguna / AI Agent  
**Tanggal:** 2026-05-28  
**Jenis Dokumen:** Technical POC Findings & Implementation Grounding  
**Nama Produk:** Centralized Log Monitoring Dashboard  
**Konteks:** Monitoring real-device untuk client Windows pengguna Accurate 5 dan server Firebird pada VPS Linux  
**Stack Terkait:** Accurate 5 Enterprise, Firebird 2.5, Laravel, MySQL, Windows Agent, RSyslog, ZeroTier, Telegram Bot API  
**Target Pembaca:** Pengguna, AI Agent/Codex, developer, dan pembimbing teknis  
**Sumber Utama:** Temuan POC pengguna terhadap Accurate 5 Enterprise berbasis Firebird 2.5  

---

## 1. Tujuan Dokumen

Dokumen ini menjelaskan hasil **Proof of Concept (POC)** terkait cara monitoring akses dan aktivitas Accurate 5 Enterprise yang menggunakan database **Firebird 2.5**.

Dokumen ini dibuat agar AI Agent/Codex tidak membuat asumsi sendiri terkait cara membaca data Accurate. Temuan POC pada dokumen ini menjadi **source of truth** untuk seluruh modul yang berkaitan dengan:

1. Monitoring koneksi Accurate 5 ke database Firebird.
2. Identifikasi proses `accurate.exe` pada client Windows.
3. Identifikasi Windows user yang menjalankan Accurate 5.
4. Pembacaan audit trail internal Accurate dari database Firebird.
5. Pembacaan username internal Accurate dari tabel aplikasi.
6. Pemisahan sumber data antara monitoring koneksi aktif dan audit aktivitas internal.
7. Batasan implementasi agar sistem tidak mengganggu database Accurate.

Dokumen ini **bukan** PRD umum dan **bukan** SRS penuh. Dokumen ini adalah dokumen teknis POC yang harus dibaca sebelum membuat:

```text
03_SRS_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md
04_Database_Design_v2.md
05_System_Architecture_v2.md
06_Windows_Agent_and_RSyslog_Guide_v2.md
07_Detection_Rules_v2.md
```

---

## 2. Posisi Dokumen dalam Rangkaian v2

Rangkaian dokumentasi v2 yang disarankan:

```text
01_PRD_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md
02_Accurate_Firebird_POC_Findings_v2.md
03_SRS_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md
04_Database_Design_v2.md
05_System_Architecture_v2.md
06_Windows_Agent_and_RSyslog_Guide_v2.md
07_Detection_Rules_v2.md
08_UI_Wireframe_v2.md
09_Test_Plan_v2.md
10_Deployment_Guide_VPS_ZeroTier_v2.md
11_Demo_Script_v2.md
12_Codex_Implementation_Brief.md
```

Dokumen ini sengaja ditempatkan setelah PRD karena modul Accurate/Firebird adalah bagian penting dari novelty. Sebelum SRS dan Database Design dibuat, cara akses Firebird harus dikunci terlebih dahulu agar tidak terjadi halusinasi implementasi.

---

## 3. Ringkasan POC

POC dilakukan pada **Accurate 5 Enterprise** yang menggunakan **Firebird 2.5** sebagai database.

Tujuan POC:

1. Membuktikan apakah akses Accurate dapat dimonitor secara custom.
2. Membuktikan apakah koneksi Accurate dapat diamati dari sisi sistem operasi/jaringan.
3. Membuktikan apakah aktivitas user internal Accurate dapat dibaca dari database.
4. Menentukan sumber data mana yang valid untuk dashboard monitoring.
5. Menentukan sumber data mana yang tidak boleh dijadikan dasar utama.

Hasil ringkas POC:

| Area | Hasil POC | Implikasi ke Sistem |
|---|---|---|
| Koneksi Accurate | `accurate.exe` terhubung ke `fbserver.exe` melalui port Firebird `3051` | Koneksi Accurate dapat dimonitor dari sisi TCP/Windows process |
| Windows User | Windows user dapat dibaca dari proses yang menjalankan Accurate | Windows Agent dapat mengirim user aktif dan owner proses Accurate |
| Username Accurate | Username internal Accurate dibaca dari tabel `AUDIT + USERS` | Accurate Audit Reader harus query Firebird secara read-only |
| Tabel LOGIN | Ada secara struktur tetapi kosong pada database uji | Jangan jadikan `LOGIN` sumber utama |
| Field COMP_NAME/IPADDRESS | Ada di `AUDIT`, tetapi kosong pada data uji | Jangan wajibkan field ini untuk mapping komputer/IP |
| Pendekatan terbaik | Gabungan TCP/Windows monitoring + Firebird audit reader | Sistem harus memisahkan data koneksi dan data aktivitas internal |

---

## 4. Keputusan Desain Berdasarkan POC

Berdasarkan POC, modul Accurate pada sistem v2 harus dibagi menjadi dua jalur data yang berbeda.

### 4.1 Jalur 1 — Accurate Active Session / Connection Monitor

Jalur ini digunakan untuk menjawab pertanyaan:

```text
Laptop mana yang sedang menjalankan Accurate?
Windows user siapa yang menjalankan Accurate?
Apakah Accurate client sedang terhubung ke Firebird?
Apakah koneksi ke port Firebird berhasil?
```

Sumber data:

```text
Windows Agent pada laptop client
```

Data yang dikirim oleh Windows Agent:

```text
hostname
agent_id
ip_zerotier
windows_user
accurate_process_status
accurate_process_name
accurate_process_pid
accurate_process_owner
accurate_process_path
firebird_target_host
firebird_target_port
firebird_connection_status
firebird_latency_ms
last_checked_at
```

Jalur pengiriman:

```text
Windows Agent
    ↓ kirim log/status terstruktur
RSyslog Server pada VPS
    ↓ simpan raw log
Laravel Parser
    ↓ parsing ke MySQL
Dashboard Devices / Device Detail
```

### 4.2 Jalur 2 — Accurate Audit Trail Reader

Jalur ini digunakan untuk menjawab pertanyaan:

```text
User Accurate siapa yang melakukan aktivitas?
Aktivitas/perubahan data apa yang terjadi?
Kapan aktivitas dilakukan?
Modul/source transaksi apa yang terkait?
Nomor invoice/referensi apa yang berubah?
```

Sumber data:

```text
Database Firebird Accurate
Tabel utama: AUDIT + USERS
```

Jalur pembacaan:

```text
Laravel Accurate Audit Reader
    ↓ koneksi read-only ke Firebird
Query AUDIT + USERS
    ↓ normalisasi data
MySQL monitoring database
    ↓ tampilkan
Dashboard Accurate Audit
```

Catatan penting:

```text
Accurate Audit Trail TIDAK dikirim melalui RSyslog.
Accurate Audit Trail dibaca langsung dari Firebird secara read-only.
```

Alasan:

1. Sumber data audit adalah tabel database Accurate, bukan pesan syslog.
2. Username internal Accurate tidak ditemukan dari koneksi TCP.
3. Pembacaan langsung dari Firebird lebih akurat untuk aktivitas internal Accurate.
4. RSyslog tetap digunakan untuk monitoring device, koneksi, performa, dan proses.

---

## 5. Topologi Target Accurate/Firebird v2

Topologi target implementasi:

```text
[Windows Laptop 1]
- Accurate 5 Client
- Windows Agent
- ZeroTier

[Windows Laptop 2]
- Accurate 5 Client
- Windows Agent
- ZeroTier

        ↓ koneksi Accurate 5 via ZeroTier

[VPS Linux]
- Firebird Server 2.5
- Database Accurate (.FDB / .GDB)
- Laravel Dashboard
- MySQL monitoring database
- RSyslog Server
- Accurate Audit Reader
- Telegram Alert Service
- ZeroTier
```

Koneksi utama:

```text
Accurate 5 Client Windows → ZeroTier IP VPS:3051 → Firebird Database
Windows Agent             → RSyslog VPS:514/5514 → Raw log monitoring
Laravel Audit Reader      → Firebird VPS:3051     → AUDIT + USERS
Dashboard Admin           → Laravel Web App       → MySQL monitoring
```

---

## 6. Temuan POC Detail

## 6.1 Accurate 5 Menggunakan Koneksi Firebird

Pada saat database Accurate dibuka, proses:

```text
accurate.exe
```

terlihat melakukan koneksi aktif ke proses server database:

```text
fbserver.exe
```

melalui port Firebird:

```text
3051
```

Temuan ini membuktikan bahwa akses Accurate dapat diamati dari sisi koneksi jaringan atau sistem operasi.

Data yang dapat dimonitor dari sisi koneksi:

| Data | Sumber | Digunakan di Dashboard? |
|---|---|---|
| Process aplikasi client `accurate.exe` | Windows process list | Ya |
| Process server database `fbserver.exe` | Server process list / service check | Ya |
| Port Firebird `3051` | TCP connection test | Ya |
| Status koneksi `Established` / timeout / refused | TCP connection table / port check | Ya |
| Windows user yang menjalankan Accurate | Process owner | Ya |
| Path executable Accurate | Process path | Ya |
| IP dan port koneksi | TCP connection table | Ya |

### Implikasi ke Windows Agent

Windows Agent harus mampu mengecek:

```text
1. Apakah accurate.exe berjalan.
2. Siapa Windows user pemilik proses accurate.exe.
3. Di path mana accurate.exe berjalan.
4. Apakah laptop dapat mengakses host Firebird pada port 3051.
5. Berapa latency koneksi ke Firebird jika berhasil.
```

Contoh event/log terstruktur yang boleh dikirim ke RSyslog:

```text
accurate-process-monitor: agent_id=... hostname=DESKTOP-ABC windows_user=DESKTOP-ABC\FINANCE01 process=accurate.exe status=running pid=1234 path="C:\Program Files (x86)\CPSSoft\ACCURATE5 Enterprise\accurate.exe"
```

```text
firebird-connection-monitor: agent_id=... hostname=DESKTOP-ABC target_host=10.10.10.5 target_port=3051 status=connected latency_ms=18
```

---

## 6.2 Windows User Dapat Dibaca dari Proses Accurate

POC menemukan bahwa Windows user yang menjalankan Accurate dapat diketahui dengan memetakan:

```text
TCP connection → PID → process → process owner
```

Contoh hasil POC:

```text
ProcessName : accurate
WindowsUser : DESKTOP-GAZAPZ\SOLIT
Path        : C:\Program Files (x86)\CPSSoft\ACCURATE5 Enterprise\accurate.exe
```

### Keputusan Implementasi

Windows user **bukan** dibaca dari Firebird audit table.

Windows user harus dibaca dari client Windows melalui Windows Agent.

Field yang disarankan:

```text
windows_user
accurate_process_owner
accurate_process_pid
accurate_process_path
accurate_process_status
```

### Batasan

Windows user hanya menunjukkan user sistem operasi yang menjalankan Accurate. Windows user tidak selalu sama dengan username internal Accurate.

Contoh:

```text
Windows user         : DESKTOP-GAZAPZ\SOLIT
Username Accurate    : FINANCE01
```

Karena itu, dashboard harus menampilkan keduanya secara terpisah jika keduanya tersedia.

---

## 6.3 Username Internal Accurate Dibaca dari Database

POC menemukan bahwa username internal Accurate tidak didapat dari koneksi TCP. Username internal Accurate dapat dibaca dari tabel audit aplikasi.

Tabel relevan:

| Tabel | Fungsi | Status POC |
|---|---|---|
| `USERS` | Master user internal Accurate | Dipakai |
| `AUDIT` | Aktivitas/perubahan data user | Dipakai |
| `AUDITDET` | Detail tambahan audit | Opsional / pengembangan |
| `LOGIN` | Struktur login tersedia | Tidak dipakai sebagai sumber utama karena kosong |

Relasi penting:

```text
AUDIT.USERID → USERS.USERID
LOGIN.USER_ID → USERS.USERID
```

Namun pada implementasi v2, relasi utama yang digunakan adalah:

```text
AUDIT.USERID → USERS.USERID
```

---

## 7. Query Utama Accurate Audit Reader

Query dasar dari POC untuk membaca aktivitas user internal Accurate:

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

Query ini adalah query dasar untuk:

1. Menampilkan audit trail terbaru di dashboard.
2. Mengambil username internal Accurate.
3. Mengambil deskripsi aktivitas atau perubahan data.
4. Mengambil nomor referensi/invoice jika tersedia.
5. Mengambil versi aplikasi dan status audit.

---

## 8. Field Mapping ke MySQL Monitoring

Data dari Firebird tidak ditampilkan langsung tanpa penyimpanan. Sistem harus menyimpan hasil sinkronisasi ke database monitoring MySQL agar:

1. Dashboard lebih cepat.
2. Data dapat difilter tanpa membebani Firebird.
3. Riwayat sync dapat dikontrol.
4. Anti-duplikasi dapat diterapkan.
5. Alert Telegram dapat menggunakan data yang sudah distandarkan.

Mapping awal:

| Firebird Field | Alias Query | MySQL Field Disarankan | Keterangan |
|---|---|---|---|
| `a.AUDITID` | `AUDITID` | `accurate_audit_id` | ID audit dari Accurate |
| `a.MODIDATE` | `ACTIVITY_TIME` | `activity_time` | Waktu aktivitas |
| `u.USERNAME` | `ACCURATE_USERNAME` | `accurate_username` | Username internal Accurate |
| `u.FULLNAME` | `ACCURATE_FULLNAME` | `accurate_fullname` | Nama lengkap user Accurate |
| `a.SOURCE` | `SOURCE` | `source_module` | Modul/source aktivitas |
| `a.TRANSTYPE` | `TRANSTYPE` | `transaction_type` | Jenis transaksi/aktivitas |
| `a.TRANSDESCRIPTION` | `TRANSDESCRIPTION` | `transaction_description` | Deskripsi aktivitas |
| `a.INVOICENO` | `INVOICENO` | `invoice_no` | Nomor invoice/referensi |
| `a.COMP_NAME` | `COMP_NAME` | `audit_comp_name` | Bisa kosong, jangan wajib |
| `a.IPADDRESS` | `IPADDRESS` | `audit_ip_address` | Bisa kosong, jangan wajib |
| `a.APPVERSION` | `APPVERSION` | `app_version` | Versi aplikasi, pada POC terisi |
| `a.STATUS` | `STATUS` | `audit_status` | Status audit |

Field tambahan di MySQL monitoring:

```text
id
accurate_audit_id
activity_time
accurate_username
accurate_fullname
source_module
transaction_type
transaction_description
invoice_no
audit_comp_name
audit_ip_address
app_version
audit_status
raw_payload
hash
synced_at
created_at
updated_at
```

---

## 9. Tabel LOGIN Tidak Boleh Menjadi Sumber Utama

Walaupun tabel `LOGIN` memiliki struktur yang tampak relevan, POC menunjukkan bahwa tabel ini kosong pada database uji.

Kolom yang ditemukan pada tabel `LOGIN`:

```text
LOGIN_TIME
USER_ID
COMPUTER_NAME
IPADDRESS
HANDLEDB
```

Namun karena kosong, tabel ini tidak boleh digunakan sebagai sumber utama untuk:

```text
login aktif Accurate
username Accurate aktif
history login Accurate
status user sedang login
```

### Larangan untuk AI Agent

AI Agent/Codex **tidak boleh** membuat fitur yang bergantung pada tabel `LOGIN` sebagai sumber utama.

Contoh implementasi yang tidak boleh:

```php
// SALAH: jangan jadikan LOGIN sebagai sumber utama
SELECT * FROM LOGIN ORDER BY LOGIN_TIME DESC;
```

Jika tabel `LOGIN` ingin ditampilkan, posisinya hanya sebagai:

```text
optional diagnostic table
experimental source
future enhancement
```

Bukan MVP.

---

## 10. Field COMP_NAME dan IPADDRESS di AUDIT Tidak Boleh Diwajibkan

POC menemukan bahwa tabel `AUDIT` memiliki field:

```text
COMP_NAME
IPADDRESS
APPVERSION
```

Namun pada data uji:

```text
COMP_NAME  : kosong
IPADDRESS  : kosong
APPVERSION : terisi
```

Karena itu:

1. `COMP_NAME` boleh disimpan jika tersedia.
2. `IPADDRESS` boleh disimpan jika tersedia.
3. Dashboard tidak boleh bergantung pada `COMP_NAME` dan `IPADDRESS` dari tabel `AUDIT`.
4. Mapping IP/komputer aktif harus berasal dari Windows Agent dan koneksi TCP/Windows process.

### Keputusan Dashboard

Untuk Accurate Audit table:

```text
COMP_NAME dan IPADDRESS boleh tampil sebagai kolom opsional.
Jika kosong, tampilkan tanda "-" atau "Tidak tersedia".
```

Untuk Device table:

```text
IP aktif, hostname, Windows user, dan status koneksi harus berasal dari Windows Agent.
```

---

## 11. Pemisahan Data Windows User dan Accurate User

Sistem harus memisahkan dua jenis user:

| Jenis User | Sumber | Makna |
|---|---|---|
| Windows User | Windows Agent / process owner | User OS yang menjalankan laptop/aplikasi |
| Accurate User | Firebird `AUDIT + USERS` | User internal aplikasi Accurate yang melakukan aktivitas |

Contoh:

```text
Windows User  : DESKTOP-GAZAPZ\SOLIT
Accurate User : FINANCE01
```

Keduanya tidak boleh dianggap selalu sama.

Dashboard boleh menampilkan keduanya pada konteks yang berbeda:

```text
Device Detail:
- Windows User aktif
- Accurate process owner
- Accurate process status

Accurate Audit:
- Accurate username
- Accurate fullname
- Aktivitas perubahan data
```

Jika suatu saat ingin mengorelasikan keduanya, harus menggunakan pendekatan korelasi berbasis waktu dan device, bukan asumsi langsung.

---

## 12. Desain Modul Accurate Audit Reader

Modul yang disarankan:

```text
AccurateAuditReaderService
AccurateAuditSyncCommand
AccurateAuditController
AccurateAuditEvent model
AccurateAuditSource model / settings
```

Nama command Laravel yang disarankan:

```bash
php artisan accurate:audit-sync
```

Fungsi utama command:

1. Membaca konfigurasi koneksi Firebird.
2. Membuka koneksi read-only ke database Accurate.
3. Menjalankan query `AUDIT + USERS`.
4. Mengambil data baru berdasarkan posisi sinkronisasi terakhir.
5. Menyimpan data ke MySQL monitoring.
6. Mencegah duplikasi berdasarkan `AUDITID` atau hash.
7. Mencatat riwayat sync.
8. Tidak mengubah data Firebird.

---

## 13. Strategi Koneksi Firebird

Karena aplikasi utama menggunakan Laravel, ada beberapa opsi teknis untuk mengakses Firebird.

### 13.1 Opsi A — PHP Firebird Extension / PDO Firebird

Aplikasi Laravel dapat membaca Firebird langsung jika environment PHP memiliki extension Firebird yang sesuai.

Konsep:

```text
Laravel/PHP
    ↓
Firebird PHP driver / PDO Firebird
    ↓
Firebird Server 2.5
```

Kelebihan:

1. Integrasi langsung ke Laravel.
2. Query dan sync bisa dibuat sebagai Artisan Command.
3. Lebih mudah disatukan dengan scheduler Laravel.

Kekurangan:

1. Setup extension Firebird di server bisa lebih rumit.
2. Perlu memastikan compatibility dengan Firebird 2.5.
3. Dockerfile / VPS environment harus disiapkan dengan benar.

### 13.2 Opsi B — External Firebird Sync Script

Laravel menjalankan atau menerima hasil dari script terpisah, misalnya Python atau CLI helper, untuk membaca Firebird.

Konsep:

```text
External Sync Script
    ↓ query Firebird
Output JSON/CSV/HTTP callback
    ↓
Laravel import ke MySQL
```

Kelebihan:

1. Bisa lebih fleksibel jika PHP Firebird sulit dikonfigurasi.
2. Driver Firebird dapat dipilih sesuai bahasa yang lebih mudah dipasang.
3. Laravel tetap fokus pada dashboard dan database monitoring.

Kekurangan:

1. Menambah satu komponen tambahan.
2. Error handling perlu dirancang rapi.
3. Deployment lebih banyak langkah.

### 13.3 Rekomendasi v2

Untuk desain sistem v2, dokumentasi ini mengizinkan dua pendekatan:

```text
Preferred     : Laravel Artisan Command dengan koneksi Firebird langsung.
Fallback      : External sync script yang output-nya diimport ke Laravel/MySQL.
```

Namun untuk Codex, implementasi awal sebaiknya dibuat dengan interface service yang tidak mengunci driver terlalu cepat:

```php
interface AccurateAuditReaderInterface
{
    public function fetchLatest(array $options = []): array;
}
```

Dengan cara ini, implementasi bisa diganti tanpa merombak controller/dashboard.

---

## 14. Konfigurasi Koneksi Firebird

Konfigurasi koneksi tidak boleh hardcode.

Contoh environment variable yang disarankan:

```env
ACCURATE_AUDIT_ENABLED=true
ACCURATE_FIREBIRD_HOST=10.10.10.5
ACCURATE_FIREBIRD_PORT=3051
ACCURATE_FIREBIRD_DATABASE=/path/to/accurate/database.fdb
ACCURATE_FIREBIRD_USERNAME=GUEST
ACCURATE_FIREBIRD_PASSWORD=
ACCURATE_FIREBIRD_CHARSET=NONE
ACCURATE_AUDIT_SYNC_LIMIT=100
ACCURATE_AUDIT_SYNC_INTERVAL_SECONDS=60
```

Catatan:

1. `GUEST` adalah contoh user read-only berdasarkan rumusan POC, bukan hardcode wajib.
2. Password tidak boleh ditulis di kode.
3. Path database harus dikonfigurasi sesuai environment VPS.
4. Jika database menggunakan path Windows atau path Linux berbeda, konfigurasi harus disesuaikan.
5. Credential harus dikelola melalui `.env`, bukan commit GitHub.

---

## 15. Prinsip Read-Only dan Non-Intrusif

Modul Accurate Audit Reader harus bersifat:

```text
read-only
non-intrusive
tidak mengubah struktur database Accurate
tidak membuat trigger baru di database Accurate
tidak menulis tabel baru ke database Accurate
tidak menghapus data Accurate
tidak mengubah user Accurate
tidak melakukan locking berlebihan
```

Query yang diizinkan:

```text
SELECT
```

Query yang dilarang untuk MVP:

```text
INSERT
UPDATE
DELETE
ALTER
DROP
CREATE TRIGGER
CREATE TABLE
```

Alasan:

1. Database Accurate adalah aset operasional.
2. Penelitian fokus pada monitoring, bukan modifikasi sistem Accurate.
3. Pendekatan non-intrusif lebih aman untuk skripsi dan implementasi real-device.
4. Perubahan struktur database Accurate dapat berisiko mengganggu aplikasi Accurate.

---

## 16. Strategi Incremental Sync

Agar sync tidak membaca seluruh tabel audit terus-menerus, sistem harus memiliki mekanisme incremental sync.

### 16.1 Metode Utama — Berdasarkan AUDITID

Jika `AUDITID` bersifat meningkat, gunakan:

```text
last_audit_id
```

Contoh query konseptual:

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
WHERE a.AUDITID > :last_audit_id
ORDER BY a.AUDITID ASC;
```

### 16.2 Metode Alternatif — Berdasarkan MODIDATE

Jika `AUDITID` tidak dapat dipastikan meningkat, gunakan:

```text
last_activity_time
```

Contoh query konseptual:

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
WHERE a.MODIDATE > :last_activity_time
ORDER BY a.MODIDATE ASC;
```

### 16.3 Anti-Duplikasi

Walaupun sudah incremental, sistem tetap harus punya anti-duplikasi.

Unique key yang disarankan:

```text
accurate_audit_id
```

Jika `AUDITID` tidak cukup aman, gunakan hash:

```text
hash = sha256(AUDITID + ACTIVITY_TIME + ACCURATE_USERNAME + SOURCE + TRANSTYPE + TRANSDESCRIPTION + INVOICENO)
```

### 16.4 Tabel Sync State

Sistem perlu menyimpan posisi sync terakhir.

Tabel disarankan:

```text
accurate_audit_sync_states
```

Field:

```text
id
source_name
firebird_host
firebird_database
last_audit_id
last_activity_time
last_synced_at
last_status
last_error_message
created_at
updated_at
```

---

## 17. Data yang Ditampilkan di Dashboard Accurate Audit

Halaman **Accurate Audit** harus menampilkan data yang relevan untuk admin, bukan raw database dump.

Kolom utama:

```text
Waktu Aktivitas
Username Accurate
Full Name
Source / Modul
Transaction Type
Transaction Description
Invoice No / Reference
App Version
Status
```

Kolom opsional:

```text
COMP_NAME
IPADDRESS
AUDITID
```

Contoh tampilan:

```text
2026-05-28 14:31:22 | FINANCE01 | Finance User | Sales Invoice | UPDATE | Perubahan transaksi penjualan | SI-000123 | 5.x.x | Success
```

Filter yang wajib:

```text
Tanggal mulai
Tanggal akhir
Username Accurate
Source / Modul
Transaction Type
Keyword
```

Filter opsional:

```text
Invoice No
Status
App Version
```

---

## 18. Accurate Audit Detail Page

Jika admin membuka detail satu audit event, tampilkan:

```text
Audit ID
Activity Time
Accurate Username
Accurate Fullname
Source / Module
Transaction Type
Transaction Description
Invoice No
COMP_NAME jika tersedia
IPADDRESS jika tersedia
App Version
Status
Raw Payload
Synced At
```

Halaman detail harus menjelaskan jika data tertentu kosong:

```text
COMP_NAME tidak tersedia pada data audit.
IPADDRESS tidak tersedia pada data audit.
Untuk informasi device/IP aktif, lihat halaman Devices yang bersumber dari Windows Agent.
```

---

## 19. Alert dari Accurate Audit

Untuk MVP, sistem tidak boleh membuat alert audit yang terlalu abstrak tanpa rule jelas.

### 19.1 Alert yang Tidak Boleh Dibuat pada MVP

Jangan membuat alert seperti:

```text
Audit activity spike detected
Suspicious audit activity
User anomaly detected
```

kecuali ada dokumen detection rule yang jelas, termasuk:

```text
window waktu
baseline
threshold
field yang dihitung
contoh data uji
acceptance criteria
```

### 19.2 Alert yang Boleh Dibuat Jika Field Mendukung

Alert boleh dibuat jika field audit jelas dan dapat dibuktikan.

Contoh rule bersyarat:

| Alert Code | Kondisi | Severity | Catatan |
|---|---|---|---|
| `ACCURATE_AUDIT_DELETE` | `TRANSTYPE` menunjukkan DELETE/hapus | CRITICAL | Hanya jika nilai field benar-benar tersedia |
| `ACCURATE_AUDIT_UPDATE` | Ada perubahan transaksi penting | INFO/WARNING | Default INFO kecuali rule menentukan critical |
| `ACCURATE_AUDIT_OUT_OF_HOURS` | Aktivitas terjadi di luar jam kerja | WARNING | Butuh setting jam kerja |

Untuk versi awal, Accurate Audit lebih aman diposisikan sebagai:

```text
monitoring dan audit trail viewer
```

bukan anomaly engine kompleks.

---

## 20. Korelasi dengan Windows Agent

POC menyimpulkan pendekatan terbaik adalah menggabungkan:

```text
TCP/Windows monitoring
+
Firebird AUDIT + USERS
```

Namun korelasi harus dilakukan hati-hati.

### 20.1 Yang Dapat Dikorelasikan

Contoh korelasi yang masuk akal:

```text
Windows Agent menunjukkan WIN-ACC-01 menjalankan accurate.exe.
Accurate Audit menunjukkan user FINANCE01 melakukan update invoice pada waktu berdekatan.
Dashboard dapat menampilkan keduanya dalam konteks waktu yang sama.
```

### 20.2 Yang Tidak Boleh Diasumsikan

Tidak boleh otomatis menyimpulkan:

```text
Windows user DESKTOP\SOLIT pasti sama dengan Accurate user FINANCE01.
```

Kecuali ada mapping manual/admin setting.

### 20.3 Optional Manual Mapping

Sistem boleh menyediakan mapping opsional:

```text
windows_user ↔ accurate_username
```

Tapi mapping ini harus dikelola admin dan tidak wajib untuk MVP.

---

## 21. Database Design Implication

Dokumen Database Design v2 harus menambahkan tabel minimal:

```text
accurate_audit_events
accurate_audit_sync_states
```

Opsional:

```text
accurate_user_mappings
accurate_audit_sources
```

### 21.1 accurate_audit_events

Field minimum:

```text
id
accurate_audit_id
activity_time
accurate_username
accurate_fullname
source_module
transaction_type
transaction_description
invoice_no
audit_comp_name
audit_ip_address
app_version
audit_status
raw_payload
hash
synced_at
created_at
updated_at
```

### 21.2 accurate_audit_sync_states

Field minimum:

```text
id
source_name
firebird_host
firebird_database
last_audit_id
last_activity_time
last_synced_at
last_status
last_error_message
created_at
updated_at
```

### 21.3 accurate_user_mappings Optional

Jika dibutuhkan:

```text
id
windows_user
accurate_username
device_id
notes
created_at
updated_at
```

---

## 22. UI Implication

Dokumen UI Wireframe v2 harus memiliki halaman:

```text
Accurate Audit
Accurate Audit Detail
Settings → Accurate Firebird Connection
Dashboard → Accurate Audit Realtime Panel
```

Dashboard utama hanya menampilkan ringkasan:

```text
Audit events today
Latest Accurate activity
Most recent Accurate username
```

Detail dan filter lengkap berada di menu:

```text
Accurate Audit
```

---

## 23. Settings Implication

Halaman Settings harus menyediakan konfigurasi Accurate Audit:

```text
Accurate audit enabled / disabled
Firebird host
Firebird port
Firebird database path
Read-only username
Password / secret reference
Sync interval
Sync limit
Last sync status
Test connection button
Run manual sync button
```

Namun password tidak boleh ditampilkan kembali dalam bentuk plain text.

---

## 24. Test Plan Implication

Dokumen Test Plan v2 harus menguji:

| ID | Test Case | Expected Result |
|---|---|---|
| TC-AUDIT-001 | Test koneksi Firebird dengan credential valid | Connection success |
| TC-AUDIT-002 | Test koneksi Firebird dengan credential salah | Error tampil jelas, tidak crash |
| TC-AUDIT-003 | Sync audit pertama | Data `AUDIT + USERS` masuk ke MySQL |
| TC-AUDIT-004 | Sync ulang tanpa data baru | Tidak ada duplikasi |
| TC-AUDIT-005 | Tabel `LOGIN` kosong | Sistem tetap berjalan karena tidak bergantung ke LOGIN |
| TC-AUDIT-006 | `COMP_NAME/IPADDRESS` kosong | Dashboard menampilkan `-`, tidak error |
| TC-AUDIT-007 | Filter by username Accurate | Data terfilter benar |
| TC-AUDIT-008 | Filter by date range | Data terfilter benar |
| TC-AUDIT-009 | Firebird tidak reachable | Error sync tercatat dan Telegram opsional dikirim |
| TC-AUDIT-010 | Query read-only | Tidak ada perubahan struktur/data Accurate |

---

## 25. Security and Safety Requirement

1. Gunakan user Firebird read-only jika memungkinkan.
2. Jangan menyimpan credential Firebird di source code.
3. Jangan commit `.env` ke GitHub.
4. Jangan expose Firebird port ke publik jika tidak perlu; gunakan ZeroTier/private network.
5. Batasi query audit dengan `FIRST n` atau pagination.
6. Jangan melakukan query berat secara terus-menerus tanpa interval.
7. Simpan error sync ke tabel monitoring, bukan menampilkan stack trace ke user.
8. Jangan membuat fitur edit/delete database Accurate.
9. Jangan membuat trigger atau stored procedure pada database Accurate.
10. Jangan menjalankan sync dengan user admin database jika user read-only tersedia.

---

## 26. Error Handling

Jika Firebird connection gagal:

```text
- Catat status sync sebagai failed.
- Simpan pesan error ringkas.
- Dashboard menampilkan status "Audit sync failed".
- Jangan crash seluruh aplikasi Laravel.
- Telegram alert boleh dikirim jika fitur enabled.
```

Jika query gagal karena field tidak ditemukan:

```text
- Catat error.
- Tampilkan instruksi bahwa struktur database perlu diverifikasi.
- Jangan menghapus data audit yang sudah tersimpan di MySQL.
```

Jika data kosong:

```text
- Tampilkan empty state.
- Jangan anggap otomatis error.
- Jelaskan bahwa belum ada audit event yang terbaca.
```

---

## 27. AI Agent / Codex Rules

AI Agent/Codex wajib mengikuti aturan berikut:

```text
1. Jangan mengarang tabel Accurate selain yang disebut dalam POC.
2. Jangan menjadikan LOGIN sebagai sumber utama.
3. Jangan menganggap COMP_NAME dan IPADDRESS selalu terisi.
4. Jangan mengambil username Accurate dari TCP connection.
5. Jangan mengambil Windows user dari AUDIT table.
6. Jangan mengirim Accurate Audit Trail melalui RSyslog.
7. Jangan membuat write operation ke database Accurate.
8. Jangan membuat trigger/tabel baru di database Accurate.
9. Jangan hardcode credential Firebird.
10. Jangan hardcode path database Accurate.
11. Jangan hardcode device name seperti WIN-ACC-01 di logic program.
12. Jangan membuat anomaly/spike detection tanpa rule dokumen Detection Rules v2.
13. Harus pisahkan Windows user dan Accurate username.
14. Harus simpan hasil audit ke MySQL monitoring sebelum ditampilkan.
15. Harus menyediakan error handling jika Firebird tidak reachable.
```

---

## 28. Acceptance Criteria

Modul Accurate Firebird POC dianggap terimplementasi dengan benar jika:

```text
AC-AUDIT-001
Sistem dapat menyimpan konfigurasi Firebird host, port, database path, username, dan status enabled.

AC-AUDIT-002
Sistem dapat menjalankan test connection ke Firebird dan menampilkan hasil sukses/gagal.

AC-AUDIT-003
Sistem dapat membaca tabel AUDIT + USERS menggunakan query read-only.

AC-AUDIT-004
Sistem dapat mengambil AUDITID, MODIDATE, USERNAME, FULLNAME, SOURCE, TRANSTYPE, TRANSDESCRIPTION, INVOICENO, APPVERSION, dan STATUS.

AC-AUDIT-005
Sistem tetap berjalan walaupun COMP_NAME dan IPADDRESS kosong.

AC-AUDIT-006
Sistem tidak bergantung pada tabel LOGIN.

AC-AUDIT-007
Sistem menyimpan hasil audit ke MySQL monitoring table accurate_audit_events.

AC-AUDIT-008
Sistem mencegah duplikasi audit event pada sync berulang.

AC-AUDIT-009
Dashboard Accurate Audit menampilkan data audit dengan filter tanggal, user, source, transaction type, dan keyword.

AC-AUDIT-010
Detail audit menampilkan raw payload dan field penting dari Firebird.

AC-AUDIT-011
Sistem tidak melakukan INSERT, UPDATE, DELETE, ALTER, DROP, atau CREATE pada database Accurate.

AC-AUDIT-012
Jika koneksi Firebird gagal, sistem mencatat error dan tidak membuat aplikasi utama crash.
```

---

## 29. Contoh Narasi Skripsi

Contoh narasi yang aman untuk Bab 3 / Bab 4:

> Berdasarkan POC yang dilakukan pada Accurate 5 Enterprise berbasis Firebird 2.5, akses aplikasi Accurate dapat diamati melalui dua pendekatan. Pendekatan pertama adalah pemantauan koneksi aktif pada level sistem operasi, yaitu proses `accurate.exe` pada client Windows yang melakukan koneksi ke server Firebird melalui port 3051. Pendekatan kedua adalah pembacaan audit trail internal Accurate secara read-only melalui tabel `AUDIT` dan `USERS`. Dengan pemisahan sumber data ini, sistem dapat menampilkan status koneksi aplikasi, Windows user yang menjalankan Accurate, serta username internal Accurate yang melakukan aktivitas perubahan data tanpa mengubah struktur database Accurate.

Contoh narasi batasan:

> Penelitian ini tidak mengklaim membongkar mekanisme login internal Accurate secara langsung. Username internal Accurate diperoleh dari data audit yang tersedia pada tabel `AUDIT` dan `USERS`, sedangkan informasi device, IP, proses aplikasi, dan Windows user diperoleh dari Windows Agent. Tabel `LOGIN` tidak dijadikan sumber utama karena pada database uji tabel tersebut kosong.

---

## 30. Kesimpulan Dokumen

POC menunjukkan bahwa monitoring custom untuk Accurate 5 berbasis Firebird feasible, tetapi harus dilakukan dengan pemisahan sumber data yang jelas.

Kesimpulan utama:

1. Monitoring koneksi Accurate dilakukan dari sisi Windows Agent dan TCP/process monitoring.
2. Monitoring user internal dan aktivitas Accurate dilakukan dari tabel Firebird `AUDIT + USERS`.
3. Tabel `LOGIN` tidak dipakai sebagai sumber utama karena kosong pada database uji.
4. Field `COMP_NAME` dan `IPADDRESS` pada `AUDIT` tidak boleh diwajibkan karena bisa kosong.
5. Accurate Audit Reader harus read-only dan non-intrusif.
6. Dashboard harus memisahkan Windows user dan Accurate user.
7. Codex harus mengikuti dokumen ini agar tidak membuat asumsi teknis yang tidak sesuai POC.

---

## 31. Checklist untuk Dokumen Berikutnya

Sebelum membuat SRS v2, pastikan poin berikut sudah dikunci:

```text
[x] Accurate 5 menggunakan Firebird 2.5.
[x] Firebird port yang ditemukan pada POC adalah 3051.
[x] active connection/process monitoring dilakukan oleh Windows Agent.
[x] Accurate audit trail dibaca langsung dari Firebird.
[x] tabel utama audit adalah AUDIT + USERS.
[x] LOGIN tidak menjadi sumber utama.
[x] COMP_NAME dan IPADDRESS tidak wajib.
[x] modul audit bersifat read-only.
[x] hasil sync disimpan ke MySQL monitoring.
[x] dashboard menampilkan audit trail secara rapi, bukan raw dump.
```

Dokumen berikutnya yang disarankan:

```text
03_SRS_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md
```

