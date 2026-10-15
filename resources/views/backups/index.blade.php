@extends('layouts.app')

@section('title', 'Backup & Restore')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Backup &amp; Restore</h1>
        <p>Create, download, restore, or delete SQLite database backups.</p>
    </div>
    <form method="POST" action="{{ route('backups.store') }}">
        @csrf
        <button type="submit" class="btn btn-primary" data-confirm="Create a new database backup now?">
            <i class="bi bi-cloud-arrow-up me-1"></i> Create Backup
        </button>
    </form>
</div>

<div class="alert alert-light border mb-3">
    <div class="fw-semibold mb-1">Important</div>
    <ul class="mb-0 small text-muted">
        <li>Backups copy the SQLite database only (not product images or uploaded files).</li>
        <li>Restoring replaces the current database. A safety backup is created automatically first.</li>
        <li>After restore, refresh the app if data looks stale.</li>
    </ul>
</div>

<div class="card stat-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>Created</th>
                        <th class="text-end">Size</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($backups as $backup)
                        <tr>
                            <td class="fw-semibold">{{ $backup['filename'] }}</td>
                            <td>{{ $backup['modified_at'] }}</td>
                            <td class="text-end">{{ $backupService->formatBytes($backup['size']) }}</td>
                            <td class="text-end">
                                <a href="{{ route('backups.download', $backup['filename']) }}" class="btn btn-sm btn-outline-secondary" title="Download">
                                    <i class="bi bi-download"></i>
                                </a>
                                <form action="{{ route('backups.restore', $backup['filename']) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Restore"
                                        data-confirm="Restore this backup? The current database will be replaced. A safety backup will be created first.">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </form>
                                <form action="{{ route('backups.destroy', $backup['filename']) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                        data-confirm="Delete this backup file permanently?">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No backups yet.
                                <div class="mt-2">
                                    <form method="POST" action="{{ route('backups.store') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">Create First Backup</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
