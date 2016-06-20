<?php namespace Gitlab\Api;

class Issues extends AbstractApi
{
    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     * @param array $params
     * @return mixed
     */
    public function all($project_id = null, $page = 1, $per_page = self::PER_PAGE, array $params = array())
    {
        $path = $project_id === null ? 'issues' : $this->getProjectPath($project_id, 'issues');

        $params = array_intersect_key($params, array('labels' => '', 'state' => '', 'sort' => '', 'order_by' => '', 'milestone' => ''));
        $params = array_merge(array(
            'page' => $page,
            'per_page' => $per_page
        ), $params);

        return $this->get($path, $params);
    }

    /**
     * @param int $project_id
     * @param int $issue_id
     * @return mixed
     */
    public function show($project_id, $issue_id)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_id)));
    }

    /**
     * @param int $project_id
     * @param array $params
     * @return mixed
     */
    public function create($project_id, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'issues'), $params);
    }

    /**
     * @param int $project_id
     * @param int $issue_id
     * @param array $params
     * @return mixed
     */
    public function update($project_id, $issue_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_id)), $params);
    }

    /**
     * @param int $project_id
     * @param int $issue_id
     * @return mixed
     */
    public function showComments($project_id, $issue_id)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_id)).'/notes');
    }

    /**
     * @param int $project_id
     * @param int $issue_id
     * @param int $note_id
     * @return mixed
     */
    public function showComment($project_id, $issue_id, $note_id)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_id)).'/notes/'.$this->encodePath($note_id));
    }

    /**
     * @param int $project_id
     * @param int $issue_id
     * @param string|array $body
     * @return mixed
     */
    public function addComment($project_id, $issue_id, $body)
    {
        // backwards compatibility
        if (is_array($body)) {
            $params = $body;
        } else {
            $params = array('body' => $body);
        }

        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_id).'/notes'), $params);
    }

    /**
     * @param int $project_id
     * @param int $issue_id
     * @param int $note_id
     * @param string $body
     * @return mixed
     */
    public function updateComment($project_id, $issue_id, $note_id, $body)
    {
        return $this->put($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_id).'/notes/'.$this->encodePath($note_id)), array(
            'body' => $body
        ));
    }
}
