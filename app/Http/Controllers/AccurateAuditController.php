<?php

namespace App\Http\Controllers;

use App\Models\AccurateAuditEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccurateAuditController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'username' => ['nullable', 'string', 'max:150'],
            'source' => ['nullable', 'string', 'max:150'],
            'transaction_type' => ['nullable', 'string', 'max:100'],
            'keyword' => ['nullable', 'string', 'max:150'],
        ]);

        $query = AccurateAuditEvent::query()->latest('activity_time');

        if (! empty($filters['from'])) {
            $query->whereDate('activity_time', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('activity_time', '<=', $filters['to']);
        }

        if (! empty($filters['username'])) {
            $query->where('accurate_username', 'like', '%'.$filters['username'].'%');
        }

        if (! empty($filters['source'])) {
            $query->where('source', 'like', '%'.$filters['source'].'%');
        }

        if (! empty($filters['transaction_type'])) {
            $query->where('transaction_type', 'like', '%'.$filters['transaction_type'].'%');
        }

        if (! empty($filters['keyword'])) {
            $keyword = '%'.$filters['keyword'].'%';
            $query->where(function ($query) use ($keyword): void {
                $query->where('transaction_description', 'like', $keyword)
                    ->orWhere('invoice_no', 'like', $keyword)
                    ->orWhere('accurate_fullname', 'like', $keyword)
                    ->orWhere('accurate_username', 'like', $keyword);
            });
        }

        return view('accurate-audit.index', [
            'auditEvents' => $query->paginate(15)->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function show(string $id): View
    {
        return view('accurate-audit.show', [
            'auditEvent' => AccurateAuditEvent::query()->with('auditSource')->find($id),
            'id' => $id,
        ]);
    }
}
