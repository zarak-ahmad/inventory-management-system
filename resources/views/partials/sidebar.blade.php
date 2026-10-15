<aside id="appSidebar" class="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <i class="bi bi-shop me-2"></i>
        {{ shop_name() }}
    </a>

    <ul class="sidebar-nav">
        <li>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        @if (auth()->user()->hasPermission('manage_users'))
            <li>
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i>
                    <span>Users</span>
                </a>
            </li>
        @endif

        @if (auth()->user()->hasPermission('manage_products'))
            <li>
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Products</span>
                </a>
            </li>
        @else
            <li>
                <a href="#" class="nav-link disabled" title="No permission">
                    <i class="bi bi-box-seam"></i>
                    <span>Products</span>
                </a>
            </li>
        @endif
        @if (auth()->user()->hasPermission('manage_categories'))
            <li>
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="bi bi-grid"></i>
                    <span>Categories</span>
                </a>
            </li>
        @else
            <li>
                <a href="#" class="nav-link disabled" title="No permission">
                    <i class="bi bi-grid"></i>
                    <span>Categories</span>
                </a>
            </li>
        @endif
        @if (auth()->user()->hasPermission('manage_brands'))
            <li>
                <a href="{{ route('brands.index') }}" class="nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}">
                    <i class="bi bi-tag"></i>
                    <span>Brands</span>
                </a>
            </li>
        @else
            <li>
                <a href="#" class="nav-link disabled" title="No permission">
                    <i class="bi bi-tag"></i>
                    <span>Brands</span>
                </a>
            </li>
        @endif
        @if (auth()->user()->hasPermission('manage_units'))
            <li>
                <a href="{{ route('units.index') }}" class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                    <i class="bi bi-rulers"></i>
                    <span>Units</span>
                </a>
            </li>
        @else
            <li>
                <a href="#" class="nav-link disabled" title="No permission">
                    <i class="bi bi-rulers"></i>
                    <span>Units</span>
                </a>
            </li>
        @endif
        @if (auth()->user()->hasPermission('manage_suppliers'))
            <li>
                <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i>
                    <span>Suppliers</span>
                </a>
            </li>
        @else
            <li>
                <a href="#" class="nav-link disabled" title="No permission">
                    <i class="bi bi-truck"></i>
                    <span>Suppliers</span>
                </a>
            </li>
        @endif
        @if (auth()->user()->hasPermission('manage_customers'))
            <li>
                <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Customers</span>
                </a>
            </li>
        @else
            <li>
                <a href="#" class="nav-link disabled" title="No permission">
                    <i class="bi bi-people"></i>
                    <span>Customers</span>
                </a>
            </li>
        @endif
        @if (auth()->user()->hasPermission('manage_sales'))
            <li>
                <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                    <i class="bi bi-cart-check"></i>
                    <span>Sales (POS)</span>
                </a>
            </li>
            <li>
                <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>Sales History</span>
                </a>
            </li>
        @else
            <li>
                <a href="#" class="nav-link disabled" title="No permission">
                    <i class="bi bi-cart-check"></i>
                    <span>Sales (POS)</span>
                </a>
            </li>
        @endif
        @if (auth()->user()->hasPermission('manage_inventory'))
            <li>
                <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                    <i class="bi bi-boxes"></i>
                    <span>Inventory</span>
                </a>
            </li>
        @else
            <li>
                <a href="#" class="nav-link disabled" title="No permission">
                    <i class="bi bi-boxes"></i>
                    <span>Inventory</span>
                </a>
            </li>
        @endif
        @if (auth()->user()->hasPermission('manage_stock_adjustments'))
            <li>
                <a href="{{ route('stock-adjustments.index') }}" class="nav-link {{ request()->routeIs('stock-adjustments.*') ? 'active' : '' }}">
                    <i class="bi bi-sliders"></i>
                    <span>Adjustments</span>
                </a>
            </li>
        @endif
        @if (auth()->user()->hasPermission('view_reports'))
            <li>
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i>
                    <span>Reports</span>
                </a>
            </li>
        @else
            <li>
                <a href="#" class="nav-link disabled" title="No permission">
                    <i class="bi bi-bar-chart"></i>
                    <span>Reports</span>
                </a>
            </li>
        @endif
        @if (auth()->user()->hasPermission('manage_settings'))
            <li>
                <a href="{{ route('settings.edit') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>
        @else
            <li>
                <a href="#" class="nav-link disabled" title="No permission">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>
        @endif
        @if (auth()->user()->hasPermission('manage_backups'))
            <li>
                <a href="{{ route('backups.index') }}" class="nav-link {{ request()->routeIs('backups.*') ? 'active' : '' }}">
                    <i class="bi bi-device-hdd"></i>
                    <span>Backup</span>
                </a>
            </li>
        @endif
        @if (auth()->user()->hasPermission('view_audit_logs'))
            <li>
                <a href="{{ route('audit-logs.index') }}" class="nav-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i>
                    <span>Audit Logs</span>
                </a>
            </li>
        @endif
        <li class="mt-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start" data-confirm="Do you want to logout?">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </form>
        </li>
    </ul>
</aside>
