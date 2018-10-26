<?php namespace Gitlab\Tests\Model;

use Gitlab\Client;
use Gitlab\Model\Issue;
use Gitlab\Model\IssueLink;
use Gitlab\Model\Project;
use PHPUnit\Framework\TestCase;

class IssueLinkTest extends TestCase
{
    /**
     * @test
     */
    public function testCorrectConstruct()
    {
        $issue = $this->getMockBuilder(Issue::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $issueLink = new IssueLink($issue, 1, $client);

        $this->assertSame(1, $issueLink->issue_link_id);
        $this->assertSame($issue, $issueLink->issue);
        $this->assertSame($client, $issueLink->getClient());
    }

    /**
     * @test
     */
    public function testFromArray()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $project = $this->getMockBuilder(Project::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $issueLink = IssueLink::fromArray($client, $project, ['issue_link_id' => 1, 'iid' => 10]);

        $this->assertSame(1, $issueLink->issue_link_id);
        $this->assertInstanceOf(Issue::class, $issueLink->issue);
        $this->assertSame(10, $issueLink->issue->iid);
        $this->assertSame($client, $issueLink->getClient());
    }
}
