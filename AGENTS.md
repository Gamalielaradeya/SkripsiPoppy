# AGENTS.md

## Project Name

Centralized Log Monitoring Dashboard

## Project Goal

Build a real-device IT monitoring dashboard for PT XYZ small office environment.

The system monitors:
- Windows laptops running Accurate 5
- Windows active user
- Device heartbeat
- CPU/RAM/Disk usage
- Firebird connectivity
- Accurate process status
- Accurate audit trail from Firebird `AUDIT + USERS`
- Contextual alerts with Telegram notification
- Manual remote desktop and remote restart actions

This project is not a random log demo. It is a real-device monitoring system for a small office scenario using Windows clients, Accurate 5, VPS Linux, ZeroTier, RSyslog, Laravel, Firebird, and Telegram.

---

## Required Stack

Use only this stack unless the user explicitly changes it:

- Laravel
- Laravel Blade
- Tailwind CSS
- Alpine.js
- Chart.js
- MySQL or MariaDB
- RSyslog
- Firebird 2.5 for Accurate database
- Windows Agent for Windows clients
- PowerShell for Windows Agent MVP
- ZeroTier private network
- Telegram Bot API

Do not introduce React, Node.js backend, ELK, Grafana, Prometheus, or SIEM-complex tooling.

---

## Required Documents

Before implementing any feature, read the relevant documents in `Dokumentasi/`.

Primary reading order:

1. `Dokumentasi/01_PRD_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md`
2. `Dokumentasi/02_Accurate_Firebird_POC_Findings_v2.md`
3. `Dokumentasi/03_SRS_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md`
4. `Dokumentasi/04_Database_Design_v2.md`
5. `Dokumentasi/05_System_Architecture_v2.md`
6. `Dokumentasi/06_Windows_Agent_and_RSyslog_Guide_v2.md`
7. `Dokumentasi/07_Detection_Rules_v2.md`
8. `Dokumentasi/08_UI_Design_System_v2.md`
9. `Dokumentasi/09_UI_Wireframe_v2.md`
10. `Dokumentasi/10_Test_Plan_v2.md`
11. `Dokumentasi/11_Deployment_Guide_VPS_ZeroTier_v2.md`
12. `Dokumentasi/12_Demo_Script_v2.md`
13. `Dokumentasi/13_Codex_Implementation_Brief.md`
14. `Dokumentasi/14_Project_Setup_Checklist_v2.md`
15. `Dokumentasi/15_AGENTS_md_and_Codex_Skills_Guide.md`

If the repository uses `docs/` instead of `Dokumentasi/`, adapt the path, but do not change the project scope or feature rules.

---

## Current Progress

Completed:
- Milestone 1: Laravel Foundation & Auth
- Milestone 2: Database Foundation
- Milestone 3: Agent Registration API
- Milestone 4: UI Foundation + Hallmark Design Pass
- Milestone 5: Windows Agent PowerShell MVP
- Milestone 6: Device Telemetry Real
- Milestone 7: RSyslog Structured Log Pipeline
- Milestone 8: Firebird Connectivity + Accurate Process Monitoring
- Milestone 9: Accurate Firebird Audit Reader

Next:
- Milestone 10: Contextual Alerts + Telegram

Do not skip ahead to remote restart or deployment before completing and committing Milestone 10.

---

## Locked Implementation Milestones

The project must follow these 12 milestones. Do not reorder, replace, or merge milestones unless the user explicitly asks.

### Milestone 1 — Laravel Foundation & Auth

Goal:
- Create the Laravel base application and admin UI shell.

Output:
- Laravel project scaffold
- Admin login/logout
- Protected routes
- Sidebar and topbar
- Placeholder pages
- Initial README
- `.env.example`
- `SECURITY_NOTES.md`

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

Status:
- Completed.

---

### Milestone 2 — Database Foundation

Goal:
- Create the database foundation for monitoring, audit, alerts, incidents, and remote actions.

Output:
- `devices`
- `agent_credentials`
- `device_telemetries`
- `network_checks`
- `accurate_process_snapshots`
- `server_service_checks`
- `logs`
- `parser_offsets`
- `parser_runs`
- `threshold_settings`
- `system_settings`
- `alerts`
- `alert_evidences`
- `alert_notifications`
- `incidents`
- `incident_alerts`
- `remote_actions`
- `accurate_audit_sources`
- `accurate_audit_events`
- `accurate_audit_sync_states`
- `accurate_audit_sync_runs`

