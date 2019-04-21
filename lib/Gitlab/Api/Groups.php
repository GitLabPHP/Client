<?php namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\Options;

class Groups extends AbstractApi
{
    /**
     * @param array $parameters (
     *
     *     @var int[]  $skip_groups   Skip the group IDs passes.
     *     @var bool   $all_available Show all the groups you have access to.
     *     @var string $search        Return list of authorized groups matching the search criteria.
     *     @var string $order_by      Order groups by name or path. Default is name.
     *     @var string $sort          Order groups in asc or desc order. Default is asc.
     *     @var bool   $statistics    Include group statistics (admins only).
     *     @var bool   $owned         Limit by groups owned by the current user.
     * )
     * @return mixed
     */
    public function all(array $parameters = [])
    {
        $resolver = $this->getGroupSearchResolver();

        return $this->get('groups', $resolver->resolve($parameters));
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->get('groups/'.$this->encodePath($id));
    }

    /**
     * @param string $name
     * @param string $path
     * @param string $description
     * @param string $visibility
     * @param bool   $lfs_enabled
     * @param bool   $request_access_enabled
     * @param int    $parent_id
     * @param int    $shared_runners_minutes_limit
     * @return mixed
     */
    public function create($name, $path, $description = null, $visibility = 'private', $lfs_enabled = null, $request_access_enabled = null, $parent_id = null, $shared_runners_minutes_limit = null)
    {
        $params = array(
            'name' => $name,
            'path' => $path,
            'description' => $description,
            'visibility' => $visibility,
            'lfs_enabled' => $lfs_enabled,
            'request_access_enabled' => $request_access_enabled,
            'parent_id' => $parent_id,
            'shared_runners_minutes_limit' => $shared_runners_minutes_limit,
        );

        return $this->post('groups', array_filter($params, 'strlen'));
    }

    /**
     * @param int $id
     * @param array $params
     * @return mixed
     */
    public function update($id, array $params)
    {
        return $this->put('groups/'.$this->encodePath($id), $params);
    }

    /**
     * @param int $group_id
     * @return mixed
     */
    public function remove($group_id)
    {
        return $this->delete('groups/'.$this->encodePath($group_id));
    }

    /**
     * @param int $group_id
     * @param int $project_id
     * @return mixed
     */
    public function transfer($group_id, $project_id)
    {
        return $this->post('groups/'.$this->encodePath($group_id).'/projects/'.$this->encodePath($project_id));
    }

    /**
     * @param integer $id
     * @param array $parameters
     * @return mixed
     */
    public function allMembers($id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('query');

        return $this->get('groups/'.$this->encodePath($id).'/members/all', $resolver->resolve($parameters));
    }

    /**
     * @param int   $id
     * @param array $parameters (
     *
     *     @var string $query A query string to search for members.
     * )
     *
     * @return mixed
     */
    public function members($id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('query');

        return $this->get('groups/'.$this->encodePath($id).'/members', $resolver->resolve($parameters));
    }

    /**
     * @param int $group_id
     * @param int $user_id
     * @param int $access_level
     * @return mixed
     */
    public function addMember($group_id, $user_id, $access_level)
    {
        return $this->post('groups/'.$this->encodePath($group_id).'/members', array(
            'user_id' => $user_id,
            'access_level' => $access_level
        ));
    }

    /**
     * @param int $group_id
     * @param int $user_id
     * @param int $access_level
     * @return mixed
     */
    public function saveMember($group_id, $user_id, $access_level)
    {
        return $this->put('groups/'.$this->encodePath($group_id).'/members/'.$this->encodePath($user_id), array(
            'access_level' => $access_level
        ));
    }

    /**
     * @param int $group_id
     * @param int $user_id
     * @return mixed
     */
    public function removeMember($group_id, $user_id)
    {
        return $this->delete('groups/'.$this->encodePath($group_id).'/members/'.$this->encodePath($user_id));
    }

    /**
     * @param $id
     * @param array $parameters (
     *
     *     @var bool   $archived                    Limit by archived status.
     *     @var string $visibility                  Limit by visibility public, internal, or private.
     *     @var string $order_by                    Return projects ordered by id, name, path, created_at, updated_at, or last_activity_at fields.
     *                                              Default is created_at.
     *     @var string $sort                        Return projects sorted in asc or desc order. Default is desc.
     *     @var string $search                      Return list of authorized projects matching the search criteria.
     *     @var bool   $simple                      Return only the ID, URL, name, and path of each project.
     *     @var bool   $owned                       Limit by projects owned by the current user.
     *     @var bool   $starred                     Limit by projects starred by the current user.
     *     @var bool   $with_issues_enabled         Limit by projects with issues feature enabled. Default is false.
     *     @var bool   $with_merge_requests_enabled Limit by projects with merge requests feature enabled. Default is false.
     *     @var bool   $with_shared                 Include projects shared to this group. Default is true.
     *     @var bool   $include_subgroups           Include projects in subgroups of this group. Default is false.
     *     @var bool   $with_custom_attributes      Include custom attributes in response (admins only).
     * )
     *
     * @return mixed
     */
    public function projects($id, array $parameters = [])
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
        $resolver->setDefined('starred')
            ->setAllowedTypes('starred', 'bool')
            ->setNormalizer('starred', $booleanNormalizer)
        ;
        $resolver->setDefined('with_issues_enabled')
            ->setAllowedTypes('with_issues_enabled', 'bool')
            ->setNormalizer('with_issues_enabled', $booleanNormalizer)
        ;
        $resolver->setDefined('with_merge_requests_enabled')
            ->setAllowedTypes('with_merge_requests_enabled', 'bool')
            ->setNormalizer('with_merge_requests_enabled', $booleanNormalizer)
        ;
        $resolver->setDefined('with_shared')
            ->setAllowedTypes('with_shared', 'bool')
            ->setNormalizer('with_shared', $booleanNormalizer)
        ;
        $resolver->setDefined('include_subgroups')
            ->setAllowedTypes('include_subgroups', 'bool')
            ->setNormalizer('include_subgroups', $booleanNormalizer)
        ;
        $resolver->setDefined('with_custom_attributes')
            ->setAllowedTypes('with_custom_attributes', 'bool')
            ->setNormalizer('with_custom_attributes', $booleanNormalizer)
        ;

