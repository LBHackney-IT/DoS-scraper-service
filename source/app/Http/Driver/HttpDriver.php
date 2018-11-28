<?php

namespace App\Http\Driver;


use Http\Driver\Exception\HttpDriverClientException;

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
     * @throws \Http\Driver\Exception\HttpDriverClientException
     */
    public function __construct($conf)
    {
        parent::__construct($conf);

        $this->logRequests = config('httpdriver.log_requests');
        $this->logExceptions = config('httpdriver.log_exceptions');
        $this->logWithBacktrace = config('httpdriver.log_with_backtrace');
    }
}