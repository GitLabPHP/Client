<?php

namespace Gitlab\Api;

class Milestones extends AbstractApi
{
    /**
     * @var string
     */
    const STATE_ACTIVE = 'active';

    /**
     * @var string
     */
    const STATE_CLOSED = 'closed';

    /**
     * @param int|string $project_id
     * @param array      $parameters {
     *
     *     @var int[]  $iids   return only the milestones having the given iids
     *     @var string $state  return only active or closed milestones
     *     @var string $search Return only milestones with a title or description matching the provided string.
     * }
     *
     * @return mixed
     */
    public function all($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('iids')
            ->setAllowedTypes('iids', 'array')
            ->setAllowedValues('iids', function (array $value) {
                return \count($value) === \count(\array_filter($value, 'is_int'));
            })
        ;
        $resolver->setDefined('state')
            ->setAllowedValues('state', [self::STATE_ACTIVE, self::STATE_CLOSED])
        ;
        $resolver->setDefined('search');

        return $this->get($this->getProjectPath($project_id, 'milestones'), $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param int        $milestone_id
     *
     * @return mixed
     */
    public function show($project_id, $milestone_id)
    {
        return $this->get($this->getProjectPath($project_id, 'milestones/'.$this->encodePath($milestone_id)));
    }

    /**
     * @param int|string $project_id
     * @param array      $params
     *
     * @return mixed
     */
    public function create($project_id, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'milestones'), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $milestone_id
     * @param array      $params
     *
     * @return mixed
     */
    public function update($project_id, $milestone_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'milestones/'.$this->encodePath($milestone_id)), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $milestone_id
     *
     * @return mixed
     */
    public function remove($project_id, $milestone_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'milestones/'.$this->encodePath($milestone_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $milestone_id
     *
     * @return mixed
     */
    public function issues($project_id, $milestone_id)
    {
        return $this->get($this->getProjectPath($project_id, 'milestones/'.$this->encodePath($milestone_id).'/issues'));
    }
}
