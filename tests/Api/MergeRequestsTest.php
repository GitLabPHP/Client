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

use Gitlab\Api\MergeRequests;
use PHPUnit\Framework\Attributes\Test;

class MergeRequestsTest extends TestCase
{
    #[Test]
    public function shouldGetAll(): void
    {
        $expectedArray = $this->getMultipleMergeRequestsData();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests', [])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1));
    }

    #[Test]
    public function shouldGetAllWithNoProject(): void
    {
        $expectedArray = $this->getMultipleMergeRequestsData();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('merge_requests', [])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    #[Test]
    public function shouldGetAllWithParams(): void
    {
        $expectedArray = $this->getMultipleMergeRequestsData();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests', [
                'page' => 2,
                'per_page' => 5,
                'approved_by_ids' => [1],
                'approver_ids' => [2],
                'assignee_id' => 1,
                'author_id' => 1,
                'environment' => 'production',
                'in' => 'title,description',
                'labels' => 'label1,label2,label3',
                'merge_user_id' => 3,
                'milestone' => 'milestone1',
                'my_reaction_emoji' => 'thumbsup',
                'non_archived' => true,
                'order_by' => 'merged_at',
                'reviewer_id' => 4,
                'scope' => 'reviews_for_me',
                'search' => 'search term',
                'sort' => 'asc',
                'source_branch' => 'develop',
                'state' => 'all',
                'target_branch' => 'master',
                'view' => 'simple',
                'with_labels_details' => true,
                'with_merge_status_recheck' => true,
                'wip' => 'yes',
            ])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1, [
            'page' => 2,
            'per_page' => 5,
            'approved_by_ids' => [1],
            'approver_ids' => [2],
            'assignee_id' => 1,
            'author_id' => 1,
            'environment' => 'production',
            'in' => 'title,description',
            'labels' => 'label1,label2,label3',
            'merge_user_id' => 3,
            'milestone' => 'milestone1',
            'my_reaction_emoji' => 'thumbsup',
            'non_archived' => true,
            'order_by' => 'merged_at',
            'reviewer_id' => 4,
            'scope' => 'reviews_for_me',
            'search' => 'search term',
            'sort' => 'asc',
            'source_branch' => 'develop',
            'state' => 'all',
            'target_branch' => 'master',
            'view' => 'simple',
            'with_labels_details' => true,
            'with_merge_status_recheck' => true,
            'wip' => true,
        ]));
    }

    #[Test]
    public function shouldGetAllWithUsernameAndNotParams(): void
    {
        $expectedArray = $this->getMultipleMergeRequestsData();
        $parameters = [
            'approved_by_usernames' => ['alice', 'bob'],
            'assignee_username' => ['carol'],
            'author_username' => 'dan',
            'merge_user_username' => 'erin',
            'not' => [
                'labels' => 'draft',
                'milestone' => 'Backlog',
                'author_username' => 'ignored-author',
                'assignee_username' => 'ignored-assignee',
                'reviewer_username' => 'ignored-reviewer',
                'my_reaction_emoji' => 'thumbsup',
            ],
            'reviewer_username' => 'frank',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests', $parameters)
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1, $parameters));
    }

    #[Test]
    public function shouldGetAllWithAnyAndNoneParams(): void
    {
        $expectedArray = $this->getMultipleMergeRequestsData();
        $parameters = [
            'approved_by_ids' => ['Any'],
            'approver_ids' => ['None'],
            'assignee_id' => 'Any',
            'labels' => 'None',
            'milestone' => 'Any',
            'reviewer_id' => 'None',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests', $parameters)
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1, $parameters));
    }

    #[Test]
    public function shouldGetAllWithDateTimeParams(): void
    {
        $expectedArray = $this->getMultipleMergeRequestsData();

        $createdAfter = new \DateTime('2018-01-01 00:00:00');
        $createdBefore = new \DateTime('2018-01-31 12:00:00.123+03:00');
        $deployedAfter = new \DateTime('2018-01-01 00:00:00');
        $deployedBefore = new \DateTime('2018-01-31 12:00:00.123+03:00');
        $updatedAfter = new \DateTime('2018-01-01 00:00:00');
        $updatedBefore = new \DateTime('2018-01-31 12:00:00.123+03:00');

        $expectedWithArray = [
            'created_after' => '2018-01-01T00:00:00.000Z',
            'created_before' => '2018-01-31T09:00:00.123Z',
            'deployed_after' => '2018-01-01T00:00:00.000Z',
            'deployed_before' => '2018-01-31T09:00:00.123Z',
            'updated_after' => '2018-01-01T00:00:00.000Z',
            'updated_before' => '2018-01-31T09:00:00.123Z',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests', $expectedWithArray)
            ->willReturn($expectedArray)
        ;

        $this->assertEquals(
            $expectedArray,
            $api->all(1, [
                'created_after' => $createdAfter,
                'created_before' => $createdBefore,
                'deployed_after' => $deployedAfter,
                'deployed_before' => $deployedBefore,
                'updated_after' => $updatedAfter,
                'updated_before' => $updatedBefore,
            ])
        );
    }

    #[Test]
    public function shouldShowMergeRequest(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'A merge request'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    #[Test]
    public function shouldShowMergeRequestWithOptionalParameters(): void
    {
        $expectedArray = [
            'id' => 2,
            'name' => 'A merge request',
            'diverged_commits_count' => 0,
            'rebase_in_progress' => false,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2', ['include_diverged_commits_count' => true,  'include_rebase_in_progress' => true])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2, [
            'include_diverged_commits_count' => true,
            'include_rebase_in_progress' => true,
        ]));
    }

    #[Test]
    public function shouldCreateMergeRequestWithoutOptionalParams(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'Merge Request'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/merge_requests', [
                'title' => 'Merge Request',
                'target_branch' => 'master',
                'source_branch' => 'develop',
            ])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->create(1, 'develop', 'master', 'Merge Request'));
    }

    #[Test]
    public function shouldCreateMergeRequestWithOptionalParams(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'Merge Request'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/merge_requests', [
                'title' => 'Merge Request',
                'target_branch' => 'master',
                'source_branch' => 'develop',
                'assignee_id' => 6,
                'target_project_id' => 20,
                'description' => 'Some changes',
                'remove_source_branch' => true,
            ])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals(
            $expectedArray,
            $api->create(
                1,
                'develop',
                'master',
                'Merge Request',
                ['assignee_id' => 6, 'target_project_id' => 20, 'description' => 'Some changes', 'remove_source_branch' => true]
            )
        );
    }

    #[Test]
    public function shouldUpdateMergeRequest(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'Updated title'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/merge_requests/2', ['title' => 'Updated title', 'description' => 'No so many changes now', 'state_event' => 'close'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->update(1, 2, [
            'title' => 'Updated title',
            'description' => 'No so many changes now',
            'state_event' => 'close',
        ]));
    }

    #[Test]
    public function shouldRemoveMergeRequest(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/merge_requests/2')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->remove(1, 2));
    }

    #[Test]
    public function shouldMergeMergeRequest(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'Updated title'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/merge_requests/2/merge', ['merge_commit_message' => 'Accepted'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->merge(1, 2, ['merge_commit_message' => 'Accepted']));
    }

    #[Test]
    public function shouldAddMergeRequestToMergeTrain(): void
    {
        $expectedArray = ['id' => 267, 'target_branch' => 'main', 'status' => 'idle'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/merge_trains/merge_requests/2', [
                'auto_merge' => true,
                'sha' => 'abc123',
                'squash' => true,
            ])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->addToMergeTrain(1, 2, [
            'auto_merge' => true,
            'sha' => 'abc123',
            'squash' => true,
        ]));
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
            ->with('projects/1/merge_requests/2/notes')
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
            ->with('projects/1/merge_requests/2/notes/3')
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
            ->with('projects/1/merge_requests/2/notes', ['body' => 'A new note'])
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
            ->with('projects/1/merge_requests/2/notes/3', ['body' => 'An edited comment'])
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
            ->with('projects/1/merge_requests/2/notes/3')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeNote(1, 2, 3));
    }

    #[Test]
    public function shouldShowMergeRequestNoteAwardEmojis(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'sparkles'],
            ['id' => 2, 'name' => 'heart_eyes'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/notes/3/award_emoji')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showNoteAwardEmojis(1, 2, 3));
    }

    #[Test]
    public function shouldShowMergeRequestNoteAwardEmoji(): void
    {
        $expectedArray = ['id' => 4, 'name' => 'sparkles'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/notes/3/award_emoji/4')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showNoteAwardEmoji(1, 2, 3, 4));
    }

    #[Test]
    public function shouldAddMergeRequestNoteAwardEmoji(): void
    {
        $expectedArray = ['id' => 4, 'name' => 'sparkles'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/merge_requests/2/notes/3/award_emoji', ['name' => 'sparkles'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->addNoteAwardEmoji(1, 2, 3, 'sparkles'));
    }

    #[Test]
    public function shouldRemoveMergeRequestNoteAwardEmoji(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/merge_requests/2/notes/3/award_emoji/4')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeNoteAwardEmoji(1, 2, 3, 4));
    }

    #[Test]
    public function shouldGetMergeRequestParticipants(): void
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
            ->with('projects/1/merge_requests/2/participants')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showParticipants(1, 2));
    }

    #[Test]
    public function shouldGetMergeRequestResourceLabelEvents(): void
    {
        $expectedArray = [
            ['id' => 119, 'resource_type' => 'MergeRequest', 'action' => 'add'],
            ['id' => 120, 'resource_type' => 'MergeRequest', 'action' => 'add'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/resource_label_events')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showResourceLabelEvents(1, 2));
    }

    #[Test]
    public function shouldGetMergeRequestResourceLabelEvent(): void
    {
        $expectedArray = ['id' => 119, 'resource_type' => 'MergeRequest', 'action' => 'add'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/resource_label_events/3')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showResourceLabelEvent(1, 2, 3));
    }

    #[Test]
    public function shouldGetMergeRequestChanges(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'A merge request'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/changes')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->changes(1, 2));
    }

    #[Test]
    public function shouldGetMergeRequestDiscussions(): void
    {
        $expectedArray = [
            ['id' => 'abc', 'body' => 'A discussion'],
            ['id' => 'def', 'body' => 'Another discussion'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/discussions')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showDiscussions(1, 2));
    }

    #[Test]
    public function shouldGetMergeRequestDiscussion(): void
    {
        $expectedArray = ['id' => 'abc', 'body' => 'A discussion'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/discussions/abc')
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
            ->with('projects/1/merge_requests/2/discussions', ['body' => 'A new discussion'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->addDiscussion(1, 2, ['body' => 'A new discussion']));
    }

    #[Test]
    public function shouldResolveDiscussion(): void
    {
        $expectedArray = ['id' => 'abc', 'resolved' => true];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/merge_requests/2/discussions/abc', ['resolved' => true])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->resolveDiscussion(1, 2, 'abc', true));
    }

    #[Test]
    public function shouldUnresolveDiscussion(): void
    {
        $expectedArray = ['id' => 'abc', 'resolved' => false];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/merge_requests/2/discussions/abc', ['resolved' => false])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->resolveDiscussion(1, 2, 'abc', false));
    }

    #[Test]
    public function shouldCreateDiscussionNote(): void
    {
        $expectedArray = ['id' => 3, 'body' => 'A new discussion note'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/merge_requests/2/discussions/abc/notes', ['body' => 'A new discussion note'])
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
            ->with('projects/1/merge_requests/2/discussions/abc/notes/3', ['body' => 'An edited discussion note'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->updateDiscussionNote(1, 2, 'abc', 3, ['body' => 'An edited discussion note']));
    }

    #[Test]
    public function shouldRemoveDiscussionNote(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/merge_requests/2/discussions/abc/notes/3')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeDiscussionNote(1, 2, 'abc', 3));
    }

    #[Test]
    public function shouldGetIssuesClosedByMergeRequest(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'A merge request'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/closes_issues')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->closesIssues(1, 2));
    }

    #[Test]
    public function shouldGetMergeRequestByIid(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'A merge request'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests', ['iids' => [2]])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1, ['iids' => [2]]));
    }

    #[Test]
    public function shouldApproveMergeRequest(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'Approvals API'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/merge_requests/2/approve')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->approve(1, 2));
    }

    #[Test]
    public function shouldUnApproveMergeRequest(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'Approvals API'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/merge_requests/2/unapprove')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->unapprove(1, 2));
    }

    #[Test]
    public function shouldGetMergeRequestApprovals(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'Approvals API'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests', ['iids' => [2]])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1, ['iids' => [2]]));
    }

    #[Test]
    public function shouldIssueMergeRequestAwardEmoji(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'sparkles'],
            ['id' => 2, 'name' => 'heart_eyes'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/award_emoji')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->awardEmoji(1, 2));
    }

    #[Test]
    public function shouldShowMergeRequestAwardEmoji(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'sparkles'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/award_emoji/3')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showAwardEmoji(1, 2, 3));
    }

    #[Test]
    public function shouldAddMergeRequestAwardEmoji(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'sparkles'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/merge_requests/2/award_emoji', ['name' => 'sparkles'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->addAwardEmoji(1, 2, 'sparkles'));
    }

    #[Test]
    public function shouldRevokeMergeRequestAwardEmoji(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/merge_requests/2/award_emoji/3')
            ->willReturn($expectedBool);

        $this->assertEquals(true, $api->removeAwardEmoji(1, 2, 3));
    }

    #[Test]
    public function shoudGetApprovalState(): void
    {
        $expectedArray = [
            'approval_rules_overwritten' => 1,
            'rules' => [],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/approval_state')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->approvalState(1, 2));
    }

    #[Test]
    public function shoudGetLevelRules(): void
    {
        $expectedArray = [
            [
                'id' => 1,
                'name' => 'Foo',
                'rule_type' => 'regular',
                'eligible_approvers' => [],
                'approvals_required' => 1,
                'users' => [],
                'groups' => [],
                'contains_hidden_groups' => null,
                'section' => null,
                'source_rule' => null,
                'overridden' => null,
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/approval_rules')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->levelRules(1, 2));
    }

    #[Test]
    public function shoudCreateLevelRuleWithoutOptionalParameters(): void
    {
        $expectedArray = [
            'id' => 20892835,
            'name' => 'Foo',
            'rule_type' => 'regular',
            'eligible_approvers' => [],
            'approvals_required' => 3,
            'users' => [],
            'groups' => [],
            'contains_hidden_groups' => null,
            'section' => null,
            'source_rule' => null,
            'overridden' => null,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with(
                'projects/1/merge_requests/2/approval_rules',
                [
                    'name' => 'Foo',
                    'approvals_required' => 3,
                ]
            )
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->createLevelRule(1, 2, 'Foo', 3));
    }

    #[Test]
    public function shoudCreateLevelRuleWithOptionalParameters(): void
    {
        $expectedArray = [
            'id' => 20892835,
            'name' => 'Foo',
            'rule_type' => 'regular',
            'eligible_approvers' => [],
            'approvals_required' => 3,
            'users' => [1951878],
            'groups' => [104121],
            'contains_hidden_groups' => null,
            'section' => null,
            'source_rule' => null,
            'overridden' => null,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with(
                'projects/1/merge_requests/2/approval_rules',
                [
                    'name' => 'Foo',
                    'approvals_required' => 3,
                    'user_ids' => [1951878],
                    'group_ids' => [104121],
                ]
            )
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->createLevelRule(1, 2, 'Foo', 3, [
            'user_ids' => [1951878],
            'group_ids' => [104121],
        ]));
    }

    #[Test]
    public function shoudUpdateLevelRuleWithoutOptionalParameters(): void
    {
        $expectedArray = [
            'id' => 20892835,
            'name' => 'Foo',
            'rule_type' => 'regular',
            'eligible_approvers' => [],
            'approvals_required' => 3,
            'users' => [],
            'groups' => [],
            'contains_hidden_groups' => null,
            'section' => null,
            'source_rule' => null,
            'overridden' => null,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with(
                'projects/1/merge_requests/2/approval_rules/20892835',
                [
                    'name' => 'Foo',
                    'approvals_required' => 3,
                ]
            )
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->updateLevelRule(1, 2, 20892835, 'Foo', 3));
    }

    #[Test]
    public function shoudUpdateLevelRuleWithOptionalParameters(): void
    {
        $expectedArray = [
            'id' => 20892835,
            'name' => 'Foo',
            'rule_type' => 'regular',
            'eligible_approvers' => [],
            'approvals_required' => 3,
            'users' => [1951878],
            'groups' => [104121],
            'contains_hidden_groups' => null,
            'section' => null,
            'source_rule' => null,
            'overridden' => null,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with(
                'projects/1/merge_requests/2/approval_rules/20892835',
                [
                    'name' => 'Foo',
                    'approvals_required' => 3,
                    'user_ids' => [1951878],
                    'group_ids' => [104121],
                ]
            )
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->updateLevelRule(1, 2, 20892835, 'Foo', 3, [
            'user_ids' => [1951878],
            'group_ids' => [104121],
        ]));
    }

    #[Test]
    public function shoudDeleteLevelRule(): void
    {
        $expectedValue = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/merge_requests/2/approval_rules/3')
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $api->deleteLevelRule(1, 2, 3));
    }

    protected function getMultipleMergeRequestsData(): array
    {
        return [
            ['id' => 1, 'title' => 'A merge request'],
            ['id' => 2, 'title' => 'Another merge request'],
        ];
    }

    protected function getApiClass(): string
    {
        return MergeRequests::class;
    }

    #[Test]
    public function shouldRebaseMergeRequest(): void
    {
        $expectedArray = ['rebase_in_progress' => true];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/merge_requests/2/rebase', ['skip_ci' => true])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->rebase(1, 2, [
            'skip_ci' => true,
        ]));
    }
}
