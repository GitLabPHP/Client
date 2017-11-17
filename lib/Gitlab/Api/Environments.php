<?php namespace Gitlab\Api;

class Environments extends AbstractApi
{
    /**
     * @param int $project_id
     * @return mixed
     */
    public function all($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'environments'));
    }

    /**
     * @param int $project_id
     * @param array $params
     * @return mixed
     */
    public function create($project_id, array $params = array())
    {
        return $this->post($this->getProjectPath($project_id, "environment"), $params);
    }

    /**
     * @param int $project_id
     * @param string $environment_id
     * @return mixed
     */
    public function remove($project_id, $environment_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'environments/'.$environment_id));
    }
}
