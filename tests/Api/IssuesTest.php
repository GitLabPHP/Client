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

use Gitlab\Api\Issues;
use PHPUnit\Framework\Attributes\Test;

class IssuesTest extends TestCase
{
    #[Test]
    public function shouldGetAllIssues(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('issues', [])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    #[Test]
    public function shouldGetAllGroupIssues(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/issues', [])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->group(1));
    }

    #[Test]
    public function shouldGetGroupIssuesWithPagination(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/issues', ['page' => 2, 'per_page' => 5])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->group(1, ['page' => 2, 'per_page' => 5]));
    }

    #[Test]
    public function shouldGetGroupIssuesWithParams(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/issues', ['order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'opened', 'iteration_title' => 'Title', 'assignee_id' => 1])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->group(1, ['order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'opened', 'iteration_title' => 'Title', 'assignee_id' => 1]));
    }

    #[Test]
    public function shouldGetProjectIssuesWithPagination(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues', ['page' => 2, 'per_page' => 5])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1, ['page' => 2, 'per_page' => 5]));
    }

    #[Test]
    public function shouldGetProjectIssuesWithParams(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues', ['order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'opened', 'iteration_id' => 1, 'assignee_id' => 2])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1, ['order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'opened', 'iteration_id' => 1, 'assignee_id' => 2]));
    }

    #[Test]
    public function shouldShowIssue(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'Another issue'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    #[Test]
    public function shouldCreateIssue(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'A new issue'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues', ['title' => 'A new issue', 'labels' => 'foo,bar'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->create(1, ['title' => 'A new issue', 'labels' => 'foo,bar']));
    }

    #[Test]
    public function shouldUpdateIssue(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'A renamed issue'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/issues/2', ['title' => 'A renamed issue', 'labels' => 'foo'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->update(1, 2, ['title' => 'A renamed issue', 'labels' => 'foo']));
    }

    #[Test]
    public function shouldReorderIssue(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'A reordered issue'];
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/issues/2/reorder', ['move_after_id' => 3, 'move_before_id' => 4])
            ->willReturn($expectedArray)
        ;
        $this->assertEquals($expectedArray, $api->reorder(1, 2, ['move_after_id' => 3, 'move_before_id' => 4]));
    }

    #[Test]
    public function shouldMoveIssue(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'A moved issue'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/move', ['to_project_id' => 3])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->move(1, 2, 3));
    }

