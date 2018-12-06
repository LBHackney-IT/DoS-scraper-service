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
     * @param \Psr\Http\Message\ResponseInterface $response
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
     * Retrieves the HTTP protocol version as a string.
     *
     * @return string
     */
    public function getProtocolVersion();

    /**
     * Get the response headers.
     *
     * @return array
     *   Headers array.
     */
    public function getHeaders();

    /**
     * Get the response body.
     *
     * @return \Psr\Http\Message\StreamInterface
     *   Returns the body as a stream.
     */
    public function getBody();
}
