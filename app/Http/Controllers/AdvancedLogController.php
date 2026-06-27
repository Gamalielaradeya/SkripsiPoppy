<?php

namespace App\Http\Controllers;

use App\Models\LogEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdvancedLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'hostname' => ['nullable', 'string', 'max:100'],
            'event_type' => ['nullable', 'string', 'max:80'],
            'severity' => ['nullable', 'string', 'max:30'],
            'keyword' => ['nullable', 'string', 'max:120'],
        ]);

        $logs = LogEntry::query()
            ->when($filters['date_from'] ?? null, fn ($query, $value) => $query->whereDate('logged_at', '>=', $value))
            ->when($filters['date_to'] ?? null, fn ($query, $value) => $query->whereDate('logged_at', '<=', $value))
            ->when($filters['hostname'] ?? null, fn ($query, $value) => $query->where('hostname', 'like', '%'.$value.'%'))
            ->when($filters['event_type'] ?? null, fn ($query, $value) => $query->where('event_type', $value))
            ->when($filters['severity'] ?? null, fn ($query, $value) => $query->where('severity', $value))
            ->when($filters['keyword'] ?? null, function ($query, $value): void {
                $query->where(function ($inner) use ($value): void {
                    $inner->where('raw_message', 'like', '%'.$value.'%')
                        ->orWhere('parsed_message', 'like', '%'.$value.'%');
                });
            })
            ->latest('logged_at');

        return view('advanced-logs.index', [
            'logs' => $logs->paginate(20)->withQueryString(),
            'filters' => $filters,
            'eventTypes' => LogEntry::query()->whereNotNull('event_type')->distinct()->orderBy('event_type')->pluck('event_type'),
            'severities' => LogEntry::query()->whereNotNull('severity')->distinct()->orderBy('severity')->pluck('severity'),
        ]);
    }

    public function show(string $id): View
    {
        return view('advanced-logs.show', [
            'log' => LogEntry::query()->find($id),
            'id' => $id,
        ]);
    }
}
