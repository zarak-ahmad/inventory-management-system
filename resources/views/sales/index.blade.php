@extends('layouts.app')

@section('title', 'Sales History')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Sales History</h1>
        <p>Completed POS invoices and payments.</p>
    </div>
    <a href="{{ route('pos.index') }}" class="btn btn-primary">
        <i class="bi bi-cart-check me-1"></i> Open POS
    </a>
</div>

<div class="card stat-card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('sales.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" value="{{ $search }}" class="form-control" placeholder="Invoice # or customer">
            </div>
            <div class="col-md-2">
                <label for="payment_method" class="form-label">Payment</label>
                <select name="payment_method" id="payment_method" class="form-select">
                    <option value="">All</option>
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method }}" @selected(request('payment_method') === $method)>{{ ucfirst($method) }}</option>
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
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Clear</a>
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
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Cashier</th>
                        <th>Payment</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td class="fw-semibold">{{ $sale->invoice_number }}</td>
                            <td>{{ $sale->created_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $sale->customer?->name ?? '—' }}</td>
                            <td>{{ $sale->user?->name ?? '—' }}</td>
                            <td>
                                <span class="badge text-bg-light border">{{ ucfirst($sale->payment_method) }}</span>
                                <span class="badge {{ $sale->payment_status === 'paid' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ ucfirst($sale->payment_status) }}
                                </span>
                            </td>
                            <td class="text-end">{{ number_format((float) $sale->total, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-receipt"></i> Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No sales found.
                                <div class="mt-2">
                                    <a href="{{ route('pos.index') }}" class="btn btn-sm btn-primary">Open POS</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($sales->hasPages())
            <div class="mt-3">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
