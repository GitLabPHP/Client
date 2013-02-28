<?php

namespace Gitlab\Api;

class MergeRequests extends AbstractApi
{
    public function all($project_id, $page = 1, $per_page = 20)
    {
        $path = 'projects/'.urlencode($project_id).'/merge_requests';
        return $this->get($path, array('page' => $page, 'per_page' => $per_page));
    }

    public function show($project_id, $mr_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/merge_request/'.urlencode($mr_id));
    }

    public function create($project_id, $source, $target, $title, $assignee = null)
    {
        return $this->post('projects/'.urlencode($project_id).'/merge_requests', array(
            'source_branch' => $source,
            'target_branch' => $target,
            'title' => $title,
            'assignee_id' => $assignee
        ));
    }

    public function update($project_id, $mr_id, array $params)
    {
        return $this->put('projects/'.urlencode($project_id).'/merge_request/'.urlencode($mr_id), $params);
    }

    public function addComment($project_id, $mr_id, $note)
    {
        return $this->post('projects/'.urlencode($project_id).'/merge_request/'.urlencode($mr_id).'/comments', array(
            'note' => $note
        ));
    }
}
