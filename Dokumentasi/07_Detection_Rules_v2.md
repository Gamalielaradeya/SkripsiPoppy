# 07 — Detection Rules v2
# Centralized Log Monitoring Dashboard

**Versi:** 2.0  
**Status:** Final Draft untuk AI Agent / Codex  
**Project:** Centralized Log Monitoring Dashboard  
**Target Implementasi:** Real device skala kecil PT. XYZ  
**Lingkungan:** 2 Laptop Windows Accurate 5 + 1 VPS Linux + ZeroTier  
**Stack Terkait:** Windows Agent, RSyslog, Laravel, MySQL/MariaDB, Firebird 2.5, Telegram Bot, Remote Action API  
**Dokumen terkait:**

- `01_PRD_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md`
- `02_Accurate_Firebird_POC_Findings_v2.md`
- `03_SRS_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md`
- `04_Database_Design_v2.md`
- `05_System_Architecture_v2.md`
- `06_Windows_Agent_and_RSyslog_Guide_v2.md`

---

# 1. Tujuan Dokumen

Dokumen ini menjelaskan aturan deteksi, alert, incident, severity, evidence, dan rekomendasi tindakan untuk sistem **Centralized Log Monitoring Dashboard** versi real-device.

Berbeda dari versi awal yang hanya memetakan keyword raw log seperti `timeout`, `CPU usage`, atau `service stopped`, versi ini menggunakan pendekatan **contextual detection**. Artinya, setiap alert harus memiliki:

1. Target yang jelas.
2. Sumber deteksi yang jelas.
3. Evidence atau bukti teknis.
4. Threshold atau kondisi yang eksplisit.
5. Dampak operasional.
6. Rekomendasi tindakan untuk IT admin.
7. Status penanganan.

Dokumen ini dibuat agar AI Agent / Codex tidak membuat alert yang ambigu seperti:

```text
CRITICAL - Firebird service unreachable
WARNING - Accurate process not running
INFO - Audit activity spike detected
```

Alert seperti itu tidak boleh dibuat tanpa konteks. Alert harus menjelaskan **siapa targetnya**, **dari mana terdeteksi**, dan **apa buktinya**.

---

# 2. Prinsip Utama Detection Rules v2

## 2.1 Alert Tidak Boleh Ngambang

Setiap alert wajib memiliki target.

Contoh yang salah:

```text
Firebird unreachable
```

Contoh yang benar:

```text
WIN-ACC-01 gagal terhubung ke Firebird VPS:3051
```

Contoh yang salah:

```text
Accurate process not running
```

Contoh yang benar:

```text
Accurate 5 tidak berjalan pada Laptop Finance 2 / WIN-ACC-02
```

---

## 2.2 Alert Harus Memiliki Evidence

Setiap alert wajib menyimpan bukti teknis pada tabel `alert_evidences`.

Contoh evidence:

```json
{
  "cpu_percent": 91,
  "duration_minutes": 3,
  "source": "Windows Agent",
  "agent_id": "uuid-device-001"
}
```

Contoh evidence koneksi Firebird:

```json
{
  "target_host": "10.147.20.5",
  "target_port": 3051,
  "tcp_status": "timeout",
  "failed_count": 3,
  "latency_ms": null,
  "checked_by": "WIN-ACC-01"
}
```

---

## 2.3 Alert Harus Dibedakan dari Incident

Sistem membedakan tiga level data:

```text
Event / Telemetry
    ↓
Alert
    ↓
Incident
```

Definisi:

| Level | Fungsi | Contoh |
|---|---|---|
| Event / Telemetry | Data masuk dari agent, parser, atau audit reader | CPU 87%, Firebird latency 650 ms |
| Alert | Peringatan dari satu kondisi spesifik | CPU tinggi pada WIN-ACC-02 |
| Incident | Korelasi beberapa alert/event yang menjelaskan masalah operasional | WIN-ACC-02 terindikasi lambat |

Contoh:

```text
Event:
WIN-ACC-02 cpu=87 ram=82 firebird_latency=650ms

Alert 1:
WARNING - CPU tinggi pada WIN-ACC-02

Alert 2:
WARNING - RAM tinggi pada WIN-ACC-02

Alert 3:
WARNING - Latency Firebird tinggi dari WIN-ACC-02

Incident:
WIN-ACC-02 terindikasi lambat karena CPU tinggi + RAM tinggi + latency Firebird tinggi.
```

---

## 2.4 Alert Tidak Selalu Harus Dibuat dari Semua Status Tidak Normal

Tidak semua kondisi harus menjadi alert.

Contoh:

```text
Accurate.exe tidak berjalan pukul 22:00
```

Belum tentu masalah, karena bisa saja di luar jam kerja.

Contoh:

```text
Accurate.exe tidak berjalan pada jam kerja saat Windows user Finance sedang aktif
```

Ini bisa menjadi alert warning.

---

## 2.5 Telegram Hanya untuk Alert Penting dan Kontekstual

Telegram wajib digunakan, tetapi tidak boleh spam.

Telegram dikirim untuk:

1. `ERROR`
2. `CRITICAL`
3. `WARNING` yang penting dan sudah melewati threshold/cooldown.

Telegram tidak dikirim untuk:

1. `INFO`
2. Telemetry normal.
3. Alert yang sama berulang dalam cooldown.
4. Status yang belum punya evidence cukup.

---

## 2.6 Tidak Ada Auto-Restart

Sistem boleh memberi rekomendasi:

```text
Gunakan Remote Desktop untuk investigasi.
```

atau:

```text
Lakukan Restart Client jika perangkat tidak responsif.
```

Tetapi sistem tidak boleh langsung restart otomatis.

Remote restart hanya boleh terjadi jika:

1. Admin klik tombol restart.
2. Admin mengisi alasan.
3. Sistem menampilkan konfirmasi.
4. Windows Agent menerima command.
5. Semua action dicatat pada `remote_actions`.

---

# 3. Sumber Data Deteksi

Detection rules v2 menggunakan beberapa sumber data.

| Sumber Data | Modul | Lewat RSyslog | Contoh Data |
|---|---|---:|---|
| Windows Agent heartbeat | Device Monitoring | Ya | hostname, user, last_seen, rdp_status |
| Windows Agent performance | Performance Monitoring | Ya | CPU, RAM, disk, uptime |
| Windows Agent network check | Network Monitoring | Ya | ping, tcp port 3051, latency |
| Windows Agent process check | Accurate Process Monitoring | Ya | accurate.exe running/tidak |
| Server health checker | VPS Service Monitoring | Bisa RSyslog / direct Laravel | Firebird service, MySQL, RSyslog |
| Firebird Audit Reader | Accurate Audit Trail | Tidak | AUDIT + USERS |
| Remote Action API | Remote Administration | Tidak | restart requested/executed |

