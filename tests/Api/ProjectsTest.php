<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Projects;

class ProjectsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray);

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldGetAllProjectsSortedByName(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock(
            'projects',
            $expectedArray,
            ['page' => 1, 'per_page' => 5, 'order_by' => 'name', 'sort' => 'asc']
        );

        $this->assertEquals(
            $expectedArray,
            $api->all(['page' => 1, 'per_page' => 5, 'order_by' => 'name', 'sort' => 'asc'])
        );
    }

    /**
     * @test
     */
    public function shouldNotNeedPaginationWhenGettingProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldGetAccessibleProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray);

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldGetOwnedProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['owned' => 'true']);

        $this->assertEquals($expectedArray, $api->all(['owned' => true]));
    }

    /**
     * @test
     */
    public function shouldGetNotArchivedProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['archived' => 'false']);

        $this->assertEquals($expectedArray, $api->all(['archived' => false]));
    }

    /**
     * @test
     * @dataProvider possibleAccessLevels
     */
    public function shouldGetProjectsWithMinimumAccessLevel($level): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['min_access_level' => $level]);

        $this->assertEquals($expectedArray, $api->all(['min_access_level' => $level]));
    }

    /**
     * @test
     */
    public function shouldSearchProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['search' => 'a project']);
        $this->assertEquals($expectedArray, $api->all(['search' => 'a project']));
    }

    /**
     * @test
     */
    public function shouldShowProject(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Project Name'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->show(1));
    }

    /**
     * @test
     */
    public function shouldShowProjectWithStatistics(): void
    {
        $expectedArray = [
            'id' => 1,
            'name' => 'Project Name',
            'statistics' => [
                'commit_count' => 37,
                'storage_size' => 1038090,
                'repository_size' => 1038090,
                'lfs_objects_size' => 0,
                'job_artifacts_size' => 0,
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1', ['statistics' => true])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->show(1, ['statistics' => true]));
    }

    /**
     * @test
     */
    public function shouldCreateProject(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Project Name'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects', ['name' => 'Project Name', 'issues_enabled' => true])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->create('Project Name', [
            'issues_enabled' => true,
        ]));
    }

    /**
     * @test
     */
    public function shouldUpdateProject(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Updated Name'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1', ['name' => 'Updated Name', 'issues_enabled' => true])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->update(1, [
            'name' => 'Updated Name',
            'issues_enabled' => true,
        ]));
    }

    /**
     * @test
     */
    public function shouldArchiveProject(): void
    {
        $expectedArray = ['id' => 1, 'archived' => true];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/archive')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->archive(1));
    }

    /**
     * @test
     */
    public function shouldUnarchiveProject(): void
    {
        $expectedArray = ['id' => 1, 'archived' => false];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/unarchive')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->unarchive(1));
    }

    /**
     * @test
     */
    public function shouldCreateProjectForUser(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Project Name'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/user/1', ['name' => 'Project Name', 'issues_enabled' => true])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->createForUser(1, 'Project Name', [
            'issues_enabled' => true,
        ]));
    }

    /**
     * @test
     */
    public function shouldRemoveProject(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->remove(1));
    }

    /**
     * @test
     */
    public function shouldGetPipelines(): void
    {
        $expectedArray = [
            ['id' => 1, 'status' => 'success', 'ref' => 'new-pipeline'],
            ['id' => 2, 'status' => 'failed', 'ref' => 'new-pipeline'],
            ['id' => 3, 'status' => 'pending', 'ref' => 'test-pipeline'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->pipelines(1));
    }

    /**
     * @test
     */
    public function shouldGetTriggers(): void
    {
        $expectedArray = [
            ['id' => 1, 'description' => 'foo', 'token' => '6d056f63e50fe6f8c5f8f4aa10edb7'],
            ['id' => 2, 'description' => 'bar', 'token' => '7bde01aa4f8f5c8f6ef05e36f650d6'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/triggers')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->triggers(1));
    }

    /**
     * @test
     */
    public function shouldGetTrigger(): void
    {
        $expectedArray = [
            'id' => 3,
            'description' => 'foo',
            'token' => '6d056f63e50fe6f8c5f8f4aa10edb7',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/triggers/3')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->trigger(1, 3));
    }

    /**
     * Check we can request project issues.
     *
     * @test
     */
    public function shouldGetProjectIssues(): void
    {
        $expectedArray = $this->getProjectIssuesExpectedArray();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->issues(1));
    }

    /**
     * Check we can request project issues.
     *
     * @test
     */
    public function shouldGetProjectUsers(): void
    {
        $expectedArray = $this->getProjectUsersExpectedArray();

        $api = $this->getApiMock();
        $api->expects($this->once())
        ->method('get')
        ->with('projects/1/users')
        ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->users(1));
    }

    /**
     * Check we can request project issues with query parameters.
     *
     * @test
     */
    public function shouldGetProjectIssuesParameters(): void
    {
        $expectedArray = $this->getProjectIssuesExpectedArray();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->issues(1, ['state' => 'opened']));
    }

    /**
     * Get expected array for tests which check project issues method.
     *
     * @return array
     *               Project issues list
     */
    public function getProjectIssuesExpectedArray()
    {
        return [
            [
                'state' => 'opened',
                'description' => 'Ratione dolores corrupti mollitia soluta quia.',
                'author' => [
                    'state' => 'active',
                    'id' => 18,
                    'web_url' => 'https://gitlab.example.com/eileen.lowe',
                    'name' => 'Alexandra Bashirian',
                    'avatar_url' => null,
                    'username' => 'eileen.lowe',
                ],
                'milestone' => [
                    'project_id' => 1,
                    'description' => 'Ducimus nam enim ex consequatur cumque ratione.',
                    'state' => 'closed',
                    'due_date' => null,
                    'iid' => 2,
                    'created_at' => '2016-01-04T15:31:39.996Z',
                    'title' => 'v4.0',
                    'id' => 17,
                    'updated_at' => '2016-01-04T15:31:39.996Z',
                ],
                'project_id' => 1,
                'assignees' => [
                    [
                        'state' => 'active',
                        'id' => 1,
                        'name' => 'Administrator',
                        'web_url' => 'https://gitlab.example.com/root',
                        'avatar_url' => null,
                        'username' => 'root',
                    ],
                ],
                'assignee' => [
                    'state' => 'active',
                    'id' => 1,
                    'name' => 'Administrator',
                    'web_url' => 'https://gitlab.example.com/root',
                    'avatar_url' => null,
                    'username' => 'root',
                ],
                'updated_at' => '2016-01-04T15:31:51.081Z',
                'closed_at' => null,
                'closed_by' => null,
                'id' => 76,
                'title' => 'Consequatur vero maxime deserunt laboriosam est voluptas dolorem.',
                'created_at' => '2016-01-04T15:31:51.081Z',
                'iid' => 6,
                'labels' => [],
                'user_notes_count' => 1,
                'due_date' => '2016-07-22',
                'web_url' => 'http://example.com/example/example/issues/6',
                'confidential' => false,
                'weight' => null,
                'discussion_locked' => false,
                'time_stats' => [
                    'time_estimate' => 0,
                    'total_time_spent' => 0,
                    'human_time_estimate' => null,
                    'human_total_time_spent' => null,
                ],
            ],
        ];
    }

    /**
     * Get expected array for tests which check project users method.
     *
     * @return array
     */
    public function getProjectUsersExpectedArray()
    {
        return [
            [
                'id' => 1,
                'name' => 'John Doe',
                'username' => 'john.doe',
                'state' => 'active',
                'avatar_url' => 'https://example.com',
                'web_url' => 'https://gitlab.com/john.doe',
            ],
        ];
    }

    /**
     * @test
     */
    public function shouldGetBoards(): void
    {
        $expectedArray = $this->getProjectIssuesExpectedArray();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/boards')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->boards(1));
    }

    /**
     * Get expected array for tests which check project boards.
     *
     * @return array
     *               Project issues list
     */
    public function getProjectBoardsExpectedArray()
    {
        return [
            [
                'id' => 1,
                'project' => [
                    'id' => 5,
                    'name' => 'Diaspora Project Site',
                    'name_with_namespace' => 'Diaspora / Diaspora Project Site',
                    'path' => 'diaspora-project-site',
                    'path_with_namespace' => 'diaspora/diaspora-project-site',
                    'http_url_to_repo' => 'http://example.com/diaspora/diaspora-project-site.git',
                    'web_url' => 'http://example.com/diaspora/diaspora-project-site',
                ],
                'milestone' => [
                    'id' => 12,
                    'title' => '10.0',
                ],
                'lists' => [
                    [
                        'id' => 1,
                        'label' => [
                            'name' => 'Testing',
                            'color' => '#F0AD4E',
                            'description' => null,
                        ],
                        'position' => 1,
                    ],
                    [
                        'id' => 2,
                        'label' => [
                            'name' => 'Ready',
                            'color' => '#FF0000',
                            'description' => null,
                        ],
                        'position' => 2,
                    ],
                    [
                        'id' => 3,
                        'label' => [
                            'name' => 'Production',
                            'color' => '#FF5F00',
                            'description' => null,
                        ],
                        'position' => 3,
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function shouldCreateTrigger(): void
    {
        $expectedArray = [
            'id' => 4,
            'description' => 'foobar',
            'token' => '6d056f63e50fe6f8c5f8f4aa10edb7',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/triggers', ['description' => 'foobar'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->createTrigger(1, 'foobar'));
    }

    /**
     * @test
     */
    public function shouldGetPipelinesWithBooleanParam(): void
    {
        $expectedArray = [
            ['id' => 1, 'status' => 'success', 'ref' => 'new-pipeline'],
            ['id' => 2, 'status' => 'failed', 'ref' => 'new-pipeline'],
            ['id' => 3, 'status' => 'pending', 'ref' => 'test-pipeline'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines', ['yaml_errors' => 'false'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->pipelines(1, ['yaml_errors' => false]));
    }

    /**
     * @test
     */
    public function shouldGetPipelineWithDateParam(): void
    {
        $expectedArray = [
            ['id' => 1, 'status' => 'success', 'ref' => 'new-pipeline'],
            ['id' => 2, 'status' => 'failed', 'ref' => 'new-pipeline'],
            ['id' => 3, 'status' => 'pending', 'ref' => 'test-pipeline'],
        ];

        $updated_after = new \DateTime('2018-01-01 00:00:00');
        $updated_before = new \DateTime('2018-01-31 00:00:00');

        $expectedWithArray = [
            'updated_after' => $updated_after->format('Y-m-d'),
            'updated_before' => $updated_before->format('Y-m-d'),
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines', $expectedWithArray)
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->pipelines(1, [
            'updated_after' => $updated_after,
            'updated_before' => $updated_before,
        ]));
    }

    /**
     * @test
     */
    public function shouldGetPipelinesWithSHA(): void
    {
        $expectedArray = [
            ['id' => 1, 'status' => 'success', 'ref' => 'new-pipeline'],
            ['id' => 2, 'status' => 'failed', 'ref' => 'new-pipeline'],
            ['id' => 3, 'status' => 'pending', 'ref' => 'test-pipeline'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines', ['sha' => '123'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->pipelines(1, ['sha' => '123']));
    }

    /**
     * @test
     */
    public function shouldGetPipeline(): void
    {
        $expectedArray = [
            ['id' => 1, 'status' => 'success', 'ref' => 'new-pipeline'],
            ['id' => 2, 'status' => 'failed', 'ref' => 'new-pipeline'],
            ['id' => 3, 'status' => 'pending', 'ref' => 'test-pipeline'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines/3')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->pipeline(1, 3));
    }

    /**
     * @test
     */
    public function shouldGetPipelineVariables(): void
    {
        $expectedArray = [
            ['key' => 'foo', 'value' => 'bar'],
            ['key' => 'baz', 'value' => '1234'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines/3/variables')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->pipelineVariables(1, 3));
    }

    /**
     * @test
     */
    public function shouldCreatePipeline(): void
    {
        $expectedArray = [
            ['id' => 4, 'status' => 'created', 'ref' => 'test-pipeline'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipeline', ['ref' => 'test-pipeline'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->createPipeline(1, 'test-pipeline'));
    }

    /**
     * @test
     */
    public function shouldCreatePipelineWithVariables(): void
    {
        $expectedArray = [
            ['id' => 4, 'status' => 'created', 'ref' => 'test-pipeline'],
        ];
        $variables = [
            [
                'key' => 'test_var_1',
                'value' => 'test_value_1',
            ],
            [
                'key' => 'test_var_2',
                'variable_type' => 'file',
                'value' => 'test_value_2',
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipeline', ['ref' => 'test-pipeline', 'variables' => $variables])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->createPipeline(1, 'test-pipeline', $variables));
    }

    /**
     * @test
     */
    public function shouldRetryPipeline(): void
    {
        $expectedArray = [
            ['id' => 5, 'status' => 'pending', 'ref' => 'test-pipeline'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipelines/4/retry')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->retryPipeline(1, 4));
    }

    /**
     * @test
     */
    public function shouldCancelPipeline(): void
    {
        $expectedArray = [
            ['id' => 6, 'status' => 'cancelled', 'ref' => 'test-pipeline'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipelines/6/cancel')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->cancelPipeline(1, 6));
    }

    /**
     * @test
     */
    public function shouldDeletePipeline(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/pipelines/3')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->deletePipeline(1, 3));
    }

    /**
     * @test
     */
    public function shouldGetAllMembers(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/members/all')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->allMembers(1));
    }

    /**
     * @test
     */
    public function shouldGetMembers(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/members')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->members(1));
    }

    /**
     * @test
     */
    public function shouldGetMembersWithQuery(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/members', ['query' => 'at'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->members(1, ['query' => 'at']));
    }

    /**
     * @test
     */
    public function shouldGetMembersWithNullQuery(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/members')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->members(1));
    }

    /**
     * @test
     */
    public function shouldGetMembersWithPagination(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/members', [
                'page' => 2,
                'per_page' => 15,
            ])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->members(1, ['page' => 2, 'per_page' => 15]));
    }

    /**
     * @test
     */
    public function shouldGetMember(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'Matt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/members/2')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->member(1, 2));
    }

    /**
     * @test
     */
    public function shouldAddMember(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/members', ['user_id' => 2, 'access_level' => 3])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->addMember(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldSaveMember(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/members/2', ['access_level' => 4])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->saveMember(1, 2, 4));
    }

    /**
     * @test
     */
    public function shouldRemoveMember(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/members/2')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->removeMember(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetHooks(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Test hook'],
            ['id' => 2, 'name' => 'Another hook'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/hooks')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->hooks(1));
    }

    /**
     * @test
     */
    public function shouldGetHook(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'Another hook'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/hooks/2')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->hook(1, 2));
    }

    /**
     * @test
     */
    public function shouldAddHook(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/hooks', [
                'url' => 'http://www.example.com',
                'push_events' => true,
                'issues_events' => true,
                'merge_requests_events' => true,
            ])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->addHook(
            1,
            'http://www.example.com',
            ['push_events' => true, 'issues_events' => true, 'merge_requests_events' => true]
        ));
    }

    /**
     * @test
     */
    public function shouldAddHookWithOnlyUrl(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/hooks', ['url' => 'http://www.example.com', 'push_events' => true])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->addHook(1, 'http://www.example.com'));
    }

    /**
     * @test
     */
    public function shouldAddHookWithoutPushEvents(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/hooks', ['url' => 'http://www.example.com', 'push_events' => false])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->addHook(1, 'http://www.example.com', ['push_events' => false]));
    }

    /**
     * @test
     */
    public function shouldUpdateHook(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/hooks/3', ['url' => 'http://www.example-test.com', 'push_events' => false])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals(
            $expectedArray,
            $api->updateHook(1, 3, ['url' => 'http://www.example-test.com', 'push_events' => false])
        );
    }

    /**
     * @test
     */
    public function shouldRemoveHook(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/hooks/2')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->removeHook(1, 2));
    }

    /**
     * @test
     */
    public function shouldTransfer(): void
    {
        $expectedArray = [
            'id' => 1,
            'name' => 'Project Name',
            'namespace' => ['name' => 'a_namespace'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/transfer', ['namespace' => 'a_namespace'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->transfer(1, 'a_namespace'));
    }

    /**
     * @test
     */
    public function shouldGetDeployKeys(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'test-key'],
            ['id' => 2, 'title' => 'another-key'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deploy_keys')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->deployKeys(1));
    }

    /**
     * @test
     */
    public function shouldGetDeployKey(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'another-key'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deploy_keys/2')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->deployKey(1, 2));
    }

    /**
     * @test
     */
    public function shouldAddKey(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'new-key', 'can_push' => false];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/deploy_keys', ['title' => 'new-key', 'key' => '...', 'can_push' => false])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->addDeployKey(1, 'new-key', '...'));
    }

    /**
     * @test
     */
    public function shouldAddKeyWithPushOption(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'new-key', 'can_push' => true];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/deploy_keys', ['title' => 'new-key', 'key' => '...', 'can_push' => true])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->addDeployKey(1, 'new-key', '...', true));
    }

    /**
     * @test
     */
    public function shouldDeleteDeployKey(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/deploy_keys/3')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->deleteDeployKey(1, 3));
    }

    /**
     * @test
     */
    public function shoudEnableDeployKey(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/deploy_keys/3/enable')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->enableDeployKey(1, 3));
    }

    /**
     * @test
     */
    public function shouldGetEvents(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An event'],
            ['id' => 2, 'title' => 'Another event'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/events', [])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->events(1));
    }

    /**
     * @test
     */
    public function shouldGetEventsWithDateTimeParams(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An event'],
            ['id' => 2, 'title' => 'Another event'],
        ];

        $after = new \DateTime('2018-01-01 00:00:00');
        $before = new \DateTime('2018-01-31 00:00:00');

        $expectedWithArray = [
            'after' => $after->format('Y-m-d'),
            'before' => $before->format('Y-m-d'),
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/events', $expectedWithArray)
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->events(1, ['after' => $after, 'before' => $before]));
    }

    /**
     * @test
     */
    public function shouldGetEventsWithPagination(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An event'],
            ['id' => 2, 'title' => 'Another event'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/events', [
                'page' => 2,
                'per_page' => 15,
            ])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->events(1, ['page' => 2, 'per_page' => 15]));
    }

    /**
     * @test
     */
    public function shouldGetLabels(): void
    {
        $expectedArray = [
            ['id' => 987, 'name' => 'bug', 'color' => '#000000'],
            ['id' => 123, 'name' => 'feature', 'color' => '#ff0000'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/labels')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->labels(1));
    }

    /**
     * @test
     */
    public function shouldAddLabel(): void
    {
        $expectedArray = ['name' => 'bug', 'color' => '#000000'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/labels', ['name' => 'wont-fix', 'color' => '#ffffff'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->addLabel(1, ['name' => 'wont-fix', 'color' => '#ffffff']));
    }

    /**
     * @test
     */
    public function shouldUpdateLabel(): void
    {
        $expectedArray = ['name' => 'bug', 'color' => '#00ffff'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/labels', ['name' => 'bug', 'new_name' => 'big-bug', 'color' => '#00ffff'])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals(
            $expectedArray,
            $api->updateLabel(1, ['name' => 'bug', 'new_name' => 'big-bug', 'color' => '#00ffff'])
        );
    }

    /**
     * @test
     */
    public function shouldRemoveLabel(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/labels', ['name' => 'bug'])
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->removeLabel(1, 'bug'));
    }

    /**
     * @test
     */
    public function shouldGetLanguages(): void
    {
        $expectedArray = ['php' => 100];
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->languages(1));
    }

    /**
     * @test
     */
    public function shouldForkWithNamespace(): void
    {
        $expectedArray = [
            'namespace' => 'new_namespace',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/fork', $expectedArray)
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->fork(1, [
            'namespace' => 'new_namespace',
        ]));
    }

    /**
     * @test
     */
    public function shouldForkWithNamespaceAndPath(): void
    {
        $expectedArray = [
            'namespace' => 'new_namespace',
            'path' => 'new_path',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/fork', $expectedArray)
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->fork(1, [
            'namespace' => 'new_namespace',
            'path' => 'new_path',
        ]));
    }

    /**
     * @test
     */
    public function shouldForkWithNamespaceAndPathAndName(): void
    {
        $expectedArray = [
            'namespace' => 'new_namespace',
            'path' => 'new_path',
            'name' => 'new_name',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/fork', $expectedArray)
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->fork(1, [
            'namespace' => 'new_namespace',
            'path' => 'new_path',
            'name' => 'new_name',
        ]));
    }

    /**
     * @test
     */
    public function shouldCreateForkRelation(): void
    {
        $expectedArray = ['project_id' => 1, 'forked_id' => 2];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/fork/2')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->createForkRelation(1, 2));
    }

    /**
     * @test
     */
    public function shouldRemoveForkRelation(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/2/fork')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->removeForkRelation(2));
    }

    /**
     * @test
     */
    public function shouldGetForks(): void
    {
        $expectedArray = [
            [
                'id' => 2,
                'forked_from_project' => [
                    'id' => 1,
                ],
            ],
            [
                'id' => 3,
                'forked_from_project' => [
                    'id' => 1,
                ],
            ],
        ];
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/forks')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->forks(1));
    }

    /**
     * @test
     */
    public function shouldSetService(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/services/hipchat', ['param' => 'value'])
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->setService(1, 'hipchat', ['param' => 'value']));
    }

    /**
     * @test
     */
    public function shouldRemoveService(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/services/hipchat')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->removeService(1, 'hipchat'));
    }

    /**
     * @test
     */
    public function shouldGetVariables(): void
    {
        $expectedArray = [
            ['key' => 'ftp_username', 'value' => 'ftp'],
            ['key' => 'ftp_password', 'value' => 'somepassword'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/variables')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->variables(1));
    }

    /**
     * @test
     */
    public function shouldGetVariable(): void
    {
        $expectedArray = ['key' => 'ftp_username', 'value' => 'ftp'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/variables/ftp_username')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->variable(1, 'ftp_username'));
    }

    /**
     * @test
     */
    public function shouldAddVariable(): void
    {
        $expectedKey = 'ftp_port';
        $expectedValue = '21';

        $expectedArray = [
            'key' => $expectedKey,
            'value' => $expectedValue,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/variables', $expectedArray)
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->addVariable(1, $expectedKey, $expectedValue));
    }

    /**
     * @test
     */
    public function shouldAddVariableWithProtected(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/variables', $expectedArray)
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true));
    }

    /**
     * @test
     */
    public function shouldAddVariableWithEnvironment(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'environment_scope' => 'staging',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/variables', $expectedArray)
            ->will($this->returnValue($expectedArray));

        $this->assertEquals(
            $expectedArray,
            $api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging')
        );
    }

    /**
     * @test
     */
    public function shouldAddVariableWithProtectionAndEnvironment(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
            'environment_scope' => 'staging',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/variables', $expectedArray)
            ->will($this->returnValue($expectedArray));

        $this->assertEquals(
            $expectedArray,
            $api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true, 'staging')
        );
    }

    /**
     * @test
     */
    public function shouldAddVariableWithEnvironmentAndVariableType(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'environment_scope' => 'staging',
            'variable_type' => 'file',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/variables', $expectedArray)
            ->will($this->returnValue($expectedArray));

        $this->assertEquals(
            $expectedArray,
            $api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging', ['variable_type' => 'file'])
        );
    }

    /**
     * @test
     */
    public function shouldAddVariableWithEnvironmentFromParameterList(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'environment_scope' => 'staging',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/variables', $expectedArray)
            ->will($this->returnValue($expectedArray));

        $this->assertEquals(
            $expectedArray,
            $api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging', ['environment_scope' => 'production'])
        );
    }

    /**
     * @test
     */
    public function shouldUpdateVariable(): void
    {
        $expectedKey = 'ftp_port';
        $expectedValue = '22';

        $expectedArray = [
            'key' => 'ftp_port',
            'value' => '22',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/variables/'.$expectedKey, ['value' => $expectedValue])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->updateVariable(1, $expectedKey, $expectedValue));
    }

    /**
     * @test
     */
    public function shouldUpdateVariableWithProtected(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/variables/DEPLOY_SERVER', ['value' => 'stage.example.com', 'protected' => true])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true));
    }

    /**
     * @test
     */
    public function shouldUpdateVariableWithEnvironment(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'environment_scope' => 'staging',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with(
                'projects/1/variables/DEPLOY_SERVER',
                ['value' => 'stage.example.com', 'environment_scope' => 'staging']
            )
            ->will($this->returnValue($expectedArray));

        $this->assertEquals(
            $expectedArray,
            $api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging')
        );
    }

    /**
     * @test
     */
    public function shouldUpdateVariableWithProtectedAndEnvironment(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
            'environment_scope' => 'staging',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with(
                'projects/1/variables/DEPLOY_SERVER',
                ['value' => 'stage.example.com', 'protected' => true, 'environment_scope' => 'staging']
            )
            ->will($this->returnValue($expectedArray));

        $this->assertEquals(
            $expectedArray,
            $api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true, 'staging')
        );
    }

    /**
     * @test
     */
    public function shouldUpdateVariableWithEnvironmentAndVariableType(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'environment_scope' => 'staging',
            'variable_type' => 'file',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with(
                'projects/1/variables/DEPLOY_SERVER',
                ['value' => 'stage.example.com', 'environment_scope' => 'staging', 'variable_type' => 'file']
            )
            ->will($this->returnValue($expectedArray));

        $this->assertEquals(
            $expectedArray,
            $api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging', ['variable_type' => 'file'])
        );
    }

    /**
     * @test
     */
    public function shouldUpdateVariableWithEnvironmentFromParameterList(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'environment_scope' => 'staging',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with(
                'projects/1/variables/DEPLOY_SERVER',
                ['value' => 'stage.example.com', 'environment_scope' => 'staging']
            )
            ->will($this->returnValue($expectedArray));

        $this->assertEquals(
            $expectedArray,
            $api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging', ['environment_scope' => 'production'])
        );
    }

    /**
     * @test
     */
    public function shouldRemoveVariable(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/variables/ftp_password')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->removeVariable(1, 'ftp_password'));
    }

    protected function getMultipleProjectsRequestMock($path, $expectedArray = [], $expectedParameters = [])
    {
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with($path, $expectedParameters)
            ->will($this->returnValue($expectedArray));

        return $api;
    }

    /**
     * @test
     */
    public function shouldGetDeployments(): void
    {
        $expectedArray = [
            ['id' => 1, 'sha' => '0000001'],
            ['id' => 2, 'sha' => '0000002'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deployments', [])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->deployments(1));
    }

    /**
     * @test
     */
    public function shouldGetDeploymentsWithPagination(): void
    {
        $expectedArray = [
            ['id' => 1, 'sha' => '0000001'],
            ['id' => 2, 'sha' => '0000002'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deployments', [
                'page' => 2,
                'per_page' => 15,
            ])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->deployments(1, ['page' => 2, 'per_page' => 15]));
    }

    protected function getMultipleProjectsData()
    {
        return [
            ['id' => 1, 'name' => 'A project'],
            ['id' => 2, 'name' => 'Another project'],
        ];
    }

    public function possibleAccessLevels()
    {
        return [
            [10],
            [20],
            [30],
            [40],
            [50],
        ];
    }

    public function getBadgeExpectedArray()
    {
        return [
            [
                'id' => 1,
                'link_url' => 'http://example.com/ci_status.svg?project=%{project_path}&ref=%{default_branch}',
                'image_url' => 'https://shields.io/my/badge',
                'rendered_link_url' => 'http://example.com/ci_status.svg?project=example-org/example-project&ref=master',
                'rendered_image_url' => 'https://shields.io/my/badge',
                'kind' => 'project',
            ],
            [
                'id' => 2,
                'link_url' => 'http://example.com/ci_status.svg?project=%{project_path}&ref=%{default_branch}',
                'image_url' => 'https://shields.io/my/badge',
                'rendered_link_url' => 'http://example.com/ci_status.svg?project=example-org/example-project&ref=master',
                'rendered_image_url' => 'https://shields.io/my/badge',
                'kind' => 'group',
            ],
        ];
    }

    /**
     * @test
     */
    public function shouldGetBadges(): void
    {
        $expectedArray = $this->getBadgeExpectedArray();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/badges')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->badges(1));
    }

    /**
     * @test
     */
    public function shouldGetBadge(): void
    {
        $expectedBadgesArray = $this->getBadgeExpectedArray();
        $expectedArray = [
            $expectedBadgesArray[0],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/badges/1')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->badge(1, 1));
    }

    /**
     * @test
     */
    public function shouldAddBadge(): void
    {
        $link_url = 'http://example.com/ci_status.svg?project=%{project_path}&ref=%{default_branch}';
        $image_url = 'https://shields.io/my/badge';
        $expectedArray = [
            'id' => 3,
            'link_url' => $link_url,
            'image_url' => $image_url,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/badges', ['link_url' => $link_url, 'image_url' => $image_url])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals(
            $expectedArray,
            $api->addBadge(1, ['link_url' => $link_url, 'image_url' => $image_url])
        );
    }

    /**
     * @test
     */
    public function shouldUpdateBadge(): void
    {
        $image_url = 'https://shields.io/my/new/badge';
        $expectedArray = [
            'id' => 2,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/badges/2')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->updateBadge(1, 2, ['image_url' => $image_url]));
    }

    /**
     * @test
     */
    public function shouldRemoveBadge(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/badges/1')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->removeBadge(1, 1));
    }

    /**
     * @test
     */
    public function shouldAddProtectedBranch(): void
    {
        $expectedArray = [
            'name' => 'master',
            'push_access_level' => [
                'access_level' => 0,
                'access_level_description' => 'No one',
            ],
            'merge_access_levels' => [
                'access_level' => 0,
                'access_level_description' => 'Developers + Maintainers',
            ],
        ];
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with(
                'projects/1/protected_branches',
                ['name' => 'master', 'push_access_level' => 0, 'merge_access_level' => 30]
            )
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->addProtectedBranch(1, ['name' => 'master', 'push_access_level' => 0, 'merge_access_level' => 30]));
    }

    /**
     * @test
     */
    public function shoudGetApprovalsConfiguration(): void
    {
        $expectedArray = [
            'approvers' => [],
            'approver_groups' => [],
            'approvals_before_merge' => 1,
            'reset_approvals_on_push' => true,
            'disable_overriding_approvers_per_merge_request' => null,
            'merge_requests_author_approval' => null,
            'merge_requests_disable_committers_approval' => null,
            'require_password_to_approve' => null,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/approvals')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->approvalsConfiguration(1));
    }

    /**
     * @test
     */
    public function shoudGetApprovalsRules(): void
    {
        $expectedArray = [
            [
                'id' => 1,
                'name' => 'All Members',
                'rule_type' => 'any_approver',
                'eligible_approvers' => [],
                'approvals_required' => 1,
                'users' => [],
                'groups' => [],
                'contains_hidden_groups' => false,
                'protected_branches' => [],
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/approval_rules')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->approvalsRules(1));
    }

    /**
     * @test
     */
    public function shouldDeleteAllMergedBranches(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/repository/merged_branches')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->deleteAllMergedBranches(1));
    }

    /**
     * @test
     */
    public function shouldGetProtectedBranches(): void
    {
        $expectedArray = [
            [
                'id' => 1,
                'name' => 'master',
                'push_access_levels' => [
                    'access_level' => 0,
                    'access_level_description' => 'No one',
                    'user_id' => null,
                    'group_id' => null,
                ],
                'merge_access_levels' => [
                    'access_level' => 40,
                    'access_level_description' => 'Maintainers',
                    'user_id' => null,
                    'group_id' => null,
                ],
                'unprotect_access_levels' => [],
                'code_owner_approval_required' => false,
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/protected_branches')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->protectedBranches(1));
    }

    protected function getApiClass()
    {
        return Projects::class;
    }
}
