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
     *
     * [ARSITEKTUR TERDISTRIBUSI] Kedua model pelaporan (Server A & Server B)
     * didaftarkan ke SystemObserver yang sama, sehingga audit_log di Server A
     * tetap mencatat semua aktivitas — termasuk data yang masuk ke Server B.
     */
    public function boot(): void
    {
        if (config('app.env') !== 'local') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Obat::observe(SystemObserver::class);
        Imunisasi::observe(SystemObserver::class);
        PelaporanPenyakit::observe(SystemObserver::class);
    }
}

