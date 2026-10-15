@extends('layouts.app')

@section('title', 'Low Stock Report')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Low Stock Report</h1>
        <p>{{ $products->count() }} product(s) at or below minimum stock.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print / PDF
        </button>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">All Reports</a>
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
                        <th>Supplier</th>
                        <th class="text-end">Current</th>
                        <th class="text-end">Minimum</th>
                        <th class="text-end">Shortfall</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->category?->name ?? '—' }}</td>
                            <td>{{ $product->supplier?->supplier_name ?? '—' }}</td>
                            <td class="text-end">{{ number_format((float) $product->current_stock, 2) }} {{ $product->unit?->short_name }}</td>
                            <td class="text-end">{{ number_format((float) $product->minimum_stock, 2) }}</td>
                            <td class="text-end text-danger fw-semibold">
                                {{ number_format(max(0, (float) $product->minimum_stock - (float) $product->current_stock), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No low-stock products. Inventory looks healthy.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@include('reports._print')
