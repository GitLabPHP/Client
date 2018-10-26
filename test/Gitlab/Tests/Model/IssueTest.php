<?php

namespace Gitlab\Tests\Model;

use Gitlab\Api\Issues;
use Gitlab\Client;
use Gitlab\Model\Issue;
use Gitlab\Model\Project;
use PHPUnit\Framework\TestCase;

class IssueTest extends TestCase
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

    public function testMove()
    {
        $project = new Project(1);
        $toProject = new Project(2);
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();
        $issues = $this->getMockBuilder(Issues::class)
            ->disableOriginalConstructor()
            ->getMock();
        $client->expects($this->once())
            ->method('issues')
            ->willReturn($issues);
        $issues->expects($this->once())
            ->method('move')
            ->willReturn(['iid' => 11]);

        $issue = Issue::fromArray($client, $project, ['iid' => 10])->move($toProject);

        $this->assertInstanceOf(Issue::class, $issue);
        $this->assertSame($client, $issue->getClient());
        $this->assertSame($toProject, $issue->project);
        $this->assertSame(11, $issue->iid);
    }
}
