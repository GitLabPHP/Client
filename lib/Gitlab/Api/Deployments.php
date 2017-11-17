<?php namespace Gitlab\Api;

class Deployments extends AbstractApi
{
    /**
     * @param int $project_id
     * @return mixed
     */
    public function all($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deployments'));
    }

    /**
     * @param int $project_id
     * @param string $deployment_id
     * @return mixed
     */
    public function show($project_id, $deployment_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deployments/' . $deployment_id));
    }
}
