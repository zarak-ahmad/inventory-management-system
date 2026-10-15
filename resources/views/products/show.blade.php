@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>{{ $product->name }}</h1>
        <p>Product details, pricing, and stock information.</p>
    </div>
    <div class="d-flex gap-2">
        @if (auth()->user()->hasPermission('manage_sales'))
            <a href="{{ route('pos.index', ['product_id' => $product->id]) }}" class="btn btn-success">
                        <i class="bi bi-cart-plus me-1"></i> Add to POS Cart
                    </a>
        @endif
        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                @if ($product->imageUrl())
                    <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="img-fluid rounded mb-3" style="max-height: 220px;">
                @else
                    <div class="bg-light border rounded d-flex align-items-center justify-content-center mb-3 mx-auto" style="width:180px;height:180px;">
                        <i class="bi bi-box-seam display-4 text-muted"></i>
                    </div>
                @endif
                <div class="fw-semibold">{{ $product->name }}</div>
                <div class="text-muted small">{{ $product->sku }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card stat-card mb-3">
            <div class="card-body">
                <h2 class="h5 mb-3">Details</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Barcode</dt>
                    <dd class="col-sm-8"><code>{{ $product->barcode }}</code></dd>

                    <dt class="col-sm-4">Category</dt>
                    <dd class="col-sm-8">{{ $product->category?->name ?? '—' }}</dd>

                    <dt class="col-sm-4">Brand</dt>
                    <dd class="col-sm-8">{{ $product->brand?->name ?? '—' }}</dd>

                    <dt class="col-sm-4">Supplier</dt>
                    <dd class="col-sm-8">{{ $product->supplier?->supplier_name ?? '—' }}</dd>

                    <dt class="col-sm-4">Unit</dt>
                    <dd class="col-sm-8">{{ $product->unit?->name ?? '—' }} ({{ $product->unit?->short_name }})</dd>

                    <dt class="col-sm-4">Physical Location</dt>
                    <dd class="col-sm-8">
                        @if ($product->location)
                            <span class="d-inline-flex align-items-center gap-2">
                                <i class="bi bi-geo-alt-fill text-primary"></i>
                                <span class="fw-semibold">{{ $product->location }}</span>
                            </span>
                        @else
                            <span class="text-muted">Not set</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        @if ($product->isActive())
                            <span class="badge text-bg-success">Active</span>
                        @else
                            <span class="badge text-bg-secondary">Inactive</span>
                        @endif
                        @if ($product->isOutOfStock())
                            <span class="badge text-bg-danger">Out of Stock</span>
                        @elseif ($product->isLowStock())
                            <span class="badge text-bg-warning text-dark">Low Stock</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Expiry</dt>
                    <dd class="col-sm-8">{{ $product->expiry_date?->format('Y-m-d') ?: '—' }}</dd>

                    <dt class="col-sm-4">Description</dt>
                    <dd class="col-sm-8">{{ $product->description ?: '—' }}</dd>
                </dl>
            </div>
        </div>

        <div class="card stat-card mb-3">
            <div class="card-body">
                <h2 class="h5 mb-2">
                    <i class="bi bi-geo-alt me-1"></i> Location
                </h2>
                <p class="text-muted small mb-3">Exact physical storage place for this product.</p>
                @if ($product->location)
                    <div class="fs-5 fw-semibold">{{ $product->location }}</div>
                @else
                    <div class="text-muted">No location recorded. Edit this product to set warehouse, aisle, shelf, or bin.</div>
                @endif
            </div>
        </div>

        <div class="card stat-card">
            <div class="card-body">
                <h2 class="h5 mb-3">Pricing & Stock</h2>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-muted small">Purchase Price</div>
                        <div class="fs-5 fw-semibold">{{ number_format((float) $product->purchase_price, 2) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Selling Price</div>
                        <div class="fs-5 fw-semibold">{{ number_format((float) $product->selling_price, 2) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Tax / Discount</div>
                        <div class="fs-5 fw-semibold">{{ number_format((float) $product->tax, 2) }}% / {{ number_format((float) $product->discount, 2) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Current Stock</div>
                        <div class="fs-5 fw-semibold">{{ number_format((float) $product->current_stock, 2) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Minimum Stock</div>
                        <div class="fs-5 fw-semibold">{{ number_format((float) $product->minimum_stock, 2) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Maximum Stock</div>
                        <div class="fs-5 fw-semibold">{{ $product->maximum_stock !== null ? number_format((float) $product->maximum_stock, 2) : '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
