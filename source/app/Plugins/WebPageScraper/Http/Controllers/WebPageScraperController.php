<?php

namespace App\Plugins\WebPageScraper\Http\Controllers;

use App\Http\Controllers\Controller;

class WebPageScraperController extends Controller
{

    /**
     * @return array
     */
    public function get()
    {
        return ['one', 'two', 'three'];
    }

    /**
     * @return void
     */
    public function post()
    {
        $args = func_get_args();
        dd($args);
        exit;
//        return $args;
    }
}
