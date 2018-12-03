<?php

namespace App\Http\Driver;

use App\Http\Request\RequestInterface;
use App\Http\Request\RequestPostInterface;
use App\Http\Request\RequestPutInterface;

interface HttpDriverInterface
{
    /**
     * Provide configuration options in an array.
     *
     * All options will be supplied and drivers can choose which to implement.
     *
     * @param array $conf
     *   Array of configuration parameters.
     */
    public function __construct($conf);

    /**
     * Make a GET request.
     *
     * @param string $endpoint
     *   Path of the web resource endpoint.
     * @param RequestInterface|null $request
     *   GET request object.
     *
     * @return array
     *   Request response.
     */
    public function get($endpoint, RequestInterface $request = null);


    /**
     * At this stage, we are assuming we will only need to GET things from an API or web page,
     * but we may need POST at a later stage. Probably not PUT or DELETE, tho.
     */

    /**
     * Make a POST request.
     *
     * @param string $endpoint
     *   Path of the web resource endpoint.
     * @param RequestPostInterface|null $request
     *   POST request object.
     *
     * @return array
     *   Request response.
     */
//    public function post($endpoint, RequestPostInterface $request = null);

    /**
     * Make a PUT request.
     *
     * @param string $endpoint
     *   Path of the web resource endpoint.
     * @param RequestPutInterface|null $request
     *   PUT request object.
     *
     * @return array
     *   Request response.
     */
//    public function put($endpoint, RequestPutInterface $request = null);

    /**
     * Make a DELETE request.
     *
     * @param string $endpoint
     *   Path of the web resource endpoint.
     * @param RequestInterface|null $request
     *   DELETE request object.
     *
     * @return array
     *   Request response.
     */
//    public function delete($endpoint, RequestInterface $request = null);
}