        return $this->get('groups/'.$this->encodePath($id).'/projects', $resolver->resolve($parameters));
    }

    /**
     * @param int $group_id
     * @param array $parameters (
     *
     *     @var int[]  $skip_groups   Skip the group IDs passes.
     *     @var bool   $all_available Show all the groups you have access to.
     *     @var string $search        Return list of authorized groups matching the search criteria.
     *     @var string $order_by      Order groups by name or path. Default is name.
     *     @var string $sort          Order groups in asc or desc order. Default is asc.
     *     @var bool   $statistics    Include group statistics (admins only).
     *     @var bool   $owned         Limit by groups owned by the current user.
     * )
     * @return mixed
     */
    public function subgroups($group_id, array $parameters = [])
    {
        $resolver = $this->getGroupSearchResolver();

        return $this->get('groups/'.$this->encodePath($group_id).'/subgroups', $resolver->resolve($parameters));
    }

    /**
     * @param int $group_id
     * @param array $parameters
     * @return mixed
     */
    public function labels($group_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get('groups/'.$this->encodePath($group_id). '/labels', $resolver->resolve($parameters));
    }

    /**
     * @param int $group_id
     * @param array $params
     * @return mixed
     */
    public function addLabel($group_id, array $params)
    {
        return $this->post('groups/'.$this->encodePath($group_id). '/labels', $params);
    }

    /**
     * @param int $group_id
     * @param array $params
     * @return mixed
     */
    public function updateLabel($group_id, array $params)
    {
        return $this->put('groups/'.$this->encodePath($group_id). '/labels', $params);
    }

    /**
     * @param int $group_id
     * @param string $name
     * @return mixed
     */
    public function removeLabel($group_id, $name)
    {
        return $this->delete('groups/'.$this->encodePath($group_id). '/labels', array(
            'name' => $name
        ));
    }

    /**
     * @param int $group_id
     * @param array $parameters
     * @return mixed
     */
    public function variables($group_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getGroupPath($group_id, 'variables'), $resolver->resolve($parameters));
    }

    /**
     * @param int $group_id
     * @param string $key
     * @return mixed
     */
    public function variable($group_id, $key)
    {
        return $this->get($this->getGroupPath($group_id, 'variables/'.$this->encodePath($key)));
    }

    /**
     * @param int $group_id
     * @param string $key
     * @param string $value
     * @param bool $protected
     * @return mixed
     */
    public function addVariable($group_id, $key, $value, $protected = null)
    {
        $payload = array(
            'key' => $key,
            'value' => $value,
        );

        if ($protected) {
            $payload['protected'] = $protected;
        }

        return $this->post($this->getGroupPath($group_id, 'variables'), $payload);
    }

    /**
     * @param int $group_id
     * @param string $key
     * @param string $value
     * @param bool $protected
     * @return mixed
     */
    public function updateVariable($group_id, $key, $value, $protected = null)
    {
        $payload = array(
            'value' => $value,
        );

        if ($protected) {
            $payload['protected'] = $protected;
        }

        return $this->put($this->getGroupPath($group_id, 'variables/'.$this->encodePath($key)), $payload);
    }

    /**
     * @param int $group_id
     * @param string $key
     * @return mixed
     */
    public function removeVariable($group_id, $key)
    {
        return $this->delete($this->getGroupPath($group_id, 'variables/'.$this->encodePath($key)));
    }

    private function getGroupSearchResolver()
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value) {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('skip_groups')
            ->setAllowedTypes('skip_groups', 'array')
            ->setAllowedValues('skip_groups', function (array $value) {
                return count($value) == count(array_filter($value, 'is_int'));
            })
        ;
        $resolver->setDefined('all_available')
            ->setAllowedTypes('all_available', 'bool')
            ->setNormalizer('all_available', $booleanNormalizer)
        ;
        $resolver->setDefined('search');
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['name', 'path'])
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;
        $resolver->setDefined('statistics')
            ->setAllowedTypes('statistics', 'bool')
            ->setNormalizer('statistics', $booleanNormalizer)
        ;
        $resolver->setDefined('owned')
            ->setAllowedTypes('owned', 'bool')
            ->setNormalizer('owned', $booleanNormalizer)
        ;

        return $resolver;
    }
}
