@extends('layouts.app')

@section('title', 'Stock Adjustments')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Stock Adjustments</h1>
        <p>Manual stock increases and decreases with full audit history.</p>
    </div>
    <a href="{{ route('stock-adjustments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Adjustment
    </a>
</div>

<div class="card stat-card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('stock-adjustments.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" value="{{ $search }}" class="form-control" placeholder="Product, SKU, reason">
            </div>
            <div class="col-md-3">
                <label for="product_id" class="form-label">Product</label>
                <select name="product_id" id="product_id" class="form-select">
                    <option value="">All products</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="adjustment_type" class="form-label">Type</label>
                <select name="adjustment_type" id="adjustment_type" class="form-select">
                    <option value="">All types</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" @selected(request('adjustment_type') === $type)>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">From</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">To</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('stock-adjustments.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Before</th>
                        <th class="text-end">After</th>
                        <th>Reason</th>
                        <th>By</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($adjustments as $adjustment)
                        <tr>
                            <td>{{ $adjustment->created_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="fw-semibold">{{ $adjustment->product?->name }}</div>
                                <small class="text-muted">{{ $adjustment->product?->sku }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $adjustment->isIncrease() ? 'text-bg-success' : 'text-bg-warning text-dark' }}">
                                    {{ ucfirst($adjustment->adjustment_type) }}
                                </span>
                            </td>
                            <td class="text-end {{ $adjustment->isIncrease() ? 'text-success' : 'text-danger' }}">
                                {{ $adjustment->isIncrease() ? '+' : '-' }}{{ number_format((float) $adjustment->quantity, 2) }}
                            </td>
                            <td class="text-end">{{ number_format((float) $adjustment->stock_before, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $adjustment->stock_after, 2) }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($adjustment->reason, 40) }}</td>
                            <td>{{ $adjustment->creator?->name ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('stock-adjustments.show', $adjustment) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                No stock adjustments found.
                                <div class="mt-2">
                                    <a href="{{ route('stock-adjustments.create') }}" class="btn btn-sm btn-primary">New Adjustment</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($adjustments->hasPages())
            <div class="mt-3">
                {{ $adjustments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