Rules:
- Do not seed fake devices.
- Do not seed fake monitoring logs.
- Do not hardcode device names.
- Store secrets only as placeholders or environment key names, not plaintext secrets.

Status:
- Completed.

---

### Milestone 3 — Agent Registration API

Goal:
- Provide API foundation for Windows Agent registration and heartbeat.

Output:
- `POST /api/agent/register`
- `POST /api/agent/heartbeat`
- `GET /api/agent/commands/pending`

Rules:
- `agent_id` is the primary device identity.
- Hostname is metadata, not identity.
- Device label can default from hostname only when new or empty.
- Store agent token as hash only.
- Return plaintext token only once when newly generated.
- Heartbeat requires valid token.
- No hardcoded device names.
- No fake data.

Status:
- Completed.

---

### Milestone 4 — UI Foundation + Hallmark Design Pass

Goal:
- Improve the current Blade/Tailwind UI into a professional IT operations cockpit.

Use:
- Laravel Blade
- Tailwind CSS
- Alpine.js
- Chart.js only where useful
- Hallmark for UI quality and anti-generic design audit

Output:
- Polished dashboard layout
- Polished devices page
- Polished device detail page
- Polished Accurate Audit page
- Polished Alerts page
- Polished Incidents page
- Polished Remote Actions page
- Polished Advanced Logs page
- Polished Settings page
- Better reusable UI components

Rules:
- Do not implement backend business logic.
- Do not implement Windows Agent.
- Do not implement RSyslog parser.
- Do not implement Firebird Audit Reader.
- Do not implement Telegram.
- Do not implement alert detection logic.
- Do not implement remote restart execution.
- Do not create fake monitoring data.
- Do not create random charts.
- Do not make raw logs the main dashboard focus.
- Use real empty states or real database counts only.

Status:
- Next.

---

### Milestone 5 — Windows Agent PowerShell MVP

Goal:
- Build the first real Windows Agent using PowerShell.

Output:
- `windows-agent/agent.ps1`
- `windows-agent/config.example.json`
- `windows-agent/README.md`

Agent must:
- Create or load persistent `agent_id`
- Read config from local config file
- Register to Laravel API if token is missing
- Store token locally
- Send heartbeat
- Collect hostname
- Collect Windows user
- Collect local IP
- Detect ZeroTier IP if possible
- Collect uptime
- Collect last boot time
- Check basic RDP status
- Send agent version

Rules:
- PowerShell only for MVP.
- Do not use Python for MVP.
- Do not implement RSyslog sending yet.
- Do not implement Firebird connectivity yet.
- Do not implement Accurate process detection yet.
- Do not implement remote restart yet.
- Do not hardcode device names.

---

### Milestone 6 — Device Telemetry Real

Goal:
- Extend Windows Agent and backend to collect real performance telemetry.

Output:
- CPU usage
- RAM usage
- Disk usage
- Uptime
- Last boot
- Telemetry snapshots

Dashboard and Device Detail should start showing real telemetry when available.

Rules:
- No fake CPU/RAM/Disk values.
- No random demo data.
- Telemetry must come from Windows Agent or real submitted payload.
- Empty state is acceptable when no data exists.

---

### Milestone 7 — RSyslog Structured Log Pipeline

Goal:
- Add structured syslog pipeline from Windows Agent to RSyslog and Laravel parser.

Output:
- RSyslog server configuration for VPS
- Windows Agent structured syslog sender
- Structured key=value log format
- Laravel parser command
- `php artisan rsyslog:parse`
- Parser offsets
- Parser runs
- Advanced Logs populated from real RSyslog logs

Rules:
- RSyslog is for monitoring logs/status only.
- RSyslog is not for remote action.
- Raw logs must belong in Advanced Logs.
- Raw logs must not be the dashboard focus.
- Parser must support structured key=value logs.
- Parser must prevent duplicate logs.

Example structured logs:
- `device-monitor: agent_id=... hostname=... status=online`
- `perf-monitor: agent_id=... cpu=45 ram=61 disk=70`
- `network-monitor: agent_id=... target=firebird port=3051 status=connected latency_ms=25`

---

### Milestone 8 — Firebird Connectivity + Accurate Process Monitoring

Goal:
- Monitor whether Windows Accurate clients can reach Firebird and whether Accurate is running.

Output:
- Firebird port 3051 connectivity check
- Firebird latency check
- Timeout/failure detection
- Accurate process detection
- `network_checks` population
- `accurate_process_snapshots` population
- Device page and Device Detail integration

