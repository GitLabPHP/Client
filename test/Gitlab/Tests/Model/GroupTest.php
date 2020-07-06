<?php

declare(strict_types=1);

namespace Gitlab\Tests\Model;

use Gitlab\Api\Groups;
use Gitlab\Client;
use Gitlab\Model\Group;
use Gitlab\Model\Project;
use PHPUnit\Framework\TestCase;

class GroupTest extends TestCase
{
    public function testFromArray()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $data = [
            'id' => 1,
            'name' => 'Foobar Group',
            'path' => 'foo-bar',
            'description' => 'An interesting group',
            'visibility' => 'public',
            'lfs_enabled' => true,
            'avatar_url' => 'http://localhost:3000/uploads/group/avatar/1/foo.jpg',
            'web_url' => 'http://localhost:3000/groups/foo-bar',
            'request_access_enabled' => false,
            'full_name' => 'Foobar Group',
            'full_path' => 'foo-bar',
            'file_template_project_id' => 1,
            'parent_id' => null,
            'projects' => [
                ['id' => 1],
            ],
            'shared_projects' => [
                ['id' => 2],
            ],
        ];

        $group = Group::fromArray($client, $data);

        $this->assertInstanceOf(Group::class, $group);
        $this->assertSame($data['id'], $group->id);
        $this->assertSame($data['name'], $group->name);
        $this->assertSame($data['path'], $group->path);
        $this->assertSame($data['description'], $group->description);
        $this->assertSame($data['visibility'], $group->visibility);
        $this->assertSame($data['lfs_enabled'], $group->lfs_enabled);
        $this->assertSame($data['avatar_url'], $group->avatar_url);
        $this->assertSame($data['web_url'], $group->web_url);
        $this->assertSame($data['request_access_enabled'], $group->request_access_enabled);
        $this->assertSame($data['full_name'], $group->full_name);
        $this->assertSame($data['full_path'], $group->full_path);
        $this->assertSame($data['file_template_project_id'], $group->file_template_project_id);
        $this->assertSame($data['parent_id'], $group->parent_id);

        $this->assertCount(1, $group->projects);
        $this->assertInstanceOf(Project::class, $group->projects[0]);

        $this->assertCount(1, $group->shared_projects);
        $this->assertInstanceOf(Project::class, $group->shared_projects[0]);
    }

    public function testProjects()
    {
        $group_data = [
            'id' => 1,
            'name' => 'Grouped',
            'path' => '',
            'description' => 'Amazing group. Wow',
        ];
        $project_data = [
            'id' => 1,
            'name' => 'A Project',
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
