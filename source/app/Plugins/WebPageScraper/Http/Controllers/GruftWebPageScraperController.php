<?php

namespace App\Plugins\WebPageScraper\Http\Controllers;

use App\Jobs\ProcessWebPageScrapeJob;

class GruftWebPageScraperController
{

    public function process()
    {
        $webPage = [
            'package' => 'icare_webpage_scraper_package',
        ];
        $processor = new ProcessWebPageScrapeJob($webPage);
        return response()->json($processor->getWebPageScrapers());
    }
}
