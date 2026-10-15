@extends('layouts.app')

@section('title', 'Adjustment Details')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Stock Adjustment</h1>
        <p>Adjustment #{{ $adjustment->id }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('inventory.show', $adjustment->product_id) }}" class="btn btn-outline-primary">Product History</a>
        <a href="{{ route('stock-adjustments.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Product</dt>
            <dd class="col-sm-9">
                {{ $adjustment->product?->name }}
                <div class="text-muted small">{{ $adjustment->product?->sku }}</div>
            </dd>

            <dt class="col-sm-3">Type</dt>
            <dd class="col-sm-9">
                <span class="badge {{ $adjustment->isIncrease() ? 'text-bg-success' : 'text-bg-warning text-dark' }}">
                    {{ ucfirst($adjustment->adjustment_type) }}
                </span>
            </dd>

            <dt class="col-sm-3">Quantity</dt>
            <dd class="col-sm-9 {{ $adjustment->isIncrease() ? 'text-success' : 'text-danger' }}">
                {{ $adjustment->isIncrease() ? '+' : '-' }}{{ number_format((float) $adjustment->quantity, 2) }}
                {{ $adjustment->product?->unit?->short_name }}
            </dd>

            <dt class="col-sm-3">Stock Before</dt>
            <dd class="col-sm-9">{{ number_format((float) $adjustment->stock_before, 2) }}</dd>

            <dt class="col-sm-3">Stock After</dt>
            <dd class="col-sm-9">{{ number_format((float) $adjustment->stock_after, 2) }}</dd>

            <dt class="col-sm-3">Reason</dt>
            <dd class="col-sm-9">{{ $adjustment->reason }}</dd>

            <dt class="col-sm-3">Performed By</dt>
            <dd class="col-sm-9">{{ $adjustment->creator?->name ?? '—' }}</dd>

            <dt class="col-sm-3">Date & Time</dt>
            <dd class="col-sm-9">{{ $adjustment->created_at?->format('Y-m-d H:i:s') }}</dd>
        </dl>
    </div>
</div>
@endsection
