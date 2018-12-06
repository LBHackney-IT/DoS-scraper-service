<?php

namespace App\Scraper\ICareWebPageScraper\Http\Request;

use App\Http\Request\AbstractRequest;

class GetICareServiceRequest extends AbstractRequest
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