---

# 4. Target Alert

Setiap alert harus mempunyai `target_type` dan `target_id` atau `target_name`.

## 4.1 Target Type

| target_type | Keterangan | Contoh |
|---|---|---|
| `device` | Laptop Windows client | WIN-ACC-01, Laptop Finance 1 |
| `server` | VPS Linux / service server | VPS-MONITOR |
| `service` | Service penting | Firebird, RSyslog, MySQL |
| `connection` | Relasi koneksi antara device dan target | WIN-ACC-01 → VPS:3051 |
| `accurate_audit` | Aktivitas audit Accurate | AUDITID 12345 |
| `remote_action` | Tindakan admin | restart command #15 |
| `system` | Sistem dashboard umum | Laravel scheduler |

## 4.2 Target Naming

Target yang tampil di UI harus memakai nama yang mudah dibaca admin.

Prioritas display name:

```text
device_label → hostname → agent_id
```

Contoh:

```text
Laptop Finance 1 / DESKTOP-A12BC
```

Bukan hanya:

```text
uuid-8e938e...
```

---

# 5. Severity Level

Severity yang digunakan:

```text
INFO
WARNING
ERROR
CRITICAL
```

## 5.1 Definisi Severity

| Severity | Definisi | Contoh |
|---|---|---|
| INFO | Kondisi normal atau aktivitas informatif | Device online, Accurate running |
| WARNING | Potensi masalah yang perlu dipantau | CPU tinggi, latency mulai tinggi |
| ERROR | Gangguan nyata pada salah satu device/service | Client gagal konek Firebird |
| CRITICAL | Gangguan serius yang berdampak luas / butuh tindakan cepat | Firebird service mati, device offline lama |

## 5.2 Prinsip Penentuan Severity

1. **WARNING** digunakan saat kondisi belum pasti fatal tetapi berpotensi mengganggu.
2. **ERROR** digunakan saat fungsi tertentu gagal.
3. **CRITICAL** digunakan saat layanan penting berhenti atau dampaknya luas.
4. Severity harus mempertimbangkan target dan impact.

Contoh:

```text
Firebird port timeout dari 1 device = ERROR untuk device tersebut.
Firebird service mati di VPS = CRITICAL karena berdampak ke semua device.
```

---

# 6. Threshold Global

Threshold disimpan di tabel `threshold_settings` agar dapat diubah dari Settings.

## 6.1 Threshold Device Heartbeat

| Key | Default | Keterangan |
|---|---:|---|
| `device_heartbeat_warning_minutes` | 5 | Warning jika agent tidak kirim heartbeat > 5 menit |
| `device_heartbeat_critical_minutes` | 15 | Critical/offline jika agent tidak kirim heartbeat > 15 menit |

## 6.2 Threshold CPU

| Key | Default | Keterangan |
|---|---:|---|
| `cpu_warning_percent` | 80 | CPU warning jika >= 80% |
| `cpu_critical_percent` | 90 | CPU critical jika >= 90% |
| `cpu_sustained_minutes` | 3 | Harus terjadi selama minimal 3 menit agar alert dibuat |

## 6.3 Threshold RAM

| Key | Default | Keterangan |
|---|---:|---|
| `ram_warning_percent` | 80 | RAM warning jika >= 80% |
| `ram_critical_percent` | 90 | RAM critical jika >= 90% |
| `ram_sustained_minutes` | 3 | Harus terjadi selama minimal 3 menit |

## 6.4 Threshold Disk

| Key | Default | Keterangan |
|---|---:|---|
| `disk_warning_percent` | 80 | Disk warning jika >= 80% |
| `disk_critical_percent` | 90 | Disk critical jika >= 90% |

## 6.5 Threshold Firebird Connectivity

| Key | Default | Keterangan |
|---|---:|---|
| `firebird_port` | 3051 | Port Firebird sesuai POC |
| `firebird_latency_warning_ms` | 500 | Warning jika latency >= 500 ms |
| `firebird_latency_critical_ms` | 1500 | Critical jika latency >= 1500 ms |
| `firebird_failed_count_error` | 3 | Error jika gagal konek 3 kali berturut-turut |
| `firebird_failed_count_critical` | 5 | Critical jika gagal konek 5 kali berturut-turut dari banyak device |

## 6.6 Threshold Accurate Process

| Key | Default | Keterangan |
|---|---:|---|
| `business_hours_start` | 08:00 | Awal jam kerja |
| `business_hours_end` | 18:00 | Akhir jam kerja |
| `accurate_process_missing_warning_minutes` | 5 | Warning jika accurate.exe tidak terdeteksi saat jam kerja |
| `accurate_process_crash_count_error` | 3 | Error jika process hilang/muncul berulang 3 kali dalam 10 menit |

## 6.7 Threshold Alert Cooldown

| Key | Default | Keterangan |
|---|---:|---|
| `alert_cooldown_minutes` | 5 | Alert sejenis tidak dibuat/dikirim ulang sebelum 5 menit |
| `telegram_cooldown_minutes` | 5 | Telegram sejenis tidak dikirim ulang sebelum 5 menit |

---

# 7. Format Alert Standar

Setiap alert harus memiliki field minimal:

```text
code
title
description
severity
category
target_type
target_id
target_name
detected_by
detected_at
impact
recommended_action
status
```

## 7.1 Format Title

Title harus singkat tetapi jelas.

Format:

```text
[Masalah] pada [Target]
```

Contoh:

```text
CPU tinggi pada Laptop Finance 2
```

```text
Service Firebird pada VPS tidak aktif
```

```text
WIN-ACC-01 gagal terhubung ke Firebird VPS:3051
```

## 7.2 Format Description

Description harus menjelaskan masalah dalam bahasa admin.

Contoh:

```text
Windows Agent pada Laptop Finance 2 melaporkan penggunaan CPU berada pada 91% selama lebih dari 3 menit. Kondisi ini dapat menyebabkan aplikasi Accurate menjadi lambat atau tidak responsif.
```

## 7.3 Format Impact

Impact menjelaskan dampak operasional.

Contoh:

```text
Aplikasi Accurate pada device tersebut dapat berjalan lambat.
```

