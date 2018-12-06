<?php

namespace App\Http\Response;

use App\Http\Request\HttpInvalidRequestException;
use ArrayIterator;
use IteratorAggregate;

abstract class AbstractResponse implements ResponseInterface, IteratorAggregate
{
    /**
     * A response result object.
     *
     * @var \App\Http\Response\ResponseResult
     */
    protected $responseResult;

    /**
     * Response body data.
     *
     * @var mixed
     */
    protected $data;

//
//    /**
//     * Response code.
//     *
//     * @var int
//     */
//    protected $statusCode;
//
//    /**
//     * Response status.
//     *
//     * @var string
//     */
//    protected $status;
//
    /**
     * Failure reason, if applicable.
     *
     * @var string
     */
    protected $failureReason;

    /**
     * Failure code, if applicable.
     *
     * @var int
     */
    protected $failureCode;

    /**
     * AbstractResponse constructor.
     *
     * @param \App\Http\Response\ResponseResult $responseResult
     *   The response result object from the HTTP request. Individual items will be found by implementing classes.
     *
     * @throws \App\Http\Request\HttpInvalidRequestException
     */
    public function __construct($responseResult)
    {
        $this->responseResult = $responseResult;
        $this->processResponse();
    }

    /**
     * Process the response from the request.
     *
     * @throws \App\Http\Request\HttpInvalidRequestException
     */
    protected function processResponse()
    {
//        // First, set status and status code.
//        $this->setStatusCode($this->getResponseResult()->getStatusCode());
//        $this->setStatus($this->rawResponse['status']);
        // Next, validate the response.
        $this->validateResponse();
        // All good. Now, process the response.
        $this->data = $this->getBody()->getContents();
//        if (!empty($this->rawResponse['id'])) {
//            $this->id = $this->rawResponse['id'];
//        }
//        $this->items = $this->buildItems($data);
    }

    /**
     * Validates the returned response.
     *
     * @throws \App\Http\Request\HttpInvalidRequestException
     */
    protected function validateResponse()
    {
        if ($this->getStatusCode() == 200) {
            // No response code or error status means we have a successful response!
            return;
        }

        $this->failureCode = $this->getStatusCode();
        $this->failureReason = $this->getStatus();
        throw new HttpInvalidRequestException($this->failureCode . ' | ' . $this->failureReason);
    }

    /**
     * Allow looping over the returned items.
     *
     * @return ArrayIterator
     *   ArrayIterator object.
     */
    public function getIterator()
    {
        if (empty($this->items)) {
            $this->items = array();
        }
        return new ArrayIterator($this->items);
    }

    /**
     * Get response status code.
     *
     * @return int
     *   Response status code.
     */
    public function getStatusCode()
    {
        return $this->getResponseResult()->getStatusCode();
    }

    /**
     * Get response status.
     *
     * @return string
     *   Response status.
     */
    public function getStatus()
    {
        return $this->getResponseResult()->getStatus();
    }

    /**
     * Get the raw response.
     *
     * @return \App\Http\Response\ResponseResult
     *   The response result object.
     */
    public function getResponseResult()
    {
        return $this->responseResult;
    }

    /**
     * Get the Guzzle response object.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse()
    {
        return $this->getResponseResult()->getResponse();
    }

    /**
     * Get the response headers.
     *
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->getResponseResult()->getHeaders();
    }

    /**
     * Get the response body stream interface.
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getBody()
    {
        return $this->getResponseResult()->getBody();
    }

    /**
     * Get the body content data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the response body items array or object.
     *
     * @return array|object
     */
    public function getItems()
    {
        return $this->items;
    }
}
