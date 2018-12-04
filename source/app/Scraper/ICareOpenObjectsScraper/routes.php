<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->get('pluginlist', ['uses' => 'ICareOpenObjectsScraperPluginController@list']);
