<?php

namespace App\Providers;

use App\Services\UserStorageService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserStorageService::class, function ($app) {
            $user = request()->user(); // or auth()->user()
            $diskName = config('filesystems.image_disk', 'public');

            if (! $user) {
                throw new \RuntimeException('Cannot resolve UserStorageService without a user.');
            }

            return new UserStorageService($user->id, $diskName);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //if($this->app->environment('production')) {
        URL::forceScheme('https');
        //};

        RateLimiter::for('background-jobs', function ($job) {
            return Limit::perMinute(1)->by(auth()->id() ?? 'guest');
        });
    }
}
