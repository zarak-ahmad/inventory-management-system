<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="supplier_name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
        <input
            type="text"
            name="supplier_name"
            id="supplier_name"
            value="{{ old('supplier_name', $supplier->supplier_name ?? '') }}"
            class="form-control @error('supplier_name') is-invalid @enderror"
            required
            autofocus
        >
        @error('supplier_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="company_name" class="form-label">Company</label>
        <input
            type="text"
            name="company_name"
            id="company_name"
            value="{{ old('company_name', $supplier->company_name ?? '') }}"
            class="form-control @error('company_name') is-invalid @enderror"
        >
        @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
        <input
            type="text"
            name="phone"
            id="phone"
            value="{{ old('phone', $supplier->phone ?? '') }}"
            class="form-control @error('phone') is-invalid @enderror"
            required
        >
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="email" class="form-label">Email</label>
        <input
            type="email"
            name="email"
            id="email"
            value="{{ old('email', $supplier->email ?? '') }}"
            class="form-control @error('email') is-invalid @enderror"
        >
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="active" @selected(old('status', $supplier->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $supplier->status ?? '') === 'inactive')>Inactive</option>
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label for="address" class="form-label">Address</label>
        <textarea
            name="address"
            id="address"
            rows="3"
            class="form-control @error('address') is-invalid @enderror"
        >{{ old('address', $supplier->address ?? '') }}</textarea>
        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
