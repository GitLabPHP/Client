<?php

namespace Gitlab\Api;

class Deployments extends AbstractApi
{
    /**
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function all($project_id, array $parameters = [])
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
    public function show($project_id, $deployment_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deployments/'.$deployment_id));
    }
}
