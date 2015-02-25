<?php namespace Gitlab\Tests\Api;

use Gitlab\Api\AbstractApi;

class IssuesTest extends ApiTestCase
{
    /**
     * @test
     */
    public function shouldGetAllIssues()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'An issue'),
            array('id' => 2, 'title' => 'Another issue'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('issues', array('page' => 1, 'per_page' => AbstractApi::PER_PAGE))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldGetProjectIssuesWithPagination()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'An issue'),
            array('id' => 2, 'title' => 'Another issue'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues', array('page' => 2, 'per_page' => 5))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1, 2, 5));
    }

    /**
     * @test
     */
    public function shouldGetProjectIssuesWithParams()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'An issue'),
            array('id' => 2, 'title' => 'Another issue'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues', array('page' => 2, 'per_page' => 5, 'order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'open'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1, 2, 5, array('order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'open')));
    }

    /**
     * @test
     */
    public function shouldShowIssue()
    {
        $expectedArray = array('id' => 2, 'title' => 'Another issue');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldCreateIssue()
    {
        $expectedArray = array('id' => 3, 'title' => 'A new issue');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues', array('title' => 'A new issue', 'labels' => 'foo,bar'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create(1, array('title' => 'A new issue', 'labels' => 'foo,bar')));
    }

    /**
     * @test
     */
    public function shouldUpdateIssue()
    {
        $expectedArray = array('id' => 2, 'title' => 'A renamed issue');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/issues/2', array('title' => 'A renamed issue', 'labels' => 'foo'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(1, 2, array('title' => 'A renamed issue', 'labels' => 'foo')));
    }

    /**
     * @test
     */
    public function shouldGetIssueComments()
    {
        $expectedArray = array(
            array('id' => 1, 'body' => 'A comment'),
            array('id' => 2, 'body' => 'Another comment')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/notes')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->showComments(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetIssueComment()
    {
        $expectedArray = array('id' => 3, 'body' => 'A new comment');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/notes/3')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->showComment(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldCreateComment()
    {
        $expectedArray = array('id' => 3, 'body' => 'A new comment');

        $api = $this->getApiMock();
        $api->expects($this->exactly(2))
            ->method('post')
            ->with('projects/1/issues/2/notes', array('body' => 'A new comment'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addComment(1, 2, array('body' => 'A new comment')));
        $this->assertEquals($expectedArray, $api->addComment(1, 2, 'A new comment'));
    }

    /**
     * @test
     */
    public function shouldUpdateComment()
    {
        $expectedArray = array('id' => 3, 'body' => 'An edited comment');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/issues/2/notes/3', array('body' => 'An edited comment'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateComment(1, 2, 3, 'An edited comment'));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Issues';
    }
}
