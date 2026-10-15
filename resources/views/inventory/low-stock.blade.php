@extends('layouts.app')

@section('title', 'Low Stock Report')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Low Stock Report</h1>
        <p>Products at or below their minimum stock level.</p>
    </div>
    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Back to Inventory</a>
</div>

<div class="card stat-card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('inventory.low-stock') }}" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" value="{{ $search }}" class="form-control" placeholder="Name or SKU">
            </div>
            <div class="col-md-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('inventory.low-stock') }}" class="btn btn-outline-secondary">Clear</a>
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print
                </button>
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
                        <th>Product</th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th class="text-end">Current</th>
                        <th class="text-end">Minimum</th>
                        <th class="text-end">Shortage</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $shortage = max(0, (float) $product->minimum_stock - (float) $product->current_stock);
                            $historyUrl = route('inventory.show', $product);
                        @endphp
                        <tr class="table-row-link" role="link" tabindex="0" data-href="{{ $historyUrl }}" onclick="if(!event.target.closest('a,button')) window.location.href=this.dataset.href" onkeydown="if(event.key==='Enter'){window.location.href=this.dataset.href}">
                            <td>
                                <div class="fw-semibold">
                                    <a href="{{ $historyUrl }}" class="link-body-emphasis text-decoration-none">{{ $product->name }}</a>
                                </div>
                                <small class="text-muted">{{ $product->sku }}</small>
                            </td>
                            <td>{{ $product->category?->name ?? '—' }}</td>
                            <td>{{ $product->supplier?->supplier_name ?? '—' }}</td>
                            <td class="text-end">
                                {{ number_format((float) $product->current_stock, 2) }}
                                {{ $product->unit?->short_name }}
                                @if ($product->isOutOfStock())
                                    <span class="badge text-bg-danger">Out</span>
                                @else
                                    <span class="badge text-bg-warning text-dark">Low</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format((float) $product->minimum_stock, 2) }}</td>
                            <td class="text-end text-danger fw-semibold">{{ number_format($shortage, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ $historyUrl }}" class="btn btn-sm btn-outline-primary">History</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No low stock products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="mt-3">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
