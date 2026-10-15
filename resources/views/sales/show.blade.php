@extends('layouts.app')

@section('title', 'Sale Receipt')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Sale Receipt</h1>
        <p>{{ $sale->invoice_number }}</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print
        </button>
        <a href="{{ route('pos.index') }}" class="btn btn-primary">New Sale</a>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">History</a>
    </div>
</div>

<div class="card stat-card" id="receiptCard">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                @if (setting('shop_logo'))
                    <img src="{{ asset('storage/'.setting('shop_logo')) }}" alt="{{ shop_name() }}" class="mb-2" style="max-height: 56px;">
                @endif
                <h2 class="h5 mb-1">{{ shop_name() }}</h2>
                @if (setting('receipt_header'))
                    <div class="text-muted small">{{ setting('receipt_header') }}</div>
                @endif
                @if (setting('shop_address') || setting('shop_phone'))
                    <div class="text-muted small">
                        @if (setting('shop_address')){{ setting('shop_address') }}<br>@endif
                        @if (setting('shop_phone')){{ setting('shop_phone') }}@endif
                    </div>
                @endif
                <div class="text-muted">Sales Receipt</div>
            </div>
            <div class="col-md-6 text-md-end">
                <div><strong>Invoice:</strong> {{ $sale->invoice_number }}</div>
                <div><strong>Date:</strong> {{ $sale->created_at?->format(setting('date_format', 'Y-m-d').' H:i') }}</div>
                <div><strong>Cashier:</strong> {{ $sale->user?->name }}</div>
                <div><strong>Customer:</strong> {{ $sale->customer?->name }}</div>
                <div><strong>Payment:</strong> {{ ucfirst($sale->payment_method) }} ({{ ucfirst($sale->payment_status) }})</div>
            </div>
        </div>

        <div class="table-responsive mb-3">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                {{ $item->product?->name }}
                                <div class="text-muted small">{{ $item->product?->sku }}</div>
                            </td>
                            <td class="text-end">{{ number_format((float) $item->quantity, 2) }} {{ $item->product?->unit?->short_name }}</td>
                            <td class="text-end">{{ number_format((float) $item->selling_price, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="d-flex justify-content-between"><span>Subtotal</span><strong>{{ money($sale->subtotal) }}</strong></div>
                <div class="d-flex justify-content-between"><span>Discount</span><strong>{{ money($sale->discount) }}</strong></div>
                <div class="d-flex justify-content-between"><span>Tax</span><strong>{{ money($sale->tax) }}</strong></div>
                <hr>
                <div class="d-flex justify-content-between fs-5"><span>Total</span><strong>{{ money($sale->total) }}</strong></div>
            </div>
        </div>

        @if ($sale->notes)
            <div class="mt-3">
                <strong>Notes:</strong>
                <div class="text-muted">{{ $sale->notes }}</div>
            </div>
        @endif

        <div class="text-center text-muted mt-4 small">{{ setting('receipt_footer', 'Thank you for your purchase.') }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Successful checkout left POS — start the next sale with an empty cart.
try {
    sessionStorage.removeItem('pos_cart_v1');
} catch (error) {
    // Ignore storage errors.
}
</script>
@endpush

@push('styles')
<style>
@media print {
    .sidebar, .top-navbar, .app-footer, .page-header .btn, .btn { display: none !important; }
    .content-area { padding: 0 !important; }
    .app-main { width: 100% !important; }
}
</style>
@endpush
