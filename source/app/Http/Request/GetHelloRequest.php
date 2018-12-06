<?php

namespace App\Http\Request;

class GetHelloRequest extends AbstractRequest
{
    /**
     * {@inheritdoc}
     */
    public function getRequiredParams()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionalParams()
    {
        return [];
    }
}
