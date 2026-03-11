<?php

namespace App\Providers;

use App\Enums\UserRoles;
use Illuminate\Support\ServiceProvider;
use Pion\Laravel\ChunkUpload\Config\AbstractConfig;
use App\Interfaces\AuthInterface;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        $this->app->bind(AuthInterface::class, AuthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        Gate::define('adminJob', function (User $user) {
            return $user->hasRole(UserRoles::Admin->value);
        });
        $this->configureRateLimiting();




        

    }

    /**
     * Summary of configureRateLimiting
     */
    public function configureRateLimiting() {
        RateLimiter::for('auth-attempts', function (Request $request) {
            $key = $request->email ?? $request->ip();
            return Limit::perMinute(5)->by($key);
        });
        RateLimiter::for('auth-logout', function (Request $request) {
            $key = $request->email ?? $request->ip();
            return Limit::perMinute(20)->by($key);
        });
    }
}
