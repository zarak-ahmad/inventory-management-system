<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    /** @var list<string> */
    public const DEFAULT_CATEGORIES = [
        'Rent',
        'Utilities',
        'Salaries',
        'Transport',
        'Maintenance',
        'Supplies',
        'Marketing',
        'Other',
    ];

    protected $fillable = [
        'category',
        'amount',
        'description',
        'expense_date',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return list<string>
     */
    public static function categorySuggestions(): array
    {
        $used = static::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->all();

        return array_values(array_unique(array_merge(self::DEFAULT_CATEGORIES, $used)));
    }
}
