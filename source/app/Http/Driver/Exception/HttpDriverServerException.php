<?php

namespace App\Http\Driver\Exception;

/**
 * Thrown when something goes wrong that's outside of our control.
 *
 * Almost certainly these exceptions link back to HTTP 5xx errors.
 */
class HttpDriverServerException extends HttpDriverException
{

}
