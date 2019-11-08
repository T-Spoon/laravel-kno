<?php

namespace App\Kno;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class KnoAuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::provider('kno', function ($app, array $config) {
            return new KnoUserProvider($this->app['hash'], $config['model']);
        });
    }
}
