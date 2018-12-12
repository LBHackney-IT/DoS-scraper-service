<?php

namespace App\Plugins\WebPageScraper\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Request\HttpInvalidRequestException;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractWebPageScraperController extends Controller
{
    /**
     * @var Request – Lumen request object.
     */
    protected $request;

    /**
     * @var array - Request query array.
     */
    protected $query;

    /**
     * @var array
     */
    protected $extract;

    /**
     * @var array
     */
    protected $conf;

    /**
     * Does this controller need to use a CSS selector?
     *
     * @var bool
     */
    protected $selectorRequired = true;

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
        $this->conf = $conf;
        $this->setRequestQuery();

        if ($this->selectorRequired && empty($this->query['selector'])) {
            throw new HttpInvalidRequestException('Please set a CSS selector', 422);
        }
        if (!empty($this->query['extract'])) {
            $this->extract = is_array($this->query['extract']) ? $this->query['extract'] : [$this->query['extract']];
        } else {
            $this->extract = ['_text'];
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
     * @return array
     */
    public function getRequestQuery(): array
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getExtract(): array
    {
        return $this->extract;
    }

    /**
     * Make an HTTP service object for using in requests.
     *
     * @return void
     *
     * @throws \App\Http\Driver\Exception\HttpDriverClientException
     * @throws \App\Plugins\WebPageScraper\Http\WebPageHttpServiceException
     */
    abstract protected function makeService();

    /**
     * Extract stuff from the CSS selector results.
     *
     * @param Crawler $crawler
     *
     * @return array
     */
    protected function extractor(Crawler $crawler)
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
                'url' => $this->service->getUrl(),
            ],
            $e->getCode()
        );
    }
}
