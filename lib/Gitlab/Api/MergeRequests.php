<?php namespace Gitlab\Api;

class MergeRequests extends AbstractApi
{
    const STATE_ALL = 'all';
    const STATE_MERGED = 'merged';
    const STATE_OPENED = 'opened';
    const STATE_CLOSED = 'closed';

    const ORDER_BY = 'created_at';
    const SORT = 'asc';

    /**
     * @param int $project_id
     * @param string $state
     * @param int $page
     * @param int $per_page
     * @param string $order_by
     * @param string $sort
     * @return mixed
     */
    public function getList($project_id, $state = self::STATE_ALL, $page = 1, $per_page = self::PER_PAGE, $order_by = self::ORDER_BY, $sort = self::SORT)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests'), array(
            'page' => $page,
            'per_page' => $per_page,
            'state' => $state,
            'order_by' => $order_by,
            'sort' => $sort
        ));
    }

    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     * @param string $order_by
     * @param string $sort
     * @return mixed
     */
    public function all($project_id, $page = 1, $per_page = self::PER_PAGE, $order_by = self::ORDER_BY, $sort = self::SORT)
    {
        return $this->getList($project_id, self::STATE_ALL, $page, $per_page, $order_by, $sort);
    }

    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     * @param string $order_by
     * @param string $sort
     * @return mixed
     */
    public function merged($project_id, $page = 1, $per_page = self::PER_PAGE, $order_by = self::ORDER_BY, $sort = self::SORT)
    {
        return $this->getList($project_id, self::STATE_MERGED, $page, $per_page, $order_by, $sort);
    }

    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     * @param string $order_by
     * @param string $sort
     * @return mixed
     */
    public function opened($project_id, $page = 1, $per_page = self::PER_PAGE, $order_by = self::ORDER_BY, $sort = self::SORT)
    {
        return $this->getList($project_id, self::STATE_OPENED, $page, $per_page, $order_by, $sort);
    }

    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     * @param string $order_by
     * @param string $sort
     * @return mixed
     */
    public function closed($project_id, $page = 1, $per_page = self::PER_PAGE, $order_by = self::ORDER_BY, $sort = self::SORT)
    {
        return $this->getList($project_id, self::STATE_CLOSED, $page, $per_page, $order_by, $sort);
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @return mixed
     */
    public function show($project_id, $mr_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_request/'.rawurlencode($mr_id)));
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
        return $this->post($this->getProjectPath($project_id, 'merge_requests'), array(
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
        return $this->put($this->getProjectPath($project_id, 'merge_request/'.rawurlencode($mr_id)), $params);
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @param string $message
     * @return mixed
     */
    public function merge($project_id, $mr_id, $message = null)
    {
        if (is_array($message)) {
            $params = $message;
        } else {
            $params = array('merge_commit_message' => $message);
        }

        return $this->put($this->getProjectPath($project_id, 'merge_request/'.rawurlencode($mr_id).'/merge'), $params);
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @return mixed
     */
    public function showComments($project_id, $mr_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_request/'.rawurlencode($mr_id).'/comments'));
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @param int $note
     * @return mixed
     */
    public function addComment($project_id, $mr_id, $note)
    {
        return $this->post($this->getProjectPath($project_id, 'merge_request/'.rawurlencode($mr_id).'/comments'), array(
            'note' => $note
        ));
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @return mixed
     */
    public function changes($project_id, $mr_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_request/'.rawurlencode($mr_id).'/changes'));
    }
}
