@extends('layouts.app')

@section('title', 'Add Supplier')

@section('content')
<div class="page-header">
    <h1>Add Supplier</h1>
    <p>Create a new supplier record.</p>
</div>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('suppliers.store') }}" novalidate>
            @csrf
            @include('suppliers._form')

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Save
                </button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
