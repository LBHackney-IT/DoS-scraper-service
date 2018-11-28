<?php

namespace App\Http\Request;

/**
 * API request interface definition RequestPutInterface.
 *
 * @package Drupal\jac_web_backend_services\Api\Request
 */
interface RequestPutInterface extends RequestInterface
{
    /**
     * Get the PUT parameters passed to this request.
     *
     * @return array
     */
    public function getPutParams();

    /**
     * Get the request body for this PUT request.
     *
     * @return mixed
     */
    public function getHttpPutRequestBody();
}
