@if (session('success'))
    <div class="d-none" data-toast="success" data-toast-message="{{ session('success') }}"></div>
@endif

@if (session('error'))
    <div class="d-none" data-toast="error" data-toast-message="{{ session('error') }}"></div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="fw-semibold mb-1"><i class="bi bi-exclamation-circle me-1"></i> Please fix the following:</div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
