<?php

namespace App\Scraper\ICareOpenObjectsScraper\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\ScraperPluginServiceProvider\ScraperPluginManager;

/**
 * Class ICareOpenObjectsScraperPluginController
 *
 * @package App\Plugins\ICareOpenObjectsScraper\Http\Controllers
 */
class ICareOpenObjectsScraperPluginController extends Controller
{

    /**
     * List plugins.
     *
     * @return array
     */
    public function list()
    {
        $app = app();
        $pluginManager = new ScraperPluginManager($app);
        return $pluginManager->getPlugins();
    }
}
