<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

class IssuesTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllIssues()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('issues', [])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldGetAllGroupIssues()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/issues', [])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->group(1));
    }

    /**
     * @test
     */
    public function shouldGetGroupIssuesWithPagination()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/issues', ['page' => 2, 'per_page' => 5])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->group(1, ['page' => 2, 'per_page' => 5]));
    }

    /**
     * @test
     */
    public function shouldGetGroupIssuesWithParams()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/issues', ['order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'opened'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->group(1, ['order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'opened']));
    }

    /**
     * @test
     */
    public function shouldGetProjectIssuesWithPagination()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues', ['page' => 2, 'per_page' => 5])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->all(1, ['page' => 2, 'per_page' => 5]));
    }

    /**
     * @test
     */
    public function shouldGetProjectIssuesWithParams()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues', ['order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'opened'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->all(1, ['order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'opened']));
    }

    /**
     * @test
     */
    public function shouldShowIssue()
    {
        $expectedArray = ['id' => 2, 'title' => 'Another issue'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldCreateIssue()
    {
        $expectedArray = ['id' => 3, 'title' => 'A new issue'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues', ['title' => 'A new issue', 'labels' => 'foo,bar'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->create(1, ['title' => 'A new issue', 'labels' => 'foo,bar']));
    }

    /**
     * @test
     */
    public function shouldUpdateIssue()
    {
        $expectedArray = ['id' => 2, 'title' => 'A renamed issue'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/issues/2', ['title' => 'A renamed issue', 'labels' => 'foo'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->update(1, 2, ['title' => 'A renamed issue', 'labels' => 'foo']));
    }

    /**
     * @test
     */
    public function shouldMoveIssue()
    {
        $expectedArray = ['id' => 2, 'title' => 'A moved issue'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/move', ['to_project_id' => 3])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->move(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldGetNotes()
    {
        $expectedArray = [
            ['id' => 1, 'body' => 'A note'],
            ['id' => 2, 'body' => 'Another note'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/notes')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->showNotes(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetNote()
    {
        $expectedArray = ['id' => 3, 'body' => 'A new note'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/notes/3')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->showNote(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldCreateNote()
    {
        $expectedArray = ['id' => 3, 'body' => 'A new note'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/notes', ['body' => 'A new note'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->addNote(1, 2, 'A new note'));
    }

    /**
     * @test
     */
    public function shouldUpdateNote()
    {
        $expectedArray = ['id' => 3, 'body' => 'An edited comment'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/issues/2/notes/3', ['body' => 'An edited comment'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->updateNote(1, 2, 3, 'An edited comment'));
    }

    /**
     * @test
     */
    public function shouldRemoveNote()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/issues/2/notes/3')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->removeNote(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldGetIssueDiscussions()
    {
        $expectedArray = [
            ['id' => 'abc', 'body' => 'A discussion'],
            ['id' => 'def', 'body' => 'Another discussion'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/discussions')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->showDiscussions(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetIssueDiscussion()
    {
        $expectedArray = ['id' => 'abc', 'body' => 'A discussion'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/discussions/abc')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->showDiscussion(1, 2, 'abc'));
    }

    /**
     * @test
     */
    public function shouldCreateDiscussion()
    {
        $expectedArray = ['id' => 'abc', 'body' => 'A new discussion'];

        $api = $this->getApiMock();
        $api->expects($this->exactly(2))
            ->method('post')
            ->with('projects/1/issues/2/discussions', ['body' => 'A new discussion'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->addDiscussion(1, 2, ['body' => 'A new discussion']));
        $this->assertEquals($expectedArray, $api->addDiscussion(1, 2, 'A new discussion'));
    }

    /**
     * @test
     */
    public function shouldCreateDiscussionNote()
    {
        $expectedArray = ['id' => 3, 'body' => 'A new discussion note'];

        $api = $this->getApiMock();
        $api->expects($this->exactly(2))
            ->method('post')
            ->with('projects/1/issues/2/discussions/abc/notes', ['body' => 'A new discussion note'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->addDiscussionNote(1, 2, 'abc', ['body' => 'A new discussion note']));
        $this->assertEquals($expectedArray, $api->addDiscussionNote(1, 2, 'abc', 'A new discussion note'));
    }

    /**
     * @test
     */
    public function shouldUpdateDiscussionNote()
    {
        $expectedArray = ['id' => 3, 'body' => 'An edited discussion note'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/issues/2/discussions/abc/notes/3', ['body' => 'An edited discussion note'])
            ->will($this->returnValue($expectedArray));

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
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->removeDiscussionNote(1, 2, 'abc', 3));
    }

    /**
     * @test
     */
    public function shouldSetTimeEstimate()
    {
        $expectedArray = ['time_estimate' => 14400, 'total_time_spent' => 0, 'human_time_estimate' => '4h', 'human_total_time_spent' => null];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/time_estimate', ['duration' => '4h'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->setTimeEstimate(1, 2, '4h'));
    }

    /**
     * @test
     */
    public function shouldResetTimeEstimate()
    {
        $expectedArray = ['time_estimate' => 0, 'total_time_spent' => 0, 'human_time_estimate' => null, 'human_total_time_spent' => null];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/reset_time_estimate')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->resetTimeEstimate(1, 2));
    }

    /**
     * @test
     */
    public function shouldAddSpentTime()
    {
        $expectedArray = ['time_estimate' => 0, 'total_time_spent' => 14400, 'human_time_estimate' => null, 'human_total_time_spent' => '4h'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/add_spent_time', ['duration' => '4h'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->addSpentTime(1, 2, '4h'));
    }

    /**
     * @test
     */
    public function shouldResetSpentTime()
    {
        $expectedArray = ['time_estimate' => 0, 'total_time_spent' => 0, 'human_time_estimate' => null, 'human_total_time_spent' => null];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/reset_spent_time')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->resetSpentTime(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetIssueTimeStats()
    {
        $expectedArray = ['time_estimate' => 14400, 'total_time_spent' => 5400, 'human_time_estimate' => '4h', 'human_total_time_spent' => '1h 30m'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/time_stats')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->getTimeStats(1, 2));
    }

    /**
     * @test
     */
    public function shouldIssueAwardEmoji()
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'sparkles'],
            ['id' => 2, 'name' => 'heart_eyes'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/award_emoji')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->awardEmoji(1, 2));
    }

    /**
     * @test
     */
    public function shouldRevokeAwardEmoji()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/issues/2/award_emoji/3')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals(true, $api->removeAwardEmoji(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldGetIssueClosedByMergeRequests()
    {
        $expectedArray = [
            ['id' => 1, 'iid' => '1111', 'title' => 'Just saving the world'],
            ['id' => 2, 'iid' => '1112', 'title' => 'Adding new feature to get merge requests that close an issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/closed_by')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->closedByMergeRequests(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetProjectIssuesByAssignee()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues', ['assignee_id' => 1])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->all(1, ['assignee_id' => 1]));
    }

    /**
     * @test
     */
    public function shouldGetIssueParticipants()
    {
        $expectedArray = [
            [
                'id'         => 1,
                'name'       => 'John Doe1',
                'username'   => 'user1',
                'state'      => 'active',
                'avatar_url' => 'http://www.gravatar.com/avatar/c922747a93b40d1ea88262bf1aebee62?s=80&d=identicon',
                'web_url'    => 'http://localhost/user1',
            ],
            [
                'id'         => 5,
                'name'       => 'John Doe5',
                'username'   => 'user5',
                'state'      => 'active',
                'avatar_url' => 'http://www.gravatar.com/avatar/4aea8cf834ed91844a2da4ff7ae6b491?s=80&d=identicon',
                'web_url'    => 'http://localhost/user5',
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/participants')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->showParticipants(1, 2));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Issues';
    }
}
