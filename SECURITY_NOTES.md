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

## Remote Restart

Remote restart must remain manual and controlled:

- Admin confirmation required.
- Reason required.
- Action must be audited.
- Windows Agent authorization required.
- No automatic restart from alert or incident.

## Accurate Database

Accurate Firebird access must be read-only:

- Use `AUDIT + USERS` for audit trail.
- Do not use `LOGIN` as primary source.
- Do not require `COMP_NAME` or `IPADDRESS`.
- Do not mutate Accurate database.
- Do not create triggers, tables, or stored procedures in Accurate database.

## Dashboard

Main dashboard must not expose raw log noise or secret values. Raw/parsed technical logs belong in Advanced Logs only.
