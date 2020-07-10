<?php

namespace Gitlab\Api;

class Wiki extends AbstractApi
{
    /**
     * @param int|string $project_id
     * @param array      $params
     *
     * @return mixed
     */
    public function create($project_id, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'wikis'), $params);
    }

    /**
     * @param int|string $project_id
     * @param string     $wiki_slug
     *
     * @return mixed
     */
    public function show($project_id, $wiki_slug)
    {
        return $this->get($this->getProjectPath($project_id, 'wikis/'.$this->encodePath($wiki_slug)));
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function showAll($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'wikis'));
    }

    /**
     * @param int|string $project_id
     * @param string     $wiki_slug
     * @param array      $params
     *
     * @return mixed
     */
    public function update($project_id, $wiki_slug, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'wikis/'.$this->encodePath($wiki_slug)), $params);
    }

    /**
     * @param int|string $project_id
     * @param string     $wiki_slug
     *
     * @return mixed
     */
    public function remove($project_id, $wiki_slug)
    {
        return $this->delete($this->getProjectPath($project_id, 'wikis/'.$this->encodePath($wiki_slug)));
    }
}
