@php
    $isEdit = isset($user);
@endphp

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" class="form-control @error('email') is-invalid @enderror" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone ?? '') }}" class="form-control @error('phone') is-invalid @enderror">
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
        <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
            <option value="">Select role</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id ?? '') == $role->id)>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
        @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="active" @selected(old('status', $user->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $user->status ?? '') === 'inactive')>Inactive</option>
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="password" class="form-label">
            Password
            @if (!$isEdit)
                <span class="text-danger">*</span>
            @else
                <span class="text-muted">(leave blank to keep current)</span>
            @endif
        </label>
        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" @if(!$isEdit) required @endif autocomplete="new-password">
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="password_confirmation" class="form-label">Confirm Password @if(!$isEdit)<span class="text-danger">*</span>@endif</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" @if(!$isEdit) required @endif autocomplete="new-password">
    </div>
</div>
