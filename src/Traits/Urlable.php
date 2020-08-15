<?php


namespace ezavalishin\LaravelJsonApiPaginator\Traits;


use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait Urlable
{
    protected Request $request;

    public function url($params = []): string
    {
        $query = array_merge($this->query, $params);

        return $this->request->root() . '/' . $this->path
            . (str_contains($this->path, '?') ? '&' : '?')
            . http_build_query($query, '', '&')
            . $this->buildFragment();
    }

    public function offsetUrl(int $offset) : string {
        return $this->url([
            'page[offset]' => $offset
        ]);
    }

    public function pageUrl(int $number): string
    {
        return $this->url([
            'page[number]' => $number
        ]);
    }

    protected function getRawQuery(array $except): array
    {
        $pageQuery = $this->request->query('page', []);

        return (new Collection($pageQuery))
            ->diffKeys($except)->all();
    }
}
