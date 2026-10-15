@extends('layouts.app')

@section('title', 'Inventory Transactions')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Inventory Transactions</h1>
        <p>Complete stock movement history across adjustments and other inventory events.</p>
    </div>
    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Back to Inventory</a>
</div>

<div class="card stat-card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('inventory.transactions') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" value="{{ $search }}" class="form-control" placeholder="Product, SKU, remarks">
            </div>
            <div class="col-md-3">
                <label for="product_id" class="form-label">Product</label>
                <select name="product_id" id="product_id" class="form-select">
                    <option value="">All products</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>{{ $product->name }} ({{ $product->sku }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="transaction_type" class="form-label">Type</label>
                <select name="transaction_type" id="transaction_type" class="form-select">
                    <option value="">All types</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" @selected(request('transaction_type') === $type)>{{ ucfirst($type) }}</option>
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
                <a href="{{ route('inventory.transactions') }}" class="btn btn-outline-secondary">Clear</a>
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
                        <th>Remarks</th>
                        <th>By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="fw-semibold">{{ $transaction->product?->name ?? '—' }}</div>
                                <small class="text-muted">{{ $transaction->product?->sku }}</small>
                            </td>
                            <td>
                                @php $type = $transaction->transaction_type; @endphp
                                <span class="badge
                                    @if($type === 'purchase') text-bg-success
                                    @elseif($type === 'sale') text-bg-primary
                                    @elseif($type === 'adjustment') text-bg-warning text-dark
                                    @else text-bg-secondary
                                    @endif">
                                    {{ ucfirst($type) }}
                                </span>
                            </td>
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
                            <td colspan="8" class="text-center text-muted py-4">No inventory transactions found.</td>
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
