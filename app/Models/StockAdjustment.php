<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    public const TYPE_INCREASE = 'increase';

    public const TYPE_DECREASE = 'decrease';

    public const TYPE_DAMAGE = 'damage';

    public const TYPE_EXPIRED = 'expired';

    public const TYPE_LOST = 'lost';

    protected $fillable = [
        'product_id',
        'adjustment_type',
        'quantity',
        'stock_before',
        'stock_after',
        'reason',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'stock_before' => 'decimal:2',
            'stock_after' => 'decimal:2',
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_INCREASE,
            self::TYPE_DECREASE,
            self::TYPE_DAMAGE,
            self::TYPE_EXPIRED,
            self::TYPE_LOST,
        ];
    }

    public function isIncrease(): bool
    {
        return $this->adjustment_type === self::TYPE_INCREASE;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
