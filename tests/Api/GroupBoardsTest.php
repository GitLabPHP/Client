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

use Gitlab\Api\GroupsBoards;

class GroupBoardsTest extends TestCase
{
    /**
     * @test
     */
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
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldShowIssueBoard(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'Another issue board'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/boards/2')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldCreateIssueBoard(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A new issue board'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups/1/boards', ['name' => 'A new issue board'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create(1, ['name' => 'A new issue board']));
    }

    /**
     * @test
     */
    public function shouldUpdateIssueBoard(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'A renamed issue board'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/1/boards/2', ['name' => 'A renamed issue board', 'labels' => 'foo'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(1, 2, ['name' => 'A renamed issue board', 'labels' => 'foo']));
    }

    /**
     * @test
     */
    public function shouldRemoveIssueBoard(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('groups/1/boards/2')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->remove(1, 2));
    }

    /**
     * @test
     */
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
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->allLists(1, 2));
    }

    /**
     * @test
     */
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
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->showList(1, 2, 3));
    }

    /**
     * @test
     */
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
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createList(1, 2, 4));
    }

    /**
     * @test
     */
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
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateList(5, 2, 3, 1));
    }

    /**
     * @test
     */
    public function shouldDeleteList(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('groups/1/boards/2/lists/3')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->deleteList(1, 2, 3));
    }

    protected function getApiClass()
    {
        return GroupsBoards::class;
    }
}
