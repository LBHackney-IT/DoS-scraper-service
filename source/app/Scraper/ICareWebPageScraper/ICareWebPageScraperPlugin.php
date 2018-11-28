<?php

namespace App\Scraper\ICareWebPageScraper;

use App\Plugins\WebPageScraper\Scraper\WebPageScraper;

class ICareWebPageScraperPlugin extends WebPageScraper
{
    /**
     * @var string
     */
    public $name = 'icare_webpage_scraper';

    /**
     * @var string
     */
    public $description = 'iCare webpage scraper';

    /**
     * @var string
     */
    public $version = '0.1';

    public function boot()
    {
        // TODO: Implement boot() method.
    }
}
