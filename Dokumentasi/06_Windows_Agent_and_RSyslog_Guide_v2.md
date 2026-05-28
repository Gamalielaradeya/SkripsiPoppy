# 06 — Windows Agent and RSyslog Guide v2

**Nama Sistem:** Centralized Log Monitoring Dashboard  
**Versi Dokumen:** 2.0  
**Jenis Dokumen:** Panduan Teknis Windows Agent dan RSyslog  
**Target Implementasi:** Real-device environment, bukan simulasi container  
**Stack Terkait:** Windows Agent, RSyslog Server, Laravel, MySQL/MariaDB, VPS Linux, ZeroTier, Telegram Bot  
**Target Pengguna Dokumen:** AI Agent / Codex / Developer / Peneliti  

---

## 1. Tujuan Dokumen

Dokumen ini menjelaskan rancangan teknis **Windows Agent** dan integrasinya dengan **RSyslog Server** pada sistem **Centralized Log Monitoring Dashboard** versi real-device.

Pada dokumen versi 1, RSyslog client masih berupa container simulasi yang menghasilkan log dummy. Pada versi 2, pendekatan tersebut diganti menjadi **Windows Agent yang berjalan pada laptop Windows nyata**. Agent ini bertugas mengambil data aktual dari sistem operasi Windows, aplikasi Accurate 5, dan konektivitas ke server Firebird di VPS, lalu mengirimkannya sebagai log terstruktur ke RSyslog Server.

Dokumen ini menjadi pedoman untuk membangun komponen berikut:

1. Windows Agent.
2. Format log terstruktur yang dikirim agent.
3. Konfigurasi pengiriman log ke RSyslog Server.
4. RSyslog Server receiver di VPS.
5. Format file raw log yang dibaca Laravel Parser.
6. Mapping event agent ke tabel monitoring.
7. Batasan keamanan dan operasional agent.

---

## 2. Prinsip Utama

Sistem versi 2 wajib mengikuti prinsip berikut:

| Prinsip | Penjelasan |
|---|---|
| Real-device first | Data berasal dari laptop Windows nyata, bukan data simulasi random. |
| Windows-native monitoring | Agent berjalan langsung di Windows host, bukan hanya di WSL. |
| Push-based telemetry | Client/agent mengirim data ke server, bukan server yang menarik data terus-menerus. |
| RSyslog sebagai collector | RSyslog tetap dipakai sebagai penerima log terpusat. |
| Custom structured log | Agent hanya mengirim data yang relevan untuk IT admin. |
| Tidak mengirim raw Windows Event Log penuh | Hindari log noise seperti DistributedCOM atau event system acak sebagai dashboard utama. |
| Device tidak hardcode | Device diregistrasi otomatis berdasarkan `agent_id`, `hostname`, dan metadata. |
| Alert berbasis evidence | Setiap alert harus punya target, sumber deteksi, bukti, dan rekomendasi tindakan. |
| Remote action bukan via RSyslog | RSyslog hanya untuk monitoring/log. Remote restart memakai API/agent command. |

---

## 3. Peran Windows Agent

Windows Agent adalah program kecil yang berjalan di setiap laptop Windows client Accurate 5.

Fungsi utama Windows Agent:

1. Mengidentifikasi device.
2. Mengirim heartbeat berkala.
3. Membaca Windows user yang sedang aktif.
4. Membaca IP ZeroTier device.
5. Membaca metrik performa CPU, RAM, dan disk.
6. Mengecek apakah `accurate.exe` berjalan.
7. Mengecek koneksi device ke Firebird Server di VPS.
8. Mengecek status RDP service pada device.
9. Mengirim log terstruktur ke RSyslog Server.
10. Melakukan polling command dari Laravel untuk remote action manual.

Windows Agent **tidak bertugas** membaca audit trail Accurate. Audit trail Accurate dibaca langsung oleh Laravel Accurate Audit Reader melalui koneksi read-only ke Firebird.

---

## 4. Arsitektur Windows Agent dan RSyslog

```text
[Windows Laptop 1]
- Accurate 5 Client
- Windows Agent
- ZeroTier Client
- RDP Enabled
        |
        | Structured Syslog over ZeroTier
        v
[VPS Linux]
- RSyslog Server
- Laravel Parser
- MySQL/MariaDB Monitoring DB
- Dashboard

[Windows Laptop 2]
- Accurate 5 Client
- Windows Agent
- ZeroTier Client
- RDP Enabled
        |
        | Structured Syslog over ZeroTier
        v
[VPS Linux]
```

Alur utama:

```text
Windows Agent
      ↓
Collect telemetry dari Windows host
      ↓
Build structured log key=value
      ↓
Send syslog ke RSyslog Server VPS
      ↓
RSyslog menyimpan raw log ke /var/log/remote/{hostname}.log
      ↓
Laravel Parser membaca raw log
      ↓
Data masuk ke tabel monitoring
      ↓
Dashboard menampilkan status device, koneksi, performa, dan alert
```

