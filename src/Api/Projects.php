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

namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Projects extends AbstractApi
{
    /**
     * @param array $parameters {
     *
     *     @var bool               $archived                    limit by archived status
     *     @var string             $visibility                  limit by visibility public, internal, or private
     *     @var string             $order_by                    Return projects ordered by id, name, path, created_at, updated_at,
     *                                                          last_activity_at, repository_size, storage_size, packages_size or
     *                                                          wiki_size fields (default is created_at)
     *     @var string             $sort                        Return projects sorted in asc or desc order (default is desc)
     *     @var string             $search                      return list of projects matching the search criteria
     *     @var bool               $search_namespaces           Include ancestor namespaces when matching search criteria
     *     @var bool               $simple                      return only the ID, URL, name, and path of each project
     *     @var bool               $owned                       limit by projects owned by the current user
     *     @var bool               $membership                  limit by projects that the current user is a member of
     *     @var bool               $starred                     limit by projects starred by the current user
     *     @var bool               $statistics                  include project statistics
     *     @var bool               $with_issues_enabled         limit by enabled issues feature
     *     @var bool               $with_merge_requests_enabled limit by enabled merge requests feature
     *     @var int                $min_access_level            Limit by current user minimal access level
     *     @var int                $id_after                    Limit by project id's greater than the specified id
     *     @var int                $id_before                   Limit by project id's less than the specified id
     *     @var \DateTimeInterface $last_activity_after         Limit by last_activity after specified time
     *     @var \DateTimeInterface $last_activity_before        Limit by last_activity before specified time
     *     @var bool               $repository_checksum_failed  Limit by failed repository checksum calculation
     *     @var string             $repository_storage          Limit by repository storage type
     *     @var bool               $wiki_checksum_failed        Limit by failed wiki checksum calculation
     *     @var bool               $with_custom_attributes      Include custom attributes in response
     *     @var string             $with_programming_language   Limit by programming language
     *     @var string             $topic                       Limit by topic
     * }
     *
     * @throws UndefinedOptionsException If an option name is undefined
     * @throws InvalidOptionsException   If an option doesn't fulfill the specified validation rules
     */
    public function all(array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('c');
        };
        $resolver->setDefined('archived')
            ->setAllowedTypes('archived', 'bool')
            ->setNormalizer('archived', $booleanNormalizer)
        ;
        $resolver->setDefined('visibility')
            ->setAllowedValues('visibility', ['public', 'internal', 'private'])
        ;
        $orderBy = [
            'id', 'name', 'path', 'created_at', 'updated_at', 'last_activity_at',
            'repository_size', 'storage_size', 'packages_size', 'wiki_size',
        ];
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', $orderBy)
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;
        $resolver->setDefined('search');
        $resolver->setDefined('search_namespaces')
            ->setAllowedTypes('search_namespaces', 'bool')
            ->setNormalizer('search_namespaces', $booleanNormalizer)
        ;
        $resolver->setDefined('simple')
            ->setAllowedTypes('simple', 'bool')
            ->setNormalizer('simple', $booleanNormalizer)
        ;
        $resolver->setDefined('owned')
            ->setAllowedTypes('owned', 'bool')
            ->setNormalizer('owned', $booleanNormalizer)
        ;
        $resolver->setDefined('membership')
            ->setAllowedTypes('membership', 'bool')
            ->setNormalizer('membership', $booleanNormalizer)
        ;
        $resolver->setDefined('starred')
            ->setAllowedTypes('starred', 'bool')
            ->setNormalizer('starred', $booleanNormalizer)
        ;
        $resolver->setDefined('statistics')
            ->setAllowedTypes('statistics', 'bool')
            ->setNormalizer('statistics', $booleanNormalizer)
        ;
        $resolver->setDefined('with_issues_enabled')
            ->setAllowedTypes('with_issues_enabled', 'bool')
            ->setNormalizer('with_issues_enabled', $booleanNormalizer)
        ;
        $resolver->setDefined('with_merge_requests_enabled')
            ->setAllowedTypes('with_merge_requests_enabled', 'bool')
            ->setNormalizer('with_merge_requests_enabled', $booleanNormalizer)
        ;
        $resolver->setDefined('min_access_level')
            ->setAllowedValues('min_access_level', [null, 10, 20, 30, 40, 50])
        ;
        $resolver->setDefined('id_after')
            ->setAllowedTypes('id_after', 'integer')
        ;
        $resolver->setDefined('id_before')
            ->setAllowedTypes('id_before', 'integer')
        ;
        $resolver->setDefined('last_activity_after')
            ->setAllowedTypes('last_activity_after', \DateTimeInterface::class)
            ->setNormalizer('last_activity_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('last_activity_before')
            ->setAllowedTypes('last_activity_before', \DateTimeInterface::class)
            ->setNormalizer('last_activity_before', $datetimeNormalizer)
        ;
        $resolver->setDefined('repository_checksum_failed')
            ->setAllowedTypes('repository_checksum_failed', 'bool')
            ->setNormalizer('repository_checksum_failed', $booleanNormalizer)
        ;
        $resolver->setDefined('repository_storage');
        $resolver->setDefined('wiki_checksum_failed')
            ->setAllowedTypes('wiki_checksum_failed', 'bool')
            ->setNormalizer('wiki_checksum_failed', $booleanNormalizer)
        ;
        $resolver->setDefined('with_custom_attributes')
            ->setAllowedTypes('with_custom_attributes', 'bool')
            ->setNormalizer('with_custom_attributes', $booleanNormalizer)
        ;
        $resolver->setDefined('with_programming_language');
        $resolver->setDefined('topic');

        return $this->get('projects', $resolver->resolve($parameters));
    }

    /**
     * @param array      $parameters {
     *
     *     @var bool   $statistics                    include project statistics
     *     @var bool   $with_custom_attributes        Include project custom attributes.
     * }
     */
    public function show(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): bool {
            return (bool) $value;
        };
        $resolver->setDefined('statistics')
            ->setAllowedTypes('statistics', 'bool')
            ->setNormalizer('statistics', $booleanNormalizer)
        ;
        $resolver->setDefined('with_custom_attributes')
            ->setAllowedTypes('with_custom_attributes', 'bool')
            ->setNormalizer('with_custom_attributes', $booleanNormalizer)
        ;

        return $this->get('projects/'.self::encodePath($project_id), $resolver->resolve($parameters));
    }

    public function create(string $name, array $parameters = []): mixed
    {
        $parameters['name'] = $name;

        return $this->post('projects', $parameters);
    }

    public function createForUser(int $user_id, string $name, array $parameters = []): mixed
    {
        $parameters['name'] = $name;

        return $this->post('projects/user/'.self::encodePath($user_id), $parameters);
    }

    public function update(int|string $project_id, array $parameters): mixed
    {
        return $this->put('projects/'.self::encodePath($project_id), $parameters);
    }

    /**
     * @param array $parameters {
     *
     *     @var string      $full_path           full path of project to use with permanently_remove
     *     @var bool|string $permanently_remove Immediately delete a project that is already marked for deletion.
     * }
     */
    public function remove(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('full_path')
            ->setAllowedTypes('full_path', 'string')
        ;
        $resolver->setDefined('permanently_remove')
            ->setAllowedTypes('permanently_remove', ['bool', 'string'])
        ;

        return $this->delete('projects/'.self::encodePath($project_id), $resolver->resolve($parameters));
    }

    public function restore(int|string $project_id): mixed
    {
        return $this->post('projects/'.self::encodePath($project_id).'/restore');
    }

    public function archive(int|string $project_id): mixed
    {
        return $this->post('projects/'.self::encodePath($project_id).'/archive');
    }

    public function unarchive(int|string $project_id): mixed
    {
        return $this->post('projects/'.self::encodePath($project_id).'/unarchive');
    }

    public function triggers(int|string $project_id): mixed
    {
        return $this->get('projects/'.self::encodePath($project_id).'/triggers');
    }

    public function trigger(int|string $project_id, int $trigger_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'triggers/'.self::encodePath($trigger_id)));
    }

    public function createTrigger(int|string $project_id, string $description): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'triggers'), [
            'description' => $description,
        ]);
    }

    public function removeTrigger(int|string $project_id, int $trigger_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'triggers/'.self::encodePath($trigger_id)));
    }

    public function triggerPipeline(int|string $project_id, string $ref, #[\SensitiveParameter] string $token, array $variables = []): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'trigger/pipeline'), [
            'ref' => $ref,
            'token' => $token,
            'variables' => $variables,
        ]);
    }

    public function disableRunner(int $project_id, int $runner_id): mixed
    {
        return $this->delete('projects/'.self::encodePath($project_id).'/runners/'.self::encodePath($runner_id));
    }

    public function enableRunner(int $project_id, int $runner_id): mixed
    {
        $parameters = [
            'runner_id' => $runner_id,
        ];

        return $this->post('projects/'.self::encodePath($project_id).'/runners', $parameters);
    }

    /**
     * @param array      $parameters {
     *
     *     @var string             $scope          the scope of pipelines, one of: running, pending, finished, branches, tags
     *     @var string             $status         the status of pipelines, one of: running, pending, success, failed, canceled, skipped
     *     @var string             $ref            the ref of pipelines
     *     @var string             $sha            the sha of pipelines
     *     @var bool               $yaml_errors    returns pipelines with invalid configurations
     *     @var string             $name           the name of the user who triggered pipelines
     *     @var string             $username       the username of the user who triggered pipelines
     *     @var \DateTimeInterface $updated_after  Return pipelines updated on or after the given date and time
     *     @var \DateTimeInterface $updated_before Return pipelines updated on or before the given date and time
     *     @var string             $order_by       order pipelines by id, status, ref, updated_at, or user_id (default: id)
     *     @var string             $sort           sort pipelines in asc or desc order (default: desc)
     *     @var string             $source         the source of the pipeline
     * }
     */
    public function pipelines(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('c');
        };

        $resolver->setDefined('scope')
            ->setAllowedValues('scope', ['running', 'pending', 'finished', 'branches', 'tags'])
        ;
        $resolver->setDefined('status')
            ->setAllowedValues('status', ['running', 'pending', 'success', 'failed', 'canceled', 'skipped'])
        ;
        $resolver->setDefined('ref');
        $resolver->setDefined('sha');
        $resolver->setDefined('yaml_errors')
            ->setAllowedTypes('yaml_errors', 'bool')
            ->setNormalizer('yaml_errors', $booleanNormalizer)
        ;
        $resolver->setDefined('name');
        $resolver->setDefined('username');
        $resolver->setDefined('updated_after')
            ->setAllowedTypes('updated_after', \DateTimeInterface::class)
            ->setNormalizer('updated_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('updated_before')
            ->setAllowedTypes('updated_before', \DateTimeInterface::class)
            ->setNormalizer('updated_before', $datetimeNormalizer)
        ;
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['id', 'status', 'ref', 'updated_at', 'user_id'])
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;
        $resolver->setDefined('source')
            ->setAllowedValues('source', ['push', 'web', 'trigger', 'schedule', 'api', 'external', 'pipeline', 'chat', 'webide', 'merge_request_event', 'external_pull_request_event', 'parent_pipeline', 'ondemand_dast_scan', 'ondemand_dast_validation'])
        ;

        return $this->get($this->getProjectPath($project_id, 'pipelines'), $resolver->resolve($parameters));
    }

    public function pipeline(int|string $project_id, int $pipeline_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'pipelines/'.self::encodePath($pipeline_id)));
    }

    /**
     * @param array $parameters {
     *
     *     @var string $ref branch or tag to check for the latest pipeline
     * }
     */
    public function latestPipeline(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('ref')
            ->setAllowedTypes('ref', 'string')
        ;

        return $this->get($this->getProjectPath($project_id, 'pipelines/latest'), $resolver->resolve($parameters));
    }

    public function pipelineJobs(int|string $project_id, int $pipeline_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'pipelines/'.self::encodePath($pipeline_id).'/jobs'));
    }

    public function pipelineVariables(int|string $project_id, int $pipeline_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'pipelines/'.self::encodePath($pipeline_id).'/variables'));
    }

    public function pipelineTestReport(int|string $project_id, int $pipeline_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'pipelines/'.self::encodePath($pipeline_id).'/test_report'));
    }

    public function pipelineTestReportSummary(int|string $project_id, int $pipeline_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'pipelines/'.self::encodePath($pipeline_id).'/test_report_summary'));
    }

    /**
     * @param array|null $variables  {
     *
     *     @var string $key            The name of the variable
     *     @var mixed $value           The value of the variable
     *     @var string $variable_type  env_var (default) or file
     * }
     *
     * @param array $parameters {
     *
     *     @var array $inputs Inputs to use when creating the pipeline.
     * }
     */
    public function createPipeline(int|string $project_id, string $commit_ref, ?array $variables = null, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('inputs')
            ->setAllowedTypes('inputs', 'array')
        ;

        $parameters = $resolver->resolve($parameters);

        if (null !== $variables) {
            $parameters['variables'] = $variables;
        }

        return $this->post($this->getProjectPath($project_id, 'pipeline'), $parameters, [], [], [
            'ref' => $commit_ref,
        ]);
    }

    public function retryPipeline(int|string $project_id, int $pipeline_id): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'pipelines/'.self::encodePath($pipeline_id)).'/retry');
    }

    public function cancelPipeline(int|string $project_id, int $pipeline_id): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'pipelines/'.self::encodePath($pipeline_id)).'/cancel');
    }

    public function deletePipeline(int|string $project_id, int $pipeline_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'pipelines/'.self::encodePath($pipeline_id)));
    }

    public function allMembers(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('query');
        $resolver->setDefined('user_ids')
            ->setAllowedTypes('user_ids', 'array')
            ->setAllowedValues('user_ids', function (array $value) {
                return \count($value) === \count(\array_filter($value, 'is_int'));
            })
        ;

        return $this->get('projects/'.self::encodePath($project_id).'/members/all', $resolver->resolve($parameters));
    }

    /**
     * @param array      $parameters {
     *
     *     @var string $query           The query you want to search members for.
     * }
     */
    public function members(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        $resolver->setDefined('query')
            ->setAllowedTypes('query', 'string')
        ;
        $resolver->setDefined('user_ids')
            ->setAllowedTypes('user_ids', 'array')
            ->setAllowedValues('user_ids', function (array $value) {
                return \count($value) === \count(\array_filter($value, 'is_int'));
            })
        ;

        return $this->get($this->getProjectPath($project_id, 'members'), $resolver->resolve($parameters));
    }

    public function member(int|string $project_id, int $user_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'members/'.self::encodePath($user_id)));
    }

    public function allMember(int|string $project_id, int $user_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'members/all/'.self::encodePath($user_id)));
    }

    public function addMember(int|string $project_id, int $user_id, int $access_level, ?string $expires_at = null): mixed
    {
        $params = [
            'user_id' => $user_id,
            'access_level' => $access_level,
        ];
        if (null !== $expires_at) {
            $params['expires_at'] = $expires_at;
        }

        return $this->post($this->getProjectPath($project_id, 'members'), $params);
    }

    public function saveMember(int|string $project_id, int $user_id, int $access_level, ?string $expires_at = null): mixed
    {
        $params = [
            'access_level' => $access_level,
        ];
        if (null !== $expires_at) {
            $params['expires_at'] = $expires_at;
        }

        return $this->put($this->getProjectPath($project_id, 'members/'.self::encodePath($user_id)), $params);
    }

    public function removeMember(int|string $project_id, int $user_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'members/'.self::encodePath($user_id)));
    }

    public function hooks(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'hooks'), $resolver->resolve($parameters));
    }

    public function hook(int|string $project_id, int $hook_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'hooks/'.self::encodePath($hook_id)));
    }

    /**
     * Get project users.
     *
     * See https://docs.gitlab.com/ee/api/projects.html#get-project-users for more info.
     */
    public function users(int|string $project_id, array $parameters = []): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'users'), $parameters);
    }

    /**
     * Get project issues.
     *
     * See https://docs.gitlab.com/ee/api/issues.html#list-project-issues for more info.
     */
    public function issues(int|string $project_id, array $parameters = []): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'issues'), $parameters);
    }

    /**
     * Get projects board list.
     *
     * See https://docs.gitlab.com/ee/api/boards.html for more info.
     */
    public function boards(int|string $project_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'boards'));
    }

    /**
     * @param array      $parameters {
     *
     *     @var string $state               Return opened, upcoming, current (previously started), closed, or all iterations.
     *                                      Filtering by started state is deprecated starting with 14.1, please use current instead.
     *     @var string $search              return only iterations with a title matching the provided string
     *     @var bool   $include_ancestors   Include iterations from parent group and its ancestors. Defaults to true.
     * }
     */
    public function iterations(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('state')
            ->setAllowedValues('state', ['opened', 'upcoming', 'current', 'current (previously started)', 'closed', 'all'])
        ;
        $resolver->setDefined('include_ancestors')
            ->setAllowedTypes('include_ancestors', 'bool')
            ->setNormalizer('include_ancestors', $booleanNormalizer)
            ->setDefault('include_ancestors', true)
        ;

        return $this->get('projects/'.self::encodePath($project_id).'/iterations', $resolver->resolve($parameters));
    }

    /**
     * Gets a list of all discussion items for a single commit.
     *
     * Example:
     * - https://gitlab.com/gitlab-org/gitlab/-/commit/695c29abcf7dc2eabde8d59869abcea0923ce8fa#note_334686748
     * - https://gitlab.com/api/v4/projects/gitlab-org%2Fgitlab/repository/commits/695c29abcf7dc2eabde8d59869abcea0923ce8fa/discussions
     *
     * @see https://docs.gitlab.com/ee/api/discussions.html#list-project-commit-discussion-items
     */
    public function getRepositoryCommitDiscussions(int|string $project_id, string $commit_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/'.self::encodePath($commit_id)).'/discussions');
    }

    public function addHook(int|string $project_id, string $url, array $parameters = []): mixed
    {
        if (0 === \count($parameters)) {
            $parameters = ['push_events' => true];
        }

        $parameters['url'] = $url;

        return $this->post($this->getProjectPath($project_id, 'hooks'), $parameters);
    }

    public function updateHook(int|string $project_id, int $hook_id, array $parameters): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'hooks/'.self::encodePath($hook_id)), $parameters);
    }

    public function removeHook(int|string $project_id, int $hook_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'hooks/'.self::encodePath($hook_id)));
    }

    public function transfer(int|string $project_id, mixed $namespace): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'transfer'), ['namespace' => $namespace]);
    }

    public function deployKeys(int|string $project_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'deploy_keys'));
    }

    public function deployKey(int|string $project_id, int $key_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'deploy_keys/'.self::encodePath($key_id)));
    }

    public function addDeployKey(int|string $project_id, string $title, string $key, bool $canPush = false): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'deploy_keys'), [
            'title' => $title,
            'key' => $key,
            'can_push' => $canPush,
        ]);
    }

    /**
     * @param array $parameters {
     *
     *     @var bool   $can_push can deploy key push to the project's repository
     *     @var string $title    new deploy key's title
     * }
     */
    public function updateDeployKey(int|string $project_id, int $key_id, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('can_push')
            ->setAllowedTypes('can_push', 'bool')
        ;
        $resolver->setDefined('title')
            ->setAllowedTypes('title', 'string')
        ;

        return $this->put($this->getProjectPath($project_id, 'deploy_keys/'.self::encodePath($key_id)), $resolver->resolve($parameters));
    }

    public function deleteDeployKey(int|string $project_id, int $key_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'deploy_keys/'.self::encodePath($key_id)));
    }

    public function enableDeployKey(int|string $project_id, int $key_id): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'deploy_keys/'.self::encodePath($key_id).'/enable'));
    }

    public function deployTokens(int|string $project_id, ?bool $active = null): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'deploy_tokens'), (null !== $active) ? ['active' => $active] : []);
    }

    /**
     * @param array      $parameters {
     *
     *     @var string $name                    the name of the deploy token
     *     @var \DateTimeInterface $expires_at  expiration date for the deploy token, does not expire if no value is provided
     *     @var string $username                the username for the deploy token
     *     @var array  $scopes                  the scopes, one or many of: read_repository, read_registry, write_registry, read_package_registry, write_package_registry
     * }
     */
    public function createDeployToken(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('c');
        };

        $resolver->define('name')
            ->required()
        ;

        $resolver->define('scopes')
            ->required()
            ->allowedTypes('array')
            ->allowedValues(function ($scopes) {
                $allowed = ['read_repository', 'read_registry', 'write_registry', 'read_package_registry', 'write_package_registry'];
                foreach ($scopes as $scope) {
                    if (!\in_array($scope, $allowed, true)) {
                        return false;
                    }
                }

                return true;
            })
        ;
        $resolver->setDefined('username')
            ->setAllowedTypes('username', 'string')
        ;

        $resolver->setDefined('expires_at')
            ->setAllowedTypes('expires_at', \DateTimeInterface::class)
            ->setNormalizer('expires_at', $datetimeNormalizer)
        ;

        return $this->post($this->getProjectPath($project_id, 'deploy_tokens'), $resolver->resolve($parameters));
    }

    public function deleteDeployToken(int|string $project_id, int $token_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'deploy_tokens/'.self::encodePath($token_id)));
    }

    public function pushRule(int|string $project_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'push_rule'));
    }

    /**
     * @param array $parameters {
     *
     *     @var string $author_email_regex            all commit author emails must match this regular expression
     *     @var string $branch_name_regex             all branch names must match this regular expression
     *     @var bool   $commit_committer_check        only allow commits when the committer email is one of the user's verified emails
     *     @var bool   $commit_committer_name_check   only allow commits when the author name matches the user's GitLab account name
     *     @var string $commit_message_negative_regex reject commit messages matching this regular expression
     *     @var string $commit_message_regex          require commit messages to match this regular expression
     *     @var bool   $deny_delete_tag               deny deleting tags
     *     @var string $file_name_regex               reject committed filenames matching this regular expression
     *     @var int    $max_file_size                 maximum file size in MB
     *     @var bool   $member_check                  restrict commit authors by email to existing GitLab users
     *     @var bool   $prevent_secrets               reject files likely to contain secrets
     *     @var bool   $reject_non_dco_commits        reject commits that are not DCO certified
     *     @var bool   $reject_unsigned_commits       reject unsigned commits
     * }
     *
     * @throws UndefinedOptionsException If an option name is undefined
     * @throws InvalidOptionsException   If an option doesn't fulfill the specified validation rules
     */
    public function createPushRule(int|string $project_id, array $parameters = []): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'push_rule'), self::createPushRuleOptionsResolver()->resolve($parameters));
    }

    /**
     * @param array $parameters {
     *
     *     @var string $author_email_regex            all commit author emails must match this regular expression
     *     @var string $branch_name_regex             all branch names must match this regular expression
     *     @var bool   $commit_committer_check        only allow commits when the committer email is one of the user's verified emails
     *     @var bool   $commit_committer_name_check   only allow commits when the author name matches the user's GitLab account name
     *     @var string $commit_message_negative_regex reject commit messages matching this regular expression
     *     @var string $commit_message_regex          require commit messages to match this regular expression
     *     @var bool   $deny_delete_tag               deny deleting tags
     *     @var string $file_name_regex               reject committed filenames matching this regular expression
     *     @var int    $max_file_size                 maximum file size in MB
     *     @var bool   $member_check                  restrict commit authors by email to existing GitLab users
     *     @var bool   $prevent_secrets               reject files likely to contain secrets
     *     @var bool   $reject_non_dco_commits        reject commits that are not DCO certified
     *     @var bool   $reject_unsigned_commits       reject unsigned commits
     * }
     *
     * @throws UndefinedOptionsException If an option name is undefined
     * @throws InvalidOptionsException   If an option doesn't fulfill the specified validation rules
     */
    public function updatePushRule(int|string $project_id, array $parameters = []): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'push_rule'), self::createPushRuleOptionsResolver()->resolve($parameters));
    }

    public function deletePushRule(int|string $project_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'push_rule'));
    }

    /**
     * @param array      $parameters {
     *
     *     @var string             $action      include only events of a particular action type
     *     @var string             $target_type include only events of a particular target type
     *     @var \DateTimeInterface $before      include only events created before a particular date
     *     @var \DateTimeInterface $after       include only events created after a particular date
     *     @var string             $sort        Sort events in asc or desc order by created_at (default is desc)
     * }
     */
    public function events(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('Y-m-d');
        };

        $resolver->setDefined('action')
            ->setAllowedValues('action', ['created', 'updated', 'closed', 'reopened', 'pushed', 'commented', 'merged', 'joined', 'left', 'destroyed', 'expired', 'approved'])
        ;
        $resolver->setDefined('target_type')
            ->setAllowedValues('target_type', ['issue', 'milestone', 'merge_request', 'note', 'project', 'snippet', 'user'])
        ;
        $resolver->setDefined('before')
            ->setAllowedTypes('before', \DateTimeInterface::class)
            ->setNormalizer('before', $datetimeNormalizer);
        $resolver->setDefined('after')
            ->setAllowedTypes('after', \DateTimeInterface::class)
            ->setNormalizer('after', $datetimeNormalizer)
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;

        return $this->get($this->getProjectPath($project_id, 'events'), $resolver->resolve($parameters));
    }

    /**
     * @param array      $parameters {
     *
     *     @var bool     $with_counts               Whether or not to include issue and merge request counts. Defaults to false.
     *     @var bool     $include_ancestor_groups   Include ancestor groups. Defaults to true.
     *     @var string   $search                    Keyword to filter labels by.
     * }
     */
    public function labels(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        $resolver->setDefined('with_counts')
            ->setAllowedTypes('with_counts', 'bool');

        $resolver->setDefined('include_ancestor_groups')
            ->setAllowedTypes('include_ancestor_groups', 'bool');

        $resolver->setDefined('search')
            ->setAllowedTypes('search', 'string');

        return $this->get($this->getProjectPath($project_id, 'labels'), $resolver->resolve($parameters));
    }

    public function addLabel(int|string $project_id, array $parameters): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'labels'), $parameters);
    }

    public function updateLabel(int|string $project_id, int $label_id, array $parameters): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'labels/'.self::encodePath($label_id)), $parameters);
    }

    public function removeLabel(int|string $project_id, int $label_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'labels/'.self::encodePath($label_id)));
    }

    /**
     * Get languages used in a project with percentage value.
     */
    public function languages(int|string $project_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'languages'));
    }

    /**
     * @param array      $parameters {
     *
     *     @var bool               $archived                    Limit by archived status
     *     @var bool               $membership                  Limit by projects that the current user is a member of
     *     @var int                $min_access_level            Limit by current user minimal access level
     *     @var string             $order_by                    Return projects ordered by id, name, path, created_at, updated_at, star_count, or last_activity_at
     *     @var bool               $owned                       Limit by projects owned by the current user
     *     @var string             $search                      Return list of projects matching the search criteria
     *     @var bool               $simple                      Return only limited fields for each project
     *     @var string             $sort                        Return projects sorted in asc or desc order
     *     @var bool               $starred                     Limit by projects starred by the current user
     *     @var bool               $statistics                  Include project statistics
     *     @var \DateTimeInterface $updated_after               Limit results to projects last updated after the specified time
     *     @var \DateTimeInterface $updated_before              Limit results to projects last updated before the specified time
     *     @var string             $visibility                  Limit by visibility public, internal, or private
     *     @var bool               $with_custom_attributes      Include custom attributes in response
     *     @var bool               $with_issues_enabled         Limit by enabled issues feature
     *     @var bool               $with_merge_requests_enabled Limit by enabled merge requests feature
     * }
     *
     * @throws UndefinedOptionsException If an option name is undefined
     * @throws InvalidOptionsException   If an option doesn't fulfill the specified validation rules
     */
    public function forks(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('c');
        };
        $resolver->setDefined('archived')
            ->setAllowedTypes('archived', 'bool')
            ->setNormalizer('archived', $booleanNormalizer)
        ;
        $resolver->setDefined('membership')
            ->setAllowedTypes('membership', 'bool')
            ->setNormalizer('membership', $booleanNormalizer)
        ;
        $resolver->setDefined('min_access_level')
            ->setAllowedValues('min_access_level', [null, 5, 10, 15, 20, 25, 30, 40, 50])
        ;
        $orderBy = ['id', 'name', 'path', 'created_at', 'updated_at', 'star_count', 'last_activity_at'];
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', $orderBy)
        ;
        $resolver->setDefined('owned')
            ->setAllowedTypes('owned', 'bool')
            ->setNormalizer('owned', $booleanNormalizer)
        ;
        $resolver->setDefined('search')
            ->setAllowedTypes('search', 'string')
        ;
        $resolver->setDefined('simple')
            ->setAllowedTypes('simple', 'bool')
            ->setNormalizer('simple', $booleanNormalizer)
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;
        $resolver->setDefined('starred')
            ->setAllowedTypes('starred', 'bool')
            ->setNormalizer('starred', $booleanNormalizer)
        ;
        $resolver->setDefined('statistics')
            ->setAllowedTypes('statistics', 'bool')
            ->setNormalizer('statistics', $booleanNormalizer)
        ;
        $resolver->setDefined('updated_after')
            ->setAllowedTypes('updated_after', \DateTimeInterface::class)
            ->setNormalizer('updated_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('updated_before')
            ->setAllowedTypes('updated_before', \DateTimeInterface::class)
            ->setNormalizer('updated_before', $datetimeNormalizer)
        ;
        $resolver->setDefined('visibility')
            ->setAllowedValues('visibility', ['public', 'internal', 'private'])
        ;
        $resolver->setDefined('with_custom_attributes')
            ->setAllowedTypes('with_custom_attributes', 'bool')
            ->setNormalizer('with_custom_attributes', $booleanNormalizer)
        ;
        $resolver->setDefined('with_issues_enabled')
            ->setAllowedTypes('with_issues_enabled', 'bool')
            ->setNormalizer('with_issues_enabled', $booleanNormalizer)
        ;
        $resolver->setDefined('with_merge_requests_enabled')
            ->setAllowedTypes('with_merge_requests_enabled', 'bool')
            ->setNormalizer('with_merge_requests_enabled', $booleanNormalizer)
        ;

        return $this->get($this->getProjectPath($project_id, 'forks'), $resolver->resolve($parameters));
    }

    /**
     * @param array      $parameters {
     *
     *     @var string     $branches               Branches to fork (empty for all branches)
     *     @var string     $description            The description assigned to the resultant project after forking
     *     @var bool       $mr_default_target_self For forked projects, target merge requests to this project
     *     @var string     $name                   The name assigned to the resultant project after forking
     *     @var int        $namespace_id           The ID of the namespace that the project is forked to
     *     @var string     $namespace_path         The path of the namespace that the project is forked to
     *     @var int|string $namespace              deprecated; use namespace_id or namespace_path instead
     *     @var string     $path                   The path assigned to the resultant project after forking
     *     @var string     $visibility             The visibility level assigned to the resultant project after forking
     * }
     *
     * @throws UndefinedOptionsException If an option name is undefined
     * @throws InvalidOptionsException   If an option doesn't fulfill the specified validation rules
     */
    public function fork(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('branches')
            ->setAllowedTypes('branches', 'string')
        ;
        $resolver->setDefined('description')
            ->setAllowedTypes('description', 'string')
        ;
        $resolver->setDefined('mr_default_target_self')
            ->setAllowedTypes('mr_default_target_self', 'bool')
        ;
        $resolver->setDefined('name')
            ->setAllowedTypes('name', 'string')
        ;
        $resolver->setDefined('namespace_id')
            ->setAllowedTypes('namespace_id', 'int')
        ;
        $resolver->setDefined('namespace_path')
            ->setAllowedTypes('namespace_path', 'string')
        ;
        $resolver->setDefined('namespace')
            ->setAllowedTypes('namespace', ['int', 'string'])
        ;
        $resolver->setDefined('path')
            ->setAllowedTypes('path', 'string')
        ;
        $resolver->setDefined('visibility')
            ->setAllowedValues('visibility', ['public', 'internal', 'private'])
        ;

        return $this->post($this->getProjectPath($project_id, 'fork'), $resolver->resolve($parameters));
    }

    public function createForkRelation(int|string $project_id, int|string $forked_project_id): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'fork/'.self::encodePath($forked_project_id)));
    }

    public function removeForkRelation(int|string $project_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'fork'));
    }

    public function setService(int|string $project_id, string $service_name, array $parameters = []): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'services/'.self::encodePath($service_name)), $parameters);
    }

    public function removeService(int|string $project_id, string $service_name): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'services/'.self::encodePath($service_name)));
    }

    public function variables(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'variables'), $resolver->resolve($parameters));
    }

    public function variable(int|string $project_id, string $key, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('filter')
            ->setAllowedTypes('filter', 'array');

        return $this->get($this->getProjectPath($project_id, 'variables/'.self::encodePath($key)), $resolver->resolve($parameters));
    }

    /**
     * @param array<string,mixed> $parameters        {
     *
     *      @var string $variable_type  env_var (default) or file
     * }
     */
    public function addVariable(int|string $project_id, string $key, string $value, ?bool $protected = null, ?string $environment_scope = null, array $parameters = []): mixed
    {
        $payload = [
            'key' => $key,
            'value' => $value,
        ];

        if ($protected) {
            $payload['protected'] = $protected;
        }

        if ($environment_scope) {
            $payload['environment_scope'] = $environment_scope;
        }

        $payload = \array_merge($parameters, $payload);

        return $this->post($this->getProjectPath($project_id, 'variables'), $payload);
    }

    /**
     * @param array<string,mixed> $parameters        {
     *
     *      @var string $variable_type  env_var (default) or file
     *}
     */
    public function updateVariable(int|string $project_id, string $key, string $value, ?bool $protected = null, ?string $environment_scope = null, array $parameters = []): mixed
    {
        $payload = [
            'value' => $value,
        ];

        if ($protected) {
            $payload['protected'] = $protected;
        }

        if ($environment_scope) {
            $payload['environment_scope'] = $environment_scope;
        }

        $payload = \array_merge($parameters, $payload);

        return $this->put($this->getProjectPath($project_id, 'variables/'.self::encodePath($key)), $payload);
    }

    /**
     * @param array<string, mixed> $parameters    {
     *
     *    @var array $filter    {
     *        @var string $environment_scope    Use filter[environment_scope] to select the variable with the matching environment_scope attribute.
     *    }
     * }
     */
    public function removeVariable(int|string $project_id, string $key, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('filter')
            ->setAllowedTypes('filter', 'array');

        return $this->delete($this->getProjectPath($project_id, 'variables/'.self::encodePath($key)), $resolver->resolve($parameters));
    }

    public function uploadFile(int|string $project_id, string $file): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'uploads'), [], [], ['file' => $file]);
    }

    public function uploadAvatar(int|string $project_id, string $file): mixed
    {
        return $this->put('projects/'.self::encodePath($project_id), [], [], ['avatar' => $file]);
    }

    /**
     * @see https://docs.gitlab.com/ee/api/deployments.html#list-project-deployments
     */
    public function deployments(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('c');
        };

        $resolver->setDefined('order_by')
            ->setAllowedTypes('order_by', 'string')
            ->setAllowedValues('order_by', ['id', 'iid', 'created_at', 'updated_at', 'finished_at', 'ref'])
        ;

        $resolver->setDefined('sort')
            ->setAllowedTypes('sort', 'string')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;

        $resolver->setDefined('updated_after')
            ->setAllowedTypes('updated_after', \DateTimeInterface::class)
            ->setNormalizer('updated_after', $datetimeNormalizer)
        ;

        $resolver->setDefined('updated_before')
            ->setAllowedTypes('updated_before', \DateTimeInterface::class)
            ->setNormalizer('updated_before', $datetimeNormalizer)
        ;

        $resolver->setDefined('finished_after')
            ->setAllowedTypes('finished_after', \DateTimeInterface::class)
            ->setNormalizer('finished_after', $datetimeNormalizer)
        ;

        $resolver->setDefined('finished_before')
            ->setAllowedTypes('finished_before', \DateTimeInterface::class)
            ->setNormalizer('finished_before', $datetimeNormalizer)
        ;

        $resolver->setDefined('environment')
            ->setAllowedTypes('environment', 'string')
        ;

        $resolver->setDefined('status')
            ->setAllowedTypes('status', 'string')
            ->setAllowedValues('status', ['created', 'running', 'success', 'failed', 'canceled', 'blocked'])
        ;

        return $this->get($this->getProjectPath($project_id, 'deployments'), $resolver->resolve($parameters));
    }

    public function deployment(int|string $project_id, int $deployment_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'deployments/'.self::encodePath($deployment_id)));
    }

    public function addShare(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        $datetimeNormalizer = function (OptionsResolver $optionsResolver, \DateTimeInterface $value): string {
            return $value->format('Y-m-d');
        };

        $resolver->setRequired('group_id')
            ->setAllowedTypes('group_id', 'int');

        $resolver->setRequired('group_access')
            ->setAllowedTypes('group_access', 'int')
            ->setAllowedValues('group_access', self::ACCESS_LEVELS);

        $resolver->setDefined('expires_at')
            ->setAllowedTypes('expires_at', \DateTimeInterface::class)
            ->setNormalizer('expires_at', $datetimeNormalizer)
        ;

        return $this->post($this->getProjectPath($project_id, 'share'), $resolver->resolve($parameters));
    }

    public function removeShare(int|string $project_id, int|string $group_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'share/'.$group_id));
    }

    public function badges(int|string $project_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'badges'));
    }

    public function badge(int|string $project_id, int $badge_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'badges/'.self::encodePath($badge_id)));
    }

    public function addBadge(int|string $project_id, array $parameters = []): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'badges'), $parameters);
    }

    public function removeBadge(int|string $project_id, int $badge_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'badges/'.self::encodePath($badge_id)));
    }

    public function updateBadge(int|string $project_id, int $badge_id, array $parameters = []): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'badges/'.self::encodePath($badge_id)), $parameters);
    }

    public function protectedBranches(int|string $project_id, array $parameters = []): mixed
    {
        return $this->get('projects/'.self::encodePath($project_id).'/protected_branches');
    }

    public function addProtectedBranch(int|string $project_id, array $parameters = []): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'protected_branches'), $parameters);
    }

    public function deleteProtectedBranch(int|string $project_id, string $branch_name): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'protected_branches/'.self::encodePath($branch_name)));
    }

    public function updateProtectedBranch(int|string $project_id, string $branch_name, array $parameters = []): mixed
    {
        return $this->patch($this->getProjectPath($project_id, 'protected_branches/'.self::encodePath($branch_name)), $parameters);
    }

    public function approvalsConfiguration(int|string $project_id): mixed
    {
        return $this->get('projects/'.self::encodePath($project_id).'/approvals');
    }

    public function updateApprovalsConfiguration(int|string $project_id, array $parameters = []): mixed
    {
        return $this->post('projects/'.self::encodePath($project_id).'/approvals', $parameters);
    }

    public function approvalsRules(int|string $project_id): mixed
    {
        return $this->get('projects/'.self::encodePath($project_id).'/approval_rules');
    }

    public function createApprovalsRule(int|string $project_id, array $parameters = []): mixed
    {
        return $this->post('projects/'.self::encodePath($project_id).'/approval_rules/', $parameters);
    }

    public function updateApprovalsRule(int|string $project_id, int $approval_rule_id, array $parameters = []): mixed
    {
        return $this->put('projects/'.self::encodePath($project_id).'/approval_rules/'.self::encodePath($approval_rule_id), $parameters);
    }

    public function deleteApprovalsRule(int|string $project_id, int $approval_rule_id): mixed
    {
        return $this->delete('projects/'.self::encodePath($project_id).'/approval_rules/'.self::encodePath($approval_rule_id));
    }

    public function deleteAllMergedBranches(int|string $project_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'repository/merged_branches'));
    }

    /**
     * @param array $parameters {
     *
     *     @var string             $search             search text
     *     @var string             $state              state of the token
     *     @var bool               $revoked            whether the token is revoked or not
     *     @var \DateTimeInterface $created_after      return tokens created after the given time
     *     @var \DateTimeInterface $created_before     return tokens created before the given time
     *     @var \DateTimeInterface $expires_after      return tokens that expire after the given date
     *     @var \DateTimeInterface $expires_before     return tokens that expire before the given date
     *     @var \DateTimeInterface $last_used_after    return tokens last used after the given time
     *     @var \DateTimeInterface $last_used_before   return tokens last used before the given time
     *     @var string             $sort               sort by created, expires, last_used, or name
     * }
     */
    public function projectAccessTokens(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('c');
        };
        $dateNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('Y-m-d');
        };
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('search')
            ->setAllowedTypes('search', 'string')
        ;
        $resolver->setDefined('state')
            ->setAllowedValues('state', ['active', 'inactive'])
        ;
        $resolver->setDefined('revoked')
            ->setAllowedTypes('revoked', 'bool')
            ->setNormalizer('revoked', $booleanNormalizer)
        ;
        $resolver->setDefined('created_after')
            ->setAllowedTypes('created_after', \DateTimeInterface::class)
            ->setNormalizer('created_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('created_before')
            ->setAllowedTypes('created_before', \DateTimeInterface::class)
            ->setNormalizer('created_before', $datetimeNormalizer)
        ;
        $resolver->setDefined('expires_after')
            ->setAllowedTypes('expires_after', \DateTimeInterface::class)
            ->setNormalizer('expires_after', $dateNormalizer)
        ;
        $resolver->setDefined('expires_before')
            ->setAllowedTypes('expires_before', \DateTimeInterface::class)
            ->setNormalizer('expires_before', $dateNormalizer)
        ;
        $resolver->setDefined('last_used_after')
            ->setAllowedTypes('last_used_after', \DateTimeInterface::class)
            ->setNormalizer('last_used_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('last_used_before')
            ->setAllowedTypes('last_used_before', \DateTimeInterface::class)
            ->setNormalizer('last_used_before', $datetimeNormalizer)
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['created_asc', 'created_desc', 'expires_asc', 'expires_desc', 'last_used_asc', 'last_used_desc', 'name_asc', 'name_desc'])
        ;

        return $this->get($this->getProjectPath($project_id, 'access_tokens'), $resolver->resolve($parameters));
    }

    public function projectAccessToken(int|string $project_id, int|string $token_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'access_tokens/'.self::encodePath($token_id)));
    }

    /**
     * @param array      $parameters {
     *
     *     @var string $name                    the name of the project access token
     *     @var array  $scopes                  the scopes, one or many of: api, read_api, read_registry, write_registry, read_repository, write_repository
     *     @var int    $access_level            the access level: 10 (Guest), 20 (Reporter), 30 (Developer), 40 (Maintainer), 50 (Owner)
     *     @var \DateTimeInterface $expires_at  the token expires at midnight UTC on that date
     * }
     */
    public function createProjectAccessToken(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('Y-m-d');
        };

        $resolver->define('name')
            ->required()
        ;

        $resolver->define('scopes')
            ->required()
            ->allowedTypes('array')
            ->allowedValues(function ($scopes) {
                $allowed = ['api', 'read_api', 'read_registry', 'write_registry', 'read_repository', 'write_repository'];
                foreach ($scopes as $scope) {
                    if (!\in_array($scope, $allowed, true)) {
                        return false;
                    }
                }

                return true;
            })
        ;

        $resolver->setDefined('access_level')
            ->setAllowedTypes('access_level', 'int')
            ->setAllowedValues('access_level', [10, 20, 30, 40, 50])
        ;

        $resolver->setDefined('expires_at')
            ->setAllowedTypes('expires_at', \DateTimeInterface::class)
            ->setNormalizer('expires_at', $datetimeNormalizer)
        ;

        return $this->post($this->getProjectPath($project_id, 'access_tokens'), $resolver->resolve($parameters));
    }

    /**
     * @param array $parameters {
     *
     *     @var \DateTimeInterface $expires_at expiration date of the access token
     * }
     */
    public function rotateProjectAccessToken(int|string $project_id, int|string $token_id, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $dateNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('Y-m-d');
        };
        $resolver->setDefined('expires_at')
            ->setAllowedTypes('expires_at', \DateTimeInterface::class)
            ->setNormalizer('expires_at', $dateNormalizer)
        ;

        return $this->post($this->getProjectPath($project_id, 'access_tokens/'.self::encodePath($token_id).'/rotate'), $resolver->resolve($parameters));
    }

    public function deleteProjectAccessToken(int|string $project_id, int|string $token_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'access_tokens/'.$token_id));
    }

    public function jobTokenScope(int|string $project_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'job_token_scope'));
    }

    public function updateJobTokenScope(int|string $project_id, bool $enabled): mixed
    {
        return $this->patch($this->getProjectPath($project_id, 'job_token_scope'), ['enabled' => $enabled]);
    }

    public function jobTokenScopeAllowlistProjects(int|string $project_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'job_token_scope/allowlist'));
    }

    public function addJobTokenScopeAllowlistProject(int|string $project_id, int $target_project_id): mixed
    {
        return $this->post(
            $this->getProjectPath($project_id, 'job_token_scope/allowlist'),
            ['target_project_id' => $target_project_id]
        );
    }

    public function removeJobTokenScopeAllowlistProject(int|string $project_id, int $target_project_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'job_token_scope/allowlist/'.self::encodePath($target_project_id)));
    }

    public function jobTokenScopeAllowlistGroups(int|string $project_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'job_token_scope/groups_allowlist'));
    }

    public function addJobTokenScopeAllowlistGroup(int|string $project_id, int $target_group_id): mixed
    {
        return $this->post(
            $this->getProjectPath($project_id, 'job_token_scope/groups_allowlist'),
            ['target_group_id' => $target_group_id]
        );
    }

    public function removeJobTokenScopeAllowlistGroup(int|string $project_id, int $target_group_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'job_token_scope/groups_allowlist/'.self::encodePath($target_group_id)));
    }

    /**
     * @param array $parameters {
     *
     *     @var bool $tags       include an array of tags in each repository
     *     @var bool $tags_count include tags_count in each repository
     * }
     */
    public function registryRepositories(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('tags')
            ->setAllowedTypes('tags', 'bool')
            ->setNormalizer('tags', $booleanNormalizer)
        ;
        $resolver->setDefined('tags_count')
            ->setAllowedTypes('tags_count', 'bool')
            ->setNormalizer('tags_count', $booleanNormalizer)
        ;

        return $this->get($this->getProjectPath($project_id, 'registry/repositories'), $resolver->resolve($parameters));
    }

    public function protectedTags(int|string $project_id): mixed
    {
        return $this->get('projects/'.self::encodePath($project_id).'/protected_tags');
    }

    public function protectedTag(int|string $project_id, string $tag_name): mixed
    {
        return $this->get('projects/'.self::encodePath($project_id).'/protected_tags/'.self::encodePath($tag_name));
    }

    public function addProtectedTag(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('name')
            ->setAllowedTypes('name', 'string')
            ->setRequired('name')
        ;
        $resolver->setDefined('create_access_level')
            ->setAllowedTypes('create_access_level', 'int')
            ->setAllowedValues('create_access_level', [0, 30, 40])
        ;
        $resolver->setDefined('allowed_to_create')
            ->setAllowedTypes('allowed_to_create', 'array')
            ->setAllowedValues('allowed_to_create', function (array $value) {
                $keys = \array_keys((array) \call_user_func_array('array_merge', $value));
                $diff = \array_diff($keys, ['user_id', 'group_id', 'access_level']);
                $values = \array_map(function ($item) {
                    return \array_values($item)[0] ?? '';
                }, $value);
                $integer = \count($values) === \count(\array_filter($values, 'is_int'));

                return \count($value) > 0 && 0 === \count($diff) && $integer;
            })
        ;

        return $this->post($this->getProjectPath($project_id, 'protected_tags'), $resolver->resolve($parameters));
    }

    public function deleteProtectedTag(int|string $project_id, string $tag_name): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'protected_tags/'.self::encodePath($tag_name)));
    }

    public function remoteMirrors(int|string $project_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'remote_mirrors'));
    }

    public function remoteMirror(int|string $project_id, int $mirror_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'remote_mirrors/'.self::encodePath($mirror_id)));
    }

    public function remoteMirrorPublicKey(int|string $project_id, int $mirror_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'remote_mirrors/'.self::encodePath($mirror_id).'/public_key'));
    }

    /**
     * @param array $parameters {
     *
     *     @var string $scope        The scope to search in
     *     @var string $search       The search query
     *     @var string $state        Filter by state. Issues and merge requests are supported; it is ignored for other scopes.
     *     @var string $ref          The name of a repository branch or tag to search on. The project’s default branch is used by default. Applicable only for scopes blobs, commits, and wiki_blobs.
     *     @var bool   $confidential Filter by confidentiality. Issues scope is supported; it is ignored for other scopes.
     *     @var string $order_by     Allowed values are created_at only. If this is not set, the results are either sorted by created_at in descending order for basic search, or by the most relevant documents when using advanced search.
     *     @var string $sort         Return projects sorted in asc or desc order (default is desc)
     * }
     *
     * @throws UndefinedOptionsException If an option name is undefined
     * @throws InvalidOptionsException   If an option doesn't fulfill the specified validation rules
     */
    public function search(int|string $id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };
        $resolver->setDefined('confidential')
            ->setAllowedTypes('confidential', 'bool')
            ->setNormalizer('confidential', $booleanNormalizer);
        $scope = [
            'blobs',
            'commits',
            'issues',
            'merge_requests',
            'milestones',
            'notes',
            'users',
            'wiki_blobs',
        ];
        $resolver->setRequired('scope')
            ->setAllowedValues('scope', $scope);
        $resolver->setRequired('search');
        $resolver->setDefined('ref')
            ->setAllowedTypes('ref', 'string');
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['created_at']);
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc']);
        $resolver->setDefined('state')
            ->setAllowedValues('state', ['opened', 'closed']);

        return $this->get('projects/'.self::encodePath($id).'/search', $resolver->resolve($parameters));
    }

    private static function createPushRuleOptionsResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();

        foreach ([
            'author_email_regex',
            'branch_name_regex',
            'commit_message_negative_regex',
            'commit_message_regex',
            'file_name_regex',
        ] as $option) {
            $resolver->setDefined($option)
                ->setAllowedTypes($option, 'string')
            ;
        }

        foreach ([
            'commit_committer_check',
            'commit_committer_name_check',
            'deny_delete_tag',
            'member_check',
            'prevent_secrets',
            'reject_non_dco_commits',
            'reject_unsigned_commits',
        ] as $option) {
            $resolver->setDefined($option)
                ->setAllowedTypes($option, 'bool')
            ;
        }

        $resolver->setDefined('max_file_size')
            ->setAllowedTypes('max_file_size', 'int')
        ;

        return $resolver;
    }
}
