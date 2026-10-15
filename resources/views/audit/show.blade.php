@extends('layouts.app')

@section('title', 'Audit Log Detail')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Audit Log Detail</h1>
        <p>{{ str_replace('_', ' ', ucfirst($log->action)) }} · {{ $log->created_at?->format('Y-m-d H:i:s') }}</p>
    </div>
    <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card stat-card mb-3">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">User</dt>
            <dd class="col-sm-9">{{ $log->user?->name ?? 'System' }} @if($log->user?->email)<span class="text-muted">({{ $log->user->email }})</span>@endif</dd>

            <dt class="col-sm-3">Action</dt>
            <dd class="col-sm-9"><span class="badge text-bg-light border">{{ str_replace('_', ' ', $log->action) }}</span></dd>

            <dt class="col-sm-3">Table</dt>
            <dd class="col-sm-9">{{ $log->table_name ?? '—' }}</dd>

            <dt class="col-sm-3">Record ID</dt>
            <dd class="col-sm-9">{{ $log->record_id ?? '—' }}</dd>

            <dt class="col-sm-3">IP Address</dt>
            <dd class="col-sm-9">{{ $log->ip_address ?? '—' }}</dd>

            <dt class="col-sm-3">When</dt>
            <dd class="col-sm-9">{{ $log->created_at?->format('Y-m-d H:i:s') }}</dd>
        </dl>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Old Values</h2>
                @if ($log->old_values)
                    <pre class="bg-light border rounded p-3 small mb-0" style="white-space: pre-wrap;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <div class="text-muted">None</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">New Values</h2>
                @if ($log->new_values)
                    <pre class="bg-light border rounded p-3 small mb-0" style="white-space: pre-wrap;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <div class="text-muted">None</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
