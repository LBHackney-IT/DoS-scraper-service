<?php

namespace App\Plugins\WebPageScraper\Service\ParameterExtractor;

class ParameterExtractJobQuery extends AbstractParameterExtractQuery
{

    public function __construct($parameters)
    {
        $query = $parameters['query'];
        $this->setQuery($query);
    }
}
