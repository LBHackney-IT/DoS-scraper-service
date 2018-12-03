<?php

namespace App\Plugins\ApiScraper\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\ScraperPluginServiceProvider\ScraperPluginManager;

class ApiScraperController extends Controller
{
    /**
     * @var string
     */
    protected $interface = '\App\Plugins\ApiScraper\Scraper\ApiScraperInterface';

    /**
     * @return array
     */
    public function apiPlugins()
    {
        $app = app();
        $pluginManager = new ScraperPluginManager($app, 'Scraper', $this->interface);
        return $pluginManager->getPlugins();
    }

    /**
     * @return array
     */
    public function get()
    {
        return ['a', 'b', 'c'];
    }

    /**
     * @return void
     */
    public function post()
    {
        $args = func_get_args();
        dd($args);
        exit;
//        return $args;
    }
}