---

## 5. Kenapa Tidak Menggunakan WSL sebagai RSyslog Client Utama

Windows Subsystem for Linux dapat menjalankan aplikasi Linux, tetapi WSL tidak ideal sebagai sumber utama monitoring device Windows karena:

1. WSL merepresentasikan environment Linux tersendiri, bukan seluruh kondisi Windows host secara natural.
2. Data seperti user Windows aktif, process owner, service RDP, dan proses `accurate.exe` lebih tepat dibaca langsung dari Windows.
3. WSL dapat memanggil PowerShell, tetapi membuat arsitektur lebih rumit dan sulit dijelaskan.
4. Untuk skripsi, pendekatan paling jelas adalah **Windows Agent berjalan langsung di Windows host**.

Kesimpulan:

```text
Tidak disarankan:
Windows -> WSL -> RSyslog client -> Server

Disarankan:
Windows Agent -> Structured syslog -> RSyslog Server
```

---

## 6. Teknologi Implementasi Agent

Windows Agent dapat dibuat dengan salah satu opsi berikut:

| Opsi | Kelebihan | Kekurangan | Rekomendasi |
|---|---|---|---|
| PowerShell Script | Mudah dibuat, native Windows, cepat untuk prototyping | Perlu pengaturan scheduled task/service | Cocok untuk MVP awal |
| Python Script | Lebih fleksibel, parsing dan HTTP API lebih mudah | Perlu Python runtime atau packaging exe | Cocok untuk versi stabil |
| .NET Worker Service | Profesional dan native Windows service | Implementasi lebih panjang | Cocok untuk tahap lanjutan |

Rekomendasi implementasi bertahap:

```text
Phase 1: PowerShell Agent untuk telemetry dan kirim syslog.
Phase 2: Python Agent untuk command polling dan packaging lebih rapi.
Phase 3: Windows Service jika dibutuhkan untuk produksi jangka panjang.
```

Untuk Codex, implementasi awal boleh menggunakan PowerShell atau Python. Namun struktur data, format log, dan alur agent harus mengikuti dokumen ini.

---

## 7. Identitas Device

Device tidak boleh di-hardcode.

Agent harus memiliki identitas berikut:

| Field | Sumber | Fungsi |
|---|---|---|
| `agent_id` | Dibuat saat agent pertama kali dijalankan | Identitas utama device. |
| `hostname` | Windows hostname | Identitas teknis host. |
| `device_label` | Diatur admin di dashboard | Nama ramah untuk UI. |
| `windows_user` | Windows session/user aktif | Mengetahui user OS yang memakai laptop. |
| `ip_zerotier` | Interface ZeroTier | Alamat private network. |
| `agent_version` | File konfigurasi agent | Tracking versi agent. |

### 7.1 Agent ID

Agent ID harus berupa UUID dan disimpan lokal pada device, misalnya:

```text
C:\ProgramData\CentralizedLogMonitoring\agent_id.txt
```

Contoh isi:

```text
8e3e8f4e-0f65-4d8f-aee5-5b3d8c8e8c01
```

Jika file belum ada, agent membuat UUID baru.

### 7.2 Alasan Menggunakan Agent ID

Agent ID diperlukan karena:

1. Hostname Windows bisa berubah.
2. IP ZeroTier bisa berubah jika konfigurasi diulang.
3. Device label bisa diubah admin.
4. Device harus tetap dikenali walaupun nama tampilannya berubah.

Identitas final:

```text
Primary identity : agent_id
Secondary        : hostname
Display label    : device_label
```

### 7.3 Hal yang Tidak Boleh

Codex tidak boleh membuat logic seperti:

```php
if ($hostname === 'WIN-ACC-01') {
    // special handling
}
```

Semua device harus berdasarkan data database.

---

## 8. Konfigurasi Agent

Agent membutuhkan file konfigurasi lokal.

Contoh path:

```text
C:\ProgramData\CentralizedLogMonitoring\agent-config.json
```

Contoh isi:

```json
{
  "agent_version": "1.0.0",
  "syslog_server": "10.147.20.5",
  "syslog_port": 5514,
  "syslog_protocol": "udp",
  "api_base_url": "http://10.147.20.5:8000/api/agent",
  "api_token": "CHANGE_ME_AGENT_TOKEN",
  "firebird_host": "10.147.20.5",
  "firebird_port": 3051,
  "heartbeat_interval_seconds": 30,
  "telemetry_interval_seconds": 30,
  "command_poll_interval_seconds": 10,
  "rdp_port": 3389,
  "working_hours_start": "08:00",
  "working_hours_end": "17:00"
}
```

Catatan:

1. `syslog_server` menggunakan IP ZeroTier VPS.
2. `firebird_host` menggunakan IP ZeroTier VPS.
3. `api_token` tidak boleh disimpan di repository publik.
4. Untuk pengembangan awal, token bisa dibuat sederhana, tetapi v2 tetap harus mendukung token per agent.

---

