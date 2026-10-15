@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="page-header">
    <h1>Audit Logs</h1>
    <p>Track user activity, product changes, and stock adjustments.</p>
</div>

<div class="card stat-card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('audit-logs.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" value="{{ $search }}" class="form-control" placeholder="User, action, table, IP">
            </div>
            <div class="col-md-2">
                <label for="action" class="form-label">Action</label>
                <select name="action" id="action" class="form-select">
                    <option value="">All</option>
                    @foreach ($actions as $actionOption)
                        <option value="{{ $actionOption }}" @selected(request('action') === $actionOption)>{{ str_replace('_', ' ', ucfirst($actionOption)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="table_name" class="form-label">Table</label>
                <select name="table_name" id="table_name" class="form-select">
                    <option value="">All</option>
                    @foreach ($tables as $table)
                        <option value="{{ $table }}" @selected(request('table_name') === $table)>{{ $table }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="user_id" class="form-label">User</label>
                <select name="user_id" id="user_id" class="form-select">
                    <option value="">All</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">From</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">To</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>
        </form>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>Record</th>
                        <th>IP</th>
                        <th class="text-end">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $log->user?->name ?? 'System' }}</td>
                            <td><span class="badge text-bg-light border">{{ str_replace('_', ' ', $log->action) }}</span></td>
                            <td>{{ $log->table_name ?? '—' }}</td>
                            <td>{{ $log->record_id ?? '—' }}</td>
                            <td class="small text-muted">{{ $log->ip_address ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('audit-logs.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No audit logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
