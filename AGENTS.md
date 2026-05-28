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
- Accurate audit trail from Firebird AUDIT + USERS
- Contextual alerts with Telegram notification
- Manual remote desktop and remote restart actions

## Required Stack
- Laravel
- Laravel Blade
- Tailwind CSS
- Alpine.js
- Chart.js
- MySQL or MariaDB
- RSyslog
- Firebird 2.5 for Accurate database
- Windows Agent for Windows clients
- ZeroTier private network
- Telegram Bot API

## Required Documents
Before implementing any feature, read the relevant documents in docs/.

Primary reading order:
1. docs/01_PRD_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md
2. docs/02_Accurate_Firebird_POC_Findings_v2.md
3. docs/03_SRS_v2_Real_Device_Centralized_Log_Monitoring_Dashboard.md
4. docs/04_Database_Design_v2.md
5. docs/05_System_Architecture_v2.md
6. docs/06_Windows_Agent_and_RSyslog_Guide_v2.md
7. docs/07_Detection_Rules_v2.md
8. docs/08_UI_Design_System_v2.md
9. docs/09_UI_Wireframe_v2.md
10. docs/13_Codex_Implementation_Brief.md

## Critical Rules
- Do not use old source code.
- Do not build simulation-first random log features.
- Do not hardcode device names.
- Device identity must use agent_id as primary identity.
- Do not use WSL as the main Windows monitoring layer.
- Do not use React.
- Do not use Node.js backend.
- Do not use ELK, Grafana, Prometheus, or SIEM-complex tooling.
- Do not auto-restart any device.
- Remote restart must be manual, confirmed, reasoned, and audited.
- RSyslog is for monitoring logs/status only, not remote actions.
- Accurate Audit Trail must not go through RSyslog.
- Accurate Audit Trail must be read directly from Firebird using AUDIT + USERS.
- Do not use LOGIN as the main Accurate audit source.
- Do not create alerts without target, evidence, impact, and recommended action.
- Raw logs belong in Advanced Logs, not the main dashboard.

## UI Rules
- Use Blade + Tailwind + Alpine.js + Chart.js.
- UI style: IT operations cockpit.
- Main dashboard must not be a raw log viewer.
- Prioritize device status, Windows user, Firebird connectivity, Accurate process, audit trail, alerts, and remote actions.
- Use Hallmark only to improve UI quality and avoid generic AI-looking layouts.
- Do not use purple/pink SaaS gradient hero sections.
- Do not overuse centered layouts.
- Do not invent vanity metrics or fake charts.

## Git Workflow
- main = stable only.
- develop = active development.
- feature/* = specific milestones.
- Commit after each working milestone.

## Done Means
A task is done only when:
- Code follows the relevant docs.
- No forbidden design choices are introduced.
- Route/view/model/migration names are consistent.
- Basic tests or manual verification steps are provided.
- The implementation can be explained in the thesis.