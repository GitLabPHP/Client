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

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\Options;

class MergeRequests extends AbstractApi
{
    /**
     * @var string
     */
    public const STATE_ALL = 'all';

    /**
     * @var string
     */
    public const STATE_MERGED = 'merged';

    /**
     * @var string
     */
    public const STATE_OPENED = 'opened';

    /**
     * @var string
     */
    public const STATE_CLOSED = 'closed';

    /**
     * @var string
     */
    public const STATE_LOCKED = 'locked';

    /**
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
     *     @var int                $reviewer_id    return merge requests which have the user as a reviewer with the given user id
     *     @var bool               $wip            return only draft merge requests (true) or only non-draft merge requests (false)
     * }
     *
     * @throws UndefinedOptionsException if an option name is undefined
     * @throws InvalidOptionsException   if an option doesn't fulfill the specified validation rules
     */
    public function all(int|string|null $project_id = null, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            $utc = (new \DateTimeImmutable($value->format(\DateTimeImmutable::RFC3339_EXTENDED)))->setTimezone(new \DateTimeZone('UTC'));

            return $utc->format('Y-m-d\TH:i:s.v\Z');
        };
        $resolver->setDefined('iids')
            ->setAllowedTypes('iids', 'array')
            ->setAllowedValues('iids', function (array $value) {
                return \count($value) === \count(\array_filter($value, 'is_int'));
            })
        ;
        $resolver->setDefined('state')
            ->setAllowedValues('state', [self::STATE_ALL, self::STATE_MERGED, self::STATE_OPENED, self::STATE_CLOSED, self::STATE_LOCKED])
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
        $resolver->setDefined('with_merge_status_recheck')
            ->setAllowedTypes('with_merge_status_recheck', 'bool')
        ;
        $resolver->setDefined('approved_by_ids')
            ->setAllowedTypes('approved_by_ids', 'array')
            ->setAllowedValues('approved_by_ids', function (array $value) {
                return \count($value) === \count(\array_filter($value, 'is_int'));
            })
        ;
        $resolver->setDefined('reviewer_id')
            ->setAllowedTypes('reviewer_id', 'integer');
        $resolver->setDefined('wip')
            ->setAllowedTypes('wip', 'boolean')
            ->addNormalizer('wip', static function ($resolver, $wip) {
                return $wip ? 'yes' : 'no';
            });

        $path = null === $project_id ? 'merge_requests' : $this->getProjectPath($project_id, 'merge_requests');

