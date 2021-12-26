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

use Gitlab\Api\GroupsEpics;

class GroupsEpicsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllEpics(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A epic'],
            ['id' => 2, 'title' => 'Another epic'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/epics')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1));
    }

    /**
     * @test
     */
    public function shouldShowEpic(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A epic'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/epics/2')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldCreateEpic(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'A new epic'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups/1/epics', ['description' => 'Some text', 'title' => 'A new epic'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create(1, ['description' => 'Some text', 'title' => 'A new epic']));
    }

    /**
     * @test
     */
    public function shouldUpdateEpic(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'Updated epic'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/1/epics/3', ['title' => 'Updated epic', 'description' => 'Updated description', 'state_event' => 'close'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(1, 3, ['title' => 'Updated epic', 'description' => 'Updated description', 'state_event' => 'close']));
    }

    /**
     * @test
     */
    public function shouldRemoveEpic(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('groups/1/epics/2')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->remove(1, 2));
    }

    protected function getApiClass()
    {
        return GroupsEpics::class;
    }
}
