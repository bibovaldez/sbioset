<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\ChickenCounterService;
use App\Http\Controllers\EncryptionController;
use App\Http\Controllers\DecryptionController;
use App\Services\CalendarDataService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(ChickenCounterService::class, function ($app) {
            return new ChickenCounterService(
                $app->make(EncryptionController::class),
                $app->make(DecryptionController::class)
            );
        });
        $this->app->bind(CalendarDataService::class, function ($app) {
            return new CalendarDataService($app->make(DecryptionController::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
