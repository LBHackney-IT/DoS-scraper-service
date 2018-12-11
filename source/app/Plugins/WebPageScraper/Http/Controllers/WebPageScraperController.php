<?php

namespace App\Plugins\WebPageScraper\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\ScraperPluginServiceProvider\ScraperPluginManager;

class WebPageScraperController extends Controller
{
    /**
     * @var string
     */
    protected $interface = '\App\Plugins\WebPageScraper\Scraper\WebPageScraperInterface';

    /**
     * List plugins.
     *
     * @return array
     */
    public function plugins()
    {
        $app = app();
        $pluginManager = new ScraperPluginManager($app);
        return $pluginManager->getPlugins();
    }

    /**
     * @return array
     */
    public function webPlugins()
    {
        $app = app();
        $pluginManager = new ScraperPluginManager($app, 'Scraper', $this->interface);
        return $pluginManager->getPlugins();
    }
}
