<?php

namespace ezavalishin\LaravelJsonApiPaginator;

use ezavalishin\LaravelJsonApiPaginator\Traits\Urlable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;

class OffsetBasedPaginator extends AbstractPaginator implements Paginator, Arrayable
{
    use Urlable;

    protected int $offset;
    protected int $total;

    /** @noinspection MagicMethodsValidityInspection */
    public function __construct(Collection $items, int $limit, int $offset, int $total, Request $request, array $options)
    {
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }

        $this->request = $request;
        $this->perPage = $limit;
        $this->offset = $offset;
        $this->total = $total;

        $this->query = $this->getRawQuery(['offset']);
        $this->path = $request->path();

        $this->items = $items->slice(0, $this->perPage);
    }

    public function nextPageUrl(): ?string
    {
        return $this->nextOffset() ? $this->offsetUrl($this->nextOffset()) : null;
    }

    public function previousPageUrl(): ?string
    {
        return $this->prevOffset() !== null ? $this->offsetUrl($this->prevOffset()) : null;
    }

    public function firstPageUrl(): ?string
    {
        return $this->offsetUrl(0);
    }

    public function lastPageUrl(): ?string
    {
        if ($this->perPage === 0) {
            $lastOffset = 0;
        } else {
            $mod = $this->total % $this->perPage;

            if ($mod === 0) {
                $lastOffset = floor(($this->total - 1) / $this->perPage) * $this->perPage;
            } else {
                $lastOffset = floor(($this->total) / $this->perPage) * $this->perPage;
            }
        }

        return $this->offsetUrl($lastOffset);
    }

    public function render($view = null, $data = []): string
    {
        return '';
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function prevOffset(): ?int
    {
        return $this->offset > 0 ? max($this->offset - $this->perPage, 0) : null;
    }

    public function nextOffset(): ?int
    {
        return $this->offset + $this->perPage > $this->total ? null : $this->offset + $this->perPage;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function toArray(): array
    {
        return [
            'data' => $this->items->toArray(),
            'offset' => $this->getOffset(),
            'limit' => $this->perPage(),
            'prev' => $this->prevOffset(),
            'next' => $this->nextOffset(),
            'total' => $this->getTotal(),
            'next_page_url' => $this->nextPageUrl(),
            'prev_page_url' => $this->previousPageUrl(),
            'first_page_url' => $this->firstPageUrl(),
            'last_page_url' => $this->lastPageUrl(),
        ];
    }

    public function hasMorePages(): bool
    {
        return $this->nextOffset() !== null;
    }
}
