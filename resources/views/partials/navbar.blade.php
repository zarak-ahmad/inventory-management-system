<header class="top-navbar">
    <div class="d-flex align-items-center gap-3 min-w-0">
        <button type="button" class="btn btn-outline-secondary btn-sm d-lg-none" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>
        <div class="min-w-0">
            <div class="fw-semibold text-truncate">{{ shop_name() }}</div>
            <small class="text-muted">Offline Inventory Management</small>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 gap-md-3">
        @php
            $lowStockNavCount = \App\Models\Product::query()->lowStock()->count();
        @endphp
        @if (auth()->user()->hasPermission('manage_inventory') || auth()->user()->hasPermission('view_reports'))
            <a
                href="{{ auth()->user()->hasPermission('manage_inventory') ? route('inventory.low-stock') : route('reports.low-stock') }}"
                class="btn btn-light btn-sm position-relative"
                title="Low stock alerts"
            >
                <i class="bi bi-bell"></i>
                @if ($lowStockNavCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.55rem;">
                        {{ $lowStockNavCount > 99 ? '99+' : $lowStockNavCount }}
                    </span>
                @endif
            </a>
        @endif

        <div class="dropdown">
            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-1"></i>
                <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li>
                    <span class="dropdown-item-text text-muted small">{{ auth()->user()->email }}</span>
                </li>
                <li>
                    <span class="dropdown-item-text small">
                        Role: {{ auth()->user()->role?->name ?? '—' }}
                    </span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person me-2"></i> My Profile
                    </a>
                </li>
                @if (auth()->user()->hasPermission('manage_settings'))
                    <li>
                        <a class="dropdown-item" href="{{ route('settings.edit') }}">
                            <i class="bi bi-gear me-2"></i> Settings
                        </a>
                    </li>
                @endif
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item" data-confirm="Do you want to logout?">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
