<?php

namespace App\Providers;

use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\SymfonyCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Storages\Drivers\FileStorage;
use BotMan\Drivers\Facebook\FacebookDriver;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        Schema::defaultStringLength(191);
        DriverManager::loadDriver(FacebookDriver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('bot', function ($app) {
            $cache = new SymfonyCache(new FilesystemAdapter('symfonycache', 120, storage_path('app')));
            $config = config('facebook', []);
            return BotManFactory::create(['facebook' => $config], $cache, null, new FileStorage(storage_path('app')));
        });
    }
}
