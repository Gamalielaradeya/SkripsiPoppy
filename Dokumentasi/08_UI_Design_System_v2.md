# 08 UI Design System v2
# Centralized Log Monitoring Dashboard

**Versi:** 2.0  
**Status:** Draft Final untuk AI Agent / Codex  
**Project:** Centralized Log Monitoring Dashboard  
**Target Implementasi:** Real-device monitoring untuk laptop Windows pengguna Accurate 5 dan VPS Linux  
**Stack UI:** Laravel Blade, Tailwind CSS, Alpine.js, Chart.js, Heroicons/Lucide Icons  
**Referensi Gaya:** IT operations cockpit, AdminLTE-inspired, Hallmark-inspired design discipline  
**Tujuan Dokumen:** Mengunci arah visual, komponen UI, prinsip desain, token status, layout, dan aturan implementasi agar AI Agent/Codex tidak membuat tampilan dashboard generik, berantakan, atau tidak relevan dengan kebutuhan IT admin.

---

## 1. Tujuan Dokumen

Dokumen ini menjelaskan **design system** untuk aplikasi **Centralized Log Monitoring Dashboard** versi real-device.

Berbeda dengan versi awal yang masih berfokus pada dashboard log umum, versi ini diarahkan sebagai **IT monitoring cockpit** untuk lingkungan PT XYZ skala kecil, yaitu:

1. Dua laptop Windows sebagai client Accurate 5.
2. Satu VPS Linux sebagai server monitoring, server Firebird/Accurate database, dan pusat dashboard.
3. ZeroTier sebagai jaringan privat antara VPS dan laptop Windows.
4. Windows Agent sebagai pengirim telemetry real-device ke RSyslog.
5. Accurate Audit Reader sebagai pembaca audit trail Firebird secara read-only.
6. Telegram sebagai contextual proactive alert.
7. Remote Desktop dan Remote Restart sebagai manual controlled action.

Dokumen ini dibuat agar Codex memiliki panduan yang jelas saat membangun tampilan aplikasi menggunakan Laravel Blade dan Tailwind CSS.

---

## 2. Masalah UI yang Harus Dihindari

Program sebelumnya menampilkan data yang terlalu mentah dan tidak fokus pada kebutuhan IT admin. UI seperti itu harus dihindari.

Masalah yang tidak boleh terulang:

| Masalah | Dampak |
|---|---|
| Menampilkan raw Windows Event Log sebagai fokus utama | Dashboard terasa random dan tidak menjawab masalah operasional |
| Tabel terlalu banyak tanpa prioritas | Admin bingung harus melihat bagian mana dulu |
| Alert generik tanpa target | Alert tidak actionable |
| Card dashboard tidak menjawab kondisi real device | Dashboard terlihat seperti template, bukan monitoring tool |
| Warna severity tidak konsisten | Sulit membaca status normal/warning/critical |
| Detail device minim konteks | Admin tidak bisa mengambil tindakan |
| UI terlalu ramai dengan grafik | Informasi utama tenggelam |
| Data teknis seperti hash/source_file muncul di dashboard utama | Membuat tampilan berat dan tidak relevan |

Prinsip utama:

```text
Dashboard utama bukan tempat menampilkan semua log.
Dashboard utama adalah cockpit untuk menjawab kondisi operasional secara cepat.
```

---

## 3. Identitas Produk

| Item | Nilai |
|---|---|
| Nama Aplikasi | Centralized Log Monitoring Dashboard |
| Jenis Aplikasi | Internal IT Monitoring Dashboard |
| Pengguna Utama | Administrator IT |
| Target Lingkungan | Real-device small office environment |
| Gaya Visual | Professional, compact, serious, status-first |
| Karakter Aplikasi | Monitoring, alerting, audit, remote administration |

Aplikasi ini bukan aplikasi publik, bukan landing page, bukan SaaS marketing dashboard, dan bukan dashboard dekoratif. Aplikasi ini adalah alat kerja IT admin.

---

## 4. Stack UI Final

Stack UI yang wajib digunakan:

