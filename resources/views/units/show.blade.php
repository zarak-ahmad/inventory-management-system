@extends('layouts.app')

@section('title', 'Unit Details')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>{{ $unit->name }}</h1>
        <p>Unit details.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('units.edit', $unit) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('units.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Name</dt>
            <dd class="col-sm-9">{{ $unit->name }}</dd>

            <dt class="col-sm-3">Short Name</dt>
            <dd class="col-sm-9"><span class="badge text-bg-light border">{{ $unit->short_name }}</span></dd>

            <dt class="col-sm-3">Created</dt>
            <dd class="col-sm-9">{{ $unit->created_at?->format('Y-m-d H:i') }}</dd>

            <dt class="col-sm-3">Updated</dt>
            <dd class="col-sm-9">{{ $unit->updated_at?->format('Y-m-d H:i') }}</dd>
        </dl>
    </div>
</div>
@endsection
