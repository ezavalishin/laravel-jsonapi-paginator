<?php

namespace ezavalishin\LaravelJsonApiPaginator;

use ezavalishin\LaravelJsonApiPaginator\Traits\Urlable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PageBasedPaginator extends LengthAwarePaginator
{
    use Urlable;

    public function __construct($items, $total, $perPage, Request $request, $currentPage = null, array $options = [])
    {
        $this->request = $request;
        $this->query = $this->getRawQuery(['number']);
        $this->path = $request->path();

        $items = $items->slice(0, $perPage);

        parent::__construct($items, $total, $perPage, $currentPage, $options);
    }

    public function nextPageUrl(): ?string
    {
        return $this->hasMorePages() ? $this->pageUrl($this->currentPage() + 1) : null;
    }

    public function previousPageUrl(): ?string
    {
        return $this->currentPage() > 1 ? $this->pageUrl($this->currentPage() - 1) : null;
    }

    public function toArray(): array
    {
        return [
            'data' => $this->items->toArray(),
            'number' => $this->currentPage(),
            'size' => $this->perPage(),
            'first_page_url' => $this->pageUrl(1),
            'last_page_url' => $this->pageUrl($this->lastPage()),
            'next_page_url' => $this->nextPageUrl(),
            'prev_page_url' => $this->previousPageUrl(),
            'total' => $this->total(),
        ];
    }
}
