<?php

namespace Gitlab\Tests\Model;

use Gitlab\Client;
use Gitlab\Model\Group;
use Gitlab\Model\Project;
use PHPUnit\Framework\TestCase;
use Gitlab\Api\Groups;
use Gitlab\Api\Projects;

class GroupTest extends TestCase
{
    private function getGroupMock(array $data = [])
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        return Group::fromArray($client, $data);
    }

    public function testProjects()
    {
        $group_data  = [
            'id' => 1,
            'name' => 'Grouped',
            'path' => '',
            'description' => 'Amazing group. Wow'
        ];
        $project_data = [
            'id' => 1,
            'name' => 'A Project'
        ];

        //Mock API methods
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $groups = $this->getMockBuilder(Groups::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $client->method('groups')->willReturn($groups);
        $groups->method('projects')->willReturn([$project_data]);

        //Create model objects
        $group = Group::fromArray($client, $group_data);
        $projects = $group->projects();
        $this->assertSame(1, count($projects));

        $project = $projects[0];
        $this->assertInstanceOf(Project::class, $project);
        $this->assertSame($project_data['id'], $project->id);
        $this->assertSame($project_data['name'], $project->name);
    }
}
