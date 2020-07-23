<?php

namespace Gitlab\Api;

class IssueLinks extends AbstractApi
{
    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function all($project_id, $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)).'/links');
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int|string $target_project_id
     * @param int        $target_issue_iid
     * @param array      $parameters        {
     *
     *     @var string $link_type
     * }
     *
     * @return mixed
     */
    public function create($project_id, $issue_iid, $target_project_id, $target_issue_iid, array $parameters = [])
    {
        $parameters['target_project_id'] = $target_project_id;
        $parameters['target_issue_iid'] = $target_issue_iid;

        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/links'), $parameters);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int|string $issue_link_id
     * @param array      $parameters    {
     *
     *     @var string $link_type
     * }
     *
     * @return mixed
     */
    public function remove($project_id, $issue_iid, $issue_link_id, array $parameters = [])
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)).'/links/'.$this->encodePath($issue_link_id), $parameters);
    }
}