| Layer | Teknologi | Fungsi |
|---|---|---|
| Template | Laravel Blade | Rendering halaman server-side |
| Styling | Tailwind CSS | Styling utama dan utility class |
| Interaction | Alpine.js | Dropdown, modal, sidebar, toggle, confirmation dialog |
| Chart | Chart.js | Grafik ringkas jika diperlukan |
| Icons | Heroicons atau Lucide Icons | Icon status, navigation, action |
| Layout | Custom dashboard layout | Sidebar, topbar, content area |

### 4.1 Larangan Stack UI

Codex tidak boleh menggunakan:

```text
React
Vue
Next.js
Nuxt
Angular
Bootstrap sebagai framework utama
AdminLTE template mentah
Grafana
Metabase
Prometheus UI
ELK/Kibana UI
```

Catatan:

- AdminLTE hanya boleh menjadi referensi pola dashboard: sidebar, topbar, card, table.
- Semua UI harus tetap custom menggunakan Tailwind CSS.
- Chart.js hanya dipakai secukupnya, bukan mendominasi dashboard.

---

## 5. Design Personality

Tampilan harus terasa seperti:

```text
IT operations cockpit
internal enterprise tool
monitoring dashboard
technical but readable
compact but clean
serious but not intimidating
```

Tampilan tidak boleh terasa seperti:

```text
startup landing page
game UI
social media dashboard
template admin generik
AI-generated purple SaaS dashboard
random log viewer
```

---

## 6. Design Principles

| Prinsip | Penjelasan |
|---|---|
| Status-first | Informasi status harus langsung terbaca: online, warning, critical, offline |
| Target-specific | Semua alert dan action harus jelas target device/server-nya |
| Evidence-based | Alert, incident, dan detail harus menampilkan bukti pengukuran |
| Dashboard-first | Halaman utama menampilkan ringkasan operasional, bukan raw log |
| Actionable | UI harus membantu admin mengambil tindakan: remote, restart, acknowledge, resolve |
| Compact | Informasi teknis cukup banyak, tetapi spacing harus tetap rapi |
| Consistent | Badge, warna, icon, table, card, dan tombol harus konsisten |
| Traceable | Data penting harus bisa ditelusuri dari dashboard ke detail dan log |
| Safe by design | Action seperti restart harus memakai modal konfirmasi dan audit log |
| No noise | Raw log teknis hanya di Advanced Logs, bukan dashboard utama |

---

## 7. Information Architecture

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

### 7.1 Fungsi Tiap Menu

| Menu | Tujuan |
|---|---|
| Dashboard | Ringkasan kondisi device, Firebird, Accurate, audit, incident |
| Devices | Daftar laptop Windows yang dimonitor |
| Device Detail | Detail satu device dan action remote |
| Accurate Audit | Audit trail aktivitas/perubahan data Accurate |
| Incidents | Korelasi masalah operasional seperti device lambat atau Firebird bermasalah |
| Alerts | Peringatan kontekstual yang punya target, evidence, dan action |
| Remote Actions | Riwayat tindakan admin seperti RDP dan restart |
| Advanced Logs | Raw/parsed logs untuk investigasi teknis |
| Settings | Threshold, Telegram, ZeroTier, Firebird, Agent, Remote Action |

---

## 8. Layout Global

Aplikasi menggunakan layout dashboard dengan:

1. Sidebar kiri.
2. Topbar atas.
3. Main content area.
4. Page header.
5. Card/table/detail panel sesuai halaman.

Struktur visual:

```text
+--------------------------------------------------------------------------------+
| Topbar: Centralized Log Monitoring Dashboard        Alert: 3      Admin         |
+----------------------+---------------------------------------------------------+
| Sidebar              | Page Header                                             |
|                      | Title + description                                     |
| Dashboard            |                                                         |
| Devices              | Main Content                                            |
| Accurate Audit       | Cards / Tables / Detail Panels / Actions                |
| Incidents            |                                                         |
| Alerts               |                                                         |
| Remote Actions       |                                                         |
| Advanced Logs        |                                                         |
| Settings             |                                                         |
+----------------------+---------------------------------------------------------+
```

### 8.1 Sidebar Rules

Sidebar harus:

- Berada di kiri pada desktop.
- Memiliki width tetap sekitar `w-64`.
- Memiliki background gelap netral.
- Menampilkan menu aktif dengan highlight jelas.
- Menggunakan icon sederhana.
- Tidak menggunakan emoji sebagai icon utama.

Contoh urutan sidebar:

