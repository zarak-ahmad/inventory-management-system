<?php

namespace App\Providers;

use App\Services\SettingService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        try {
            if (Schema::hasTable('settings')) {
                $timezone = app(SettingService::class)->get('timezone', config('app.timezone'));
                if (is_string($timezone) && $timezone !== '') {
                    config(['app.timezone' => $timezone]);
                    date_default_timezone_set($timezone);
                }
            }
        } catch (\Throwable) {
            // Settings table may not exist during early migrate/install.
        }
    }
}
