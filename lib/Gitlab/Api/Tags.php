<?php

namespace Gitlab\Api;

class Tags extends AbstractApi
{
    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function all($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/tags'));
    }

    /**
     * @param int|string $project_id
     * @param string     $tag_name
     *
     * @return mixed
     */
    public function show($project_id, $tag_name)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/tags/'.$this->encodePath($tag_name)));
    }

    /**
     * @param int|string $project_id
     * @param array      $params
     *
     * @return mixed
     */
    public function create($project_id, array $params = [])
    {
        return $this->post($this->getProjectPath($project_id, 'repository/tags'), $params);
    }

    /**
     * @param int|string $project_id
     * @param string     $tag_name
     *
     * @return mixed
     */
    public function remove($project_id, $tag_name)
    {
        return $this->delete($this->getProjectPath($project_id, 'repository/tags/'.$this->encodePath($tag_name)));
    }

    /**
     * @param int|string $project_id
     * @param string     $tag_name
     * @param array      $params
     *
     * @return mixed
     */
    public function createRelease($project_id, $tag_name, array $params = [])
    {
        return $this->post($this->getProjectPath($project_id, 'repository/tags/'.$this->encodePath($tag_name).'/release'), $params);
    }

    /**
     * @param int|string $project_id
     * @param string     $tag_name
     * @param array      $params
     *
     * @return mixed
     */
    public function updateRelease($project_id, $tag_name, array $params = [])
    {
        return $this->put($this->getProjectPath($project_id, 'repository/tags/'.$this->encodePath($tag_name).'/release'), $params);
    }
}
