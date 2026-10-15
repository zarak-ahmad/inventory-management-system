@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>{{ $user->name }}</h1>
        <p>User account details and assigned permissions.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card stat-card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Account</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Name</dt>
                    <dd class="col-sm-8">{{ $user->name }}</dd>
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $user->email }}</dd>
                    <dt class="col-sm-4">Phone</dt>
                    <dd class="col-sm-8">{{ $user->phone ?: '—' }}</dd>
                    <dt class="col-sm-4">Role</dt>
                    <dd class="col-sm-8">{{ $user->role?->name ?? '—' }}</dd>
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        @if ($user->status === 'active')
                            <span class="badge text-bg-success">Active</span>
                        @else
                            <span class="badge text-bg-secondary">Inactive</span>
                        @endif
                    </dd>
                    <dt class="col-sm-4">Created</dt>
                    <dd class="col-sm-8">{{ $user->created_at?->format('Y-m-d H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card stat-card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Permissions</h2>
                @if ($user->role?->permissions?->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($user->role->permissions as $permission)
                            <span class="badge text-bg-light border">{{ $permission->label }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No permissions assigned.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
