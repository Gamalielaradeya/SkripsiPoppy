# CHANGELOG — Sesion 27 Juni 2026

## Ringkasan

Sesi ini fokus pada polish UI, membuat halaman Settings fungsional (membaca dan menulis .env secara langsung), menambahkan gate untuk Agent API dan Remote Restart, serta memperbaiki bug permission dan config cache di production.

---

## Commit 1: `553097b` — UI polish (settings, filter, tombol, label)

### Halaman Settings
- Controller dibaca `config('monitoring.xxx')` bukan `env()` langsung — mencegah stale config cache
- Semua setting bisa diedit langsung dari UI, disimpan ke `.env`
- Boolean fields jadi dropdown **Aktif / Nonaktif** (bukan text input)
- Sensitive fields (token, password) pakai input type=password + tombol mata show/hide
- Label pakai bahasa Indonesia proper (bukan raw database key)
- Zona waktu hardcode "Asia/Jakarta"
- Tambah 3 key baru di `config/monitoring.php`: `remote_action.agent_api_enabled`, `remote_action.restart_enabled`, `remote_action.restart_require_reason`

### Dashboard
- Tambah 2 summary cards: **Audit Today** dan **Open Incidents** (sebelumnya cuma 4 card)
- Grid berubah dari `xl:grid-cols-4` jadi `xl:grid-cols-6`

### Device List
- Tombol RDP conditional: hanya active kalau device online + ada IP (ZeroTier atau local)
- Tombol Restart conditional: hanya active kalau device online
- Offline device: tombol disabled + pesan warning

### Device Detail
- `agent_id` di-mask (10 karakter pertama + `...`) — sebelumnya full
- Restart dialog pakai reusable `<x-confirm-modal>` component
- Tombol Restart conditional: offline → disabled + warning

### Filter Panels
- Filter di Devices, Alerts, Incidents yang sebelumnya semua `disabled` sekarang aktif
- Controller support query params (search, status, firebird, accurate, severity, keyword, target)
- Tiap filter panel ada tombol Apply + Reset

### UI Components
- Topbar mobile toggle: teks "Menu" diganti SVG hamburger icon (3 garis)
- `<x-confirm-modal>` component di-rewrite: support `formAction`, `formMethod`, `triggerVariant`
- Alert & incident controller tambah backend filter logic

### Files
- `app/Http/Controllers/SettingController.php` — rewrite
- `app/Http/Controllers/DeviceController.php` — tambah filter
- `app/Http/Controllers/AlertController.php` — tambah filter
- `app/Http/Controllers/IncidentController.php` — tambah filter
- `config/monitoring.php` — tambah 3 key remote_action
- `resources/views/settings/index.blade.php` — rewrite
- `resources/views/dashboard/index.blade.php` — +2 cards
- `resources/views/devices/index.blade.php` — filter + conditional buttons
- `resources/views/devices/show.blade.php` — mask agent_id + confirm-modal
- `resources/views/alerts/index.blade.php` — live filter
- `resources/views/incidents/index.blade.php` — live filter
- `resources/views/components/confirm-modal.blade.php` — rewrite
- `resources/views/components/topbar.blade.php` — hamburger icon
- `database/seeders/SystemSettingSeeder.php` — rewrite
- `routes/web.php` — tambah PUT settings routes

---

## Commit 2: `13a1d29` — Fix config read

### Problem
Config cache (`bootstrap/cache/config.php`) dari 26 June bikin `env()` return null di semua call.

### Fix
- SettingController: semua diubah dari `env()` ke `config('monitoring.xxx')`
- `config:clear` dijalankan untuk hapus stale cache
- Setelah save, `config:cache` di-rebuild dengan nilai baru

---

## Commit 3: `d4524b8` — Gate agent API dan remote restart

### Agent API Gate
- `AGENT_API_ENABLED=false` → semua endpoint `/api/agent/*` return 503
- `AGENT_API_ENABLED=true` → endpoint normal

### Remote Restart Gate
- `REMOTE_RESTART_ENABLED=false` → `RemoteActionController@storeRestart` reject dengan flash message
- Tombol Restart di device list dan device detail hidden/disabled
- RDP tidak terpengaruh (tetap jalan normal)

---

## Commit 4: `52b804a` — Backup .env ke git

- `.env.snapshot` disimpan di git sebagai salinan backup
- `.env` asli tetap di `.gitignore` (tidak masuk git)
- Stale compiled view cache ikut terhapus

---

## Commit 5: `677e6da` — Fix 500 pada Agent API

### Problem
Middleware gate (`EnsureAgentApiEnabled`) menyebabkan 500 error di nginx+php-fpm dengan `config:cache`. Artisan serve jalan normal.

### Root Cause
- Middleware return type `: JsonResponse` terlalu strict — tidak kompatibel dengan Laravel pipeline saat config di-cache
- `.env` dan `bootstrap/cache/*` permission: owner `root:root`, PHP-FPM jalan sebagai `www-data` → tidak bisa menulis

### Fix
- Gate dipindah dari middleware ke controller method `gateCheck()` — langsung di `AgentApiController`
- Middleware file dihapus, bootstrap/app.php dibersihkan
- `chown www-data:www-data .env bootstrap/cache/config.php`
- Settings save pakai `config:clear` (bukan `config:cache`) supaya nilai baru langsung terbaca

---

## Catatan Penting

### Arsitektur Settings
- **`.env` = source of truth**, dibaca `config/monitoring.php`
- Settings page baca dari `config('monitoring.xxx')` dan tulis balik ke `.env`
- `threshold_settings` DB tetap dipakai untuk threshold (tidak masuk .env)
- `system_settings` DB sudah tidak dipakai runtime (hanya untuk kompatibilitas)

### Permission Production
- `.env`: owner `www-data:www-data`, mode `664`
- `bootstrap/cache/*`: owner `www-data:www-data`, mode `664`
- `storage/`: owner `www-data:www-data`, mode `775`

### Nilai .env saat ini
- `AGENT_API_ENABLED=true`
- `REMOTE_RESTART_ENABLED=true`
- `REMOTE_RESTART_REQUIRE_REASON=true`
- `TELEGRAM_ALERT_ENABLED=true`
- `ACCURATE_AUDIT_ENABLED=true`
- `ACCURATE_FIREBIRD_HOST=127.0.0.1`
- `ACCURATE_FIREBIRD_DATABASE=/firebird/data/XYZ.GDB`
- `ACCURATE_FIREBIRD_USERNAME=GUEST`