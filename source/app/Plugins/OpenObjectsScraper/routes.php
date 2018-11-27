<?php

use \Illuminate\Support\Facades\Route;

Route::get('pluginlist', ['uses' => 'OpenObjectsScraperPluginController@list']);