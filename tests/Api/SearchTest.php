<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Tests\Api;

use Gitlab\Api\Search;

class SearchTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAll(): void
    {
        $expectedArray = [
            ['id' => 6, 'name' => 'Project 6 bla'],
            ['id' => 7, 'name' => 'Project 7 bla'],
            ['id' => 8, 'name' => 'Project 8 bla'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('search', [
                'scope' => 'projects',
                'confidential' => 'false',
                'search' => 'bla',
                'order_by' => 'created_at',
                'sort' => 'desc'
            ])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->all([
            'scope' => 'projects',
            'confidential' => false,
            'search' => 'bla',
            'order_by' => 'created_at',
            'sort' => 'desc'
        ]));
    }

    protected function getApiClass()
    {
        return Search::class;
    }
}
