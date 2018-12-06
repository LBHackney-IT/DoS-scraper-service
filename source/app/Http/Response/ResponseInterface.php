<?php

namespace App\Http\Response;

/**
 * Interface ResponseInterface.
 *
 * @package Drupal\jac_web_backend_services\Api\Response
 */
interface ResponseInterface
{

    /**
     * AbstractResponse constructor.
     *
     * @param \App\Http\Response\ResponseResult $responseResult
     *   The response result object from the HTTP request. Individual items will be found by implementing classes.
     *
     * @throws \App\Http\Request\HttpInvalidRequestException
     */
    public function __construct($responseResult);

    /**
     * Get response status code.
     *
     * @return int
     *   Response status code.
     */
    public function getStatusCode();

}
