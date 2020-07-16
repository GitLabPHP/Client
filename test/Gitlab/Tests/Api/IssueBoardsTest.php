<?php

namespace Gitlab\Tests\Api;

use Gitlab\Api\IssueBoards;
class IssueBoardsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllBoards()
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
    public function shouldShowIssueBoard()
    {
        $expectedArray = ['id' => 2, 'name' => 'Another issue board'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/boards/2')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldCreateIssueBoard()
    {
        $expectedArray = ['id' => 3, 'name' => 'A new issue board'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/boards', ['name' => 'A new issue board'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create(1, ['name' => 'A new issue board']));
    }

    /**
     * @test
     */
    public function shouldUpdateIssueBoard()
    {
        $expectedArray = ['id' => 2, 'name' => 'A renamed issue board'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/boards/2', ['name' => 'A renamed issue board', 'labels' => 'foo'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(1, 2, ['name' => 'A renamed issue board', 'labels' => 'foo']));
    }

    /**
     * @test
     */
    public function shouldRemoveIssueBoard()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/boards/2')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->remove(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetAllLists()
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
            ->with('projects/1/boards/2/lists')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->allLists(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetList()
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
            ->with('projects/1/boards/2/lists/3')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->showList(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldCreateList()
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
            ->with('projects/1/boards/2/lists', ['label_id' => 4])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createList(1, 2, 4));
    }

    /**
     * @test
     */
    public function shouldUpdateList()
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
            ->with('projects/5/boards/2/lists/3', ['position' => 1])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateList(5, 2, 3, 1));
    }

    /**
     * @test
     */
    public function shouldDeleteList()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/boards/2/lists/3')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->deleteList(1, 2, 3));
    }

    protected function getApiClass()
    {
        return IssueBoards::class;
    }
}
