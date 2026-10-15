@extends('layouts.app')

@section('title', 'Edit Brand')

@section('content')
<div class="page-header">
    <h1>Edit Brand</h1>
    <p>Update brand details and status.</p>
</div>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('brands.update', $brand) }}" novalidate>
            @csrf
            @method('PUT')
            @include('brands._form')

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i> Update
                </button>
                <a href="{{ route('brands.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
