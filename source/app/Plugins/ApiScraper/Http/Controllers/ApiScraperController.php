<?php

namespace App\Plugins\ApiScraper\Http\Controllers;

use App\Http\Controllers\Controller;

class ApiScraperController extends Controller
{
    /**
     * @return array
     */
    public function get()
    {
        return ['a', 'b', 'c'];
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
