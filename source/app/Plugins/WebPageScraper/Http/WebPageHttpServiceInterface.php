<?php

namespace App\Plugins\WebPageScraper\Http;

/**
 * Interface for a sercice to make HTTP requests for web pages.
 *
 * @package App\Plugins\WebPageScraper\Http
 */
interface WebPageHttpServiceInterface
{
    /**
     * Check the connection to the web page.
     *
     * @return bool
     *   FALSE if the connection failed, otherwise the service is assumed to be
     *   up. If this function throws a WebPageHttpUnavailableException then FALSE is also
     *   assumed.
     */
    public function webPageCheckConnection();
}