```text
Centralized Monitor
───────────────────
Dashboard
Devices
Accurate Audit
Incidents
Alerts
Remote Actions
Advanced Logs
Settings
───────────────────
Logout
```

### 8.2 Topbar Rules

Topbar harus menampilkan:

| Elemen | Keterangan |
|---|---|
| Nama sistem | Centralized Log Monitoring Dashboard |
| Last refresh | Waktu terakhir data diperbarui |
| Active alerts badge | Jumlah alert open |
| Admin dropdown | Nama admin dan logout |
| Mobile sidebar toggle | Untuk layar kecil |

Topbar tidak boleh penuh dengan tombol yang jarang dipakai.

### 8.3 Page Header Rules

Setiap halaman wajib punya page header:

```text
Judul Halaman
Deskripsi singkat yang menjelaskan fungsi halaman.
```

Contoh:

```text
Devices
Daftar laptop Windows pengguna Accurate 5 yang terhubung ke sistem monitoring.
```

---

## 9. Visual Tokens

### 9.1 Color Philosophy

Gunakan warna netral sebagai dasar dan warna status hanya untuk informasi penting.

| Fungsi | Warna Konsep |
|---|---|
| Background utama | Light neutral / slate |
| Sidebar | Dark slate/navy |
| Card background | White |
| Border | Soft gray |
| Text utama | Dark slate |
| Text sekunder | Muted gray |
| Primary action | Blue / indigo |
| Success/normal | Green |
| Warning | Amber/yellow |
| Error | Orange/red |
| Critical | Red |
| Offline/disabled | Gray |

### 9.2 Tailwind Token Rekomendasi

| Token | Tailwind Example |
|---|---|
| Page background | `bg-slate-50` |
| Sidebar background | `bg-slate-900` |
| Sidebar text | `text-slate-300` |
| Sidebar active | `bg-slate-800 text-white` |
| Card background | `bg-white` |
| Card border | `border border-slate-200` |
| Card shadow | `shadow-sm` |
| Heading | `text-slate-900` |
| Body text | `text-slate-700` |
| Muted text | `text-slate-500` |

Catatan:

- Codex boleh menyesuaikan shade, tetapi harus tetap konsisten.
- Jangan membuat terlalu banyak warna baru.
- Jangan menggunakan gradient mencolok untuk dashboard monitoring.

---

## 10. Status and Severity System

Status dan severity harus konsisten di seluruh aplikasi.

### 10.1 Operational Status

| Status | Meaning | UI Badge |
|---|---|---|
| Online | Device/agent/service aktif dan normal | Green |
| Warning | Ada indikasi masalah ringan | Amber |
| Error | Masalah nyata tetapi belum fatal | Orange/Red |
| Critical | Gangguan serius membutuhkan tindakan segera | Red |
| Offline | Tidak terhubung / tidak ada heartbeat | Gray |
| Unknown | Belum ada data valid | Slate/Gray |

### 10.2 Severity

| Severity | Makna | Contoh |
|---|---|---|
| INFO | Informasi normal | Heartbeat diterima |
| WARNING | Perlu perhatian | CPU tinggi, latency tinggi |
| ERROR | Gangguan nyata | Firebird timeout dari satu client |
| CRITICAL | Gangguan serius | Firebird service di VPS mati |

### 10.3 Badge Style Guidelines

Badge harus:

- Berbentuk pill kecil.
- Menggunakan uppercase label.
- Memiliki warna background lembut dan text gelap.
- Konsisten di semua halaman.

Contoh Tailwind:

```html
<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-700">
  ONLINE
</span>
```

### 10.4 Badge Labels

Gunakan label berikut:

```text
ONLINE
WARNING
ERROR
CRITICAL
OFFLINE
UNKNOWN
OPEN
ACKNOWLEDGED
RESOLVED
PENDING
EXECUTED
FAILED
```

Jangan mencampur bahasa label status secara random. Untuk UI teknis, label status boleh memakai bahasa Inggris, sedangkan deskripsi boleh memakai bahasa Indonesia.

---

## 11. Typography Rules

Typography harus sederhana dan mudah dibaca.

### 11.1 Font

Gunakan font default modern:

```text
Inter
system-ui
sans-serif
```

Jika tidak ada Inter, gunakan default Tailwind/system font.

