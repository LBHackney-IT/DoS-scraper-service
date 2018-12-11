<?php

namespace App\Scraper\ICareWebPageScraper\Http\Controllers;

use App\Plugins\WebPageScraper\Http\Controllers\AbstractWebPageScraperController;
use App\Scraper\ICareWebPageScraper\Http\Driver\ICareWebPageHttpDriver;
use App\Scraper\ICareWebPageScraper\Http\ICareWebPageHttpService;
use Illuminate\Http\Request;

/**
 * Class ICareWebPageScraperPluginController
 *
 * @package App\Scraper\ICareWebPageScraper\Http\Controllers
 */
abstract class AbstractICareWebPageScraperPluginController extends AbstractWebPageScraperController
{
    /**
     * @var string
     */
    protected $baseUrl = 'https://www.hackneyicare.org.uk';

    /**
     * @var \App\Scraper\ICareWebPageScraper\Http\ICareWebPageHttpService
     */
    protected $service;

    public function __construct(Request $request, $conf = [])
    {
        $conf = array_merge(['base_url' => $this->baseUrl], $conf);
        parent::__construct($request, $conf);
    }

    /**
     * {@inheritdoc}
     */
    protected function makeService()
    {
        $driver = new ICareWebPageHttpDriver($this->conf);
        $this->service = new ICareWebPageHttpService($driver, $this->conf);
    }
}
