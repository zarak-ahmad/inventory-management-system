@extends('layouts.app')

@section('title', 'Brands')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <h1>Brands</h1>
        <p>Manage product brands.</p>
    </div>
    <a href="{{ route('brands.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Brand
    </a>
</div>

<div class="card stat-card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('brands.index') }}" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" value="{{ $search }}" class="form-control" placeholder="Name or description">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('brands.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($brands as $brand)
                        <tr>
                            <td class="fw-semibold">{{ $brand->name }}</td>
                            <td>{{ $brand->description ? \Illuminate\Support\Str::limit($brand->description, 60) : '—' }}</td>
                            <td>
                                @if ($brand->isActive())
                                    <span class="badge text-bg-success">Active</span>
                                @else
                                    <span class="badge text-bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $brand->created_at?->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <a href="{{ route('brands.show', $brand) }}" class="btn btn-sm btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('brands.edit', $brand) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('brands.destroy', $brand) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" data-confirm="Delete this brand?">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No brands found.
                                <div class="mt-2">
                                    <a href="{{ route('brands.create') }}" class="btn btn-sm btn-primary">Add Brand</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($brands->hasPages())
            <div class="mt-3">
                {{ $brands->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
