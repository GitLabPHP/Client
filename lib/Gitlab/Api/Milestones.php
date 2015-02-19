<?php namespace Gitlab\Api;

class Milestones extends AbstractApi
{
    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function all($project_id, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get($this->getProjectPath($project_id, '/milestones'), array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    /**
     * @param int $project_id
     * @param int $milestone_id
     * @return mixed
     */
    public function show($project_id, $milestone_id)
    {
        return $this->get($this->getProjectPath($project_id, '/milestones/'.urlencode($milestone_id)));
    }

    /**
     * @param int $project_id
     * @param array $params
     * @return mixed
     */
    public function create($project_id, array $params)
    {
        return $this->post($this->getProjectPath($project_id, '/milestones'), $params);
    }

    /**
     * @param int $project_id
     * @param int $milestone_id
     * @param array $params
     * @return mixed
     */
    public function update($project_id, $milestone_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, '/milestones/'.urlencode($milestone_id)), $params);
    }
}
