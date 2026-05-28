# 15 — AGENTS.md and Codex Skills Guide v2
# Centralized Log Monitoring Dashboard

## Versi Dokumen

| Informasi | Keterangan |
|---|---|
| Nama Dokumen | AGENTS.md and Codex Skills Guide |
| Versi | 2.0 |
| Sistem | Centralized Log Monitoring Dashboard |
| Target Implementasi | Real-device monitoring berbasis VPS + ZeroTier + Windows Agent + RSyslog + Laravel + Firebird/Accurate |
| Target Pembaca | Pengembang, AI Agent, Codex, reviewer teknis |
| Tujuan | Memberikan aturan permanen untuk Codex agar implementasi tidak menyimpang dari dokumen v2 |

---

# 1. Tujuan Dokumen

Dokumen ini menjelaskan cara menyiapkan instruksi permanen untuk Codex ketika membangun ulang project **Centralized Log Monitoring Dashboard**.

Dokumen ini dibuat agar Codex tidak mengulang masalah dari program sebelumnya, seperti:

1. Membuat dashboard terlalu generik.
2. Menampilkan raw log Windows random di dashboard utama.
3. Membuat alert tanpa target dan evidence.
4. Menganggap semua data harus masuk lewat RSyslog.
5. Menganggap Accurate Audit Trail berasal dari tabel `LOGIN`.
6. Melakukan hardcode device seperti `WIN-ACC-01` di source code.
7. Mengubah stack menjadi React, Node.js backend, ELK, Grafana, atau Prometheus.
8. Membuat remote restart otomatis tanpa persetujuan admin.

Dokumen ini berisi:

1. Struktur `AGENTS.md` untuk repository.
2. Aturan kerja Codex.
3. Template `AGENTS.md` yang siap dipakai.
4. Rekomendasi struktur Codex Skills.
5. Template skill untuk project monitoring.
6. Template skill untuk UI design system.
7. Template skill untuk Accurate Firebird Audit.
8. Prompt awal untuk Codex.
9. Prompt per milestone.
10. Checklist sebelum Codex mulai coding.

---

# 2. Posisi Dokumen Ini dalam Paket v2

Dokumen ini harus dibaca setelah dokumen berikut:

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
12_Demo_Script_v2.md
13_Codex_Implementation_Brief.md
14_Project_Setup_Checklist_v2.md
```

Dokumen ini bukan pengganti dokumen teknis. Dokumen ini adalah **aturan operasional untuk Codex**.

---

# 3. Prinsip Utama Penggunaan Codex

Codex harus diperlakukan sebagai developer yang diberi spesifikasi, bukan sebagai pihak yang bebas menafsirkan ulang scope.

Prinsip penggunaan:

| Prinsip | Penjelasan |
|---|---|
| Document-first | Codex wajib mengikuti dokumen v2 sebelum menulis kode. |
| Plan-before-code | Untuk task besar, Codex harus membuat rencana dulu sebelum implementasi. |
| Small milestone | Pengerjaan dibagi menjadi milestone kecil. |
| Reviewable diff | Setiap perubahan harus mudah direview lewat Git diff. |
| No hallucination | Jika ada informasi yang tidak ada di dokumen, Codex harus bertanya. |
| No hardcode | Device, credential, path, IP, dan threshold harus dari database/config/env. |
| Real-device first | Implementasi diarahkan ke perangkat nyata, bukan simulasi random. |
| Safe remote action | Restart client harus manual, terkontrol, dan tercatat. |

---

# 4. Fungsi AGENTS.md

`AGENTS.md` adalah file instruksi permanen yang diletakkan di root repository. File ini menjadi pedoman agar Codex selalu memahami aturan project setiap kali membuka repo.

File ini harus menjawab:

1. Project ini apa?
2. Stack yang boleh digunakan apa?
3. Stack yang tidak boleh digunakan apa?
4. Arsitektur wajib seperti apa?
5. Fitur wajib apa saja?
6. Hal apa yang tidak boleh dilakukan?
7. Command penting apa yang harus dipakai?
8. Bagaimana cara menjalankan dan menguji project?
9. Apa definisi selesai untuk sebuah task?

---

# 5. Lokasi File AGENTS.md

Buat file ini di root repository:

```text
centralized-log-monitoring-dashboard/
├── AGENTS.md
├── docs/
├── app/
├── database/
├── resources/
├── routes/
├── docker/
├── windows-agent/
└── .codex/
```

Lokasi wajib:

```text
/AGENTS.md
```

Jika project membesar, boleh tambah `AGENTS.md` spesifik di subfolder:

```text
/windows-agent/AGENTS.md
/resources/views/AGENTS.md
/docs/AGENTS.md
```

Namun untuk awal, cukup satu `AGENTS.md` di root.

---

# 6. Template AGENTS.md Final

Copy isi berikut ke file `AGENTS.md` di root repository.

```md
# AGENTS.md
# Centralized Log Monitoring Dashboard

## Project Summary

This repository contains the implementation of **Centralized Log Monitoring Dashboard**, a real-device IT monitoring system for a small PT XYZ environment.

The system monitors Windows client devices running Accurate 5, sends structured telemetry to a VPS-based RSyslog server, parses the logs in Laravel, stores monitoring data in MySQL/MariaDB, reads Accurate/Firebird audit trail directly from Firebird, displays operational status in a dashboard, sends contextual Telegram alerts, and supports manual controlled remote actions.

This project is for a thesis implementation. Code must be simple, explainable, testable, and aligned with the v2 documentation.

---

## Required Documentation Reading Order

Before implementing any feature, read the relevant docs in this order:

