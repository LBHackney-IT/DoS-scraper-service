<?php

namespace App\Scraper\ICareWebPageScraper\Http\Controllers;

use App\Http\Driver\Exception\HttpDriverClientException;
use App\Http\Driver\Exception\HttpDriverServerException;
use App\Http\Request\HttpInvalidRequestException;
use App\Plugins\WebPageScraper\Http\WebPageHttpServiceException;
use App\Scraper\ICareWebPageScraper\Http\Request\GetICareServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ICareWebPageScraperPluginController
 *
 * @package App\Scraper\ICareWebPageScraper\Http\Controllers
 */
class ICareWebPageScraperPluginController extends AbstractICareWebPageScraperPluginController
{
    /**
     * @var Request – Lumen request object.
     */
    protected $request;

    /**
     * @var string - The base path on the iCare website.
     */
    protected $path = '/kb5/hackney/asch/service.page';

    /**
     * @var array - Request query array.
     */
    protected $query;

    /**
     * @var array
     */
    protected $extract;

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
        $this->request = $request;
        $conf['path'] = $this->path;
        parent::__construct($conf);
        $this->setRequestQuery();
        if (empty($this->query['selector'])) {
            throw new HttpInvalidRequestException('Please set a CSS selector', 422);
        }
        if (!empty($this->query['extract'])) {
            $this->extract = is_array($this->query['extract']) ? $this->query['extract'] : [$this->query['extract']];
        } else {
            $this->extract = ['_text'];
        }
    }

    public function retrieve($id)
    {
        try {
            $this->makeService();
            $request = new GetICareServiceRequest();
            $request->setQueryParameter('id', $id);
            $response = $this->service->iCareService($request);
            $status = $this->service->getResponse()->getStatusCode();
            // The data to return.
            $build = [
                'message' => $this->service->getResponse()->getStatus(),
                'code' => null,
                'url' => current($response->getResponseHeaders()['X-GUZZLE-EFFECTIVE-URL']),
                'id' => $id,
            ];
            $html = $response->getData();
            $crawler = new Crawler($html);
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
            return Response::create($build, $status);
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

    /**
     * Set the query from the request.
     */
    protected function setRequestQuery()
    {
        $qs = $this->request->getQueryString();
        parse_str($qs, $query);
        $this->query = $query;
    }

    /**
     * Cleanup multi-line text.
     *
     * Tidy up messy markup on the way.
     *
     * @param string $text - Text string.
     *
     * @return string - Cleaner text string.
     */
    protected function multiLineTextCleanup($text)
    {
        // Remove multiple tabs.
        $text = preg_replace('/[\t]+/S', "", $text);
        // Convert multiple spaces to single spaces.
        $text = preg_replace('/[ ]+/S', " ", $text);
        // Convert multiple new lines (unix or windoze) to new lines.
        $text = preg_replace('/[\r\n]+/S', "\n", $text);
        // Finally trim whitespace.
        return trim($text);
    }

    /**
     * Extract stuff from the CSS selector results.
     *
     * @param Crawler $crawler
     *
     * @return array
     */
    private function extractor(Crawler $crawler)
    {
        $dom = [];
        foreach ($this->extract as $type) {
            $extracted = null;
            switch ($type) {
                case '_text':
                    $extracted = $this->extractText($crawler);
                    break;

                default:
                    $extracted = $crawler->extract([$type]);
            }
            if (isset($extracted)) {
                $dom[$type] = $extracted;
            }
        }
        return $dom;
    }

    /**
     * Extract (and clean) text from a selected DOM node.
     *
     * @param Crawler $crawler
     *
     * @return array
     */
    private function extractText(Crawler $crawler)
    {
        $dom = [
            'raw' => $crawler->text(),
            'clean' => $this->multiLineTextCleanup($crawler->text()),
        ];
        return $dom;
    }

}
