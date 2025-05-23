<?php

namespace Mitoop\Fxzb;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public array $singletons = [
        ClientManager::class => ClientManager::class,
        Router::class => Router::class,
    ];

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/fxzb.php' => config_path('fxzb.php')], 'config');
        }

        Client::setDispatcher($this->app['events']);
    }
}
