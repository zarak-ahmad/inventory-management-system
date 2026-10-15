@extends('layouts.app')

@section('title', 'Edit Unit')

@section('content')
<div class="page-header">
    <h1>Edit Unit</h1>
    <p>Update unit name and short name.</p>
</div>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('units.update', $unit) }}" novalidate>
            @csrf
            @method('PUT')
            @include('units._form')

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i> Update
                </button>
                <a href="{{ route('units.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
