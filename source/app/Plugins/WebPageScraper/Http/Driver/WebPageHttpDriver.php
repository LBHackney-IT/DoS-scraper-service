<?php

namespace App\Plugins\WebPageScraper\Http\Driver;

use App\Http\Driver\HttpDriver;
use Psr\Http\Message\ResponseInterface;

/**
 * An HTTP request/response driver for requesting web pages.
 *
 * @package App\Plugins\WebPageScraper\Http\Driver
 */
abstract class WebPageHttpDriver extends HttpDriver implements WebPageHttpDriverInterface
{
    /**
     * Get the useful stuff out of the response and return as an array.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *   Response object.
     *
     * @return array
     *   Result array.
     */
    public function getResponseResult(ResponseInterface $response)
    {
        // Create the cacheable results array.
        $result = array(
            'body' => $response->getBody(),
            'statusCode' => $response->getStatusCode(),
            'status' => $response->getReasonPhrase(),
            'headers' => $response->getHeaders(),
            'protocol' => $response->getProtocolVersion(),
            'response' => $response,
        );
        // Are there any errors in the result body.
        if (!empty($body['errors'])) {
            $result['errors'] = $body['errors'];
        }
        return $result;
    }
}
