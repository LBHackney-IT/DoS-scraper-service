<?php

namespace App\Providers\ScraperPluginServiceProvider;

use Illuminate\Contracts\Container\Container;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

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
    protected $pluginBaseDirectory;

    /**
     * @var string
     */
    protected $pluginDirectory;

    /**
     * @var string
     */
    protected $interface;

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
     * @param string $pluginBaseDirectory
     * @param string $interface
     */
    public function __construct($app, $pluginBaseDirectory = '', $interface = null)
    {
        $this->app             = $app;
        $this->pluginBaseDirectory = empty($pluginBaseDirectory) ? 'Plugins' : $pluginBaseDirectory;
        $this->pluginDirectory = $app->path() . DIRECTORY_SEPARATOR . $this->pluginBaseDirectory;
        $this->interface = empty($interface)
            ? '\App\Providers\ScraperPluginServiceProvider\ScraperPluginInterface'
            : $interface;
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
                Log::error('Plugin ' . $directoryName . ' needs a ' . $directoryName . 'Plugin class.');
            }

            try {
                /** @var ScraperPlugin $plugin */
                $plugin = $this->app->makeWith($pluginClass, [$this->app]);
            } catch (\ReflectionException $e) {
                Log::error('Plugin ' . $directoryName . ' could not be booted: "' . $e->getMessage() . '"');
            }
            try {
                $reflector = new \ReflectionClass($pluginClass);
                if (!($reflector->implementsInterface($this->interface))) {
                    // Ignore plugins that do not implement the interface required.
                    continue;
                }
            } catch (\ReflectionException $e) {
                Log::error("Plugin {$directoryName} could not be booted: {$e->getMessage()}");
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
        return "App\\{$this->pluginBaseDirectory}\\${directory}\\${directory}Plugin";
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
     * Get a plugin by name
     *
     * @param string $name - Plugin name.
     *
     * @return AbstractScraper
     *
     * @throws \InvalidArgumentException
     */
    public function getPlugin($name)
    {
        if (empty($this->getPlugins()[$name])) {
            throw new InvalidArgumentException('Missing plugin');
        }
        return $this->getPlugins()[$name];
    }

    /**
     * @return string
     */
    public function getPluginDirectory()
    {
        return $this->pluginDirectory;
    }

}