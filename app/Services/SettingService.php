<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingService
{
    public const CACHE_KEY = 'app_settings';

    /** @var array<string, string|null> */
    public const DEFAULTS = [
        'shop_name' => 'Inventory System',
        'shop_address' => '',
        'shop_phone' => '',
        'shop_email' => '',
        'shop_logo' => null,
        'currency' => 'PKR',
        'currency_symbol' => 'Rs',
        'tax_rate' => '0',
        'receipt_header' => '',
        'receipt_footer' => 'Thank you for your purchase.',
        'date_format' => 'Y-m-d',
        'timezone' => 'Asia/Karachi',
        'theme' => 'light',
        'product_search_placeholder' => 'Search by product name or barcode',
    ];

    /**
     * @return array<string, string|null>
     */
    public function all(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, function () {
                $stored = Setting::query()->pluck('value', 'key')->all();

                return array_merge(self::DEFAULTS, $stored);
            });
        } catch (\Throwable) {
            return self::DEFAULTS;
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        if (array_key_exists($key, $settings) && $settings[$key] !== null && $settings[$key] !== '') {
            return $settings[$key];
        }

        return $default ?? (self::DEFAULTS[$key] ?? null);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            if (! array_key_exists($key, self::DEFAULTS)) {
                continue;
            }

            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value === null ? null : (string) $value]
            );
        }

        $this->forgetCache();
    }

    public function storeLogo(UploadedFile $file, ?string $existing = null): string
    {
        if ($existing) {
            Storage::disk('public')->delete($existing);
        }

        return $file->store('settings', 'public');
    }

    public function removeLogo(?string $existing = null): void
    {
        if ($existing) {
            Storage::disk('public')->delete($existing);
        }

        Setting::query()->updateOrCreate(
            ['key' => 'shop_logo'],
            ['value' => null]
        );

        $this->forgetCache();
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function seedDefaults(): void
    {
        foreach (self::DEFAULTS as $key => $value) {
            Setting::query()->firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->forgetCache();
    }
}
