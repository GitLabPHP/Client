<?php namespace Gitlab\Tests\Api;

use Gitlab\Api\AbstractApi;
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
    public function shouldGetAllWithNoProject()
    {
        $expectedArray = $this->getMultipleMergeRequestsData();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('merge_requests', array())
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
            ->with('projects/1/merge_requests', ['page' => 2, 'per_page' => 5, 'order_by' => 'updated_at', 'sort' => 'desc', 'scope' => 'all'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1, ['page' => 2, 'per_page' => 5, 'order_by' => 'updated_at', 'sort' => 'desc', 'scope' => 'all']));
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
                'description' => 'Some changes',
                'assignee_id' => 6,
                'target_project_id' => 20
            ))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create(1, 'develop', 'master', 'Merge Request', 6, 20, 'Some changes'));
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
}
