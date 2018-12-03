<?php

namespace App\Providers\ScraperPluginServiceProvider;

use Illuminate\Contracts\Container\Container as Application;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;

abstract class ScraperPlugin implements ScraperPluginInterface
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application $app
     */
    protected $app;

    /**
     * The Plugin Name.
     *
     * @var string
     */
    public $name;

    /**
     * A description of the plugin.
     *
     * @var string
     */
    public $description;

    /**
     * The version of the plugin.
     *
     * @var string
     */
    public $version;

    /**
     * The type of plugin.
     *
     * @var string
     */
    public $type;

    /**
     * @var $this
     */
    private $reflector = null;

    /**
     * Plugin constructor.
     *
     *
     * @param \Illuminate\Contracts\Container\Container $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->checkPluginName();
        $this->setPluginType();
    }

    abstract public function boot();

    /**
     * Check for empty plugin name.
     *
     * @throws \InvalidArgumentException
     */
    private function checkPluginName()
    {
        if (!$this->name) {
            throw new InvalidArgumentException('Missing plugin name.');
        }
    }

    /**
     * Set the plugin type.
     */
    private function setPluginType()
    {
        try {
            $this->type = $this->getReflector()->getParentClass()->getShortName();
        } catch (ReflectionException $e) {
            Log::notice('Could not set plugin type: ' . $e->getMessage());
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
                require $this->getPluginPath() . DIRECTORY_SEPARATOR . $path;
            }
        );
    }

    /**
     * Register a database migration path for this plugin.
     *
     * @param  array|string  $paths
     * @return void
     */
    protected function enableMigrations($paths = 'migrations')
    {
        $this->app->afterResolving('migrator', function ($migrator) use ($paths) {
            foreach ((array) $paths as $path) {
                $migrator->path($this->getPluginPath() . DIRECTORY_SEPARATOR . $path);
            }
        });
    }

    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function getPluginPath()
    {
        try {
            $reflector = $this->getReflector();
            $fileName  = $reflector->getFileName();
            return dirname($fileName);
        } catch (ReflectionException $e) {
            dd('Plugin path could not be found: "' . $e->getMessage() . '"');
            exit;
        }
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