```text
Semua client Accurate berpotensi gagal mengakses database Firebird.
```

## 7.4 Format Recommended Action

Recommended action harus praktis.

Contoh:

```text
Gunakan Remote Desktop untuk memeriksa aplikasi yang menggunakan CPU tinggi.
```

```text
Periksa service Firebird pada VPS dan restart service secara manual jika diperlukan.
```

```text
Cek koneksi ZeroTier pada device dan VPS.
```

---

# 8. Detection Rule — Device Heartbeat

## 8.1 DEVICE_HEARTBEAT_MISSED

| Field | Nilai |
|---|---|
| Code | `DEVICE_HEARTBEAT_MISSED` |
| Category | `device` |
| Severity | `WARNING` |
| Target | Device Windows |
| Detected by | Laravel Scheduler / Device Monitor |
| Condition | `last_seen_at` lebih lama dari `device_heartbeat_warning_minutes` |
| Default Threshold | 5 menit |
| Create Alert | Ya |
| Telegram | Opsional, jika belum ada alert sejenis |

### Evidence

```json
{
  "last_seen_at": "2026-05-28 10:10:00",
  "current_time": "2026-05-28 10:16:00",
  "minutes_since_last_seen": 6,
  "threshold_minutes": 5
}
```

### UI Title

```text
Heartbeat tidak diterima dari Laptop Finance 1
```

### Impact

```text
Device atau Windows Agent mungkin tidak aktif, sehingga data monitoring terbaru belum tersedia.
```

### Recommended Action

```text
Cek koneksi device, status ZeroTier, atau jalankan ulang Windows Agent.
```

---

## 8.2 DEVICE_OFFLINE

| Field | Nilai |
|---|---|
| Code | `DEVICE_OFFLINE` |
| Category | `device` |
| Severity | `CRITICAL` |
| Target | Device Windows |
| Detected by | Laravel Scheduler / Device Monitor |
| Condition | `last_seen_at` lebih lama dari `device_heartbeat_critical_minutes` |
| Default Threshold | 15 menit |
| Create Alert | Ya |
| Telegram | Ya |

### Evidence

```json
{
  "last_seen_at": "2026-05-28 10:00:00",
  "current_time": "2026-05-28 10:18:00",
  "minutes_since_last_seen": 18,
  "threshold_minutes": 15
}
```

### UI Title

```text
Laptop Finance 1 terdeteksi offline
```

### Impact

```text
Administrator tidak dapat memantau kondisi terbaru device tersebut. Jika device digunakan untuk Accurate, aktivitas operasional pengguna dapat terganggu.
```

### Recommended Action

```text
Hubungi pengguna device, cek koneksi ZeroTier, dan pastikan Windows Agent berjalan.
```

---

# 9. Detection Rule — CPU

## 9.1 CPU_HIGH

| Field | Nilai |
|---|---|
| Code | `CPU_HIGH` |
| Category | `performance` |
| Severity | `WARNING` |
| Target | Device Windows |
| Detected by | Windows Agent + Laravel Alert Service |
| Condition | CPU >= `cpu_warning_percent` selama `cpu_sustained_minutes` |
| Default Threshold | CPU >= 80% selama 3 menit |
| Create Alert | Ya |
| Telegram | Opsional / jika penting |

### Evidence

```json
{
  "cpu_percent": 87,
  "threshold_percent": 80,
  "duration_minutes": 3,
  "sample_count": 3,
  "source": "Windows Agent"
}
```

### UI Title

```text
CPU tinggi pada Laptop Finance 2
```

### Impact

```text
Aplikasi pada device dapat berjalan lambat, termasuk Accurate 5.
```

### Recommended Action

```text
Gunakan Remote Desktop untuk memeriksa aplikasi yang menggunakan CPU tinggi.
```

---

## 9.2 CPU_CRITICAL

| Field | Nilai |
|---|---|
| Code | `CPU_CRITICAL` |
| Category | `performance` |
| Severity | `CRITICAL` |
| Target | Device Windows |
| Detected by | Windows Agent + Laravel Alert Service |
| Condition | CPU >= `cpu_critical_percent` selama `cpu_sustained_minutes` |
| Default Threshold | CPU >= 90% selama 3 menit |
| Create Alert | Ya |
| Telegram | Ya |

### Evidence

```json
{
  "cpu_percent": 94,
  "threshold_percent": 90,
  "duration_minutes": 3,
  "sample_count": 3,
  "source": "Windows Agent"
}
```

### UI Title

```text
CPU sangat tinggi pada Laptop Finance 2
```

### Impact

```text
Device berpotensi tidak responsif dan mengganggu penggunaan Accurate 5.
```

### Recommended Action

```text
Lakukan investigasi melalui Remote Desktop. Jika device tidak responsif, pertimbangkan Restart Client secara manual dengan konfirmasi admin.
```

---

# 10. Detection Rule — RAM

## 10.1 RAM_HIGH

| Field | Nilai |
|---|---|
| Code | `RAM_HIGH` |
| Category | `performance` |
| Severity | `WARNING` |
| Target | Device Windows |
| Detected by | Windows Agent |
| Condition | RAM >= `ram_warning_percent` selama `ram_sustained_minutes` |
| Default Threshold | RAM >= 80% selama 3 menit |
| Create Alert | Ya |
| Telegram | Opsional |

### UI Title

```text
Penggunaan RAM tinggi pada Laptop Finance 2
```

### Evidence

```json
{
  "ram_percent": 86,
  "threshold_percent": 80,
  "duration_minutes": 3
}
```

---

## 10.2 RAM_CRITICAL

| Field | Nilai |
|---|---|
| Code | `RAM_CRITICAL` |
| Category | `performance` |
| Severity | `CRITICAL` |
| Target | Device Windows |
| Detected by | Windows Agent |
| Condition | RAM >= `ram_critical_percent` selama `ram_sustained_minutes` |
| Default Threshold | RAM >= 90% selama 3 menit |
| Create Alert | Ya |
| Telegram | Ya |

### UI Title

```text
Penggunaan RAM sangat tinggi pada Laptop Finance 2
```

---

# 11. Detection Rule — Disk

## 11.1 DISK_HIGH

| Field | Nilai |
|---|---|
| Code | `DISK_HIGH` |
| Category | `performance` |
| Severity | `WARNING` |
| Target | Device Windows |
| Detected by | Windows Agent |
| Condition | Disk usage >= `disk_warning_percent` |
| Default Threshold | 80% |
| Create Alert | Ya |
| Telegram | Opsional |

