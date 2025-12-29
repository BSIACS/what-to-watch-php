<?php

namespace App\Providers;

use App\Services\Interfaces\FileStorageService;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\LocalFileStorageService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class,UserService::class);
        $this->app->bind(FileStorageService::class, LocalFileStorageService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
