<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->group(['prefix' => 'scraper'], function () use ($router) {
    $router->get('plugins', ['uses' => 'WebPageScraperController@plugins']);
    $router->group(['prefix' => 'web'], function () use ($router) {
        $router->get('plugins', ['uses' => 'WebPageScraperController@webPlugins']);
        $router->get('/', ['uses' => 'WebPageScraperController@get']);
        $router->post('/', ['uses' => 'WebPageScraperController@post']);
    });
});
