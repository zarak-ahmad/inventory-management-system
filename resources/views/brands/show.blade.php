@extends('layouts.app')

@section('title', 'Brand Details')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>{{ $brand->name }}</h1>
        <p>Brand details.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('brands.edit', $brand) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('brands.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Name</dt>
            <dd class="col-sm-9">{{ $brand->name }}</dd>

            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9">
                @if ($brand->isActive())
                    <span class="badge text-bg-success">Active</span>
                @else
                    <span class="badge text-bg-secondary">Inactive</span>
                @endif
            </dd>

            <dt class="col-sm-3">Description</dt>
            <dd class="col-sm-9">{{ $brand->description ?: '—' }}</dd>

            <dt class="col-sm-3">Created</dt>
            <dd class="col-sm-9">{{ $brand->created_at?->format('Y-m-d H:i') }}</dd>

            <dt class="col-sm-3">Updated</dt>
            <dd class="col-sm-9">{{ $brand->updated_at?->format('Y-m-d H:i') }}</dd>
        </dl>
    </div>
</div>
@endsection
