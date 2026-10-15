@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Dashboard</h1>
        <p>Welcome back, {{ auth()->user()->name }}. Here’s today’s inventory snapshot.</p>
    </div>
    <div class="d-flex gap-2">
        @if (auth()->user()->hasPermission('manage_inventory'))
            <a href="{{ route('inventory.index') }}" class="btn btn-primary">
                <i class="bi bi-boxes me-1"></i> Inventory
            </a>
        @endif
        @if (auth()->user()->hasPermission('view_reports'))
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-bar-chart me-1"></i> Reports
            </a>
        @endif
    </div>
</div>

<div class="card stat-card mb-3">
    <div class="card-body">
        <label for="dashboardProductSearch" class="form-label fw-semibold mb-2">
            <i class="bi bi-search me-1"></i> Search products
        </label>
        <div class="product-search" id="dashboardProductSearchWrap">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-upc-scan"></i></span>
                <input
                    type="search"
                    id="dashboardProductSearch"
                    class="form-control"
                    placeholder="{{ setting('product_search_placeholder', 'Search by product name or barcode') }}"
                    autocomplete="off"
                    spellcheck="false"
                >
            </div>
            <div id="dashboardProductSuggestions" class="product-search-suggestions d-none" role="listbox" aria-label="Product suggestions"></div>
            <div id="dashboardProductSearchHint" class="form-text mt-2">Enter at least 3 characters to see matching products.</div>
        </div>
    </div>
</div>

@if ($metrics['low_stock_count'] > 0 || $metrics['out_of_stock_count'] > 0)
    <div class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <strong>Stock alert:</strong>
            {{ $metrics['low_stock_count'] }} low-stock
            @if ($metrics['out_of_stock_count'] > 0)
                · {{ $metrics['out_of_stock_count'] }} out of stock
            @endif
        </div>
        <div class="d-flex gap-2">
            @if (auth()->user()->hasPermission('view_reports'))
                <a href="{{ route('reports.low-stock') }}" class="btn btn-sm btn-outline-dark">Low Stock Report</a>
            @endif
            @if (auth()->user()->hasPermission('manage_inventory'))
                <a href="{{ route('inventory.low-stock') }}" class="btn btn-sm btn-dark">Inventory</a>
            @endif
        </div>
    </div>
@endif

@php
    $user = auth()->user();
    $inventoryValueUrl = $user->hasPermission('manage_inventory')
        ? route('inventory.index')
        : ($user->hasPermission('view_reports') ? route('reports.inventory') : null);
    $lowStockUrl = $user->hasPermission('manage_inventory')
        ? route('inventory.low-stock')
        : ($user->hasPermission('view_reports')
            ? route('reports.low-stock')
            : ($user->hasPermission('manage_products') ? route('products.index', ['low_stock' => 1]) : null));
    $productsUrl = $user->hasPermission('manage_products') ? route('products.index') : null;
    $suppliersUrl = $user->hasPermission('manage_suppliers') ? route('suppliers.index') : null;
    $customersUrl = $user->hasPermission('manage_customers') ? route('customers.index') : null;
@endphp

