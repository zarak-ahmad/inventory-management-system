@extends('layouts.app')

@section('title', 'Add User')

@section('content')
<div class="page-header">
    <h1>Add User</h1>
    <p>Create a new system user and assign a role.</p>
</div>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('users.store') }}" novalidate>
            @csrf
            @include('users._form')

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Save
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
