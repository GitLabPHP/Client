<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Niclas Hoyer <info@niclashoyer.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Tests\Api;

use Gitlab\Api\Events;

class EventsTest extends TestCase
{
    protected function getApiClass()
    {
        return Events::class;
    }

    /**
     * @test
     */
    public function shouldGetAllEvents(): void
    {
        $expectedArray = [
            ['id' => 1, 'target_type' => 'Issue'],
            ['id' => 2, 'target_type' => null],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('events', [])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldGetEventsAfter(): void
    {
        $expectedArray = [
            ['id' => 1, 'target_type' => 'Issue'],
            ['id' => 2, 'target_type' => null],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('events', ['after' => '1970-01-01'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(['after' => new \DateTime('1970-01-01')]));
    }
}
