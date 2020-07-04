<?php

namespace Gitlab\Api;

class GroupsMilestones extends AbstractApi
{
    const STATE_ACTIVE = 'active';

    const STATE_CLOSED = 'closed';

    /**
     * @param int   $group_id
     * @param array $parameters {
     *
     *     @var int[]  $iids   return only the milestones having the given iids
     *     @var string $state  return only active or closed milestones
     *     @var string $search Return only milestones with a title or description matching the provided string.
     * }
     *
     * @return mixed
     */
    public function all($group_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('iids')
            ->setAllowedTypes('iids', 'array')
            ->setAllowedValues('iids', function (array $value) {
                return count($value) == count(array_filter($value, 'is_int'));
            })
        ;
        $resolver->setDefined('state')
            ->setAllowedValues('state', [self::STATE_ACTIVE, self::STATE_CLOSED])
        ;
        $resolver->setDefined('search');

        return $this->get($this->getGroupPath($group_id, 'milestones'), $resolver->resolve($parameters));
    }

    /**
     * @param int $group_id
     * @param int $milestone_id
     *
     * @return mixed
     */
    public function show($group_id, $milestone_id)
    {
        return $this->get($this->getGroupPath($group_id, 'milestones/'.$this->encodePath($milestone_id)));
    }

    /**
     * @param int   $group_id
     * @param array $params
     *
     * @return mixed
     */
    public function create($group_id, array $params)
    {
        return $this->post($this->getGroupPath($group_id, 'milestones'), $params);
    }

    /**
     * @param int   $group_id
     * @param int   $milestone_id
     * @param array $params
     *
     * @return mixed
     */
    public function update($group_id, $milestone_id, array $params)
    {
        return $this->put($this->getGroupPath($group_id, 'milestones/'.$this->encodePath($milestone_id)), $params);
    }

    /**
     * @param int $group_id
     * @param int $milestone_id
     *
     * @return mixed
     */
    public function remove($group_id, $milestone_id)
    {
        return $this->delete($this->getGroupPath($group_id, 'milestones/'.$this->encodePath($milestone_id)));
    }

    /**
     * @param int $group_id
     * @param int $milestone_id
     *
     * @return mixed
     */
    public function issues($group_id, $milestone_id)
    {
        return $this->get($this->getGroupPath($group_id, 'milestones/'.$this->encodePath($milestone_id).'/issues'));
    }

    /**
     * @param int $group_id
     * @param int $milestone_id
     *
     * @return mixed
     */
    public function mergeRequests($group_id, $milestone_id)
    {
        return $this->get($this->getGroupPath($group_id, 'milestones/'.$this->encodePath($milestone_id).'/merge_requests'));
    }
}