## 9. Data yang Diambil Windows Agent

### 9.1 Device Heartbeat

Tujuan:

Menandakan device masih hidup dan agent berjalan.

Data minimal:

```text
agent_id
hostname
windows_user
ip_zerotier
agent_version
uptime_seconds
rdp_status
timestamp
```

Contoh log:

```text
agent-heartbeat: event_type=device_heartbeat agent_id=8e3e8f4e-0f65-4d8f-aee5-5b3d8c8e8c01 hostname=DESKTOP-FIN01 windows_user=DESKTOP-FIN01\Finance ip_zerotier=10.147.20.11 agent_version=1.0.0 uptime_seconds=18200 rdp_status=available status=online
```

### 9.2 Performance Telemetry

Tujuan:

Mengirim penggunaan CPU, RAM, dan disk.

Data minimal:

```text
agent_id
hostname
cpu_percent
ram_percent
disk_percent
status
```

Contoh log normal:

```text
perf-monitor: event_type=performance agent_id=8e3e8f4e-0f65-4d8f-aee5-5b3d8c8e8c01 hostname=DESKTOP-FIN01 cpu_percent=42 ram_percent=61 disk_percent=55 status=normal
```

Contoh log warning:

```text
perf-monitor: event_type=performance agent_id=8e3e8f4e-0f65-4d8f-aee5-5b3d8c8e8c01 hostname=DESKTOP-FIN01 cpu_percent=87 ram_percent=82 disk_percent=55 status=warning
```

### 9.3 Firebird Connectivity Check

Tujuan:

Mengecek apakah laptop Windows dapat terhubung ke Firebird Server di VPS.

Data minimal:

```text
agent_id
hostname
target_host
target_port
ping_status
latency_ms
tcp_status
status
```

Contoh log sukses:

```text
network-monitor: event_type=firebird_connectivity agent_id=8e3e8f4e-0f65-4d8f-aee5-5b3d8c8e8c01 hostname=DESKTOP-FIN01 target_host=10.147.20.5 target_port=3051 ping_status=ok latency_ms=24 tcp_status=connected status=normal
```

Contoh log gagal:

```text
network-monitor: event_type=firebird_connectivity agent_id=8e3e8f4e-0f65-4d8f-aee5-5b3d8c8e8c01 hostname=DESKTOP-FIN01 target_host=10.147.20.5 target_port=3051 ping_status=ok latency_ms=28 tcp_status=timeout status=error
```

Makna:

1. Jika `ping_status=ok` tetapi `tcp_status=timeout`, jaringan dasar hidup tetapi port Firebird tidak bisa diakses.
2. Jika `ping_status=failed`, device tidak bisa menjangkau VPS.
3. Jika hanya satu device gagal, kemungkinan masalah di device atau jalur device tersebut.
4. Jika semua device gagal, kemungkinan masalah di VPS, ZeroTier, firewall, atau Firebird Server.

### 9.4 Accurate Process Check

Tujuan:

Mendeteksi apakah aplikasi Accurate 5 sedang berjalan di laptop Windows.

Data minimal:

```text
agent_id
hostname
windows_user
process_name
process_status
process_owner
process_path
status
```

Contoh log Accurate berjalan:

```text
accurate-process-monitor: event_type=accurate_process agent_id=8e3e8f4e-0f65-4d8f-aee5-5b3d8c8e8c01 hostname=DESKTOP-FIN01 windows_user=DESKTOP-FIN01\Finance process_name=accurate.exe process_status=running process_owner=DESKTOP-FIN01\Finance process_path="C:\Program Files (x86)\CPSSoft\ACCURATE5 Enterprise\accurate.exe" status=normal
```

Contoh log Accurate tidak berjalan:

```text
accurate-process-monitor: event_type=accurate_process agent_id=8e3e8f4e-0f65-4d8f-aee5-5b3d8c8e8c01 hostname=DESKTOP-FIN01 windows_user=DESKTOP-FIN01\Finance process_name=accurate.exe process_status=not_running process_owner=null process_path=null status=warning
```

Catatan severity:

1. `accurate.exe not_running` tidak otomatis critical.
2. Jika terjadi di luar jam kerja, cukup status informasi.
3. Jika terjadi saat jam kerja dan device digunakan tim keuangan, dapat menjadi warning.
4. Jika crash berulang, dapat menjadi error jika rule tersebut ditambahkan pada Detection Rules v2.

### 9.5 RDP Status Check

Tujuan:

Mengetahui apakah device siap untuk remote desktop.

Data minimal:

```text
agent_id
hostname
rdp_service_status
rdp_port
rdp_port_status
status
```

Contoh log:

```text
rdp-monitor: event_type=rdp_status agent_id=8e3e8f4e-0f65-4d8f-aee5-5b3d8c8e8c01 hostname=DESKTOP-FIN01 rdp_service_status=running rdp_port=3389 rdp_port_status=open status=available
```

Catatan:

