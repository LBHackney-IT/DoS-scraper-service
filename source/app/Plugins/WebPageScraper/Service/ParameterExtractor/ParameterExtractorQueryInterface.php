<?php

namespace App\Plugins\WebPageScraper\Service\ParameterExtractor;

interface ParameterExtractorQueryInterface extends ParameterExtractorInterface
{
    /**
     * Set the query array.
     *
     * @param array $query
     *
     * @return void
     */
    public function setQuery(array $query);

    /**
     * Get the query array.
     *
     * @return array
     */
    public function getQuery();
}
