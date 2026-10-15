<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

class AuditService
{
    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    public function log(
        string $action,
        ?string $tableName = null,
        ?int $recordId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null
    ): void {
        try {
            AuditLog::query()->create([
                'user_id' => $userId ?? Auth::id(),
                'action' => $action,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'old_values' => $oldValues ? $this->sanitize($oldValues) : null,
                'new_values' => $newValues ? $this->sanitize($newValues) : null,
                'ip_address' => Request::ip(),
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function logModel(
        string $action,
        Model $model,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null
    ): void {
        $this->log(
            action: $action,
            tableName: $model->getTable(),
            recordId: (int) $model->getKey(),
            oldValues: $oldValues,
            newValues: $newValues ?? $this->modelSnapshot($model),
            userId: $userId
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function modelSnapshot(Model $model, array $except = ['password', 'remember_token']): array
    {
        return $this->sanitize($model->withoutRelations()->toArray(), $except);
    }

    /**
     * @param  array<string, mixed>  $values
     * @param  list<string>  $except
     * @return array<string, mixed>
     */
    private function sanitize(array $values, array $except = ['password', 'remember_token']): array
    {
        foreach ($except as $key) {
            unset($values[$key]);
        }

        return $values;
    }
}
