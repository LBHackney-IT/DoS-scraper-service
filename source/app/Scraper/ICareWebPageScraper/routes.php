<?php
/** @var \Laravel\Lumen\Routing\Router $router */
$router->group(['prefix' => 'scraper'], function () use ($router) {
    $router->group(['prefix' => 'web'], function () use ($router) {
        $router->group(['prefix' => 'icare'], function () use ($router) {
            $router->get('/', ['uses' => 'ICareWebPageScraperPluginController@retrieve']);
        });
    });
});
