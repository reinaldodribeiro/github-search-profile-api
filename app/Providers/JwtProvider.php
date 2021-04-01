<?php

namespace App\Providers;

use App\Guard\JwtGuard;
use Tymon\JWTAuth\Providers\LaravelServiceProvider;

class JwtProvider extends LaravelServiceProvider
{
    protected function extendAuthGuard()
    {
        $this->app['auth']->extend('jwt', function ($app, $name, array $config) {
            $guard = new JwtGuard(
                $app['tymon.jwt'],
                $app['auth']->createUserProvider($config['provider']),
                $app['request']
            );
            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }

}
