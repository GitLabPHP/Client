<?php

namespace Gitlab\Tests\Model;

use Gitlab\Client;
use Gitlab\Model\Issue;
use Gitlab\Model\Project;

class IssueTest extends \PHPUnit_Framework_TestCase
{
    public function testCorrectConstructWithoutIidAndClient()
    {
        $project = new Project();

        $sUT = new Issue($project);

        $this->assertSame($project, $sUT->project);
        $this->assertSame(null, $sUT->iid);
        $this->assertSame(null, $sUT->getClient());
    }

    public function testCorrectConstructWithoutClient()
    {
        $project = new Project();

        $sUT = new Issue($project, 10);

        $this->assertSame($project, $sUT->project);
        $this->assertSame(10, $sUT->iid);
        $this->assertSame(null, $sUT->getClient());
    }

    public function testCorrectConstruct()
    {
        $project = new Project();
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $sUT = new Issue($project, 10, $client);

        $this->assertSame($project, $sUT->project);
        $this->assertSame(10, $sUT->iid);
        $this->assertSame($client, $sUT->getClient());
    }

    public function testFromArray()
    {
        $project = new Project();
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $sUT = Issue::fromArray($client, $project, ['iid' => 10]);

        $this->assertSame($project, $sUT->project);
        $this->assertSame(10, $sUT->iid);
        $this->assertSame($client, $sUT->getClient());
    }

    private function getIssueMock(array $data = [])
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $project = new Project(1, $client);

        return Issue::fromArray($client, $project, $data);
    }

    public function testIsClosed()
    {
        $opened_data  = [
            'iid'   => 1,
            'state' => 'opened',
        ];
        $opened_issue = $this->getIssueMock($opened_data);

        $this->assertFalse($opened_issue->isClosed());

        $closed_data  = [
            'iid'   => 1,
            'state' => 'closed',
        ];
        $closed_issue = $this->getIssueMock($closed_data);

        $this->assertTrue($closed_issue->isClosed());
    }

    public function testHasLabel()
    {
        $data  = [
            'iid'    => 1,
            'labels' => ['foo', 'bar'],
        ];
        $issue = $this->getIssueMock($data);

        $this->assertTrue($issue->hasLabel('foo'));
        $this->assertTrue($issue->hasLabel('bar'));
        $this->assertFalse($issue->hasLabel(''));
    }
}
