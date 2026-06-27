# SECURITY NOTES

This project is a thesis implementation for real-device monitoring.

## Sensitive Values

Do not commit:

- `.env`
- Firebird username/password
- Telegram bot token
- Telegram chat id
- Agent tokens
- ZeroTier network details if private
- VPS SSH private keys
- Real Accurate database files
- Raw logs containing operational or user data

Milestone 2A system settings may store environment variable names such as `TELEGRAM_BOT_TOKEN`, but must not store actual tokens, passwords, or production secrets.

## Remote Restart

Remote restart must remain manual and controlled:

- Admin confirmation required.
- Reason required.
- Action must be audited.
- Windows Agent authorization required.
- No automatic restart from alert or incident.
- Milestone 2B stores `remote_actions` records only; it does not execute restart commands.

## Accurate Database

Accurate Firebird access must be read-only:

- Use `AUDIT + USERS` for audit trail.
- Do not use `LOGIN` as primary source.
- Do not require `COMP_NAME` or `IPADDRESS`.
- Do not mutate Accurate database.
- Do not create triggers, tables, or stored procedures in Accurate database.

## Dashboard

Main dashboard must not expose raw log noise or secret values. Raw/parsed technical logs belong in Advanced Logs only.
