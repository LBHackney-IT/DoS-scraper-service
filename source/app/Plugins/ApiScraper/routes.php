<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->group(['prefix' => 'scraper'], function () use ($router) {
    $router->get('api', ['uses' => 'ApiScraperController@get']);
});
