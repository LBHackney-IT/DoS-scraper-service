<?php

namespace App\Plugins\WebPageScraper\Service\ParameterExtractor;

abstract class AbstractParameterExtractQuery implements ParameterExtractorQueryInterface
{

    /**
     * @var array
     */
    protected $query;

    public function setQuery(array $query)
    {
        $this->query = $query;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }
}
