<?php

namespace App\Providers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        // Auto nonaktifkan event aktif yang tanggalnya sudah lewat.
        if (Schema::hasTable('events')) {
            Event::query()
                ->where('status', 'active')
                ->whereDate('event_date', '<', Carbon::today()->toDateString())
                ->update(['status' => 'ended']);
        }
    }
}
