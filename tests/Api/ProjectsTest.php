<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Tests\Api;

use DateTime;
use Gitlab\Api\Projects;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

class ProjectsTest extends TestCase
{
    #[Test]
    public function shouldGetAllProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray);

        $this->assertEquals($expectedArray, $api->all());
    }

    #[Test]
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

    #[Test]
    public function shouldNotNeedPaginationWhenGettingProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->all());
    }

    #[Test]
    public function shouldGetAccessibleProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray);

        $this->assertEquals($expectedArray, $api->all());
    }

    #[Test]
    public function shouldGetOwnedProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['owned' => 'true']);

        $this->assertEquals($expectedArray, $api->all(['owned' => true]));
    }

    #[Test]
    public function shouldGetNotArchivedProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['archived' => 'false']);

        $this->assertEquals($expectedArray, $api->all(['archived' => false]));
    }

    #[Test]
    #[DataProvider('possibleAccessLevels')]
    public function shouldGetProjectsWithMinimumAccessLevel($level): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['min_access_level' => $level]);

        $this->assertEquals($expectedArray, $api->all(['min_access_level' => $level]));
    }

    #[Test]
    public function shouldSearchProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['search' => 'a project']);
        $this->assertEquals($expectedArray, $api->all(['search' => 'a project']));
    }

    #[Test]
    public function shouldSearchProjectsWithNamespace(): void
    {
        $expectedArray = $this->getMultipleProjectsDataWithNamespace();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['search' => 'a_project', 'search_namespaces' => 'true']);
        $this->assertEquals($expectedArray, $api->all(['search' => 'a_project', 'search_namespaces' => true]));
    }

    #[Test]
    public function shouldGetProjectsAfterId(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['id_after' => 0]);

        $this->assertEquals($expectedArray, $api->all(['id_after' => 0]));
    }

    #[Test]
    public function shouldGetProjectsWithLastActivityAfter(): void
    {
        $unixEpochDateTime = new DateTime('@0');

        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['last_activity_after' => $unixEpochDateTime->format('c')]);

        $this->assertEquals($expectedArray, $api->all(['last_activity_after' => $unixEpochDateTime]));
    }

    #[Test]
    public function shouldGetProjectsWithLastActivityBefore(): void
    {
        $now = new DateTime();

        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['last_activity_before' => $now->format('c')]);

        $this->assertEquals($expectedArray, $api->all(['last_activity_before' => $now]));
    }

    #[Test]
    public function shouldGetProjectsWithoutFailedRepositoryChecksum(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['repository_checksum_failed' => 'false']);

        $this->assertEquals($expectedArray, $api->all(['repository_checksum_failed' => false]));
    }

    #[Test]
    public function shouldGetProjectsWithDefaultRepositoryStorage(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['repository_storage' => 'default']);

        $this->assertEquals($expectedArray, $api->all(['repository_storage' => 'default']));
    }

    #[Test]
    public function shouldGetStarredProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['starred' => 'true']);

        $this->assertEquals($expectedArray, $api->all(['starred' => true]));
    }

    #[Test]
    public function shouldGetProjectsWithoutFailedWikiChecksum(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['wiki_checksum_failed' => 'false']);

        $this->assertEquals($expectedArray, $api->all(['wiki_checksum_failed' => false]));
    }

    #[Test]
    public function shouldGetProjectsWithCustomAttributes(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['with_custom_attributes' => 'true']);

        $this->assertEquals($expectedArray, $api->all(['with_custom_attributes' => true]));
    }

    #[Test]
    public function shouldGetProjectsWithPhpProgrammingLanguage(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['with_programming_language' => 'php']);

        $this->assertEquals($expectedArray, $api->all(['with_programming_language' => 'php']));
    }

    #[Test]
    public function shouldShowProject(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Project Name'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->show(1));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->show(1, ['statistics' => true]));
    }

    #[Test]
    public function shouldCreateProject(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Project Name'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects', ['name' => 'Project Name', 'issues_enabled' => true])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->create('Project Name', [
            'issues_enabled' => true,
        ]));
    }

    #[Test]
    public function shouldUpdateProject(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Updated Name'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1', ['name' => 'Updated Name', 'issues_enabled' => true])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->update(1, [
            'name' => 'Updated Name',
            'issues_enabled' => true,
        ]));
    }

    #[Test]
    public function shouldArchiveProject(): void
    {
        $expectedArray = ['id' => 1, 'archived' => true];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/archive')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->archive(1));
    }

    #[Test]
    public function shouldUnarchiveProject(): void
    {
        $expectedArray = ['id' => 1, 'archived' => false];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/unarchive')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->unarchive(1));
    }

    #[Test]
    public function shouldCreateProjectForUser(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Project Name'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/user/1', ['name' => 'Project Name', 'issues_enabled' => true])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->createForUser(1, 'Project Name', [
            'issues_enabled' => true,
        ]));
    }

    #[Test]
    public function shouldRemoveProject(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->remove(1));
    }

    #[Test]
    public function shouldRemoveProjectPermanently(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1', ['full_path' => 'group/project', 'permanently_remove' => true])
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->remove(1, [
            'full_path' => 'group/project',
            'permanently_remove' => true,
        ]));
    }

    #[Test]
    public function shouldRemoveProjectPermanentlyWithStringParameter(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1', ['full_path' => 'group/project', 'permanently_remove' => 'true'])
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->remove(1, [
            'full_path' => 'group/project',
            'permanently_remove' => 'true',
        ]));
    }

    #[Test]
    public function shouldRestoreProject(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Project Name'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/restore')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->restore(1));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->pipelines(1));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->triggers(1));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->trigger(1, 3));
    }

    #[Test]
    public function shouldGetProjectIssues(): void
    {
        $expectedArray = $this->getProjectIssuesExpectedArray();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->issues(1));
    }

    #[Test]
    public function shouldGetProjectUsers(): void
    {
        $expectedArray = $this->getProjectUsersExpectedArray();

        $api = $this->getApiMock();
        $api->expects($this->once())
        ->method('get')
        ->with('projects/1/users')
        ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->users(1));
    }

    #[Test]
    public function shouldGetProjectIssuesParameters(): void
    {
        $expectedArray = $this->getProjectIssuesExpectedArray();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->issues(1, ['state' => 'opened']));
    }

    /**
     * Get expected array for tests which check project issues method.
     *
     * @return array
     *               Project issues list
     */
    public function getProjectIssuesExpectedArray(): array
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
     */
    public function getProjectUsersExpectedArray(): array
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

    #[Test]
    public function shouldGetBoards(): void
    {
        $expectedArray = $this->getProjectIssuesExpectedArray();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/boards')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->boards(1));
    }

    /**
     * Get expected array for tests which check project boards.
     *
     * @return array
     *               Project issues list
     */
    public function getProjectBoardsExpectedArray(): array
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

    #[Test]
    public function shouldGetIterations(): void
    {
        $expectedArray = [
            [
                'id' => 5,
                'iid' => 2,
                'sequence' => 1,
                'group_id' => 123,
                'title' => '2022: Sprint 1',
                'description' => '',
                'state' => 3,
                'created_at' => '2021-09-29T21:24:43.913Z',
                'updated_at' => '2022-03-29T19:09:08.368Z',
                'start_date' => '2022-01-10',
                'due_date' => '2022-01-23',
                'web_url' => 'https://example.com/groups/example/-/iterations/34',
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/iterations')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->iterations(1));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->createTrigger(1, 'foobar'));
    }

    #[Test]
    public function shouldRemoveTrigger(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/triggers/2')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeTrigger(1, 2));
    }

    #[Test]
    public function shouldTriggerPipeline(): void
    {
        $expectedArray = [
            'id' => 4,
            'sha' => 'commit_hash',
            'ref' => 'master',
            'status' => 'pending',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with(
                'projects/1/trigger/pipeline',
                ['ref' => 'master', 'token' => 'some_token', 'variables' => ['VAR_1' => 'value 1']]
            )
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->triggerPipeline(1, 'master', 'some_token', ['VAR_1' => 'value 1']));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->pipelines(1, ['yaml_errors' => false]));
    }

    #[Test]
    public function shouldGetPipelineWithDateParam(): void
    {
        $expectedArray = [
            ['id' => 1, 'status' => 'success', 'ref' => 'new-pipeline'],
            ['id' => 2, 'status' => 'failed', 'ref' => 'new-pipeline'],
            ['id' => 3, 'status' => 'pending', 'ref' => 'test-pipeline'],
        ];

        $updatedAfter = new DateTime('2018-01-01 00:00:00');
        $updatedBefore = new DateTime('2018-01-31 00:00:00');

        $expectedWithArray = [
            'updated_after' => $updatedAfter->format('c'),
            'updated_before' => $updatedBefore->format('c'),
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines', $expectedWithArray)
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->pipelines(1, [
            'updated_after' => $updatedAfter,
            'updated_before' => $updatedBefore,
        ]));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->pipelines(1, ['sha' => '123']));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->pipeline(1, 3));
    }

    #[Test]
    public function shouldGetPipelineJobs(): void
    {
        $expectedArray = [
            ['id' => 1, 'status' => 'success', 'stage' => 'Build'],
            ['id' => 2, 'status' => 'failed', 'stage' => 'Build'],
            ['id' => 3, 'status' => 'pending', 'stage' => 'Build'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines/3/jobs')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->pipelineJobs(1, 3));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->pipelineVariables(1, 3));
    }

    #[Test]
    public function shouldGetPipelineTestReport(): void
    {
        $expectedArray = [
            'total_time' => 0.011809,
            'total_count' => 8,
            'success_count' => 8,
            'failed_count' => 0,
            'skipped_count' => 0,
            'error_count' => 0,
            'test_suites' => [],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines/3/test_report')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->pipelineTestReport(1, 3));
    }

    #[Test]
    public function shouldGetPipelineTestReportSummary(): void
    {
        $expectedArray = [
            'total_time' => 0.011809,
            'total_count' => 8,
            'success_count' => 8,
            'failed_count' => 0,
            'skipped_count' => 0,
            'error_count' => 0,
            'test_suites' => [],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines/3/test_report_summary')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->pipelineTestReportSummary(1, 3));
    }

    #[Test]
    public function shouldCreatePipeline(): void
    {
        $expectedArray = [
            ['id' => 4, 'status' => 'created', 'ref' => 'test-pipeline'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipeline', [], [], [], ['ref' => 'test-pipeline'])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->createPipeline(1, 'test-pipeline'));
    }

    #[Test]
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
            ->with('projects/1/pipeline', ['variables' => $variables], [], [], ['ref' => 'test-pipeline'])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->createPipeline(1, 'test-pipeline', $variables));
    }

    #[Test]
    public function shouldCreatePipelineWithInputs(): void
    {
        $expectedArray = [
            ['id' => 4, 'status' => 'created', 'ref' => 'test-pipeline'],
        ];
        $inputs = [
            'environment' => 'production',
            'scan_security' => false,
            'level' => 3,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipeline', ['inputs' => $inputs], [], [], ['ref' => 'test-pipeline'])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->createPipeline(1, 'test-pipeline', null, ['inputs' => $inputs]));
    }

    #[Test]
    public function shouldCreatePipelineWithVariablesAndInputs(): void
    {
        $expectedArray = [
            ['id' => 4, 'status' => 'created', 'ref' => 'test-pipeline'],
        ];
        $variables = [
            [
                'key' => 'test_var_1',
                'value' => 'test_value_1',
            ],
        ];
        $inputs = [
            'environment' => 'production',
            'scan_security' => false,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipeline', ['inputs' => $inputs, 'variables' => $variables], [], [], ['ref' => 'test-pipeline'])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->createPipeline(1, 'test-pipeline', $variables, ['inputs' => $inputs]));
    }

    #[Test]
    public function shouldRetryPipeline(): void
    {
        $expectedArray = [
            ['id' => 5, 'status' => 'pending', 'ref' => 'test-pipeline'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipelines/4/retry')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->retryPipeline(1, 4));
    }

    #[Test]
    public function shouldCancelPipeline(): void
    {
        $expectedArray = [
            ['id' => 6, 'status' => 'cancelled', 'ref' => 'test-pipeline'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipelines/6/cancel')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->cancelPipeline(1, 6));
    }

    #[Test]
    public function shouldDeletePipeline(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/pipelines/3')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->deletePipeline(1, 3));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->allMembers(1));
    }

    #[Test]
    public function shouldGetAllMember(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'Bob'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/members/all/2')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->allMember(1, 2));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->members(1));
    }

    #[Test]
    public function shouldGetMembersWithQuery(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/members', ['query' => 'at'])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->members(1, ['query' => 'at']));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->members(1));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->members(1, ['page' => 2, 'per_page' => 15]));
    }

    #[Test]
    public function shouldGetMember(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'Matt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/members/2')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->member(1, 2));
    }

    #[Test]
    public function shouldAddMember(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/members', ['user_id' => 2, 'access_level' => 3])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->addMember(1, 2, 3));
    }

    #[Test]
    public function shouldAddMemberWithExpiration(): void
    {
        // tomorrow
        $expiration = \date('Y-m-d', \time() + 86400);
        $expectedArray = [
            'user_id' => 3,
            'access_level' => 3,
            'expires_at' => $expiration,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/members', ['user_id' => 3, 'access_level' => 3, 'expires_at' => $expiration])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->addMember(1, 3, 3, $expiration));
    }

    #[Test]
    public function shouldSaveMember(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/members/2', ['access_level' => 4])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->saveMember(1, 2, 4));
    }

    #[Test]
    public function shouldSaveMemberWithExpiration(): void
    {
        // tomorrow
        $expiration = \date('Y-m-d', \time() + 86400);
        $expectedArray = [
            'user_id' => 3,
            'access_level' => 4,
            'expires_at' => $expiration,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/members/3', ['access_level' => 4, 'expires_at' => $expiration])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->saveMember(1, 3, 4, $expiration));
    }

    #[Test]
    public function shouldRemoveMember(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/members/2')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeMember(1, 2));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->hooks(1));
    }

    #[Test]
    public function shouldGetHook(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'Another hook'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/hooks/2')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->hook(1, 2));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->addHook(
            1,
            'http://www.example.com',
            ['push_events' => true, 'issues_events' => true, 'merge_requests_events' => true]
        ));
    }

    #[Test]
    public function shouldAddHookWithOnlyUrl(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/hooks', ['url' => 'http://www.example.com', 'push_events' => true])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->addHook(1, 'http://www.example.com'));
    }

    #[Test]
    public function shouldAddHookWithoutPushEvents(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/hooks', ['url' => 'http://www.example.com', 'push_events' => false])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->addHook(1, 'http://www.example.com', ['push_events' => false]));
    }

    #[Test]
    public function shouldUpdateHook(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/hooks/3', ['url' => 'http://www.example-test.com', 'push_events' => false])
            ->willReturn($expectedArray);

        $this->assertEquals(
            $expectedArray,
            $api->updateHook(1, 3, ['url' => 'http://www.example-test.com', 'push_events' => false])
        );
    }

    #[Test]
    public function shouldRemoveHook(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/hooks/2')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeHook(1, 2));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->transfer(1, 'a_namespace'));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->deployKeys(1));
    }

    #[Test]
    public function shouldGetDeployKey(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'another-key'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deploy_keys/2')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->deployKey(1, 2));
    }

    #[Test]
    public function shouldAddKey(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'new-key', 'can_push' => false];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/deploy_keys', ['title' => 'new-key', 'key' => '...', 'can_push' => false])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->addDeployKey(1, 'new-key', '...'));
    }

    #[Test]
    public function shouldAddKeyWithPushOption(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'new-key', 'can_push' => true];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/deploy_keys', ['title' => 'new-key', 'key' => '...', 'can_push' => true])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->addDeployKey(1, 'new-key', '...', true));
    }

    #[Test]
    public function shouldUpdateDeployKey(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'new-title', 'key' => 'ssh-rsa AAAA...', 'can_push' => true];
        $parameters = ['can_push' => true, 'title' => 'new-title'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/deploy_keys/3', $parameters)
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->updateDeployKey(1, 3, $parameters));
    }

    #[Test]
    public function shouldDeleteDeployKey(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/deploy_keys/3')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->deleteDeployKey(1, 3));
    }

    #[Test]
    public function shoudEnableDeployKey(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/deploy_keys/3/enable')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->enableDeployKey(1, 3));
    }

    #[Test]
    public function shouldGetDeployTokens(): void
    {
        $expectedArray = [
            [
                'id' => 1,
                'name' => 'MyToken',
                'username' => 'gitlab+deploy-token-1',
                'expires_at' => '2020-02-14T00:00:00.000Z',
                'revoked' => false,
                'expired' => false,
                'scopes' => [
                    'read_repository',
                    'read_registry',
                ],
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deploy_tokens')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->deployTokens(1));
    }

    #[Test]
    public function shouldGetActiveDeployTokens(): void
    {
        $expectedArray = [
            [
                'id' => 1,
                'name' => 'MyToken',
                'username' => 'gitlab+deploy-token-1',
                'expires_at' => '2020-02-14T00:00:00.000Z',
                'revoked' => false,
                'expired' => true,
                'scopes' => [
                    'read_repository',
                    'read_registry',
                ],
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deploy_tokens', ['active' => true])
            ->willReturn([]);

        $this->assertEquals([], $api->deployTokens(1, true));
    }

    #[Test]
    public function shouldGetInactiveDeployTokens(): void
    {
        $expectedArray = [
            [
                'id' => 1,
                'name' => 'MyToken',
                'username' => 'gitlab+deploy-token-1',
                'expires_at' => '2020-02-14T00:00:00.000Z',
                'revoked' => false,
                'expired' => true,
                'scopes' => [
                    'read_repository',
                    'read_registry',
                ],
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deploy_tokens', ['active' => false])
            ->willReturn([]);

        $this->assertEquals([], $api->deployTokens(1, false));
    }

    #[Test]
    public function shouldCreateDeployToken(): void
    {
        $expectedArray = [
            'id' => 1,
            'name' => 'My Deploy Token',
            'username' => 'custom-user',
            'token' => 'jMRvtPNxrn3crTAGukpZ',
            'expires_at' => '2021-01-01T00:00:00.000Z',
            'revoked' => false,
            'expired' => false,
            'scopes' => [
                'read_repository',
                'read_registry',
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with(
                'projects/1/deploy_tokens',
                [
                    'name' => 'My Deploy Token',
                    'scopes' => [
                        'read_repository',
                        'read_registry',
                    ],
                    'expires_at' => (new DateTime('2021-01-01'))->format('c'),
                ]
            )
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->createDeployToken(1, [
            'name' => 'My Deploy Token',
            'scopes' => [
                'read_repository',
                'read_registry',
            ],
            'expires_at' => new DateTime('2021-01-01'),
        ]));
    }

    #[Test]
    public function shouldDeleteDeployToken(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/deploy_tokens/2')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->deleteDeployToken(1, 2));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->events(1));
    }

    #[Test]
    public function shouldGetEventsWithDateTimeParams(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An event'],
            ['id' => 2, 'title' => 'Another event'],
        ];

        $after = new DateTime('2018-01-01 00:00:00');
        $before = new DateTime('2018-01-31 00:00:00');

        $expectedWithArray = [
            'after' => $after->format('Y-m-d'),
            'before' => $before->format('Y-m-d'),
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/events', $expectedWithArray)
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->events(1, ['after' => $after, 'before' => $before]));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->events(1, ['page' => 2, 'per_page' => 15]));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->labels(1));
    }

    #[Test]
    public function shouldAddLabel(): void
    {
        $expectedArray = ['name' => 'bug', 'color' => '#000000'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/labels', ['name' => 'wont-fix', 'color' => '#ffffff'])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->addLabel(1, ['name' => 'wont-fix', 'color' => '#ffffff']));
    }

    #[Test]
    public function shouldUpdateLabel(): void
    {
        $expectedArray = ['name' => 'bug', 'color' => '#00ffff'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/labels/123', ['new_name' => 'big-bug', 'color' => '#00ffff'])
            ->willReturn($expectedArray);

        $this->assertEquals(
            $expectedArray,
            $api->updateLabel(1, 123, ['new_name' => 'big-bug', 'color' => '#00ffff'])
        );
    }

    #[Test]
    public function shouldRemoveLabel(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/labels/456', [])
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeLabel(1, 456));
    }

    #[Test]
    public function shouldGetLanguages(): void
    {
        $expectedArray = ['php' => 100];
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->languages(1));
    }

    #[Test]
    public function shouldForkWithNamespace(): void
    {
        $expectedArray = [
            'namespace' => 'new_namespace',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/fork', $expectedArray)
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->fork(1, [
            'namespace' => 'new_namespace',
        ]));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->fork(1, [
            'namespace' => 'new_namespace',
            'path' => 'new_path',
        ]));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->fork(1, [
            'namespace' => 'new_namespace',
            'path' => 'new_path',
            'name' => 'new_name',
        ]));
    }

    #[Test]
    public function shouldCreateForkRelation(): void
    {
        $expectedArray = ['project_id' => 1, 'forked_id' => 2];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/fork/2')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->createForkRelation(1, 2));
    }

    #[Test]
    public function shouldRemoveForkRelation(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/2/fork')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeForkRelation(2));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->forks(1));
    }

    #[Test]
    public function shouldGetForksUsingParameters(): void
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
        $updated_after = new DateTime('2018-01-01 00:00:00');
        $updated_before = new DateTime('2018-01-31 00:00:00');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/forks', [
                'archived' => 'false',
                'visibility' => 'public',
                'order_by' => 'id',
                'sort' => 'asc',
                'search' => 'term',
                'simple' => 'true',
                'owned' => 'false',
                'membership' => 'false',
                'starred' => 'false',
                'statistics' => 'false',
                'with_issues_enabled' => 'false',
                'with_merge_requests_enabled' => 'false',
                'min_access_level' => 30,
                'updated_after' => $updated_after->format('c'),
                'updated_before' => $updated_before->format('c'),
                'with_custom_attributes' => 'true',
            ])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->forks(1, [
            'archived' => false,
            'visibility' => 'public',
            'order_by' => 'id',
            'sort' => 'asc',
            'search' => 'term',
            'simple' => true,
            'owned' => false,
            'membership' => false,
            'starred' => false,
            'statistics' => false,
            'with_issues_enabled' => false,
            'with_merge_requests_enabled' => false,
            'min_access_level' => 30,
            'updated_after' => $updated_after,
            'updated_before' => $updated_before,
            'with_custom_attributes' => true,
        ]));
    }

    #[Test]
    public function shouldSetService(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/services/hipchat', ['param' => 'value'])
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->setService(1, 'hipchat', ['param' => 'value']));
    }

    #[Test]
    public function shouldRemoveService(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/services/hipchat')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeService(1, 'hipchat'));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->variables(1));
    }

    #[Test]
    public function shouldGetVariable(): void
    {
        $expectedArray = ['key' => 'ftp_username', 'value' => 'ftp'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/variables/ftp_username')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->variable(1, 'ftp_username'));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->addVariable(1, $expectedKey, $expectedValue));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals(
            $expectedArray,
            $api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging')
        );
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals(
            $expectedArray,
            $api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true, 'staging')
        );
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals(
            $expectedArray,
            $api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging', ['variable_type' => 'file'])
        );
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals(
            $expectedArray,
            $api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging', ['environment_scope' => 'production'])
        );
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->updateVariable(1, $expectedKey, $expectedValue));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals(
            $expectedArray,
            $api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging')
        );
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals(
            $expectedArray,
            $api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true, 'staging')
        );
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals(
            $expectedArray,
            $api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging', ['variable_type' => 'file'])
        );
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals(
            $expectedArray,
            $api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging', ['environment_scope' => 'production'])
        );
    }

    #[Test]
    public function shouldRemoveVariable(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/variables/ftp_password')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeVariable(1, 'ftp_password'));
    }

    protected function getMultipleProjectsRequestMock($path, $expectedArray = [], $expectedParameters = []): MockObject
    {
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with($path, $expectedParameters)
            ->willReturn($expectedArray);

        return $api;
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->deployments(1));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->deployments(1, ['page' => 2, 'per_page' => 15]));
    }

    #[Test]
    public function shouldGetDeploymentsSorted(): void
    {
        $expectedArray = [
            ['id' => 1, 'sha' => '0000001'],
            ['id' => 2, 'sha' => '0000002'],
            ['id' => 3, 'sha' => '0000003'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deployments', [
                'order_by' => 'id',
                'sort' => 'asc',
            ])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->deployments(1, ['order_by' => 'id', 'sort' => 'asc']));

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deployments', [
                'order_by' => 'id',
                'sort' => 'desc',
            ])
            ->willReturn(\array_reverse($expectedArray));

        $this->assertEquals(\array_reverse($expectedArray), $api->deployments(1, ['order_by' => 'id', 'sort' => 'desc']));
    }

    #[Test]
    public function shouldGetDeploymentsFiltered(): void
    {
        $expectedArray = [
            ['id' => 1, 'sha' => '0000001'],
            ['id' => 2, 'sha' => '0000002'],
            ['id' => 3, 'sha' => '0000003'],
        ];

        $time = new DateTime('now');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deployments', [
                'updated_after' => $time->format('c'),
            ])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->deployments(1, ['updated_after' => $time]));
    }

    protected function getMultipleProjectsData(): array
    {
        return [
            ['id' => 1, 'name' => 'A project'],
            ['id' => 2, 'name' => 'Another project'],
        ];
    }

    protected function getMultipleProjectsDataWithNamespace(): array
    {
        return [
            ['id' => 1, 'name' => 'A project', 'namespace' => ['id' => 4, 'name' => 'A namespace', 'path' => 'a_namespace']],
            ['id' => 2, 'name' => 'Another project', 'namespace' => ['id' => 5, 'name' => 'Another namespace', 'path' => 'another_namespace']],
        ];
    }

    public static function possibleAccessLevels(): array
    {
        return [
            [10],
            [20],
            [30],
            [40],
            [50],
        ];
    }

    public function getBadgeExpectedArray(): array
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

    #[Test]
    public function shouldGetBadges(): void
    {
        $expectedArray = $this->getBadgeExpectedArray();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/badges')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->badges(1));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->badge(1, 1));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals(
            $expectedArray,
            $api->addBadge(1, ['link_url' => $link_url, 'image_url' => $image_url])
        );
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->updateBadge(1, 2, ['image_url' => $image_url]));
    }

    #[Test]
    public function shouldRemoveBadge(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/badges/1')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeBadge(1, 1));
    }

    #[Test]
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
            ->willReturn($expectedArray);
        $this->assertEquals($expectedArray, $api->addProtectedBranch(1, ['name' => 'master', 'push_access_level' => 0, 'merge_access_level' => 30]));
    }

    #[Test]
    public function shouldRemoveProtectedBranch(): void
    {
        $expectedBool = true;
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with(
                'projects/1/protected_branches/test-branch'
            )
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->deleteProtectedBranch(1, 'test-branch'));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->approvalsConfiguration(1));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->approvalsRules(1));
    }

    #[Test]
    public function shoudCreateApprovalsRule(): void
    {
        $expectedArray = [
            [
                'id' => 1,
                'name' => 'All Members',
                'rule_type' => 'any_approver',
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/approval_rules/', ['name' => 'All Members', 'rule_type' => 'any_approver'])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->createApprovalsRule(1, [
            'name' => 'All Members',
            'rule_type' => 'any_approver',
        ]));
    }

    #[Test]
    public function shoudUpdateApprovalsRule(): void
    {
        $expectedArray = [
            [
                'name' => 'Updated Name',
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/approval_rules/1', ['name' => 'Updated Name'])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->updateApprovalsRule(1, 1, [
            'name' => 'Updated Name',
        ]));
    }

    #[Test]
    public function shoudDeleteApprovalsRule(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/approval_rules/1')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->deleteApprovalsRule(1, 1));
    }

    #[Test]
    public function shouldDeleteAllMergedBranches(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/repository/merged_branches')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->deleteAllMergedBranches(1));
    }

    #[Test]
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->protectedBranches(1));
    }

    #[Test]
    public function shouldGetProjectAccessTokens(): void
    {
        $expectedArray = [
            [
                'user_id' => 141,
                'scopes' => [
                    'api',
                ],
                'name' => 'token',
                'expires_at' => '2021-01-31',
                'id' => 42,
                'active' => true,
                'created_at' => '2021-01-20T22:11:48.151Z',
                'revoked' => false,
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/access_tokens', [])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->projectAccessTokens(1));
    }

    #[Test]
    public function shouldGetProjectAccessTokensWithFilters(): void
    {
        $expectedArray = [
            [
                'user_id' => 141,
                'scopes' => [
                    'api',
                ],
                'name' => 'token',
                'expires_at' => '2021-01-31',
                'id' => 42,
                'active' => true,
                'created_at' => '2021-01-20T22:11:48.151Z',
                'revoked' => false,
            ],
        ];
        $createdAfter = new DateTime('2025-01-01 00:00:00');
        $createdBefore = new DateTime('2025-02-01 00:00:00');
        $expiresAfter = new DateTime('2025-03-01 00:00:00');
        $expiresBefore = new DateTime('2025-04-01 00:00:00');
        $lastUsedAfter = new DateTime('2025-05-01 00:00:00');
        $lastUsedBefore = new DateTime('2025-06-01 00:00:00');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/access_tokens', [
                'search' => 'token',
                'state' => 'active',
                'revoked' => 'false',
                'created_after' => $createdAfter->format('c'),
                'created_before' => $createdBefore->format('c'),
                'expires_after' => $expiresAfter->format('Y-m-d'),
                'expires_before' => $expiresBefore->format('Y-m-d'),
                'last_used_after' => $lastUsedAfter->format('c'),
                'last_used_before' => $lastUsedBefore->format('c'),
                'sort' => 'name_desc',
            ])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->projectAccessTokens(1, [
            'search' => 'token',
            'state' => 'active',
            'revoked' => false,
            'created_after' => $createdAfter,
            'created_before' => $createdBefore,
            'expires_after' => $expiresAfter,
            'expires_before' => $expiresBefore,
            'last_used_after' => $lastUsedAfter,
            'last_used_before' => $lastUsedBefore,
            'sort' => 'name_desc',
        ]));
    }

    #[Test]
    public function shouldGetProjectAccessToken(): void
    {
        $expectedArray = [
            'user_id' => 141,
            'scopes' => [
                'api',
            ],
            'name' => 'token',
            'expires_at' => '2021-01-31',
            'id' => 42,
            'active' => true,
            'created_at' => '2021-01-20T22:11:48.151Z',
            'revoked' => false,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/access_tokens/42')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->projectAccessToken(1, 42));
    }

    #[Test]
    public function shouldCreateProjectAccessToken(): void
    {
        $expectedArray = [
            'scopes' => [
                'api',
                'read_repository',
            ],
            'active' => true,
            'name' => 'test',
            'revoked' => false,
            'created_at' => '2021-01-21T19:35:37.921Z',
            'user_id' => 166,
            'id' => 58,
            'expires_at' => '2021-01-31',
            'token' => 'D4y...Wzr',
        ];
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with(
                'projects/1/access_tokens',
                [
                    'name' => 'test_token',
                    'scopes' => [
                        'api',
                        'read_repository',
                    ],
                    'access_level' => 30,
                    'expires_at' => '2021-01-31',
                ]
            )
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->createProjectAccessToken(1, [
            'name' => 'test_token',
            'scopes' => [
                'api',
                'read_repository',
            ],
            'access_level' => 30,
            'expires_at' => new DateTime('2021-01-31'),
        ]));
    }

    #[Test]
    public function shouldRotateProjectAccessToken(): void
    {
        $expectedArray = [
            'scopes' => [
                'api',
                'read_repository',
            ],
            'active' => true,
            'name' => 'test',
            'revoked' => false,
            'created_at' => '2021-01-21T19:35:37.921Z',
            'user_id' => 166,
            'id' => 58,
            'expires_at' => '2021-01-31',
            'token' => 'D4y...Wzr',
        ];
        $expiresAt = new DateTime('2021-01-31');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/access_tokens/2/rotate', ['expires_at' => '2021-01-31'])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->rotateProjectAccessToken(1, 2, ['expires_at' => $expiresAt]));
    }

    #[Test]
    public function shouldRotateCurrentProjectAccessToken(): void
    {
        $expectedArray = [
            'scopes' => [
                'api',
                'read_repository',
            ],
            'active' => true,
            'name' => 'test',
            'revoked' => false,
            'created_at' => '2021-01-21T19:35:37.921Z',
            'user_id' => 166,
            'id' => 58,
            'expires_at' => '2021-01-31',
            'token' => 'D4y...Wzr',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/access_tokens/self/rotate', [])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->rotateProjectAccessToken(1, 'self'));
    }

    #[Test]
    public function shouldDeleteProjectAccessToken(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/access_tokens/2')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->deleteProjectAccessToken(1, 2));
    }

    #[Test]
    public function shouldGetJobTokenScope(): void
    {
        $expectedArray = ['inbound_enabled' => true, 'outbound_enabled' => false];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/job_token_scope')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->jobTokenScope(1));
    }

    #[Test]
    public function shouldUpdateJobTokenScope(): void
    {
        $expectedString = '';

        $api = $this->getApiMock(['patch']);
        $api->expects($this->once())
            ->method('patch')
            ->with('projects/1/job_token_scope', ['enabled' => false])
            ->willReturn($expectedString);

        $this->assertEquals($expectedString, $api->updateJobTokenScope(1, false));
    }

    #[Test]
    public function shouldGetJobTokenScopeAllowlistProjects(): void
    {
        $expectedArray = [[
            'id' => 4,
            'name' => 'Diaspora Client',
            'web_url' => 'https://gitlab.example.com/diaspora/diaspora-client',
        ]];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/job_token_scope/allowlist')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->jobTokenScopeAllowlistProjects(1));
    }

    #[Test]
    public function shouldAddJobTokenScopeAllowlistProject(): void
    {
        $expectedArray = [
            'source_project_id' => 1,
            'target_project_id' => 42,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/job_token_scope/allowlist', ['target_project_id' => 42])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->addJobTokenScopeAllowlistProject(1, 42));
    }

    #[Test]
    public function shouldRemoveJobTokenScopeAllowlistProject(): void
    {
        $expectedString = '';

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/job_token_scope/allowlist/42')
            ->willReturn($expectedString);

        $this->assertEquals($expectedString, $api->removeJobTokenScopeAllowlistProject(1, 42));
    }

    #[Test]
    public function shouldGetJobTokenScopeAllowlistGroups(): void
    {
        $expectedArray = [[
            'id' => 4,
            'name' => 'namegroup',
            'web_url' => 'https://gitlab.example.com/groups/diaspora/diaspora-group',
        ]];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/job_token_scope/groups_allowlist')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->jobTokenScopeAllowlistGroups(1));
    }

    #[Test]
    public function shouldAddJobTokenScopeAllowlistGroup(): void
    {
        $expectedArray = [
            'source_project_id' => 1,
            'target_group_id' => 42,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/job_token_scope/groups_allowlist', ['target_group_id' => 42])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->addJobTokenScopeAllowlistGroup(1, 42));
    }

    #[Test]
    public function shouldRemoveJobTokenScopeAllowlistGroup(): void
    {
        $expectedString = '';

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/job_token_scope/groups_allowlist/42')
            ->willReturn($expectedString);

        $this->assertEquals($expectedString, $api->removeJobTokenScopeAllowlistGroup(1, 42));
    }

    #[Test]
    public function shouldGetProjectRegistryRepositories(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A registry'],
            ['id' => 2, 'name' => 'Another registry'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/123/registry/repositories', [])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->registryRepositories(123));
    }

    #[Test]
    public function shouldGetProjectRegistryRepositoriesWithTags(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A registry', 'tags' => ['1.0', '1.1'], 'tags_count' => 2],
            ['id' => 2, 'name' => 'Another registry', 'tags' => ['2.0', '2.1'], 'tags_count' => 2],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/123/registry/repositories', ['tags' => 'true', 'tags_count' => 'true'])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->registryRepositories(123, ['tags' => true, 'tags_count' => true]));
    }

    #[Test]
    public function shouldUploadAvatar(): void
    {
        $emptyPNGContents = 'iVBORw0KGgoAAAANSUhEUgAAAQAAAAEACAYAAABccqhmAAACYElEQVR42u3UMQEAAAjDMFCO9GEAByQSerQrmQJeagMAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwADAAAwADAAwAMAAAAMADAAwAMAAAAMADAAwAMAAAAMADAAwAMAAAAMADAAwAMAAAAMADAAwAMAAAAMADAAwAMAAAAMADAAwAMAAAAMADAAwAMAAAAMAAzAAMADAAAADAAwAMADAAAADAAwAMADAAAADAAwAMADAAAADAAwAMADAAAADAAwAMADAAAADAAwAMADAAAADAAwAMADAAAADAAwAMADAAAADAAwADMAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAMAAZwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAwAAAAwAMADAAMADAAAADAAwAMADAAAADAAwAMADAAAADAAwAMADAAAADAAwAMADAAAADAAwAMADAAAADAAwAMADAAAADAAwAMADAAAADAAwAMADAAAADAAwAMAAwAMAAAAMADAAwAMAAAAMADAAwAMAAAAMADAAwAMAAAAMADAAwAMAAAAMADAAwAMAAAAMADAAwAMAAAAMADAAwAMAAAAMADAAwAOCybrx+H1CTHLYAAAAASUVORK5CYII=';
        $fileName = \uniqid().'.png';
        $expectedArray = ['id' => 1, 'name' => 'Project Name', 'avatar_url' => 'https://gitlab.example.com/uploads/-/system/project/avatar/1/'.$fileName];
        \file_put_contents($fileName, \base64_decode($emptyPNGContents));
        $this->assertFileExists($fileName);
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1', [], [], ['avatar' => $fileName])
            ->willReturn($expectedArray);
        $this->assertEquals($expectedArray, $api->uploadAvatar(1, $fileName));
        \unlink($fileName);
    }

    #[Test]
    public function shouldAddProtectedTag(): void
    {
        $expectedArray = [
            'name' => 'release-*',
            'create_access_level' => [
                ['access_level' => 40, 'access_level_description' => 'Maintainers'],
                ['group_id' => 123],
            ],
        ];
        $api = $this->getApiMock();
        $params = [
            'name' => 'release-*',
            'create_access_level' => 40,
            'allowed_to_create' => [['group_id' => 123]],
        ];
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/protected_tags', $params)
            ->willReturn($expectedArray);
        $this->assertEquals($expectedArray, $api->addProtectedTag(1, $params));
    }

    #[Test]
    public function shouldRemoveProtectedTag(): void
    {
        $expectedBool = true;
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with(
                'projects/1/protected_tags/release-%2A'
            )
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->deleteProtectedTag(1, 'release-*'));
    }

    #[Test]
    public function shouldGetRemoteMirrors(): void
    {
        $expectedArray = [
            ['id' => 101486, 'url' => 'https://*****:*****@gitlab.com/gitlab-org/security/gitlab.git'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/remote_mirrors')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->remoteMirrors(1));
    }

    #[Test]
    public function shouldGetRemoteMirror(): void
    {
        $expectedArray = ['id' => 101486, 'url' => 'https://*****:*****@gitlab.com/gitlab-org/security/gitlab.git'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/remote_mirrors/101486')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->remoteMirror(1, 101486));
    }

    #[Test]
    public function shouldGetRemoteMirrorPublicKey(): void
    {
        $expectedArray = ['public_key' => 'ssh-rsa AAAAB3NzaC1yc2EA...'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/remote_mirrors/101486/public_key')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->remoteMirrorPublicKey(1, 101486));
    }

    protected function getApiClass(): string
    {
        return Projects::class;
    }

    #[Test]
    public function shouldSearchGroups(): void
    {
        $expectedArray = [
            ['id' => 6, 'title' => 'Issue 6 bla'],
            ['id' => 7, 'title' => 'Issue 7 bla'],
            ['id' => 8, 'title' => 'Issue 8 bla'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/123/search', [
                'scope' => 'issues',
                'confidential' => 'false',
                'search' => 'bla',
                'order_by' => 'created_at',
                'sort' => 'desc',
            ])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->search(123, [
            'scope' => 'issues',
            'confidential' => false,
            'search' => 'bla',
            'order_by' => 'created_at',
            'sort' => 'desc',
        ]));
    }
}
