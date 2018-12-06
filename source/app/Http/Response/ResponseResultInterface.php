<?php

namespace App\Http\Response;

use Psr\Http\Message\ResponseInterface;

interface ResponseResultInterface
{

    /**
     * Get the Guzzle Response object.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse();

    /**
     * Set the Guzzle Response object.
     *
     * @param ResponseInterface $response
     *
     * @return void
     */
    public function setResponse(ResponseInterface $response);

    /**
     * Get the response status code.
     *
     * @return int
     */
    public function getStatusCode();

    /**
     * Get the response status message.
     *
     * @return string
     */
    public function getStatus();

    /**
     * Get the response headers.
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Get the response body.
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getBody();
}
