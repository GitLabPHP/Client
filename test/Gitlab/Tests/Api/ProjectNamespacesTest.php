<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

class ProjectNamespacesTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllNamespaces()
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'bespokes'],
            ['id' => 2, 'name' => 'internal'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('namespaces', [])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldShowNamespace()
    {
        $expectedArray = ['id' => 1, 'name' => 'internal'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('namespaces/1')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->show(1));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\ProjectNamespaces';
    }
}
