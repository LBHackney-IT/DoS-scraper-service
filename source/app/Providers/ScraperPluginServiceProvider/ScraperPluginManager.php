<?php

namespace App\Providers\ScraperPluginServiceProvider;

use Illuminate\Contracts\Container\Container;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * ScraperPluginManager class.
 *
 * @package App\Providers\ScraperPluginServiceProvider
 */
class ScraperPluginManager
{
    /**
     * An application instance of an Illuminate Container.
     *
     * @var Container
     */
    private $app;

    /**
     * A Plugin Manager object.
     *
     * @var ScraperPluginManager
     */
    private static $instance = null;

    /**
     * @var string
     */
    protected $pluginDirectory;

    /**
     * @var array
     */
    protected $plugins = [];

    /**
     * @var array
     */
    protected $classMap = [];

    /**
     * PluginManager constructor.
     *
     * @param \Illuminate\Contracts\Container\Container $app - Application object
     */
    public function __construct($app)
    {
        $this->app             = $app;
        $this->pluginDirectory = $app->path() . DIRECTORY_SEPARATOR . 'Plugins';
        // $this->pluginExtender  = new PluginExtender($this, $app);

        $this->bootPlugins();
//        $this->pluginExtender->extendAll();

        $this->registerClassLoader();
    }


    /**
     * Registers plugin autoloader.
     */
    private function registerClassLoader()
    {
        spl_autoload_register([new ClassLoader($this), 'loadClass'], true, true);
    }

    /**
     * Get a plugin instance.
     *
     * @param \Illuminate\Contracts\Container\Container $app - Application object
     *
     * @return ScraperPluginManager
     */
    public static function getInstance(Container $app)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($app);
        }

        return self::$instance;
    }

    /**
     * Boot the plugins.
     *
     * @return void
     */
    protected function bootPlugins()
    {
        foreach (Finder::create()->in($this->pluginDirectory)->directories()->depth(0) as $dir) {
            /** @var SplFileInfo $dir */
            $directoryName = $dir->getBasename();

            $pluginClass = $this->getPluginClassNameFromDirectory($directoryName);

            if (!class_exists($pluginClass)) {
                dd('Plugin ' . $directoryName . ' needs a ' . $directoryName . 'Plugin class.');
            }

            try {
                $plugin = $this->app->makeWith($pluginClass, [$this->app]);
            } catch (\ReflectionException $e) {
                dd('Plugin ' . $directoryName . ' could not be booted: "' . $e->getMessage() . '"');
                exit;
            }

            if (!($plugin instanceof ScraperPlugin)) {
                dd('Plugin ' . $directoryName . ' must extend the Plugin Base Class');
            }

            $plugin->boot();

            $this->plugins[$plugin->name] = $plugin;
        }
    }

    /**
     * @param $directory
     * @return string
     */
    protected function getPluginClassNameFromDirectory($directory)
    {
        return "App\\Plugins\\${directory}\\${directory}Plugin";
    }

    /**
     * @return array
     */
    public function getClassMap()
    {
        return $this->classMap;
    }

    /**
     * @param array $classMap
     * @return $this
     */
    public function setClassMap($classMap)
    {
        $this->classMap = $classMap;

        return $this;
    }

    /**
     * @param $classNamespace
     * @param $storagePath
     */
    public function addClassMapping($classNamespace, $storagePath)
    {
        $this->classMap[$classNamespace] = $storagePath;
    }

    /**
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @return string
     */
    public function getPluginDirectory()
    {
        return $this->pluginDirectory;
    }

}