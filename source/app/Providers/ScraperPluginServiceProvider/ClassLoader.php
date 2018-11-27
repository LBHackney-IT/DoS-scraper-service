<?php

namespace App\Providers\ScraperPluginServiceProvider;

class ClassLoader
{
    /**
     * @var ScraperPluginManager
     */
    protected $pluginManager;

    /**
     * ClassLoader constructor.
     *
     * @param ScraperPluginManager $pluginManager
     */
    public function __construct(ScraperPluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    /**
     * Loads the given class or interface.
     *
     * @param $class
     * @return bool|null
     */
    public function loadClass($class)
    {
        if (isset($this->pluginManager->getClassMap()[$class])) {
            \Composer\Autoload\includeFile($this->pluginManager->getClassMap()[$class]);

            return true;
        }
    }
}
