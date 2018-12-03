<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->group(['prefix' => 'scraper'], function () use ($router) {
    $router->group(['prefix' => 'api'], function () use ($router) {
        $router->get('plugins', ['uses' => 'ApiScraperController@apiPlugins']);
        $router->get('/', ['uses' => 'ApiScraperController@get']);
    });
});
