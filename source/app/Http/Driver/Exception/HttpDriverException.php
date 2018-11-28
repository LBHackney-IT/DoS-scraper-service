<?php
/**
 * @file
 */

namespace Http\Driver\Exception;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * A base HttpDriverException.
 */
abstract class HttpDriverException extends Exception
{

    /**
     * ApiException constructor.
     *
     * @param string $message
     *   Exception message.
     * @param int|null $code
     *   Exception code.
     * @param bool $increment_fault_counter
     *   Fault counter.
     * @param \Exception|null $previous
     *   Exception.
     */
    public function __construct($message = '', $code = 0, $increment_fault_counter = false, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        // To avoid double logging exceptions generated when the API is unavailable
        // only log when the fault counter hasn't yet been incremented.
        // Do this after the initial construction so that watchdog_exception can
        // read all applicable parameters.
        if ($increment_fault_counter && config('httpdriver.log_exceptions')) {
            Log::error($this->getMessage());
        }
    }
}
