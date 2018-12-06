<?php

namespace App\Plugins\WebPageScraper\Http;

use App\Http\Request\GetHelloRequest;
use App\Plugins\WebPageScraper\Http\Driver\WebPageHttpDriver;
use App\Plugins\WebPageScraper\Http\Response\AbstractWebPageResponse;
use App\Plugins\WebPageScraper\Http\Response\HelloWebPageResponse;

abstract class WebPageHttpService implements WebPageHttpServiceInterface
{

    /**
     * @var WebPageHttpDriver
     */
    protected $driver;

    /**
     * Configuration array.
     *
     * @var array
     */
    protected $conf;

    /**
     * @var \App\Plugins\WebPageScraper\Http\Response\AbstractWebPageResponse
     */
    protected $response;

    /**
     * Request URL string.
     *
     * @var string
     */
    protected $url;

    /**
     * WebPageHttpService constructor.
     *
     * @param WebPageHttpDriver $driver
     * @param array $conf
     *
     * @throws WebPageHttpServiceConfigurationException
     */
    public function __construct(WebPageHttpDriver $driver, array $conf = [])
    {
        if (!$driver->baseUrl) {
            throw new WebPageHttpServiceConfigurationException('No base URL set for this service driver');
        }
        $this->driver = $driver;
        $this->conf = $conf;
    }

    /**
     * Get the driver for this service.
     *
     * @return WebPageHttpDriver
     */
    protected function getDriver(): WebPageHttpDriver
    {
        return $this->driver;
    }

    /**
     * Get configuration.
     *
     * @return array
     */
    public function getConf(): array
    {
        return $this->conf;
    }


    /**
     * Check a web page is alive.
     *
     * @return bool
     *
     * @throws WebPageHttpUnavailableException
     */
    public function webPageCheckConnection()
    {
        $request = new GetHelloRequest();
        $response = $this->hello($request);
        if ($response->getStatusCode() == 200) {
            $this->response = $response;
            return true;
        } else {
            throw new WebPageHttpUnavailableException(
                'Web page not responding: ' . $this->driver->baseUrl,
                $response->getStatusCode()
            );
        }
    }

    /**
     * Set a formatted web page URL.
     *
     * @param string $path
     *   The web page sub-path.
     * @param string $query
     *   A formatted query string.
     */
    protected function setUrl($path)
    {
        $parts = parse_url($this->driver->baseUrl);
        $parts['path'] = empty($parts['path']) ? $path : "{$parts['path']}/{$path}";
        $this->url = $this->buildUrl($parts);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Build a full URL from some parts.
     *
     * @see parse_url().
     *   The opposite of ^^.
     *
     * @param array $parts
     *
     * @return string
     */
    private function buildUrl(array $parts)
    {
        return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') .
            ((isset($parts['user']) || isset($parts['host'])) ? '//' : '') .
            (isset($parts['user']) ? "{$parts['user']}" : '') .
            (isset($parts['pass']) ? ":{$parts['pass']}" : '') .
            (isset($parts['user']) ? '@' : '') .
            (isset($parts['host']) ? "{$parts['host']}" : '') .
            (isset($parts['port']) ? ":{$parts['port']}" : '') .
            (isset($parts['path']) ? "{$parts['path']}" : '') .
            (isset($parts['query']) ? "?{$parts['query']}" : '') .
            (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
    }

    /**
     * @return AbstractWebPageResponse
     */
    public function getResponse()
    {
        return $this->response;
    }


    /*************************** HTTP GET callbacks. ***************************/

    /**
     * Make a simple request for the API hello endpoint.
     *
     * @param \App\Http\Request\GetHelloRequest $request
     *   GetHelloRequest object.
     *
     * @return \App\Plugins\WebPageScraper\Http\Response\HelloWebPageResponse
     *   Hello response object.
     *
     * @throws
     */
    public function hello(GetHelloRequest $request)
    {
        $this->setUrl('/');
        return new HelloWebPageResponse($this->getDriver()->get($this->getUrl(), $request));
    }
}
