<?php

namespace App\Scraper\ICareWebPageScraper\Http\Controllers;

use App\Http\Driver\Exception\HttpDriverClientException;
use App\Plugins\WebPageScraper\Http\WebPageHttpServiceException;

/**
 * Class ICareWebPageScraperPluginHelloController
 *
 * @package App\Scraper\ICareWebPageScraper\Http\Controllers
 */
class ICareWebPageScraperPluginHelloController extends AbstractICareWebPageScraperPluginController
{
    /**
     * Hello
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function hello()
    {
        try {
            $this->makeService();
            $this->service->webPageCheckConnection();
            return response()->json([
                'message' => $this->service->getResponse()->getStatus(),
                'code' => $this->service->getResponse()->getStatusCode(),
                'base_url' => $this->service->getUrl(),
            ]);
        } catch (HttpDriverClientException $e) {
            return $this->exceptionResponse($e);
        } catch (WebPageHttpServiceException $e) {
            return $this->exceptionResponse($e);
        }
    }
}
