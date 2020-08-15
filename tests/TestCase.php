<?php

namespace ezavalishin\LaravelJsonApiPaginator\Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function request(): Request
    {
        return new Request();
    }

    protected function mockedCollection($count = 100): Collection
    {
        $data = array_fill(0, $count, random_int(0, 1000));

        return new Collection($data);
    }

    protected function parseResult(array $result): array
    {
        $data = $result['data'];

        $links = $this->paginationLinks($result);
        $meta = $this->meta($result);

        return [$data, $links, $meta];
    }

    /**
     * Get the pagination links for the response.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function paginationLinks($paginated): array
    {
        return [
            'first' => $paginated['first_page_url'] ?? null,
            'last' => $paginated['last_page_url'] ?? null,
            'prev' => $paginated['prev_page_url'] ?? null,
            'next' => $paginated['next_page_url'] ?? null,
        ];
    }

    /**
     * Gather the meta data for the response.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function meta($paginated): array
    {
        return Arr::except($paginated, [
            'data',
            'first_page_url',
            'last_page_url',
            'prev_page_url',
            'next_page_url',
        ]);
    }
}
