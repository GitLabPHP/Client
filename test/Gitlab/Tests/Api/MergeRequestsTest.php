<?php namespace Gitlab\Tests\Api;

use Gitlab\Api\MergeRequests;

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
            ->with('projects/1/merge_requests', array())
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1));
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
        $expectedArray = array('id' => 2, 'name' => 'A merge request');

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
        $expectedArray = array(
            'id' => 2,
            'name' => 'A merge request',
            'diverged_commits_count' => 0,
            'rebase_in_progress' => false
        );
        
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests/2', array('include_diverged_commits_count' => true,  'include_rebase_in_progress' => true))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2, array(
            'include_diverged_commits_count' => true,
            'include_rebase_in_progress' => true
        )));
    }

    /**
     * @test
     */
    public function shouldCreateMergeRequestWithoutOptionalParams()
    {
        $expectedArray = array('id' => 3, 'title' => 'Merge Request');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/merge_requests', array(
                'title' => 'Merge Request',
                'target_branch' => 'master',
                'source_branch' => 'develop',
                'description' => null,
                'assignee_id' => null,
                'target_project_id' => null
            ))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create(1, 'develop', 'master', 'Merge Request'));
    }

    /**
     * @test
     */
    public function shouldCreateMergeRequestWithOptionalParams()
    {
        $expectedArray = array('id' => 3, 'title' => 'Merge Request');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/merge_requests', array(
                'title' => 'Merge Request',
                'target_branch' => 'master',
                'source_branch' => 'develop',
                'assignee_id' => 6,
                'target_project_id' => 20,
                'description' => 'Some changes',
                'remove_source_branch' => true,
            ))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals(
            $expectedArray,
            $api->create(
                1,
                'develop',
                'master',
                'Merge Request',
                6,
                20,
                'Some changes',
                ['remove_source_branch' => true]
            )
        );
    }

    /**
     * @test
     */
    public function shouldUpdateMergeRequest()
    {
        $expectedArray = array('id' => 2, 'title' => 'Updated title');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/merge_requests/2', array('title' => 'Updated title', 'description' => 'No so many changes now', 'state_event' => 'close'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(1, 2, array(
            'title' => 'Updated title',
            'description' => 'No so many changes now',
            'state_event' => 'close'
        )));
    }

    /**
     * @test
     */
    public function shouldMergeMergeRequest()
    {
        $expectedArray = array('id' => 2, 'title' => 'Updated title');

        $api = $this->getApiMock();
        $api->expects($this->exactly(2))
            ->method('put')
            ->with('projects/1/merge_requests/2/merge', array('merge_commit_message' => 'Accepted'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->merge(1, 2, 'Accepted'));
        $this->assertEquals($expectedArray, $api->merge(1, 2, array('merge_commit_message' => 'Accepted')));
    }

    /**
     * @test
     */
    public function shouldGetMergeRequestNotes()
    {
        $expectedArray = array(
            array('id' => 1, 'body' => 'A comment'),
            array('id' => 2, 'body' => 'Another comment')
        );

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
    public function shouldRemoveMergeRequestNote()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/merge_requests/2/notes/1')
            ->will($this->returnValue($expectedBool));
        $this->assertEquals($expectedBool, $api->removeNote(1, 2, 1));
    }

    /**
     * @test
     */
    public function shouldGetMergeRequestChanges()
    {
        $expectedArray = array('id' => 1, 'title' => 'A merge request');

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
        $expectedArray = array(
            array('id' => 'abc', 'body' => 'A discussion'),
            array('id' => 'def', 'body' => 'Another discussion')
        );

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
        $expectedArray = array('id' => 'abc', 'body' => 'A discussion');

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
        $expectedArray = array('id' => 'abc', 'body' => 'A new discussion');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/merge_requests/2/discussions', array('body' => 'A new discussion'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addDiscussion(1, 2, array('body' => 'A new discussion')));
    }

    /**
     * @test
     */
    public function shouldResolveDiscussion()
    {
        $expectedArray = array('id' => 'abc', 'resolved' => true);

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/merge_requests/2/discussions/abc', array('resolved' => true))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->resolveDiscussion(1, 2, 'abc', true));
    }

    /**
     * @test
     */
    public function shouldUnresolveDiscussion()
    {
        $expectedArray = array('id' => 'abc', 'resolved' => false);

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/merge_requests/2/discussions/abc', array('resolved' => false))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->resolveDiscussion(1, 2, 'abc', false));
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
            ->with('projects/1/merge_requests/2/discussions/abc/notes', array('body' => 'A new discussion note'))
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
            ->with('projects/1/merge_requests/2/discussions/abc/notes/3', array('body' => 'An edited discussion note'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateDiscussionNote(1, 2, 'abc', 3, array('body' => 'An edited discussion note')));
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
        $expectedArray = array('id' => 1, 'title' => 'A merge request');

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
        $expectedArray = array('id' => 1, 'title' => 'A merge request');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests', array('iids' => [2]))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1, ['iids' => [2]]));
    }

    /**
     * @test
     */
    public function shouldApproveMergeRequest()
    {
        $expectedArray = array('id' => 1, 'title' => 'Approvals API');

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
        $expectedArray = array('id' => 1, 'title' => 'Approvals API');

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
        $expectedArray = array('id' => 1, 'title' => 'Approvals API');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/merge_requests', array('iids' => [2]))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1, ['iids' => [2]]));
    }

    /**
     * @test
     */
    public function shouldGetMergeRequestAwardEmoji()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'sparkles'),
            array('id' => 2, 'name' => 'heart_eyes'),
        );

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

    protected function getMultipleMergeRequestsData()
    {
        return array(
            array('id' => 1, 'title' => 'A merge request'),
            array('id' => 2, 'title' => 'Another merge request')
        );
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
        $expectedArray = array('rebase_in_progress' => true);

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/merge_requests/2/rebase', array('skip_ci' => true))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->rebase(1, 2, array(
            'skip_ci' => true,
        )));
    }
}