1. `docs/01_PRD_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md`
2. `docs/02_Accurate_Firebird_POC_Findings_v2.md`
3. `docs/03_SRS_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md`
4. `docs/04_Database_Design_v2.md`
5. `docs/05_System_Architecture_v2.md`
6. `docs/06_Windows_Agent_and_RSyslog_Guide_v2.md`
7. `docs/07_Detection_Rules_v2.md`
8. `docs/08_UI_Design_System_v2.md`
9. `docs/09_UI_Wireframe_v2.md`
10. `docs/10_Test_Plan_v2.md`
11. `docs/11_Deployment_Guide_VPS_ZeroTier_v2.md`
12. `docs/12_Demo_Script_v2.md`
13. `docs/13_Codex_Implementation_Brief.md`
14. `docs/14_Project_Setup_Checklist_v2.md`
15. `docs/15_AGENTS_md_and_Codex_Skills_Guide.md`

If a detail is unclear or missing, ask for clarification instead of inventing behavior.

---

## Fixed Product Name

Use this product name consistently:

```text
Centralized Log Monitoring Dashboard
```

Do not rename it to SIEM, SOC Dashboard, Accurate Monitor, Observability Platform, or any other name unless explicitly requested.

---

## Fixed Target Environment

The target environment is real-device, not random simulated data:

```text
2 Windows laptops with Accurate 5 + Windows Agent
1 VPS Linux server
ZeroTier private network
RSyslog Server on VPS
Laravel Dashboard on VPS
MySQL/MariaDB monitoring database on VPS
Firebird 2.5 / Accurate database on VPS
Telegram contextual alert
Remote Desktop launcher
Manual controlled remote restart
```

Simulation may be used only as fallback or development helper, not as the main product direction.

---

## Required Stack

Use this stack:

```text
Backend        : Laravel
Frontend       : Laravel Blade
Styling        : Tailwind CSS
Interaction    : Alpine.js
Charts         : Chart.js
Database       : MySQL or MariaDB
Log Collector  : RSyslog
Client Agent   : Windows Agent using PowerShell or Python
Network        : ZeroTier private network
Audit Source   : Firebird 2.5 Accurate database
Alert          : Telegram Bot API
Deployment     : VPS Linux
Version Control: GitHub
```

---

## Forbidden Stack / Tools

Do not introduce:

```text
React
Vue
Node.js backend
Next.js
Express backend
ELK Stack
Logstash
Kibana
Grafana
Prometheus
Wazuh
Splunk
Enterprise SIEM architecture
Automatic IPS/blocking
Auto remediation without admin approval
Mobile app
Complex multi-role access unless requested
```

Do not replace Laravel Blade with SPA frontend.

---

## Core Architecture

Preserve this architecture:

```text
Windows Agent on client devices
        ↓ structured syslog key=value messages
RSyslog Server on VPS
        ↓ raw log files
Laravel RSyslog Parser
        ↓ parsed monitoring data
MySQL/MariaDB Monitoring DB
        ↓
Laravel Dashboard
        ↓
Telegram Contextual Alert
```

Accurate Audit Trail has a separate source:

```text
Laravel Accurate Audit Reader
        ↓ read-only Firebird connection
Firebird Accurate Database
        ↓ AUDIT + USERS
MySQL/MariaDB Monitoring DB
        ↓
Accurate Audit Dashboard
```

Remote actions use a separate API/agent flow:

```text
Admin clicks action
        ↓
Laravel creates remote action command
        ↓
Windows Agent polls command API
        ↓
Agent executes approved command
        ↓
Agent reports result
        ↓
Remote Actions audit log
```

RSyslog must not be used for remote action execution.

---

## Device Identity Rules

Never hardcode device names.

Use:

```text
Primary identity : agent_id
Secondary        : hostname
Display name     : device_label
Network address  : zerotier_ip / last_ip_address
```

Example names such as `WIN-ACC-01`, `WIN-ACC-02`, and `VPS-FIREBIRD` are documentation examples only.

Do not write logic such as:

```php
if ($hostname === 'WIN-ACC-01') { ... }
```

Use database records and configurable labels.

---

## Accurate Firebird Rules

Accurate Audit Trail must follow the POC document.

Rules:

1. Accurate 5 uses Firebird 2.5 in this project context.
2. Accurate client process is `accurate.exe` on Windows.
3. Firebird server process is `fbserver.exe` or Linux Firebird service on VPS.
4. Firebird port is `3051` for this implementation context unless configured otherwise.
5. Accurate Audit Trail is read directly from Firebird using a read-only user.
6. Use `AUDIT + USERS` as the primary source for Accurate internal user and activity.
7. Do not use `LOGIN` as the primary source because it was empty in the POC.
8. `COMP_NAME` and `IPADDRESS` in `AUDIT` may be empty and must not be required fields.
9. Windows user and active connection data come from Windows Agent, not from Accurate audit table.
10. Do not hardcode Firebird credentials.
11. Do not mutate Accurate database.
12. Use incremental sync state to avoid duplicate audit imports.

If the required Firebird table/column is missing, fail gracefully and show configuration error instead of inventing fields.

---

## Windows Agent Rules

Windows Agent is responsible for reading real Windows host data.

It must collect:

```text
agent_id
hostname
device_label if configured
windows_user
zerotier_ip
agent_version
heartbeat
CPU usage
RAM usage
Disk usage
Uptime
RDP service status
Accurate process status
Firebird connectivity to VPS:3051
Latency / timeout evidence
Remote command polling result
```

Windows Agent must send structured `key=value` syslog messages to RSyslog Server.

Example:

```text
device-monitor: event=device_heartbeat agent_id=... hostname=DESKTOP-ABC windows_user=DESKTOP-ABC\\Finance zerotier_ip=10.147.20.11 status=online rdp_status=available
perf-monitor: event=perf_snapshot agent_id=... hostname=DESKTOP-ABC cpu=42 ram=61 disk=55 uptime_minutes=320
network-monitor: event=firebird_connectivity agent_id=... target=vps-firebird port=3051 status=connected latency_ms=24
accurate-process-monitor: event=accurate_process agent_id=... process=accurate.exe status=running owner=DESKTOP-ABC\\Finance
remote-action-monitor: event=remote_action_result action_id=... status=success message="restart scheduled"
```

