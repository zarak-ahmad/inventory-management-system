<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public const UPDATED_AT = null;

    public const ACTION_LOGIN = 'login';

    public const ACTION_LOGOUT = 'logout';

    public const ACTION_CREATE = 'create';

    public const ACTION_UPDATE = 'update';

    public const ACTION_DELETE = 'delete';

    public const ACTION_RESTORE = 'restore';

    public const ACTION_SALE = 'sale';

    public const ACTION_PURCHASE = 'purchase';

    public const ACTION_STOCK_ADJUSTMENT = 'stock_adjustment';

    public const ACTION_BACKUP = 'backup';

    public const ACTION_RESTORE_BACKUP = 'restore_backup';

    public const ACTION_SETTINGS = 'settings_update';

    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return list<string>
     */
    public static function actions(): array
    {
        return [
            self::ACTION_LOGIN,
            self::ACTION_LOGOUT,
            self::ACTION_CREATE,
            self::ACTION_UPDATE,
            self::ACTION_DELETE,
            self::ACTION_RESTORE,
            self::ACTION_SALE,
            self::ACTION_PURCHASE,
            self::ACTION_STOCK_ADJUSTMENT,
            self::ACTION_BACKUP,
            self::ACTION_RESTORE_BACKUP,
            self::ACTION_SETTINGS,
        ];
    }
}
