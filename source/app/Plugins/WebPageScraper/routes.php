<?php


$this->app->router->get('scraper/plugins', ['uses' => 'WebPageScraperController@plugins']);
$this->app->router->get('scraper/web/plugins', ['uses' => 'WebPageScraperController@webPlugins']);
$this->app->router->get('scraper/web', ['uses' => 'WebPageScraperController@get']);
$this->app->router->post('scraper/web', ['uses' => 'WebPageScraperController@post']);
