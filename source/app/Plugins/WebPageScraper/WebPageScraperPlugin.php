<?php

namespace App\Plugins\WebPageScraper;

use App\Providers\ScraperPluginServiceProvider\ScraperPlugin;

/**
 * Web page scraper plugin.
 *
 * @package App\Plugins\WebPageScraper
 */
class WebPageScraperPlugin extends ScraperPlugin
{
    /**
     * The Plugin Name.
     *
     * @var string
     */
    public $name = 'web_page_scraper';

    /**
     * A description of the plugin.
     *
     * @var string
     */
    public $description = 'A scraper for web pages.';

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
