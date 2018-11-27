<?php

namespace App\Providers\ScraperPluginServiceProvider;

interface ScraperPluginInterface
{

    /**
     * Do stuff at boot time.
     *
     * @return void
     */
    public function boot();


    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function getPluginPath();

}
