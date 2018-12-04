<?php

namespace App\Plugins\ApiScraper;

use App\Providers\ScraperPluginServiceProvider\ScraperPlugin;
use App\Providers\ScraperPluginServiceProvider\ScraperPluginManager;

class ApiScraperPlugin extends ScraperPlugin
{
    /**
     * The Plugin Name.
     *
     * @var string
     */
    public $name = 'api_scraper';

    /**
     * A description of the plugin.
     *
     * @var string
     */
    public $description = 'A scraper for APis.';

    /**
     * The version of the plugin.
     *
     * @var string
     */
    public $version = '0.1';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ScraperPluginManager::getInstance($this->app, 'Scraper');
        $this->enableRoutes();
    }
}
