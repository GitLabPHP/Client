<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Tests\Api;

use Gitlab\Api\ProjectNamespaces;

class ProjectNamespacesTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllNamespaces(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'bespokes'],
            ['id' => 2, 'name' => 'internal'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('namespaces', [])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldShowNamespace(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'internal'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('namespaces/1')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1));
    }

    protected function getApiClass()
    {
        return ProjectNamespaces::class;
    }
}
