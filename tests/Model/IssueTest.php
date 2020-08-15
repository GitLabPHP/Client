<?php

declare(strict_types=1);

namespace Gitlab\Tests\Model;

use Gitlab\Api\IssueLinks;
use Gitlab\Api\Issues;
use Gitlab\Api\Projects;
use Gitlab\Client;
use Gitlab\Model\Issue;
use Gitlab\Model\IssueLink;
use Gitlab\Model\Project;
use PHPUnit\Framework\TestCase;

class IssueTest extends TestCase
{
    public function testCorrectConstructWithoutIidAndClient(): void
    {
        $project = new Project();

        $sUT = new Issue($project);

        $this->assertSame($project, $sUT->project);
        $this->assertSame(null, $sUT->iid);
        $this->assertSame(null, $sUT->getClient());
    }

    public function testCorrectConstructWithoutClient(): void
    {
        $project = new Project();

        $sUT = new Issue($project, 10);

        $this->assertSame($project, $sUT->project);
        $this->assertSame(10, $sUT->iid);
        $this->assertSame(null, $sUT->getClient());
    }

    public function testCorrectConstruct(): void
    {
        $project = new Project();
        $client = $this->createMock(Client::class);

        $sUT = new Issue($project, 10, $client);

        $this->assertSame($project, $sUT->project);
        $this->assertSame(10, $sUT->iid);
        $this->assertSame($client, $sUT->getClient());
    }

    public function testFromArray(): void
    {
        $project = new Project();
        $client = $this->createMock(Client::class);

        $sUT = Issue::fromArray($client, $project, ['iid' => 10]);

        $this->assertSame($project, $sUT->project);
        $this->assertSame(10, $sUT->iid);
        $this->assertSame($client, $sUT->getClient());
    }

    private function getIssueMock(array $data = [])
    {
        $client = $this->createMock(Client::class);

        $project = new Project(1, $client);

        return Issue::fromArray($client, $project, $data);
    }

    public function testIsClosed(): void
    {
        $opened_data = [
            'iid' => 1,
            'state' => 'opened',
        ];
        $opened_issue = $this->getIssueMock($opened_data);

        $this->assertFalse($opened_issue->isClosed());

        $closed_data = [
            'iid' => 1,
            'state' => 'closed',
        ];
        $closed_issue = $this->getIssueMock($closed_data);

        $this->assertTrue($closed_issue->isClosed());
    }

    public function testHasLabel(): void
    {
        $data = [
            'iid' => 1,
            'labels' => ['foo', 'bar'],
        ];
        $issue = $this->getIssueMock($data);

        $this->assertTrue($issue->hasLabel('foo'));
        $this->assertTrue($issue->hasLabel('bar'));
        $this->assertFalse($issue->hasLabel(''));
    }

