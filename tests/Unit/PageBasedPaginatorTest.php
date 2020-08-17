<?php

namespace ezavalishin\LaravelJsonApiPaginator\Tests\Unit;

use ezavalishin\LaravelJsonApiPaginator\PageBasedPaginator;
use ezavalishin\LaravelJsonApiPaginator\Tests\TestCase;

class PageBasedPaginatorTest extends TestCase
{
    public function testHasLimitLtTotal(): void
    {
        $data = $this->mockedCollection(99);
        $perPage = 10;
        $pageNumber = 1;
        $total = $data->count();

        $paginator = new PageBasedPaginator($data, $total, $perPage, $this->request(), $pageNumber, ['key' => 'value']);

        $result = $paginator->toArray();

        [$data, $links, $meta] = $this->parseResult($result);

        $this->assertCount($perPage, $data);

        $this->assertEquals($pageNumber, $meta['number']);
        $this->assertEquals($perPage, $meta['size']);
        $this->assertEquals($total, $meta['total']);

        $this->assertStringContainsString('page[number]=1', urldecode($links['first']));
        $this->assertStringContainsString('page[number]=10', urldecode($links['last']));
        $this->assertStringContainsString('page[number]=2', urldecode($links['next']));
        $this->assertNull($links['prev']);
    }
}
