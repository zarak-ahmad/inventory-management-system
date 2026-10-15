@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
<div class="page-header">
    <h1>Add Product</h1>
    <p>Create a new product. SKU and barcode are generated automatically if left blank.</p>
</div>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" novalidate>
            @csrf
            @include('products._form')

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Save
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