Do not use WSL as the primary method for monitoring Windows host state.

---

## Parser Rules

Laravel parser must support structured key=value messages.

The parser must:

1. Read RSyslog raw log files.
2. Parse timestamp, hostname, tag/process, and message.
3. Parse key=value fields from message.
4. Store raw message in Advanced Logs.
5. Update domain tables such as devices, telemetries, network checks, accurate process snapshots, remote action results.
6. Prevent duplication using hash and/or parser offsets.
7. Create contextual alerts only through detection rules.

Do not rely only on vague keyword matching for v2 features.

---

## Alert Rules

Never create vague alerts.

Every alert must contain:

```text
severity
target_type
target_id / target_name
title
description
detected_by
evidence
impact
recommended_action
status
```

Bad alert examples:

```text
Firebird unreachable
Accurate process not running
Audit activity spike detected
CPU high
```

Good alert examples:

```text
ERROR - WIN-ACC-02 gagal terhubung ke Firebird VPS:3051
Target: WIN-ACC-02 → VPS-FIREBIRD
Detected by: Windows Agent
Evidence: tcp_connect timeout 3 times, last latency unavailable
Impact: Accurate client may not access database
Recommended action: Check ZeroTier/VPS Firebird connectivity
```

```text
WARNING - Accurate 5 tidak berjalan pada Laptop Finance 2
Target: Laptop Finance 2
Detected by: Windows Agent
Evidence: accurate.exe not found, windows_user=DESKTOP-XYZ\\Finance
Impact: User may not be using Accurate or application may have crashed
Recommended action: Confirm with user or open Remote Desktop
```

Do not implement `Audit activity spike detected` unless a clear threshold and time-window rule exists in the documentation.

---

## Telegram Rules

Telegram is required and must not be treated as optional-only.

Telegram alerts must be contextual and include:

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

Telegram must respect cooldown / anti-spam rules.

Do not send duplicate alerts repeatedly for the same condition within cooldown window.

---

## Remote Desktop Rules

Remote Desktop is a manual admin action.

Requirements:

1. Remote Desktop button should be available on Device Detail.
2. If device is online/reachable, allow opening/generating RDP target.
3. If device is offline, disable or warn.
4. Record the action in `remote_actions` as `OPEN_RDP`.
5. Do not execute hidden remote actions without showing intent to admin.

Remote Desktop may be implemented as:

```text
mstsc /v:<zerotier_ip>
```

or downloadable `.rdp` file.

---

## Remote Restart Rules

Remote restart is manual and controlled.

Requirements:

1. Admin must click Restart Client.
2. UI must show confirmation modal.
3. Admin must provide a reason.
4. Laravel creates a pending command.
5. Windows Agent polls command API.
6. Windows Agent executes restart only for valid, authorized command.
7. Agent reports result.
8. Laravel updates `remote_actions`.
9. Telegram may notify if configured.

Forbidden:

```text
No automatic restart.
No restart triggered only by high CPU.
No restart without confirmation.
No restart without audit log.
No restart command hardcoded to specific device.
```

---

## UI Rules

UI must follow documents 08 and 09.

Design personality:

```text
IT operations cockpit
serious
clean
status-first
professional
not generic SaaS landing page
not raw log dashboard
```

Required UI stack:

```text
Laravel Blade
Tailwind CSS
Alpine.js
Chart.js
Heroicons or Lucide icons
```

Main menu:

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

Dashboard must focus on:

```text
Device online status
Windows user currently active
Firebird connectivity
Accurate process status
Performance health
Recent Accurate audit activity
Open incidents
Contextual alerts
```

Dashboard must not primarily show raw Windows Event Logs.

Raw logs belong to:

```text
Advanced Logs
```

---

## Database Rules

Use migrations matching `04_Database_Design_v2.md`.

Important entities:

```text
devices
agent_credentials
device_telemetries
network_checks
accurate_process_snapshots
server_service_checks
logs
parser_offsets
parser_runs
accurate_audit_sources
accurate_audit_events
accurate_audit_sync_states
accurate_audit_sync_runs
alerts
alert_evidences
alert_notifications
incidents
incident_alerts
remote_actions
threshold_settings
system_settings
```

Credentials must not be stored in plaintext.

Sensitive values must be stored in `.env` or encrypted config where appropriate.

---

## Git Workflow

Use GitHub.

Recommended branches:

```text
main      = stable branch
develop   = active integration branch
feature/* = feature implementation branch
fix/*     = bug fix branch
```

Do not commit secrets.

Before committing, ensure:

```text
.env is not committed
vendor is not committed unless intentionally required
node_modules is not committed
storage logs are not committed
real credentials are not committed
```

---

## Commands

Laravel commands that should exist eventually:

```bash
php artisan rsyslog:parse
php artisan accurate:audit-sync
php artisan monitoring:server-check
php artisan alerts:evaluate
php artisan remote-actions:expire
php artisan demo:health-check
```

The parser command name must be:

```bash
php artisan rsyslog:parse
```

Do not rename it to `parse:rsyslog` unless explicitly requested.

---

## Testing Expectations

Before marking a task as done, run relevant checks.

For Laravel:

```bash
php artisan test
php artisan route:list
php artisan migrate:fresh --seed
```

For frontend build if Vite/Tailwind is used:

```bash
npm install
npm run build
```

For parser:

```bash
php artisan rsyslog:parse
```

For audit sync:

```bash
php artisan accurate:audit-sync --dry-run
```

If a command cannot be run because environment is missing, state exactly what is missing.

---

## Definition of Done

A task is done only if:

