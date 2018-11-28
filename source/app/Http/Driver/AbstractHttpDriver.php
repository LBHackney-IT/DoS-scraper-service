<?php

namespace App\Http\Driver;

use App\Http\Request\HttpInvalidRequestException;
use App\Http\Request\RequestInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Http\Driver\Exception\HttpDriverClientException;
use Http\Driver\Exception\HttpDriverServerException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

/**
 * Abstract implementation of the HTTP request driver interface.
 *
 * @package App\Http\Driver
 */
class AbstractHttpDriver implements HttpDriverInterface
{
    const LOG_TYPE_DISABLED = 0;
    const LOG_TYPE_DISPLAY = 1;
    const LOG_TYPE_LOG_FILE = 2;

    /**
     * Resource base URL.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Guzzle client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Configuration values.
     *
     * @var array
     */
    protected $conf;

    /**
     * Format of an API request.
     *
     * @var string|null
     */
    protected $apiFormat = 'json';

    /**
     * Request headers array.
     *
     * @var array
     */
    protected $headers = array();

    /** @var bool  */
    protected $logRequests = false;

    /** @var bool  */
    protected $logExceptions = false;

    /** @var bool  */
    protected $logWithBacktrace = false;

    /** @var int */
    protected $logType = self::LOG_TYPE_DISABLED;

