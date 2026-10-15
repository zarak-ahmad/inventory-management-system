@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="page-header">
    <h1>Settings</h1>
    <p>Shop information, currency, tax, receipt, and appearance.</p>
</div>

<form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card stat-card mb-3">
                <div class="card-body">
                    <h2 class="h5 mb-3">Shop Information</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="shop_name" class="form-label">Shop Name <span class="text-danger">*</span></label>
                            <input type="text" name="shop_name" id="shop_name" value="{{ old('shop_name', $settings['shop_name']) }}" class="form-control @error('shop_name') is-invalid @enderror" required>
                            @error('shop_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="shop_phone" class="form-label">Phone</label>
                            <input type="text" name="shop_phone" id="shop_phone" value="{{ old('shop_phone', $settings['shop_phone']) }}" class="form-control @error('shop_phone') is-invalid @enderror">
                            @error('shop_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="shop_email" class="form-label">Email</label>
                            <input type="email" name="shop_email" id="shop_email" value="{{ old('shop_email', $settings['shop_email']) }}" class="form-control @error('shop_email') is-invalid @enderror">
                            @error('shop_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="shop_address" class="form-label">Address</label>
                            <input type="text" name="shop_address" id="shop_address" value="{{ old('shop_address', $settings['shop_address']) }}" class="form-control @error('shop_address') is-invalid @enderror">
                            @error('shop_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-8">
                            <label for="shop_logo" class="form-label">Logo</label>
                            <input type="file" name="shop_logo" id="shop_logo" accept="image/*" class="form-control @error('shop_logo') is-invalid @enderror">
                            @error('shop_logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">PNG, JPG, or WEBP. Max 2 MB.</div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            @if (! empty($settings['shop_logo']))
                                <div>
                                    <img src="{{ asset('storage/'.$settings['shop_logo']) }}" alt="Logo" class="img-thumbnail mb-2" style="max-height: 64px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remove_logo" id="remove_logo" value="1" @checked(old('remove_logo'))>
                                        <label class="form-check-label" for="remove_logo">Remove logo</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card stat-card mb-3">
                <div class="card-body">
                    <h2 class="h5 mb-3">Currency &amp; Tax</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="currency" class="form-label">Currency Code <span class="text-danger">*</span></label>
                            <input type="text" name="currency" id="currency" value="{{ old('currency', $settings['currency']) }}" class="form-control @error('currency') is-invalid @enderror" required>
                            @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="currency_symbol" class="form-label">Currency Symbol <span class="text-danger">*</span></label>
                            <input type="text" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol']) }}" class="form-control @error('currency_symbol') is-invalid @enderror" required>
                            @error('currency_symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="tax_rate" class="form-label">Default Tax Rate (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" max="100" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', $settings['tax_rate']) }}" class="form-control @error('tax_rate') is-invalid @enderror" required>
                            @error('tax_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card stat-card mb-3">
                <div class="card-body">
                    <h2 class="h5 mb-3">Receipt Settings</h2>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="receipt_header" class="form-label">Receipt Header</label>
                            <textarea name="receipt_header" id="receipt_header" rows="2" class="form-control @error('receipt_header') is-invalid @enderror">{{ old('receipt_header', $settings['receipt_header']) }}</textarea>
                            @error('receipt_header') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label for="receipt_footer" class="form-label">Receipt Footer</label>
                            <textarea name="receipt_footer" id="receipt_footer" rows="2" class="form-control @error('receipt_footer') is-invalid @enderror">{{ old('receipt_footer', $settings['receipt_footer']) }}</textarea>
                            @error('receipt_footer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card stat-card mb-3">
                <div class="card-body">
                    <h2 class="h5 mb-3">Date, Time &amp; Theme</h2>
                    <div class="mb-3">
                        <label for="date_format" class="form-label">Date Format <span class="text-danger">*</span></label>
                        <select name="date_format" id="date_format" class="form-select @error('date_format') is-invalid @enderror" required>
                            @foreach (['Y-m-d' => '2026-07-30', 'd-m-Y' => '30-07-2026', 'm/d/Y' => '07/30/2026', 'd M Y' => '30 Jul 2026'] as $format => $example)
                                <option value="{{ $format }}" @selected(old('date_format', $settings['date_format']) === $format)>{{ $example }} ({{ $format }})</option>
                            @endforeach
                        </select>
                        @error('date_format') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="timezone" class="form-label">Timezone <span class="text-danger">*</span></label>
                        <select name="timezone" id="timezone" class="form-select @error('timezone') is-invalid @enderror" required>
                            @foreach ($timezones as $timezone)
                                <option value="{{ $timezone }}" @selected(old('timezone', $settings['timezone']) === $timezone)>{{ $timezone }}</option>
                            @endforeach
                        </select>
                        @error('timezone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-0">
                        <label for="theme" class="form-label">Theme <span class="text-danger">*</span></label>
                        <select name="theme" id="theme" class="form-select @error('theme') is-invalid @enderror" required>
                            <option value="light" @selected(old('theme', $settings['theme']) === 'light')>Light</option>
                            <option value="dark" @selected(old('theme', $settings['theme']) === 'dark')>Dark</option>
                        </select>
                        @error('theme') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="card stat-card mb-3">
                <div class="card-body">
                    <h2 class="h5 mb-3">Dashboard Search</h2>
                    <div class="mb-0">
                        <label for="product_search_placeholder" class="form-label">Product Search Placeholder <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            name="product_search_placeholder"
                            id="product_search_placeholder"
                            value="{{ old('product_search_placeholder', $settings['product_search_placeholder']) }}"
                            class="form-control @error('product_search_placeholder') is-invalid @enderror"
                            maxlength="120"
                            required
                        >
                        @error('product_search_placeholder') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Shown in the dashboard product search box.</div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-1"></i> Save Settings
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
