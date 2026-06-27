<?php

namespace App\Services\Alerts;

use App\Models\AccurateAuditEvent;
use App\Models\AccurateProcessSnapshot;
use App\Models\Alert;
use App\Models\Device;
use App\Models\DeviceTelemetry;
use App\Models\NetworkCheck;
use App\Models\ThresholdSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AlertDetectionService
{
    /** @var array<string, string> */
    private array $thresholds = [];

    public function __construct(
        private readonly TelegramAlertService $telegramAlertService,
    ) {
    }

    /**
     * @return array{created: int, updated: int, skipped: int, notifications: int}
     */
    public function detect(): array
    {
        $this->thresholds = ThresholdSetting::query()->pluck('value', 'key')->all();

        $summary = [
            'created' => 0,
            'updated' => 0,
            'resolved' => 0,
            'notifications' => 0,
        ];

        // Track which dedupe keys are active in this detection run.
        $activeDedupeKeys = [];

        Device::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->each(function (Device $device) use (&$summary, &$activeDedupeKeys): void {
                foreach ($this->deviceRules($device) as $alertPayload) {
                    $activeDedupeKeys[] = $this->dedupeKey($alertPayload);
                    $this->recordResult($summary, $this->raise($alertPayload));
                }
            });

        AccurateAuditEvent::query()
            ->whereNotNull('transaction_type')
            ->whereRaw('UPPER(TRIM(transaction_type)) = ?', ['DELETE'])
            ->orderBy('id')
            ->each(function (AccurateAuditEvent $event) use (&$summary, &$activeDedupeKeys): void {
                $payload = $this->auditDeletePayload($event);
                $activeDedupeKeys[] = $this->dedupeKey($payload);
                $this->recordResult($summary, $this->raise($payload));
            });

        // Auto-resolve: any open alert whose condition is NO LONGER present gets
        // resolved so it can be re-triggered as a fresh occurrence later.
        if ($activeDedupeKeys !== []) {
            $resolved = Alert::query()
                ->whereIn('status', ['open', 'acknowledged'])
                ->whereNotIn('dedupe_key', array_unique($activeDedupeKeys))
                ->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                    'updated_at' => now(),
                ]);
            $summary['resolved'] = $resolved;
        }

        return $summary;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function deviceRules(Device $device): array
    {
        return array_values(array_filter(array_merge([
            $this->heartbeatPayload($device),
        ], $this->performancePayloads($device), [
            $this->networkFailurePayload($device),
            $this->networkLatencyPayload($device),
            $this->accurateProcessPayload($device),
        ])));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function heartbeatPayload(Device $device): ?array
    {
        if (! $device->last_seen_at) {
            return null;
        }

        $minutes = (int) $device->last_seen_at->diffInMinutes(now());
        $critical = $this->intThreshold('heartbeat_critical_minutes');
        $warning = $this->intThreshold('heartbeat_warning_minutes');

        if ($minutes >= $critical) {
            return $this->baseDevicePayload($device, [
                'alert_code' => 'DEVICE_OFFLINE',
                'category' => 'device',
                'severity' => 'critical',
                'title' => "{$device->display_name} terdeteksi offline",
                'description' => "Device {$device->display_name} tidak mengirim heartbeat melewati threshold critical.",
                'detected_by' => 'Laravel Alert Detection Service',
                'source' => 'devices.last_seen_at',
                'evidence_summary' => sprintf(
                    'Last seen %s, %d menit lalu; threshold critical %d menit.',
                    $device->last_seen_at->format('Y-m-d H:i:s'),
                    $minutes,
                    $critical,
                ),
                'impact' => 'Administrator tidak dapat memantau kondisi terbaru device. Jika device dipakai untuk Accurate, aktivitas operasional dapat terganggu.',
                'recommended_action' => 'Hubungi pengguna device, cek koneksi ZeroTier, dan pastikan Windows Agent berjalan.',
                'evidences' => [
                    ['last_seen_at', $device->last_seen_at->toDateTimeString(), 'timestamp', 'devices.last_seen_at', $device->last_seen_at],
                    ['minutes_since_last_seen', (string) $minutes, 'number', 'devices.last_seen_at', now()],
                    ['threshold_minutes', (string) $critical, 'number', 'threshold_settings', now()],
                ],
            ]);
        }

        if ($minutes >= $warning) {
            return $this->baseDevicePayload($device, [
                'alert_code' => 'DEVICE_HEARTBEAT_MISSED',
                'category' => 'device',
                'severity' => 'warning',
                'title' => "Heartbeat tidak diterima dari {$device->display_name}",
                'description' => "Device {$device->display_name} belum mengirim heartbeat melewati threshold warning.",
                'detected_by' => 'Laravel Alert Detection Service',
                'source' => 'devices.last_seen_at',
                'evidence_summary' => sprintf(
                    'Last seen %s, %d menit lalu; threshold warning %d menit.',
                    $device->last_seen_at->format('Y-m-d H:i:s'),
                    $minutes,
                    $warning,
                ),
                'impact' => 'Device atau Windows Agent mungkin tidak aktif, sehingga data monitoring terbaru belum tersedia.',
                'recommended_action' => 'Cek koneksi device, status ZeroTier, atau jalankan ulang Windows Agent.',
                'evidences' => [
                    ['last_seen_at', $device->last_seen_at->toDateTimeString(), 'timestamp', 'devices.last_seen_at', $device->last_seen_at],
                    ['minutes_since_last_seen', (string) $minutes, 'number', 'devices.last_seen_at', now()],
                    ['threshold_minutes', (string) $warning, 'number', 'threshold_settings', now()],
                ],
            ]);
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function performancePayloads(Device $device): array
    {
        $telemetry = $device->latestTelemetry()->first();

        if (! $telemetry) {
            return [];
        }

        $payloads = [];

        if ($telemetry->cpu_usage_percent !== null && $telemetry->cpu_usage_percent >= $this->floatThreshold('cpu_critical_threshold')) {
            $payloads[] = $this->performanceAlert($device, $telemetry, 'CPU_CRITICAL', 'critical', 'CPU sangat tinggi', 'cpu_usage_percent', $this->floatThreshold('cpu_critical_threshold'));
        } elseif ($telemetry->cpu_usage_percent !== null && $telemetry->cpu_usage_percent >= $this->floatThreshold('cpu_warning_threshold')) {
            $payloads[] = $this->performanceAlert($device, $telemetry, 'CPU_HIGH', 'warning', 'CPU tinggi', 'cpu_usage_percent', $this->floatThreshold('cpu_warning_threshold'));
        }

        if ($telemetry->ram_usage_percent !== null && $telemetry->ram_usage_percent >= $this->floatThreshold('ram_warning_threshold')) {
            $payloads[] = $this->performanceAlert($device, $telemetry, 'RAM_HIGH', 'warning', 'Penggunaan RAM tinggi', 'ram_usage_percent', $this->floatThreshold('ram_warning_threshold'));
        }

        if ($telemetry->disk_usage_percent !== null && $telemetry->disk_usage_percent >= $this->floatThreshold('disk_warning_threshold')) {
            $payloads[] = $this->performanceAlert($device, $telemetry, 'DISK_HIGH', 'warning', 'Kapasitas disk mulai penuh', 'disk_usage_percent', $this->floatThreshold('disk_warning_threshold'));
        }

        return $payloads;
    }

    /**
     * @return array<string, mixed>
     */
    private function performanceAlert(Device $device, DeviceTelemetry $telemetry, string $code, string $severity, string $label, string $metric, float $threshold): array
    {
        $value = (float) $telemetry->{$metric};
        $metricLabel = match ($metric) {
            'cpu_usage_percent' => 'CPU',
            'ram_usage_percent' => 'RAM',
            default => 'Disk',
        };

        return $this->baseDevicePayload($device, [
            'alert_code' => $code,
            'category' => 'performance',
            'severity' => $severity,
            'title' => "{$label} pada {$device->display_name}",
            'description' => "{$metricLabel} {$device->display_name} mencapai {$value}% dan melewati threshold {$threshold}%.",
            'detected_by' => 'Windows Agent + Laravel Alert Detection Service',
            'source' => 'device_telemetries',
            'evidence_summary' => sprintf(
                '%s %.2f%%, threshold %.2f%%, reported at %s.',
                $metricLabel,
                $value,
                $threshold,
                $telemetry->reported_at?->format('Y-m-d H:i:s') ?? '-',
            ),
            'impact' => $metric === 'disk_usage_percent'
                ? 'Sistem operasi atau aplikasi Accurate dapat terganggu jika ruang disk habis.'
                : 'Aplikasi pada device dapat berjalan lambat, termasuk Accurate 5.',
            'recommended_action' => $metric === 'disk_usage_percent'
                ? 'Periksa kapasitas drive pada device dan bersihkan file tidak perlu sebelum ruang habis.'
                : 'Gunakan Remote Desktop untuk memeriksa aplikasi yang memakai resource tinggi.',
            'evidences' => [
                [$metric, (string) $value, 'number', 'device_telemetries', $telemetry->reported_at],
                ['threshold_percent', (string) $threshold, 'number', 'threshold_settings', now()],
                ['telemetry_id', (string) $telemetry->id, 'number', 'device_telemetries', $telemetry->reported_at],
            ],
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function networkFailurePayload(Device $device): ?array
    {
        $check = $this->latestFirebirdCheck($device);

        if (! $check) {
            return null;
        }

        $tcpStatus = strtolower((string) $check->tcp_status);
        $status = strtolower((string) $check->status);
        $failed = in_array($tcpStatus, ['timeout', 'refused', 'failed', 'unavailable'], true)
            || in_array($status, ['error', 'critical', 'failed', 'unavailable'], true);

        if (! $failed) {
            return null;
        }

        $severity = in_array($tcpStatus, ['timeout', 'unavailable'], true) || $status === 'critical'
            ? 'critical'
            : 'error';

        $target = $this->firebirdTargetName($device, $check);
        $reason = $check->raw_payload['failure_reason'] ?? $tcpStatus ?: $status;

        return $this->baseDevicePayload($device, [
            'alert_code' => 'FIREBIRD_CONNECTIVITY_FAILED',
            'target_type' => 'device_firebird',
            'target_id' => $this->firebirdTargetId($device, $check),
            'target_name' => $target,
            'category' => 'network',
            'severity' => $severity,
            'title' => "{$target} gagal koneksi",
            'description' => "{$device->display_name} gagal melakukan koneksi TCP ke Firebird {$check->target_host}:{$check->target_port}.",
            'detected_by' => 'Windows Agent + Laravel Alert Detection Service',
            'source' => 'network_checks',
            'evidence_summary' => sprintf(
                'TCP status %s, host %s, port %s, latency %s ms, reason %s.',
                $check->tcp_status,
                $check->target_host ?: '-',
                $check->target_port ?: '-',
                $check->tcp_latency_ms ?? '-',
                $reason ?: '-',
            ),
            'impact' => 'Accurate pada device tersebut dapat gagal membuka atau memakai database Firebird.',
            'recommended_action' => 'Cek koneksi ZeroTier device, firewall, target host/port Firebird, dan status Server bila beberapa device ikut gagal.',
            'evidences' => [
                ['target_host', (string) $check->target_host, 'text', 'network_checks', $check->checked_at],
                ['target_port', (string) $check->target_port, 'number', 'network_checks', $check->checked_at],
                ['tcp_status', (string) $check->tcp_status, 'text', 'network_checks', $check->checked_at],
                ['status', (string) $check->status, 'text', 'network_checks', $check->checked_at],
                ['tcp_latency_ms', (string) ($check->tcp_latency_ms ?? ''), 'number', 'network_checks', $check->checked_at],
                ['failure_reason', (string) $reason, 'text', 'network_checks', $check->checked_at],
            ],
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function networkLatencyPayload(Device $device): ?array
    {
        $check = $this->latestFirebirdCheck($device);

        if (! $check || $check->tcp_latency_ms === null || strtolower((string) $check->tcp_status) !== 'connected') {
            return null;
        }

        $threshold = $this->floatThreshold('firebird_latency_warning_ms');
        if ($check->tcp_latency_ms < $threshold) {
            return null;
        }

        $target = $this->firebirdTargetName($device, $check);

        return $this->baseDevicePayload($device, [
            'alert_code' => 'FIREBIRD_LATENCY_HIGH',
            'target_type' => 'device_firebird',
            'target_id' => $this->firebirdTargetId($device, $check),
            'target_name' => $target,
            'category' => 'network',
            'severity' => 'warning',
            'title' => "Koneksi Firebird lambat dari {$device->display_name}",
            'description' => "Latency koneksi Firebird dari {$device->display_name} melewati threshold.",
            'detected_by' => 'Windows Agent + Laravel Alert Detection Service',
            'source' => 'network_checks',
            'evidence_summary' => sprintf(
                'Latency %.2f ms, threshold %.2f ms, target %s:%s.',
                $check->tcp_latency_ms,
                $threshold,
                $check->target_host ?: '-',
                $check->target_port ?: '-',
            ),
            'impact' => 'Akses Accurate dari device tersebut dapat terasa lambat.',
            'recommended_action' => 'Cek koneksi ZeroTier, kualitas jaringan device, dan status Server.',
            'evidences' => [
                ['target_host', (string) $check->target_host, 'text', 'network_checks', $check->checked_at],
                ['target_port', (string) $check->target_port, 'number', 'network_checks', $check->checked_at],
                ['tcp_latency_ms', (string) $check->tcp_latency_ms, 'number', 'network_checks', $check->checked_at],
                ['threshold_ms', (string) $threshold, 'number', 'threshold_settings', now()],
            ],
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function accurateProcessPayload(Device $device): ?array
    {
        if (! $this->boolConfig('accurate_process_check_enabled')) {
            return null;
        }

        if (! in_array($device->agent_status, ['online'], true) && ! in_array($device->status, ['online'], true)) {
            return null;
        }

        $snapshot = $device->latestAccurateProcessSnapshot()->first();

        if (! $snapshot || $snapshot->process_status !== 'not_running') {
            return null;
        }

        $processName = $snapshot->process_name ?: 'accurate.exe';

        return $this->baseDevicePayload($device, [
            'alert_code' => 'ACCURATE_PROCESS_NOT_RUNNING',
            'category' => 'accurate_process',
            'severity' => 'warning',
            'title' => "Accurate 5 tidak berjalan pada {$device->display_name}",
            'description' => "{$processName} tidak berjalan pada {$device->display_name} saat device terpantau online.",
            'detected_by' => 'Windows Agent + Laravel Alert Detection Service',
            'source' => 'accurate_process_snapshots',
            'evidence_summary' => sprintf(
                'Process %s status %s, checked at %s, Windows user %s.',
                $processName,
                $snapshot->process_status,
                $snapshot->checked_at?->format('Y-m-d H:i:s') ?? '-',
                $device->windows_user ?: '-',
            ),
            'impact' => 'Pengguna device tersebut kemungkinan tidak sedang dapat menggunakan Accurate, atau aplikasi Accurate tertutup/crash.',
            'recommended_action' => 'Hubungi pengguna atau gunakan Remote Desktop untuk memastikan aplikasi Accurate berjalan normal.',
            'evidences' => [
                ['process_name', $processName, 'text', 'accurate_process_snapshots', $snapshot->checked_at],
                ['process_status', $snapshot->process_status, 'text', 'accurate_process_snapshots', $snapshot->checked_at],
                ['checked_at', $snapshot->checked_at?->toDateTimeString() ?? '', 'timestamp', 'accurate_process_snapshots', $snapshot->checked_at],
                ['windows_user', (string) $device->windows_user, 'text', 'devices', $snapshot->checked_at],
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function auditDeletePayload(AccurateAuditEvent $event): array
    {
        return [
            'device_id' => null,
            'accurate_audit_event_id' => $event->id,
            'alert_code' => 'ACCURATE_AUDIT_DELETE',
            'target_type' => 'accurate_audit',
            'target_id' => (string) $event->id,
            'target_name' => trim('Accurate Audit #'.$event->accurate_audit_id),
            'category' => 'accurate_audit',
            'severity' => 'critical',
            'title' => 'Aktivitas DELETE terdeteksi pada Accurate Audit',
            'description' => 'Accurate Audit Reader menyimpan event dengan transaction_type DELETE secara eksplisit.',
            'detected_by' => 'Accurate Audit Reader + Laravel Alert Detection Service',
            'source' => 'accurate_audit_events.transaction_type',
            'evidence_summary' => sprintf(
                'TRANSTYPE DELETE, user %s, source %s, invoice %s, activity time %s.',
                $event->accurate_username ?: '-',
                $event->source ?: '-',
                $event->invoice_no ?: '-',
                $event->activity_time?->format('Y-m-d H:i:s') ?? '-',
            ),
            'impact' => 'Penghapusan data Accurate perlu diverifikasi karena dapat memengaruhi jejak transaksi atau data operasional.',
            'recommended_action' => 'Buka detail Accurate Audit, konfirmasi aktivitas dengan user terkait, dan cocokkan dengan prosedur operasional.',
            'evidences' => [
                ['accurate_audit_id', $event->accurate_audit_id, 'text', 'accurate_audit_events', $event->activity_time],
                ['transaction_type', (string) $event->transaction_type, 'text', 'accurate_audit_events', $event->activity_time],
                ['accurate_username', (string) $event->accurate_username, 'text', 'accurate_audit_events', $event->activity_time],
                ['source', (string) $event->source, 'text', 'accurate_audit_events', $event->activity_time],
                ['invoice_no', (string) $event->invoice_no, 'text', 'accurate_audit_events', $event->activity_time],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function baseDevicePayload(Device $device, array $overrides): array
    {
        return $overrides + [
            'device_id' => $device->id,
            'target_type' => 'device',
            'target_id' => (string) $device->id,
            'target_name' => $device->display_name,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{status: string, notified: bool}
     */
    private function raise(array $payload): array
    {
        $now = now();
        $dedupeKey = $this->dedupeKey($payload);
        $existing = Alert::query()
            ->where('dedupe_key', $dedupeKey)
            ->latest('last_detected_at')
            ->first();

        if ($existing) {
            // Resolved alert triggered again → create a fresh occurrence so it
            // re-notifies. Stale resolved alerts are left alone.
            if ($existing->status === 'resolved') {
                goto create_new;
            }

            $existing->forceFill([
                'last_detected_at' => $now,
                'evidence_summary' => $payload['evidence_summary'],
                'status' => $existing->status === 'resolved' ? 'open' : $existing->status,
            ])->save();

            // "Notify and forget": a dedupe key gets at most ONE notification
            // ever. Future runs only update the record silently.
            $alreadyNotified = $existing->notifications()
                ->where('status', 'sent')
                ->exists();

            if ($alreadyNotified) {
                return ['status' => 'updated', 'notified' => false];
            }

            // First-time notification for a previously un-notified alert.
            $this->storeEvidence($existing, $payload['evidences']);
            $this->telegramAlertService->sendContextualAlert($existing->refresh());

            return ['status' => 'updated', 'notified' => true];
        }

        // Brand-new condition, or resolved alert re-triggered — create + notify.
    create_new:
        $alert = DB::transaction(function () use ($payload, $dedupeKey, $now): Alert {
            $alert = Alert::query()->create([
                'device_id' => $payload['device_id'] ?? null,
                'log_id' => $payload['log_id'] ?? null,
                'accurate_audit_event_id' => $payload['accurate_audit_event_id'] ?? null,
                'alert_code' => $payload['alert_code'],
                'target_type' => $payload['target_type'],
                'target_id' => $payload['target_id'] ?? null,
                'target_name' => $payload['target_name'],
                'category' => $payload['category'],
                'severity' => $payload['severity'],
                'title' => $payload['title'],
                'description' => $payload['description'],
                'detected_by' => $payload['detected_by'],
                'source' => $payload['source'],
                'evidence_summary' => $payload['evidence_summary'],
                'impact' => $payload['impact'],
                'recommended_action' => $payload['recommended_action'],
                'status' => 'open',
                'dedupe_key' => $dedupeKey,
                'first_detected_at' => $now,
                'last_detected_at' => $now,
                'detected_at' => $now,
            ]);

            $this->storeEvidence($alert, $payload['evidences']);

            return $alert;
        });

        $this->telegramAlertService->sendContextualAlert($alert->refresh());

        return ['status' => 'created', 'notified' => true];
    }

    /**
     * @param  array<int, array{0: string, 1: string, 2: string, 3: string, 4: Carbon|null}>  $evidences
     */
    private function storeEvidence(Alert $alert, array $evidences): void
    {
        foreach ($evidences as [$key, $value, $type, $source, $measuredAt]) {
            $alert->evidences()->create([
                'evidence_key' => $key,
                'evidence_value' => $value,
                'evidence_type' => $type,
                'source' => $source,
                'measured_at' => $measuredAt,
            ]);
        }
    }

    /**
     * @param  array{created: int, updated: int, skipped: int, notifications: int}  $summary
     * @param  array{status: string, notified: bool}  $result
     */
    private function recordResult(array &$summary, array $result): void
    {
        $summary[$result['status']]++;
        if ($result['notified']) {
            $summary['notifications']++;
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function dedupeKey(array $payload): string
    {
        return hash('sha256', implode('|', [
            $payload['alert_code'],
            $payload['target_type'],
            $payload['target_id'] ?? $payload['target_name'],
            $payload['severity'],
        ]));
    }

    private function latestFirebirdCheck(Device $device): ?NetworkCheck
    {
        return $device->networkChecks()
            ->where('target_type', 'firebird')
            ->latest('checked_at')
            ->first();
    }

    private function firebirdTargetName(Device $device, NetworkCheck $check): string
    {
        $target = $check->target_name ?: 'Firebird';
        $host = $check->target_host ?: 'unknown-host';
        $port = $check->target_port ?: $this->intThreshold('firebird_port');

        return "{$device->display_name} -> {$target} {$host}:{$port}";
    }

    private function firebirdTargetId(Device $device, NetworkCheck $check): string
    {
        return implode(':', [
            $device->id,
            $check->target_host ?: 'unknown-host',
            $check->target_port ?: $this->intThreshold('firebird_port'),
        ]);
    }

    private function intThreshold(string $key): int
    {
        return (int) ($this->thresholds[$key] ?? config("monitoring.alerting.{$key}", 0));
    }

    private function floatThreshold(string $key): float
    {
        return (float) ($this->thresholds[$key] ?? config("monitoring.alerting.{$key}", 0));
    }

    private function boolConfig(string $key): bool
    {
        return (bool) config("monitoring.alerting.{$key}", false);
    }
}