Rules:
- Firebird connectivity from Windows Agent means client-to-server connectivity.
- Firebird service health on VPS must be distinguished from client connectivity failure.
- `accurate.exe` process status must be associated with a specific device.
- Do not create generic alerts like “Firebird unreachable” without target and evidence.

---

### Milestone 9 — Accurate Firebird Audit Reader

Goal:
- Read Accurate audit trail directly from Firebird.

Output:
- Read-only Firebird connection
- Query `AUDIT + USERS`
- Incremental sync command
- Dedupe by source audit id/hash
- `accurate_audit_events` populated
- Accurate Audit UI with filters

Rules:
- Accurate Audit Trail must not go through RSyslog.
- Do not use `LOGIN` as the main Accurate audit source.
- `AUDIT + USERS` is the main source of truth.
- `COMP_NAME` and `IPADDRESS` may be nullable.
- Windows user and Accurate internal user are different concepts.
- Do not infer Windows user from Accurate audit if not available.
- Do not invent missing fields.

---

### Milestone 10 — Contextual Alerts + Telegram

Goal:
- Implement evidence-based alert detection and Telegram notification.

Output:
- AlertDetectionService
- AlertEvidence
- Alert lifecycle
- Telegram formatter
- Telegram notification history
- Alert cooldown/deduplication

Rules:
- Telegram is required.
- Alerts must be contextual.
- Alerts must include target, detected_by/source, evidence, impact, recommended action, time, and dashboard link when available.
- Do not create alerts without target and evidence.
- Do not create vague alerts like “Firebird unreachable” or “Audit spike detected” unless the rule is explicitly implemented and evidence exists.
- Do not implement automatic remediation.

Telegram message format should include:
- Severity
- Target
- Detected by
- Evidence
- Impact
- Recommended action
- Time
- Dashboard link if available

---

### Milestone 11 — Remote Desktop + Remote Restart Manual

Goal:
- Add manual IT admin action tools.

Output:
- Remote Desktop launcher
- Restart Client request
- Confirmation modal
- Reason required
- Remote command polling
- Agent command execution
- Agent result reporting
- Remote Actions audit log

Rules:
- Remote restart must be manual.
- Remote restart must require confirmation.
- Remote restart must require admin reason.
- Remote restart must be audited in `remote_actions`.
- Remote restart must not be automatic.
- Remote restart must not be sent through RSyslog.
- Device must be registered and authorized before receiving commands.

---

### Milestone 12 — Deployment, Testing, Real-Device UAT

Goal:
- Prepare system for real-device operation and thesis demonstration.

Output:
- VPS setup
- ZeroTier setup
- Laravel deployment
- MySQL/MariaDB deployment
- RSyslog server deployment
- Firebird setup
- Accurate DB setup
- Windows Agent installation
- Telegram test
- Real-device verification
- UAT checklist
- Final demo readiness

Rules:
- Use real devices where possible.
- No random simulation-first demo.
- Fallback plans may exist, but the main target is real-device implementation.
- The final system must be explainable in the thesis.

---

## Critical Rules

Always follow these rules:

- Do not use old source code.
- Do not build simulation-first random log features.
- Do not hardcode device names.
- Device identity must use `agent_id` as primary identity.
- Do not use WSL as the main Windows monitoring layer.
- Do not use React.
- Do not use Node.js backend.
- Do not use ELK, Grafana, Prometheus, or SIEM-complex tooling.
- Do not auto-restart any device.
- Remote restart must be manual, confirmed, reasoned, and audited.
- RSyslog is for monitoring logs/status only, not remote actions.
- Accurate Audit Trail must not go through RSyslog.
- Accurate Audit Trail must be read directly from Firebird using `AUDIT + USERS`.
- Do not use `LOGIN` as the main Accurate audit source.
- Do not create alerts without target, evidence, impact, and recommended action.
- Raw logs belong in Advanced Logs, not the main dashboard.
- Do not seed fake monitoring data.
- Do not create fake charts.
- Do not invent unavailable Accurate audit fields.
- Do not expose secrets in code, README, screenshots, logs, or responses.

---

## Device Identity Rules

Device identity must be flexible and real-device friendly.

Primary identity:
- `agent_id`

Secondary metadata:
- hostname
- device_label
- Windows user
- local IP
- ZeroTier IP
- agent version
- last seen

Rules:
- Never use hardcoded device names in logic.
- Example names like `WIN-ACC-01` are documentation examples only.
- Do not use hostname as primary identity.
- Device label can be edited by admin.
- Agent may set device label only when creating the device or when label is empty.