### UI Title

```text
Kapasitas disk mulai penuh pada Laptop Finance 1
```

### Evidence

```json
{
  "disk_percent": 83,
  "drive": "C:",
  "threshold_percent": 80
}
```

---

## 11.2 DISK_CRITICAL

| Field | Nilai |
|---|---|
| Code | `DISK_CRITICAL` |
| Category | `performance` |
| Severity | `CRITICAL` |
| Target | Device Windows |
| Detected by | Windows Agent |
| Condition | Disk usage >= `disk_critical_percent` |
| Default Threshold | 90% |
| Create Alert | Ya |
| Telegram | Ya |

### UI Title

```text
Kapasitas disk hampir penuh pada Laptop Finance 1
```

### Impact

```text
Sistem operasi atau aplikasi Accurate dapat terganggu jika ruang disk habis.
```

---

# 12. Detection Rule — Firebird Connectivity dari Client

## 12.1 FIREBIRD_LATENCY_HIGH

| Field | Nilai |
|---|---|
| Code | `FIREBIRD_LATENCY_HIGH` |
| Category | `network` |
| Severity | `WARNING` |
| Target | Connection: Device → VPS Firebird |
| Detected by | Windows Agent |
| Condition | Latency TCP/Ping ke Firebird >= `firebird_latency_warning_ms` |
| Default Threshold | 500 ms |
| Create Alert | Ya |
| Telegram | Opsional |

### UI Title

```text
Koneksi Firebird lambat dari Laptop Finance 2
```

### Evidence

```json
{
  "source_device": "WIN-ACC-02",
  "target_host": "10.147.20.5",
  "target_port": 3051,
  "latency_ms": 680,
  "threshold_ms": 500
}
```

### Impact

```text
Akses Accurate dari device tersebut dapat terasa lambat.
```

### Recommended Action

```text
Cek koneksi ZeroTier, kualitas jaringan device, dan status VPS.
```

---

## 12.2 FIREBIRD_PORT_TIMEOUT

| Field | Nilai |
|---|---|
| Code | `FIREBIRD_PORT_TIMEOUT` |
| Category | `network` |
| Severity | `ERROR` |
| Target | Connection: Device → VPS Firebird |
| Detected by | Windows Agent |
| Condition | TCP connect ke Firebird port 3051 gagal >= `firebird_failed_count_error` kali berturut-turut |
| Default Threshold | 3 kali gagal |
| Create Alert | Ya |
| Telegram | Ya |

### UI Title

```text
Laptop Finance 1 gagal terhubung ke Firebird VPS:3051
```

### Evidence

```json
{
  "source_device": "WIN-ACC-01",
  "target_host": "10.147.20.5",
  "target_port": 3051,
  "tcp_status": "timeout",
  "failed_count": 3
}
```

### Impact

```text
Accurate pada device tersebut berpotensi tidak dapat membuka database.
```

### Recommended Action

```text
Cek koneksi ZeroTier device, firewall, dan pastikan Firebird service aktif di VPS.
```

---

## 12.3 FIREBIRD_CLIENT_CONNECTION_RECOVERED

| Field | Nilai |
|---|---|
| Code | `FIREBIRD_CLIENT_CONNECTION_RECOVERED` |
| Category | `network` |
| Severity | `INFO` |
| Target | Connection: Device → VPS Firebird |
| Detected by | Windows Agent |
| Condition | Koneksi yang sebelumnya timeout kembali sukses |
| Create Alert | Tidak wajib, update status alert sebelumnya |
| Telegram | Opsional, bisa dikirim sebagai resolved notice jika diperlukan |

### Behavior

Jika alert `FIREBIRD_PORT_TIMEOUT` masih open untuk device yang sama dan koneksi sudah pulih, sistem boleh:

1. Menandai alert sebagai `resolved`, atau
2. Menampilkan status recovered pada detail alert.

---

# 13. Detection Rule — Firebird Service di VPS

## 13.1 FIREBIRD_SERVICE_DOWN

| Field | Nilai |
|---|---|
| Code | `FIREBIRD_SERVICE_DOWN` |
| Category | `service` |
| Severity | `CRITICAL` |
| Target | VPS Firebird Service |
| Detected by | Server Health Checker |
| Condition | Service Firebird inactive/stopped atau port 3051 closed di VPS |
| Create Alert | Ya |
| Telegram | Ya |

### UI Title

```text
Service Firebird pada VPS tidak aktif
```

### Evidence

```json
{
  "server": "VPS-MONITOR",
  "service_name": "firebird",
  "service_status": "inactive",
  "port_3051_status": "closed",
  "checked_by": "Server Health Checker"
}
```

### Impact

```text
Semua laptop client Accurate berpotensi gagal mengakses database Accurate.
```

### Recommended Action

```text
Login ke VPS dan periksa service Firebird. Restart service secara manual jika diperlukan.
```

---

## 13.2 FIREBIRD_SERVICE_RUNNING

| Field | Nilai |
|---|---|
| Code | `FIREBIRD_SERVICE_RUNNING` |
| Category | `service` |
| Severity | `INFO` |
| Target | VPS Firebird Service |
| Detected by | Server Health Checker |
| Condition | Firebird service active dan port 3051 open |
| Create Alert | Tidak |
| Telegram | Tidak |

### Behavior

Status ini digunakan untuk update dashboard, bukan alert.

---

# 14. Detection Rule — Accurate Process

## 14.1 ACCURATE_PROCESS_NOT_DETECTED

| Field | Nilai |
|---|---|
| Code | `ACCURATE_PROCESS_NOT_DETECTED` |
| Category | `accurate_process` |
| Severity | `WARNING` |
| Target | Device Windows |
| Detected by | Windows Agent |
| Condition | `accurate.exe` tidak ditemukan saat jam kerja dan device online |
| Create Alert | Ya |
| Telegram | Opsional |

### Important Rule

Alert ini hanya dibuat jika semua kondisi berikut terpenuhi:

1. Device online.
2. Agent berjalan.
3. Waktu saat ini berada dalam business hours.
4. Windows user aktif tersedia.
5. `accurate.exe` tidak ditemukan selama threshold tertentu.

Jika di luar jam kerja, cukup tampilkan status:

```text
Accurate Not Running
```

bukan alert.

### UI Title

```text
Accurate 5 tidak berjalan pada Laptop Finance 2
```

### Evidence

