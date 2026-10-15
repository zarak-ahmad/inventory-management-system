@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<div class="page-header">
    <h1>Edit Supplier</h1>
    <p>Update supplier contact details and status.</p>
</div>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('suppliers.update', $supplier) }}" novalidate>
            @csrf
            @method('PUT')
            @include('suppliers._form')

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i> Update
                </button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