    #[Test]
    public function shouldGetNotes(): void
    {
        $expectedArray = [
            ['id' => 1, 'body' => 'A note'],
            ['id' => 2, 'body' => 'Another note'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/notes')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showNotes(1, 2));
    }

    #[Test]
    public function shouldGetNote(): void
    {
        $expectedArray = ['id' => 3, 'body' => 'A new note'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/notes/3')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showNote(1, 2, 3));
    }

    #[Test]
    public function shouldCreateNote(): void
    {
        $expectedArray = ['id' => 3, 'body' => 'A new note'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/notes', ['body' => 'A new note'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->addNote(1, 2, 'A new note'));
    }

    #[Test]
    public function shouldUpdateNote(): void
    {
        $expectedArray = ['id' => 3, 'body' => 'An edited comment'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/issues/2/notes/3', ['body' => 'An edited comment'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->updateNote(1, 2, 3, 'An edited comment'));
    }

    #[Test]
    public function shouldRemoveNote(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/issues/2/notes/3')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeNote(1, 2, 3));
    }

    #[Test]
    public function shouldGetIssueDiscussions(): void
    {
        $expectedArray = [
            ['id' => 'abc', 'body' => 'A discussion'],
            ['id' => 'def', 'body' => 'Another discussion'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/discussions')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showDiscussions(1, 2));
    }

    #[Test]
    public function shouldGetIssueDiscussion(): void
    {
        $expectedArray = ['id' => 'abc', 'body' => 'A discussion'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/discussions/abc')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showDiscussion(1, 2, 'abc'));
    }

    #[Test]
    public function shouldCreateDiscussion(): void
    {
        $expectedArray = ['id' => 'abc', 'body' => 'A new discussion'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/discussions', ['body' => 'A new discussion'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->addDiscussion(1, 2, 'A new discussion'));
    }

    #[Test]
    public function shouldCreateDiscussionNote(): void
    {
        $expectedArray = ['id' => 3, 'body' => 'A new discussion note'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/discussions/abc/notes', ['body' => 'A new discussion note'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->addDiscussionNote(1, 2, 'abc', 'A new discussion note'));
    }

    #[Test]
    public function shouldUpdateDiscussionNote(): void
    {
        $expectedArray = ['id' => 3, 'body' => 'An edited discussion note'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/issues/2/discussions/abc/notes/3', ['body' => 'An edited discussion note'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->updateDiscussionNote(1, 2, 'abc', 3, 'An edited discussion note'));
    }

    #[Test]
    public function shouldRemoveDiscussionNote(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/issues/2/discussions/abc/notes/3')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeDiscussionNote(1, 2, 'abc', 3));
    }

    #[Test]
    public function shouldSetTimeEstimate(): void
    {
        $expectedArray = ['time_estimate' => 14400, 'total_time_spent' => 0, 'human_time_estimate' => '4h', 'human_total_time_spent' => null];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/time_estimate', ['duration' => '4h'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->setTimeEstimate(1, 2, '4h'));
    }

    #[Test]
    public function shouldResetTimeEstimate(): void
    {
        $expectedArray = ['time_estimate' => 0, 'total_time_spent' => 0, 'human_time_estimate' => null, 'human_total_time_spent' => null];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/reset_time_estimate')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->resetTimeEstimate(1, 2));
    }

    #[Test]
    public function shouldAddSpentTime(): void
    {
        $expectedArray = ['time_estimate' => 0, 'total_time_spent' => 14400, 'human_time_estimate' => null, 'human_total_time_spent' => '4h'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/add_spent_time', ['duration' => '4h'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->addSpentTime(1, 2, '4h'));
    }

    #[Test]
    public function shouldResetSpentTime(): void
    {
        $expectedArray = ['time_estimate' => 0, 'total_time_spent' => 0, 'human_time_estimate' => null, 'human_total_time_spent' => null];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/reset_spent_time')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->resetSpentTime(1, 2));
    }

    #[Test]
    public function shouldGetIssueTimeStats(): void
    {
        $expectedArray = ['time_estimate' => 14400, 'total_time_spent' => 5400, 'human_time_estimate' => '4h', 'human_total_time_spent' => '1h 30m'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/time_stats')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->getTimeStats(1, 2));
    }

    #[Test]
    public function shouldIssueAwardEmoji(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'sparkles'],
            ['id' => 2, 'name' => 'heart_eyes'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/award_emoji')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->awardEmoji(1, 2));
    }

    #[Test]
    public function shouldRevokeAwardEmoji(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/issues/2/award_emoji/3')
            ->willReturn($expectedBool);

        $this->assertEquals(true, $api->removeAwardEmoji(1, 2, 3));
    }

    #[Test]
    public function shouldGetIssueClosedByMergeRequests(): void
    {
        $expectedArray = [
            ['id' => 1, 'iid' => '1111', 'title' => 'Just saving the world'],
            ['id' => 2, 'iid' => '1112', 'title' => 'Adding new feature to get merge requests that close an issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/closed_by')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->closedByMergeRequests(1, 2));
    }

    #[Test]
    public function shouldGetIssueRelatedMergeRequests(): void
    {
        $expectedArray = [
            ['id' => 1, 'iid' => '1111', 'title' => 'Just saving the world'],
            ['id' => 2, 'iid' => '1112', 'title' => 'Adding new feature to get merge requests that close an issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/related_merge_requests')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->relatedMergeRequests(1, 2));
    }

    #[Test]
    public function shouldGetProjectIssuesByAssignee(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues', ['assignee_id' => 1])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1, ['assignee_id' => 1]));
    }

    #[Test]
    public function shouldGetIssueParticipants(): void
    {
        $expectedArray = [
            [
                'id' => 1,
                'name' => 'John Doe1',
                'username' => 'user1',
                'state' => 'active',
                'avatar_url' => 'http://www.gravatar.com/avatar/c922747a93b40d1ea88262bf1aebee62?s=80&d=identicon',
                'web_url' => 'http://localhost/user1',
            ],
            [
                'id' => 5,
                'name' => 'John Doe5',
                'username' => 'user5',
                'state' => 'active',
                'avatar_url' => 'http://www.gravatar.com/avatar/4aea8cf834ed91844a2da4ff7ae6b491?s=80&d=identicon',
                'web_url' => 'http://localhost/user5',
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/participants')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showParticipants(1, 2));
    }

    #[Test]
    public function shouldGetIssueResourceLabelEvents(): void
    {
        $expectedArray = [
            ['id' => 1, 'resource_type' => 'Issue', 'action' => 'add'],
            ['id' => 2, 'resource_type' => 'Issue', 'action' => 'add'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/resource_label_events')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showResourceLabelEvents(1, 2));
    }

    #[Test]
    public function shouldGetIssueResourceLabelEvent(): void
    {
        $expectedArray = ['id' => 1, 'resource_type' => 'Issue', 'action' => 'add'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/2/resource_label_events/3')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showResourceLabelEvent(1, 2, 3));
    }

    protected function getApiClass(): string
    {
        return Issues::class;
    }
}