### 11.2 Hierarchy

| Elemen | Ukuran Rekomendasi |
|---|---|
| Page title | `text-2xl font-semibold` |
| Section title | `text-lg font-semibold` |
| Card title | `text-sm font-medium` |
| Main value | `text-2xl font-semibold` |
| Body text | `text-sm` |
| Table text | `text-sm` |
| Muted/help text | `text-xs text-slate-500` |

### 11.3 Copywriting Style

Gunakan bahasa Indonesia yang jelas.

Contoh baik:

```text
WIN-ACC-02 tidak mengirim heartbeat selama 12 menit.
```

Contoh buruk:

```text
Heartbeat missed critical issue detected.
```

Gunakan kalimat yang menjawab:

```text
Apa yang terjadi?
Di device/server mana?
Buktinya apa?
Admin sebaiknya melakukan apa?
```

---

## 12. Spacing and Density

Dashboard harus compact, tetapi tidak sesak.

### 12.1 Page Spacing

| Area | Tailwind Example |
|---|---|
| Main content padding | `p-6` |
| Section gap | `space-y-6` |
| Card padding | `p-5` |
| Table cell padding | `px-4 py-3` |
| Form field gap | `gap-4` |

### 12.2 Density Rule

Karena aplikasi monitoring menampilkan banyak data:

- Card boleh compact.
- Table harus readable.
- Jangan memberi padding terlalu besar seperti landing page.
- Jangan menampilkan terlalu banyak whitespace kosong di dashboard.

---

## 13. Component Standards

## 13.1 Status Card

Dipakai di Dashboard untuk summary penting.

Struktur:

```text
+------------------------------------------------+
| Icon / Label              Status Badge          |
| Main Value                                      |
| Description / trend                            |
+------------------------------------------------+
```

Field:

| Field | Keterangan |
|---|---|
| title | Judul card |
| value | Angka/status utama |
| status | normal/warning/error/critical |
| description | Penjelasan singkat |
| icon | Icon opsional |
| link | Link ke halaman detail |

Contoh card:

```text
Device Online
2 / 2
Semua Windows Agent mengirim heartbeat.
```

```text
Firebird Connectivity
1 Warning
WIN-ACC-02 latency tinggi ke VPS:3051.
```

## 13.2 Device Health Row

Dipakai pada dashboard dan devices page.

Kolom minimal:

```text
Device Label
Hostname
Windows User
IP ZeroTier
Agent Status
CPU
RAM
Firebird
Accurate
Last Seen
Actions
```

Action minimal:

```text
Detail
Remote Desktop
Restart
```

Row harus menonjolkan status device dan action, bukan raw log.

## 13.3 Alert Card / Alert Row

Alert wajib menampilkan:

```text
Severity
Title
Target
Detected By
Evidence
Impact
Recommended Action
Status
Time
```

Contoh:

```text
WARNING - CPU tinggi pada Laptop Finance 2
Target: Laptop Finance 2 / DESKTOP-XYZ
Evidence: CPU 87% selama 5 menit
Action: Remote Desktop untuk cek proses berjalan
```

Jangan tampilkan alert tanpa target.

## 13.4 Incident Card / Incident Row

Incident adalah hasil korelasi beberapa event/alert.

Kolom minimal:

```text
Incident Type
Target Device/Server
Severity
Summary
Evidence Count
Status
Detected At
Action
```

Detail incident harus menampilkan evidence.

## 13.5 Data Table

Tabel wajib memiliki:

- Header jelas.
- Filter jika datanya banyak.
- Pagination.
- Empty state.
- Badge status/severity.
- Tombol detail.
- Kolom action di kanan.

Tabel tidak boleh:

- Menampilkan semua field database mentah.
- Menampilkan hash di halaman utama.
- Menampilkan source_file di dashboard utama.
- Terlalu banyak kolom sampai tidak terbaca.

## 13.6 Filter Panel

Filter panel digunakan di:

```text
Devices
Accurate Audit
Incidents
Alerts
Remote Actions
Advanced Logs
```

Style:

- Gunakan card putih.
- Label form jelas.
- Tombol Apply dan Reset.
- Jangan terlalu tinggi.

## 13.7 Detail Panel

Detail page harus menggunakan layout 2 kolom jika layar cukup besar:

