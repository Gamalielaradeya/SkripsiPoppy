<?php

namespace App\Services\Incidents;

use App\Models\Alert;
use App\Models\Device;
use App\Models\Incident;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class IncidentCorrelationService
{
    /**
     * Incident type codes.
     */
    private const DEVICE_OFFLINE = 'DEVICE_OFFLINE';
    private const DEVICE_PERFORMANCE_DEGRADED = 'DEVICE_PERFORMANCE_DEGRADED';
    private const FIREBIRD_CONNECTIVITY_PROBLEM = 'FIREBIRD_CONNECTIVITY_PROBLEM';
    private const FIREBIRD_LATENCY_ISSUE = 'FIREBIRD_LATENCY_ISSUE';
    private const ACCURATE_PROCESS_PROBLEM = 'ACCURATE_PROCESS_PROBLEM';
    private const DEVICE_SLOW = 'DEVICE_SLOW';

    /**
     * @return array{created: int, updated: int}
     */
    public function correlate(): array
    {
        $summary = ['created' => 0, 'updated' => 0];

        $openAlerts = Alert::query()
            ->whereIn('status', ['open'])
            ->whereNotNull('device_id')
            ->with('device')
            ->get();

        $groupedByDevice = $openAlerts->groupBy('device_id');

        foreach ($groupedByDevice as $deviceId => $deviceAlerts) {
            /** @var Collection<int, Alert> $deviceAlerts */
            $device = $deviceAlerts->first()->device;

            if (! $device || ! $device->is_active) {
                continue;
            }

            foreach ($this->correlateForDevice($device, $deviceAlerts) as $incidentPayload) {
                $result = $this->upsertIncident($incidentPayload);
                if ($result === 'created') {
                    $summary['created']++;
                } elseif ($result === 'updated') {
                    $summary['updated']++;
                }
            }
        }

        return $summary;
    }

    /**
     * @param  Collection<int, Alert>  $alerts
     * @return array<int, array<string, mixed>>
     */
    private function correlateForDevice(Device $device, Collection $alerts): array
    {
        $incidents = [];

        $alertCodes = $alerts->pluck('alert_code')->unique()->values();

        // 1. DEVICE_OFFLINE — ketika ada alert DEVICE_OFFLINE atau DEVICE_HEARTBEAT_MISSED
        if ($alertCodes->contains(fn ($c) => in_array($c, ['DEVICE_OFFLINE', 'DEVICE_HEARTBEAT_MISSED']))) {
            $matched = $alerts->filter(fn (Alert $a) => in_array($a->alert_code, ['DEVICE_OFFLINE', 'DEVICE_HEARTBEAT_MISSED']));
            $incidents[] = $this->buildPayload(
                self::DEVICE_OFFLINE,
                $device,
                'warning',
                "{$device->display_name} terdeteksi offline",
                "Device {$device->display_name} tidak mengirim heartbeat melewati batas waktu. Aktivitas monitoring tidak tersedia.",
                'Hubungi pengguna device dan pastikan Windows Agent serta koneksi ZeroTier berjalan.',
                $matched
            );
        }

        // 2. DEVICE_PERFORMANCE_DEGRADED — 2+ alert CPU/RAM/Disk
        $perfAlerts = $alerts->filter(fn (Alert $a) => in_array($a->alert_code, ['CPU_HIGH', 'CPU_CRITICAL', 'RAM_HIGH', 'DISK_HIGH']));
        if ($perfAlerts->count() >= 2) {
            $worstSeverity = $perfAlerts->contains(fn (Alert $a) => $a->severity === 'critical') ? 'critical' : 'warning';
            $metrics = $perfAlerts->pluck('alert_code')->unique()->implode(', ');
            $incidents[] = $this->buildPayload(
                self::DEVICE_PERFORMANCE_DEGRADED,
                $device,
                $worstSeverity,
                "{$device->display_name} mengalami penurunan performa",
                "Beberapa metrik performa pada {$device->display_name} melampaui ambang batas: {$metrics}. Aplikasi termasuk Accurate 5 dapat berjalan lambat.",
                'Gunakan Remote Desktop untuk memeriksa aplikasi yang memakai resource tinggi, atau restart device jika diperlukan.',
                $perfAlerts
            );
        }

        // 3. FIREBIRD_CONNECTIVITY_PROBLEM
        $fbConnAlerts = $alerts->filter(fn (Alert $a) => $a->alert_code === 'FIREBIRD_CONNECTIVITY_FAILED');
        if ($fbConnAlerts->isNotEmpty()) {
            $sev = $fbConnAlerts->contains(fn (Alert $a) => $a->severity === 'critical') ? 'critical' : 'error';
            $incidents[] = $this->buildPayload(
                self::FIREBIRD_CONNECTIVITY_PROBLEM,
                $device,
                $sev,
                "Koneksi Firebird bermasalah dari {$device->display_name}",
                "{$device->display_name} gagal melakukan koneksi TCP ke server Firebird. Accurate tidak dapat mengakses database.",
                'Cek koneksi ZeroTier, firewall, status Server Firebird, dan pastikan port 3051 terbuka.',
                $fbConnAlerts
            );
        }

        // 4. FIREBIRD_LATENCY_ISSUE
        $fbLatAlerts = $alerts->filter(fn (Alert $a) => $a->alert_code === 'FIREBIRD_LATENCY_HIGH');
        if ($fbLatAlerts->isNotEmpty()) {
            $incidents[] = $this->buildPayload(
                self::FIREBIRD_LATENCY_ISSUE,
                $device,
                'warning',
                "Latensi Firebird tinggi dari {$device->display_name}",
                "Koneksi Firebird dari {$device->display_name} memiliki latensi di atas ambang batas. Akses Accurate dapat terasa lambat.",
                'Periksa kualitas jaringan ZeroTier dan beban Server Firebird.',
                $fbLatAlerts
            );
        }

        // 5. ACCURATE_PROCESS_PROBLEM
        $accAlerts = $alerts->filter(fn (Alert $a) => $a->alert_code === 'ACCURATE_PROCESS_NOT_RUNNING');
        if ($accAlerts->isNotEmpty()) {
            $incidents[] = $this->buildPayload(
                self::ACCURATE_PROCESS_PROBLEM,
                $device,
                'warning',
                "Accurate 5 tidak berjalan pada {$device->display_name}",
                "Proses Accurate 5 tidak terdeteksi berjalan pada {$device->display_name} meskipun device online. Pengguna tidak dapat menggunakan Accurate.",
                'Hubungi pengguna device atau gunakan Remote Desktop untuk memastikan Accurate berjalan normal.',
                $accAlerts
            );
        }

        // 6. DEVICE_SLOW — CPU/RAM tinggi + Firebird latency tinggi di device yang sama (3+)
        $cpuRamAlerts = $alerts->filter(fn (Alert $a) => in_array($a->alert_code, ['CPU_HIGH', 'CPU_CRITICAL', 'RAM_HIGH']));
        if ($fbLatAlerts->isNotEmpty() && $cpuRamAlerts->isNotEmpty() && ($fbLatAlerts->count() + $cpuRamAlerts->count()) >= 3) {
            $combined = $fbLatAlerts->merge($cpuRamAlerts);
            $incidents[] = $this->buildPayload(
                self::DEVICE_SLOW,
                $device,
                'warning',
                "{$device->display_name} terindikasi lambat",
                "{$device->display_name} menunjukkan gejala performa rendah: penggunaan resource tinggi dan latensi Firebird di atas ambang. Pengalaman pengguna Accurate terganggu.",
                'Restart device atau hentikan aplikasi berat yang tidak diperlukan. Pastikan koneksi ZeroTier stabil.',
                $combined
            );
        }

        return $incidents;
    }

    /**
     * @param  Collection<int, Alert>  $matchedAlerts
     * @return array<string, mixed>
     */
    private function buildPayload(
        string $incidentCode,
        Device $device,
        string $severity,
        string $title,
        string $summary,
        string $recommendedAction,
        Collection $matchedAlerts,
    ): array {
        $now = now();
        $earliestAlertAt = $matchedAlerts->min('detected_at') ?: $now;
        $latestAlertAt = $matchedAlerts->max('detected_at') ?: $now;

        $evidence = [];
        foreach ($matchedAlerts as $alert) {
            $evidence["alert_{$alert->id}"] = $alert->evidence_summary;
        }

        return [
            'device_id' => $device->id,
            'incident_code' => $incidentCode,
            'target_type' => 'device',
            'target_id' => (string) $device->id,
            'target_name' => $device->display_name,
            'severity' => $severity,
            'title' => $title,
            'summary' => $summary,
            'evidence_json' => $evidence,
            'status' => 'open',
            'detected_at' => $earliestAlertAt,
            'alert_ids' => $matchedAlerts->pluck('id')->toArray(),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return string 'created' or 'updated'
     */
    private function upsertIncident(array $payload): string
    {
        $alertIds = $payload['alert_ids'];
        unset($payload['alert_ids']);

        // Dedup: cek apakah sudah ada incident open/acknowledged dengan kode & device yang sama
        $existing = Incident::query()
            ->where('device_id', $payload['device_id'])
            ->where('incident_code', $payload['incident_code'])
            ->whereIn('status', ['open', 'acknowledged'])
            ->first();

        if ($existing) {
            // Merge evidence tanpa duplikat
            $existingJson = $existing->evidence_json ?: [];
            $existing->forceFill([
                'severity' => $payload['severity'],
                'title' => $payload['title'],
                'summary' => $payload['summary'],
                'evidence_json' => array_merge($existingJson, $payload['evidence_json']),
                'updated_at' => now(),
            ])->save();

            // Attach new alerts to existing incident
            $existing->alerts()->syncWithoutDetaching($alertIds);

            return 'updated';
        }

        // Buat baru
        $incident = DB::transaction(function () use ($payload, $alertIds): Incident {
            $incident = Incident::query()->create($payload);

            // Attach alerts via pivot
            $incident->alerts()->attach($alertIds);

            return $incident;
        });

        return 'created';
    }
}