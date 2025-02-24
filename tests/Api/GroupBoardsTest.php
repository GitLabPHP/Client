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

use Gitlab\Api\GroupsBoards;
use PHPUnit\Framework\Attributes\Test;

class GroupBoardsTest extends TestCase
{
    #[Test]
    public function shouldGetAllBoards(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A board'],
            ['id' => 2, 'title' => 'Another board'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('boards', [])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    #[Test]
    public function shouldShowIssueBoard(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'Another issue board'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/boards/2')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    #[Test]
    public function shouldCreateIssueBoard(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A new issue board'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups/1/boards', ['name' => 'A new issue board'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->create(1, ['name' => 'A new issue board']));
    }

    #[Test]
    public function shouldUpdateIssueBoard(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'A renamed issue board'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/1/boards/2', ['name' => 'A renamed issue board', 'labels' => 'foo'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->update(1, 2, ['name' => 'A renamed issue board', 'labels' => 'foo']));
    }

    #[Test]
    public function shouldRemoveIssueBoard(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('groups/1/boards/2')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->remove(1, 2));
    }

    #[Test]
    public function shouldGetAllLists(): void
    {
        $expectedArray = [
            [
                'id' => 1,
                'label' => [
                    'name' => 'First label',
                    'color' => '#F0AD4E',
                    'description' => null,
                ],
                'position' => 1,
            ], [
                'id' => 2,
                'label' => [
                    'name' => 'Second label',
                    'color' => '#F0AD4E',
                    'description' => null,
                ],
                'position' => 2,
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/boards/2/lists')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->allLists(1, 2));
    }

    #[Test]
    public function shouldGetList(): void
    {
        $expectedArray = [
            [
                'id' => 3,
                'label' => [
                    'name' => 'Some label',
                    'color' => '#F0AD4E',
                    'description' => null,
                ],
                'position' => 3,
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/boards/2/lists/3')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showList(1, 2, 3));
    }

    #[Test]
    public function shouldCreateList(): void
    {
        $expectedArray = [
            [
                'id' => 3,
                'label' => [
                    'name' => 'Some label',
                    'color' => '#F0AD4E',
                    'description' => null,
                ],
                'position' => 3,
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups/1/boards/2/lists', ['label_id' => 4])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->createList(1, 2, 4));
    }

    #[Test]
    public function shouldUpdateList(): void
    {
        $expectedArray = [
            [
                'id' => 3,
                'label' => [
                    'name' => 'Some label',
                    'color' => '#F0AD4E',
                    'description' => null,
                ],
                'position' => 1,
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/5/boards/2/lists/3', ['position' => 1])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->updateList(5, 2, 3, 1));
    }

    #[Test]
    public function shouldDeleteList(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('groups/1/boards/2/lists/3')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->deleteList(1, 2, 3));
    }

    protected function getApiClass(): string
    {
        return GroupsBoards::class;
    }
}
