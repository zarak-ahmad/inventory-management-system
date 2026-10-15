@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Sales Report</h1>
        <p>{{ $periodLabel }}</p>
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
        <form method="GET" action="{{ route('reports.sales') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="date_from" class="form-label">From</label>
                <input type="date" name="date_from" id="date_from" value="{{ $date_from }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">To</label>
                <input type="date" name="date_to" id="date_to" value="{{ $date_to }}" class="form-control">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="{{ route('reports.sales') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><div class="text-muted small">Invoices</div><div class="fs-4 fw-semibold">{{ $summary['count'] }}</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><div class="text-muted small">Subtotal</div><div class="fs-4 fw-semibold">{{ number_format($summary['subtotal'], 2) }}</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><div class="text-muted small">Discount / Tax</div><div class="fs-5 fw-semibold">{{ number_format($summary['discount'], 2) }} / {{ number_format($summary['tax'], 2) }}</div></div></div></div>
    <div class="col-md-3"><div class="card stat-card"><div class="card-body"><div class="text-muted small">Total Sales</div><div class="fs-4 fw-semibold">{{ number_format($summary['total'], 2) }}</div></div></div></div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Daily Sales</h2>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>Date</th><th class="text-end">Count</th><th class="text-end">Total</th></tr></thead>
                        <tbody>
                            @forelse ($daily as $row)
                                <tr>
                                    <td>{{ $row->period }}</td>
                                    <td class="text-end">{{ $row->count }}</td>
                                    <td class="text-end">{{ number_format((float) $row->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No daily data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Monthly Sales</h2>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>Month</th><th class="text-end">Count</th><th class="text-end">Total</th></tr></thead>
                        <tbody>
                            @forelse ($monthly as $row)
                                <tr>
                                    <td>{{ $row->period }}</td>
                                    <td class="text-end">{{ $row->count }}</td>
                                    <td class="text-end">{{ number_format((float) $row->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No monthly data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body">
        <h2 class="h5 mb-3">Invoice Detail</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Cashier</th>
                        <th>Payment</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td class="fw-semibold">{{ $sale->invoice_number }}</td>
                            <td>{{ $sale->created_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $sale->customer?->name ?? '—' }}</td>
                            <td>{{ $sale->user?->name ?? '—' }}</td>
                            <td>{{ ucfirst($sale->payment_method) }}</td>
                            <td class="text-end">{{ number_format((float) $sale->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No sales in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@include('reports._print')
