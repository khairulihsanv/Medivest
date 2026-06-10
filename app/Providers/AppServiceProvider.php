<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Obat;
use App\Models\Imunisasi;
use App\Models\PelaporanPenyakit;
use App\Observers\SystemObserver;

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
        Obat::observe(SystemObserver::class);
        Imunisasi::observe(SystemObserver::class);
        PelaporanPenyakit::observe(SystemObserver::class);
    }
}
