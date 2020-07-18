<?php

namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\Options;

class MergeRequests extends AbstractApi
{
    const STATE_ALL = 'all';

    const STATE_MERGED = 'merged';

    const STATE_OPENED = 'opened';

    const STATE_CLOSED = 'closed';

    /**
     * @param int|string|null $project_id
     * @param array           $parameters {
     *
     *     @var int[]              $iids           return the request having the given iid
     *     @var string             $state          return all merge requests or just those that are opened, closed, or
     *                                             merged
     *     @var string             $scope          Return merge requests for the given scope: created-by-me,
     *                                             assigned-to-me or all (default is created-by-me)
     *     @var string             $order_by       return requests ordered by created_at or updated_at fields (default is created_at)
     *     @var string             $sort           return requests sorted in asc or desc order (default is desc)
     *     @var string             $milestone      return merge requests for a specific milestone
     *     @var string             $view           if simple, returns the iid, URL, title, description, and basic state of merge request
     *     @var string             $labels         return merge requests matching a comma separated list of labels
     *     @var \DateTimeInterface $created_after  return merge requests created after the given time (inclusive)
     *     @var \DateTimeInterface $created_before return merge requests created before the given time (inclusive)
     * }
     *
     * @throws UndefinedOptionsException if an option name is undefined
     * @throws InvalidOptionsException   if an option doesn't fulfill the specified validation rules
     *
     * @return mixed
     */
    public function all($project_id = null, array $parameters = [])
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
            ->setAllowedValues('state', [self::STATE_ALL, self::STATE_MERGED, self::STATE_OPENED, self::STATE_CLOSED])
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

        $path = null === $project_id ? 'merge_requests' : $this->getProjectPath($project_id, 'merge_requests');

