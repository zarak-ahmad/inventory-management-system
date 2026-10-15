<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $logs = AuditLog::query()
            ->with('user:id,name,email')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('action', 'like', "%{$search}%")
                        ->orWhere('table_name', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->query('action')))
            ->when($request->filled('table_name'), fn ($q) => $q->where('table_name', $request->query('table_name')))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->query('user_id')))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->query('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->query('date_to')))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $tables = AuditLog::query()
            ->whereNotNull('table_name')
            ->distinct()
            ->orderBy('table_name')
            ->pluck('table_name');

        return view('audit.index', [
            'logs' => $logs,
            'search' => $search,
            'actions' => AuditLog::actions(),
            'tables' => $tables,
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(AuditLog $auditLog): View
    {
        $auditLog->load('user:id,name,email');

        return view('audit.show', [
            'log' => $auditLog,
        ]);
    }
}
