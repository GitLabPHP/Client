<?php

namespace Gitlab\Api;

class MergeRequests extends AbstractApi
{
    public function all($project_id, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('projects/'.urlencode($project_id).'/merge_requests', array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    public function show($project_id, $mr_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/merge_request/'.urlencode($mr_id));
    }

    public function create($project_id, $source, $target, $title, $assignee = null, $target_project_id = null)
    {
        if ($target_project_id && ! is_numeric($target_project_id)) {
            throw new InvalidArgumentException('target_project_id should be numeric, the project name is not allowed');
        }

        return $this->post('projects/'.urlencode($project_id).'/merge_requests', array(
            'source_branch' => $source,
            'target_branch' => $target,
            'title' => $title,
            'assignee_id' => $assignee,
            'target_project_id' => $target_project_id
        ));
    }

    public function update($project_id, $mr_id, array $params)
    {
        return $this->put('projects/'.urlencode($project_id).'/merge_request/'.urlencode($mr_id), $params);
    }

    public function merge($project_id, $mr_id, array $params)
    {
        return $this->put('projects/'.urlencode($project_id).'/merge_request/'.urlencode($mr_id).'/merge', $params);
    }

    public function addComment($project_id, $mr_id, $note)
    {
        return $this->post('projects/'.urlencode($project_id).'/merge_request/'.urlencode($mr_id).'/comments', array(
            'note' => $note
        ));
    }
}
