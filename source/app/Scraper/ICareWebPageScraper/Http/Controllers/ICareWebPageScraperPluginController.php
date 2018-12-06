<?php

namespace App\Scraper\ICareWebPageScraper\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Driver\Exception\HttpDriverClientException;
use App\Plugins\WebPageScraper\Http\WebPageHttpServiceException;
use App\Scraper\ICareWebPageScraper\Http\Driver\ICareWebPageHttpDriver;
use App\Scraper\ICareWebPageScraper\Http\ICareWebPageHttpService;

/**
 * Class ICareWebPageScraperPluginController
 *
 * @package App\Scraper\ICareWebPageScraper\Http\Controllers
 */
class ICareWebPageScraperPluginController extends Controller
{
    /** @var string */
    protected $baseUrl = 'https://www.hackneyicare.org.uk';

    public function retrieve()
    {
        return [1, 2, 3];
    }


    /**
     * Hello
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function hello()
    {
        try {
            $conf = [
                'base_url' => $this->baseUrl,
            ];
            $driver = new ICareWebPageHttpDriver($conf);
            $service = new ICareWebPageHttpService($driver);
            $service->webPageCheckConnection();
            return response()->json([
                'base_url' => $service->getUrl(),
                'message' => $service->getResponse()->getStatus(),
                'code' => $service->getResponse()->getStatusCode(),
                'body' => $service->getResponse()->getBody(),
            ]);
        } catch (HttpDriverClientException $e) {
            return response()->json(
                [
                    'error' => true,
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                ],
                $e->getCode()
            );
        } catch (WebPageHttpServiceException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
