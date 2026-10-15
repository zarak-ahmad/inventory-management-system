@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Inventory</h1>
        <p>Current stock levels, inventory value, and product movement overview.</p>
    </div>
    <div class="d-flex gap-2">
        @if (auth()->user()->hasPermission('manage_stock_adjustments'))
            <a href="{{ route('stock-adjustments.create') }}" class="btn btn-outline-success">
                <i class="bi bi-sliders me-1"></i> Adjust Stock
            </a>
        @endif
        <a href="{{ route('inventory.transactions') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left-right me-1"></i> Transactions
        </a>
        <a href="{{ route('inventory.low-stock') }}" class="btn btn-warning">
            <i class="bi bi-exclamation-triangle me-1"></i> Low Stock
        </a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6 col-xl">
        <a href="{{ route('inventory.index') }}" class="card stat-card stat-card-link h-100">
            <div class="card-body">
                <div class="text-muted small">Products</div>
                <div class="fs-4 fw-semibold">{{ number_format($summary['total_products']) }}</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl">
        <a href="{{ route('inventory.transactions') }}" class="card stat-card stat-card-link h-100">
            <div class="card-body">
                <div class="text-muted small">Total Units</div>
                <div class="fs-4 fw-semibold">{{ number_format($summary['total_units'], 2) }}</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl">
        @if (auth()->user()->hasPermission('view_reports'))
            <a href="{{ route('reports.inventory') }}" class="card stat-card stat-card-link h-100">
        @else
            <div class="card stat-card h-100">
        @endif
            <div class="card-body">
                <div class="text-muted small">Stock Value</div>
                <div class="fs-4 fw-semibold">{{ number_format($summary['stock_value'], 2) }}</div>
            </div>
        @if (auth()->user()->hasPermission('view_reports'))</a>@else</div>@endif
    </div>
    <div class="col-md-6 col-xl">
        <a href="{{ route('inventory.low-stock') }}" class="card stat-card stat-card-link h-100">
            <div class="card-body">
                <div class="text-muted small">Low Stock</div>
                <div class="fs-4 fw-semibold text-warning">{{ number_format($summary['low_stock']) }}</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl">
        <a href="{{ route('inventory.index', ['out_of_stock' => 1]) }}" class="card stat-card stat-card-link h-100">
            <div class="card-body">
                <div class="text-muted small">Out of Stock</div>
                <div class="fs-4 fw-semibold text-danger">{{ number_format($summary['out_of_stock']) }}</div>
            </div>
        </a>
    </div>
</div>

<div class="card stat-card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('inventory.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" value="{{ $search }}" class="form-control" placeholder="Name, SKU, barcode">
            </div>
            <div class="col-md-2">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">All</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="supplier_id" class="form-label">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-select">
                    <option value="">All</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>{{ $supplier->supplier_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 d-flex flex-wrap gap-3 align-items-center pt-4">
                <div class="form-check mb-0">
                    <input type="checkbox" name="low_stock" value="1" id="low_stock" class="form-check-input" @checked(request()->boolean('low_stock'))>
                    <label for="low_stock" class="form-check-label">Low stock</label>
                </div>
                <div class="form-check mb-0">
                    <input type="checkbox" name="out_of_stock" value="1" id="out_of_stock" class="form-check-input" @checked(request()->boolean('out_of_stock'))>
                    <label for="out_of_stock" class="form-check-label">Out of stock</label>
                </div>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">Clear</a>
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
                        <th class="text-end">Stock</th>
                        <th class="text-end">Min</th>
                        <th class="text-end">Unit Cost</th>
                        <th class="text-end">Value</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $value = (float) $product->current_stock * (float) $product->purchase_price;
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
                                @elseif ($product->isLowStock())
                                    <span class="badge text-bg-warning text-dark">Low</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format((float) $product->minimum_stock, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $product->purchase_price, 2) }}</td>
                            <td class="text-end">{{ number_format($value, 2) }}</td>
                            <td>
                                @if ($product->isActive())
                                    <span class="badge text-bg-success">Active</span>
                                @else
                                    <span class="badge text-bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ $historyUrl }}" class="btn btn-sm btn-outline-primary" title="Stock history">
                                    <i class="bi bi-clock-history"></i>
                                </a>
                                @if (auth()->user()->hasPermission('manage_products'))
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-secondary" title="Product details">
                                        <i class="bi bi-box-seam"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No inventory records found.</td>
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
