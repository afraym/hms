<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        if (!Cache::has('medical_id_counter')) {
            // Get the last medical ID from database or start from 0
            $lastMedicalId = \App\Models\Patient::max('medical_id') ?? 0;
            Cache::forever('medical_id_counter', (int)$lastMedicalId);
        }
    }
}
