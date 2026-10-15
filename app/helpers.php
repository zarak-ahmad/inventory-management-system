<?php

use App\Services\SettingService;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return app(SettingService::class)->get($key, $default);
    }
}

if (! function_exists('shop_name')) {
    function shop_name(): string
    {
        return (string) setting('shop_name', config('app.name'));
    }
}

if (! function_exists('money')) {
    function money(float|int|string|null $amount): string
    {
        $symbol = (string) setting('currency_symbol', '');
        $formatted = number_format((float) $amount, 2);

        return $symbol !== '' ? trim($symbol.' '.$formatted) : $formatted;
    }
}