1. It follows the relevant v2 document.
2. It does not introduce forbidden stack/scope.
3. It has no hardcoded device names, IPs, credentials, or demo-only assumptions.
4. It has route/controller/model/view consistency if UI-related.
5. It stores data in the correct table.
6. Alerts have target and evidence.
7. Remote actions are manually controlled and audited.
8. It can be tested or has clear test instructions.
9. It does not break existing routes/tests.
10. It is easy to explain in thesis Chapter 4.

---

## If Unsure

If unsure about any of these:

```text
Firebird table/column
Accurate audit meaning
Windows Agent command
remote restart behavior
alert severity
UI layout
production/deployment decision
```

Stop and ask for clarification. Do not invent behavior.
```

---

# 7. Struktur `.codex/skills`

Selain `AGENTS.md`, project dapat memakai Codex Skills untuk aturan berulang.

Struktur yang disarankan:

```text
.codex/
└── skills/
    ├── centralized-log-monitoring-core/
    │   └── SKILL.md
    │
    ├── centralized-log-monitoring-ui/
    │   └── SKILL.md
    │
    ├── accurate-firebird-audit/
    │   └── SKILL.md
    │
    └── windows-agent-rsyslog/
        └── SKILL.md
```

Skill tidak wajib di awal, tapi sangat membantu jika Codex sering mengerjakan task berulang.

---

# 8. Skill 1 — Core Project Skill

Buat folder:

```text
.codex/skills/centralized-log-monitoring-core/
```

Buat file:

```text
.codex/skills/centralized-log-monitoring-core/SKILL.md
```

Isi:

```md
---
name: centralized-log-monitoring-core
description: Use this skill when implementing or reviewing core backend features for Centralized Log Monitoring Dashboard, including Laravel, database migrations, RSyslog parser, alerts, incidents, Telegram, and remote actions.
---

# Centralized Log Monitoring Core Skill

Follow the v2 documentation exactly.

## Required architecture

Windows Agent -> RSyslog Server -> Laravel Parser -> MySQL/MariaDB -> Dashboard -> Telegram.

Accurate Audit Reader -> Firebird AUDIT + USERS -> MySQL/MariaDB -> Accurate Audit Dashboard.

Remote Actions -> Laravel command API -> Windows Agent polling -> result back to Laravel.

## Do not

- Do not hardcode device names.
- Do not use React or Node backend.
- Do not introduce ELK, Grafana, Prometheus, Wazuh, or SIEM stack.
- Do not create vague alerts.
- Do not use RSyslog for remote command execution.
- Do not read Accurate login from LOGIN as the primary source.
- Do not mutate Accurate database.

## Required implementation style

- Keep Laravel code simple and explainable.
- Use Eloquent models and migrations.
- Use service classes for parser, alert, telegram, audit sync, and remote actions.
- Use config/env for ports, credentials, paths, and thresholds.
- Use tests or at least testable commands.

## Alert rule

Every alert must include target, evidence, impact, detected_by, and recommended_action.

## Definition of done

Before completing, confirm the relevant docs were followed and list what was tested.
```

---

# 9. Skill 2 — UI Skill

Buat folder:

```text
.codex/skills/centralized-log-monitoring-ui/
```

Buat file:

```text
.codex/skills/centralized-log-monitoring-ui/SKILL.md
```

Isi:

```md
---
name: centralized-log-monitoring-ui
description: Use this skill when creating or editing Laravel Blade and Tailwind UI for the Centralized Log Monitoring Dashboard. The UI must look like a professional IT operations cockpit, not a generic AI dashboard or raw log viewer.
---

# Centralized Log Monitoring UI Skill

Follow:

- docs/08_UI_Design_System_v2.md
- docs/09_UI_Wireframe_v2.md

## UI stack

Use Laravel Blade, Tailwind CSS, Alpine.js, Chart.js, and Heroicons or Lucide icons.

Do not use React, Vue, Next.js, or a separate frontend app.

## Visual direction

Build an IT operations cockpit:

- serious
- clean
- professional
- status-first
- dashboard-first
- data-dense but readable
- suitable for IT admin monitoring real devices

## Main navigation

Dashboard
Devices
Accurate Audit
Incidents
Alerts
Remote Actions
Advanced Logs
Settings
Logout

## Dashboard priorities

The dashboard must answer:

- Which devices are online?
- Who is the active Windows user?
- Can each device reach Firebird?
- Is Accurate running?
- Is device performance healthy?
- What Accurate audit activity happened recently?
- Which incidents need action?

## Do not

- Do not put raw log tables as the main dashboard focus.
- Do not create generic SaaS landing page UI.
- Do not use random gradients or decorative hero sections.
- Do not hide target/evidence from alerts.
- Do not show meaningless charts.
- Do not overcrowd the dashboard with unrelated Windows Event Log noise.

## Component rules

Use consistent components for:

- status card
- device table row
- severity badge
- alert panel
- incident panel
- remote action modal
- filter toolbar
- empty state

## Remote restart UI

Restart Client must use a confirmation modal with:

- target device name
- current status
- warning message
- required reason input
- cancel button
- confirm button

Never implement one-click restart without confirmation.

## Done when

The page is readable, status-oriented, consistent with the design system, and uses real fields from the database/schema.
```

---

# 10. Skill 3 — Accurate Firebird Audit Skill

Buat folder:

```text
.codex/skills/accurate-firebird-audit/
```

Buat file:

```text
.codex/skills/accurate-firebird-audit/SKILL.md
```

Isi:

```md
---
name: accurate-firebird-audit
description: Use this skill when implementing or reviewing Accurate 5 Firebird audit trail integration. It must follow the POC findings and must not invent database tables or login sources.
---

# Accurate Firebird Audit Skill

Follow docs/02_Accurate_Firebird_POC_Findings_v2.md.

## Core POC rules

