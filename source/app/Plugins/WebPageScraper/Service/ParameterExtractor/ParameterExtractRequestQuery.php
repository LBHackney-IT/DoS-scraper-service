<?php

namespace App\Plugins\WebPageScraper\Service\ParameterExtractor;

use Illuminate\Http\Request;

class ParameterExtractRequestQuery extends AbstractParameterExtractQuery
{
    /**
     * @var Request – Lumen request object.
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $qs = $this->request->getQueryString();
        parse_str($qs, $query);
        $this->setQuery($query);
    }
}