```json
{
  "device": "WIN-ACC-02",
  "windows_user": "DESKTOP-XYZ\\Finance",
  "process_name": "accurate.exe",
  "process_status": "not_found",
  "business_hours": true,
  "duration_minutes": 5
}
```

### Impact

```text
Pengguna device tersebut kemungkinan tidak sedang dapat menggunakan Accurate, atau aplikasi Accurate tertutup/crash.
```

### Recommended Action

```text
Hubungi pengguna atau gunakan Remote Desktop untuk memastikan aplikasi Accurate berjalan normal.
```

---

## 14.2 ACCURATE_PROCESS_CRASH_LOOP

| Field | Nilai |
|---|---|
| Code | `ACCURATE_PROCESS_CRASH_LOOP` |
| Category | `accurate_process` |
| Severity | `ERROR` |
| Target | Device Windows |
| Detected by | Windows Agent + Laravel Correlation |
| Condition | `accurate.exe` running/not_running berubah berulang >= threshold dalam 10 menit |
| Default Threshold | 3 kali |
| Create Alert | Ya |
| Telegram | Ya |

### UI Title

```text
Accurate 5 terindikasi crash berulang pada Laptop Finance 2
```

### Evidence

```json
{
  "device": "WIN-ACC-02",
  "process_name": "accurate.exe",
  "state_changes": 3,
  "window_minutes": 10
}
```

### Recommended Action

```text
Gunakan Remote Desktop untuk melihat error aplikasi dan periksa koneksi Firebird.
```

---

# 15. Detection Rule — Accurate Audit Trail

Accurate Audit Trail berasal dari **Firebird Audit Reader**, bukan dari RSyslog.

Sumber utama:

```text
AUDIT + USERS
```

Tidak boleh menjadikan tabel `LOGIN` sebagai sumber utama karena berdasarkan POC tabel tersebut kosong pada database uji.

---

## 15.1 ACCURATE_AUDIT_EVENT_IMPORTED

| Field | Nilai |
|---|---|
| Code | `ACCURATE_AUDIT_EVENT_IMPORTED` |
| Category | `accurate_audit` |
| Severity | `INFO` |
| Target | Accurate Audit Event |
| Detected by | Accurate Audit Reader |
| Condition | Event baru berhasil diambil dari AUDIT + USERS |
| Create Alert | Tidak |
| Telegram | Tidak |

### Behavior

Semua audit event ditampilkan di halaman Accurate Audit, tetapi tidak semuanya menjadi alert.

---

## 15.2 ACCURATE_AUDIT_DELETE_DETECTED

| Field | Nilai |
|---|---|
| Code | `ACCURATE_AUDIT_DELETE_DETECTED` |
| Category | `accurate_audit` |
| Severity | `CRITICAL` atau `ERROR` sesuai modul |
| Target | Accurate Audit Event |
| Detected by | Accurate Audit Reader |
| Condition | Field audit menunjukkan aktivitas delete/penghapusan, jika field tersedia dan valid |
| Create Alert | Ya, jika field valid |
| Telegram | Ya |

### Important Limitation

Rule ini hanya boleh diaktifkan jika data dari tabel `AUDIT` benar-benar menunjukkan aktivitas delete dengan field yang dapat dipercaya, misalnya:

```text
TRANSTYPE = DELETE
```

atau pola lain yang terbukti dari data POC/produksi.

Codex tidak boleh mengarang mapping delete jika field tidak jelas.

### UI Title

```text
Penghapusan transaksi Accurate terdeteksi
```

### Evidence

```json
{
  "audit_id": 12345,
  "accurate_username": "FINANCE01",
  "source": "Sales Invoice",
  "transaction_type": "DELETE",
  "invoice_no": "SI-000123",
  "activity_time": "2026-05-28 14:31:00"
}
```

### Impact

```text
Ada perubahan data penting pada database Accurate yang perlu diverifikasi oleh admin/keuangan.
```

### Recommended Action

```text
Verifikasi aktivitas tersebut kepada user Accurate terkait dan cek detail transaksi di Accurate.
```

---

## 15.3 ACCURATE_AUDIT_UPDATE_IMPORTANT_DATA

| Field | Nilai |
|---|---|
| Code | `ACCURATE_AUDIT_UPDATE_IMPORTANT_DATA` |
| Category | `accurate_audit` |
| Severity | `WARNING` |
| Target | Accurate Audit Event |
| Detected by | Accurate Audit Reader |
| Condition | Update pada modul atau transaksi yang ditandai penting |
| Create Alert | Opsional, jika daftar modul penting sudah dikonfigurasi |
| Telegram | Opsional |

### Important Limitation

Rule ini membutuhkan konfigurasi modul penting, misalnya:

```text
Sales Invoice
Purchase Invoice
Customer
Vendor
General Ledger
```

Jika modul penting belum dikonfigurasi, event hanya ditampilkan di Accurate Audit tanpa alert.

---

## 15.4 AUDIT_ACTIVITY_SPIKE_DETECTED — Deferred

Rule ini **tidak masuk MVP**.

Alasan:

1. Membutuhkan baseline normal aktivitas user.
2. Membutuhkan perhitungan agregasi per user/modul/waktu.
3. Berisiko halusinasi jika Codex mengarang threshold.
4. Tidak ada bukti POC yang cukup untuk langsung mengklaim spike detection.

Jika ingin ditambahkan di masa depan, harus ada dokumen khusus:

```text
Audit Activity Baseline and Spike Detection Rules
```

Untuk MVP, jangan buat alert:

```text
Audit activity spike detected
```

---

# 16. Detection Rule — Indikasi Device Lambat / Hang

Indikasi device lambat bukan alert tunggal, melainkan **incident correlation**.

## 16.1 DEVICE_SLOW_INDICATION

| Field | Nilai |
|---|---|
| Type | Incident |
| Code | `DEVICE_SLOW_INDICATION` |
| Severity | `WARNING` atau `ERROR` |
| Target | Device Windows |
| Detected by | Incident Correlation Service |
| Condition | Kombinasi beberapa alert/event |
| Create Incident | Ya |
| Telegram | Opsional jika severity ERROR |

### Rule Minimum

Incident dibuat jika dalam window 5 menit terdapat minimal 2 kondisi berikut:

1. CPU >= 80%.
2. RAM >= 80%.
3. Firebird latency >= 500 ms.
4. Heartbeat delay > 2x interval normal.
5. Accurate process crash loop.

### Severity

