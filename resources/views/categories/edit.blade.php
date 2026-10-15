@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="page-header">
    <h1>Edit Category</h1>
    <p>Update category details and status.</p>
</div>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('categories.update', $category) }}" novalidate>
            @csrf
            @method('PUT')
            @include('categories._form')

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i> Update
                </button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