- Accurate 5 Enterprise uses Firebird 2.5 in this project context.
- Firebird is hosted on the VPS.
- Accurate clients run on Windows laptops.
- Use a read-only Firebird user for audit reading.
- Accurate audit activity comes from AUDIT + USERS.
- Internal Accurate username comes from AUDIT.USERID joined to USERS.USERID.
- LOGIN must not be used as the primary source because it was empty in the POC.
- COMP_NAME and IPADDRESS may be empty.
- Windows user comes from Windows Agent, not from Firebird AUDIT.
- Active connection/process info comes from Windows Agent / process/network telemetry.

## Do not

- Do not mutate the Accurate database.
- Do not hardcode Firebird credentials.
- Do not require COMP_NAME or IPADDRESS.
- Do not assume LOGIN contains active sessions.
- Do not invent fields not present in POC.
- Do not claim the system knows active Accurate sessions unless supported by telemetry.

## Implementation requirements

Create/maintain:

- accurate_audit_sources
- accurate_audit_events
- accurate_audit_sync_states
- accurate_audit_sync_runs

Use incremental sync to avoid duplicate import.

If Firebird cannot connect, store sync failure and show configuration error.

## Done when

Audit events can be imported, deduplicated, filtered by user/date/module/keyword, and displayed in Accurate Audit page without relying on unsupported assumptions.
```

---

# 11. Skill 4 — Windows Agent and RSyslog Skill

Buat folder:

```text
.codex/skills/windows-agent-rsyslog/
```

Buat file:

```text
.codex/skills/windows-agent-rsyslog/SKILL.md
```

Isi:

```md
---
name: windows-agent-rsyslog
description: Use this skill when implementing or reviewing the Windows Agent, structured syslog messages, RSyslog server integration, and Laravel parser for real Windows device monitoring.
---

# Windows Agent and RSyslog Skill

Follow docs/06_Windows_Agent_and_RSyslog_Guide_v2.md.

## Main rule

Do not use WSL as the primary way to monitor Windows host state.

The Windows Agent must run on the Windows host and collect real Windows data.

## Agent must collect

- agent_id
- hostname
- Windows user
- ZeroTier IP
- heartbeat
- CPU usage
- RAM usage
- disk usage
- uptime
- RDP status
- Accurate process status
- Firebird connectivity to VPS:3051
- latency / timeout evidence
- remote action result

## Structured syslog format

Use key=value messages such as:

```text
device-monitor: event=device_heartbeat agent_id=... hostname=... windows_user=... status=online
perf-monitor: event=perf_snapshot agent_id=... cpu=42 ram=61 disk=55
network-monitor: event=firebird_connectivity agent_id=... status=connected latency_ms=24
accurate-process-monitor: event=accurate_process agent_id=... process=accurate.exe status=running
```

## Do not

- Do not send all Windows Event Logs by default.
- Do not send random DCOM/System logs to dashboard main page.
- Do not hardcode device name.
- Do not execute remote restart from RSyslog.
- Do not assume server pulls data from client. Client/agent pushes data to RSyslog.

## Parser requirements

Laravel parser must parse key=value fields and update the correct domain tables.

Raw messages can be stored in Advanced Logs, but dashboard must use structured data.

## Done when

A real Windows device can send heartbeat/performance/network/Accurate process data to VPS RSyslog, Laravel parses it, and dashboard displays device status.
```

---

# 12. Optional Skill — Hallmark-Inspired UI Quality

Jika menggunakan Hallmark atau prinsip sejenis, posisikan sebagai **design quality guidance**, bukan pengganti stack.

Buat folder opsional:

```text
.codex/skills/it-ops-ui-quality/
```

Buat file:

```text
.codex/skills/it-ops-ui-quality/SKILL.md
```

Isi:

```md
---
name: it-ops-ui-quality
description: Use this skill to review UI quality and prevent generic AI-generated dashboard design. It should be used after creating Blade/Tailwind pages for the monitoring dashboard.
---

# IT Operations UI Quality Skill

Review the UI as an internal IT monitoring cockpit.

## Check for

- Clear visual hierarchy
- Good spacing
- Professional sidebar/topbar
- Readable tables
- Status badges with consistent meaning
- Alerts with target/evidence/action
- Device status visible at a glance
- Minimal but useful charts
- No generic SaaS hero sections
- No decorative clutter
- No raw log overload on dashboard

## Improve

- Typography scale
- Card grouping
- Filter toolbar clarity
- Table density
- Empty states
- Confirmation modals
- Alert detail layouts
- Mobile/tablet responsiveness

## Do not

- Change stack to React.
- Add fancy gradients without purpose.
- Add meaningless charts.
- Hide important operational data.
- Make the UI look like a landing page.
```

---

# 13. Skill Activation Recommendation

Saat meminta Codex mengerjakan task, sebut skill eksplisit jika tersedia.

Contoh:

```text
Use $centralized-log-monitoring-core.
Read AGENTS.md and docs/13_Codex_Implementation_Brief.md.
Implement the devices table migrations and Eloquent models according to docs/04_Database_Design_v2.md.
Do not implement UI yet.
Stop after migrations, models, factories, and basic tests.
```

Untuk UI:

```text
Use $centralized-log-monitoring-ui and $it-ops-ui-quality.
Read docs/08_UI_Design_System_v2.md and docs/09_UI_Wireframe_v2.md.
Implement the Dashboard Blade page using existing models and controller data only.
Do not add fake random log data.
```

Untuk Accurate:

```text
Use $accurate-firebird-audit.
Read docs/02_Accurate_Firebird_POC_Findings_v2.md and docs/04_Database_Design_v2.md.
Implement AccurateAuditService with read-only sync skeleton, config validation, incremental sync state, and dry-run support.
Do not assume LOGIN is useful.
```

---

# 14. Prompt Awal untuk Codex — Repository Initialization

Gunakan prompt berikut saat pertama kali membuka repo baru di Codex:

```text
Read AGENTS.md and the v2 documentation in docs/.

