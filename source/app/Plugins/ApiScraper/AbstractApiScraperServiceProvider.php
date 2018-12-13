<?php

namespace App\Plugins\ApiScraper;

use ReflectionClass;
use ReflectionException;

abstract class AbstractApiScraperServiceProvider extends \Illuminate\Support\ServiceProvider
{

    /**
     * @var $this
     */
    private $reflector = null;

    /**
     * @var bool
     */
    public $validPlugin = false;

    /**
     * Implementation of boot method.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function boot()
    {
        $webScraperPlugin = new ApiScraperPlugin($this->app);
        $webPlugins = $webScraperPlugin->getWebPlugins();
        $this->validPlugin = !empty($webPlugins[$this->name]);
        if ($this->validPlugin) {
            $this->enableRoutes('routes.php');
        }
    }

    /**
     * Enable routes for this plugin.
     *
     * @param string $path
     */
    protected function enableRoutes($path = 'routes.php')
    {
        $this->app->router->group(
            ['namespace' => $this->getPluginControllerNamespace()],
            function ($router) use ($path) {
                require dirname($this->getReflector()->getFileName()) . DIRECTORY_SEPARATOR . $path;
            }
        );
    }


    /**
     * Get the plugin controller namespace.
     *
     * @return string
     */
    protected function getPluginControllerNamespace()
    {
        try {
            $reflector = $this->getReflector();
            $baseDir = str_replace($reflector->getShortName(), '', $reflector->getName());

            return $baseDir . 'Http\\Controllers';
        } catch (ReflectionException $e) {
            dd('Plugin namespace could not be determined: "' . $e->getMessage() . '"');
            exit;
        }
    }

    /**
     * Get the class reflector.
     *
     * @return \ReflectionClass
     *
     * @throws \ReflectionException
     */
    private function getReflector()
    {
        if (is_null($this->reflector)) {
            $this->reflector = new ReflectionClass($this);
        }

        return $this->reflector;
    }
}