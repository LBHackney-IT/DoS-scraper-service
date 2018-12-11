<?php

namespace App\Scraper\ICareWebPageScraper\Http\Controllers;

use App\Http\Driver\Exception\HttpDriverClientException;
use App\Http\Driver\Exception\HttpDriverServerException;
use App\Http\Request\HttpInvalidRequestException;
use App\Plugins\WebPageScraper\Http\WebPageHttpServiceException;
use App\Scraper\ICareWebPageScraper\Http\Request\GetICareServiceRequest;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ICareWebPageScraperPluginController
 *
 * @package App\Scraper\ICareWebPageScraper\Http\Controllers
 */
class ICareWebPageScraperPluginController extends AbstractICareWebPageScraperPluginController
{
    /**
     * @var string - The base path on the iCare website.
     */
    protected $path = '/kb5/hackney/asch/service.page';

    /**
     * ICareWebPageScraperPluginController constructor.
     *
     * @param Request $request
     * @param array $conf
     *
     * @throws HttpInvalidRequestException
     */
    public function __construct(Request $request, array $conf = [])
    {
        $conf['path'] = $this->path;
        parent::__construct($request, $conf);
    }

    /**
     * Retrieve stuff from a service page on the Hackney iCare website.
     *
     * @param string $id – Service id on the Hackney iCare web site.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function retrieve($id)
    {
        try {
            // Create the request/response service.
            $this->makeService();
            // Prepare the request.
            $request = new GetICareServiceRequest();
            $request->setQueryParameter('id', $id);
            // Do the request and get the response.
            $response = $this->service->iCareService($request);
            // Assume the status code from the iCare website is good place to start.
            $status = $this->service->getResponse()->getStatusCode();
            // Build the data to return.
            $build = [
                'message' => $this->service->getResponse()->getStatus(),
                'code' => null,
                'url' => current($response->getResponseHeaders()['X-GUZZLE-EFFECTIVE-URL']),
                'id' => $id,
            ];
            // Get the iCare service page data from the response.
            $html = $response->getData();
            // A crawler tool to traverse the data with a CSS selector.
            $crawler = new Crawler($html);
            // Page title.
            $build['label'] = $crawler->filter('#content > h1')->text();
            $dom = [];
            if (!empty($this->query['selector'])) {
                $build['selector'] = $this->query['selector'];
                try {
                    $selected = $crawler->filter($this->query['selector']);
                    $dom = $this->extractor($selected);
                } catch (\InvalidArgumentException $e) {
                    $build['message'] = sprintf(
                        'No data could be retrieved with the current selector: %s',
                        $this->query['selector']
                    );
                    $status = 422;
                }
            } else {
                $build['message'] = 'Please set a CSS selector';
                $build['selector'] = null;
                $status = 400;
            }
            $build['code'] = $status;
            $build['response'] = [
                'dom' => $dom,
                'headers' => $response->getResponseHeaders(),
            ];
            return response()->json($build, $status);
        } catch (HttpDriverServerException $e) {
            return $this->exceptionResponse($e);
        } catch (HttpDriverClientException $e) {
            return $this->exceptionResponse($e);
        } catch (WebPageHttpServiceException $e) {
            return $this->exceptionResponse($e);
        } catch (HttpInvalidRequestException $e) {
            return $this->exceptionResponse($e);
        }
    }
}
