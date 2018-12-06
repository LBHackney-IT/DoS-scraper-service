<?php

namespace App\Scraper\ICareWebPageScraper\Http;

use App\Plugins\WebPageScraper\Http\WebPageHttpService;
use App\Scraper\ICareWebPageScraper\Http\Request\GetICareServiceRequest;
use App\Scraper\ICareWebPageScraper\Http\Response\ICareWebPageServiceResponse;

/**
 * Class ICareWebPageHttpService
 *
 * @package App\Scraper\ICareWebPageScraper\Http
 */
class ICareWebPageHttpService extends WebPageHttpService
{
    /**
     * Get an iCare service web page.
     *
     * @param GetICareServiceRequest $request
     *
     * @return ICareWebPageServiceResponse
     *
     * @throws \App\Http\Driver\Exception\HttpDriverClientException
     * @throws \App\Http\Driver\Exception\HttpDriverServerException
     * @throws \App\Http\Request\HttpInvalidRequestException
     */
    public function iCareService(GetICareServiceRequest $request)
    {
        $path = $this->getConf()['path'];
        $this->setUrl($path);
        $this->response = new ICareWebPageServiceResponse($this->getDriver()->get($this->getUrl(), $request));
        return $this->response;
    }
}
