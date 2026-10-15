@extends('layouts.app')

@section('title', 'Customer Report')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Customer Report</h1>
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
        <form method="GET" action="{{ route('reports.customers') }}" class="row g-2 align-items-end">
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
                <a href="{{ route('reports.customers') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="alert alert-light border d-flex justify-content-between">
    <span>Customers with sales</span>
    <strong>{{ $customers->count() }} · Total {{ number_format($grandTotal, 2) }}</strong>
</div>

<div class="card stat-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th class="text-end">Invoices</th>
                        <th class="text-end">Total Sales</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td class="fw-semibold">{{ $customer->name }}</td>
                            <td>{{ $customer->phone ?: '—' }}</td>
                            <td class="text-end">{{ $customer->sales_count }}</td>
                            <td class="text-end">{{ number_format((float) $customer->sales_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No customer sales in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@include('reports._print')