| Kondisi | Severity |
|---|---|
| 2 indikator warning | WARNING |
| 3 indikator warning atau ada 1 error | ERROR |
| Device offline / CPU critical + heartbeat missed | CRITICAL |

### UI Title

```text
Laptop Finance 2 terindikasi lambat
```

### Evidence

```json
{
  "window_minutes": 5,
  "cpu_percent": 87,
  "ram_percent": 82,
  "firebird_latency_ms": 680,
  "heartbeat_delay_seconds": 90
}
```

### Impact

```text
Penggunaan Accurate pada device tersebut berpotensi lambat atau tidak responsif.
```

### Recommended Action

```text
Gunakan Remote Desktop untuk investigasi. Jika tidak responsif, lakukan Restart Client manual dengan konfirmasi admin.
```

---

## 16.2 DEVICE_HANG_INDICATION

| Field | Nilai |
|---|---|
| Type | Incident |
| Code | `DEVICE_HANG_INDICATION` |
| Severity | `CRITICAL` |
| Target | Device Windows |
| Detected by | Incident Correlation Service |
| Condition | Heartbeat terlambat + CPU/RAM critical atau agent berhenti mengirim data |
| Create Incident | Ya |
| Telegram | Ya |

### Rule Minimum

Incident dibuat jika:

1. Heartbeat tidak diterima > critical threshold, atau
2. CPU/RAM critical terakhir tercatat, lalu heartbeat berhenti, atau
3. Device masih pingable tetapi agent tidak merespons command/status.

### UI Title

```text
Laptop Finance 2 terindikasi tidak responsif
```

### Recommended Action

```text
Coba Remote Desktop. Jika RDP gagal tetapi device masih reachable, lakukan Restart Client manual.
```

---

# 17. Detection Rule — Server Health

## 17.1 RSYSLOG_SERVICE_DOWN

| Field | Nilai |
|---|---|
| Code | `RSYSLOG_SERVICE_DOWN` |
| Category | `service` |
| Severity | `CRITICAL` |
| Target | VPS RSyslog Service |
| Detected by | Server Health Checker |
| Condition | RSyslog service inactive atau port syslog tidak listen |
| Telegram | Ya |

### Impact

```text
Sistem tidak dapat menerima log baru dari Windows Agent.
```

---

## 17.2 MYSQL_SERVICE_DOWN

| Field | Nilai |
|---|---|
| Code | `MYSQL_SERVICE_DOWN` |
| Category | `service` |
| Severity | `CRITICAL` |
| Target | VPS MySQL/MariaDB Service |
| Detected by | Server Health Checker |
| Condition | Database monitoring tidak dapat diakses |
| Telegram | Ya jika Telegram masih bisa dikirim |

### Impact

```text
Dashboard tidak dapat membaca atau menyimpan data monitoring.
```

---

## 17.3 LARAVEL_SCHEDULER_NOT_RUNNING

| Field | Nilai |
|---|---|
| Code | `LARAVEL_SCHEDULER_NOT_RUNNING` |
| Category | `system` |
| Severity | `ERROR` |
| Target | Laravel Scheduler |
| Detected by | Health Check / last scheduler heartbeat |
| Condition | Scheduler heartbeat tidak update melebihi threshold |
| Telegram | Ya |

---

# 18. Detection Rule — Remote Action

Remote action bukan alert utama, tetapi hasilnya bisa menghasilkan alert jika gagal.

## 18.1 REMOTE_RESTART_REQUESTED

| Field | Nilai |
|---|---|
| Code | `REMOTE_RESTART_REQUESTED` |
| Type | Remote Action Event |
| Severity | `INFO` |
| Target | Device Windows |
| Detected by | Laravel Remote Action API |
| Condition | Admin klik restart dan konfirmasi |
| Create Alert | Tidak |
| Telegram | Tidak |

### Required Fields

```text
admin_id
device_id
action_type = RESTART_CLIENT
reason
requested_at
status = pending
```

---

## 18.2 REMOTE_RESTART_FAILED

| Field | Nilai |
|---|---|
| Code | `REMOTE_RESTART_FAILED` |
| Category | `remote_action` |
| Severity | `ERROR` |
| Target | Device Windows |
| Detected by | Remote Action Service |
| Condition | Command gagal dieksekusi atau timeout |
| Create Alert | Ya |
| Telegram | Opsional |

### UI Title

```text
Restart Client gagal pada Laptop Finance 2
```

### Evidence

```json
{
  "remote_action_id": 15,
  "action_type": "RESTART_CLIENT",
  "status": "failed",
  "error_message": "Agent did not poll command within timeout"
}
```

---

# 19. Anti-Duplicate dan Cooldown

## 19.1 Alert Deduplication Key

Alert sejenis tidak boleh dibuat berulang-ulang tanpa jeda.

Deduplication key:

```text
code + target_type + target_id + severity
```

Contoh:

```text
CPU_HIGH:device:15:WARNING
```

## 19.2 Cooldown

Jika alert dengan key sama masih open dan belum melewati cooldown:

1. Jangan buat alert baru.
2. Update evidence atau occurrence count pada alert existing.
3. Jangan kirim Telegram ulang.

Default cooldown:

```text
5 menit
```

## 19.3 Escalation

Jika kondisi memburuk, alert boleh dibuat/escalate.

Contoh:

```text
CPU_HIGH WARNING → CPU_CRITICAL CRITICAL
```

Dalam kondisi ini:

1. Alert critical baru boleh dibuat.
2. Alert warning sebelumnya dapat ditandai superseded/resolved.
3. Telegram critical dikirim.

---

# 20. Alert Status Lifecycle

Status alert:

```text
open
acknowledged
resolved
suppressed
```

## 20.1 Open

Alert baru dibuat dan belum ditangani admin.

## 20.2 Acknowledged

Admin sudah mengetahui alert.

## 20.3 Resolved

Kondisi sudah pulih atau admin menandai selesai.

## 20.4 Suppressed

Alert tidak dimunculkan/dikirim karena cooldown atau rule suppression.

---

# 21. Incident Status Lifecycle

Status incident:

```text
open
investigating
resolved
closed
```

## 21.1 Open

Incident baru dibuat oleh correlation service.

## 21.2 Investigating

Admin sudah mulai investigasi.

## 21.3 Resolved

Kondisi utama sudah pulih.

## 21.4 Closed

Incident ditutup setelah validasi admin.

---

# 22. Telegram Contextual Alert Format