1. RDP status digunakan untuk menampilkan tombol Remote Desktop.
2. Jika RDP tidak aktif, tombol Remote Desktop tetap boleh tampil, tetapi diberi status warning atau disabled.
3. Membuka Remote Desktop bukan lewat RSyslog, melainkan link/file `.rdp` dari dashboard.

---

## 10. Format Log Terstruktur

Windows Agent harus mengirim log dengan format yang mudah diparse.

Format umum:

```text
<tag>: key=value key=value key=value
```

Contoh:

```text
perf-monitor: event_type=performance agent_id=uuid hostname=DESKTOP-FIN01 cpu_percent=87 ram_percent=82 disk_percent=55 status=warning
```

### 10.1 Aturan Format

1. Gunakan `event_type` untuk membedakan jenis event.
2. Gunakan `key=value` untuk semua field utama.
3. Jika value mengandung spasi, gunakan kutip ganda.
4. Gunakan lowercase untuk key.
5. Gunakan snake_case untuk key.
6. Jangan gunakan format bebas yang sulit diparse.
7. Jangan mengirim seluruh Windows Event Log mentah sebagai telemetry utama.

### 10.2 Event Type Wajib

| Event Type | Tag Syslog | Fungsi |
|---|---|---|
| `device_heartbeat` | `agent-heartbeat` | Menandakan device online. |
| `performance` | `perf-monitor` | CPU/RAM/Disk. |
| `firebird_connectivity` | `network-monitor` | Koneksi device ke Firebird. |
| `accurate_process` | `accurate-process-monitor` | Status `accurate.exe`. |
| `rdp_status` | `rdp-monitor` | Status RDP device. |
| `remote_action_result` | `remote-action-monitor` | Hasil eksekusi remote command. |

### 10.3 Field Wajib Semua Event

| Field | Keterangan |
|---|---|
| `event_type` | Jenis event. |
| `agent_id` | UUID agent. |
| `hostname` | Hostname Windows. |
| `status` | Status event. |

### 10.4 Field Status

Nilai status yang disarankan:

```text
normal
warning
error
critical
online
offline
available
unavailable
running
not_running
connected
timeout
failed
```

---

## 11. Pengiriman Log ke RSyslog Server

### 11.1 Model Koneksi

RSyslog memakai model push:

```text
Windows Agent mengirim log → RSyslog Server menerima log
```

Bukan:

```text
RSyslog Server meminta data ke Windows Agent
```

### 11.2 Protokol

Untuk implementasi awal, agent dapat memakai UDP karena lebih sederhana. Untuk stabilitas lebih baik, TCP dapat digunakan setelah MVP berjalan.

| Protokol | Kelebihan | Kekurangan | Rekomendasi |
|---|---|---|---|
| UDP | Simpel, mudah dari PowerShell/Python | Tidak menjamin delivery | Cocok untuk heartbeat/telemetry awal |
| TCP | Lebih andal | Implementasi sedikit lebih panjang | Cocok untuk versi stabil |

Rekomendasi:

```text
MVP real-device: UDP ke port 5514 atau 514.
Versi stabil: TCP jika agent sudah matang.
```

### 11.3 Port

Jika RSyslog Server berjalan langsung di VPS Linux, port yang disarankan:

| Kebutuhan | Port | Catatan |
|---|---:|---|
| Syslog UDP | 514/udp atau 5514/udp | 514 standar, 5514 lebih aman untuk non-root/container. |
| Syslog TCP | 514/tcp atau 5514/tcp | Opsional untuk versi stabil. |

Karena VPS adalah Linux server, port 514 dapat digunakan jika service memiliki privilege yang sesuai. Jika memakai Docker atau ingin menghindari privileged port, gunakan 5514.

### 11.4 Jalur Jaringan

Pengiriman log harus melalui ZeroTier private IP.

Contoh:

```text
Windows Agent -> 10.147.20.5:5514/udp -> RSyslog Server VPS
```

Jangan jadikan IP publik VPS sebagai target utama agent kecuali memang dibutuhkan.

---

## 12. Contoh Pengiriman Log dengan PowerShell

Contoh sederhana UDP syslog sender:

```powershell
function Send-SyslogUdp {
    param(
        [string]$Server,
        [int]$Port,
        [string]$Message
    )

    $udpClient = New-Object System.Net.Sockets.UdpClient
    $bytes = [System.Text.Encoding]::UTF8.GetBytes($Message)
    [void]$udpClient.Send($bytes, $bytes.Length, $Server, $Port)
    $udpClient.Close()
}

$hostname = $env:COMPUTERNAME
$message = "agent-heartbeat: event_type=device_heartbeat agent_id=AGENT_ID hostname=$hostname status=online"
Send-SyslogUdp -Server "10.147.20.5" -Port 5514 -Message $message
```

Catatan:

1. Contoh ini hanya untuk proof of concept.
2. Implementasi final harus menambahkan timestamp, agent_id, windows_user, dan error handling.
3. Agent harus berjalan berkala melalui Scheduled Task atau service.

---

