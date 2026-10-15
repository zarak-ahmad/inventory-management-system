<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Audit\AuditLogController;
use App\Http\Controllers\Backups\BackupController;
use App\Http\Controllers\Brands\BrandController;
use App\Http\Controllers\Categories\CategoryController;
use App\Http\Controllers\Customers\CustomerController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\Inventory\StockAdjustmentController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\Reports\ReportController;
use App\Http\Controllers\Sales\SaleController;
use App\Http\Controllers\Settings\SettingController;
use App\Http\Controllers\Suppliers\SupplierController;
use App\Http\Controllers\Units\UnitController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/product-search', [DashboardController::class, 'searchProducts'])->name('dashboard.product-search');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::middleware('permission:manage_users')->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::middleware('permission:manage_categories')->group(function () {
        Route::resource('categories', CategoryController::class);
    });

    Route::middleware('permission:manage_brands')->group(function () {
        Route::resource('brands', BrandController::class);
    });

    Route::middleware('permission:manage_units')->group(function () {
        Route::resource('units', UnitController::class);
    });

    Route::middleware('permission:manage_suppliers')->group(function () {
        Route::resource('suppliers', SupplierController::class);
    });

    Route::middleware('permission:manage_customers')->group(function () {
        Route::resource('customers', CustomerController::class);
    });

    Route::middleware('permission:manage_products')->group(function () {
        Route::post('products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::resource('products', ProductController::class);
    });

    Route::middleware('permission:manage_inventory')->group(function () {
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('inventory/transactions', [InventoryController::class, 'transactions'])->name('inventory.transactions');
        Route::get('inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
        Route::get('inventory/{product}', [InventoryController::class, 'show'])->name('inventory.show');
    });

    Route::middleware('permission:manage_stock_adjustments')->group(function () {
        Route::resource('stock-adjustments', StockAdjustmentController::class)->only(['index', 'create', 'store', 'show']);
    });

    Route::middleware('permission:manage_sales')->group(function () {
        Route::get('pos', [SaleController::class, 'pos'])->name('pos.index');
        Route::resource('sales', SaleController::class)->only(['index', 'store', 'show']);
    });

    Route::middleware('permission:view_reports')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/low-stock', [ReportController::class, 'lowStock'])->name('low-stock');
        Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
    });

    Route::middleware('permission:manage_settings')->group(function () {
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });

    Route::middleware('permission:manage_backups')->group(function () {
        Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('backups', [BackupController::class, 'store'])->name('backups.store');
        Route::get('backups/{backup}/download', [BackupController::class, 'download'])
            ->where('backup', 'backup-\d{8}-\d{6}\.sqlite')
            ->name('backups.download');
        Route::post('backups/{backup}/restore', [BackupController::class, 'restore'])
            ->where('backup', 'backup-\d{8}-\d{6}\.sqlite')
            ->name('backups.restore');
        Route::delete('backups/{backup}', [BackupController::class, 'destroy'])
            ->where('backup', 'backup-\d{8}-\d{6}\.sqlite')
            ->name('backups.destroy');
    });

    Route::middleware('permission:view_audit_logs')->group(function () {
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    });
});
