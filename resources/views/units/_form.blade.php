<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            id="name"
            value="{{ old('name', $unit->name ?? '') }}"
            class="form-control @error('name') is-invalid @enderror"
            placeholder="e.g. Piece, Kilogram, Box"
            required
            autofocus
        >
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="short_name" class="form-label">Short Name <span class="text-danger">*</span></label>
        <input
            type="text"
            name="short_name"
            id="short_name"
            value="{{ old('short_name', $unit->short_name ?? '') }}"
            class="form-control @error('short_name') is-invalid @enderror"
            placeholder="e.g. pc, kg, box"
            required
        >
        @error('short_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
