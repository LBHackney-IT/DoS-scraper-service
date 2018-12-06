<?php

namespace App\Plugins\WebPageScraper\Http\Response;

use App\Http\Response\AbstractResponse;

/**
 * An abstract web page response.
 *
 * @package App\Plugins\WebPageScraper\Http\Response
 */
abstract class AbstractWebPageResponse extends AbstractResponse
{
    /**
     * Build the items in the response.
     *
     * @param object $items
     */
    protected function buildItems($items)
    {
        $this->items = $items;
    }
}