    public function testMove(): void
    {
        $project = new Project(1);
        $toProject = new Project(2);
        $client = $this->createMock(Client::class);
        $issues = $this->createMock(Issues::class);
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

    /**
     * @test
     */
    public function testLinks(): void
    {
        $issueLinks = $this->createMock(IssueLinks::class);
        $projects = $this->createMock(Projects::class);
        $client = $this->createMock(Client::class);

        $client->method('issueLinks')->willReturn($issueLinks);
        $client->method('projects')->willReturn($projects);

        $issueLinks->expects($this->once())
            ->method('all')
            ->with(1, 10)
            ->willReturn([
                ['issue_link_id' => 100, 'iid' => 10, 'project_id' => 1],
                ['issue_link_id' => 200, 'iid' => 20, 'project_id' => 2],
            ])
        ;
        $projects->expects($this->exactly(2))
            ->method('show')
            ->withConsecutive([1], [2])
            ->will($this->onConsecutiveCalls(['id' => 1], ['id' => 2]))
        ;

        $issue = new Issue(new Project(1, $client), 10, $client);
        $issueLinks = $issue->links();

        $this->assertIsArray($issueLinks);
        $this->assertCount(2, $issueLinks);

        $this->assertInstanceOf(IssueLink::class, $issueLinks[0]);
        $this->assertSame(100, $issueLinks[0]->issue_link_id);
        $this->assertInstanceOf(Issue::class, $issueLinks[0]->issue);
        $this->assertSame(10, $issueLinks[0]->issue->iid);
        $this->assertInstanceOf(Project::class, $issueLinks[0]->issue->project);
        $this->assertSame(1, $issueLinks[0]->issue->project->id);

        $this->assertInstanceOf(IssueLink::class, $issueLinks[1]);
        $this->assertSame(200, $issueLinks[1]->issue_link_id);
        $this->assertInstanceOf(Issue::class, $issueLinks[1]->issue);
        $this->assertSame(20, $issueLinks[1]->issue->iid);
        $this->assertInstanceOf(Project::class, $issueLinks[1]->issue->project);
        $this->assertSame(2, $issueLinks[1]->issue->project->id);
    }

    /**
     * @test
     */
    public function testAddLink(): void
    {
        $issueLinks = $this->createMock(IssueLinks::class);
        $client = $this->createMock(Client::class);

        $client->method('issueLinks')->willReturn($issueLinks);

        $issueLinks->expects($this->once())
            ->method('create')
            ->with(1, 10, 2, 20)
            ->willReturn([
                'source_issue' => ['iid' => 10, 'project_id' => 1],
                'target_issue' => ['iid' => 20, 'project_id' => 2],
            ])
        ;

        $issue = new Issue(new Project(1, $client), 10, $client);
        $issueLinks = $issue->addLink(new Issue(new Project(2, $client), 20, $client));

        $this->assertIsArray($issueLinks);
        $this->assertCount(2, $issueLinks);

        $this->assertInstanceOf(Issue::class, $issueLinks['source_issue']);
        $this->assertSame(10, $issueLinks['source_issue']->iid);
        $this->assertInstanceOf(Project::class, $issueLinks['source_issue']->project);
        $this->assertSame(1, $issueLinks['source_issue']->project->id);

        $this->assertInstanceOf(Issue::class, $issueLinks['target_issue']);
        $this->assertSame(20, $issueLinks['target_issue']->iid);
        $this->assertInstanceOf(Project::class, $issueLinks['target_issue']->project);
        $this->assertSame(2, $issueLinks['target_issue']->project->id);
    }

    /**
     * @test
     */
    public function testRemoveLink(): void
    {
        $issueLinks = $this->createMock(IssueLinks::class);
        $projects = $this->createMock(Projects::class);
        $client = $this->createMock(Client::class);

        $client->method('issueLinks')->willReturn($issueLinks);
        $client->method('projects')->willReturn($projects);

        $issueLinks->expects($this->once())
            ->method('remove')
            ->with(1, 10, 100)
            ->willReturn([
                'source_issue' => ['iid' => 10, 'project_id' => 1],
                'target_issue' => ['iid' => 20, 'project_id' => 2],
            ])
        ;
        $projects->expects($this->once())
            ->method('show')
            ->with(2)
            ->willReturn(['id' => 2])
        ;

        $issue = new Issue(new Project(1, $client), 10, $client);
        $issueLinks = $issue->removeLink(100);

        $this->assertIsArray($issueLinks);
        $this->assertCount(2, $issueLinks);

        $this->assertInstanceOf(Issue::class, $issueLinks['source_issue']);
        $this->assertSame(10, $issueLinks['source_issue']->iid);
        $this->assertInstanceOf(Project::class, $issueLinks['source_issue']->project);
        $this->assertSame(1, $issueLinks['source_issue']->project->id);

        $this->assertInstanceOf(Issue::class, $issueLinks['target_issue']);
        $this->assertSame(20, $issueLinks['target_issue']->iid);
        $this->assertInstanceOf(Project::class, $issueLinks['target_issue']->project);
        $this->assertSame(2, $issueLinks['target_issue']->project->id);
    }
}