## 13. Contoh Pengiriman Log dengan Python

Contoh sederhana UDP syslog sender:

```python
import socket

SYSLOG_SERVER = "10.147.20.5"
SYSLOG_PORT = 5514

message = "agent-heartbeat: event_type=device_heartbeat agent_id=AGENT_ID hostname=DESKTOP-FIN01 status=online"

sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
sock.sendto(message.encode("utf-8"), (SYSLOG_SERVER, SYSLOG_PORT))
sock.close()
```

Catatan:

Python lebih enak untuk:

1. Membaca file konfigurasi JSON.
2. Polling remote command API.
3. Mengirim HTTP request ke Laravel.
4. Packaging menjadi executable.

---

## 14. RSyslog Server di VPS

### 14.1 Tujuan

RSyslog Server menerima log dari Windows Agent dan menyimpannya ke file berdasarkan hostname atau sumber.

### 14.2 Konfigurasi RSyslog Server

Contoh file:

```text
/etc/rsyslog.d/10-centralized-monitoring.conf
```

Isi contoh:

```conf
# Load UDP and TCP input modules
module(load="imudp")
module(load="imtcp")

# Listen for Windows Agent logs
input(type="imudp" port="5514" ruleset="remoteLogs")
input(type="imtcp" port="5514" ruleset="remoteLogs")

# Template for per-host files
template(name="RemoteHostFile" type="string" string="/var/log/remote/%HOSTNAME%.log")

# Template for all logs
template(name="RemoteAllFile" type="string" string="/var/log/remote/all.log")

# Template format for Laravel parser
# Example output:
# 2026-05-28T19:30:00+00:00 DESKTOP-FIN01 perf-monitor: event_type=performance ...
template(name="RemoteLogFormat" type="string" string="%timereported:::date-rfc3339% %HOSTNAME% %syslogtag%%msg%\n")

ruleset(name="remoteLogs") {
    action(
        type="omfile"
        dynaFile="RemoteHostFile"
        template="RemoteLogFormat"
        createDirs="on"
    )

    action(
        type="omfile"
        file="/var/log/remote/all.log"
        template="RemoteLogFormat"
    )

    stop
}
```

### 14.3 Folder Log

Buat folder:

```bash
sudo mkdir -p /var/log/remote
sudo chown syslog:adm /var/log/remote
sudo chmod 755 /var/log/remote
```

Restart RSyslog:

```bash
sudo systemctl restart rsyslog
sudo systemctl status rsyslog
```

### 14.4 Output yang Diharapkan

Contoh file:

```text
/var/log/remote/DESKTOP-FIN01.log
/var/log/remote/DESKTOP-FIN02.log
/var/log/remote/all.log
```

Contoh isi:

```text
2026-05-28T19:30:01+00:00 DESKTOP-FIN01 agent-heartbeat: event_type=device_heartbeat agent_id=8e3e8f4e hostname=DESKTOP-FIN01 status=online
2026-05-28T19:30:02+00:00 DESKTOP-FIN01 perf-monitor: event_type=performance agent_id=8e3e8f4e hostname=DESKTOP-FIN01 cpu_percent=42 ram_percent=61 disk_percent=55 status=normal
2026-05-28T19:30:03+00:00 DESKTOP-FIN01 network-monitor: event_type=firebird_connectivity agent_id=8e3e8f4e hostname=DESKTOP-FIN01 target_host=10.147.20.5 target_port=3051 tcp_status=connected latency_ms=24 status=normal
```

---

## 15. Laravel Parser untuk Log Agent

Laravel Parser harus membaca file RSyslog dan mendukung format key=value.

### 15.1 Format Raw Log

Format dari RSyslog:

```text
2026-05-28T19:30:02+00:00 DESKTOP-FIN01 perf-monitor: event_type=performance agent_id=8e3e8f4e hostname=DESKTOP-FIN01 cpu_percent=42 ram_percent=61 disk_percent=55 status=normal
```

Komponen:

| Komponen | Contoh |
|---|---|
| Timestamp | `2026-05-28T19:30:02+00:00` |
| Syslog hostname | `DESKTOP-FIN01` |
| Tag | `perf-monitor:` |
| Message | `event_type=performance ...` |

### 15.2 Parsing Key Value

Parser harus mengubah message menjadi associative array.

Input:

```text
event_type=performance agent_id=8e3e8f4e hostname=DESKTOP-FIN01 cpu_percent=42 ram_percent=61 disk_percent=55 status=normal
```

Output:

```json
{
  "event_type": "performance",
  "agent_id": "8e3e8f4e",
  "hostname": "DESKTOP-FIN01",
  "cpu_percent": "42",
  "ram_percent": "61",
  "disk_percent": "55",
  "status": "normal"
}
```

### 15.3 Quote Handling

Value dengan spasi harus didukung:

```text
process_path="C:\Program Files (x86)\CPSSoft\ACCURATE5 Enterprise\accurate.exe"
```

