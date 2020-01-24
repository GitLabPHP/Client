<?php namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

class MergeRequests extends AbstractApi
{
    const STATE_ALL = 'all';
    const STATE_MERGED = 'merged';
    const STATE_OPENED = 'opened';
    const STATE_CLOSED = 'closed';

    /**
     * @param int   $project_id
     * @param array $parameters {
     *
     *     @var int[]              $iids           Return the request having the given iid.
     *     @var string             $state          Return all merge requests or just those that are opened, closed, or
     *                                             merged.
     *     @var string             $order_by       Return requests ordered by created_at or updated_at fields. Default
     *                                             is created_at.
     *     @var string             $sort           Return requests sorted in asc or desc order. Default is desc.
     *     @var string             $milestone      Return merge requests for a specific milestone.
     *     @var string             $view           If simple, returns the iid, URL, title, description, and basic state
     *                                             of merge request.
     *     @var string             $labels         Return merge requests matching a comma separated list of labels.
     *     @var \DateTimeInterface $created_after  Return merge requests created after the given time (inclusive).
     *     @var \DateTimeInterface $created_before Return merge requests created before the given time (inclusive).
     * }
     *
     * @throws UndefinedOptionsException If an option name is undefined.
     * @throws InvalidOptionsException   If an option doesn't fulfill the specified validation rules.
     *
     * @return mixed
     */
    public function all($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value) {
            return $value->format('c');
        };
        $resolver->setDefined('iids')
            ->setAllowedTypes('iids', 'array')
            ->setAllowedValues('iids', function (array $value) {
                return count($value) == count(array_filter($value, 'is_int'));
            })
        ;
        $resolver->setDefined('state')
            ->setAllowedValues('state', ['all', 'opened', 'merged', 'closed'])
        ;
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['created_at', 'updated_at'])
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;
        $resolver->setDefined('milestone');
        $resolver->setDefined('view')
            ->setAllowedValues('view', ['simple'])
        ;
        $resolver->setDefined('labels');
        $resolver->setDefined('created_after')
            ->setAllowedTypes('created_after', \DateTimeInterface::class)
            ->setNormalizer('created_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('created_before')
            ->setAllowedTypes('created_before', \DateTimeInterface::class)
            ->setNormalizer('created_before', $datetimeNormalizer)
        ;

        $resolver->setDefined('updated_after')
            ->setAllowedTypes('updated_after', \DateTimeInterface::class)
            ->setNormalizer('updated_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('updated_before')
            ->setAllowedTypes('updated_before', \DateTimeInterface::class)
            ->setNormalizer('updated_before', $datetimeNormalizer)
        ;

        $resolver->setDefined('scope')
            ->setAllowedValues('scope', ['created_by_me', 'assigned_to_me', 'all'])
        ;
        $resolver->setDefined('author_id')
            ->setAllowedTypes('author_id', 'integer');

        $resolver->setDefined('assignee_id')
            ->setAllowedTypes('assignee_id', 'integer');

        $resolver->setDefined('search');
        $resolver->setDefined('source_branch');
        $resolver->setDefined('target_branch');

        return $this->get($this->getProjectPath($project_id, 'merge_requests'), $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @return mixed
     */
    public function show($project_id, $mr_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_id)));
    }

    /**
     * @param int $project_id
     * @param string $source
     * @param string $target
     * @param string $title
     * @param int $assignee @deprecated will be moved into $optionalParams
     * @param int $target_project_id @deprecated will be moved into $optionalParams
     * @param string $description @deprecated will be moved into $optionalParams
     * @param array $optionalParams
     * @return mixed
     */
    public function create($project_id, $source, $target, $title, $assignee = null, $target_project_id = null, $description = null, array $optionalParams = [])
    {
        $baseParams = [
            'source_branch' => $source,
            'target_branch' => $target,
            'title' => $title,
            'assignee_id' => $assignee,
            'description' => $description,
            'target_project_id' => $target_project_id,
        ];

        return $this->post(
            $this->getProjectPath($project_id, 'merge_requests'),
            array_merge($baseParams, $optionalParams)
        );
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @param array $params
     * @return mixed
     */
    public function update($project_id, $mr_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_id)), $params);
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

        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_id).'/merge'), $params);
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     *
     * @return mixed
     */
    public function showNotes($project_id, $mr_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_id).'/notes'));
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @param string $note
     * @return mixed
     */
    public function addNote($project_id, $mr_id, $note)
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_id).'/notes'), array(
            'body' => $note
        ));
    }

    /**
     * @param int $projectId
     * @param int $mrId
     * @param int $noteId
     * @return mixed
     */
    public function removeNote($projectId, $mrId, $noteId)
    {
        return $this->delete($this->getProjectPath($projectId, 'merge_requests/'.$this->encodePath($mrId).'/notes/'.$this->encodePath($noteId)));
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @return mixed
     */
    public function showComments($project_id, $mr_id)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.1 and will be removed in 10.0. Use the showNotes() method instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->showNotes($project_id, $mr_id);
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @param string $note
     * @return mixed
     */
    public function addComment($project_id, $mr_id, $note)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.1 and will be removed in 10.0. Use the addNote() method instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->addNote($project_id, $mr_id, $note);
    }

    /**
     * @param int $project_id
     * @param int $mr_iid
     * @return mixed
     */
    public function showDiscussions($project_id, $mr_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid)).'/discussions');
    }

    /**
     * @param int $project_id
     * @param int $mr_iid
     * @param string $discussion_id
     * @return mixed
     */
    public function showDiscussion($project_id, $mr_iid, $discussion_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid)).'/discussions/'.$this->encodePath($discussion_id));
    }

    /**
     * @param int $project_id
     * @param int $mr_iid
     * @param array $params
     * @return mixed
     */
    public function addDiscussion($project_id, $mr_iid, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/discussions'), $params);
    }

    /**
     * @param int $project_id
     * @param int $mr_iid
     * @param string $discussion_id
     * @param bool $resolved
     * @return mixed
     */
    public function resolveDiscussion($project_id, $mr_iid, $discussion_id, $resolved = true)
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/discussions/'.$this->encodePath($discussion_id)), array(
            'resolved' => $resolved
        ));
    }

    /**
     * @param int $project_id
     * @param int $mr_iid
     * @param string $discussion_id
     * @param string|array $body
     * @return mixed
     */
    public function addDiscussionNote($project_id, $mr_iid, $discussion_id, $body)
    {
        // backwards compatibility
        if (is_array($body)) {
            $params = $body;
        } else {
            $params = array('body' => $body);
        }

        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/discussions/'.$this->encodePath($discussion_id).'/notes'), $params);
    }

    /**
     * @param int $project_id
     * @param int $mr_iid
     * @param string $discussion_id
     * @param int $note_id
     * @param array $params
     * @return mixed
     */
    public function updateDiscussionNote($project_id, $mr_iid, $discussion_id, $note_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/discussions/'.$this->encodePath($discussion_id).'/notes/'.$this->encodePath($note_id)), $params);
    }

    /**
     * @param int $project_id
     * @param int $mr_iid
     * @param string $discussion_id
     * @param int $note_id
     * @return mixed
     */
    public function removeDiscussionNote($project_id, $mr_iid, $discussion_id, $note_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/discussions/'.$this->encodePath($discussion_id).'/notes/'.$this->encodePath($note_id)));
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @return mixed
     */
    public function changes($project_id, $mr_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_id).'/changes'));
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @return mixed
     */
    public function commits($project_id, $mr_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_id).'/commits'));
    }

    /**
     * @param int $project_id
     * @param int $mr_id
     * @return mixed
     */
    public function closesIssues($project_id, $mr_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_id).'/closes_issues'));
    }

    /**
     * @param int $project_id
     * @param int $mr_iid
     * @return mixed
     */
    public function approvals($project_id, $mr_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/approvals'));
    }

    /**
     * @param int $project_id
     * @param int $mr_iid
     * @return mixed
     */
    public function approve($project_id, $mr_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/approve'));
    }

    /**
     * @param int $project_id
     * @param int $mr_iid
     * @return mixed
     */
    public function unapprove($project_id, $mr_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/unapprove'));
    }

    /**
     * @param int $project_id
     * @param int $mr_iid
     * @return mixed
     */
    public function awardEmoji($project_id, $mr_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/award_emoji'));
    }
}
