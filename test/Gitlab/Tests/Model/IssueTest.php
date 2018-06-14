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
}
