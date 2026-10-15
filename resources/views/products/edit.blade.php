@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="page-header">
    <h1>Edit Product</h1>
    <p>Update product details, pricing, and stock thresholds.</p>
</div>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')
            @include('products._form')

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i> Update
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
