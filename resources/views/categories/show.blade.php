@extends('layouts.app')

@section('title', 'Category Details')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>{{ $category->name }}</h1>
        <p>Category details.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Name</dt>
            <dd class="col-sm-9">{{ $category->name }}</dd>

            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9">
                @if ($category->isActive())
                    <span class="badge text-bg-success">Active</span>
                @else
                    <span class="badge text-bg-secondary">Inactive</span>
                @endif
            </dd>

            <dt class="col-sm-3">Description</dt>
            <dd class="col-sm-9">{{ $category->description ?: '—' }}</dd>

            <dt class="col-sm-3">Created</dt>
            <dd class="col-sm-9">{{ $category->created_at?->format('Y-m-d H:i') }}</dd>

            <dt class="col-sm-3">Updated</dt>
            <dd class="col-sm-9">{{ $category->updated_at?->format('Y-m-d H:i') }}</dd>
        </dl>
    </div>
</div>
@endsection
