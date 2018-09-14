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
    
    /**
     * @test
     */
    public function shouldShowIssueBoard()
    {
        $expectedArray = array('id' => 2, 'name' => 'Another issue board');

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
        $expectedArray = array('id' => 3, 'name' => 'A new issue board');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/boards', array('name' => 'A new issue board'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create(1, array('name' => 'A new issue board')));
    }

    /**
     * @test
     */
    public function shouldUpdateIssueBoard()
    {
        $expectedArray = array('id' => 2, 'name' => 'A renamed issue board');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/boards/2', array('name' => 'A renamed issue board', 'labels' => 'foo'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(1, 2, array('name' => 'A renamed issue board', 'labels' => 'foo')));
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

    // /**
    //  * @test
    //  */
    // public function shouldGetIssueComments()
    // {
    //     $expectedArray = array(
    //         array('id' => 1, 'body' => 'A comment'),
    //         array('id' => 2, 'body' => 'Another comment')
    //     );
    //
    //     $api = $this->getApiMock();
    //     $api->expects($this->once())
    //         ->method('get')
    //         ->with('projects/1/issues/2/notes')
    //         ->will($this->returnValue($expectedArray))
    //     ;
    //
    //     $this->assertEquals($expectedArray, $api->showComments(1, 2));
    // }
    //
    // /**
    //  * @test
    //  */
    // public function shouldGetIssueComment()
    // {
    //     $expectedArray = array('id' => 3, 'body' => 'A new comment');
    //
    //     $api = $this->getApiMock();
    //     $api->expects($this->once())
    //         ->method('get')
    //         ->with('projects/1/issues/2/notes/3')
    //         ->will($this->returnValue($expectedArray))
    //     ;
    //
    //     $this->assertEquals($expectedArray, $api->showComment(1, 2, 3));
    // }
    //
    // /**
    //  * @test
    //  */
    // public function shouldCreateComment()
    // {
    //     $expectedArray = array('id' => 3, 'body' => 'A new comment');
    //
    //     $api = $this->getApiMock();
    //     $api->expects($this->exactly(2))
    //         ->method('post')
    //         ->with('projects/1/issues/2/notes', array('body' => 'A new comment'))
    //         ->will($this->returnValue($expectedArray))
    //     ;
    //
    //     $this->assertEquals($expectedArray, $api->addComment(1, 2, array('body' => 'A new comment')));
    //     $this->assertEquals($expectedArray, $api->addComment(1, 2, 'A new comment'));
    // }
    //
    // /**
    //  * @test
    //  */
    // public function shouldUpdateComment()
    // {
    //     $expectedArray = array('id' => 3, 'body' => 'An edited comment');
    //
    //     $api = $this->getApiMock();
    //     $api->expects($this->once())
    //         ->method('put')
    //         ->with('projects/1/issues/2/notes/3', array('body' => 'An edited comment'))
    //         ->will($this->returnValue($expectedArray))
    //     ;
    //
    //     $this->assertEquals($expectedArray, $api->updateComment(1, 2, 3, 'An edited comment'));
    // }

    protected function getApiClass()
    {
        return 'Gitlab\Api\IssueBoards';
    }
}
