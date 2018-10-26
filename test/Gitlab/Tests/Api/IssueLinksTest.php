<?php namespace Gitlab\Tests\Api;

use Gitlab\Api\IssueLinks;

class IssueLinksTest extends TestCase
{
    /**
     * @inheritdoc
     */
    protected function getApiClass()
    {
        return IssueLinks::class;
    }

    /**
     * @test
     */
    public function shouldGetIssueLinks()
    {
        $expectedArray = array(
            array('issue_link_id' => 100),
            array('issue_link_id' => 101),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/10/links')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1, 10));
    }

    /**
     * @test
     */
    public function shouldCreateIssueLink()
    {
        $expectedArray = array(
            'source_issue' => array('iid' => 10, 'project_id' => 1),
            'target_issue' => array('iid' => 20, 'project_id' => 2),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/10/links', array('target_project_id' => 2, 'target_issue_iid' => 20))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create(1, 10, 2, 20));
    }

    /**
     * @test
     */
    public function shouldRemoveIssueLink()
    {
        $expectedArray = array(
            'source_issue' => array('iid' => 10, 'project_id' => 1),
            'target_issue' => array('iid' => 20, 'project_id' => 2),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/issues/10/links/100')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->remove(1, 10, 100));
    }
}
