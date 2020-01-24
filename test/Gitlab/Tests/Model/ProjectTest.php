<?php

namespace Gitlab\Tests\Model;

use Gitlab\Client;
use Gitlab\Model\Group;
use Gitlab\Model\Project;
use Gitlab\Model\ProjectNamespace;
use Gitlab\Model\User;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    public function testFromArray()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $data = [
            'id' => 4,
            'description' => null,
            'default_branch' => 'master',
            'visibility' => 'private',
            'ssh_url_to_repo' => 'git@example.com:diaspora/diaspora-client.git',
            'http_url_to_repo' => 'http://example.com/diaspora/diaspora-client.git',
            'web_url' => 'http://example.com/diaspora/diaspora-client',
            'readme_url' => 'http://example.com/diaspora/diaspora-client/blob/master/README.md',
            'tag_list' => [
                'example',
                'disapora client'
            ],
            'owner' => [
                'id' => 3,
            ],
            'name' => 'Diaspora Client',
            'name_with_namespace' => 'Diaspora / Diaspora Client',
            'path' => 'diaspora-client',
            'path_with_namespace' => 'diaspora/diaspora-client',
            'issues_enabled' => true,
            'open_issues_count' => 1,
            'merge_requests_enabled' => true,
            'jobs_enabled' => true,
            'wiki_enabled' => true,
            'snippets_enabled' => false,
            'resolve_outdated_diff_discussions' => false,
            'container_registry_enabled' => false,
            'created_at' => '2013-09-30T13:46:02Z',
            'last_activity_at' => '2013-09-30T13:46:02Z',
            'creator_id' => 3,
            'namespace' => [
                'id' => 3,
            ],
            'import_status' => 'none',
            'archived' => false,
            'avatar_url' => 'http://example.com/uploads/project/avatar/4/uploads/avatar.png',
            'shared_runners_enabled' => true,
            'forks_count' => 0,
            'star_count' => 0,
            'runners_token' => 'b8547b1dc37721d05889db52fa2f02',
            'public_jobs' => true,
            'shared_with_groups' => [
                ['id' => 12]
            ],
            'only_allow_merge_if_pipeline_succeeds' => false,
            'only_allow_merge_if_all_discussions_are_resolved' => false,
            'request_access_enabled' => false,
            'merge_method' => 'merge',
            'approvals_before_merge' => 0,
        ];

        $project = Project::fromArray($client, $data);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertSame($data['id'], $project->id);
        $this->assertSame($data['description'], $project->description);
        $this->assertSame($data['default_branch'], $project->default_branch);
        $this->assertSame($data['visibility'], $project->visibility);
        $this->assertSame($data['ssh_url_to_repo'], $project->ssh_url_to_repo);
        $this->assertSame($data['http_url_to_repo'], $project->http_url_to_repo);
        $this->assertSame($data['web_url'], $project->web_url);
        $this->assertSame($data['readme_url'], $project->readme_url);
        $this->assertSame($data['tag_list'], $project->tag_list);
        $this->assertInstanceOf(User::class, $project->owner);
        $this->assertSame($data['name'], $project->name);
        $this->assertSame($data['name_with_namespace'], $project->name_with_namespace);
        $this->assertSame($data['path'], $project->path);
        $this->assertSame($data['path_with_namespace'], $project->path_with_namespace);
        $this->assertSame($data['issues_enabled'], $project->issues_enabled);
        $this->assertSame($data['open_issues_count'], $project->open_issues_count);
        $this->assertSame($data['merge_requests_enabled'], $project->merge_requests_enabled);
        $this->assertSame($data['jobs_enabled'], $project->jobs_enabled);
        $this->assertSame($data['wiki_enabled'], $project->wiki_enabled);
        $this->assertSame($data['snippets_enabled'], $project->snippets_enabled);
        $this->assertSame($data['resolve_outdated_diff_discussions'], $project->resolve_outdated_diff_discussions);
        $this->assertSame($data['container_registry_enabled'], $project->container_registry_enabled);
        $this->assertSame($data['created_at'], $project->created_at);
        $this->assertSame($data['last_activity_at'], $project->last_activity_at);
        $this->assertSame($data['creator_id'], $project->creator_id);
        $this->assertInstanceOf(ProjectNamespace::class, $project->namespace);
        $this->assertSame($data['import_status'], $project->import_status);
        $this->assertSame($data['archived'], $project->archived);
        $this->assertSame($data['avatar_url'], $project->avatar_url);
        $this->assertSame($data['shared_runners_enabled'], $project->shared_runners_enabled);
        $this->assertSame($data['forks_count'], $project->forks_count);
        $this->assertSame($data['star_count'], $project->star_count);
        $this->assertSame($data['runners_token'], $project->runners_token);
        $this->assertSame($data['public_jobs'], $project->public_jobs);
        $this->assertCount(1, $project->shared_with_groups);
        $this->assertInstanceOf(Group::class, $project->shared_with_groups[0]);
        $this->assertSame($data['only_allow_merge_if_pipeline_succeeds'], $project->only_allow_merge_if_pipeline_succeeds);
        $this->assertSame($data['only_allow_merge_if_all_discussions_are_resolved'], $project->only_allow_merge_if_all_discussions_are_resolved);
        $this->assertSame($data['request_access_enabled'], $project->request_access_enabled);
        $this->assertSame($data['merge_method'], $project->merge_method);
        $this->assertSame($data['approvals_before_merge'], $project->approvals_before_merge);
    }
}