```text
Left / Main:
- Metadata utama
- Evidence
- Timeline

Right / Side:
- Status
- Actions
- Related items
```

---

## 14. Button System

### 14.1 Button Types

| Type | Fungsi | Style |
|---|---|---|
| Primary | Aksi utama seperti Save, Apply | Solid blue |
| Secondary | Aksi biasa seperti Detail, Back | Light/outline |
| Danger | Restart, destructive action | Red |
| Success | Resolve, confirm normal action | Green |
| Ghost | Action ringan | Transparent |

### 14.2 Remote Action Buttons

Tombol remote action harus konsisten:

```text
Remote Desktop
Restart Client
Ping Test
View Logs
```

Aturan:

- Tombol Remote Desktop boleh aktif jika device memiliki IP ZeroTier dan RDP status available/unknown.
- Tombol Restart Client harus membuka modal konfirmasi.
- Restart tidak boleh langsung jalan tanpa confirmation.
- Restart wajib meminta alasan admin.
- Jika device offline, tombol restart disabled atau diberi warning.

### 14.3 Confirmation Modal for Restart

Modal restart wajib menampilkan:

```text
Target device
Hostname
Windows user terakhir
IP ZeroTier
Risiko restart
Input alasan
Checkbox konfirmasi
Button Cancel
Button Confirm Restart
```

Contoh copy:

```text
Anda akan mengirim perintah restart ke Laptop Finance 2.
Tindakan ini dapat menutup aplikasi yang sedang berjalan pada device tersebut.
Masukkan alasan tindakan untuk audit log.
```

---

## 15. Page-Level Design Requirements

## 15.1 Login Page

Tujuan: autentikasi admin.

Style:

- Bersih dan sederhana.
- Tidak memakai ilustrasi berlebihan.
- Menampilkan nama sistem.
- Menampilkan konteks PT XYZ bila diperlukan.

Elemen:

```text
App name
Email input
Password input
Login button
Error message
```

## 15.2 Dashboard Page

Dashboard adalah halaman paling penting.

Wajib menampilkan:

```text
Summary cards
Device health table
Accurate audit latest activities
Recent incidents
Recent alerts
```

Dashboard tidak boleh menampilkan raw logs panjang.

Urutan prioritas visual:

1. Kondisi device dan server.
2. Koneksi Firebird.
3. Accurate process dan audit activity.
4. Incident/alert yang perlu tindakan.
5. Grafik ringkas bila masih ada ruang.

Contoh sections:

```text
Overview
Device Health
Accurate Audit Realtime
Recent Incidents
Recent Alerts
```

## 15.3 Devices Page

Tujuan: daftar device Windows real.

Wajib menampilkan:

```text
Device label
Hostname
Agent ID partial
Windows user
IP ZeroTier
Agent status
RDP status
Accurate status
Firebird status
CPU/RAM/Disk
Last seen
Actions
```

Device label bisa diedit oleh admin.

Hostname tidak boleh dianggap sebagai identitas utama yang permanen.

## 15.4 Device Detail Page

Tujuan: investigasi dan action untuk satu device.

Sections:

```text
Device Identity
Current Status
Performance
Network & Firebird
Accurate Process
Recent Alerts
Recent Telemetry
Remote Actions
Advanced Logs for this Device
```

Action panel harus mudah ditemukan.

## 15.5 Accurate Audit Page

Tujuan: menampilkan audit trail Accurate dari Firebird.

Wajib menampilkan:

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

Filter:

```text
Date range
Accurate user
Source/module
Transaction type
Keyword
```

Catatan UI:

- Jangan klaim data IP/komputer wajib ada dari AUDIT.
- Jika COMP_NAME/IPADDRESS kosong, tampilkan `-` atau `Tidak tersedia`.
- Jangan tampilkan tabel LOGIN sebagai sumber data.

## 15.6 Incidents Page

Tujuan: hasil korelasi masalah operasional.

Wajib menampilkan:

```text
Incident title
Target
Severity
Summary
Evidence count
Status
Detected at
Actions
```

Incident detail menampilkan evidence list.

## 15.7 Alerts Page

Tujuan: daftar peringatan kontekstual.

Wajib menampilkan:

```text
Time
Severity
Title
Target
Detected by
Evidence summary
Status
Notification status
Action
```

