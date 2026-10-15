@extends('layouts.app')

@section('title', 'Add Category')

@section('content')
<div class="page-header">
    <h1>Add Category</h1>
    <p>Create a new product category.</p>
</div>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('categories.store') }}" novalidate>
            @csrf
            @include('categories._form')

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Save
                </button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
