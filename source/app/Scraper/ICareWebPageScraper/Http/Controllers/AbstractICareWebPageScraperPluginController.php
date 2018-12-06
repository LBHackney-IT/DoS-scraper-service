<?php

namespace App\Scraper\ICareWebPageScraper\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Scraper\ICareWebPageScraper\Http\Driver\ICareWebPageHttpDriver;
use App\Scraper\ICareWebPageScraper\Http\ICareWebPageHttpService;

/**
 * Class ICareWebPageScraperPluginController
 *
 * @package App\Scraper\ICareWebPageScraper\Http\Controllers
 */
abstract class AbstractICareWebPageScraperPluginController extends Controller
{
    /** @var string */
    protected $baseUrl = 'https://www.hackneyicare.org.uk';

    /**
     * @var \App\Plugins\WebPageScraper\Http\WebPageHttpService
     */
    protected $service;

    /**
     * Make an HTTP service object for using in requests.
     *
     * @throws \App\Http\Driver\Exception\HttpDriverClientException
     * @throws \App\Plugins\WebPageScraper\Http\WebPageHttpServiceException
     */
    protected function makeService()
    {
        $conf = [
            'base_url' => $this->baseUrl,
        ];
        $driver = new ICareWebPageHttpDriver($conf);
        $this->service = new ICareWebPageHttpService($driver);
    }

    /**
     * Consistent formatting of Exception responses into a JSON response.
     *
     * @param \Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function exceptionResponse(\Exception $e)
    {
        return response()->json(
            [
                'error' => true,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ],
            $e->getCode()
        );
    }
}
