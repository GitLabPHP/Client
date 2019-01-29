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
     *     @var bool   $archived   Limit by archived status.
     *     @var string $visibility Limit by visibility public, internal, or private.
     *     @var string $order_by   Return projects ordered by id, name, path, created_at, updated_at, or last_activity_at fields.
     *                             Default is created_at.
     *     @var string $sort       Return projects sorted in asc or desc order. Default is desc.
     *     @var string $search     Return list of authorized projects matching the search criteria.
     *     @var bool   $simple     Return only the ID, URL, name, and path of each project.
     *     @var bool   $owned      Limit by projects owned by the current user.
     *     @var bool   $starred    Limit by projects starred by the current user.
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

        return $this->get('groups/'.$this->encodePath($id).'/projects', $resolver->resolve($parameters));
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function allprojects($id)
    {
        $projects= $this->get('groups/'.$this->encodePath($id).'/projects');
        $allprojects=$projects;
        $page=1;
	while ($projects!=null){
		$page++;
		$projects= $this->get('groups/'.$this->encodePath($id).'/projects?page='.$page);
		$allprojects=array_merge($allprojects,$projects);
        }
        return $allprojects;
    }

    /**
     * @param int $groupId
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
    public function subgroups($groupId, array $parameters = [])
    {
        $resolver = $this->getGroupSearchResolver();

        return $this->get('groups/'.$this->encodePath($groupId).'/subgroups', $resolver->resolve($parameters));
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
