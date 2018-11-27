<?php


$this->app->router->get('scraper/web', ['uses' => 'WebPageScraperController@get']);
$this->app->router->post('scraper/web', ['uses' => 'WebPageScraperController@post']);
