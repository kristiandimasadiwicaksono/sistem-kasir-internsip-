<?php

namespace App\Providers;

use App\Models\RestockDetail;
use Illuminate\Support\Facades\View;
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
        View::share('pendingRestock', 0);

        View::composer('*', function($view) {
            try {
                $count = RestockDetail::whereIn('status_penerimaan', ['BELUM_DITERIMA', 'SEBAGIAN'])->count();
            } catch (\Throwable $e) {
                $count = 0;
            }
            $view->with('pendingRestock', $count);
        });
    }
}
