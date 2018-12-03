<?php

namespace App\Http\Request;

use stdClass;

/**
 * Class AbstractRequest
 * @package App\Http\Request
 */
abstract class AbstractRequest implements RequestInterface
{
    /**
     * Request parameters.
     *
     * @var array
     */
    protected $params = array();

    /**
     * Request query string parameters.
     *
     * @var array
     */
    protected $queryParameters = array();

    /**
     * Flag: Is the request cacheable?
     *
     * @var bool
     */
    protected $cacheable = false;

    /**
     * Cache lifetime, in seconds.
     *
     * @var int
     */
    protected $cacheLifetime = 3600;

    /**
     * Flag: reset the request cache.
     *
     * @var bool
     */
    protected $resetCache = false;

    /**
     * Use session authentication for this request.
     *
     * @var bool
     */
    protected $sessionAuth = false;

    /**
     * A user account object, for session authentication.
     *
     * @var object
     */
    protected $account;

    /**
     * X-CSRF-Token value.
     *
     * @var string
     */
    protected $csrfToken;

    /**
     * Session ID.
     *
     * @var string
     */
    protected $sessionId;

    /**
     * Session name.
     *
     * @var string
     */
    protected $sessionName;

    /**
     * Request constructor.
     *
     * @param \stdClass|null $account
     *   User object.
     */
    public function __construct(stdClass $account = null)
    {
        if (isset($account) && $account->uid != 0) {
            $this->account = $account;
            // Will we have accounts to handle?

//            if ($this->account instanceof Account) {
//                $this->sessionAuth = TRUE;
//                $this->validateSessionAuth();
//                $this->csrfToken = $this->jarsLogin->getToken();
//                $this->sessionId = $this->jarsLogin->getSessId();
//                $this->sessionName = $this->jarsLogin->getSessionName();
//            }
        }
        /** @todo we're not really dealing with cacheable requests at the moment. */
        // If the request builder explicitly states the cacheability, set that now.
        // (May be set in the individual request class properties already.)
        if (isset($cacheable)) {
            $this->setIsCacheable($cacheable);
        }
    }

    /**
     * Set a request parameter.
     *
     * @param string $key
     *   Parameter key (identifier).
     * @param mixed $value
     *   Parameter value.
     *
     * @return $this
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Set multiple query parameters.
     *
     * @param array $params
     *   Array of parameters, with keys as parameter keys.
     *
     * @return $this
     */
    public function setParams(array $params)
    {
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }
        return $this;
    }

    /**
     * Set a request query parameter.
     *
     * @param string $key
     *   Parameter key (identifier).
     * @param mixed $value
     *   Parameter value.
     *
     * @return $this
     */
    public function setQueryParameter($key, $value)
    {
        $this->queryParameters[$key] = $value;
        return $this;
    }

    /**
     * Set multiple query parameters.
     *
     * @param array $params
     *   Array of query parameters, with keys as parameter keys.
     *
     * @return $this
     */
    public function setQueryParameters(array $params)
    {
        foreach ($params as $key => $value) {
            $this->setQueryParameter($key, $value);
        }
        return $this;
    }

    /**
     * Get a specified parameter.
     *
     * @param string $key
     *   Parameter key identifier.
     * @param mixed|null $default
     *   Default value for parameter if not set.
     *
     * @return mixed|null
     *   Parameter value.
     */
    public function getParam($key, $default = null)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }

        $optional = $this->getOptionalParams();
        if (in_array($key, $optional)) {
            return $default;
        }

        throw new \LogicException(t('Non-optional param @key is missing.', array('@key' => $key)));
    }

    /**
     * Get a specified query parameter.
     *
     * @param string $key
     *   Parameter key identifier.
     * @param mixed|null $default
     *   Default value for parameter if not set.
     *
     * @return mixed|null
     *   Parameter value.
     */
    public function getQueryParameter($key, $default = null)
    {
        if (isset($this->queryParameters[$key])) {
            return $this->queryParameters[$key];
        }
        return $default;
    }

    /**
     * Get all request parameters.
     *
     * @return array
     *   Array of request parameters.
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get all request query parameters.
     *
     * @return array
     *   Array of request query parameters.
     */
    public function getQueryParameters()
    {
        return $this->queryParameters;
    }

    /**
     * Validate a request.
     *
     * @return array
     */
    public function validate()
    {
        // Ignore empty values as invalid.
        $params = array_filter($this->getParams());

        $required = array_flip($this->getRequiredParams());

        // Check that all required parameters are specified.
        $supplied_required_params = array_intersect_key($required, $params);
        if (count($required) !== count($supplied_required_params)) {
            return array(
                'Missing parameters: ' .
                implode('; ', array_keys(array_diff_key($required, $supplied_required_params)))
            );
        }

        $params_not_required = array_diff_key($params, $required);
        $optional = array_flip($this->getOptionalParams());

        // Check that only recognised parameters are left.
        if ($unexpected = array_diff_key($params_not_required, $optional)) {
            return array('Unexpected parameters: ' . implode(', ', $unexpected));
        }

        return array();
    }

    /**
     * Set a request as cacheable.
     *
     * @param bool $cacheable
     *   Request is cacheable, true/false.
     */
    public function setIsCacheable($cacheable)
    {
        $this->cacheable = $cacheable;
    }

    /**
     * Is a request cacheable?
     *
     * @return bool
     *   Request is cacheable, true/false.
     */
    public function isCacheable()
    {
        if (!is_null($this->cacheable)) {
            return $this->cacheable;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setResetCache($resetCache)
    {
        $this->resetCache = $resetCache;
    }

    /**
     * {@inheritdoc}
     */
    public function resetCache()
    {
        return $this->resetCache;
    }

    /**
     * API request cache lifetime.
     *
     * @return int
     *   Lifetime of API request cache.
     */
    public function getCacheLifetime()
    {
        return $this->cacheLifetime;
    }

    /**
     * Set the request X-CSRF-Token value.
     *
     * @param string $csrfToken
     *   X-CSRF-Token value.
     */
    protected function setCsrfToken($csrfToken)
    {
        $this->csrfToken = $csrfToken;
    }

    /**
     * Get the session CSRF-Token value.
     *
     * @return string
     *   CSRF-Token string.
     */
    public function getCsrfToken()
    {
        return $this->csrfToken;
    }

    /**
     * Set the session ID.
     *
     * @param string $sessionId
     *   Session ID string.
     */
    protected function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * Get the session ID value.
     *
     * @return string
     *   Session ID string.
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set the session name.
     *
     * @param string $sessionName
     *   Session name string.
     */
    protected function setSessionName($sessionName)
    {
        $this->sessionName = $sessionName;
    }

    /**
     * Get the session name value.
     *
     * @return string
     *   Session name string.
     */
    public function getSessionName()
    {
        return $this->sessionName;
    }

    /**
     * Get array of required request parameters.
     *
     * @return array
     *   Required request parameters.
     */
    abstract public function getRequiredParams();

    /**
     * Get array of optional request parameters.
     *
     * @return array
     *   Optional request parameters.
     */
    abstract public function getOptionalParams();
}
