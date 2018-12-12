<?php

namespace App\Providers;

use App\Providers\ScraperPluginServiceProvider\ScraperPluginManager;
use Illuminate\Support\ServiceProvider;

class ScraperPluginServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        ScraperPluginManager::getInstance($this->app);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'plugins',
            function ($app) {
                return ScraperPluginManager::getInstance($app);
            }
        );
    }
}
