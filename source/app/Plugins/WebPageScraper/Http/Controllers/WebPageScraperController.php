<?php

namespace App\Plugins\WebPageScraper\Http\Controllers;

use App\Jobs\ProcessWebPageScrapeJob;
use App\Plugins\WebPageScraper\WebPageScraperPlugin;
use App\Providers\ScraperPluginServiceProvider\ScraperPluginManager;
use ReflectionException;

class WebPageScraperController extends AbstractWebPageScraperController
{

    /**
     * @var array
     */
    private $plugins = [];

    /**
     * @var \Laravel\Lumen\Application
     */
    protected $app;

    /**
     * WebPageScraperController constructor.
     */
    public function __construct()
    {
        $this->app = app();
    }

    /**
     * return void
     */
    protected function makeService()
    {
        // TODO: Implement makeService() method.
    }

    /**
     * List plugins.
     *
     * @return array
     */
    public function plugins()
    {
        $pluginManager = new ScraperPluginManager($this->app);
        return $pluginManager->getPlugins();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function webPlugins()
    {
        try {
            $webScraperPlugin = new WebPageScraperPlugin($this->app);
            return response()->json($webScraperPlugin->getWebPlugins());
        } catch (ReflectionException $e) {
            return $this->exceptionResponse($e);
        }
    }
}
