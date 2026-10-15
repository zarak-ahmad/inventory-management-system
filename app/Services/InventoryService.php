<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class InventoryService
{
    /**
     * Increase product stock and record an inventory transaction.
     */
    public function increase(
        Product $product,
        float $quantity,
        string $transactionType,
        ?Model $reference = null,
        ?string $remarks = null,
        ?int $userId = null
    ): InventoryTransaction {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be greater than zero.');
        }

        $stockBefore = (float) $product->current_stock;
        $stockAfter = $stockBefore + $quantity;

        $product->current_stock = $stockAfter;
        $product->save();

        return InventoryTransaction::query()->create([
            'product_id' => $product->id,
            'transaction_type' => $transactionType,
            'reference_type' => $reference ? $reference::class : null,
            'reference_id' => $reference?->getKey(),
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'remarks' => $remarks,
            'created_by' => $userId ?? Auth::id(),
        ]);
    }

    /**
     * Decrease product stock and record an inventory transaction.
     */
    public function decrease(
        Product $product,
        float $quantity,
        string $transactionType,
        ?Model $reference = null,
        ?string $remarks = null,
        ?int $userId = null
    ): InventoryTransaction {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be greater than zero.');
        }

        $stockBefore = (float) $product->current_stock;

        if ($stockBefore < $quantity) {
            throw new InvalidArgumentException("Insufficient stock for product: {$product->name}");
        }

        $stockAfter = $stockBefore - $quantity;

        $product->current_stock = $stockAfter;
        $product->save();

        return InventoryTransaction::query()->create([
            'product_id' => $product->id,
            'transaction_type' => $transactionType,
            'reference_type' => $reference ? $reference::class : null,
            'reference_id' => $reference?->getKey(),
            'quantity' => -$quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'remarks' => $remarks,
            'created_by' => $userId ?? Auth::id(),
        ]);
    }
}