Alert tanpa target tidak boleh dibuat atau ditampilkan.

## 15.8 Remote Actions Page

Tujuan: audit tindakan admin.

Wajib menampilkan:

```text
Requested at
Admin
Target device
Action type
Status
Reason
Executed at
Result message
```

Remote action detail menampilkan:

```text
Request metadata
Agent response
Execution result
Error if any
```

## 15.9 Advanced Logs Page

Tujuan: investigasi teknis.

Wajib menampilkan:

```text
Logged at
Hostname
Source/tag
Category
Severity
Parsed message
Raw message preview
Action detail
```

Detail log boleh menampilkan:

```text
Raw message
Parsed fields
Source file
Hash
Parser run
```

Catatan:

- Hash dan source_file tidak muncul di dashboard utama.
- Advanced Logs bukan menu utama yang ditonjolkan di dashboard.

## 15.10 Settings Page

Settings dibagi menjadi tab/section:

```text
General
Device Monitoring
Performance Threshold
Network & Firebird
Accurate Audit
Telegram
Remote Actions
Agent
```

Field sensitif seperti token Telegram dan credential Firebird harus disamarkan.

---

## 16. Chart Guidelines

Chart.js boleh digunakan, tetapi tidak boleh mendominasi UI.

Chart yang disarankan:

| Chart | Halaman | Tujuan |
|---|---|---|
| Device status summary | Dashboard | Online/warning/offline |
| Alert trend | Dashboard/Alerts | Jumlah alert per waktu |
| CPU trend | Device Detail | Grafik CPU device tertentu |
| RAM trend | Device Detail | Grafik RAM device tertentu |
| Audit event count | Accurate Audit | Jumlah audit per hari/user |

Chart yang tidak disarankan:

```text
Grafik terlalu banyak di dashboard
3D chart
Chart warna-warni tanpa makna
Doughnut chart untuk semua hal
Chart yang tidak actionable
```

---

## 17. Empty State Guidelines

Setiap halaman harus punya empty state yang membantu.

Contoh:

### Devices Empty

```text
Belum ada device terdaftar.
Pastikan Windows Agent sudah dijalankan pada laptop client dan dapat mengirim heartbeat ke RSyslog server.
```

### Accurate Audit Empty

```text
Belum ada data audit Accurate.
Pastikan koneksi Firebird read-only sudah dikonfigurasi dan command sync audit berjalan.
```

### Alerts Empty

```text
Belum ada alert aktif.
Sistem akan menampilkan alert jika ditemukan kondisi warning, error, atau critical.
```

### Advanced Logs Empty

```text
Belum ada log yang diterima.
Pastikan Windows Agent dapat mengirim syslog ke VPS melalui jaringan ZeroTier.
```

---

## 18. Loading and Refresh Behavior

Karena sistem monitoring bersifat near-real-time:

- Dashboard boleh auto-refresh ringan setiap 30–60 detik.
- Tampilkan last refresh time di topbar.
- Hindari full page reload jika memungkinkan, tetapi tidak wajib memakai SPA.
- Alpine.js cukup untuk interaksi ringan.

Contoh:

```text
Last refresh: 2026-05-28 20:15:10
```

Jika data stale:

```text
Data terakhir diterima 12 menit lalu.
```

---

## 19. Responsive Rules

Target utama adalah laptop/desktop, tetapi UI tetap harus responsive.

Desktop:

- Sidebar fixed.
- Table penuh.
- Cards grid 3–5 kolom.
- Detail page 2 kolom.

Tablet/mobile:

- Sidebar collapsible.
- Cards menjadi 1 kolom.
- Tabel bisa horizontal scroll.
- Action button boleh masuk dropdown.

---

## 20. Accessibility and Readability

UI harus:

- Memiliki contrast yang cukup.
- Tidak hanya mengandalkan warna untuk severity; gunakan label teks.
- Tombol destructive harus jelas.
- Modal confirmation harus bisa dibaca jelas.
- Font minimal `text-sm` untuk tabel.

---

## 21. Hallmark-Inspired Design Guidance

Hallmark dapat digunakan sebagai inspirasi disiplin desain untuk mencegah UI terlihat seperti template AI generik.

Dalam konteks project ini, prinsip Hallmark yang perlu diterjemahkan ke Codex:

