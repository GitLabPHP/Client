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
use PHPUnit\Framework\Attributes\Test;

class GroupsEpicsTest extends TestCase
{
    #[Test]
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
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1));
    }

    #[Test]
    public function shouldShowEpic(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A epic'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/epics/2')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    #[Test]
    public function shouldCreateEpic(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'A new epic'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups/1/epics', ['description' => 'Some text', 'title' => 'A new epic'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->create(1, ['description' => 'Some text', 'title' => 'A new epic']));
    }

    #[Test]
    public function shouldUpdateEpic(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'Updated epic'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/1/epics/3', ['title' => 'Updated epic', 'description' => 'Updated description', 'state_event' => 'close'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->update(1, 3, ['title' => 'Updated epic', 'description' => 'Updated description', 'state_event' => 'close']));
    }

    #[Test]
    public function shouldRemoveEpic(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('groups/1/epics/2')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->remove(1, 2));
    }

    #[Test]
    public function shouldGetEpicsIssues(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/epics/2/issues')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->issues(1, 2));
    }

    protected function getApiClass(): string
    {
        return GroupsEpics::class;
    }
}
