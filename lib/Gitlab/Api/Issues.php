<?php

namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\Options;

class Issues extends AbstractApi
{
    const STATE_OPENED = 'opened';

    const STATE_CLOSED = 'closed';

    /**
     * @param int|string|null $project_id
     * @param array           $parameters {
     *
     *     @var string $state                return all issues or just those that are opened or closed
     *     @var string $labels               comma-separated list of label names, issues must have all labels to be returned
     *     @var bool   $with_labels_details  if true, response will return more details for each label
     *     @var string $milestone            the milestone title
     *     @var string scope                 return issues for the given scope: created-by-me, assigned-to-me or all (default is created-by-me)
     *     @var int[]  $iids                 return only the issues having the given iid
     *     @var string $order_by             return requests ordered by created_at or updated_at fields (default is created_at)
     *     @var string $sort                 return requests sorted in asc or desc order (default is desc)
     *     @var string $search               search issues against their title and description
     * }
     *
     * @return mixed
     */
    public function all($project_id = null, array $parameters = [])
    {
        $path = null === $project_id ? 'issues' : $this->getProjectPath($project_id, 'issues');

        return $this->get($path, $this->createOptionsResolver()->resolve($parameters));
    }

    /**
     * @param int   $group_id
     * @param array $parameters
     *
     * @return mixed
     */
    public function group($group_id, array $parameters = [])
    {
        return $this->get(
            $this->getGroupPath($group_id, 'issues'),
            $this->createOptionsResolver()->resolve($parameters)
        );
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function show($project_id, $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)));
    }

    /**
     * @param int|string $project_id
     * @param array      $params
     *
     * @return mixed
     */
    public function create($project_id, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'issues'), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param array      $params
     *
     * @return mixed
     */
    public function update($project_id, $issue_iid, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int|string $to_project_id
     *
     * @return mixed
     */
    public function move($project_id, $issue_iid, $to_project_id)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)).'/move', [
            'to_project_id' => $to_project_id,
        ]);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function remove($project_id, $issue_iid)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function showNotes($project_id, $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/notes'));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int        $note_id
     *
     * @return mixed
     */
    public function showNote($project_id, $issue_iid, $note_id)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/notes/'.$this->encodePath($note_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param string     $body
     *
     * @return mixed
     */
    public function addNote($project_id, $issue_iid, $body)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/notes'), [
            'body' => $body,
        ]);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int        $note_id
     * @param string     $body
     *
     * @return mixed
     */
    public function updateNote($project_id, $issue_iid, $note_id, $body)
    {
        return $this->put($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/notes/'.$this->encodePath($note_id)), [
            'body' => $body,
        ]);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int        $note_id
     *
     * @return mixed
     */
    public function removeNote($project_id, $issue_iid, $note_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/notes/'.$this->encodePath($note_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     *
     * @deprecated since version 9.18 and will be removed in 10.0. Use the showNotes() method instead.
     */
    public function showComments($project_id, $issue_iid)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0. Use the showNotes() method instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->showNotes($project_id, $issue_iid);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int        $note_id
     *
     * @return mixed
     *
     * @deprecated since version 9.18 and will be removed in 10.0. Use the showNote() method instead.
     */
    public function showComment($project_id, $issue_iid, $note_id)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0. Use the showNote() method instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->showNote($project_id, $issue_iid, $note_id);
    }

    /**
     * @param int|string   $project_id
     * @param int          $issue_iid
     * @param string|array $body
     *
     * @return mixed
     *
     * @deprecated since version 9.18 and will be removed in 10.0. Use the addNote() method instead.
     */
    public function addComment($project_id, $issue_iid, $body)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0. Use the addNote() method instead.', __METHOD__), E_USER_DEPRECATED);

        if (is_array($body)) {
            return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/notes'), $body);
        }

        return $this->addNote($project_id, $issue_iid, $body);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int        $note_id
     * @param string     $body
     *
     * @return mixed
     *
     * @deprecated since version 9.18 and will be removed in 10.0. Use the updateNote() method instead.
     */
    public function updateComment($project_id, $issue_iid, $note_id, $body)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0. Use the updateNote() method instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->updateNote($project_id, $issue_iid, $note_id, $body);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int        $note_id
     *
     * @return mixed
     *
     * @deprecated since version 9.18 and will be removed in 10.0. Use the removeNote() method instead.
     */
    public function removeComment($project_id, $issue_iid, $note_id)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0. Use the removeNote() method instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->removeNote($project_id, $issue_iid, $note_id);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function showDiscussions($project_id, $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)).'/discussions');
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param string     $discussion_id
     *
     * @return mixed
     */
    public function showDiscussion($project_id, $issue_iid, $discussion_id)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)).'/discussions/'.$this->encodePath($discussion_id));
    }

    /**
     * @param int|string   $project_id
     * @param int          $issue_iid
     * @param string|array $body
     *
     * @return mixed
     */
    public function addDiscussion($project_id, $issue_iid, $body)
    {
        // backwards compatibility
        if (is_array($body)) {
            @trigger_error(sprintf('Passing an array to the $body parameter of %s() is deprecated since 9.18 and will be banned in 10.0.', __METHOD__), E_USER_DEPRECATED);
            $params = $body;
        } else {
            $params = ['body' => $body];
        }

        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/discussions'), $params);
    }

    /**
     * @param int|string   $project_id
     * @param int          $issue_iid
     * @param string       $discussion_id
     * @param string|array $body
     *
     * @return mixed
     */
    public function addDiscussionNote($project_id, $issue_iid, $discussion_id, $body)
    {
        // backwards compatibility
        if (is_array($body)) {
            @trigger_error(sprintf('Passing an array to the $body parameter of %s() is deprecated since 9.18 and will be banned in 10.0.', __METHOD__), E_USER_DEPRECATED);
            $params = $body;
        } else {
            $params = ['body' => $body];
        }

        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/discussions/'.$this->encodePath($discussion_id).'/notes'), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param string     $discussion_id
     * @param int        $note_id
     * @param string     $body
     *
     * @return mixed
     */
    public function updateDiscussionNote($project_id, $issue_iid, $discussion_id, $note_id, $body)
    {
        return $this->put($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/discussions/'.$this->encodePath($discussion_id).'/notes/'.$this->encodePath($note_id)), [
            'body' => $body,
        ]);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param string     $discussion_id
     * @param int        $note_id
     *
     * @return mixed
     */
    public function removeDiscussionNote($project_id, $issue_iid, $discussion_id, $note_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/discussions/'.$this->encodePath($discussion_id).'/notes/'.$this->encodePath($note_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param string     $duration
     *
     * @return mixed
     */
    public function setTimeEstimate($project_id, $issue_iid, $duration)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/time_estimate'), ['duration' => $duration]);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function resetTimeEstimate($project_id, $issue_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/reset_time_estimate'));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param string     $duration
     *
     * @return mixed
     */
    public function addSpentTime($project_id, $issue_iid, $duration)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/add_spent_time'), ['duration' => $duration]);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function resetSpentTime($project_id, $issue_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/reset_spent_time'));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function getTimeStats($project_id, $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/time_stats'));
    }

    /**
     * Subscribes the authenticated user to an issue to receive notifications.
     * If the user is already subscribed to the issue, the status code 304 is returned.
     *
     * @see https://docs.gitlab.com/ee/api/issues.html#subscribe-to-an-issue
     *
     * @param int|string $project_id The ID or URL-encoded path of the project owned by the authenticated user
     * @param int        $issue_iid  The internal ID of a project’s issue
     *
     * @return array|string issue object if change is made, empty string otherwise
     */
    public function subscribe($project_id, $issue_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/subscribe'));
    }

    /**
     * Unsubscribes the authenticated user from the issue to not receive notifications from it.
     * If the user is not subscribed to the issue, the status code 304 is returned.
     *
     * @see https://docs.gitlab.com/ee/api/issues.html#unsubscribe-from-an-issue
     *
     * @param int|string $project_id The ID or URL-encoded path of the project owned by the authenticated user
     * @param int        $issue_iid  The internal ID of a project’s issue
     *
     * @return array|string issue object if change is made, empty string otherwise
     */
    public function unsubscribe($project_id, $issue_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/unsubscribe'));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function awardEmoji($project_id, $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/award_emoji'));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int        $award_id
     *
     * @return mixed
     */
    public function removeAwardEmoji($project_id, $issue_iid, $award_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/award_emoji/'.$this->encodePath($award_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function closedByMergeRequests($project_id, $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)).'/closed_by');
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function relatedMergeRequests($project_id, $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid).'/related_merge_requests'));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function showParticipants($project_id, $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.$this->encodePath($issue_iid)).'/participants');
    }

    /**
     * {@inheritdoc}
     */
    protected function createOptionsResolver()
    {
        $resolver = parent::createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value) {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('state')
            ->setAllowedValues('state', [self::STATE_OPENED, self::STATE_CLOSED])
        ;
        $resolver->setDefined('labels');
        $resolver->setDefined('milestone');
        $resolver->setDefined('with_labels_details')
            ->setAllowedTypes('with_labels_details', 'bool')
            ->setNormalizer('with_labels_details', $booleanNormalizer)
        ;
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
        $resolver->setDefined('created_after');
        $resolver->setDefined('created_before');
        $resolver->setDefined('updated_after');
        $resolver->setDefined('updated_before');
        $resolver->setDefined('assignee_id')
            ->setAllowedTypes('assignee_id', 'integer')
        ;
        $resolver->setDefined('weight')
            ->setAllowedTypes('weight', 'integer')
        ;

        return $resolver;
    }
}
