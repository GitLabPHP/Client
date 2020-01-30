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
    public function shouldGetAllGroupIssues()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'An issue'),
            array('id' => 2, 'title' => 'Another issue'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/issues', array())
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->group(1));
    }

    /**
     * @test
     */
    public function shouldGetGroupIssuesWithPagination()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'An issue'),
            array('id' => 2, 'title' => 'Another issue'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/issues', array('page' => 2, 'per_page' => 5))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->group(1, ['page' => 2, 'per_page' => 5]));
    }

    /**
     * @test
     */
    public function shouldGetGroupIssuesWithParams()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'An issue'),
            array('id' => 2, 'title' => 'Another issue'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/issues', array('order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'opened'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->group(1, array('order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'opened')));
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
    public function shouldMoveIssue()
    {
        $expectedArray = array('id' => 2, 'title' => 'A moved issue');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/move', array('to_project_id' => 3))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->move(1, 2, 3));
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
    public function shouldGetIssueDiscussions()
    {
        $expectedArray = array(
            array('id' => 'abc', 'body' => 'A discussion'),
            array('id' => 'def', 'body' => 'Another discussion')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/discussions')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->showDiscussions(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetIssueDiscussion()
    {
        $expectedArray = array('id' => 'abc', 'body' => 'A discussion');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/discussions/abc')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->showDiscussion(1, 2, 'abc'));
    }

    /**
     * @test
     */
    public function shouldCreateDiscussion()
    {
        $expectedArray = array('id' => 'abc', 'body' => 'A new discussion');

        $api = $this->getApiMock();
        $api->expects($this->exactly(2))
            ->method('post')
            ->with('projects/1/issues/2/discussions', array('body' => 'A new discussion'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addDiscussion(1, 2, array('body' => 'A new discussion')));
        $this->assertEquals($expectedArray, $api->addDiscussion(1, 2, 'A new discussion'));
    }

    /**
     * @test
     */
    public function shouldCreateDiscussionNote()
    {
        $expectedArray = array('id' => 3, 'body' => 'A new discussion note');

        $api = $this->getApiMock();
        $api->expects($this->exactly(2))
            ->method('post')
            ->with('projects/1/issues/2/discussions/abc/notes', array('body' => 'A new discussion note'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addDiscussionNote(1, 2, 'abc', array('body' => 'A new discussion note')));
        $this->assertEquals($expectedArray, $api->addDiscussionNote(1, 2, 'abc', 'A new discussion note'));
    }

    /**
     * @test
     */
    public function shouldUpdateDiscussionNote()
    {
        $expectedArray = array('id' => 3, 'body' => 'An edited discussion note');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/issues/2/discussions/abc/notes/3', array('body' => 'An edited discussion note'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateDiscussionNote(1, 2, 'abc', 3, 'An edited discussion note'));
    }

    /**
     * @test
     */
    public function shouldRemoveDiscussionNote()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/issues/2/discussions/abc/notes/3')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeDiscussionNote(1, 2, 'abc', 3));
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

    /**
     * @test
     */
    public function shouldGetIssueAwardEmoji()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'sparkles'),
            array('id' => 2, 'name' => 'heart_eyes'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/award_emoji')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->awardEmoji(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetIssueClosedByMergeRequests()
    {
        $expectedArray = array(
            array('id' => 1, 'iid' => '1111', 'title' => 'Just saving the world'),
            array('id' => 2, 'iid' => '1112', 'title' => 'Adding new feature to get merge requests that close an issue'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/closed_by')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->closedByMergeRequests(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetProjectIssuesByAssignee()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'An issue'),
            array('id' => 2, 'title' => 'Another issue'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues', array('assignee_id' => 1))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1, array('assignee_id' => 1)));
    }

    /**
     * @test
     */
    public function shouldGetIssueParticipants()
    {
        $expectedArray = array(
            array(
                "id" => 1,
                "name" => "John Doe1",
                "username" => "user1",
                "state" => "active",
                "avatar_url" => "http://www.gravatar.com/avatar/c922747a93b40d1ea88262bf1aebee62?s=80&d=identicon",
                "web_url" => "http://localhost/user1",
            ),
            array(
                "id" => 5,
                "name" => "John Doe5",
                "username" => "user5",
                "state" => "active",
                "avatar_url" => "http://www.gravatar.com/avatar/4aea8cf834ed91844a2da4ff7ae6b491?s=80&d=identicon",
                "web_url" => "http://localhost/user5",
            )
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/participants')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->showParticipants(1, 2));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Issues';
    }
}
