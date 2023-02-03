<?php


namespace Andileong\Framework\Core\Database\Model;

use JsonSerializable;

class Paginator implements JsonSerializable
{
    private $results;
    private $perPage;
    private $total;
    public $currentPage;
    private $pageName;
    private $request;
    private $groupBy = 3;

    public function __construct($results, $perPage, $total, $page, $pageName)
    {
        $this->results = $results;
        $this->perPage = $perPage;
        $this->total = $total;
        $this->currentPage = (int) $page;
        $this->pageName = $pageName;
        $this->request = request();
    }

    /**
     * full path of th request
     * @return string
     */
    public function path()
    {
        return $this->request()->fullUrl();
    }

    /**
     * check if it has more page
     * @return bool
     */
    public function hasMorePage()
    {
        return $this->totalPage() > $this->currentPage;
    }


    /**
     * json representation
     * @param $options
     * @return false|string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * get all items return to client
     * @return array
     */
    private function toArray()
    {
        return [
            'current_page' => $this->currentPage,
            'data' => $this->results,
            'first_page_url' => $this->firstPageUrl(),
            'from' => $this->getFromTo()[0],
            'last_page' => $this->totalPage(),
            'last_page_url' => $this->lastPageUrl(),
            'links' => $this->links(),
            'next_page_url' => $this->nextPageUrl(),
            'path' => $this->path(),
            'base_path' => $this->basePath(),
            'per_page' => $this->perPage,
            'previous_page_url' => $this->previousPageUrl(),
            'to' => $this->getFromTo()[1],
            'total' => $this->total,
            'total_page' => $this->totalPage(),
            'has_next_page' => $this->hasMorePage(),
            'has_previous_page' => $this->hasPreviousPage(),
        ];
    }

    /**
     * get the total page
     * @return float
     */
    public function totalPage()
    {
        return ceil($this->total / $this->perPage);
    }

    /**
     * check if it has previous page
     * @return bool
     */
    private function hasPreviousPage(): bool
    {
        return !$this->firstPage();
    }

    /**
     * determine if it's on the first page
     * @return bool
     */
    private function firstPage(): bool
    {
        return $this->currentPage === 1;
    }

    /**
     * construct a url based on all the query string and set the page
     * @param int $page
     * @return string
     */
    public function url(int $page)
    {
        $request = $this->request()->all();
        $request[$this->pageName] = $page;

        return $this->basePath() . '?' . http_build_query($request);
    }

    /**
     * get the last page url
     * @return string
     */
    private function lastPageUrl()
    {
        return $this->url($this->totalPage());
    }

    /**
     * get the first page url
     * @return string
     */
    private function firstPageUrl()
    {
        return $this->url(1);
    }

    /**
     * get the previous page url
     * @return string|void
     */
    private function previousPageUrl()
    {
        if ($this->hasPreviousPage()) {
            return $this->url($this->currentPage - 1);
        }
    }

    /**
     * get the next page url
     * @return string|void
     */
    private function nextPageUrl()
    {
        if ($this->hasMorePage()) {
            return $this->url($this->currentPage + 1);
        }
    }

    /**
     * get the request full url
     * @return string
     */
    private function basePath()
    {
        return $this->request()->url();
    }

    /**
     * return the request object
     * @return object|null
     */
    private function request()
    {
        return $this->request;
    }

    /**
     * generate a list links based on the total pages
     * @return array|array[]
     */
    private function links()
    {
        $generator = new GeneratePaginatorLinks($this);
        return $generator($this->groupBy);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * get the results from and to value
     * @return array
     */
    private function getFromTo()
    {
        if ($this->currentPage === 1) {
            return [1, $this->perPage];
        }

        $start = $this->countItemsFromPastPages();

        return [
            $start + 1,
            $start + $this->perPage,
        ];
    }

    /**
     * get how many page results before the current page
     * @return float|int
     */
    private function countItemsFromPastPages()
    {
        return ($this->currentPage - 1) * $this->perPage;
    }
}
