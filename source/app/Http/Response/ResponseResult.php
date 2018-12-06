<?php

namespace App\Http\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * A response result class.
 *
 * @package App\Http\Response
 */
class ResponseResult implements ResponseResultInterface
{
    /**
     * A Guzzle response object.
     *
     * @var \Psr\Http\Message\ResponseInterface
     */
    private $response;

    public function __construct(ResponseInterface $response)
    {
        $this->setResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public function setResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->getResponse()->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getResponse()->getReasonPhrase();
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->getResponse()->getProtocolVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->getResponse()->getHeaders();
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->getResponse()->getBody();
    }
}
