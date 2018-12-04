<?php

namespace App\Scraper\ICareWebPageScraper\Http\Controllers;

use App\Http\Controllers\Controller;

/**
 * Class ICareWebPageScraperPluginController
 *
 * @package App\Scraper\ICareWebPageScraper\Http\Controllers
 */
class ICareWebPageScraperPluginController extends Controller
{
    /** @var string */
    protected $baseUrl = 'https://www.hackneyicare.org.uk/';

    public function retrieve()
    {
        return [1, 2, 3];
    }
}
