<?php

namespace App\Http\Controllers;

use App\Models\AccurateAuditEvent;
use App\Models\Alert;
use App\Models\Device;
use App\Models\Incident;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function devices(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $results = Device::query()
            ->where(function ($query) use ($q) {
                $query->where('device_label', 'like', "%{$q}%")
                    ->orWhere('hostname', 'like', "%{$q}%")
                    ->orWhere('windows_user', 'like', "%{$q}%")
                    ->orWhere('ip_zerotier', 'like', "%{$q}%")
                    ->orWhere('ip_local', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get()
            ->map(fn (Device $d) => [
                'value' => $d->display_name,
                'label' => $d->display_name.' ('.$d->hostname.')',
            ]);

        return response()->json($results);
    }

    public function alertTargets(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        $query = Alert::query()->select('target_name')->distinct();

        if ($q !== '') {
            $query->where('target_name', 'like', "%{$q}%");
        }

        return response()->json(
            $query->orderBy('target_name')->limit(10)->pluck('target_name')->map(fn ($v) => ['value' => $v, 'label' => $v])->values()
        );
    }

    public function alertKeywords(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        $query = Alert::query()->select('title');

        if ($q !== '') {
            $query->where('title', 'like', "%{$q}%");
        }

        return response()->json(
            $query->orderBy('title')->limit(10)->pluck('title')->map(fn ($v) => ['value' => $v, 'label' => $v])->values()
        );
    }

    public function incidentTargets(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        $query = Incident::query()->select('target_name')->distinct();

        if ($q !== '') {
            $query->where('target_name', 'like', "%{$q}%");
        }

        return response()->json(
            $query->orderBy('target_name')->limit(10)->pluck('target_name')->map(fn ($v) => ['value' => $v, 'label' => $v])->values()
        );
    }

    public function incidentTypes(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        $query = Incident::query()->select('incident_code')->distinct();

        if ($q !== '') {
            $query->where('incident_code', 'like', "%{$q}%");
        }

        return response()->json(
            $query->orderBy('incident_code')->limit(10)->pluck('incident_code')->map(fn ($v) => ['value' => $v, 'label' => $v])->values()
        );
    }
}