Telegram wajib mengirim alert yang jelas dan kontekstual.

## 22.1 Template Umum

```text
[{{ severity }}] {{ title }}

Target     : {{ target_name }}
Category   : {{ category }}
Detected by: {{ detected_by }}
Time       : {{ detected_at }}

Evidence:
{{ evidence_summary }}

Impact:
{{ impact }}

Action:
{{ recommended_action }}

Dashboard: {{ dashboard_url }}
```

## 22.2 Contoh CPU Critical

```text
[CRITICAL] CPU sangat tinggi pada Laptop Finance 2

Target     : Laptop Finance 2 / WIN-ACC-02
Category   : Performance
Detected by: Windows Agent
Time       : 2026-05-28 14:12:00

Evidence:
CPU 94% selama 3 menit, RAM 82%.

Impact:
Device berpotensi tidak responsif dan mengganggu penggunaan Accurate 5.

Action:
Gunakan Remote Desktop untuk investigasi. Jika device tidak responsif, lakukan Restart Client manual dengan konfirmasi admin.

Dashboard: https://monitoring.example.com/alerts/123
```

## 22.3 Contoh Firebird Service Down

```text
[CRITICAL] Service Firebird pada VPS tidak aktif

Target     : VPS-MONITOR / Firebird
Category   : Service
Detected by: Server Health Checker
Time       : 2026-05-28 14:20:00

Evidence:
service_status=inactive, port_3051=closed.

Impact:
Semua laptop client Accurate berpotensi gagal mengakses database Accurate.

Action:
Login ke VPS dan periksa service Firebird. Restart service secara manual jika diperlukan.

Dashboard: https://monitoring.example.com/alerts/124
```

## 22.4 Contoh Firebird Timeout dari Satu Device

```text
[ERROR] Laptop Finance 1 gagal terhubung ke Firebird VPS:3051

Target     : Laptop Finance 1 → VPS Firebird
Category   : Network
Detected by: Windows Agent
Time       : 2026-05-28 14:25:00

Evidence:
TCP connect ke 10.147.20.5:3051 timeout 3 kali berturut-turut.

Impact:
Accurate pada device tersebut berpotensi tidak dapat membuka database.

Action:
Cek koneksi ZeroTier device, firewall, dan pastikan Firebird service aktif di VPS.

Dashboard: https://monitoring.example.com/alerts/125
```

---

# 23. Dashboard Alert Display Rules

Halaman Alerts harus menampilkan kolom:

```text
Waktu
Severity
Title
Target
Category
Status
Detected By
Action
```

Detail alert harus menampilkan:

```text
Title
Severity
Status
Target
Detected by
Detected at
Description
Evidence
Impact
Recommended Action
Related Logs
Related Telemetry
Related Incident
Notification History
```

Alert list tidak boleh hanya menampilkan raw message tanpa konteks.

---

# 24. Dashboard Incident Display Rules

Halaman Incidents harus menampilkan:

```text
Waktu
Severity
Incident Type
Target
Summary
Evidence Count
Status
Action
```

Detail incident harus menampilkan:

```text
Summary
Target
Severity
Status
Timeline
Related Alerts
Related Telemetry
Evidence
Recommended Actions
Remote Actions
```

---

# 25. Mapping Event ke Alert

## 25.1 Event `device_heartbeat`

| Field | Alert Rule |
|---|---|
| last_seen delay > 5 min | DEVICE_HEARTBEAT_MISSED |
| last_seen delay > 15 min | DEVICE_OFFLINE |

## 25.2 Event `performance`

| Field | Alert Rule |
|---|---|
| cpu >= 80% sustained | CPU_HIGH |
| cpu >= 90% sustained | CPU_CRITICAL |
| ram >= 80% sustained | RAM_HIGH |
| ram >= 90% sustained | RAM_CRITICAL |
| disk >= 80% | DISK_HIGH |
| disk >= 90% | DISK_CRITICAL |

## 25.3 Event `network_check`

| Field | Alert Rule |
|---|---|
| latency >= 500 ms | FIREBIRD_LATENCY_HIGH |
| tcp timeout >= 3 times | FIREBIRD_PORT_TIMEOUT |
| recovered after timeout | FIREBIRD_CLIENT_CONNECTION_RECOVERED |

## 25.4 Event `accurate_process`

| Field | Alert Rule |
|---|---|
| process not found during business hours | ACCURATE_PROCESS_NOT_DETECTED |
| process state flapping | ACCURATE_PROCESS_CRASH_LOOP |

## 25.5 Event `server_service_check`

| Field | Alert Rule |
|---|---|
| firebird inactive | FIREBIRD_SERVICE_DOWN |
| rsyslog inactive | RSYSLOG_SERVICE_DOWN |
| mysql inactive | MYSQL_SERVICE_DOWN |
| scheduler stale | LARAVEL_SCHEDULER_NOT_RUNNING |

## 25.6 Event `accurate_audit_event`

| Field | Alert Rule |
|---|---|
| new audit event | No alert, show in Accurate Audit |
| delete event if clearly available | ACCURATE_AUDIT_DELETE_DETECTED |
| update important module if configured | ACCURATE_AUDIT_UPDATE_IMPORTANT_DATA |
| spike activity | Deferred, do not implement in MVP |

---

# 26. Pseudocode Alert Detection

## 26.1 General Alert Service

```php
public function detectAlert(array $event): ?Alert
{
    $rule = $this->ruleResolver->resolve($event);

    if (!$rule) {
        return null;
    }

    if (!$rule->conditionMet($event)) {
        return null;
    }

    $dedupeKey = $this->buildDedupeKey($rule, $event);

    $existing = $this->findOpenAlertByDedupeKey($dedupeKey);

    if ($existing && !$this->cooldownExpired($existing)) {
        $this->appendEvidence($existing, $event);
        return $existing;
    }

    $alert = $this->createAlert([
        'code' => $rule->code,
        'title' => $rule->buildTitle($event),
        'description' => $rule->buildDescription($event),
        'severity' => $rule->severity($event),
        'category' => $rule->category,
        'target_type' => $rule->targetType($event),
        'target_id' => $rule->targetId($event),
        'target_name' => $rule->targetName($event),
        'detected_by' => $rule->detectedBy,
        'impact' => $rule->buildImpact($event),
        'recommended_action' => $rule->buildRecommendedAction($event),
        'dedupe_key' => $dedupeKey,
        'status' => 'open',
        'detected_at' => now(),
    ]);

    $this->storeEvidence($alert, $event);

    if ($this->shouldSendTelegram($alert)) {
        $this->telegramService->sendContextualAlert($alert);
    }

    return $alert;
}
```

