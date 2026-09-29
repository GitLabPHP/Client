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
     * List issues, either all issues visible to the authenticated user (when
     * $project_id is null) or all issues of a specific project.
     *
     * @see https://docs.gitlab.com/api/issues/#list-all-issues
     * @see https://docs.gitlab.com/api/issues/#list-all-project-issues
     *
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
     *     @var bool   $confidential         filter confidential or public issues
     *     @var string $search               search issues against their title and description
     *     @var int    $author_id            filter issues assigned to the specified author user id
     *     @var int    $assignee_id          filter issues assigned to the specified assignee user id
     *     @var int    $iteration_id         filter issues assigned to the specified iteration id
     *     @var string $iteration_title      filter issues assigned to the specified iteration title
     * }
     */
    public function all(int|string|null $project_id = null, array $parameters = []): mixed
    {
        $path = null === $project_id ? 'issues' : $this->getProjectPath($project_id, 'issues');

        return $this->get($path, $this->createOptionsResolver()->resolve($parameters));
    }

    /**
     * List all issues of a group.
     *
     * @see https://docs.gitlab.com/api/issues/#list-all-group-issues
     */
    public function group(int|string $group_id, array $parameters = []): mixed
    {
        return $this->get(
            'groups/'.self::encodePath($group_id).'/issues',
            $this->createOptionsResolver()->resolve($parameters)
        );
    }

    /**
     * Get a single project issue.
     *
     * @see https://docs.gitlab.com/api/issues/#retrieve-a-project-issue
     */
    public function show(int|string $project_id, int $issue_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)));
    }

    /**
     * Create a new project issue.
     *
     * @see https://docs.gitlab.com/api/issues/#create-an-issue
     */
    public function create(int|string $project_id, array $params): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'issues'), $params);
    }

    /**
     * Update an existing project issue.
     *
     * @see https://docs.gitlab.com/api/issues/#update-an-issue
     */
    public function update(int|string $project_id, int $issue_iid, array $params): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)), $params);
    }

    /**
     * Reorder an issue.
     *
     * @see https://docs.gitlab.com/api/issues/#reorder-an-issue
     */
    public function reorder(int|string $project_id, int $issue_iid, array $params): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/reorder', $params);
    }

    /**
     * Move an issue to a different project.
     *
     * @see https://docs.gitlab.com/api/issues/#move-an-issue
     */
    public function move(int|string $project_id, int $issue_iid, int|string $to_project_id): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/move', [
            'to_project_id' => $to_project_id,
        ]);
    }

    /**
     * Delete a project issue.
     *
     * @see https://docs.gitlab.com/api/issues/#delete-an-issue
     */
    public function remove(int|string $project_id, int $issue_iid): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)));
    }

    /**
     * List all notes of an issue.
     *
     * @see https://docs.gitlab.com/api/notes/#list-all-issue-notes
     */
    public function showNotes(int|string $project_id, int $issue_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/notes'));
    }

    /**
     * Get a single note of an issue.
     *
     * @see https://docs.gitlab.com/api/notes/#retrieve-an-issue-note
     */
    public function showNote(int|string $project_id, int $issue_iid, int $note_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/notes/'.self::encodePath($note_id)));
    }

    /**
     * Create a new note for an issue.
     *
     * @see https://docs.gitlab.com/api/notes/#create-an-issue-note
     */
    public function addNote(int|string $project_id, int $issue_iid, string $body, array $params = []): mixed
    {
        $params['body'] = $body;

        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/notes'), $params);
    }

    /**
     * Update an existing note of an issue.
     *
     * @see https://docs.gitlab.com/api/notes/#update-an-issue-note
     */
    public function updateNote(int|string $project_id, int $issue_iid, int $note_id, string $body, array $params = []): mixed
    {
        $params['body'] = $body;

        return $this->put($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/notes/'.self::encodePath($note_id)), $params);
    }

    /**
     * Delete a note of an issue.
     *
     * @see https://docs.gitlab.com/api/notes/#delete-an-issue-note
     */
    public function removeNote(int|string $project_id, int $issue_iid, int $note_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/notes/'.self::encodePath($note_id)));
    }

    /**
     * List all discussion items of an issue.
     *
     * @see https://docs.gitlab.com/api/discussions/#list-all-issue-discussion-items
     */
    public function showDiscussions(int|string $project_id, int $issue_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/discussions');
    }

    /**
     * Get a single discussion item of an issue.
     *
     * @see https://docs.gitlab.com/api/discussions/#retrieve-an-issue-discussion-item
     */
    public function showDiscussion(int|string $project_id, int $issue_iid, string $discussion_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/discussions/'.self::encodePath($discussion_id));
    }

    /**
     * Create a new thread on an issue.
     *
     * @see https://docs.gitlab.com/api/discussions/#create-an-issue-thread
     */
    public function addDiscussion(int|string $project_id, int $issue_iid, string $body): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/discussions'), ['body' => $body]);
    }

    /**
     * Add a note to an existing issue thread.
     *
     * @see https://docs.gitlab.com/api/discussions/#add-a-note-to-an-issue-thread
     */
    public function addDiscussionNote(int|string $project_id, int $issue_iid, string $discussion_id, string $body): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/discussions/'.self::encodePath($discussion_id).'/notes'), ['body' => $body]);
    }

    /**
     * Update an existing note of an issue thread.
     *
     * @see https://docs.gitlab.com/api/discussions/#update-an-issue-thread-note
     */
    public function updateDiscussionNote(int|string $project_id, int $issue_iid, string $discussion_id, int $note_id, string $body): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/discussions/'.self::encodePath($discussion_id).'/notes/'.self::encodePath($note_id)), [
            'body' => $body,
        ]);
    }

    /**
     * Delete a note of an issue thread.
     *
     * @see https://docs.gitlab.com/api/discussions/#delete-an-issue-thread-note
     */
    public function removeDiscussionNote(int|string $project_id, int $issue_iid, string $discussion_id, int $note_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/discussions/'.self::encodePath($discussion_id).'/notes/'.self::encodePath($note_id)));
    }

    /**
     * Set a time estimate for an issue.
     *
     * @see https://docs.gitlab.com/api/issues/#set-a-time-estimate-for-an-issue
     */
    public function setTimeEstimate(int|string $project_id, int $issue_iid, string $duration): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/time_estimate'), ['duration' => $duration]);
    }

    /**
     * Reset the time estimate for an issue.
     *
     * @see https://docs.gitlab.com/api/issues/#reset-the-time-estimate-for-an-issue
     */
    public function resetTimeEstimate(int|string $project_id, int $issue_iid): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/reset_time_estimate'));
    }

    /**
     * Add spent time for an issue.
     *
     * @see https://docs.gitlab.com/api/issues/#add-spent-time-for-an-issue
     */
    public function addSpentTime(int|string $project_id, int $issue_iid, string $duration): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/add_spent_time'), ['duration' => $duration]);
    }

    /**
     * Reset spent time for an issue.
     *
     * @see https://docs.gitlab.com/api/issues/#reset-spent-time-for-an-issue
     */
    public function resetSpentTime(int|string $project_id, int $issue_iid): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/reset_spent_time'));
    }

    /**
     * Get time tracking stats for an issue.
     *
     * @see https://docs.gitlab.com/api/issues/#retrieve-time-tracking-stats-for-an-issue
     */
    public function getTimeStats(int|string $project_id, int $issue_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/time_stats'));
    }

    /**
     * Subscribes the authenticated user to an issue to receive notifications.
     * If the user is already subscribed to the issue, the status code 304 is returned.
     *
     * @see https://docs.gitlab.com/api/issues/#subscribe-to-an-issue
     *
     * @param int|string $project_id The ID or URL-encoded path of the project owned by the authenticated user
     * @param int        $issue_iid  The internal ID of a project’s issue
     */
    public function subscribe(int|string $project_id, int $issue_iid): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/subscribe'));
    }

    /**
     * Unsubscribes the authenticated user from the issue to not receive notifications from it.
     * If the user is not subscribed to the issue, the status code 304 is returned.
     *
     * @see https://docs.gitlab.com/api/issues/#unsubscribe-from-an-issue
     *
     * @param int|string $project_id The ID or URL-encoded path of the project owned by the authenticated user
     * @param int        $issue_iid  The internal ID of a project’s issue
     */
    public function unsubscribe(int|string $project_id, int $issue_iid): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/unsubscribe'));
    }

    /**
     * List all award emoji of an issue.
     *
     * @see https://docs.gitlab.com/api/award_emoji/#list-an-awardables-award-emojis
     */
    public function awardEmoji(int|string $project_id, int $issue_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/award_emoji'));
    }

    /**
     * Delete an award emoji of an issue.
     *
     * @see https://docs.gitlab.com/api/award_emoji/#delete-an-award-emoji
     */
    public function removeAwardEmoji(int|string $project_id, int $issue_iid, int $award_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/award_emoji/'.self::encodePath($award_id)));
    }

    /**
     * List all merge requests that will close an issue when merged.
     *
     * @see https://docs.gitlab.com/api/issues/#list-all-merge-requests-that-close-an-issue-on-merge
     */
    public function closedByMergeRequests(int|string $project_id, int $issue_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/closed_by');
    }

    /**
     * List all merge requests related to an issue.
     *
     * @see https://docs.gitlab.com/api/issues/#list-all-merge-requests-related-to-an-issue
     */
    public function relatedMergeRequests(int|string $project_id, int $issue_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/related_merge_requests'));
    }

    /**
     * List all participants of an issue.
     *
     * Not currently documented on the public Issues API reference page; verify
     * against a running GitLab instance or the GitLab source before relying on it.
     */
    public function showParticipants(int|string $project_id, int $issue_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/participants');
    }

    /**
     * List all label events of an issue.
     *
     * @see https://docs.gitlab.com/api/resource_label_events/#list-project-issue-label-events
     */
    public function showResourceLabelEvents(int|string $project_id, int $issue_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/resource_label_events');
    }

    /**
     * Get a single label event of an issue.
     *
     * @see https://docs.gitlab.com/api/resource_label_events/#retrieve-a-single-issue-label-event
     */
    public function showResourceLabelEvent(int|string $project_id, int $issue_iid, int $resource_label_event_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/resource_label_events/'.self::encodePath($resource_label_event_id));
    }

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
        $resolver->setDefined('milestone_id')
            ->setAllowedTypes('milestone_id', 'integer');
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
        $resolver->setDefined('author_id')
            ->setAllowedTypes('author_id', 'integer')
        ;
        $resolver->setDefined('assignee_id')
            ->setAllowedTypes('assignee_id', 'integer')
        ;
        $resolver->setDefined('iteration_id')
            ->setAllowedTypes('iteration_id', 'integer')
        ;
        $resolver->setDefined('iteration_title')
            ->setAllowedTypes('iteration_title', 'string')
        ;
        $resolver->setDefined('weight')
            ->setAllowedTypes('weight', 'integer')
        ;

        return $resolver;
    }
}
