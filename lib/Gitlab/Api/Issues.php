<?php

namespace Gitlab\Api;

class Issues extends AbstractApi
{
    public function all($project_id = null)
    {
        $path = $project_id === null ? 'issues' : 'projects/'.urlencode($project_id).'/issues';

        return $this->get($path);
    }

    public function show($project_id, $issue_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/issues/'.urlencode($issue_id));
    }

    public function create($project_id, array $params)
    {
        return $this->post('projects/'.urlencode($project_id).'/issues', $params);
    }

    public function update($project_id, $issue_id, array $params)
    {
        return $this->put('projects/'.urlencode($project_id).'/issues/'.urlencode($issue_id), $params);
    }

    public function remove($project_id, $issue_id)
    {
        return $this->delete('projects/'.urlencode($project_id).'/issues/'.urlencode($issue_id));
    }

    public function addComment($project_id, $issue_id, array $params)
    {
        return $this->post('projects/'.urlencode($project_id).'/issues/'.urlencode($issue_id).'/comments', $params);
    }

}
