<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StockAdjustmentService
{
    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly AuditService $auditService
    ) {}

    /**
     * @param  array{product_id:int, adjustment_type:string, quantity:float|int|string, reason:string}  $data
     */
    public function create(array $data, int $userId): StockAdjustment
    {
        return DB::transaction(function () use ($data, $userId) {
            /** @var Product $product */
            $product = Product::query()->lockForUpdate()->findOrFail($data['product_id']);

            $quantity = (float) $data['quantity'];
            $type = $data['adjustment_type'];
            $reason = trim((string) $data['reason']);

            if ($quantity <= 0) {
                throw new InvalidArgumentException('Quantity must be greater than zero.');
            }

            if ($reason === '') {
                throw new InvalidArgumentException('Adjustment reason is required.');
            }

            $stockBefore = (float) $product->current_stock;
            $isIncrease = $type === StockAdjustment::TYPE_INCREASE;

            if (! $isIncrease && $stockBefore < $quantity) {
                throw new InvalidArgumentException("Insufficient stock for product: {$product->name}");
            }

            $stockAfter = $isIncrease
                ? $stockBefore + $quantity
                : $stockBefore - $quantity;

            $adjustment = StockAdjustment::query()->create([
                'product_id' => $product->id,
                'adjustment_type' => $type,
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reason' => $reason,
                'created_by' => $userId,
            ]);

            $remarks = sprintf(
                'Stock %s (%s): %s',
                $isIncrease ? 'increase' : 'decrease',
                ucfirst($type),
                $reason
            );

            if ($isIncrease) {
                $this->inventoryService->increase(
                    product: $product,
                    quantity: $quantity,
                    transactionType: InventoryTransaction::TYPE_ADJUSTMENT,
                    reference: $adjustment,
                    remarks: $remarks,
                    userId: $userId
                );
            } else {
                $this->inventoryService->decrease(
                    product: $product,
                    quantity: $quantity,
                    transactionType: InventoryTransaction::TYPE_ADJUSTMENT,
                    reference: $adjustment,
                    remarks: $remarks,
                    userId: $userId
                );
            }

            $adjustment->load(['product.unit', 'creator']);

            $this->auditService->log(
                action: AuditLog::ACTION_STOCK_ADJUSTMENT,
                tableName: 'stock_adjustments',
                recordId: $adjustment->id,
                newValues: [
                    'product_id' => $adjustment->product_id,
                    'adjustment_type' => $adjustment->adjustment_type,
                    'quantity' => $adjustment->quantity,
                    'stock_before' => $adjustment->stock_before,
                    'stock_after' => $adjustment->stock_after,
                    'reason' => $adjustment->reason,
                ],
                userId: $userId
            );

            return $adjustment;
        });
    }
}
