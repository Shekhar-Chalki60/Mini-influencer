<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Providers\ProfileProviderInterface;
use App\Services\Providers\ApifyProfileProvider;

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
