<?php namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Projects extends AbstractApi
{
    /**
     * @param array $parameters {
     *
     *     @var bool   $archived                    Limit by archived status.
     *     @var string $visibility                  Limit by visibility public, internal, or private.
     *     @var string $order_by                    Return projects ordered by id, name, path, created_at, updated_at,
     *                                              or last_activity_at fields. Default is created_at.
     *     @var string $sort                        Return projects sorted in asc or desc order. Default is desc.
     *     @var string $search                      Return list of projects matching the search criteria.
     *     @var bool   $simple                      Return only the ID, URL, name, and path of each project.
     *     @var bool   $owned                       Limit by projects owned by the current user.
     *     @var bool   $membership                  Limit by projects that the current user is a member of.
     *     @var bool   $starred                     Limit by projects starred by the current user.
     *     @var bool   $statistics                  Include project statistics.
     *     @var bool   $with_issues_enabled         Limit by enabled issues feature.
     *     @var bool   $with_merge_requests_enabled Limit by enabled merge requests feature.
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
     * @param array $parameters {
     *
     *     @var bool   $statistics                    Include project statistics.
     *     @var bool   $with_custom_attributes        Include project custom attributes.
     * }
     * @return mixed
     */
    public function show($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value) {
            return $value ? true : false;
        };
        $resolver->setDefined('statistics')
            ->setAllowedTypes('statistics', 'bool')
            ->setNormalizer('statistics', $booleanNormalizer)
        ;
        $resolver->setDefined('with_custom_attributes')
            ->setAllowedTypes('with_custom_attributes', 'bool')
            ->setNormalizer('with_custom_attributes', $booleanNormalizer)
        ;
        return $this->get('projects/'.$this->encodePath($project_id), $resolver->resolve($parameters));
    }

    /**
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function create($name, array $params = array())
    {
        $params['name'] = $name;

        return $this->post('projects', $params);
    }

    /**
     * @param int $user_id
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function createForUser($user_id, $name, array $params = array())
    {
        $params['name'] = $name;

        return $this->post('projects/user/'.$this->encodePath($user_id), $params);
    }

    /**
     * @param int $project_id
     * @param array $params
     * @return mixed
     */
    public function update($project_id, array $params)
    {
        return $this->put('projects/'.$this->encodePath($project_id), $params);
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function remove($project_id)
    {
        return $this->delete('projects/'.$this->encodePath($project_id));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function archive($project_id)
    {
        return $this->post("projects/".$this->encodePath($project_id)."/archive");
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function unarchive($project_id)
    {
        return $this->post("projects/".$this->encodePath($project_id)."/unarchive");
    }

    /**
     * @param int   $project_id
     * @param array $parameters (
     *
     *     @var string $scope       The scope of pipelines, one of: running, pending, finished, branches, tags.
     *     @var string $status      The status of pipelines, one of: running, pending, success, failed, canceled, skipped.
     *     @var string $ref         The ref of pipelines.
     *     @var string $sha         The sha of pipelines.
     *     @var bool   $yaml_errors Returns pipelines with invalid configurations.
     *     @var string $name        The name of the user who triggered pipelines.
     *     @var string $username    The username of the user who triggered pipelines.
     *     @var string $order_by    Order pipelines by id, status, ref, or user_id (default: id).
     *     @var string $order       Sort pipelines in asc or desc order (default: desc).
     * )
     * @return mixed
     */
    public function pipelines($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value) {
            return $value ? 'true' : 'false';
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
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['id', 'status', 'ref', 'user_id'])
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;

        return $this->get($this->getProjectPath($project_id, 'pipelines'), $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param int $pipeline_id
     * @return mixed
     */
    public function pipeline($project_id, $pipeline_id)
    {
        return $this->get($this->getProjectPath($project_id, 'pipelines/'.$this->encodePath($pipeline_id)));
    }

    /**
     * @param int $project_id
     * @param string $commit_ref
     * @return mixed
     */
    public function createPipeline($project_id, $commit_ref)
    {
        return $this->post($this->getProjectPath($project_id, 'pipeline'), array(
            'ref' => $commit_ref));
    }

    /**
     * @param int $project_id
     * @param int $pipeline_id
     * @return mixed
     */
    public function retryPipeline($project_id, $pipeline_id)
    {
        return $this->post($this->getProjectPath($project_id, 'pipelines/'.$this->encodePath($pipeline_id)).'/retry');
    }

    /**
     * @param int $project_id
     * @param int $pipeline_id
     * @return mixed
     */
    public function cancelPipeline($project_id, $pipeline_id)
    {
        return $this->post($this->getProjectPath($project_id, 'pipelines/'.$this->encodePath($pipeline_id)).'/cancel');
    }
    
    /**
     * @param $project_id
     * @param $pipeline_id
     * @return mixed
     */
    public function deletePipeline($project_id, $pipeline_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'pipelines/'.$this->encodePath($pipeline_id)));
    }
    
    /**
     * @param integer $project_id
     * @param array $parameters
     * @return mixed
     */
    public function allMembers($project_id, $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('query');

        return $this->get('projects/'.$this->encodePath($project_id).'/members/all', $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param array $parameters (
     *
     *     @var string $query           The query you want to search members for.
     * )
     *
     * @throws MissingOptionsException  If a required option is not provided
     *
     * @return mixed
     */
    public function members($project_id, $parameters = [])
    {
        if (!is_array($parameters)) {
            @trigger_error("Deprecated: String parameter of the members() function is deprecated.", E_USER_NOTICE);
            $username_query = $parameters;
            $parameters = array();
            if (!empty($username_query)) {
                $parameters['query'] = $username_query;
            }
        }

        $resolver = $this->createOptionsResolver();

        $resolver->setDefined('query')
            ->setAllowedTypes('query', 'string')
        ;

        return $this->get($this->getProjectPath($project_id, 'members'), $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param int $user_id
     * @return mixed
     */
    public function member($project_id, $user_id)
    {
        return $this->get($this->getProjectPath($project_id, 'members/'.$this->encodePath($user_id)));
    }

    /**
     * @param int $project_id
     * @param int $user_id
     * @param int $access_level
     * @return mixed
     */
    public function addMember($project_id, $user_id, $access_level)
    {
        return $this->post($this->getProjectPath($project_id, 'members'), array(
            'user_id' => $user_id,
            'access_level' => $access_level
        ));
    }

    /**
     * @param int $project_id
     * @param int $user_id
     * @param int $access_level
     * @return mixed
     */
    public function saveMember($project_id, $user_id, $access_level)
    {
        return $this->put($this->getProjectPath($project_id, 'members/'.urldecode($user_id)), array(
            'access_level' => $access_level
        ));
    }

    /**
     * @param int $project_id
     * @param int $user_id
     * @return mixed
     */
    public function removeMember($project_id, $user_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'members/'.urldecode($user_id)));
    }

    /**
     * @param int $project_id
     * @param array $parameters
     * @return mixed
     */
    public function hooks($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'hooks'), $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param int $hook_id
     * @return mixed
     */
    public function hook($project_id, $hook_id)
    {
        return $this->get($this->getProjectPath($project_id, 'hooks/'.$this->encodePath($hook_id)));
    }

    /**
     * Get project users.
     *
     * See https://docs.gitlab.com/ee/api/projects.html#get-project-users for more info.
     *
     * @param int $project_id
     *   Project id.
     * @param array $parameters
     *   Url parameters.
     *
     * @return array
     *   List of project users.
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
     * @param int $project_id
     *   Project id.
     * @param array $parameters
     *   Url parameters. For example: issue state (opened / closed).
     *
     * @return array
     *   List of project issues.
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
     * @param int $project_id
     *   Project id.
     *
     * @return array
     *   List of project boards.
     */
    public function boards($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'boards'));
    }

    /**
     * @param int $project_id
     * @param string $url
     * @param array $params
     * @return mixed
     */
    public function addHook($project_id, $url, array $params = array())
    {
        if (empty($params)) {
            $params = array('push_events' => true);
        }

        $params['url'] = $url;

        return $this->post($this->getProjectPath($project_id, 'hooks'), $params);
    }

    /**
     * @param int $project_id
     * @param int $hook_id
     * @param array $params
     * @return mixed
     */
    public function updateHook($project_id, $hook_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'hooks/'.$this->encodePath($hook_id)), $params);
    }

    /**
     * @param int $project_id
     * @param int $hook_id
     * @return mixed
     */
    public function removeHook($project_id, $hook_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'hooks/'.$this->encodePath($hook_id)));
    }

    /**
     * @param int $project_id
     * @param mixed $namespace
     * @return mixed
     */
    public function transfer($project_id, $namespace)
    {
        return $this->put($this->getProjectPath($project_id, 'transfer'), ['namespace' => $namespace]);
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function deployKeys($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deploy_keys'));
    }

    /**
     * @param int $project_id
     * @param int $key_id
     * @return mixed
     */
    public function deployKey($project_id, $key_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deploy_keys/'.$this->encodePath($key_id)));
    }

    /**
     * @param int $project_id
     * @param string $title
     * @param string $key
     * @param bool $canPush
     * @return mixed
     */
    public function addDeployKey($project_id, $title, $key, $canPush = false)
    {
        return $this->post($this->getProjectPath($project_id, 'deploy_keys'), array(
            'title' => $title,
            'key' => $key,
            'can_push' => $canPush
        ));
    }

    /**
     * @param int $project_id
     * @param int $key_id
     * @return mixed
     */
    public function deleteDeployKey($project_id, $key_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'deploy_keys/'.$this->encodePath($key_id)));
    }

    /**
     * @param int $project_id
     * @param int $key_id
     * @return mixed
     */
    public function enableDeployKey($project_id, $key_id)
    {
        return $this->post($this->getProjectPath($project_id, 'deploy_keys/'.$this->encodePath($key_id).'/enable'));
    }

    /**
     * @param int $project_id
     * @param array $parameters (
     *
     *     @var string             $action      Include only events of a particular action type.
     *     @var string             $target_type Include only events of a particular target type.
     *     @var \DateTimeInterface $before      Include only events created before a particular date.
     *     @var \DateTimeInterface $after       Include only events created after a particular date.
     *     @var string             $sort        Sort events in asc or desc order by created_at. Default is desc.
     * )
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
     * @param int $project_id
     * @param array $parameters
     * @return mixed
     */
    public function labels($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'labels'), $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param array $params
     * @return mixed
     */
    public function addLabel($project_id, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'labels'), $params);
    }

    /**
     * @param int $project_id
     * @param array $params
     * @return mixed
     */
    public function updateLabel($project_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'labels'), $params);
    }

    /**
     * @param int $project_id
     * @param string $name
     * @return mixed
     */
    public function removeLabel($project_id, $name)
    {
        return $this->delete($this->getProjectPath($project_id, 'labels'), array(
            'name' => $name
        ));
    }

    /**
     * Get languages used in a project with percentage value.
     *
     * @param int $project_id
     * @return mixed
     */
    public function languages($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'languages'));
    }

    /**
     * @param int $project_id
     * @param array $params (
     *
     *     @var string $namespace      The ID or path of the namespace that the project will be forked to
     *     @var string $path           The path of the forked project (optional)
     *     @var string $name           The name of the forked project (optional)
     * )
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
     * @param int $project_id
     * @param int $forked_project_id
     * @return mixed
     */
    public function createForkRelation($project_id, $forked_project_id)
    {
        return $this->post($this->getProjectPath($project_id, 'fork/'.$this->encodePath($forked_project_id)));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function removeForkRelation($project_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'fork'));
    }

    /**
     * @param int $project_id
     * @param string $service_name
     * @param array $params
     * @return mixed
     */
    public function setService($project_id, $service_name, array $params = array())
    {
        return $this->put($this->getProjectPath($project_id, 'services/'.$this->encodePath($service_name)), $params);
    }

    /**
     * @param int $project_id
     * @param string $service_name
     * @return mixed
     */
    public function removeService($project_id, $service_name)
    {
        return $this->delete($this->getProjectPath($project_id, 'services/'.$this->encodePath($service_name)));
    }

    /**
     * @param int $project_id
     * @param array $parameters
     * @return mixed
     */
    public function variables($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'variables'), $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param string $key
     * @return mixed
     */
    public function variable($project_id, $key)
    {
        return $this->get($this->getProjectPath($project_id, 'variables/'.$this->encodePath($key)));
    }

    /**
     * @param int $project_id
     * @param string $key
     * @param string $value
     * @param bool $protected
     * @param string $environment_scope
     * @return mixed
     */
    public function addVariable($project_id, $key, $value, $protected = null, $environment_scope = null)
    {
        $payload = array(
            'key'   => $key,
            'value' => $value,
        );

        if ($protected) {
            $payload['protected'] = $protected;
        }

        if ($environment_scope) {
            $payload['environment_scope'] = $environment_scope;
        }

        return $this->post($this->getProjectPath($project_id, 'variables'), $payload);
    }

    /**
     * @param int $project_id
     * @param string $key
     * @param string $value
     * @param bool $protected
     * @param string $environment_scope
     * @return mixed
     */
    public function updateVariable($project_id, $key, $value, $protected = null, $environment_scope = null)
    {
        $payload = array(
            'value' => $value,
        );

        if ($protected) {
            $payload['protected'] = $protected;
        }

        if ($environment_scope) {
            $payload['environment_scope'] = $environment_scope;
        }

        return $this->put($this->getProjectPath($project_id, 'variables/'.$this->encodePath($key)), $payload);
    }

    /**
     * @param int $project_id
     * @param string $key
     * @return mixed
     */
    public function removeVariable($project_id, $key)
    {
        return $this->delete($this->getProjectPath($project_id, 'variables/'.$this->encodePath($key)));
    }

    /**
     * @param int $project_id
     * @param string $file
     * @return mixed
     */
    public function uploadFile($project_id, $file)
    {
        return $this->post($this->getProjectPath($project_id, 'uploads'), array(), array(), array('file' => $file));
    }

    /**
     * @param int $project_id
     * @param array $parameters
     * @return mixed
     */
    public function deployments($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'deployments'), $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param int $deployment_id
     * @return mixed
     */
    public function deployment($project_id, $deployment_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deployments/'.$this->encodePath($deployment_id)));
    }
    
    /**
     * @param mixed $project_id
     * @param array $parameters
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
            ->setAllowedValues('group_access', [0,10,20,30,40,50]);

        $resolver->setDefined('expires_at')
            ->setAllowedTypes('expires_at', \DateTimeInterface::class)
            ->setNormalizer('expires_at', $datetimeNormalizer)
        ;

        return $this->post($this->getProjectPath($project_id, 'share'), $resolver->resolve($parameters));
    }
    
    /**
     * @param mixed $project_id
     * @param int $group_id
     * @return mixed
     */
    public function removeShare($project_id, $group_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'share/' . $group_id));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function badges($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'badges'));
    }

    /**
     * @param int $project_id
     * @param string $badge_id
     * @return mixed
     */
    public function badge($project_id, $badge_id)
    {
        return $this->get($this->getProjectPath($project_id, 'badges/' . $this->encodePath($badge_id)));
    }

    /**
     * @param int $project_id
     * @param array $params
     * @return mixed
     */
    public function addBadge($project_id, array $params = array())
    {
        return $this->post($this->getProjectPath($project_id, 'badges'), $params);
    }

    /**
     * @param int $project_id
     * @param string $badge_id
     * @return mixed
     */
    public function removeBadge($project_id, $badge_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'badges/' . $this->encodePath($badge_id)));
    }

    /**
     * @param int $project_id
     * @param string $badge_id
     * @param array $params
     * @return mixed
    */
    public function updateBadge($project_id, $badge_id, array $params = array())
    {
        return $this->put($this->getProjectPath($project_id, 'badges/' . $this->encodePath($badge_id)));
    }


    /**
     * @param int $project_id
     * @param array $params
     * @return mixed
     */
    public function addProtectedBranch($project_id, array $params = [])
    {
        return $this->post($this->getProjectPath($project_id, 'protected_branches'), $params);
    }
}
