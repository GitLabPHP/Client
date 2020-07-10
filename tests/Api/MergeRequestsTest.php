<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

class MergeRequestsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAll()
    {
        $expectedArray = $this->getMultipleMergeRequestsData();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests', [])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1));
    }

    /**
     * @test
     */
    public function shouldGetAllWithNoProject()
    {
        $expectedArray = $this->getMultipleMergeRequestsData();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('merge_requests', [])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldGetAllWithParams()
    {
        $expectedArray = $this->getMultipleMergeRequestsData();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests', [
                'page' => 2,
                'per_page' => 5,
                'labels' => 'label1,label2,label3',
                'milestone' => 'milestone1',
                'order_by' => 'updated_at',
                'state' => 'all',
                'sort' => 'desc',
                'scope' => 'all',
                'author_id' => 1,
                'assignee_id' => 1,
                'source_branch' => 'develop',
                'target_branch' => 'master',
            ])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1, [
            'page' => 2,
            'per_page' => 5,
            'labels' => 'label1,label2,label3',
            'milestone' => 'milestone1',
            'order_by' => 'updated_at',
            'state' => 'all',
            'sort' => 'desc',
            'scope' => 'all',
            'author_id' => 1,
            'assignee_id' => 1,
            'source_branch' => 'develop',
            'target_branch' => 'master',
        ]));
    }

    /**
     * @test
     */
    public function shouldGetAllWithDateTimeParams()
    {
        $expectedArray = $this->getMultipleMergeRequestsData();

        $createdAfter = new \DateTime('2018-01-01 00:00:00');
        $createdBefore = new \DateTime('2018-01-31 00:00:00');

        $expectedWithArray = [
            'created_after' => $createdAfter->format(DATE_ATOM),
            'created_before' => $createdBefore->format(DATE_ATOM),
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests', $expectedWithArray)
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals(
            $expectedArray,
            $api->all(1, ['created_after' => $createdAfter, 'created_before' => $createdBefore])
        );
    }

    /**
     * @test
     */
    public function shouldShowMergeRequest()
    {
        $expectedArray = ['id' => 2, 'name' => 'A merge request'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldShowMergeRequestWithOptionalParameters()
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
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2, [
            'include_diverged_commits_count' => true,
            'include_rebase_in_progress' => true,
        ]));
    }

    /**
     * @test
     */
    public function shouldCreateMergeRequestWithoutOptionalParams()
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
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create(1, 'develop', 'master', 'Merge Request'));
    }

    /**
     * @test
     */
    public function shouldCreateMergeRequestWithOptionalParams()
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
            ->will($this->returnValue($expectedArray))
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

    /**
     * @test
     */
    public function shouldUpdateMergeRequest()
    {
        $expectedArray = ['id' => 2, 'title' => 'Updated title'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/merge_requests/2', ['title' => 'Updated title', 'description' => 'No so many changes now', 'state_event' => 'close'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(1, 2, [
            'title' => 'Updated title',
            'description' => 'No so many changes now',
            'state_event' => 'close',
        ]));
    }

    /**
     * @test
     */
    public function shouldMergeMergeRequest()
    {
        $expectedArray = ['id' => 2, 'title' => 'Updated title'];

        $api = $this->getApiMock();
        $api->expects($this->exactly(2))
            ->method('put')
            ->with('projects/1/merge_requests/2/merge', ['merge_commit_message' => 'Accepted'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->merge(1, 2, 'Accepted'));
        $this->assertEquals($expectedArray, $api->merge(1, 2, ['merge_commit_message' => 'Accepted']));
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
            ->with('projects/1/merge_requests/2/notes')
            ->will($this->returnValue($expectedArray))
        ;

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
            ->with('projects/1/merge_requests/2/notes/3')
            ->will($this->returnValue($expectedArray))
        ;

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
            ->with('projects/1/merge_requests/2/notes', ['body' => 'A new note'])
            ->will($this->returnValue($expectedArray))
        ;

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
            ->with('projects/1/merge_requests/2/notes/3', ['body' => 'An edited comment'])
            ->will($this->returnValue($expectedArray))
        ;

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
            ->with('projects/1/merge_requests/2/notes/3')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeNote(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldGetMergeRequestChanges()
    {
        $expectedArray = ['id' => 1, 'title' => 'A merge request'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/changes')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->changes(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetMergeRequestDiscussions()
    {
        $expectedArray = [
            ['id' => 'abc', 'body' => 'A discussion'],
            ['id' => 'def', 'body' => 'Another discussion'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/discussions')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->showDiscussions(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetMergeRequestDiscussion()
    {
        $expectedArray = ['id' => 'abc', 'body' => 'A discussion'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/discussions/abc')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->showDiscussion(1, 2, 'abc'));
    }

    /**
     * @test
     */
    public function shouldCreateDiscussion()
    {
        $expectedArray = ['id' => 'abc', 'body' => 'A new discussion'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/merge_requests/2/discussions', ['body' => 'A new discussion'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addDiscussion(1, 2, ['body' => 'A new discussion']));
    }

    /**
     * @test
     */
    public function shouldResolveDiscussion()
    {
        $expectedArray = ['id' => 'abc', 'resolved' => true];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/merge_requests/2/discussions/abc', ['resolved' => true])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->resolveDiscussion(1, 2, 'abc', true));
    }

    /**
     * @test
     */
    public function shouldUnresolveDiscussion()
    {
        $expectedArray = ['id' => 'abc', 'resolved' => false];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/merge_requests/2/discussions/abc', ['resolved' => false])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->resolveDiscussion(1, 2, 'abc', false));
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
            ->with('projects/1/merge_requests/2/discussions/abc/notes', ['body' => 'A new discussion note'])
            ->will($this->returnValue($expectedArray))
        ;

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
            ->with('projects/1/merge_requests/2/discussions/abc/notes/3', ['body' => 'An edited discussion note'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateDiscussionNote(1, 2, 'abc', 3, ['body' => 'An edited discussion note']));
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
            ->with('projects/1/merge_requests/2/discussions/abc/notes/3')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeDiscussionNote(1, 2, 'abc', 3));
    }

    /**
     * @test
     */
    public function shouldGetIssuesClosedByMergeRequest()
    {
        $expectedArray = ['id' => 1, 'title' => 'A merge request'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/closes_issues')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->closesIssues(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetMergeRequestByIid()
    {
        $expectedArray = ['id' => 1, 'title' => 'A merge request'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests', ['iids' => [2]])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1, ['iids' => [2]]));
    }

    /**
     * @test
     */
    public function shouldApproveMergeRequest()
    {
        $expectedArray = ['id' => 1, 'title' => 'Approvals API'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/merge_requests/2/approve')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->approve(1, 2));
    }

    /**
     * @test
     */
    public function shouldUnApproveMergeRequest()
    {
        $expectedArray = ['id' => 1, 'title' => 'Approvals API'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/merge_requests/2/unapprove')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->unapprove(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetMergeRequestApprovals()
    {
        $expectedArray = ['id' => 1, 'title' => 'Approvals API'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests', ['iids' => [2]])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1, ['iids' => [2]]));
    }

    /**
     * @test
     */
    public function shouldIssueMergeRequestAwardEmoji()
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'sparkles'],
            ['id' => 2, 'name' => 'heart_eyes'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/award_emoji')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->awardEmoji(1, 2));
    }

    /**
     * @test
     */
    public function shouldRevokeMergeRequestAwardEmoji()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/merge_requests/2/award_emoji/3')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals(true, $api->removeAwardEmoji(1, 2, 3));
    }

    /**
     * @test
     */
    public function shoudGetApprovalState()
    {
        $expectedArray = [
            'approval_rules_overwritten' => 1,
            'rules' => [],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2/approval_state')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->approvalState(1, 2));
    }

    /**
     * @test
     */
    public function shoudGetLevelRules()
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
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->levelRules(1, 2));
    }

    /**
     * @test
     */
    public function shoudCreateLevelRuleWithoutOptionalParameters()
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
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->createLevelRule(1, 2, 'Foo', 3));
    }

    /**
     * @test
     */
    public function shoudCreateLevelRuleWithOptionalParameters()
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
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->createLevelRule(1, 2, 'Foo', 3, [
            'user_ids' => [1951878],
            'group_ids' => [104121],
        ]));
    }

    /**
     * @test
     */
    public function shoudUpdateLevelRuleWithoutOptionalParameters()
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
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->updateLevelRule(1, 2, 20892835, 'Foo', 3));
    }

    /**
     * @test
     */
    public function shoudUpdateLevelRuleWithOptionalParameters()
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
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->updateLevelRule(1, 2, 20892835, 'Foo', 3, [
            'user_ids' => [1951878],
            'group_ids' => [104121],
        ]));
    }

    /**
     * @test
     */
    public function shoudDeleteLevelRule()
    {
        $expectedValue = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/merge_requests/2/approval_rules/3')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $api->deleteLevelRule(1, 2, 3));
    }

    protected function getMultipleMergeRequestsData()
    {
        return [
            ['id' => 1, 'title' => 'A merge request'],
            ['id' => 2, 'title' => 'Another merge request'],
        ];
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\MergeRequests';
    }

    /**
     * @test
     */
    public function shouldRebaseMergeRequest()
    {
        $expectedArray = ['rebase_in_progress' => true];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/merge_requests/2/rebase', ['skip_ci' => true])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->rebase(1, 2, [
            'skip_ci' => true,
        ]));
    }
}
