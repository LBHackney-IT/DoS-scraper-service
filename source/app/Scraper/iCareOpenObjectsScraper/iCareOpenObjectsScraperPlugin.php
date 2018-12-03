<?php

namespace App\Scraper\iCareOpenObjectsScraper;

use App\Plugins\ApiScraper\Scraper\ApiScraper;

/**
 * A scraper for iCare Open Objects.
 *
 * @package App\Scraper\OpenObjectsScraper
 */
class iCareOpenObjectsScraperPlugin extends ApiScraper
{
    /**
     * The Plugin Name.
     *
     * @var string
     */
    public $name = 'icare_open_objects_scraper';

    /**
     * A description of the plugin.
     *
     * @var string
     */
    public $description = 'A scraper for the iCare Open Objects API.';

    /**
     * The version of the plugin.
     *
     * @var string
     */
    public $version = '0.1';

    /**
     * @return void
     */
    public function boot()
    {
        // TODO: Implement boot() method.
    }
}
