@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="page-header">
    <h1>Edit User</h1>
    <p>Update user details, role, and status.</p>
</div>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('users.update', $user) }}" novalidate>
            @csrf
            @method('PUT')
            @include('users._form')

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i> Update
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
