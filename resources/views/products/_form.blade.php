@php
    $isEdit = isset($product);
@endphp

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required autofocus>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="sku" class="form-label">SKU @unless($isEdit)<span class="text-muted">(optional)</span>@else<span class="text-danger">*</span>@endunless</label>
        <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku ?? '') }}" class="form-control @error('sku') is-invalid @enderror" @if($isEdit) required @endif>
        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="barcode" class="form-label">Barcode @unless($isEdit)<span class="text-muted">(optional)</span>@else<span class="text-danger">*</span>@endunless</label>
        <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $product->barcode ?? '') }}" class="form-control @error('barcode') is-invalid @enderror" @if($isEdit) required @endif>
        @error('barcode') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">Select category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="brand_id" class="form-label">Brand</label>
        <select name="brand_id" id="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
            <option value="">None</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id ?? '') == $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
        @error('brand_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
        <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
            <option value="">Select supplier</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $product->supplier_id ?? '') == $supplier->id)>{{ $supplier->supplier_name }}</option>
            @endforeach
        </select>
        @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
        <select name="unit_id" id="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
            <option value="">Select unit</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}" @selected(old('unit_id', $product->unit_id ?? '') == $unit->id)>{{ $unit->name }} ({{ $unit->short_name }})</option>
            @endforeach
        </select>
        @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="purchase_price" class="form-label">Purchase Price <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $product->purchase_price ?? '0') }}" class="form-control @error('purchase_price') is-invalid @enderror" required>
        @error('purchase_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="selling_price" class="form-label">Selling Price <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price ?? '0') }}" class="form-control @error('selling_price') is-invalid @enderror" required>
        @error('selling_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="tax" class="form-label">Tax (%)</label>
        <input type="number" step="0.01" min="0" name="tax" id="tax" value="{{ old('tax', $product->tax ?? '0') }}" class="form-control @error('tax') is-invalid @enderror">
        @error('tax') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="discount" class="form-label">Discount</label>
        <input type="number" step="0.01" min="0" name="discount" id="discount" value="{{ old('discount', $product->discount ?? '0') }}" class="form-control @error('discount') is-invalid @enderror">
        @error('discount') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="current_stock" class="form-label">Current Stock</label>
        <input type="number" step="0.01" min="0" name="current_stock" id="current_stock" value="{{ old('current_stock', $product->current_stock ?? '0') }}" class="form-control @error('current_stock') is-invalid @enderror">
        @error('current_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="minimum_stock" class="form-label">Minimum Stock</label>
        <input type="number" step="0.01" min="0" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock ?? '0') }}" class="form-control @error('minimum_stock') is-invalid @enderror">
        @error('minimum_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="maximum_stock" class="form-label">Maximum Stock</label>
        <input type="number" step="0.01" min="0" name="maximum_stock" id="maximum_stock" value="{{ old('maximum_stock', $product->maximum_stock ?? '') }}" class="form-control @error('maximum_stock') is-invalid @enderror">
        @error('maximum_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="active" @selected(old('status', $product->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $product->status ?? '') === 'inactive')>Inactive</option>
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="expiry_date" class="form-label">Expiry Date</label>
        <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', isset($product) && $product->expiry_date ? $product->expiry_date->format('Y-m-d') : '') }}" class="form-control @error('expiry_date') is-invalid @enderror">
        @error('expiry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="image" class="form-label">Product Image</label>
        <input type="file" name="image" id="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @if ($isEdit && $product->imageUrl())
            <div class="mt-2 d-flex align-items-center gap-3">
                <img src="{{ $product->imageUrl() }}" alt="" width="64" height="64" class="rounded border object-fit-cover">
                <div class="form-check">
                    <input type="checkbox" name="remove_image" value="1" id="remove_image" class="form-check-input" @checked(old('remove_image'))>
                    <label for="remove_image" class="form-check-label">Remove current image</label>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-6">
        <label for="location" class="form-label">Physical Location</label>
        <input
            type="text"
            name="location"
            id="location"
            value="{{ old('location', $product->location ?? '') }}"
            class="form-control @error('location') is-invalid @enderror"
            placeholder="e.g. Warehouse A · Aisle 3 · Shelf B2 · Bin 14"
            maxlength="255"
        >
        <div class="form-text">Exact place where this product is stored (warehouse, aisle, shelf, bin, etc.).</div>
        @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
