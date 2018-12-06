<?php

namespace App\Scraper\ICareWebPageScraper\Http\Controllers;

use App\Http\Driver\Exception\HttpDriverClientException;
use App\Http\Driver\Exception\HttpDriverServerException;
use App\Plugins\WebPageScraper\Http\WebPageHttpServiceException;
use App\Scraper\ICareWebPageScraper\Http\Request\GetICareServiceRequest;

/**
 * Class ICareWebPageScraperPluginController
 *
 * @package App\Scraper\ICareWebPageScraper\Http\Controllers
 */
class ICareWebPageScraperPluginController extends AbstractICareWebPageScraperPluginController
{
    protected $path = '/kb5/hackney/asch/service.page';

    public function __construct(array $conf = [])
    {
        $conf['path'] = $this->path;
        parent::__construct($conf);
    }

    public function retrieve($id)
    {
        try {
            $this->makeService();
            $request = new GetICareServiceRequest();
            $request->setQueryParameter('id', $id);
            $response = $this->service->iCareService($request);
            return [
                'message' => $this->service->getResponse()->getStatus(),
                'code' => $this->service->getResponse()->getStatusCode(),
                'url' => $this->service->getUrl(),
                'id' => $id,
                'response' => [
                    'headers' => $response->getResponseHeaders(),
                ],
            ];
        } catch (HttpDriverServerException $e) {
            return $this->exceptionResponse($e);
        } catch (HttpDriverClientException $e) {
            return $this->exceptionResponse($e);
        } catch (WebPageHttpServiceException $e) {
            return $this->exceptionResponse($e);
        }
    }
}
