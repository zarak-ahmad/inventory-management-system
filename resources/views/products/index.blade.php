@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Products</h1>
        <p>Manage product catalog, stock levels, SKU, and barcodes.</p>
    </div>
    <div class="d-flex gap-2">
        @if ($lowStockCount > 0 && ! $trashed)
            <a href="{{ route('products.index', ['low_stock' => 1]) }}" class="btn btn-warning">
                <i class="bi bi-exclamation-triangle me-1"></i> Low Stock ({{ $lowStockCount }})
            </a>
        @endif
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Product
        </a>
    </div>
</div>

<div class="card stat-card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('products.index') }}" class="row g-2 align-items-end">
            @if ($trashed)
                <input type="hidden" name="trashed" value="1">
            @endif
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
                <label for="brand_id" class="form-label">Brand</label>
                <select name="brand_id" id="brand_id" class="form-select">
                    <option value="">All</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @selected(request('brand_id') == $brand->id)>{{ $brand->name }}</option>
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
                <select name="status" id="status" class="form-select" @disabled($trashed)>
                    <option value="">All</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 form-check mt-4">
                <input type="checkbox" name="low_stock" value="1" id="low_stock" class="form-check-input" @checked(request()->boolean('low_stock')) @disabled($trashed)>
                <label for="low_stock" class="form-check-label">Low stock only</label>
            </div>
            <div class="col-md-9 d-flex gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('products.index', $trashed ? ['trashed' => 1] : []) }}" class="btn btn-outline-secondary">Clear</a>
                @if ($trashed)
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">Active Products</a>
                @else
                    <a href="{{ route('products.index', ['trashed' => 1]) }}" class="btn btn-outline-danger">Trash</a>
                @endif
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
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($product->imageUrl())
                                        <img src="{{ $product->imageUrl() }}" alt="" width="40" height="40" class="rounded object-fit-cover">
                                    @else
                                        <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                            <i class="bi bi-box-seam text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        <small class="text-muted">{{ $product->barcode }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->category?->name ?? '—' }}</td>
                            <td>
                                {{ number_format((float) $product->current_stock, 2) }}
                                {{ $product->unit?->short_name }}
                                @if ($product->isOutOfStock())
                                    <span class="badge text-bg-danger">Out</span>
                                @elseif ($product->isLowStock())
                                    <span class="badge text-bg-warning text-dark">Low</span>
                                @endif
                            </td>
                            <td>{{ number_format((float) $product->selling_price, 2) }}</td>
                            <td>
                                @if ($trashed)
                                    <span class="badge text-bg-secondary">Trashed</span>
                                @elseif ($product->isActive())
                                    <span class="badge text-bg-success">Active</span>
                                @else
                                    <span class="badge text-bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if ($trashed)
                                    <form action="{{ route('products.restore', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-secondary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" data-confirm="Move this product to trash?">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No products found.
                                @unless ($trashed)
                                    <div class="mt-2">
                                        <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">Add Product</a>
                                    </div>
                                @endunless
                            </td>
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
