@extends('layouts.app')

@section('title', 'New Stock Adjustment')

@section('content')
<div class="page-header">
    <h1>New Stock Adjustment</h1>
    <p>Increase or decrease stock with a required reason. Inventory history is updated automatically.</p>
</div>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('stock-adjustments.store') }}" novalidate>
            @csrf

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                        <option value="">Select product</option>
                        @foreach ($products as $product)
                            <option
                                value="{{ $product->id }}"
                                data-stock="{{ $product->current_stock }}"
                                data-unit="{{ $product->unit?->short_name }}"
                                @selected(old('product_id') == $product->id)
                            >
                                {{ $product->name }} ({{ $product->sku }}) — Stock: {{ number_format((float) $product->current_stock, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text" id="currentStockHint">Select a product to see current stock.</div>
                </div>

                <div class="col-md-3">
                    <label for="adjustment_type" class="form-label">Adjustment Type <span class="text-danger">*</span></label>
                    <select name="adjustment_type" id="adjustment_type" class="form-select @error('adjustment_type') is-invalid @enderror" required>
                        @foreach ($types as $type)
                            <option value="{{ $type }}" @selected(old('adjustment_type') === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                    @error('adjustment_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="quantity" id="quantity" value="{{ old('quantity') }}" class="form-control @error('quantity') is-invalid @enderror" required>
                    @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                    <textarea name="reason" id="reason" rows="3" class="form-control @error('reason') is-invalid @enderror" required placeholder="Explain why this adjustment is needed">{{ old('reason') }}</textarea>
                    @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" data-confirm="Apply this stock adjustment?">
                    <i class="bi bi-check-lg me-1"></i> Save Adjustment
                </button>
                <a href="{{ route('stock-adjustments.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const productSelect = document.getElementById('product_id');
    const hint = document.getElementById('currentStockHint');

    const updateHint = () => {
        const option = productSelect.selectedOptions[0];
        if (!option || !option.value) {
            hint.textContent = 'Select a product to see current stock.';
            return;
        }
        hint.textContent = `Current stock: ${option.dataset.stock} ${option.dataset.unit || ''}`.trim();
    };

    productSelect.addEventListener('change', updateHint);
    updateHint();
});
</script>
@endpush
