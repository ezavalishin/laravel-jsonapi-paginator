<?php

namespace ezavalishin\LaravelJsonApiPaginator\Tests\Unit;

use ezavalishin\LaravelJsonApiPaginator\OffsetBasedPaginator;
use ezavalishin\LaravelJsonApiPaginator\Tests\TestCase;

class OffsetBasedPaginatorTest extends TestCase
{
    public function testHasLimitLtTotal(): void
    {
        $data = $this->mockedCollection(99);
        $limit = 10;
        $offset = 0;
        $total = $data->count();

        $paginator = new OffsetBasedPaginator($data, $limit, $offset, $total, $this->request(), ['key' => 'value']);

        $result = $paginator->toArray();

        [$data, $links, $meta] = $this->parseResult($result);

        $this->assertCount($limit, $data);

        $this->assertEquals($limit, $meta['limit']);
        $this->assertEquals($offset, $meta['offset']);
        $this->assertNull($meta['prev']);
        $this->assertEquals($meta['next'], $limit);
        $this->assertEquals($meta['total'], $total);

        $this->assertStringContainsString('page[offset]=0', urldecode($links['first']));
        $this->assertStringContainsString('page[offset]=90', urldecode($links['last']));
        $this->assertStringContainsString('page[offset]='.$limit, urldecode($links['next']));
        $this->assertNull($links['prev']);
    }

    public function testHasLimitRtTotal(): void
    {
        $data = $this->mockedCollection(20);
        $limit = 100;
        $offset = 0;
        $total = $data->count();

        $paginator = new OffsetBasedPaginator($data, $limit, $offset, $total, $this->request(), []);

        $result = $paginator->toArray();

        [$data, $links, $meta] = $this->parseResult($result);

        $this->assertCount($total, $data);

        $this->assertEquals($limit, $meta['limit']);
        $this->assertEquals($offset, $meta['offset']);
        $this->assertNull($meta['prev']);
        $this->assertNull($meta['next']);
        $this->assertEquals($meta['total'], $total);

        $this->assertStringContainsString('page[offset]=0', urldecode($links['first']));
        $this->assertStringContainsString('page[offset]=0', urldecode($links['last']));
        $this->assertNull($links['next']);
        $this->assertNull($links['prev']);
    }

    public function testHasLimitLtTotalAndHasOffset(): void
    {
        $data = $this->mockedCollection(100);
        $limit = 20;
        $offset = 20;
        $total = $data->count();

        $paginator = new OffsetBasedPaginator($data, $limit, $offset, $total, $this->request(), []);

        $result = $paginator->toArray();

        [$data, $links, $meta] = $this->parseResult($result);

        $this->assertCount($limit, $data);

        $this->assertEquals($limit, $meta['limit']);
        $this->assertEquals($offset, $meta['offset']);
        $this->assertEquals(0, $meta['prev']);
        $this->assertEquals($offset + $limit, $meta['next']);
        $this->assertEquals($meta['total'], $total);

        $this->assertStringContainsString('page[offset]=0', urldecode($links['first']));
        $this->assertStringContainsString('page[offset]=80', urldecode($links['last']));
        $this->assertStringContainsString('page[offset]=0', urldecode($links['prev']));
        $this->assertStringContainsString('page[offset]=40', urldecode($links['next']));

        $this->assertTrue($paginator->hasMorePages());
    }

    public function testHasLimitRtTotalAndHasOffset(): void
    {
        $data = $this->mockedCollection(100);
        $limit = 1000;
        $offset = 10;
        $total = $data->count();

        $paginator = new OffsetBasedPaginator($data, $limit, $offset, $total, $this->request(), []);

        $result = $paginator->toArray();

        [$data, $links, $meta] = $this->parseResult($result);

        $this->assertCount($total, $data);

        $this->assertEquals($limit, $meta['limit']);
        $this->assertEquals($offset, $meta['offset']);
        $this->assertEquals(0, $meta['prev']);
        $this->assertNull($meta['next']);
        $this->assertEquals($meta['total'], $total);

        $this->assertStringContainsString('page[offset]=0', urldecode($links['first']));
        $this->assertStringContainsString('page[offset]=0', urldecode($links['last']));
        $this->assertStringContainsString('page[offset]=0', urldecode($links['prev']));
        $this->assertNull($links['next']);

        $this->assertFalse($paginator->hasMorePages());
        $this->assertEquals('', $paginator->render());
    }

    public function testHasNotLimitAndHasOffset(): void
    {
        $data = $this->mockedCollection(100);
        $limit = 0;
        $offset = 10;
        $total = $data->count();

        $paginator = new OffsetBasedPaginator($data, $limit, $offset, $total, $this->request(), []);

        $result = $paginator->toArray();

        [$data, $links, $meta] = $this->parseResult($result);

        $this->assertCount($limit, $data);

        $this->assertEquals($limit, $meta['limit']);
        $this->assertEquals($offset, $meta['offset']);
        $this->assertEquals($offset, $meta['prev']);
        $this->assertEquals($offset, $meta['next']);
        $this->assertEquals($meta['total'], $total);

        $this->assertStringContainsString('page[offset]=0', urldecode($links['first']));
        $this->assertStringContainsString('page[offset]=0', urldecode($links['last']));
        $this->assertStringContainsString('page[offset]=10', urldecode($links['prev']));
        $this->assertStringContainsString('page[offset]=10', urldecode($links['next']));
    }
}
