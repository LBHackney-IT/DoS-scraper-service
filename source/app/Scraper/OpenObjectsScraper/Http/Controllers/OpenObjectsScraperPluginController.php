<?php

namespace App\Plugins\OpenObjectsScraper\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\ScraperPluginServiceProvider\ScraperPluginManager;

/**
 * Class OpenObjectsScraperPluginController
 *
 * @package App\Plugins\OpenObjectsScraper\Http\Controllers
 */
class OpenObjectsScraperPluginController extends Controller
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
