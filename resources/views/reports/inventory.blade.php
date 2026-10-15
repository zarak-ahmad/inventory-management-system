@extends('layouts.app')

@section('title', 'Inventory Report')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Inventory / Stock Value</h1>
        <p>Current on-hand stock valued at purchase and selling prices.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print / PDF
        </button>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">All Reports</a>
    </div>
</div>

<div class="card stat-card mb-3 no-print">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.inventory') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">All</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($categoryId === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="{{ route('reports.inventory') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><div class="text-muted small">Products</div><div class="fs-4 fw-semibold">{{ $summary['count'] }}</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><div class="text-muted small">Total Units</div><div class="fs-4 fw-semibold">{{ number_format($summary['total_units'], 2) }}</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><div class="text-muted small">Stock Value (Cost)</div><div class="fs-4 fw-semibold">{{ number_format($summary['stock_value'], 2) }}</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><div class="text-muted small">Retail Value</div><div class="fs-4 fw-semibold">{{ number_format($summary['retail_value'], 2) }}</div></div></div></div>
</div>

<div class="card stat-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th class="text-end">Stock</th>
                        <th class="text-end">Cost</th>
                        <th class="text-end">Sell</th>
                        <th class="text-end">Stock Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="{{ $product->isLowStock() ? 'table-warning' : '' }}">
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->category?->name ?? '—' }}</td>
                            <td class="text-end">{{ number_format((float) $product->current_stock, 2) }} {{ $product->unit?->short_name }}</td>
                            <td class="text-end">{{ number_format((float) $product->purchase_price, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $product->selling_price, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $product->current_stock * (float) $product->purchase_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@include('reports._print')
