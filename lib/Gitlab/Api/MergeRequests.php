<?php namespace Gitlab\Api;

class MergeRequests extends AbstractApi
{
    const STATE_ALL = 'all';
    const STATE_MERGED = 'merged';
    const STATE_OPENED = 'opened';
    const STATE_CLOSED = 'closed';

    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function all($project_id, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->getMrList($project_id, $page, $per_page, self::STATE_ALL);
    }

    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function merged($project_id, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->getMrList($project_id, $page, $per_page, self::STATE_MERGED);
    }

    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function opened($project_id, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->getMrList($project_id, $page, $per_page, self::STATE_OPENED);
    }

    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function closed($project_id, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->getMrList($project_id, $page, $per_page, self::STATE_CLOSED);
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @return mixed
     */
    public function show($project_id, $mr_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/merge_request/'.urlencode($mr_id));
    }

    /**
     * @param int $project_id
     * @param string $source
     * @param string $target
     * @param string $title
     * @param int $assignee
     * @param int $target_project_id
     * @param string $description
     * @return mixed
     */
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

    /**
     * @param int $project_id
     * @param int $mr_id
     * @param array $params
     * @return mixed
     */
    public function update($project_id, $mr_id, array $params)
    {
        return $this->put('projects/'.urlencode($project_id).'/merge_request/'.urlencode($mr_id), $params);
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @param array $params
     * @return mixed
     */
    public function merge($project_id, $mr_id, array $params)
    {
        return $this->put('projects/'.urlencode($project_id).'/merge_request/'.urlencode($mr_id).'/merge', $params);
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @return mixed
     */
    public function showComments($project_id, $mr_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/merge_request/'.urlencode($mr_id).'/comments');
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @param int $note
     * @return mixed
     */
    public function addComment($project_id, $mr_id, $note)
    {
        return $this->post('projects/'.urlencode($project_id).'/merge_request/'.urlencode($mr_id).'/comments', array(
            'note' => $note
        ));
    }

    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     * @param string $state
     * @return mixed
     */
    protected function getMrList($project_id, $page, $per_page, $state = self::STATE_ALL)
    {
        return $this->get('projects/'.urlencode($project_id).'/merge_requests', array(
            'page' => $page,
            'per_page' => $per_page,
            'state' => $state
        ));
    }
}
