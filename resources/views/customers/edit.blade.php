@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="page-header">
    <h1>Edit Customer</h1>
    <p>Update customer details.</p>
</div>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('customers.update', $customer) }}" novalidate>
            @csrf
            @method('PUT')
            @include('customers._form')

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i> Update
                </button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
