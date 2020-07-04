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
     * @param int|string $source_project_id
     * @param int|string $source_issue_iid
     * @param int|string $target_project_id
     * @param int|string $target_issue_iid
     *
     * @return mixed
     */
    public function create($source_project_id, $source_issue_iid, $target_project_id, $target_issue_iid)
    {
        return $this->post($this->getProjectPath($source_project_id, 'issues/'.$this->encodePath($source_issue_iid).'/links'), [
            'target_project_id' => $target_project_id,
            'target_issue_iid' => $target_issue_iid,
        ]);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int        $issue_link_id
     *
     * @return mixed
     */
    public function remove($project_id, $issue_iid, $issue_link_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)).'/links/'.$this->encodePath($issue_link_id));
    }
}