<div class="row g-3">
    <div class="col-md-6 col-xl-3">
        @if ($inventoryValueUrl)
            <a href="{{ $inventoryValueUrl }}" class="card stat-card stat-card-link h-100">
        @else
            <div class="card stat-card h-100">
        @endif
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info-subtle text-info"><i class="bi bi-boxes"></i></div>
                <div>
                    <div class="text-muted small">Inventory Value</div>
                    <div class="fs-4 fw-semibold">{{ money($metrics['inventory_value']) }}</div>
                    <div class="small text-muted">{{ $metrics['products_count'] }} products</div>
                </div>
            </div>
        @if ($inventoryValueUrl)</a>@else</div>@endif
    </div>
    <div class="col-md-6 col-xl-3">
        @if ($lowStockUrl)
            <a href="{{ $lowStockUrl }}" class="card stat-card stat-card-link h-100">
        @else
            <div class="card stat-card h-100">
        @endif
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <div class="text-muted small">Low Stock</div>
                    <div class="fs-4 fw-semibold">{{ $metrics['low_stock_count'] }}</div>
                    <div class="small text-muted">{{ $metrics['out_of_stock_count'] }} out of stock</div>
                </div>
            </div>
        @if ($lowStockUrl)</a>@else</div>@endif
    </div>
    <div class="col-md-6 col-xl-3">
        @if ($productsUrl)
            <a href="{{ $productsUrl }}" class="card stat-card stat-card-link h-100">
        @else
            <div class="card stat-card h-100">
        @endif
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-box-seam"></i></div>
                <div>
                    <div class="text-muted small">Products</div>
                    <div class="fs-4 fw-semibold">{{ $metrics['products_count'] }}</div>
                </div>
            </div>
        @if ($productsUrl)</a>@else</div>@endif
    </div>
    <div class="col-md-6 col-xl-3">
        @if ($suppliersUrl)
            <a href="{{ $suppliersUrl }}" class="card stat-card stat-card-link h-100">
        @else
            <div class="card stat-card h-100">
        @endif
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-truck"></i></div>
                <div>
                    <div class="text-muted small">Suppliers</div>
                    <div class="fs-4 fw-semibold">{{ $metrics['suppliers_count'] }}</div>
                </div>
            </div>
        @if ($suppliersUrl)</a>@else</div>@endif
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-md-6 col-xl-4">
        @if ($customersUrl)
            <a href="{{ $customersUrl }}" class="card stat-card stat-card-link h-100">
        @else
            <div class="card stat-card h-100">
        @endif
            <div class="card-body">
                <div class="text-muted small">Customers</div>
                <div class="fs-4 fw-semibold">{{ $metrics['customers_count'] }}</div>
            </div>
        @if ($customersUrl)</a>@else</div>@endif
    </div>
    <div class="col-md-6 col-xl-4">
        @if ($lowStockUrl)
            <a href="{{ $lowStockUrl }}" class="card stat-card stat-card-link h-100">
        @else
            <div class="card stat-card h-100">
        @endif
            <div class="card-body">
                <div class="text-muted small">Out of Stock</div>
                <div class="fs-4 fw-semibold">{{ $metrics['out_of_stock_count'] }}</div>
            </div>
        @if ($lowStockUrl)</a>@else</div>@endif
    </div>
    <div class="col-md-6 col-xl-4">
        @if ($lowStockUrl)
            <a href="{{ $lowStockUrl }}" class="card stat-card stat-card-link h-100">
        @else
            <div class="card stat-card h-100">
        @endif
            <div class="card-body">
                <div class="text-muted small">Low Stock Items</div>
                <div class="fs-4 fw-semibold">{{ $metrics['low_stock_count'] }}</div>
            </div>
        @if ($lowStockUrl)</a>@else</div>@endif
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-lg-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Inventory Value by Category</h2>
                @if (count($inventoryChart['labels']) === 0)
                    <div class="text-muted text-center py-5">No inventory data yet.</div>
                @else
                    <canvas id="inventoryChart" height="180"></canvas>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Low Stock Alerts</h2>
                    @if (auth()->user()->hasPermission('manage_inventory'))
                        <a href="{{ route('inventory.low-stock') }}" class="small">View all</a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-end">Stock</th>
                                <th class="text-end">Min</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lowStockProducts as $product)
                                @php
                                    $productUrl = $user->hasPermission('manage_products')
                                        ? route('products.show', $product)
                                        : ($user->hasPermission('manage_inventory') ? route('inventory.show', $product) : null);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            @if ($productUrl)
                                                <a href="{{ $productUrl }}" class="link-body-emphasis text-decoration-none">{{ $product->name }}</a>
                                            @else
                                                {{ $product->name }}
                                            @endif
                                        </div>
                                        <div class="small text-muted">{{ $product->sku }}</div>
                                    </td>
                                    <td class="text-end text-danger fw-semibold">
                                        {{ number_format((float) $product->current_stock, 2) }}
                                        {{ $product->unit?->short_name }}
                                    </td>
                                    <td class="text-end">{{ number_format((float) $product->minimum_stock, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">All stock levels look healthy.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.dashboardCharts = {
    inventory: @json($inventoryChart),
};

document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.initDashboardCharts === 'function') {
        window.initDashboardCharts();
    }

    initDashboardProductSearch();
});

