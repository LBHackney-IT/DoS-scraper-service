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
     * Get the response ID, if applicable.
     *
     * @return int
     *   Response ID.
     */
    public function getId();

}
