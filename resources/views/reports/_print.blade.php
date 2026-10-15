@push('styles')
<style>
@media print {
    .sidebar, .top-navbar, .app-footer, .page-header .btn, .no-print, .btn { display: none !important; }
    .content-area { padding: 0 !important; }
    .app-main { width: 100% !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endpush