Parser harus membaca value di dalam tanda kutip sebagai satu nilai.

### 15.4 Mapping Event ke Tabel

| Event Type | Tabel Tujuan |
|---|---|
| `device_heartbeat` | `devices`, `device_telemetries` atau heartbeat field di `devices` |
| `performance` | `device_telemetries` |
| `firebird_connectivity` | `network_checks` |
| `accurate_process` | `accurate_process_snapshots` |
| `rdp_status` | `devices` atau `device_telemetries` |
| `remote_action_result` | `remote_actions` |
| Semua raw line | `logs` / Advanced Logs |

### 15.5 Anti-Duplikasi

Setiap raw log tetap disimpan dengan hash untuk mencegah duplikasi.

Hash dibuat dari:

```text
source_file + logged_at + syslog_hostname + raw_message
```

---

## 16. Mapping Severity dari Agent Event

Severity tidak boleh dibuat generik tanpa bukti.

### 16.1 Performance

| Kondisi | Severity |
|---|---|
| CPU < 80 dan RAM < 85 | INFO |
| CPU >= 80 dan < 90 | WARNING |
| CPU >= 90 | CRITICAL |
| RAM >= 85 | WARNING |
| Disk >= 90 | WARNING |

### 16.2 Firebird Connectivity

| Kondisi | Severity |
|---|---|
| `tcp_status=connected` | INFO |
| `latency_ms` melebihi threshold warning | WARNING |
| `tcp_status=timeout` dari 1 device | ERROR |
| semua device `tcp_status=timeout` | Incident/CRITICAL kandidat, perlu korelasi |

### 16.3 Accurate Process

| Kondisi | Severity |
|---|---|
| `process_status=running` | INFO |
| `process_status=not_running` di luar jam kerja | INFO atau tidak buat alert |
| `process_status=not_running` saat jam kerja pada device finance | WARNING |
| crash berulang | ERROR, jika rule dibuat di Detection Rules v2 |

### 16.4 Heartbeat

Heartbeat tidak selalu menghasilkan log warning. Status offline dihitung oleh Laravel berdasarkan `last_seen_at`.

| Kondisi | Severity |
|---|---|
| Last seen <= threshold normal | INFO |
| Last seen > 5 menit | WARNING |
| Last seen > 15 menit | CRITICAL |

---

## 17. Contextual Alert dari Agent Event

Alert harus jelas. Setiap alert minimal berisi:

| Field | Wajib | Contoh |
|---|---|---|
| `title` | Ya | CPU tinggi pada Laptop Finance 2 |
| `target_type` | Ya | device |
| `target_id` | Ya | device id |
| `target_label` | Ya | Laptop Finance 2 |
| `detected_by` | Ya | Windows Agent |
| `severity` | Ya | WARNING |
| `evidence` | Ya | CPU 87%, RAM 82% |
| `impact` | Ya | Device berpotensi lambat saat menjalankan Accurate |
| `recommended_action` | Ya | Gunakan Remote Desktop untuk investigasi |

Contoh alert rapi:

```text
WARNING - CPU tinggi pada Laptop Finance 2
Target     : Laptop Finance 2 / DESKTOP-FIN02
Detected by: Windows Agent
Evidence   : CPU 87%, RAM 82%, interval 5 menit terakhir
Impact     : Device dapat terasa lambat saat menjalankan Accurate
Action     : Remote Desktop untuk cek aplikasi aktif, restart manual bila diperlukan
```

Contoh alert yang tidak boleh:

```text
WARNING - CPU tinggi
CRITICAL - Firebird unreachable
Accurate process not running
```

Karena tidak jelas target, evidence, dan sumber deteksinya.

---

## 18. Remote Action Result

Remote action tidak dikirim melalui RSyslog sebagai perintah. Namun agent boleh mengirim hasil eksekusi action sebagai log monitoring.

### 18.1 Alur Command

```text
Admin klik Restart Client
      ↓
Laravel membuat remote_action status=pending
      ↓
Windows Agent polling API
      ↓
Agent menjalankan command setelah validasi
      ↓
Agent update API status executed/failed
      ↓
Agent mengirim log result ke RSyslog
```

### 18.2 Contoh Result Log

```text
remote-action-monitor: event_type=remote_action_result agent_id=8e3e8f4e hostname=DESKTOP-FIN01 action_id=123 action_type=restart_client result=accepted message="Restart scheduled in 30 seconds" status=info
```

### 18.3 Hal yang Tidak Boleh

1. Jangan kirim command restart via RSyslog.
2. Jangan auto restart berdasarkan alert.
3. Jangan restart tanpa konfirmasi admin.
4. Jangan restart tanpa mencatat alasan.

---

## 19. Agent Command Polling API

Meskipun dokumen ini fokus pada RSyslog, agent perlu polling API untuk remote action.

Endpoint konseptual:

```text
GET  /api/agent/commands
POST /api/agent/commands/{id}/ack
POST /api/agent/commands/{id}/result
```

Header:

