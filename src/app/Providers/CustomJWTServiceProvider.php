<?php

namespace App\Providers;

use App\Auth\CustomJWTGuard;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class CustomJWTServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Auth::extend('custom-jwt', function ($app, $name, array $config) {
            return new CustomJWTGuard(
                $app['tymon.jwt'],
                $app['auth']->createUserProvider($config['provider']),
                $app['request']
            );
        });
    }
}
