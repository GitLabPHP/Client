<?php namespace Gitlab\Api;

class Issues extends AbstractApi
{
    /**
     * @param int $project_id
     * @param array $parameters (
     *
     *     @var string $state     Return all issues or just those that are opened or closed.
     *     @var string $labels    Comma-separated list of label names, issues must have all labels to be returned.
     *                            No+Label lists all issues with no labels.
     *     @var string $milestone The milestone title.
     *     @var string scope      Return issues for the given scope: created-by-me, assigned-to-me or all. Defaults to created-by-me
     *     @var int[]  $iids      Return only the issues having the given iid.
     *     @var string $order_by  Return requests ordered by created_at or updated_at fields. Default is created_at.
     *     @var string $sort      Return requests sorted in asc or desc order. Default is desc.
     *     @var string $search    Search issues against their title and description.
     * )
     *
     * @return mixed
     */
    public function all($project_id = null, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        $resolver->setDefined('state')
            ->setAllowedValues('state', ['opened', 'closed'])
        ;
        $resolver->setDefined('labels');
        $resolver->setDefined('milestone');
        $resolver->setDefined('iids')
            ->setAllowedTypes('iids', 'array')
            ->setAllowedValues('iids', function (array $value) {
                return count($value) == count(array_filter($value, 'is_int'));
            })
        ;
        $resolver->setDefined('scope')
            ->setAllowedValues('scope', ['created-by-me', 'assigned-to-me', 'all'])
        ;
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['created_at', 'updated_at'])
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;
        $resolver->setDefined('search');
        $resolver->setDefined('assignee_id')
            ->setAllowedTypes('assignee_id', 'integer')
        ;

        $path = $project_id === null ? 'issues' : $this->getProjectPath($project_id, 'issues');

        return $this->get($path, $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @return mixed
     */
    public function show($project_id, $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)));
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
     * @param int $issue_iid
     * @param array $params
     * @return mixed
     */
    public function update($project_id, $issue_iid, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)), $params);
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @param int $to_project_id
     * @return mixed
     */
    public function move($project_id, $issue_iid, $to_project_id)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)).'/move', array(
            'to_project_id' => $to_project_id
        ));
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @return mixed
     */
    public function remove($project_id, $issue_iid)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)));
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @return mixed
     */
    public function showComments($project_id, $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)).'/notes');
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @param int $note_id
     * @return mixed
     */
    public function showComment($project_id, $issue_iid, $note_id)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)).'/notes/'.$this->encodePath($note_id));
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @param string|array $body
     * @return mixed
     */
    public function addComment($project_id, $issue_iid, $body)
    {
        // backwards compatibility
        if (is_array($body)) {
            $params = $body;
        } else {
            $params = array('body' => $body);
        }

        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/notes'), $params);
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @param int $note_id
     * @param string $body
     * @return mixed
     */
    public function updateComment($project_id, $issue_iid, $note_id, $body)
    {
        return $this->put($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/notes/'.$this->encodePath($note_id)), array(
            'body' => $body
        ));
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @param int $note_id
     * @return mixed
     */
    public function removeComment($project_id, $issue_iid, $note_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/notes/'.$this->encodePath($note_id)));
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @return mixed
     */
    public function showDiscussions($project_id, $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)).'/discussions');
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @param string $discussion_id
     * @return mixed
     */
    public function showDiscussion($project_id, $issue_iid, $discussion_id)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)).'/discussions/'.$this->encodePath($discussion_id));
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @param string|array $body
     * @return mixed
     */
    public function addDiscussion($project_id, $issue_iid, $body)
    {
        // backwards compatibility
        if (is_array($body)) {
            $params = $body;
        } else {
            $params = array('body' => $body);
        }

        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/discussions'), $params);
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @param string $discussion_id
     * @param string|array $body
     * @return mixed
     */
    public function addDiscussionNote($project_id, $issue_iid, $discussion_id, $body)
    {
        // backwards compatibility
        if (is_array($body)) {
            $params = $body;
        } else {
            $params = array('body' => $body);
        }

        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/discussions/'.$this->encodePath($discussion_id).'/notes'), $params);
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @param string $discussion_id
     * @param int $note_id
     * @param string $body
     * @return mixed
     */
    public function updateDiscussionNote($project_id, $issue_iid, $discussion_id, $note_id, $body)
    {
        return $this->put($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/discussions/'.$this->encodePath($discussion_id).'/notes/'.$this->encodePath($note_id)), array(
            'body' => $body
        ));
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @param string $discussion_id
     * @param int $note_id
     * @return mixed
     */
    public function removeDiscussionNote($project_id, $issue_iid, $discussion_id, $note_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/discussions/'.$this->encodePath($discussion_id).'/notes/'.$this->encodePath($note_id)));
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @param string $duration
     * @return mixed
     */
    public function setTimeEstimate($project_id, $issue_iid, $duration)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/time_estimate'), array('duration' => $duration));
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @return mixed
     */
    public function resetTimeEstimate($project_id, $issue_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/reset_time_estimate'));
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @param string $duration
     * @return mixed
     */
    public function addSpentTime($project_id, $issue_iid, $duration)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/add_spent_time'), array('duration' => $duration));
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @return mixed
     */
    public function resetSpentTime($project_id, $issue_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/reset_spent_time'));
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     * @return mixed
     */
    public function getTimeStats($project_id, $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid) .'/time_stats'));
    }

    /**
     * @param int $project_id
     * @param int $issue_iid
     *
     * @return mixed
     */
    public function awardEmoji($project_id, $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/award_emoji'));
    }

    /**
    * @param int $project_id
    * @param int $issue_iid
    * @return mixed
    */
    public function closedByMergeRequests($project_id, $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)).'/closed_by');
    }
}
