<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SaleService
{
    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly AuditService $auditService
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array{product_id:int|string, quantity:float|int|string, selling_price?:float|int|string}>  $items
     */
    public function create(array $data, array $items, int $userId): Sale
    {
        return DB::transaction(function () use ($data, $items, $userId) {
            $customerId = (int) ($data['customer_id'] ?? 0);

            if ($customerId <= 0) {
                $customerId = (int) Customer::query()
                    ->where('is_default', true)
                    ->value('id');
            }

            if ($customerId <= 0) {
                throw new RuntimeException('Walk-in Customer is missing. Please seed customers.');
            }

            $normalizedItems = $this->normalizeItems($items);

            if ($normalizedItems === []) {
                throw new RuntimeException('Add at least one product to the cart.');
            }

            $subtotal = collect($normalizedItems)->sum('subtotal');
            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0);
            $total = max(0, $subtotal - $discount + $tax);

            $sale = Sale::query()->create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id' => $customerId,
                'user_id' => $userId,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'payment_status' => $data['payment_status'] ?? Sale::STATUS_PAID,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($normalizedItems as $item) {
                /** @var Product $product */
                $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);

                if ((float) $product->current_stock < $item['quantity']) {
                    throw new RuntimeException("Insufficient stock for product: {$product->name}");
                }

                SaleItem::query()->create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'selling_price' => $item['selling_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $this->inventoryService->decrease(
                    product: $product,
                    quantity: $item['quantity'],
                    transactionType: InventoryTransaction::TYPE_SALE,
                    reference: $sale,
                    remarks: "Sale {$sale->invoice_number}",
                    userId: $userId
                );
            }

            $sale->load(['customer', 'user', 'items.product.unit']);

            $this->auditService->log(
                action: AuditLog::ACTION_SALE,
                tableName: 'sales',
                recordId: $sale->id,
                newValues: [
                    'invoice_number' => $sale->invoice_number,
                    'customer_id' => $sale->customer_id,
                    'total' => $sale->total,
                    'payment_method' => $sale->payment_method,
                    'items' => $normalizedItems,
                ],
                userId: $userId
            );

            return $sale;
        });
    }

    /**
     * @param  array<int, array{product_id:int|string, quantity:float|int|string, selling_price?:float|int|string}>  $items
     * @return array<int, array{product_id:int, quantity:float, selling_price:float, subtotal:float}>
     */
    private function normalizeItems(array $items): array
    {
        $normalized = [];

        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (float) ($item['quantity'] ?? 0);

            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            $product = Product::query()->find($productId);
            if (! $product) {
                continue;
            }

            $sellingPrice = array_key_exists('selling_price', $item) && $item['selling_price'] !== null && $item['selling_price'] !== ''
                ? (float) $item['selling_price']
                : (float) $product->selling_price;

            $normalized[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'selling_price' => $sellingPrice,
                'subtotal' => round($quantity * $sellingPrice, 2),
            ];
        }

        return $normalized;
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV-'.now()->format('Ymd');
        $latest = Sale::query()
            ->where('invoice_number', 'like', $prefix.'%')
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $sequence = 1;

        if ($latest) {
            $sequence = (int) substr($latest, -4) + 1;
        }

        return sprintf('%s-%04d', $prefix, $sequence);
    }
}
