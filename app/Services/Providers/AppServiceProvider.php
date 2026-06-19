<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Providers\ApifyProfileProvider;
use App\Services\Providers\ProfileProviderInterface;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ProfileProviderInterface::class,
            ApifyProfileProvider::class
        );
    }

    public function boot(): void
    {
        //
    }
}
