<?php

namespace App\Scraper\OpenObjectsScraper;

use App\Plugins\ApiScraper\Scraper\ApiScraperInterface;

/**
 * The ScraperPlugin base class
 *
 * @package App\Plugins\Scraper
 */
class OpenObjectsScraperPlugin implements ApiScraperInterface
{
    /**
     * The Plugin Name.
     *
     * @var string
     */
    public $name = 'open_objects_scraper';

    /**
     * A description of the plugin.
     *
     * @var string
     */
    public $description = 'A scraper for Open Objects.';

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