```text
Lock the design DNA.
Use consistent visual grammar.
Prioritize hierarchy before decoration.
Make components feel intentional.
Avoid generic AI dashboard look.
```

### 21.1 Design DNA Project Ini

```text
Serious internal IT tool.
Status-oriented.
Compact data table.
Clear evidence and action.
No decorative clutter.
No random raw log focus.
```

### 21.2 Hallmark/Codex Prompt Snippet

Gunakan instruksi berikut saat meminta Codex membangun UI:

```text
Apply the project UI design system.
The interface must feel like an internal IT operations cockpit, not a generic SaaS dashboard.
Use Laravel Blade and Tailwind CSS only.
Use compact status cards, clear tables, consistent severity badges, and evidence-based alert presentation.
Do not show raw logs on the main dashboard.
Do not use decorative gradients, oversized hero sections, or random chart-heavy layouts.
```

---

## 22. File and Component Structure

Struktur Blade yang disarankan:

```text
resources/views/
├── layouts/
│   └── app.blade.php
├── auth/
│   └── login.blade.php
├── dashboard/
│   └── index.blade.php
├── devices/
│   ├── index.blade.php
│   └── show.blade.php
├── accurate-audits/
│   ├── index.blade.php
│   └── show.blade.php
├── incidents/
│   ├── index.blade.php
│   └── show.blade.php
├── alerts/
│   ├── index.blade.php
│   └── show.blade.php
├── remote-actions/
│   ├── index.blade.php
│   └── show.blade.php
├── logs/
│   ├── index.blade.php
│   └── show.blade.php
├── settings/
│   └── index.blade.php
└── components/
    ├── sidebar.blade.php
    ├── topbar.blade.php
    ├── page-header.blade.php
    ├── status-card.blade.php
    ├── status-badge.blade.php
    ├── severity-badge.blade.php
    ├── data-table.blade.php
    ├── filter-panel.blade.php
    ├── empty-state.blade.php
    ├── confirmation-modal.blade.php
    └── action-button.blade.php
```

---

## 23. Route Naming Guidance

Route UI yang disarankan:

| Page | URL | Route Name |
|---|---|---|
| Login | `/login` | `login` |
| Dashboard | `/dashboard` | `dashboard.index` |
| Devices | `/devices` | `devices.index` |
| Device Detail | `/devices/{device}` | `devices.show` |
| Accurate Audit | `/accurate-audits` | `accurate-audits.index` |
| Accurate Audit Detail | `/accurate-audits/{event}` | `accurate-audits.show` |
| Incidents | `/incidents` | `incidents.index` |
| Incident Detail | `/incidents/{incident}` | `incidents.show` |
| Alerts | `/alerts` | `alerts.index` |
| Alert Detail | `/alerts/{alert}` | `alerts.show` |
| Remote Actions | `/remote-actions` | `remote-actions.index` |
| Remote Action Detail | `/remote-actions/{remoteAction}` | `remote-actions.show` |
| Advanced Logs | `/logs` | `logs.index` |
| Log Detail | `/logs/{log}` | `logs.show` |
| Settings | `/settings` | `settings.index` |

---

## 24. Content Rules for Alerts and Incidents

Alert title format:

```text
[Masalah] pada [Target]
```

Contoh:

```text
CPU tinggi pada Laptop Finance 2
WIN-ACC-01 gagal terhubung ke Firebird VPS:3051
Service Firebird pada VPS tidak aktif
Accurate 5 tidak berjalan pada Laptop Finance 1
```

Alert detail wajib menjawab:

```text
Target apa?
Sumber deteksi apa?
Buktinya apa?
Dampaknya apa?
Saran tindakan apa?
```

Jangan gunakan alert title seperti:

```text
Firebird unreachable
Process not running
Audit spike detected
System warning
```

---

## 25. Dashboard Content Priority

Urutan tampilan dashboard yang disarankan:

1. Summary cards.
2. Device health table.
3. Recent Accurate Audit activity.
4. Recent incidents.
5. Recent alerts.
6. Small trend charts.

Dashboard harus menjawab pertanyaan berikut dalam 10 detik:

```text
Berapa device online?
Device mana bermasalah?
User Windows siapa yang sedang aktif?
Client bisa konek ke Firebird?
Accurate sedang berjalan?
Ada audit aktivitas terbaru?
Ada incident/alert yang butuh tindakan?
```