Goal:
Build the Centralized Log Monitoring Dashboard from scratch as a real-device monitoring system.

Context:
This is a thesis project. The system monitors two Windows laptops running Accurate 5 through a Windows Agent, sends structured telemetry to an RSyslog server on a VPS, parses the logs in Laravel, stores monitoring data in MySQL/MariaDB, reads Accurate Firebird audit trail directly from AUDIT + USERS, sends contextual Telegram alerts, and supports manual controlled Remote Desktop and Restart Client actions.

Constraints:
- Use Laravel, Blade, Tailwind CSS, Alpine.js, Chart.js.
- Use RSyslog as log collector.
- Use MySQL/MariaDB as monitoring DB.
- Use Windows Agent for real Windows telemetry.
- Use Accurate Firebird Audit Reader for AUDIT + USERS.
- Do not use React, Node backend, ELK, Grafana, Prometheus, or SIEM stack.
- Do not hardcode device names.
- Do not use RSyslog for remote action execution.
- Do not implement automatic restart.
- Do not use LOGIN as the primary Accurate audit source.

Task:
Create an implementation plan only. Do not write code yet.

Plan must include:
1. Repository structure.
2. Laravel setup steps.
3. Database migration order.
4. Backend service order.
5. Windows Agent implementation order.
6. RSyslog integration order.
7. Accurate Audit Reader implementation order.
8. UI implementation order.
9. Telegram implementation order.
10. Remote action implementation order.
11. Testing and verification steps.

Done when:
The plan is specific, follows all v2 docs, and lists open questions before coding.
```

---

# 15. Prompt Milestone 1 — Laravel Skeleton

```text
Read AGENTS.md and docs/13_Codex_Implementation_Brief.md.

Goal:
Set up the Laravel project skeleton for Centralized Log Monitoring Dashboard.

Implement:
- Laravel app structure.
- Authentication for one admin role.
- Blade layout with sidebar/topbar skeleton.
- Basic routes for Dashboard, Devices, Accurate Audit, Incidents, Alerts, Remote Actions, Advanced Logs, Settings.
- Empty controllers returning placeholder views.
- Tailwind/Alpine/Chart.js setup.

Constraints:
- Do not implement business logic yet.
- Do not add fake random logs.
- Do not use React.
- UI must follow docs/08 and docs/09.

Done when:
- App runs.
- Login works.
- Protected routes redirect if not authenticated.
- All menu pages open with placeholder content.
- php artisan route:list works.
```

---

# 16. Prompt Milestone 2 — Database Migrations

```text
Read AGENTS.md and docs/04_Database_Design_v2.md.

Goal:
Implement database migrations and Eloquent models.

Implement tables:
- devices
- agent_credentials
- device_telemetries
- network_checks
- accurate_process_snapshots
- server_service_checks
- logs
- parser_offsets
- parser_runs
- accurate_audit_sources
- accurate_audit_events
- accurate_audit_sync_states
- accurate_audit_sync_runs
- alerts
- alert_evidences
- alert_notifications
- incidents
- incident_alerts
- remote_actions
- threshold_settings
- system_settings

Constraints:
- Do not store plaintext secrets.
- Do not hardcode device names.
- Use indexes described in database design.
- Use enum/string values consistently.

Done when:
- php artisan migrate:fresh --seed works.
- Admin user seeder works.
- Default threshold seeder works.
- Models and relationships are defined.
```

---

# 17. Prompt Milestone 3 — RSyslog Parser

```text
Use $windows-agent-rsyslog if available.
Read AGENTS.md, docs/06_Windows_Agent_and_RSyslog_Guide_v2.md, and docs/07_Detection_Rules_v2.md.

Goal:
Implement Laravel parser for structured key=value syslog messages.

Implement:
- php artisan rsyslog:parse
- LogParserService
- KeyValueLogParser
- Parser offset handling
- Hash-based duplicate prevention
- Event routing to domain tables
- Raw log storage in logs table for Advanced Logs

Supported events:
- device_heartbeat
- perf_snapshot
- firebird_connectivity
- accurate_process
- remote_action_result
- server_service_check if present

Constraints:
- Do not rely only on keyword matching.
- Do not create vague alerts directly in parser.
- Parser may call detection service after storing structured data.

Done when:
- Sample structured log lines parse correctly.
- Devices auto-register by agent_id.
- Telemetry/network/process tables update.
- Raw logs remain available for Advanced Logs.
```

---

# 18. Prompt Milestone 4 — Windows Agent

```text
Use $windows-agent-rsyslog if available.
Read docs/06_Windows_Agent_and_RSyslog_Guide_v2.md.

Goal:
Implement Windows Agent MVP.

Implement:
- Agent config file.
- agent_id generation and persistence.
- Device heartbeat collection.
- Windows user detection.
- ZeroTier IP detection or configured IP.
- CPU/RAM/disk collection.
- Accurate.exe process detection.
- Firebird port connectivity check to VPS:3051.
- RDP service status check.
- Structured syslog sender.
- Basic command polling endpoint client for future remote actions.

Constraints:
- Agent must run on Windows host, not WSL.
- Do not send all Windows Event Logs.
- Do not hardcode device names.
- Do not execute restart until remote action flow is implemented safely.

Done when:
- Agent can send heartbeat and telemetry to RSyslog server.
- Log format matches docs/06.
- It can run manually from PowerShell or Python.
```

---

# 19. Prompt Milestone 5 — Detection and Alerts

```text
Use $centralized-log-monitoring-core.
Read docs/07_Detection_Rules_v2.md.

Goal:
Implement contextual detection and alerts.

Implement:
- AlertDetectionService
- IncidentCorrelationService
- Alert cooldown / anti-duplicate logic
- alert_evidences creation
- alert_notifications creation status

