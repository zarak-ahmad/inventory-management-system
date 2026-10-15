<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\AuditLog;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use App\Services\AuditService;
use App\Services\ProductCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductCodeService $productCodeService,
        private readonly AuditService $auditService
    ) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $trashed = $request->boolean('trashed');

        $productsQuery = $trashed
            ? Product::onlyTrashed()
            : Product::query();

        $products = $productsQuery
            ->with(['category', 'brand', 'supplier', 'unit'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->query('category_id')))
            ->when($request->filled('brand_id'), fn ($q) => $q->where('brand_id', $request->query('brand_id')))
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->query('supplier_id')))
            ->when($request->filled('status') && ! $trashed, fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->boolean('low_stock') && ! $trashed, fn ($q) => $q->lowStock())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $lowStockCount = Product::query()->lowStock()->count();

        return view('products.index', [
            'products' => $products,
            'search' => $search,
            'trashed' => $trashed,
            'lowStockCount' => $lowStockCount,
            'categories' => Category::query()->orderBy('name')->get(),
            'brands' => Brand::query()->orderBy('name')->get(),
            'suppliers' => Supplier::query()->orderBy('supplier_name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('products.create', $this->formData());
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image']);

        $data['sku'] = filled($data['sku'] ?? null)
            ? $data['sku']
            : $this->productCodeService->generateSku();

        $data['barcode'] = filled($data['barcode'] ?? null)
            ? $data['barcode']
            : $this->productCodeService->generateBarcode();

        $data['tax'] = $data['tax'] ?? 0;
        $data['discount'] = $data['discount'] ?? 0;
        $data['current_stock'] = $data['current_stock'] ?? 0;
        $data['minimum_stock'] = $data['minimum_stock'] ?? 0;
        $data['brand_id'] = $data['brand_id'] ?: null;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::query()->create($data);
        $this->auditService->logModel(AuditLog::ACTION_CREATE, $product);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'brand', 'supplier', 'unit']);

        return view('products.show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            ...$this->formData(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'remove_image']);
        $data['tax'] = $data['tax'] ?? 0;
        $data['discount'] = $data['discount'] ?? 0;
        $data['current_stock'] = $data['current_stock'] ?? 0;
        $data['minimum_stock'] = $data['minimum_stock'] ?? 0;
        $data['brand_id'] = $data['brand_id'] ?: null;

        if ($request->boolean('remove_image') && $product->image) {
            Storage::disk('public')->delete($product->image);
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $old = $this->auditService->modelSnapshot($product);
        $product->update($data);
        $this->auditService->logModel(AuditLog::ACTION_UPDATE, $product, $old);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $old = $this->auditService->modelSnapshot($product);
        $product->delete();
        $this->auditService->log(
            action: AuditLog::ACTION_DELETE,
            tableName: 'products',
            recordId: $product->id,
            oldValues: $old
        );

        return redirect()
            ->route('products.index')
            ->with('success', 'Product moved to trash.');
    }

    public function restore(int $product): RedirectResponse
    {
        $trashedProduct = Product::onlyTrashed()->findOrFail($product);
        $trashedProduct->restore();
        $this->auditService->logModel(AuditLog::ACTION_RESTORE, $trashedProduct);

        return redirect()
            ->route('products.index', ['trashed' => 1])
            ->with('success', 'Product restored successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'categories' => Category::query()->orderBy('name')->get(),
            'brands' => Brand::query()->orderBy('name')->get(),
            'suppliers' => Supplier::query()->orderBy('supplier_name')->get(),
            'units' => Unit::query()->orderBy('name')->get(),
        ];
    }
}