---

## 26. Security UX Rules

Untuk fitur sensitif:

### 26.1 Remote Restart

Wajib:

```text
Modal konfirmasi
Input alasan
Checkbox persetujuan
Audit log remote action
Status eksekusi
```

Tidak boleh:

```text
Restart langsung dari tombol tanpa konfirmasi
Auto restart karena alert
Restart tanpa mencatat admin dan alasan
```

### 26.2 Telegram Token

Di Settings:

- Token disamarkan.
- Ada tombol test notification.
- Jangan tampilkan full token setelah tersimpan.

### 26.3 Firebird Credential

Di Settings:

- Password disamarkan.
- Jangan tampilkan credential penuh.
- Tampilkan status koneksi saja.

---

## 27. Codex Implementation Rules

Saat Codex membangun UI, wajib mematuhi aturan berikut:

```text
1. Gunakan Laravel Blade + Tailwind CSS.
2. Jangan gunakan React/Vue/Next.
3. Jangan membuat dashboard utama sebagai raw log viewer.
4. Buat UI status-first dan target-specific.
5. Semua alert harus punya target dan evidence.
6. Semua remote restart harus memakai modal konfirmasi.
7. Raw message, hash, source_file hanya tampil di Advanced Logs/detail.
8. Device name tidak boleh hardcode.
9. Device label berasal dari database dan bisa diedit admin.
10. Gunakan component reusable untuk badge, card, table, modal.
11. Jaga UI compact, clean, dan professional.
12. Jangan gunakan gradient/hero berlebihan.
13. Jangan tampilkan data yang belum ada sumbernya.
14. Jika data belum tersedia, tampilkan empty state yang informatif.
```

---

## 28. Acceptance Criteria UI Design System

UI dianggap sesuai jika memenuhi:

| ID | Criteria |
|---|---|
| UI-AC-001 | Aplikasi memakai Laravel Blade dan Tailwind CSS |
| UI-AC-002 | Sidebar memiliki menu final sesuai dokumen |
| UI-AC-003 | Dashboard tidak menampilkan raw log sebagai konten dominan |
| UI-AC-004 | Dashboard menampilkan device status, Firebird connectivity, Accurate audit, incident, alert |
| UI-AC-005 | Semua badge status dan severity konsisten |
| UI-AC-006 | Devices page menampilkan device real tanpa hardcode |
| UI-AC-007 | Device detail menyediakan Remote Desktop dan Restart Client |
| UI-AC-008 | Restart Client memakai modal konfirmasi dan alasan |
| UI-AC-009 | Accurate Audit page menampilkan field audit sesuai POC |
| UI-AC-010 | Alerts page menampilkan target, evidence, impact, recommended action |
| UI-AC-011 | Advanced Logs menjadi tempat raw log dan detail teknis |
| UI-AC-012 | Empty state tersedia pada halaman tanpa data |
| UI-AC-013 | UI tetap readable pada laptop/desktop |
| UI-AC-014 | Tidak ada React/Vue/SPA framework tambahan |
| UI-AC-015 | Tampilan terasa seperti IT operations cockpit, bukan template dashboard generik |

---

## 29. Next Document Dependency

Dokumen ini menjadi dasar untuk dokumen berikutnya:

```text
09_UI_Wireframe_v2.md
```

Dokumen wireframe harus mengikuti design system ini. Jika ada konflik antara wireframe dan design system, prioritaskan design system untuk prinsip visual dan UX, lalu sesuaikan wireframe.

---

## 30. Ringkasan Final

Design system v2 mengunci bahwa UI **Centralized Log Monitoring Dashboard** harus menjadi cockpit operasional untuk administrator IT, bukan sekadar tabel raw log.

Fokus UI:

```text
Device status
Windows user
Firebird connectivity
Performance
Accurate process
Accurate audit trail
Contextual alerts
Incidents
Remote actions
Advanced logs only for technical investigation
```

Fokus desain:

```text
Clean
Professional
Compact
Status-first
Evidence-based
Actionable
Safe
Consistent
```

Dengan design system ini, Codex diharapkan membangun UI yang sesuai kebutuhan skripsi dan tidak kembali ke pola dashboard random log seperti versi sebelumnya.