    /**
     * AbstractHttpDriver constructor.
     *
     * @param array $conf - Driver configuration array.
     * @param string $apiFormat - Format of the API if requested.
     *
     * @throws HttpDriverClientException
     */
    public function __construct($conf, $apiFormat = null)
    {
        $this->conf = $conf;
        if (empty($this->conf['base_url'])) {
            throw new HttpDriverClientException(('The HTTP request driver requires a valid base URL');
        }
        $this->baseUrl = $conf['base_url'];

        $this->client = new Client($this->getGuzzleOptions());
        $this->apiFormat = isset($apiFormat) ? $apiFormat : $this->apiFormat;
        if (!empty($this->conf['request'])) {
            /** @var \Illuminate\Http\Request $request */
            $request = $this->conf['request'];
            $this->setHeader('X-Forwarded-For', $request->ip());
        }
    }


    /**
     * Get the options for Guzzle.
     *
     * @return array
     *   Array of options for Guzzle.
     */
    public function getGuzzleOptions()
    {
        return array();
    }

    /**
     * Make a GET request on a resource.
     *
     * @param string $endpoint
     * @param RequestInterface|null $request
     *
     * @return array|bool
     *
     * @throws HttpInvalidRequestException
     * @throws HttpDriverServerException
     */
    public function get($endpoint, RequestInterface $request = null)
    {
        if ($request) {
            if ($errors = $request->validate()) {
                throw new HttpInvalidRequestException(
                    'Invalid request to "' . $endpoint . '". ' . implode(' ', $errors)
                );
            }
        }
        try {
            if (count($request->getQueryParameters()) > 0) {
                $options['query'] = $request->getQueryParameters();
            }
            // Add any request headers.
            $options['headers'] = $this->getHeaders();
            $response = $this->client->request('GET', $endpoint, $options);
            $result = $this->getResponseResult($response);
        } catch (ClientException $e) {
            // If you need to debug exceptions then you can do so nicely by
            // (string) $ex->getRequest() and (string) $ex->getResponse().
            $this->logException(strtoupper(__FUNCTION__), $e->getResponse()->getHeaderLine('X-GUZZLE-EFFECTIVE-URL'), $options, $this->decodeError($e));
            throw new HttpDriverServerException($$this->decodeError($e),$e->getCode(),  false, $e);
        } catch (ServerException $e) {
            // Same here.
            $this->logException(strtoupper(__FUNCTION__), $e->getResponse()->getHeaderLine('X-GUZZLE-EFFECTIVE-URL'), $options, $this->decodeError($e));
            throw new HttpDriverServerException($this->decodeError($e), $e->getCode(), false, $e);
        }
        $this->logRequest(strtoupper(__FUNCTION__), $response->getHeaderLine('X-GUZZLE-EFFECTIVE-URL'), $options, !isset($response), $result);

        return $result;
    }

    /**
     * Get the useful stuff out of the response and return as an array.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *   Response object.
     *
     * @return array
     *   Result array.
     */
    public function getResponseResult(ResponseInterface $response) {
        // Get the response body.
        $body = json_decode($response->getBody(), TRUE);
        // If the result is not an array set it to be an array.
        if (!is_array($body)) {
            $body = array($body);
        }
        // Create the cacheable results array.
        $result = array(
            'body' => $body,
            'statusCode' => $response->getStatusCode(),
            'status' => $response->getReasonPhrase(),
            'protocol' => $response->getProtocolVersion(),
        );
        // Are there any errors in the result body.
        if (!empty($body['errors'])) {
            $result['errors'] = $body['errors'];
        }
        return $result;
    }

    /**
     * Set a value in the request headers array.
     *
     * @param string $key
     *   Header key name.
     * @param mixed $value
     *   Header value.
     */
    protected function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Format the endpoint for the requested JARS format.
     *
     * @param string $endpoint
     *   Request JARS endpoint.
     *
     * @return string
     *   Endpoint string with JARS API format appended.
     */
    protected function formatEndpoint($endpoint)
    {
        return $endpoint . '.' . $this->apiFormat;
    }


    /**
     * Provide efficient exception reporting.
     *
     * @param BadResponseException $ex
     *
     * @return string
     */
    public function decodeError(BadResponseException $ex) {
        return ltrim($ex->getResponse()->getReasonPhrase(), ': ');
    }

    /**
     * Determine if and how to log API requests.
     *
     * @param $method
     * @param $endpoint
     * @param $options
     * @param $cached
     * @param $response
     */
    public function logRequest($method, $endpoint, $options, $cached, $response) {
        if (!$this->logType) {
            return;
        }

        $log_data = array(
            'request' => array(
                'method' => $method,
                'endpoint' => $endpoint,
                'options' => $options,
            ),
            'response' => $response,
            'cache' => $cached ? 'hit' : 'miss',
            // Have to re-encode for the purposes of cache hits.
            'raw_response' => json_encode($response),
        );
        if ($this->logWithBacktrace) {
            $log_data['backtrace'] = debug_backtrace();
        }
        $this->log($endpoint, $log_data);
    }
    /**
     * Logs exception messages to a separate log file.
     *
     * @param $method
     * @param $endpoint
     * @param $options
     * @param $exception_message
     */
    public function logException($method, $endpoint, $options, $exception_message)
    {
        if (!$this->logType) {
            return;
        }

        $log_data = array(
            'request' => array(
                'method' => $method,
                'endpoint' => $endpoint,
                'options' => $options,
            ),
            'exception' => $exception_message,
        );
        if ($this->logWithBacktrace) {
            $log_data['backtrace'] = debug_backtrace();
        }
        $this->log($endpoint, $log_data, true);
    }

    /**
     * Function for logging the message depending upon the log type.
     *
     * @param $endpoint
     * @param $log_data
     * @param bool $exception
     */
    protected function log($endpoint, $log_data, $exception = FALSE) {

        if (($this->logRequests && !$exception) || ($this->logExceptions && $exception)) {
            if ($this->logType == self::LOG_TYPE_DISPLAY) {
                $log_display_function = (function_exists('dump') ? 'dsm' : 'print_r';
                $log_display_function($log_data);
            }

            if ($this->logType == self::LOG_TYPE_LOG_FILE) {
                $log_info = date('Y-m-d H:i:s') . ": Request to: $endpoint\n" . print_r($log_data, TRUE);
                Log::error($log_info);
            }
        }
    }
}
