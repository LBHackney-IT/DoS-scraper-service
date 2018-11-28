<?php

namespace App\Http\Request;

/**
 * API request interface definition RequestPostInterface.
 *
 * @package App\Component\Api\Request;
 */
interface RequestPostInterface extends RequestInterface
{
    /**
     * Get the POST parameters passed to this request.
     *
     * @return array
     */
    public function getPostParams();

    /**
     * Get the request body for this POST request.
     *
     * @return mixed
     */
    public function getHttpPostRequestBody();
}
