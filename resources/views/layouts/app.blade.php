<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="{{ setting('theme', 'light') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ shop_name() }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        <div id="sidebarBackdrop" class="sidebar-backdrop"></div>
        <div id="appLoadingOverlay" class="app-loading-overlay" aria-live="polite" aria-busy="true">
            <div class="spinner-panel">
                <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
                <span>Please wait…</span>
            </div>
        </div>

        <div class="app-body">
            @include('partials.sidebar')

            <div class="app-main">
                @include('partials.navbar')

                <main class="content-area">
                    @include('partials.alerts')
                    @yield('content')
                </main>

                @include('partials.footer')
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