---

## 26.2 Incident Correlation Service

```php
public function correlateDeviceIncidents(Device $device): void
{
    $window = now()->subMinutes(5);

    $signals = $this->collectSignals($device, $window);

    if ($this->matchesSlowDeviceRule($signals)) {
        $this->createOrUpdateIncident('DEVICE_SLOW_INDICATION', $device, $signals);
    }

    if ($this->matchesHangRule($signals)) {
        $this->createOrUpdateIncident('DEVICE_HANG_INDICATION', $device, $signals);
    }
}
```

---

# 27. Hal yang Tidak Boleh Dilakukan Codex

Codex tidak boleh:

1. Membuat alert tanpa target.
2. Membuat alert tanpa evidence.
3. Membuat alert `Firebird unreachable` tanpa membedakan:
   - client gagal konek Firebird,
   - Firebird service di VPS mati,
   - device offline.
4. Membuat alert `Accurate process not running` tanpa device dan business-hour rule.
5. Membuat `Audit activity spike detected` pada MVP.
6. Mengambil username internal Accurate dari TCP/Windows process.
7. Menggunakan tabel `LOGIN` sebagai sumber utama audit Accurate.
8. Menganggap `COMP_NAME` dan `IPADDRESS` pada `AUDIT` selalu terisi.
9. Mengirim Telegram untuk semua event normal.
10. Melakukan auto-restart device.
11. Menjadikan raw log sebagai dashboard utama.
12. Meng-hardcode nama device seperti `WIN-ACC-01` di logic program.
13. Membuat rule security yang tidak ada di dokumen.
14. Menambahkan ELK/Grafana/Prometheus/SIEM kompleks.
15. Mengubah scope menjadi IPS atau auto-blocking system.

---

# 28. Acceptance Criteria Detection Rules

## 28.1 Alert Context

```text
Given sistem membuat alert
When alert muncul di dashboard
Then alert harus memiliki target, detected_by, evidence, impact, dan recommended_action
```

## 28.2 Firebird Client Timeout

```text
Given WIN-ACC-01 gagal connect ke Firebird port 3051 sebanyak 3 kali
When detection service berjalan
Then sistem membuat alert FIREBIRD_PORT_TIMEOUT untuk target WIN-ACC-01 → VPS Firebird
And evidence berisi target_host, target_port, tcp_status, dan failed_count
```

## 28.3 Firebird Service Down

```text
Given service Firebird di VPS inactive
When server health checker berjalan
Then sistem membuat alert FIREBIRD_SERVICE_DOWN dengan target VPS Firebird
And Telegram dikirim ke admin
```

## 28.4 Accurate Process Missing

```text
Given device online dan berada dalam jam kerja
And Windows Agent tidak menemukan accurate.exe selama 5 menit
When detection service berjalan
Then sistem membuat alert ACCURATE_PROCESS_NOT_DETECTED untuk device tersebut
```

## 28.5 No Accurate Process Alert Outside Business Hours

```text
Given waktu saat ini di luar jam kerja
And accurate.exe tidak berjalan
When detection service berjalan
Then sistem tidak membuat alert ACCURATE_PROCESS_NOT_DETECTED
And dashboard hanya menampilkan status Accurate Not Running
```

## 28.6 No Audit Spike Detection

```text
Given banyak audit event masuk dalam waktu singkat
When MVP detection service berjalan
Then sistem tidak boleh membuat alert Audit Activity Spike
```

## 28.7 Telegram Contextual Alert

```text
Given alert CRITICAL dibuat
When Telegram notification dikirim
Then pesan Telegram harus berisi severity, target, detected_by, evidence, impact, recommended action, dan link dashboard
```

## 28.8 Remote Restart Manual Only

```text
Given device mengalami incident critical
When admin belum menekan tombol restart
Then sistem tidak boleh menjalankan restart otomatis
```

---

# 29. Rekomendasi Implementasi Bertahap

## Phase 1 — Basic Detection

Implementasi rule:

```text
DEVICE_HEARTBEAT_MISSED
DEVICE_OFFLINE
CPU_HIGH
CPU_CRITICAL
RAM_HIGH
DISK_HIGH
FIREBIRD_PORT_TIMEOUT
FIREBIRD_SERVICE_DOWN
ACCURATE_PROCESS_NOT_DETECTED
```

## Phase 2 — Contextual Telegram

Implementasi:

```text
Telegram contextual template
Alert cooldown
Notification history
```

## Phase 3 — Incident Correlation

Implementasi:

```text
DEVICE_SLOW_INDICATION
DEVICE_HANG_INDICATION
Related alerts/evidence
```

## Phase 4 — Accurate Audit Alert

Implementasi hanya jika field audit sudah valid:

```text
ACCURATE_AUDIT_DELETE_DETECTED
ACCURATE_AUDIT_UPDATE_IMPORTANT_DATA
```

## Phase 5 — Remote Action Failure Alerts

Implementasi:

```text
REMOTE_RESTART_FAILED
REMOTE_COMMAND_TIMEOUT
```

---

# 30. Kesimpulan

Detection Rules v2 mengubah sistem dari sekadar parser raw log menjadi sistem alert yang kontekstual dan dapat digunakan oleh IT admin.

Prinsip utama:

```text
Alert harus menjawab:
- Masalah apa?
- Terjadi pada siapa?
- Terdeteksi dari mana?
- Buktinya apa?
- Dampaknya apa?
- Admin harus melakukan apa?
```

Dengan aturan ini, dashboard tidak akan menampilkan alert ambigu seperti:

```text
Firebird unreachable
Accurate process not running
Audit activity spike detected
```

Sebaliknya, sistem akan menampilkan alert yang jelas, misalnya:

```text
Laptop Finance 1 gagal terhubung ke Firebird VPS:3051
Service Firebird pada VPS tidak aktif
CPU sangat tinggi pada Laptop Finance 2
Accurate 5 tidak berjalan pada Laptop Finance 2 saat jam kerja
```

Dokumen ini wajib dijadikan acuan oleh Codex sebelum mengimplementasikan modul:

1. `AlertDetectionService`
2. `IncidentCorrelationService`
3. `TelegramService`
4. `DeviceHealthService`
5. `ServerHealthService`
6. `AccurateAuditAlertService`
7. `RemoteActionAlertService`

