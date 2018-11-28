<?php

namespace App\Http\Request;

/**
 * API request interface definition RequestInterface.
 *
 * @package App\Component\Api\Request
 */
interface RequestInterface
{

    /**
    * Method for calling any user api resource.
    *
    * @param string $path
    *   Path element to call.
    * @param array $options
    *   An associative array of options for the HTTP request. Default values.
    *   - 'params': associative array of parameters to POST/PUT in HTTP requests.
    *   - 'headers': array, associative array of request header parameters.
    *   - 'method': string, HTTP request method; defaults to GET.
    *   - 'use_auth': boolean, use HTTP Auth; defaults to FALSE.
    *   - 'alt': string, use alternative API alternative.
    *   - 'api_path': string, path of REST endpoint.
    *   - 'csrf_token': string, CSRF-prevention token; defaults to NULL.
    * @param bool $full_response
    *   Return the full response.
    *
    * @return object
    *   A parsed response or FALSE.
    */
    //  public function call($path, array $options = array(), $full_response = FALSE);

    /**
    * Validate a request.
    *
    * @return array
    *   Array of missing parameters.
    */
    public function validate();

    /**
    * Get request parameters.
    *
    * @return array
    *   Array of request parameters.
    */
    public function getParams();

    public function setParam($key, $value);

    public function setParams(array $params);

    public function getParam($key, $default = null);

    public function getRequiredParams();

    public function getOptionalParams();

    public function setQueryParameter($key, $value);

    public function setQueryParameters(array $params);

    public function getQueryParameter($key, $default = null);

    public function getQueryParameters();

    public function isCacheable();

    /**
    * Set the cache reset flag.
    *
    * @param bool $resetCache
    *   Cache reset flag value.
    */
    public function setResetCache($resetCache);

    /**
    * Should we reset the cache on this request?
    *
    * @return bool
    *   Flag: reset the request cache.
    */
    public function resetCache();

    public function getCacheLifetime();

    /**
    * Should this request use session authentication?
    */
    public function useSessionAuth();

    /**
    * Get the session CSRF-Token value.
    */
    public function getCsrfToken();

    /**
    * Set the session name.
    */
    public function getSessionName();

    /**
    * Set the session ID.
    */
    public function getSessionId();

}
