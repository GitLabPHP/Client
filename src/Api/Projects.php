<?php

declare(strict_types=1);

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
     *     @var bool   $archived                    limit by archived status
     *     @var string $visibility                  limit by visibility public, internal, or private
     *     @var string $order_by                    Return projects ordered by id, name, path, created_at, updated_at,
     *                                              or last_activity_at fields (default is created_at)
     *     @var string $sort                        Return projects sorted in asc or desc order (default is desc)
     *     @var string $search                      return list of projects matching the search criteria
     *     @var bool   $simple                      return only the ID, URL, name, and path of each project
     *     @var bool   $owned                       limit by projects owned by the current user
     *     @var bool   $membership                  limit by projects that the current user is a member of
     *     @var bool   $starred                     limit by projects starred by the current user
     *     @var bool   $statistics                  include project statistics
     *     @var bool   $with_issues_enabled         limit by enabled issues feature
     *     @var bool   $with_merge_requests_enabled limit by enabled merge requests feature
     *     @var int    $min_access_level            Limit by current user minimal access level
     * }
     *
     * @throws UndefinedOptionsException If an option name is undefined
     * @throws InvalidOptionsException   If an option doesn't fulfill the
     *                                   specified validation rules
     *
     * @return mixed
     */
    public function all(array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value) {
            return $value ? 'true' : 'false';
        };
        $resolver->setDefined('archived')
            ->setAllowedTypes('archived', 'bool')
            ->setNormalizer('archived', $booleanNormalizer)
        ;
        $resolver->setDefined('visibility')
            ->setAllowedValues('visibility', ['public', 'internal', 'private'])
        ;
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['id', 'name', 'path', 'created_at', 'updated_at', 'last_activity_at'])
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;
        $resolver->setDefined('search');
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

        return $this->get('projects', $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters {
     *
     *     @var bool   $statistics                    include project statistics
     *     @var bool   $with_custom_attributes        Include project custom attributes.
     * }
     *
     * @return mixed
     */
    public function show($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value) {
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

    /**
     * @param string $name
     * @param array  $parameters
     *
     * @return mixed
     */
    public function create(string $name, array $parameters = [])
    {
        $parameters['name'] = $name;

        return $this->post('projects', $parameters);
    }

    /**
     * @param int    $user_id
     * @param string $name
     * @param array  $parameters
     *
     * @return mixed
     */
    public function createForUser(int $user_id, string $name, array $parameters = [])
    {
        $parameters['name'] = $name;

        return $this->post('projects/user/'.self::encodePath($user_id), $parameters);
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function update($project_id, array $parameters)
    {
        return $this->put('projects/'.self::encodePath($project_id), $parameters);
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function remove($project_id)
    {
        return $this->delete('projects/'.self::encodePath($project_id));
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function archive($project_id)
    {
        return $this->post('projects/'.self::encodePath($project_id).'/archive');
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function unarchive($project_id)
    {
        return $this->post('projects/'.self::encodePath($project_id).'/unarchive');
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function triggers($project_id)
    {
        return $this->get('projects/'.self::encodePath($project_id).'/triggers');
    }

    /**
     * @param int|string $project_id
     * @param int        $trigger_id
     *
     * @return mixed
     */
    public function trigger($project_id, int $trigger_id)
    {
        return $this->get($this->getProjectPath($project_id, 'triggers/'.self::encodePath($trigger_id)));
    }

    /**
     * @param int|string $project_id
     * @param string     $description
     *
     * @return mixed
     */
    public function createTrigger($project_id, string $description)
    {
        return $this->post($this->getProjectPath($project_id, 'triggers'), [
            'description' => $description,
        ]);
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters {
     *
     *     @var string $scope       the scope of pipelines, one of: running, pending, finished, branches, tags
     *     @var string $status      the status of pipelines, one of: running, pending, success, failed, canceled, skipped
     *     @var string $ref         the ref of pipelines
     *     @var string $sha         the sha of pipelines
     *     @var bool   $yaml_errors returns pipelines with invalid configurations
     *     @var string $name        the name of the user who triggered pipelines
     *     @var string $username    the username of the user who triggered pipelines
     *     @var string $order_by    order pipelines by id, status, ref, or user_id (default: id)
     *     @var string $order       Sort pipelines in asc or desc order (default: desc).
     * }
     *
     * @return mixed
     */
    public function pipelines($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value) {
            return $value ? 'true' : 'false';
        };
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value) {
            return $value->format('Y-m-d');
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
            ->setAllowedValues('order_by', ['id', 'status', 'ref', 'user_id'])
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;

        return $this->get($this->getProjectPath($project_id, 'pipelines'), $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param int        $pipeline_id
     *
     * @return mixed
     */
    public function pipeline($project_id, int $pipeline_id)
    {
        return $this->get($this->getProjectPath($project_id, 'pipelines/'.self::encodePath($pipeline_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $pipeline_id
     *
     * @return mixed
     */
    public function pipelineVariables($project_id, int $pipeline_id)
    {
        return $this->get($this->getProjectPath($project_id, 'pipelines/'.self::encodePath($pipeline_id).'/variables'));
    }

    /**
     * @param int|string $project_id
     * @param string     $commit_ref
     * @param array|null $variables  {
     *
     *     @var string $key            The name of the variable
     *     @var mixed $value           The value of the variable
     *     @var string $variable_type  env_var (default) or file
     * }
     *
     * @return mixed
     */
    public function createPipeline($project_id, string $commit_ref, array $variables = null)
    {
        $parameters = [
            'ref' => $commit_ref,
        ];

        if (null !== $variables) {
            $parameters['variables'] = $variables;
        }

        return $this->post($this->getProjectPath($project_id, 'pipeline'), $parameters);
    }

    /**
     * @param int|string $project_id
     * @param int        $pipeline_id
     *
     * @return mixed
     */
    public function retryPipeline($project_id, int $pipeline_id)
    {
        return $this->post($this->getProjectPath($project_id, 'pipelines/'.self::encodePath($pipeline_id)).'/retry');
    }

    /**
     * @param int|string $project_id
     * @param int        $pipeline_id
     *
     * @return mixed
     */
    public function cancelPipeline($project_id, int $pipeline_id)
    {
        return $this->post($this->getProjectPath($project_id, 'pipelines/'.self::encodePath($pipeline_id)).'/cancel');
    }

    /**
     * @param int|string $project_id
     * @param int        $pipeline_id
     *
     * @return mixed
     */
    public function deletePipeline($project_id, int $pipeline_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'pipelines/'.self::encodePath($pipeline_id)));
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function allMembers($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('query');

        return $this->get('projects/'.self::encodePath($project_id).'/members/all', $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters {
     *
     *     @var string $query           The query you want to search members for.
     * }
     *
     * @return mixed
     */
    public function members($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        $resolver->setDefined('query')
            ->setAllowedTypes('query', 'string')
        ;

        return $this->get($this->getProjectPath($project_id, 'members'), $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param int        $user_id
     *
     * @return mixed
     */
    public function member($project_id, int $user_id)
    {
        return $this->get($this->getProjectPath($project_id, 'members/'.self::encodePath($user_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $user_id
     * @param int        $access_level
     *
     * @return mixed
     */
    public function addMember($project_id, int $user_id, int $access_level)
    {
        return $this->post($this->getProjectPath($project_id, 'members'), [
            'user_id' => $user_id,
            'access_level' => $access_level,
        ]);
    }

    /**
     * @param int|string $project_id
     * @param int        $user_id
     * @param int        $access_level
     *
     * @return mixed
     */
    public function saveMember($project_id, int $user_id, int $access_level)
    {
        return $this->put($this->getProjectPath($project_id, 'members/'.self::encodePath($user_id)), [
            'access_level' => $access_level,
        ]);
    }

    /**
     * @param int|string $project_id
     * @param int        $user_id
     *
     * @return mixed
     */
    public function removeMember($project_id, int $user_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'members/'.self::encodePath($user_id)));
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function hooks($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'hooks'), $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param int        $hook_id
     *
     * @return mixed
     */
    public function hook($project_id, int $hook_id)
    {
        return $this->get($this->getProjectPath($project_id, 'hooks/'.self::encodePath($hook_id)));
    }

    /**
     * Get project users.
     *
     * See https://docs.gitlab.com/ee/api/projects.html#get-project-users for more info.
     *
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return array
     */
    public function users($project_id, array $parameters = [])
    {
        return $this->get($this->getProjectPath($project_id, 'users'), $parameters);
    }

    /**
     * Get project issues.
     *
     * See https://docs.gitlab.com/ee/api/issues.html#list-project-issues for more info.
     *
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return array
     *               List of project issues
     */
    public function issues($project_id, array $parameters = [])
    {
        return $this->get($this->getProjectPath($project_id, 'issues'), $parameters);
    }

    /**
     * Get projects board list.
     *
     * See https://docs.gitlab.com/ee/api/boards.html for more info.
     *
     * @param int|string $project_id
     *
     * @return array
     */
    public function boards($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'boards'));
    }

    /**
     * Gets a list of all discussion items for a single commit.
     *
     * Example:
     * - https://gitlab.com/gitlab-org/gitlab/-/commit/695c29abcf7dc2eabde8d59869abcea0923ce8fa#note_334686748
     * - https://gitlab.com/api/v4/projects/gitlab-org%2Fgitlab/repository/commits/695c29abcf7dc2eabde8d59869abcea0923ce8fa/discussions
     *
     * @see https://docs.gitlab.com/ee/api/discussions.html#list-project-commit-discussion-items
     *
     * @param int|string $project_id
     * @param string     $commit_id
     *
     * @return mixed
     */
    public function getRepositoryCommitDiscussions($project_id, string $commit_id)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/'.self::encodePath($commit_id)).'/discussions');
    }

    /**
     * @param int|string $project_id
     * @param string     $url
     * @param array      $parameters
     *
     * @return mixed
     */
    public function addHook($project_id, string $url, array $parameters = [])
    {
        if (0 === \count($parameters)) {
            $parameters = ['push_events' => true];
        }

        $parameters['url'] = $url;

        return $this->post($this->getProjectPath($project_id, 'hooks'), $parameters);
    }

    /**
     * @param int|string $project_id
     * @param int        $hook_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function updateHook($project_id, int $hook_id, array $parameters)
    {
        return $this->put($this->getProjectPath($project_id, 'hooks/'.self::encodePath($hook_id)), $parameters);
    }

    /**
     * @param int|string $project_id
     * @param int        $hook_id
     *
     * @return mixed
     */
    public function removeHook($project_id, int $hook_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'hooks/'.self::encodePath($hook_id)));
    }

    /**
     * @param int|string $project_id
     * @param mixed      $namespace
     *
     * @return mixed
     */
    public function transfer($project_id, $namespace)
    {
        return $this->put($this->getProjectPath($project_id, 'transfer'), ['namespace' => $namespace]);
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function deployKeys($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deploy_keys'));
    }

    /**
     * @param int|string $project_id
     * @param int        $key_id
     *
     * @return mixed
     */
    public function deployKey($project_id, int $key_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deploy_keys/'.self::encodePath($key_id)));
    }

    /**
     * @param int|string $project_id
     * @param string     $title
     * @param string     $key
     * @param bool       $canPush
     *
     * @return mixed
     */
    public function addDeployKey($project_id, string $title, string $key, bool $canPush = false)
    {
        return $this->post($this->getProjectPath($project_id, 'deploy_keys'), [
            'title' => $title,
            'key' => $key,
            'can_push' => $canPush,
        ]);
    }

    /**
     * @param int|string $project_id
     * @param int        $key_id
     *
     * @return mixed
     */
    public function deleteDeployKey($project_id, int $key_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'deploy_keys/'.self::encodePath($key_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $key_id
     *
     * @return mixed
     */
    public function enableDeployKey($project_id, int $key_id)
    {
        return $this->post($this->getProjectPath($project_id, 'deploy_keys/'.self::encodePath($key_id).'/enable'));
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters {
     *
     *     @var string             $action      include only events of a particular action type
     *     @var string             $target_type include only events of a particular target type
     *     @var \DateTimeInterface $before      include only events created before a particular date
     *     @var \DateTimeInterface $after       include only events created after a particular date
     *     @var string             $sort        Sort events in asc or desc order by created_at (default is desc)
     * }
     *
     * @return mixed
     */
    public function events($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value) {
            return $value->format('Y-m-d');
        };

        $resolver->setDefined('action')
            ->setAllowedValues('action', ['created', 'updated', 'closed', 'reopened', 'pushed', 'commented', 'merged', 'joined', 'left', 'destroyed', 'expired'])
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
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function labels($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'labels'), $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function addLabel($project_id, array $parameters)
    {
        return $this->post($this->getProjectPath($project_id, 'labels'), $parameters);
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function updateLabel($project_id, array $parameters)
    {
        return $this->put($this->getProjectPath($project_id, 'labels'), $parameters);
    }

    /**
     * @param int|string $project_id
     * @param string     $name
     *
     * @return mixed
     */
    public function removeLabel($project_id, string $name)
    {
        return $this->delete($this->getProjectPath($project_id, 'labels'), [
            'name' => $name,
        ]);
    }

    /**
     * Get languages used in a project with percentage value.
     *
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function languages($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'languages'));
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function forks($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'forks'), $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters {
     *
     *     @var string $namespace      The ID or path of the namespace that the project will be forked to
     *     @var string $path           The path of the forked project (optional)
     *     @var string $name           The name of the forked project (optional)
     * }
     *
     * @return mixed
     */
    public function fork($project_id, array $parameters = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined(['namespace', 'path', 'name']);

        $resolved = $resolver->resolve($parameters);

        return $this->post($this->getProjectPath($project_id, 'fork'), $resolved);
    }

    /**
     * @param int|string $project_id
     * @param int|string $forked_project_id
     *
     * @return mixed
     */
    public function createForkRelation($project_id, $forked_project_id)
    {
        return $this->post($this->getProjectPath($project_id, 'fork/'.self::encodePath($forked_project_id)));
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function removeForkRelation($project_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'fork'));
    }

    /**
     * @param int|string $project_id
     * @param string     $service_name
     * @param array      $parameters
     *
     * @return mixed
     */
    public function setService($project_id, string $service_name, array $parameters = [])
    {
        return $this->put($this->getProjectPath($project_id, 'services/'.self::encodePath($service_name)), $parameters);
    }

    /**
     * @param int|string $project_id
     * @param string     $service_name
     *
     * @return mixed
     */
    public function removeService($project_id, string $service_name)
    {
        return $this->delete($this->getProjectPath($project_id, 'services/'.self::encodePath($service_name)));
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function variables($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'variables'), $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param string     $key
     *
     * @return mixed
     */
    public function variable($project_id, string $key)
    {
        return $this->get($this->getProjectPath($project_id, 'variables/'.self::encodePath($key)));
    }

    /**
     * @param int|string          $project_id
     * @param string              $key
     * @param string              $value
     * @param bool|null           $protected
     * @param string|null         $environment_scope
     * @param array<string,mixed> $parameters        {
     *
     *      @var string $variable_type  env_var (default) or file
     * }
     *
     * @return mixed
     */
    public function addVariable($project_id, string $key, string $value, ?bool $protected = null, ?string $environment_scope = null, array $parameters = [])
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
     * @param int|string          $project_id
     * @param string              $key
     * @param string              $value
     * @param bool|null           $protected
     * @param string|null         $environment_scope
     * @param array<string,mixed> $parameters        {
     *
     *      @var string $variable_type  env_var (default) or file
     *}
     *
     * @return mixed
     */
    public function updateVariable($project_id, string $key, string $value, ?bool $protected = null, ?string $environment_scope = null, array $parameters = [])
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
     * @param int|string $project_id
     * @param string     $key
     *
     * @return mixed
     */
    public function removeVariable($project_id, string $key)
    {
        return $this->delete($this->getProjectPath($project_id, 'variables/'.self::encodePath($key)));
    }

    /**
     * @param int|string $project_id
     * @param string     $file
     *
     * @return mixed
     */
    public function uploadFile($project_id, string $file)
    {
        return $this->post($this->getProjectPath($project_id, 'uploads'), [], [], ['file' => $file]);
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function deployments($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'deployments'), $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param int        $deployment_id
     *
     * @return mixed
     */
    public function deployment($project_id, int $deployment_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deployments/'.self::encodePath($deployment_id)));
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function addShare($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        $datetimeNormalizer = function (OptionsResolver $optionsResolver, \DateTimeInterface $value) {
            return $value->format('Y-m-d');
        };

        $resolver->setRequired('group_id')
            ->setAllowedTypes('group_id', 'int');

        $resolver->setRequired('group_access')
            ->setAllowedTypes('group_access', 'int')
            ->setAllowedValues('group_access', [0, 10, 20, 30, 40, 50]);

        $resolver->setDefined('expires_at')
            ->setAllowedTypes('expires_at', \DateTimeInterface::class)
            ->setNormalizer('expires_at', $datetimeNormalizer)
        ;

        return $this->post($this->getProjectPath($project_id, 'share'), $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param int|string $group_id
     *
     * @return mixed
     */
    public function removeShare($project_id, $group_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'share/'.$group_id));
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function badges($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'badges'));
    }

    /**
     * @param int|string $project_id
     * @param int        $badge_id
     *
     * @return mixed
     */
    public function badge($project_id, int $badge_id)
    {
        return $this->get($this->getProjectPath($project_id, 'badges/'.self::encodePath($badge_id)));
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function addBadge($project_id, array $parameters = [])
    {
        return $this->post($this->getProjectPath($project_id, 'badges'), $parameters);
    }

    /**
     * @param int|string $project_id
     * @param int        $badge_id
     *
     * @return mixed
     */
    public function removeBadge($project_id, int $badge_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'badges/'.self::encodePath($badge_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $badge_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function updateBadge($project_id, int $badge_id, array $parameters = [])
    {
        return $this->put($this->getProjectPath($project_id, 'badges/'.self::encodePath($badge_id)), $parameters);
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function protectedBranches($project_id, array $parameters = [])
    {
        return $this->get('projects/'.self::encodePath($project_id).'/protected_branches');
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function addProtectedBranch($project_id, array $parameters = [])
    {
        return $this->post($this->getProjectPath($project_id, 'protected_branches'), $parameters);
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function approvalsConfiguration($project_id)
    {
        return $this->get('projects/'.self::encodePath($project_id).'/approvals');
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function approvalsRules($project_id)
    {
        return $this->get('projects/'.self::encodePath($project_id).'/approval_rules');
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function deleteAllMergedBranches($project_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'repository/merged_branches'));
    }
}
