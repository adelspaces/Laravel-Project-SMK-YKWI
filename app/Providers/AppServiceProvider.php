<?php

namespace App\Providers;

use App\Models\Pengaturan;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        try {
            // Cek koneksi DB dan tabel pengaturans
            if (DB::connection()->getDatabaseName() && Schema::hasTable('pengaturans')) {
                $pengaturan = Pengaturan::first();
                View::share('pengaturan', $pengaturan);
            }
        } catch (\Exception $e) {
            // Tidak melakukan apa-apa jika error, agar aplikasi tetap jalan
            // Bisa juga log error ke storage/logs jika kamu mau
        }
    }
}
