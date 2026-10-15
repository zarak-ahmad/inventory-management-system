@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="page-header">
    <h1>Reports</h1>
    <p>Sales, inventory, and stock reports. Use Print → Save as PDF for exports.</p>
</div>

<div class="row g-3">
    @php
        $cards = [
            ['route' => 'reports.sales', 'icon' => 'bi-cart-check', 'title' => 'Sales Report', 'desc' => 'Daily and monthly sales totals with invoice detail.'],
            ['route' => 'reports.inventory', 'icon' => 'bi-boxes', 'title' => 'Inventory / Stock Value', 'desc' => 'On-hand stock with cost and retail value.'],
            ['route' => 'reports.low-stock', 'icon' => 'bi-exclamation-triangle', 'title' => 'Low Stock Report', 'desc' => 'Products at or below minimum stock.'],
            ['route' => 'reports.customers', 'icon' => 'bi-people', 'title' => 'Customer Report', 'desc' => 'Sales totals by customer for the period.'],
        ];
    @endphp

    @foreach ($cards as $card)
        <div class="col-md-6 col-xl-4">
            <a href="{{ route($card['route']) }}" class="text-decoration-none text-body">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                            <div class="fs-3 text-primary"><i class="bi {{ $card['icon'] }}"></i></div>
                            <div>
                                <h2 class="h5 mb-1">{{ $card['title'] }}</h2>
                                <p class="text-muted mb-0 small">{{ $card['desc'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
@endsection
