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
     * @var string
     */
    protected $interface = 'App\Plugins\ApiScraper\Scraper\ApiScraperInterface';

    /**
     * @var string
     */
    protected $baseClass = 'Illuminate\Support\ServiceProvider';

    /**
     * Array of valid plugins.
     *
     * @var array
     */
    protected $plugins = [];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->enableRoutes();
    }

    /**
     * Populate the plugins array.
     *
     * @throws \ReflectionException
     */
    private function setApiPlugins()
    {
        $classes = get_declared_classes();
        foreach ($classes as $class) {
            $reflector = new \ReflectionClass($class);
            if ($reflector->implementsInterface($this->interface) && $reflector->isSubclassOf($this->baseClass)) {
                $klass = new $class($this->app);
                $extends = $reflector->getParentClass();
                $interfaces = $reflector->getInterfaces();
                $if = [];
                foreach ($interfaces as $interface) {
                    $if[] = [
                        $interface->getName(),
                        $interface->getShortName(),
                    ];
                }
                $this->plugins[$reflector->getProperty('name')->getValue($klass)] = [
                    'name' => $reflector->getProperty('name')->getValue($klass),
                    'description' => $reflector->getProperty('description')->getValue($klass),
                    'version' => $reflector->getProperty('version')->getValue($klass),
                    'class' => [
                        $reflector->getName(),
                        $reflector->getShortName(),
                    ],
                    'interfaces' => $if,
                    'type' => isset($extends) ? $extends->getName() : null,
                ];
            }
        }
    }

    /**
     * Get the web page scraper plugins.
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function getApiPlugins()
    {
        if (empty($this->plugins)) {
            $this->setApiPlugins();
        }
        return $this->plugins;
    }
}
