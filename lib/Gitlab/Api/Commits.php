<?php namespace Gitlab\Api;

class Commits extends AbstractApi
{
    /**
     * @param int $project_id
     * @return mixed
     */
    public function all($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits'));
    }

    /**
     * @param int $project_id
     * @param string $sha
     * @return mixed
     */
    public function show($project_id, $sha)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/'.$sha));
    }

    /**
     * @param int $project_id
     * @param string $sha
     * @return mixed
     */
    public function diff($project_id, $sha)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/'.$sha.'/diff'));
    }

    /**
     * @param int $project_id
     * @param string $sha
     * @return mixed
     */
    public function comments($project_id, $sha)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/'.$sha.'/comments'));
    }
}
