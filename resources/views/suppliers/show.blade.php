@extends('layouts.app')

@section('title', 'Supplier Details')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>{{ $supplier->supplier_name }}</h1>
        <p>Supplier contact details.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8 col-xl-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Contact Information</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Supplier</dt>
                    <dd class="col-sm-8">{{ $supplier->supplier_name }}</dd>

                    <dt class="col-sm-4">Company</dt>
                    <dd class="col-sm-8">{{ $supplier->company_name ?: '—' }}</dd>

                    <dt class="col-sm-4">Phone</dt>
                    <dd class="col-sm-8">{{ $supplier->phone }}</dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $supplier->email ?: '—' }}</dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        @if ($supplier->isActive())
                            <span class="badge text-bg-success">Active</span>
                        @else
                            <span class="badge text-bg-secondary">Inactive</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Address</dt>
                    <dd class="col-sm-8">{{ $supplier->address ?: '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