Rules:
- DEVICE_HEARTBEAT_MISSED
- DEVICE_OFFLINE
- CPU_HIGH
- CPU_CRITICAL
- RAM_HIGH
- DISK_HIGH
- FIREBIRD_PORT_TIMEOUT
- FIREBIRD_LATENCY_HIGH
- FIREBIRD_SERVICE_DOWN
- ACCURATE_PROCESS_NOT_DETECTED
- ACCURATE_AUDIT_DELETE only if field exists

Constraints:
- Every alert must include target, detected_by, evidence, impact, and recommended_action.
- Do not implement Audit activity spike detected.
- Do not create vague alert titles.

Done when:
- Alerts generated from real stored telemetry are contextual.
- Duplicate alerts respect cooldown.
- Incidents group related alerts where applicable.
```

---

# 20. Prompt Milestone 6 — Telegram

```text
Use $centralized-log-monitoring-core.
Read docs/07_Detection_Rules_v2.md and docs/13_Codex_Implementation_Brief.md.

Goal:
Implement Telegram contextual alert service.

Implement:
- TelegramService
- Telegram message formatter
- Enable/disable setting
- Bot token and chat id from env/settings
- alert_notifications record update
- error handling
- cooldown respect

Message must include:
- Severity
- Title
- Target
- Detected by
- Evidence
- Impact
- Recommended action
- Time
- Dashboard URL if configured

Constraints:
- Telegram is required for the project.
- Do not send vague one-line messages.
- Do not spam duplicate alerts.

Done when:
- Test Telegram command works.
- Alert notification records are stored.
- Failed sends are logged safely without exposing token.
```

---

# 21. Prompt Milestone 7 — Accurate Firebird Audit

```text
Use $accurate-firebird-audit.
Read docs/02_Accurate_Firebird_POC_Findings_v2.md and docs/04_Database_Design_v2.md.

Goal:
Implement Accurate Firebird Audit Reader skeleton and sync.

Implement:
- accurate_audit_sources CRUD/config model
- AccurateAuditService
- php artisan accurate:audit-sync
- --dry-run option
- config validation
- read-only Firebird connection abstraction
- AUDIT + USERS query placeholder/configurable query
- incremental sync state
- sync run logging
- event deduplication
- failure handling

Constraints:
- Do not mutate Firebird database.
- Do not use LOGIN as primary source.
- Do not require COMP_NAME/IPADDRESS.
- Do not invent unknown columns.
- Use POC field mapping only.

Done when:
- Dry-run can validate configuration.
- Sync run records success/failure.
- Imported events appear in Accurate Audit page data source.
```

---

# 22. Prompt Milestone 8 — UI Pages

```text
Use $centralized-log-monitoring-ui and $it-ops-ui-quality if available.
Read docs/08_UI_Design_System_v2.md and docs/09_UI_Wireframe_v2.md.

Goal:
Implement UI pages using Blade and Tailwind.

Pages:
- Dashboard
- Devices
- Device Detail
- Accurate Audit
- Incidents
- Alerts
- Remote Actions
- Advanced Logs
- Settings

Constraints:
- Dashboard is not raw log viewer.
- Use structured data from domain tables.
- Show target/evidence/impact/recommended action for alerts.
- Device Detail must show Remote Desktop and Restart Client actions.
- Restart action requires confirmation modal and reason.
- Advanced Logs is the only page focused on raw logs.

Done when:
- Pages match wireframe intent.
- Empty states exist.
- Filters work where required.
- UI is clean, professional, status-first.
```

---

# 23. Prompt Milestone 9 — Remote Actions

```text
Use $centralized-log-monitoring-core.
Read docs/03_SRS_v2, docs/05_System_Architecture_v2, and docs/09_UI_Wireframe_v2.

Goal:
Implement manual remote actions.

Implement:
- Remote Desktop action logging
- RDP launcher/downloadable .rdp or mstsc command helper
- Restart Client request flow
- Confirmation modal with required reason
- remote_actions records
- Agent command polling API
- Agent result API
- Command expiration
- Authorization token validation

Constraints:
- No automatic restart.
- No restart without reason.
- No restart without audit log.
- RSyslog is not command execution path.
- Only registered devices with valid agent credentials can poll/execute commands.

Done when:
- Admin can request restart manually.
- Pending command is visible.
- Agent can fetch command and report result.
- Remote Actions page shows full audit trail.
```

---

# 24. Prompt Milestone 10 — Real-Device Integration Test

```text
Read docs/10_Test_Plan_v2.md and docs/12_Demo_Script_v2.md.

Goal:
Prepare and execute real-device integration testing.

Verify:
- VPS services running
- ZeroTier connectivity
- RSyslog receiving messages
- Windows Agent heartbeat from Laptop 1
- Windows Agent heartbeat from Laptop 2
- CPU/RAM/disk telemetry parsed
- Firebird connectivity parsed
- Accurate process status parsed
- Accurate audit reader syncs
- Alerts generated with evidence
- Telegram receives contextual alert
- Device Detail shows actions
- Remote Desktop action logs
- Remote Restart action flow works with confirmation
- Advanced Logs stores raw messages

