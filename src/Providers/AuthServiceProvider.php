<?php

namespace Sangth\Auth\Sdk\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Sangth\Auth\Sdk\Credentials\Authenticable;
use Sangth\Auth\Sdk\Credentials\TokenAuthenticator;
use Sangth\Auth\Sdk\Guards\TokenGuard;
use Illuminate\Auth\RequestGuard;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->extendGuards();

        $this->customUserProvider();
    }

    public function register()
    {
        $this->registerCredentials();
        
        $this->registerConfigs();
    }

    protected function registerCredentials()
    {
        $this->app->bind(Authenticable::class, TokenAuthenticator::class);
    }

    protected function registerConfigs()
    {
        $source = realpath(__DIR__ . '/../config/auth.php');

        $this->publishes([$source => config_path('auth.php')]);

        $this->mergeConfigFrom($source, 'auth');
    }

    protected function customUserProvider()
    {
        Auth::provider('prep', function ($app, array $config) {
            if (empty($config['host'])) {
                throw new \Exception('The prep user provider host is empty. Please set AUTH_HOST in .env');
            }
            $model = $config['model'];

            return new UserProvider($config['host'], $model);
        });
    }

    protected function extendGuards()
    {
        Auth::extend('token', function ($app, $name, array $config) {
            return tap($this->createTokenDriver($config), function ($guard) {
                app()->refresh('request', $guard, 'setRequest');
            });
        });
    }

    protected function createTokenDriver(array $config): RequestGuard
    {
        $tokenGuard = new TokenGuard(Auth::createUserProvider($config['provider'] ?? 'users'), app(Authenticable::class));

        return new RequestGuard(function ($request) use ($tokenGuard) {
            return $tokenGuard->user($request);
        }, app('request'));
    }
}
