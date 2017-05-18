<?php namespace Gitlab\Api;

class Tags extends AbstractApi
{
    /**
     * @param int $project_id
     * @return mixed
     */
    public function all($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/tags'));
    }

    /**
     * @param int $project_id
     * @param string $tag_name
     * @return mixed
     */
    public function show($project_id, $tag_name)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/tags/'.$tag_name));
    }

    /**
     * @param int $project_id
     * @param array $params
     * @return mixed
     */
    public function create($project_id, array $params = array())
    {
        return $this->post($this->getProjectPath($project_id, "repository/tags"), $params);
    }

    /**
     * @param int $project_id
     * @param string $tag_name
     * @return mixed
     */
    public function remove($project_id, $tag_name)
    {
        return $this->delete($this->getProjectPath($project_id, 'repository/tags/'.$tag_name));
    }
}