        return $this->get($path, $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     * @param array      $parameters {
     *
     *     @var bool               $include_diverged_commits_count      Return the commits behind the target branch
     *     @var bool               $include_rebase_in_progress          Return whether a rebase operation is in progress
     * }
     *
     * @return mixed
     */
    public function show($project_id, $mr_iid, $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('include_diverged_commits_count')
            ->setAllowedTypes('include_diverged_commits_count', 'bool')
        ;
        $resolver->setDefined('include_rebase_in_progress')
            ->setAllowedTypes('include_rebase_in_progress', 'bool')
        ;

        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid)), $resolver->resolve($parameters));
    }

    /**
     * @param int|string               $project_id
     * @param string                   $source
     * @param string                   $target
     * @param string                   $title
     * @param int|array<string,mixed>  $parameters
     * @param int|string               $target_project_id @deprecated since version 9.18 and will be removed in 10.0. Use $parameters['target_project_id'] instead.
     * @param string                   $description       @deprecated since version 9.18 and will be removed in 10.0. Use $parameters['description'] instead
     * @param array<string,mixed>|null $legacyParams      @deprecated since version 9.18 and will be removed in 10.0. Use $parameters instead
     *
     * @return mixed
     */
    public function create($project_id, $source, $target, $title, $parameters = null, $target_project_id = null, $description = null, array $legacyParams = null)
    {
        if (is_array($parameters)) {
            return $this->post(
                $this->getProjectPath($project_id, 'merge_requests'),
                $parameters
            );
        }

        if (null !== $parameters) {
            @trigger_error(sprintf('Passing the assignee to the %s() method\'s $parameters parameter is deprecated since version 9.18 and will be banned in 10.0. Use $parameters[\'assignee_id\'] instead.', __METHOD__), E_USER_DEPRECATED);
        }

        if (null !== $target_project_id) {
            @trigger_error(sprintf('The %s() method\'s $target_project_id parameter is deprecated since version 9.18 and will be removed in 10.0. Use $parameters[\'target_project_id\'] instead.', __METHOD__), E_USER_DEPRECATED);
        }

        if (null !== $description) {
            @trigger_error(sprintf('The %s() method\'s $description parameter is deprecated since version 9.18 and will be removed in 10.0. Use $parameters[\'description\'] instead.', __METHOD__), E_USER_DEPRECATED);
        }

        if (null !== $legacyParams) {
            @trigger_error(sprintf('The %s() method\'s $legacyParams parameter is deprecated since version 9.18 and will be removed in 10.0. Use $parameters instead.', __METHOD__), E_USER_DEPRECATED);
        }

        $baseParams = [
            'source_branch' => $source,
            'target_branch' => $target,
            'title' => $title,
            'assignee_id' => $parameters,
            'description' => $description,
            'target_project_id' => $target_project_id,
        ];

        return $this->post(
            $this->getProjectPath($project_id, 'merge_requests'),
            array_merge($baseParams, null === $legacyParams ? [] : $legacyParams)
        );
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     * @param array      $parameters
     *
     * @return mixed
     */
    public function update($project_id, $mr_iid, array $parameters)
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid)), $parameters);
    }

    /**
     * @param int|string        $project_id
     * @param int               $mr_iid
     * @param string|array|null $parameters
     *
     * @return mixed
     */
    public function merge($project_id, $mr_iid, $parameters = null)
    {
        if (is_array($parameters)) {
            $params = $parameters;
        } else {
            @trigger_error(sprintf('Passing a string to the $parameters parameter of %s() is deprecated since 9.18 and will be banned in 10.0.', __METHOD__), E_USER_DEPRECATED);
            $params = ['merge_commit_message' => $parameters];
        }

        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/merge'), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     *
     * @return mixed
     */
    public function showNotes($project_id, $mr_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/notes'));
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     * @param int        $note_id
     *
     * @return mixed
     */
    public function showNote($project_id, $mr_iid, $note_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/notes/'.$this->encodePath($note_id)));
    }

    /**
     * @param int|string  $project_id
     * @param int         $mr_iid
     * @param string      $body
     * @param string|null $created_at @deprecated since version 9.18 and will be removed in 10.0. There is no replacement as this parameter was removed by GitLab.
     *
     * @return mixed
     */
    public function addNote($project_id, $mr_iid, $body, $created_at = null)
    {
        if (null !== $created_at) {
            @trigger_error(sprintf('The %s() method\'s $created_at parameter is deprecated since version 9.18 and will be removed in 10.0. There is no replacement as this parameter was removed by GitLab.', __METHOD__), E_USER_DEPRECATED);

            return $this->post($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/notes'), [
                'body' => $body,
                'created_at' => $created_at,
            ]);
        }

        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/notes'), [
            'body' => $body,
        ]);
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     * @param int        $note_id
     * @param string     $body
     *
     * @return mixed
     */
    public function updateNote($project_id, $mr_iid, $note_id, $body)
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/notes/'.$this->encodePath($note_id)), [
            'body' => $body,
        ]);
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     * @param int        $note_id
     *
     * @return mixed
     */
    public function removeNote($project_id, $mr_iid, $note_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/notes/'.$this->encodePath($note_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     *
     * @return mixed
     *
     * @deprecated since version 9.1 and will be removed in 10.0. Use the showNotes() method instead.
     */
    public function showComments($project_id, $mr_iid)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.1 and will be removed in 10.0. Use the showNotes() method instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->showNotes($project_id, $mr_iid);
    }

    /**
     * @param int|string  $project_id
     * @param int         $mr_iid
     * @param string      $note
     * @param string|null $created_at
     *
     * @return mixed
     *
     * @deprecated since version 9.1 and will be removed in 10.0. Use the addNote() method instead.
     */
    public function addComment($project_id, $mr_iid, $note, $created_at = null)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.1 and will be removed in 10.0. Use the addNote() method instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->addNote($project_id, $mr_iid, $note, $created_at);
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     *
     * @return mixed
     */
    public function showDiscussions($project_id, $mr_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid)).'/discussions');
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     * @param string     $discussion_id
     *
     * @return mixed
     */
    public function showDiscussion($project_id, $mr_iid, $discussion_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid)).'/discussions/'.$this->encodePath($discussion_id));
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     * @param array      $params
     *
     * @return mixed
     */
    public function addDiscussion($project_id, $mr_iid, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/discussions'), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     * @param string     $discussion_id
     * @param bool       $resolved
     *
     * @return mixed
     */
    public function resolveDiscussion($project_id, $mr_iid, $discussion_id, $resolved = true)
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/discussions/'.$this->encodePath($discussion_id)), [
            'resolved' => $resolved,
        ]);
    }

    /**
     * @param int|string   $project_id
     * @param int          $mr_iid
     * @param string       $discussion_id
     * @param string|array $body
     *
     * @return mixed
     */
    public function addDiscussionNote($project_id, $mr_iid, $discussion_id, $body)
    {
        // backwards compatibility
        if (is_array($body)) {
            @trigger_error(sprintf('Passing an array to the $message parameter of %s() is deprecated since 9.18 and will be banned in 10.0.', __METHOD__), E_USER_DEPRECATED);
            $params = $body;
        } else {
            $params = ['body' => $body];
        }

        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/discussions/'.$this->encodePath($discussion_id).'/notes'), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     * @param string     $discussion_id
     * @param int        $note_id
     * @param array      $params
     *
     * @return mixed
     */
    public function updateDiscussionNote($project_id, $mr_iid, $discussion_id, $note_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/discussions/'.$this->encodePath($discussion_id).'/notes/'.$this->encodePath($note_id)), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     * @param string     $discussion_id
     * @param int        $note_id
     *
     * @return mixed
     */
    public function removeDiscussionNote($project_id, $mr_iid, $discussion_id, $note_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/discussions/'.$this->encodePath($discussion_id).'/notes/'.$this->encodePath($note_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     *
     * @return mixed
     */
    public function changes($project_id, $mr_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/changes'));
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     *
     * @return mixed
     */
    public function commits($project_id, $mr_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/commits'));
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     *
     * @return mixed
     */
    public function closesIssues($project_id, $mr_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/closes_issues'));
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     *
     * @return mixed
     */
    public function approvals($project_id, $mr_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/approvals'));
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     *
     * @return mixed
     */
    public function approve($project_id, $mr_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/approve'));
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     *
     * @return mixed
     */
    public function unapprove($project_id, $mr_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/unapprove'));
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     *
     * @return mixed
     */
    public function awardEmoji($project_id, $mr_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/award_emoji'));
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     * @param int        $award_id
     *
     * @return mixed
     */
    public function removeAwardEmoji($project_id, $mr_iid, $award_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/award_emoji/'.$this->encodePath($award_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     * @param array      $params
     *
     * @return mixed
     */
    public function rebase($project_id, $mr_iid, array $params = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('skip_ci')
            ->setAllowedTypes('skip_ci', 'bool');

        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid)).'/rebase', $resolver->resolve($params));
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     *
     * @return mixed
     */
    public function approvalState($project_id, $mr_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/approval_state'));
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     *
     * @return mixed
     */
    public function levelRules($project_id, $mr_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/approval_rules'));
    }

    /**
     * @param int|string          $project_id
     * @param int                 $mr_iid
     * @param string              $name
     * @param int                 $approvals_required
     * @param array<string,mixed> $parameters
     *
     * @return mixed
     */
    public function createLevelRule($project_id, $mr_iid, $name, $approvals_required, array $parameters = [])
    {
        $baseParam = [
            'name' => $name,
            'approvals_required' => $approvals_required,
        ];

        return $this->post(
            $this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/approval_rules'),
            array_merge($baseParam, $parameters)
        );
    }

    /**
     * @param int|string          $project_id
     * @param int                 $mr_iid
     * @param int                 $approval_rule_id
     * @param string              $name
     * @param int                 $approvals_required
     * @param array<string,mixed> $parameters
     *
     * @return mixed
     */
    public function updateLevelRule($project_id, $mr_iid, $approval_rule_id, $name, $approvals_required, array $parameters = [])
    {
        $baseParam = [
            'name' => $name,
            'approvals_required' => $approvals_required,
        ];

        return $this->put(
            $this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/approval_rules/'.$this->encodePath($approval_rule_id)),
            array_merge($baseParam, $parameters)
        );
    }

    /**
     * @param int|string $project_id
     * @param int        $mr_iid
     * @param int        $approval_rule_id
     *
     * @return mixed
     */
    public function deleteLevelRule($project_id, $mr_iid, $approval_rule_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.$this->encodePath($mr_iid).'/approval_rules/'.$this->encodePath($approval_rule_id)));
    }
}
