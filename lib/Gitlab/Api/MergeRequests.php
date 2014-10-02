<?php

namespace Gitlab\Api;

class MergeRequests extends AbstractApi
{
    const STATE_ALL = 'all';
    const STATE_MERGED = 'merged';
    const STATE_OPENED = 'opened';
    const STATE_CLOSED = 'closed';

    public function all($project_id, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->getMrList($project_id, $page, $per_page, self::STATE_ALL);
    }

    public function merged($project_id, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->getMrList($project_id, $page, $per_page, self::STATE_MERGED);
    }

    public function opened($project_id, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->getMrList($project_id, $page, $per_page, self::STATE_OPENED);
    }

    public function closed($project_id, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->getMrList($project_id, $page, $per_page, self::STATE_CLOSED);
    }

    public function show($project_id, $mr_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/merge_request/'.urlencode($mr_id));
    }

    public function create($project_id, $source, $target, $title, $assignee = null, $target_project_id = null, $description = null)
    {
        if ($target_project_id && ! is_numeric($target_project_id)) {
            throw new \InvalidArgumentException('target_project_id should be numeric, the project name is not allowed');
        }

        return $this->post('projects/'.urlencode($project_id).'/merge_requests', array(
            'source_branch' => $source,
            'target_branch' => $target,
            'title' => $title,
            'assignee_id' => $assignee,
            'target_project_id' => $target_project_id,
            'description' => $description
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

    public function showComments($project_id, $mr_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/merge_request/'.urlencode($mr_id).'/comments');
    }

    public function addComment($project_id, $mr_id, $note)
    {
        return $this->post('projects/'.urlencode($project_id).'/merge_request/'.urlencode($mr_id).'/comments', array(
            'note' => $note
        ));
    }

    protected function getMrList($project_id, $page, $per_page, $state = self::STATE_ALL)
    {
        return $this->get('projects/'.urlencode($project_id).'/merge_requests', array(
            'page' => $page,
            'per_page' => $per_page,
            'state' => $state
        ));
    }
}