```text
Authorization: Bearer {agent_token}
X-Agent-ID: {agent_id}
```

Jenis command:

| Command | Fungsi |
|---|---|
| `ping_test` | Agent melakukan test koneksi dan melapor. |
| `restart_client` | Agent menjalankan restart Windows manual. |
| `restart_agent` | Agent restart dirinya sendiri jika didukung. |
| `collect_now` | Agent langsung mengirim telemetry tanpa menunggu interval. |

Remote Desktop tidak perlu command API karena dashboard cukup membuka `.rdp` link/file.

---

## 20. Instalasi Windows Agent

### 20.1 Struktur Folder Agent

Disarankan:

```text
C:\ProgramData\CentralizedLogMonitoring\
├── agent-config.json
├── agent_id.txt
├── logs\
│   └── agent.log
└── agent\
    ├── agent.ps1 / agent.py
    └── helper files
```

### 20.2 Scheduled Task

Untuk MVP PowerShell, agent dapat dijalankan menggunakan Windows Scheduled Task.

Contoh jadwal:

```text
Every 1 minute: run telemetry collection
Every 10 seconds: command polling, jika script mendukung loop
```

Contoh command:

```powershell
powershell.exe -ExecutionPolicy Bypass -File "C:\ProgramData\CentralizedLogMonitoring\agent\agent.ps1"
```

### 20.3 Windows Service

Untuk versi lebih rapi, agent dapat dibuat sebagai service menggunakan:

1. NSSM untuk menjalankan script sebagai service.
2. Python executable + NSSM.
3. .NET Worker Service.

---

## 21. Security Considerations

### 21.1 Network Security

1. Gunakan ZeroTier private IP untuk komunikasi agent ke VPS.
2. Jangan expose syslog port ke publik jika tidak perlu.
3. Firewall VPS sebaiknya hanya mengizinkan port syslog dari subnet ZeroTier.
4. Firebird port 3051 sebaiknya hanya terbuka untuk subnet ZeroTier.

### 21.2 Agent Token

1. Setiap agent harus memiliki token unik.
2. Token tidak boleh di-hardcode di repository.
3. Token disimpan lokal pada `agent-config.json`.
4. Jika token bocor, admin harus bisa regenerate token.

### 21.3 Remote Restart

1. Harus manual oleh admin.
2. Harus menggunakan modal konfirmasi.
3. Harus mencatat alasan.
4. Harus mencatat admin yang meminta.
5. Harus mencatat waktu request, waktu execute, dan hasil.
6. Tidak boleh dilakukan otomatis oleh sistem.

### 21.4 Data Privacy

Agent hanya mengirim data operasional IT:

```text
hostname
windows_user
cpu/ram/disk
process accurate.exe
koneksi Firebird
status RDP
heartbeat
```

Agent tidak boleh mengirim:

```text
password Windows
password Accurate
isi transaksi Accurate
file pribadi user
full Windows Event Log tanpa filter
```

---

## 22. Error Handling Agent

Agent harus menangani kondisi berikut:

| Kondisi | Handling |
|---|---|
| Tidak bisa baca config | Tulis log lokal dan berhenti. |
| Tidak bisa kirim syslog | Tulis log lokal, retry pada interval berikutnya. |
| ZeroTier belum aktif | Kirim status network failed jika memungkinkan. |
| Firebird host tidak reachable | Kirim network event `ping_status=failed`. |
| Port Firebird timeout | Kirim network event `tcp_status=timeout`. |
| Accurate process tidak ditemukan | Kirim accurate process status `not_running`. |
| API command tidak bisa diakses | Tulis log lokal dan retry. |
| Remote command gagal | Update command result failed dan kirim result log. |

---

## 23. Testing Agent dan RSyslog

### 23.1 Test Case Agent Identity

| ID | Test Case | Expected Result |
|---|---|---|
| AGENT-ID-001 | Agent pertama kali dijalankan | `agent_id.txt` dibuat. |
| AGENT-ID-002 | Agent dijalankan ulang | Agent ID tetap sama. |
| AGENT-ID-003 | Hostname berubah | Device tetap dikenali dari `agent_id`. |

### 23.2 Test Case Heartbeat

| ID | Test Case | Expected Result |
|---|---|---|
| AGENT-HB-001 | Agent mengirim heartbeat | RSyslog menerima `device_heartbeat`. |
| AGENT-HB-002 | Agent berhenti > threshold | Dashboard menampilkan device warning/offline. |

### 23.3 Test Case Performance

| ID | Test Case | Expected Result |
|---|---|---|
| AGENT-PERF-001 | Agent kirim CPU/RAM/Disk normal | Data masuk `device_telemetries`. |
| AGENT-PERF-002 | CPU > 80 | Alert warning dibuat dengan target device. |
| AGENT-PERF-003 | CPU > 90 | Alert critical dibuat dengan evidence CPU. |

### 23.4 Test Case Firebird Connectivity

