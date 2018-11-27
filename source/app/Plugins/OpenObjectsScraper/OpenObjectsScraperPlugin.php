<?php

namespace App\Plugins\OpenObjectsScraper;

use App\Providers\ScraperPluginServiceProvider\ScraperPlugin;

/**
 * The ScraperPlugin base class
 *
 * @package App\Plugins\Scraper
 */
class OpenObjectsScraperPlugin extends ScraperPlugin
{
    /**
     * The Plugin Name.
     *
     * @var string
     */
    public $name = 'open_objects_scraper';

    /**
     * A description of the plugin.
     *
     * @var string
     */
    public $description = 'A scraper for Open Objects.';

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
        $this->enableRoutes();
    }
}
