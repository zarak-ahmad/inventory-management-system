@php
    $isWalkIn = isset($customer) && $customer->isWalkIn();
@endphp

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            id="name"
            value="{{ old('name', $customer->name ?? '') }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            autofocus
            @disabled($isWalkIn)
        >
        @if ($isWalkIn)
            <input type="hidden" name="name" value="{{ \App\Models\Customer::WALK_IN_NAME }}">
            <div class="form-text">Walk-in Customer name cannot be changed.</div>
        @endif
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required @disabled($isWalkIn)>
            <option value="active" @selected(old('status', $customer->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $customer->status ?? '') === 'inactive')>Inactive</option>
        </select>
        @if ($isWalkIn)
            <input type="hidden" name="status" value="active">
            <div class="form-text">Walk-in Customer must remain active.</div>
        @endif
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label">Phone</label>
        <input
            type="text"
            name="phone"
            id="phone"
            value="{{ old('phone', $customer->phone ?? '') }}"
            class="form-control @error('phone') is-invalid @enderror"
        >
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input
            type="email"
            name="email"
            id="email"
            value="{{ old('email', $customer->email ?? '') }}"
            class="form-control @error('email') is-invalid @enderror"
        >
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label for="address" class="form-label">Address</label>
        <textarea
            name="address"
            id="address"
            rows="3"
            class="form-control @error('address') is-invalid @enderror"
        >{{ old('address', $customer->address ?? '') }}</textarea>
        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
