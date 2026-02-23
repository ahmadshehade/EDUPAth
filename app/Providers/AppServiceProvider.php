<?php

namespace App\Providers;

use App\Enums\UserRoles;
use Illuminate\Support\ServiceProvider;

use App\Interfaces\AuthInterface;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Gate;

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
    }
}