        return $this->get($path, $resolver->resolve($parameters));
    }

    /**
     * @param array      $parameters {
     *
     *     @var bool               $include_diverged_commits_count      Return the commits behind the target branch
     *     @var bool               $include_rebase_in_progress          Return whether a rebase operation is in progress
     * }
     */
    public function show(int|string $project_id, int $mr_iid, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('include_diverged_commits_count')
            ->setAllowedTypes('include_diverged_commits_count', 'bool')
        ;
        $resolver->setDefined('include_rebase_in_progress')
            ->setAllowedTypes('include_rebase_in_progress', 'bool')
        ;

        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid)), $resolver->resolve($parameters));
    }

    /**
     * @param array<string,mixed> $parameters {
     *
     *     @var int        $assignee_id       the assignee id
     *     @var int|string $target_project_id the target project id
     *     @var string     $description       the description
     * }
     */
    public function create(int|string $project_id, string $source, string $target, string $title, array $parameters = []): mixed
    {
        $baseParams = [
            'source_branch' => $source,
            'target_branch' => $target,
            'title' => $title,
        ];

        return $this->post(
            $this->getProjectPath($project_id, 'merge_requests'),
            \array_merge($baseParams, $parameters)
        );
    }

    public function update(int|string $project_id, int $mr_iid, array $parameters): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid)), $parameters);
    }

    public function merge(int|string $project_id, int $mr_iid, array $parameters = []): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/merge'), $parameters);
    }

    public function showNotes(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/notes'));
    }

    public function showNote(int|string $project_id, int $mr_iid, int $note_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/notes/'.self::encodePath($note_id)));
    }

    public function addNote(int|string $project_id, int $mr_iid, string $body, array $params = []): mixed
    {
        $params['body'] = $body;

        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/notes'), $params);
    }

    public function updateNote(int|string $project_id, int $mr_iid, int $note_id, string $body): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/notes/'.self::encodePath($note_id)), [
            'body' => $body,
        ]);
    }

    public function removeNote(int|string $project_id, int $mr_iid, int $note_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/notes/'.self::encodePath($note_id)));
    }

    public function showDiscussions(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid)).'/discussions');
    }

    public function showDiscussion(int|string $project_id, int $mr_iid, string $discussion_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid)).'/discussions/'.self::encodePath($discussion_id));
    }

    public function addDiscussion(int|string $project_id, int $mr_iid, array $params): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/discussions'), $params);
    }

    public function resolveDiscussion(int|string $project_id, int $mr_iid, string $discussion_id, bool $resolved = true): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/discussions/'.self::encodePath($discussion_id)), [
            'resolved' => $resolved,
        ]);
    }

    public function addDiscussionNote(int|string $project_id, int $mr_iid, string $discussion_id, string $body): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/discussions/'.self::encodePath($discussion_id).'/notes'), ['body' => $body]);
    }

    public function updateDiscussionNote(int|string $project_id, int $mr_iid, string $discussion_id, int $note_id, array $params): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/discussions/'.self::encodePath($discussion_id).'/notes/'.self::encodePath($note_id)), $params);
    }

    public function removeDiscussionNote(int|string $project_id, int $mr_iid, string $discussion_id, int $note_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/discussions/'.self::encodePath($discussion_id).'/notes/'.self::encodePath($note_id)));
    }

    public function showParticipants(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid)).'/participants');
    }

    public function changes(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/changes'));
    }

    public function commits(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/commits'));
    }

    public function closesIssues(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/closes_issues'));
    }

    public function approvals(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/approvals'));
    }

    public function approve(int|string $project_id, int $mr_iid): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/approve'));
    }

    public function unapprove(int|string $project_id, int $mr_iid): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/unapprove'));
    }

    public function awardEmoji(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/award_emoji'));
    }

    public function removeAwardEmoji(int|string $project_id, int $mr_iid, int $award_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/award_emoji/'.self::encodePath($award_id)));
    }

    public function rebase(int|string $project_id, int $mr_iid, array $params = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('skip_ci')
            ->setAllowedTypes('skip_ci', 'bool');

        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid)).'/rebase', $resolver->resolve($params));
    }

    public function approvalState(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/approval_state'));
    }

    public function levelRules(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/approval_rules'));
    }

    /**
     * @param array<string,mixed> $parameters
     */
    public function createLevelRule(int|string $project_id, int $mr_iid, string $name, int $approvals_required, array $parameters = []): mixed
    {
        $baseParam = [
            'name' => $name,
            'approvals_required' => $approvals_required,
        ];

        return $this->post(
            $this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/approval_rules'),
            \array_merge($baseParam, $parameters)
        );
    }

    /**
     * @param array<string,mixed> $parameters
     */
    public function updateLevelRule(int|string $project_id, int $mr_iid, int $approval_rule_id, string $name, int $approvals_required, array $parameters = []): mixed
    {
        $baseParam = [
            'name' => $name,
            'approvals_required' => $approvals_required,
        ];

        return $this->put(
            $this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/approval_rules/'.self::encodePath($approval_rule_id)),
            \array_merge($baseParam, $parameters)
        );
    }

    public function deleteLevelRule(int|string $project_id, int $mr_iid, int $approval_rule_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/approval_rules/'.self::encodePath($approval_rule_id)));
    }
}
