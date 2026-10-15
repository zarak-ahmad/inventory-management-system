@extends('layouts.app')

@section('title', 'Stock History')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>{{ $product->name }}</h1>
        <p>Stock history and movement for this product.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('inventory.transactions', ['product_id' => $product->id]) }}" class="btn btn-outline-primary">All Filters</a>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted small">Current Stock</div>
                <div class="fs-4 fw-semibold">
                    {{ number_format((float) $product->current_stock, 2) }}
                    <small class="text-muted">{{ $product->unit?->short_name }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted small">Minimum Stock</div>
                <div class="fs-4 fw-semibold">{{ number_format((float) $product->minimum_stock, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted small">Stock Value</div>
                <div class="fs-4 fw-semibold">{{ number_format($stockValue, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted small">Status</div>
                <div class="fs-5 fw-semibold">
                    @if ($product->isOutOfStock())
                        <span class="badge text-bg-danger">Out of Stock</span>
                    @elseif ($product->isLowStock())
                        <span class="badge text-bg-warning text-dark">Low Stock</span>
                    @else
                        <span class="badge text-bg-success">In Stock</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card stat-card mb-3">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-2">SKU</dt>
            <dd class="col-sm-4">{{ $product->sku }}</dd>
            <dt class="col-sm-2">Barcode</dt>
            <dd class="col-sm-4">{{ $product->barcode }}</dd>
            <dt class="col-sm-2">Category</dt>
            <dd class="col-sm-4">{{ $product->category?->name ?? '—' }}</dd>
            <dt class="col-sm-2">Supplier</dt>
            <dd class="col-sm-4">{{ $product->supplier?->supplier_name ?? '—' }}</dd>
            <dt class="col-sm-2">Location</dt>
            <dd class="col-sm-4">{{ $product->location ?: '—' }}</dd>
        </dl>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body">
        <h2 class="h5 mb-3">Movement History</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Before</th>
                        <th class="text-end">After</th>
                        <th>Remarks</th>
                        <th>By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ ucfirst($transaction->transaction_type) }}</td>
                            <td class="text-end {{ (float) $transaction->quantity < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format((float) $transaction->quantity, 2) }}
                            </td>
                            <td class="text-end">{{ number_format((float) $transaction->stock_before, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $transaction->stock_after, 2) }}</td>
                            <td>{{ $transaction->remarks ?: '—' }}</td>
                            <td>{{ $transaction->creator?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No stock movements recorded for this product.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transactions->hasPages())
            <div class="mt-3">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
