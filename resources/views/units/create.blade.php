@extends('layouts.app')

@section('title', 'Add Unit')

@section('content')
<div class="page-header">
    <h1>Add Unit</h1>
    <p>Create a new measurement unit.</p>
</div>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('units.store') }}" novalidate>
            @csrf
            @include('units._form')

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Save
                </button>
                <a href="{{ route('units.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
