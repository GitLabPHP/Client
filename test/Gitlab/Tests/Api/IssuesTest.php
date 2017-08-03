<?php namespace Gitlab\Tests\Api;

class IssuesTest extends TestCase
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
            ->with('issues', array())
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

        $this->assertEquals($expectedArray, $api->all(1, ['page' => 2, 'per_page' => 5]));
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
            ->with('projects/1/issues', array('order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'opened'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1, array('order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'opened')));
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

    /**
     * @test
     */
    public function shouldSetTimeEstimate()
    {
        $expectedArray = array('time_estimate' => 14400, 'total_time_spent' => 0, 'human_time_estimate' => '4h', 'human_total_time_spent' => null);

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/time_estimate', array('duration' => '4h'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->setTimeEstimate(1, 2, '4h'));
    }

    /**
     * @test
     */
    public function shouldResetTimeEstimate()
    {
        $expectedArray = array('time_estimate' => 0, 'total_time_spent' => 0, 'human_time_estimate' => null, 'human_total_time_spent' => null);

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/reset_time_estimate')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->resetTimeEstimate(1, 2));
    }

    /**
     * @test
     */
    public function shouldAddSpentTime()
    {
        $expectedArray = array('time_estimate' => 0, 'total_time_spent' => 14400, 'human_time_estimate' => null, 'human_total_time_spent' => '4h');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/add_spent_time', array('duration' => '4h'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addSpentTime(1, 2, '4h'));
    }

    /**
     * @test
     */
    public function shouldResetSpentTime()
    {
        $expectedArray = array('time_estimate' => 0, 'total_time_spent' => 0, 'human_time_estimate' => null, 'human_total_time_spent' => null);

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/reset_spent_time')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->resetSpentTime(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetIssueTimeStats()
    {
        $expectedArray = array('time_estimate' => 14400, 'total_time_spent' => 5400, 'human_time_estimate' => '4h', 'human_total_time_spent' => '1h 30m');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/time_stats')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->getTimeStats(1, 2));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Issues';
    }
}
