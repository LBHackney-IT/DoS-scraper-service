<?php

namespace App\Http\Response;

use App\Http\Request\HttpInvalidRequestException;
use ArrayIterator;
use IteratorAggregate;

abstract class AbstractResponse implements ResponseInterface, IteratorAggregate
{
    /**
     * The raw response.
     *
     * E.g. decoded JSON data.
     *
     * @var array
     */
    protected $rawResponse;

    /**
     * Response code.
     *
     * @var int
     */
    protected $statusCode;

    /**
     * Response status.
     *
     * @var string
     */
    protected $status;

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
     * The content of the response.
     *
     * @var array|object|string
     */
    protected $items;

    /**
     * Item ID, if applicable.
     *
     * @var int
     */
    protected $id;

    /**
     * AbstractResponse constructor.
     *
     * @param array $rawResponse
     *   The raw response from the HTTP request. Individual items will be found by implementing classes.
     *
     * @throws \App\Http\Request\HttpInvalidRequestException
     */
    public function __construct(array $rawResponse)
    {
        $this->rawResponse = $rawResponse;
        $this->processResponse();
    }

    /**
     * Process the response from the request.
     *
     * @throws \App\Http\Request\HttpInvalidRequestException
     */
    protected function processResponse()
    {
        // First, set status and status code.
        $this->setStatusCode($this->rawResponse['statusCode']);
        $this->setStatus($this->rawResponse['status']);
        // Next, validate the response.
        $this->validateResponse();
        // All good. Now, process the response.
        $data = $this->rawResponse['body'];
        if (!empty($this->rawResponse['id'])) {
            $this->id = $this->rawResponse['id'];
        }
        $this->items = $this->buildItems($data);
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

        // Are there field errors?
        if (!empty($this->rawResponse['errors'])) {
            $errors = array();
            foreach ($this->rawResponse['errors'] as $error) {
                $errors[] = t('@field: @message', array(
                    '@field' => $error['field'],
                    '@message' => $error['message'],
                ));
            }
            $this->failureReason = implode('; ', $errors);
            throw new HttpInvalidRequestException($this->failureCode . ' : ' . $this->failureReason);
        } else {
            throw new HttpInvalidRequestException($this->failureCode . ' : ' . $this->failureReason);
        }
    }

    /**
     * Convert a list of items into model object(s).
     *
     * @param array|object $items
     *   Array or object of items from the response body.
     */
    abstract protected function buildItems($items);

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
        return $this->statusCode;
    }

    /**
     * Set the response status code.
     *
     * @param int $statusCode
     *   Response status code.
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Get response status.
     *
     * @return string
     *   Response status.
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the response status.
     *
     * @param string $status
     *   Response status.
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get the raw response.
     *
     * @return array
     *   The raw response.
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse()
    {
        return $this->getRawResponse()['response'];
    }

    /**
     * Get the response headers.
     *
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->getRawResponse()['headers'];
    }

    /**
     * Get the response body.
     *
     * @return mixed
     */
    public function getBody()
    {
        return $this->getRawResponse()['body'];
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
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
