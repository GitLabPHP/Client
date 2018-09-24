<?php namespace Gitlab\Tests\Api;

class IssueBoardsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllBoards()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'A board'),
            array('id' => 2, 'title' => 'Another board'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('boards', array())
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }
    //
    // /**
    //  * @test
    //  */
    // public function shouldGetProjectIssuesWithPagination()
    // {
    //     $expectedArray = array(
    //         array('id' => 1, 'title' => 'An issue'),
    //         array('id' => 2, 'title' => 'Another issue'),
    //     );
    //
    //     $api = $this->getApiMock();
    //     $api->expects($this->once())
    //         ->method('get')
    //         ->with('projects/1/issues', array('page' => 2, 'per_page' => 5))
    //         ->will($this->returnValue($expectedArray))
    //     ;
    //
    //     $this->assertEquals($expectedArray, $api->all(1, 2, 5));
    // }
    //
    // /**
    //  * @test
    //  */
    // public function shouldGetProjectIssuesWithParams()
    // {
    //     $expectedArray = array(
    //         array('id' => 1, 'title' => 'An issue'),
    //         array('id' => 2, 'title' => 'Another issue'),
    //     );
    //
    //     $api = $this->getApiMock();
    //     $api->expects($this->once())
    //         ->method('get')
    //         ->with('projects/1/issues', array('page' => 2, 'per_page' => 5, 'order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'open'))
    //         ->will($this->returnValue($expectedArray))
    //     ;
    //
    //     $this->assertEquals($expectedArray, $api->all(1, 2, 5, array('order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'open')));
    // }
    //
    // /**
    //  * @test
    //  */
    // public function shouldShowIssue()
    // {
    //     $expectedArray = array('id' => 2, 'title' => 'Another issue');
    //
    //     $api = $this->getApiMock();
    //     $api->expects($this->once())
    //         ->method('get')
    //         ->with('projects/1/issues?iid=2')
    //         ->will($this->returnValue($expectedArray))
    //     ;
    //
    //     $this->assertEquals($expectedArray, $api->show(1, 2));
    // }
    //
    // /**
    //  * @test
    //  */
    // public function shouldCreateIssue()
    // {
    //     $expectedArray = array('id' => 3, 'title' => 'A new issue');
    //
    //     $api = $this->getApiMock();
    //     $api->expects($this->once())
    //         ->method('post')
    //         ->with('projects/1/issues', array('title' => 'A new issue', 'labels' => 'foo,bar'))
    //         ->will($this->returnValue($expectedArray))
    //     ;
    //
    //     $this->assertEquals($expectedArray, $api->create(1, array('title' => 'A new issue', 'labels' => 'foo,bar')));
    // }
    //
    // /**
    //  * @test
    //  */
    // public function shouldUpdateIssue()
    // {
    //     $expectedArray = array('id' => 2, 'title' => 'A renamed issue');
    //
    //     $api = $this->getApiMock();
    //     $api->expects($this->once())
    //         ->method('put')
    //         ->with('projects/1/issues/2', array('title' => 'A renamed issue', 'labels' => 'foo'))
    //         ->will($this->returnValue($expectedArray))
    //     ;
    //
    //     $this->assertEquals($expectedArray, $api->update(1, 2, array('title' => 'A renamed issue', 'labels' => 'foo')));
    // }

    /**
     * @test
     */
    public function shouldGetAllLists()
    {
        $expectedArray = array(
            array(
                'id' => 1,
                'label' => array(
                    'name' => 'First label',
                    'color' => '#F0AD4E',
                    'description' => null
                ),
                'position' => 1
            ), array(
                'id' => 2,
                'label' => array(
                    'name' => 'Second label',
                    'color' => '#F0AD4E',
                    'description' => null
                ),
                'position' => 2
            )
        );

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
        $expectedArray = array(
            array(
                'id' => 3,
                'label' => array(
                    'name' => 'Some label',
                    'color' => '#F0AD4E',
                    'description' => null
                ),
                'position' => 3
            )
        );
    
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
        $expectedArray = array(
            array(
                'id' => 3,
                'label' => array(
                    'name' => 'Some label',
                    'color' => '#F0AD4E',
                    'description' => null
                ),
                'position' => 3
            )
        );
    
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/boards/2/lists', array('label_id' => 4))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createList(1, 2, 4));
    }

    /**
     * @test
     */
    public function shouldUpdateList()
    {
        $expectedArray = array(
            array(
                'id' => 3,
                'label' => array(
                    'name' => 'Some label',
                    'color' => '#F0AD4E',
                    'description' => null
                ),
                'position' => 1
            )
        );
    
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/5/boards/2/lists/3', array('position' => 1))
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
        return 'Gitlab\Api\IssueBoards';
    }
}
