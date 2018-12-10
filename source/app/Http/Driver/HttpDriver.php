<?php

namespace App\Http\Driver;

use GuzzleHttp\HandlerStack;

/**
 * HTTP request/response driver.
 *
 * @package App\Http\Driver
 */
class HttpDriver extends AbstractHttpDriver
{

    /**
     * Http request/response driver constructor.
     *
     * @param array $conf
     *   API request configuration array.
     *
     * @throws \App\Http\Driver\Exception\HttpDriverClientException
     */
    public function __construct(array $conf)
    {
        parent::__construct($conf);

        $this->logRequests = config('httpdriver.log_requests');
        $this->logExceptions = config('httpdriver.log_exceptions');
        $this->logWithBacktrace = config('httpdriver.log_with_backtrace');
    }

    /**
     * Get Guzzle options.
     *
     * @return array
     *   Guzzle request options.
     */
    public function getGuzzleOptions()
    {
        $options = parent::getGuzzleOptions();
        $options['base_uri'] = $this->baseUrl;
        // Add effective URL Guzzle middleware handler.
        $stack = HandlerStack::create();
        $stack->push(HttpEffectiveUrlMiddleware::middleware());
        $options['handler'] = $stack;
        return $options;
    }
}
