<?php


$this->app->router->get('scraper/api', ['uses' => 'ApiScraperController@get']);
$this->app->router->post('scraper/api', ['uses' => 'ApiScraperController@post']);