| ID | Test Case | Expected Result |
|---|---|---|
| AGENT-NET-001 | Port 3051 reachable | `tcp_status=connected`. |
| AGENT-NET-002 | Port 3051 timeout | Alert error target device → VPS. |
| AGENT-NET-003 | VPS tidak bisa diping | Network check failed. |

### 23.5 Test Case Accurate Process

| ID | Test Case | Expected Result |
|---|---|---|
| AGENT-ACC-001 | Accurate berjalan | `process_status=running`. |
| AGENT-ACC-002 | Accurate tidak berjalan | `process_status=not_running`. |
| AGENT-ACC-003 | Accurate tidak berjalan saat jam kerja | Alert warning jika rule aktif. |

### 23.6 Test Case RDP Status

| ID | Test Case | Expected Result |
|---|---|---|
| AGENT-RDP-001 | RDP service aktif | Dashboard menampilkan RDP available. |
| AGENT-RDP-002 | RDP service mati | Dashboard menampilkan RDP unavailable. |

### 23.7 Test Case Remote Action Result

| ID | Test Case | Expected Result |
|---|---|---|
| AGENT-RA-001 | Admin request restart | Agent menerima pending command. |
| AGENT-RA-002 | Agent execute restart | `remote_actions` berubah executed. |
| AGENT-RA-003 | Agent gagal execute | `remote_actions` berubah failed dengan error message. |

---

## 24. Acceptance Criteria

Windows Agent dan RSyslog dianggap berhasil jika:

1. Dua laptop Windows dapat mengirim heartbeat real ke VPS.
2. Device otomatis muncul di dashboard tanpa hardcode.
3. Dashboard menampilkan Windows user aktif dari masing-masing device.
4. Dashboard menampilkan CPU, RAM, dan disk dari masing-masing device.
5. Dashboard menampilkan status koneksi device ke Firebird port 3051.
6. Dashboard menampilkan status `accurate.exe` pada masing-masing device.
7. RSyslog menyimpan raw log per hostname di `/var/log/remote`.
8. Laravel Parser dapat membaca log key=value dari RSyslog.
9. Alert yang muncul memiliki target, evidence, detected by, impact, dan recommended action.
10. Remote action tidak berjalan otomatis dan hanya dipicu manual oleh admin.
11. Raw log tetap tersedia di Advanced Logs tetapi tidak menjadi fokus dashboard utama.

---

## 25. Hal yang Tidak Boleh Dilakukan AI Agent / Codex

Codex tidak boleh:

1. Menggunakan container `rsyslog-client` sebagai sumber utama real-device monitoring.
2. Menganggap WSL sebagai sumber monitoring Windows utama.
3. Mengirim semua Windows Event Log mentah ke dashboard utama.
4. Membuat device hardcoded seperti `WIN-ACC-01` di logic program.
5. Membuat alert tanpa target device/server.
6. Membuat alert tanpa evidence.
7. Menggunakan RSyslog untuk mengirim remote restart command.
8. Melakukan auto restart berdasarkan alert.
9. Menganggap `accurate.exe not_running` selalu critical.
10. Menganggap Firebird timeout dari satu device berarti Firebird server pasti mati.
11. Mengambil Accurate audit trail dari RSyslog.
12. Mencampur data audit Accurate dengan telemetry Windows tanpa field sumber yang jelas.

---

## 26. Ringkasan Implementasi untuk Codex

Bangun Windows Agent dan integrasi RSyslog dengan urutan berikut:

```text
1. Buat struktur konfigurasi agent.
2. Buat agent_id persistent.
3. Implement heartbeat event.
4. Implement syslog UDP sender.
5. Konfigurasi RSyslog Server VPS port 5514.
6. Pastikan raw log masuk /var/log/remote.
7. Implement parser key=value di Laravel.
8. Simpan heartbeat ke devices.
9. Implement performance telemetry.
10. Implement Firebird connectivity check.
11. Implement Accurate process check.
12. Implement RDP status check.
13. Implement contextual alert dari event yang jelas.
14. Implement polling command API untuk remote action.
15. Implement remote action result.
```

Dokumen ini harus digunakan bersama:

```text
01_PRD_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md
02_Accurate_Firebird_POC_Findings_v2.md
03_SRS_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md
04_Database_Design_v2.md
05_System_Architecture_v2.md
```

---

## 27. Kesimpulan

Windows Agent adalah komponen utama yang membuat sistem versi 2 benar-benar berbasis real-device. Agent mengambil data aktual dari laptop Windows pengguna Accurate 5, lalu mengirimkannya sebagai log terstruktur ke RSyslog Server di VPS melalui jaringan ZeroTier.

RSyslog tetap digunakan sebagai log collector terpusat, tetapi data yang dikirim harus relevan dan terstruktur, bukan raw Windows Event Log acak. Dengan pendekatan ini, dashboard dapat menampilkan informasi yang benar-benar dibutuhkan IT admin: device online, user Windows aktif, performa device, koneksi ke Firebird, status Accurate, RDP availability, dan hasil remote action.