---

## Accurate / Firebird Rules

Accurate audit trail is based on the user’s POC findings.

Source:
- Firebird 2.5 Accurate database
- `AUDIT + USERS`

Rules:
- Read Accurate audit directly from Firebird.
- Use read-only database access.
- Do not use RSyslog for Accurate Audit Trail.
- Do not use the `LOGIN` table as the primary source.
- `COMP_NAME` may be empty.
- `IPADDRESS` may be empty.
- Accurate internal username comes from `AUDIT.USERID -> USERS.USERID`.
- Windows user comes from Windows Agent or Windows process context, not from Firebird audit.
- Do not assume fields that are not confirmed by POC.
- Preserve raw payload when useful to avoid losing unknown audit columns.

---

## Alert Rules

Alerts must be evidence-based and useful for IT admin.

Every alert should have:
- severity
- category
- target_type
- target_id or target_name
- detected_by/source
- evidence
- impact
- recommended_action
- status
- detected_at

Do not create generic alerts.

Bad examples:
- `Firebird unreachable`
- `Accurate process not running`
- `Audit activity spike detected`

Good examples:
- `WIN-ACC-02 failed to connect to Firebird VPS:3051`
- `Accurate 5 is not running on Laptop Finance 2`
- `Firebird service on VPS is inactive`
- `CPU high on Laptop Finance 2`

---

## UI Rules

Use:
- Blade
- Tailwind CSS
- Alpine.js
- Chart.js only where useful

Style:
- IT operations cockpit
- Compact
- Professional
- Status-first
- Evidence-based
- Real-device oriented
- Not generic SaaS
- Not AI-looking dashboard
- Not landing page style

Main dashboard must prioritize:
- Device status
- Windows user
- Firebird connectivity
- Accurate process status
- Accurate audit trail
- Alerts
- Incidents
- Remote actions

Rules:
- Main dashboard must not be a raw log viewer.
- Advanced Logs is the only place for raw/technical logs.
- Do not use purple/pink SaaS gradient hero sections.
- Do not overuse centered layouts.
- Do not invent vanity metrics.
- Do not create fake charts.
- Empty states are better than fake data.
- Hallmark may be used only to improve UI quality and avoid generic AI-looking layouts.

---

## Git Workflow

Use this workflow:

- `main` = stable only
- `develop` = active development
- `feature/*` = specific milestones

Commit after each working milestone.

Recommended commit messages:
- `feat: scaffold Laravel auth and IT operations UI shell`
- `feat: add monitoring database foundation`
- `feat: add agent registration and heartbeat API`
- `feat: polish IT operations UI foundation`
- `feat: add PowerShell Windows agent MVP`
- `feat: add real device telemetry collection`
- `feat: add rsyslog structured log pipeline`
- `feat: add Firebird connectivity and Accurate process monitoring`
- `feat: add Accurate Firebird audit reader`
- `feat: add contextual alerts and Telegram notifications`
- `feat: add remote desktop and manual restart actions`
- `chore: finalize deployment and real-device UAT`

---

## Testing Rules

Run relevant tests after each milestone.

Common commands:
- `php artisan test`
- `php artisan route:list`
- `php artisan migrate:fresh --seed`
- `npm run build`

For UI milestones:
- Run `npm run build`
- Verify protected pages load
- Verify empty states
- Verify no fake data appears
- Verify no raw logs dominate dashboard

For API milestones:
- Test successful request
- Test validation failure
- Test auth failure
- Test duplicate/second request behavior
- Test no plaintext secret leak

For agent milestones:
- Support dry-run when possible
- Do not print token values
- Provide manual Windows test steps
- Keep config sample free of real secrets

---

## Security Rules

- Do not commit `.env`.
- Do not commit real Telegram token.
- Do not commit real Firebird credentials.
- Do not commit real VPS passwords or private keys.
- Do not print stored agent tokens.
- Do not print token hash.
- Do not store plaintext agent token in database.
- Do not expose secrets in README or docs.
- Use environment variables for secrets.
- Use placeholders in sample config files.
- Remote restart must require confirmation and reason.

---

## Done Means

A task is done only when:
- Code follows the relevant documents.
- No forbidden design choices are introduced.
- Route/view/model/migration names are consistent.
- Basic tests or manual verification steps are provided.
- No fake monitoring data is introduced.
- No hardcoded device names are introduced.
- No secrets are exposed.
- The implementation can be explained in the thesis.