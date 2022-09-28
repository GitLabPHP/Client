<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Issues extends AbstractApi
{
    /**
     * @var string
     */
    public const STATE_OPENED = 'opened';

    /**
     * @var string
     */
    public const STATE_CLOSED = 'closed';

    /**
     * @param int|string|null $project_id
     * @param array           $parameters {
     *
     *     @var string $state                return all issues or just those that are opened or closed
     *     @var string $labels               comma-separated list of label names, issues must have all labels to be returned
     *     @var bool   $with_labels_details  if true, response will return more details for each label
     *     @var string $milestone            the milestone title
     *     @var string $scope                return issues for the given scope: created-by-me, assigned-to-me or all (default is created-by-me)
     *     @var int[]  $iids                 return only the issues having the given iid
     *     @var string $order_by             return requests ordered by created_at or updated_at fields (default is created_at)
     *     @var string $sort                 return requests sorted in asc or desc order (default is desc)
     *     @var bool $confidential           filter confidential or public issues
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
     * @param int|string $group_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function group($group_id, array $parameters = [])
    {
        return $this->get(
            'groups/'.self::encodePath($group_id).'/issues',
            $this->createOptionsResolver()->resolve($parameters)
        );
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function show($project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)));
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
    public function update($project_id, int $issue_iid, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param array      $params
     *
     * @return mixed
     */
    public function reorder($project_id, int $issue_iid, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/reorder', $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int|string $to_project_id
     *
     * @return mixed
     */
    public function move($project_id, int $issue_iid, $to_project_id)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/move', [
            'to_project_id' => $to_project_id,
        ]);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function remove($project_id, int $issue_iid)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function showNotes($project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/notes'));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int        $note_id
     *
     * @return mixed
     */
    public function showNote($project_id, int $issue_iid, int $note_id)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/notes/'.self::encodePath($note_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param string     $body
     * @param array      $params
     *
     * @return mixed
     */
    public function addNote($project_id, int $issue_iid, string $body, array $params = [])
    {
        $params['body'] = $body;

        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/notes'), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int        $note_id
     * @param string     $body
     * @param array      $params
     *
     * @return mixed
     */
    public function updateNote($project_id, int $issue_iid, int $note_id, string $body, array $params = [])
    {
        $params['body'] = $body;

        return $this->put($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/notes/'.self::encodePath($note_id)), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int        $note_id
     *
     * @return mixed
     */
    public function removeNote($project_id, int $issue_iid, int $note_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/notes/'.self::encodePath($note_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function showDiscussions($project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/discussions');
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param string     $discussion_id
     *
     * @return mixed
     */
    public function showDiscussion($project_id, int $issue_iid, string $discussion_id)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/discussions/'.self::encodePath($discussion_id));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param string     $body
     *
     * @return mixed
     */
    public function addDiscussion($project_id, int $issue_iid, string $body)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/discussions'), ['body' => $body]);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param string     $discussion_id
     * @param string     $body
     *
     * @return mixed
     */
    public function addDiscussionNote($project_id, int $issue_iid, string $discussion_id, string $body)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/discussions/'.self::encodePath($discussion_id).'/notes'), ['body' => $body]);
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
    public function updateDiscussionNote($project_id, int $issue_iid, string $discussion_id, int $note_id, string $body)
    {
        return $this->put($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/discussions/'.self::encodePath($discussion_id).'/notes/'.self::encodePath($note_id)), [
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
    public function removeDiscussionNote($project_id, int $issue_iid, string $discussion_id, int $note_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/discussions/'.self::encodePath($discussion_id).'/notes/'.self::encodePath($note_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param string     $duration
     *
     * @return mixed
     */
    public function setTimeEstimate($project_id, int $issue_iid, string $duration)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/time_estimate'), ['duration' => $duration]);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function resetTimeEstimate($project_id, int $issue_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/reset_time_estimate'));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param string     $duration
     *
     * @return mixed
     */
    public function addSpentTime($project_id, int $issue_iid, string $duration)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/add_spent_time'), ['duration' => $duration]);
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function resetSpentTime($project_id, int $issue_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/reset_spent_time'));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function getTimeStats($project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/time_stats'));
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
     * @return mixed
     */
    public function subscribe($project_id, int $issue_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/subscribe'));
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
     * @return mixed
     */
    public function unsubscribe($project_id, int $issue_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/unsubscribe'));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function awardEmoji($project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/award_emoji'));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int        $award_id
     *
     * @return mixed
     */
    public function removeAwardEmoji($project_id, int $issue_iid, int $award_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/award_emoji/'.self::encodePath($award_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function closedByMergeRequests($project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/closed_by');
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function relatedMergeRequests($project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/related_merge_requests'));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function showParticipants($project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/participants');
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function showResourceLabelEvents($project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/resource_label_events');
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int        $resource_label_event_id
     *
     * @return mixed
     */
    public function showResourceLabelEvent($project_id, int $issue_iid, int $resource_label_event_id)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/resource_label_events/'.self::encodePath($resource_label_event_id));
    }

    /**
     * @return OptionsResolver
     */
    protected function createOptionsResolver(): OptionsResolver
    {
        $resolver = parent::createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
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
                return \count($value) === \count(\array_filter($value, 'is_int'));
            })
        ;
        $resolver->setDefined('scope')
            ->setAllowedValues('scope', ['created-by-me', 'assigned-to-me', 'all'])
        ;
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['created_at', 'updated_at', 'priority', 'due_date', 'relative_position', 'label_priority', 'milestone_due', 'popularity', 'weight'])
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;
        $resolver->setDefined('confidential')
            ->setAllowedValues('confidential', [false, true])
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
        
        $resolver->setDefined('not[assignee_id]');
        $resolver->setDefined('not[assignee_username]');
        $resolver->setDefined('not[author_id]');
        $resolver->setDefined('not[author_username]');
        $resolver->setDefined('not[iids]');
        $resolver->setDefined('not[iteration_id]');
        $resolver->setDefined('not[iteration_title]');
        $resolver->setDefined('not[labels]');
        $resolver->setDefined('not[milestone]');
        $resolver->setDefined('not[milestone_id]');
        $resolver->setDefined('not[weight]');

        return $resolver;
    }
}