Done when:
- All test results are documented.
- Any failed item has a troubleshooting note.
```

---

# 25. How to Keep Codex from Going Off-Scope

Use short, strict prompts.

Bad prompt:

```text
Build the whole monitoring dashboard.
```

Good prompt:

```text
Read AGENTS.md and docs/04_Database_Design_v2.md.
Implement only the devices and device_telemetries migrations, models, relationships, factories, and seeders.
Do not implement UI.
Do not implement parser.
Stop after tests pass.
```

Bad prompt:

```text
Make the dashboard nice.
```

Good prompt:

```text
Use docs/08_UI_Design_System_v2.md and docs/09_UI_Wireframe_v2.md.
Improve only the Devices index page.
It must show hostname, device_label, windows_user, zerotier_ip, agent status, RDP status, Accurate status, Firebird status, CPU, RAM, disk, last_seen, and action buttons.
Do not add unrelated charts.
```

---

# 26. Review Checklist for Codex Output

After Codex makes changes, review:

```text
[ ] Did Codex read the relevant docs?
[ ] Did Codex keep Laravel Blade/Tailwind stack?
[ ] Did Codex avoid React/Node/ELK/Grafana/Prometheus?
[ ] Did Codex avoid hardcoded devices?
[ ] Did Codex avoid fake demo data as main logic?
[ ] Did Codex keep raw logs in Advanced Logs only?
[ ] Did alerts include target and evidence?
[ ] Did Telegram message include context?
[ ] Did remote restart require confirmation and reason?
[ ] Did Accurate audit follow AUDIT + USERS?
[ ] Did Codex avoid LOGIN as primary source?
[ ] Did Codex avoid mutating Accurate DB?
[ ] Did routes/controllers/views align?
[ ] Did migrations match database design?
[ ] Did it update tests or test instructions?
[ ] Did it avoid committing secrets?
```

---

# 27. Recommended First Git Commit Plan

Suggested commit sequence:

```text
commit 1: docs: add v2 planning documents
commit 2: chore: scaffold Laravel project
commit 3: chore: add AGENTS.md and Codex skills
commit 4: feat(auth): add admin authentication and base layout
commit 5: feat(db): add monitoring database migrations
commit 6: feat(parser): add structured rsyslog parser skeleton
commit 7: feat(agent): add Windows Agent MVP
commit 8: feat(alerts): add contextual alert engine
commit 9: feat(telegram): add contextual Telegram notification
commit 10: feat(audit): add Accurate Firebird audit sync skeleton
commit 11: feat(ui): add dashboard and device pages
commit 12: feat(remote): add manual remote actions
commit 13: test: add integration and demo checks
```

---

# 28. Recommended Repo Files to Create Before Coding

Before implementation, create:

```text
AGENTS.md
.env.example
README.md
PLANS.md
CHANGELOG.md
SECURITY_NOTES.md
```

Optional:

```text
.codex/skills/centralized-log-monitoring-core/SKILL.md
.codex/skills/centralized-log-monitoring-ui/SKILL.md
.codex/skills/accurate-firebird-audit/SKILL.md
.codex/skills/windows-agent-rsyslog/SKILL.md
.codex/skills/it-ops-ui-quality/SKILL.md
```

---

# 29. SECURITY_NOTES.md Template

Create file:

```text
SECURITY_NOTES.md
```

Suggested content:

```md
# SECURITY NOTES

This project is a thesis implementation for real-device monitoring.

## Sensitive values

Do not commit:

- Firebird username/password
- Telegram bot token
- Telegram chat id
- ZeroTier network id if considered private
- Agent tokens
- VPS SSH private key
- Production .env

## Remote restart

Remote restart is manual and controlled.

Requirements:

- Admin confirmation
- Required reason
- Audit log
- Agent authorization
- No auto restart

## Accurate database

Accurate Firebird access must be read-only.

Do not mutate Accurate database from the monitoring application.
```

---

# 30. PLANS.md Template

Create file:

```text
PLANS.md
```

Suggested content:

```md
# PLANS.md

Use this file to track Codex implementation plans.

## Current milestone

Milestone:
Status:
Branch:
Started:
Finished:

## Goal


## Relevant docs


## Constraints


## Implementation steps

1.
2.
3.

## Test steps

1.
2.
3.

## Result


## Notes / follow-up


```

---

# 31. Minimum AGENTS.md vs Skills Strategy

Jika ingin mulai cepat:

```text
Wajib:
- AGENTS.md

Opsional dulu:
- .codex/skills/*
```

Jika Codex mulai sering salah UI, salah Firebird, atau salah parser, baru aktifkan skill khusus.

Rekomendasi praktis:

```text
Step 1: Buat AGENTS.md dulu.
Step 2: Mulai scaffold Laravel.
Step 3: Kalau masuk UI, tambahkan UI skill.
Step 4: Kalau masuk Accurate, tambahkan Firebird skill.
Step 5: Kalau masuk Windows Agent, tambahkan Windows Agent skill.
```

---

# 32. Acceptance Criteria Dokumen Ini

Dokumen ini dianggap selesai jika:

```text
[ ] Ada template AGENTS.md siap pakai.
[ ] Ada aturan scope project yang jelas.
[ ] Ada larangan stack dan fitur yang tidak boleh dibuat.
[ ] Ada aturan device identity tanpa hardcode.
[ ] Ada aturan Accurate Firebird POC.
[ ] Ada aturan Windows Agent + RSyslog.
[ ] Ada aturan Telegram contextual alert.
[ ] Ada aturan Remote Desktop dan Restart manual.
[ ] Ada template Codex Skills.
[ ] Ada prompt awal dan prompt per milestone.
[ ] Ada review checklist untuk output Codex.
```

---

# 33. Kesimpulan

Dokumen ini menjadi pagar utama agar Codex membangun sistem sesuai arah v2.

Inti aturan:

```text
Real device first.
No hardcoded device.
No raw log dashboard.
No vague alerts.
No auto restart.
No mutation to Accurate DB.
No unsupported LOGIN assumption.
Use Laravel Blade/Tailwind.
Use RSyslog for monitoring logs.
Use Firebird AUDIT + USERS for Accurate audit.
Use Telegram as contextual proactive alert.
Use remote actions only with admin approval.
```

Setelah dokumen ini dipakai, langkah berikutnya adalah membuat file `AGENTS.md` di repository, lalu mulai milestone pertama dengan Codex menggunakan prompt yang sudah disediakan.