function initDashboardProductSearch() {
    const input = document.getElementById('dashboardProductSearch');
    const list = document.getElementById('dashboardProductSuggestions');
    const hint = document.getElementById('dashboardProductSearchHint');
    const wrap = document.getElementById('dashboardProductSearchWrap');
    if (!input || !list) {
        return;
    }

    const searchUrl = @json(route('dashboard.product-search'));
    let timer = null;
    let requestId = 0;

    const hideSuggestions = () => {
        list.classList.add('d-none');
        list.innerHTML = '';
        delete list.dataset.activeIndex;
    };

    const suggestionItems = () => [...list.querySelectorAll('.product-search-item')];

    const setActiveSuggestion = (index) => {
        const items = suggestionItems();
        if (!items.length) return;
        const next = ((index % items.length) + items.length) % items.length;
        items.forEach((item, i) => item.classList.toggle('is-active', i === next));
        list.dataset.activeIndex = String(next);
        items[next].scrollIntoView({ block: 'nearest' });
    };

    const moveSuggestion = (delta) => {
        if (list.classList.contains('d-none')) return false;
        const items = suggestionItems();
        if (!items.length) return false;
        const current = Number(list.dataset.activeIndex ?? -1);
        setActiveSuggestion(current < 0 ? (delta > 0 ? 0 : items.length - 1) : current + delta);
        return true;
    };

    const activateSelectedSuggestion = () => {
        if (list.classList.contains('d-none')) return false;
        const items = suggestionItems();
        const index = Number(list.dataset.activeIndex ?? -1);
        const item = items[index];
        if (!item) return false;

        if (item.tagName === 'A' && item.href) {
            window.location.href = item.href;
            return true;
        }

        item.click();
        return true;
    };

    const formatMoney = (value) => Number(value || 0).toFixed(2);

    const renderSuggestions = (products) => {
        if (!products.length) {
            list.innerHTML = `<div class="product-search-empty text-muted">No matching products.</div>`;
            list.classList.remove('d-none');
            delete list.dataset.activeIndex;
            return;
        }

        list.innerHTML = products.map((product) => {
            const stockLabel = `${Number(product.stock || 0).toFixed(2)}${product.unit ? ' ' + product.unit : ''}`;
            const sku = String(product.sku || '').trim();
            const barcode = String(product.barcode || '').trim();
            const codeParts = [];
            if (sku && barcode && sku.toLowerCase() === barcode.toLowerCase()) {
                codeParts.push(`Barcode ${barcode}`);
            } else {
                if (sku) codeParts.push(`SKU ${sku}`);
                if (barcode) codeParts.push(`Barcode ${barcode}`);
            }
            const meta = [
                ...codeParts,
                product.category || null,
                product.location ? `Location ${product.location}` : null,
            ].filter(Boolean).join(' · ');

            const content = `
                <div class="product-search-item-main">
                    <div class="fw-semibold text-truncate">${escapeHtml(product.name)}</div>
                    <div class="small text-muted text-truncate">${escapeHtml(meta)}</div>
                </div>
                <div class="product-search-item-side text-end">
                    <div class="small fw-semibold">${escapeHtml(stockLabel)}</div>
                    <div class="small text-muted">${escapeHtml(formatMoney(product.selling_price))}</div>
                </div>
            `;

            if (product.url) {
                return `<a href="${escapeAttr(product.url)}" class="product-search-item" role="option">${content}</a>`;
            }

            return `<div class="product-search-item" role="option">${content}</div>`;
        }).join('');

        suggestionItems().forEach((item, index) => {
            item.addEventListener('mouseenter', () => setActiveSuggestion(index));
        });

        list.classList.remove('d-none');
        setActiveSuggestion(0);
    };

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const escapeAttr = (value) => escapeHtml(value).replaceAll('`', '&#096;');

    const runSearch = async (term) => {
        const currentId = ++requestId;
        hint.textContent = 'Searching…';

        try {
            const response = await fetch(`${searchUrl}?q=${encodeURIComponent(term)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Search failed');
            }

            const data = await response.json();
            if (currentId !== requestId) {
                return;
            }

            renderSuggestions(data.products || []);
            hint.textContent = (data.products || []).length
                ? `${data.products.length} suggestion(s) — ↑↓ to move, Enter to open`
                : 'No matching products.';
        } catch (error) {
            if (currentId !== requestId) {
                return;
            }
            hideSuggestions();
            hint.textContent = 'Unable to search right now.';
        }
    };

    input.addEventListener('input', () => {
        const term = input.value.trim();
        clearTimeout(timer);

        if (term.length < 3) {
            hideSuggestions();
            hint.textContent = 'Enter at least 3 characters to see matching products.';
            return;
        }

        timer = setTimeout(() => runSearch(term), 250);
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowDown') {
            if (moveSuggestion(1)) event.preventDefault();
            return;
        }
        if (event.key === 'ArrowUp') {
            if (moveSuggestion(-1)) event.preventDefault();
            return;
        }
        if (event.key === 'Enter') {
            if (activateSelectedSuggestion()) {
                event.preventDefault();
            }
            return;
        }
        if (event.key === 'Escape') {
            hideSuggestions();
            input.blur();
        }
    });

    document.addEventListener('click', (event) => {
        if (wrap && !wrap.contains(event.target)) {
            hideSuggestions();
        }
    });
}
</script>
@endpush
