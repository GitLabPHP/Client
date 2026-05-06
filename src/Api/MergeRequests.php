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
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * @param array $parameters {
     *
     *     @var int[]              $iids                      return merge requests having the given IIDs
     *     @var array|string       $approved_by_ids           return merge requests approved by the given user IDs
     *     @var array|string       $approved_by_usernames     return merge requests approved by the given usernames
     *     @var array|string       $approver_ids              return merge requests with the given eligible approver IDs
     *     @var int|string         $assignee_id               return merge requests assigned to the given user ID, Any, or None
     *     @var string[]           $assignee_username         return merge requests assigned to the given usernames
     *     @var int                $author_id                 return merge requests created by the given user ID
     *     @var string             $author_username           return merge requests created by the given username
     *     @var \DateTimeInterface $created_after             return merge requests created on or after the given time
     *     @var \DateTimeInterface $created_before            return merge requests created on or before the given time
     *     @var \DateTimeInterface $deployed_after            return merge requests deployed after the given time
     *     @var \DateTimeInterface $deployed_before           return merge requests deployed before the given time
     *     @var string             $environment               return merge requests deployed to the given environment
     *     @var string             $in                        change the scope of the search attribute
     *     @var string             $labels                    return merge requests matching a comma separated list of labels
     *     @var int                $merge_user_id             return merge requests merged by the given user ID
     *     @var string             $merge_user_username       return merge requests merged by the given username
     *     @var string             $milestone                 return merge requests for a specific milestone
     *     @var string             $my_reaction_emoji         return merge requests reacted to by the authenticated user
     *     @var bool               $non_archived              return merge requests from non-archived projects only
     *     @var array<string,mixed> $not                      return merge requests that do not match the supplied filters
     *     @var string             $order_by                  return requests ordered by the given field
     *     @var int|string         $reviewer_id               return merge requests reviewed by the given user ID, Any, or None
     *     @var string             $reviewer_username         return merge requests reviewed by the given username
     *     @var string             $scope                     return merge requests for the given scope
     *     @var string             $search                    search merge requests by title and description
     *     @var string             $sort                      return requests sorted in asc or desc order
     *     @var string             $source_branch             return merge requests with the given source branch
     *     @var string             $state                     return merge requests with the given state
     *     @var string             $target_branch             return merge requests with the given target branch
     *     @var \DateTimeInterface $updated_after             return merge requests updated on or after the given time
     *     @var \DateTimeInterface $updated_before            return merge requests updated on or before the given time
     *     @var string             $view                      if simple, returns a limited set of merge request fields
     *     @var bool               $with_labels_details       include label details in each merge request
     *     @var bool               $with_merge_status_recheck request an asynchronous merge status recalculation
     *     @var bool|string        $wip                       return only draft or only non-draft merge requests
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
        $integerArrayValidator = function (array $value): bool {
            return \count($value) === \count(\array_filter($value, 'is_int'));
        };
        $stringArrayValidator = function (array $value): bool {
            return \count($value) === \count(\array_filter($value, 'is_string'));
        };
        $anyNoneValidator = function ($value): bool {
            return \in_array($value, ['Any', 'None'], true);
        };
        $idOrAnyNoneValidator = function ($value) use ($anyNoneValidator): bool {
            return \is_int($value) || $anyNoneValidator($value);
        };
        $idArrayOrAnyNoneValidator = function ($value) use ($idOrAnyNoneValidator, $anyNoneValidator): bool {
            if (\is_string($value)) {
                return $anyNoneValidator($value);
            }

            if (!\is_array($value)) {
                return false;
            }

            return \count($value) === \count(\array_filter($value, $idOrAnyNoneValidator));
        };
        $stringOrStringArrayValidator = function ($value) use ($stringArrayValidator): bool {
            if (\is_string($value)) {
                return true;
            }

            return \is_array($value) && $stringArrayValidator($value);
        };
        $notFilterValidator = function (array $value): bool {
            return [] === \array_diff(\array_keys($value), [
                'labels',
                'milestone',
                'author_id',
                'author_username',
                'assignee_id',
                'assignee_username',
                'reviewer_id',
                'reviewer_username',
                'my_reaction_emoji',
            ]);
        };

        $resolver->setDefined('iids')
            ->setAllowedTypes('iids', 'array')
            ->setAllowedValues('iids', $integerArrayValidator)
        ;
        $resolver->setDefined('approved_by_ids')
            ->setAllowedTypes('approved_by_ids', ['array', 'string'])
            ->setAllowedValues('approved_by_ids', $idArrayOrAnyNoneValidator)
        ;
        $resolver->setDefined('approved_by_usernames')
            ->setAllowedTypes('approved_by_usernames', ['array', 'string'])
            ->setAllowedValues('approved_by_usernames', $stringOrStringArrayValidator)
        ;
        $resolver->setDefined('approver_ids')
            ->setAllowedTypes('approver_ids', ['array', 'string'])
            ->setAllowedValues('approver_ids', $idArrayOrAnyNoneValidator)
        ;
        $resolver->setDefined('assignee_id')
            ->setAllowedTypes('assignee_id', ['integer', 'string'])
            ->setAllowedValues('assignee_id', $idOrAnyNoneValidator)
        ;
        $resolver->setDefined('assignee_username')
            ->setAllowedTypes('assignee_username', 'array')
            ->setAllowedValues('assignee_username', $stringArrayValidator)
        ;
        $resolver->setDefined('author_id')
            ->setAllowedTypes('author_id', 'integer')
        ;
        $resolver->setDefined('author_username')
            ->setAllowedTypes('author_username', 'string')
        ;
        $resolver->setDefined('created_after')
            ->setAllowedTypes('created_after', \DateTimeInterface::class)
            ->setNormalizer('created_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('created_before')
            ->setAllowedTypes('created_before', \DateTimeInterface::class)
            ->setNormalizer('created_before', $datetimeNormalizer)
        ;
        $resolver->setDefined('deployed_after')
            ->setAllowedTypes('deployed_after', \DateTimeInterface::class)
            ->setNormalizer('deployed_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('deployed_before')
            ->setAllowedTypes('deployed_before', \DateTimeInterface::class)
            ->setNormalizer('deployed_before', $datetimeNormalizer)
        ;
        $resolver->setDefined('environment')
            ->setAllowedTypes('environment', 'string')
        ;
        $resolver->setDefined('in')
            ->setAllowedValues('in', ['title', 'description', 'title,description', 'description,title'])
        ;
        $resolver->setDefined('labels')
            ->setAllowedTypes('labels', 'string')
        ;
        $resolver->setDefined('merge_user_id')
            ->setAllowedTypes('merge_user_id', 'integer')
        ;
        $resolver->setDefined('merge_user_username')
            ->setAllowedTypes('merge_user_username', 'string')
        ;
        $resolver->setDefined('milestone')
            ->setAllowedTypes('milestone', 'string')
        ;
        $resolver->setDefined('my_reaction_emoji')
            ->setAllowedTypes('my_reaction_emoji', 'string')
        ;
        $resolver->setDefined('non_archived')
            ->setAllowedTypes('non_archived', 'bool')
        ;
        $resolver->setDefined('not')
            ->setAllowedTypes('not', 'array')
            ->setAllowedValues('not', $notFilterValidator)
        ;
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['created_at', 'updated_at', 'merged_at', 'label_priority', 'priority', 'milestone_due', 'popularity', 'title'])
        ;
        $resolver->setDefined('reviewer_id')
            ->setAllowedTypes('reviewer_id', ['integer', 'string'])
            ->setAllowedValues('reviewer_id', $idOrAnyNoneValidator)
        ;
        $resolver->setDefined('reviewer_username')
            ->setAllowedTypes('reviewer_username', 'string')
        ;
        $resolver->setDefined('scope')
            ->setAllowedValues('scope', ['created_by_me', 'assigned_to_me', 'reviews_for_me', 'all'])
        ;
        $resolver->setDefined('search')
            ->setAllowedTypes('search', 'string')
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;
        $resolver->setDefined('source_branch')
            ->setAllowedTypes('source_branch', 'string')
        ;
        $resolver->setDefined('state')
            ->setAllowedValues('state', [self::STATE_ALL, self::STATE_MERGED, self::STATE_OPENED, self::STATE_CLOSED, self::STATE_LOCKED])
        ;
        $resolver->setDefined('target_branch')
            ->setAllowedTypes('target_branch', 'string')
        ;
        $resolver->setDefined('updated_after')
            ->setAllowedTypes('updated_after', \DateTimeInterface::class)
            ->setNormalizer('updated_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('updated_before')
            ->setAllowedTypes('updated_before', \DateTimeInterface::class)
            ->setNormalizer('updated_before', $datetimeNormalizer)
        ;
        $resolver->setDefined('view')
            ->setAllowedValues('view', ['simple'])
        ;
        $resolver->setDefined('with_labels_details')
            ->setAllowedTypes('with_labels_details', 'bool')
        ;
        $resolver->setDefined('with_merge_status_recheck')
            ->setAllowedTypes('with_merge_status_recheck', 'bool')
        ;
        $resolver->setDefined('wip')
            ->setAllowedTypes('wip', ['bool', 'string'])
            ->setAllowedValues('wip', [true, false, 'yes', 'no'])
            ->addNormalizer('wip', static function ($resolver, $wip) {
                if (\is_string($wip)) {
                    return $wip;
                }

                return $wip ? 'yes' : 'no';
            })
        ;

        $path = null === $project_id ? 'merge_requests' : $this->getProjectPath($project_id, 'merge_requests');

        return $this->get($path, $resolver->resolve($parameters));
    }

    /**
     * @param array      $parameters {
     *
     *     @var bool               $include_diverged_commits_count      Return the commits behind the target branch
     *     @var bool               $include_rebase_in_progress          Return whether a rebase operation is in progress
     *     @var bool               $render_html                         Return `title` and `description` fields as HTML
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
        $resolver->setDefined('render_html')
            ->setAllowedTypes('render_html', 'bool')
        ;

        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid)), $resolver->resolve($parameters));
    }

    /**
     * @param array $parameters {
     *
     *     @var bool     $allow_collaboration    allow commits from upstream members
     *     @var bool     $allow_maintainer_to_push deprecated; use allow_collaboration instead
     *     @var int      $approvals_before_merge deprecated; use merge request approvals instead
     *     @var int      $assignee_id            the assignee id
     *     @var int[]    $assignee_ids           the assignee ids
     *     @var string   $description            the description
     *     @var string   $labels                 labels for the merge request
     *     @var string   $merge_after            date after which the merge request can be merged
     *     @var int      $milestone_id           the milestone id
     *     @var bool     $remove_source_branch   remove the source branch when merged
     *     @var int[]    $reviewer_ids           the reviewer ids
     *     @var bool     $squash                 squash commits into one commit when merged
     *     @var int      $target_project_id      the target project id
     * }
     */
    public function create(int|string $project_id, string $source, string $target, string $title, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('allow_collaboration')
            ->setAllowedTypes('allow_collaboration', 'bool')
        ;
        $resolver->setDefined('allow_maintainer_to_push')
            ->setAllowedTypes('allow_maintainer_to_push', 'bool')
        ;
        $resolver->setDefined('approvals_before_merge')
            ->setAllowedTypes('approvals_before_merge', 'int')
        ;
        $resolver->setDefined('assignee_id')
            ->setAllowedTypes('assignee_id', 'int')
        ;
        $resolver->setDefined('assignee_ids')
            ->setAllowedTypes('assignee_ids', 'array')
            ->setAllowedValues('assignee_ids', static function (array $value): bool {
                return self::isIntegerArray($value);
            })
        ;
        $resolver->setDefined('description')
            ->setAllowedTypes('description', 'string')
        ;
        $resolver->setDefined('labels')
            ->setAllowedTypes('labels', 'string')
        ;
        $resolver->setDefined('merge_after')
            ->setAllowedTypes('merge_after', 'string')
        ;
        $resolver->setDefined('milestone_id')
            ->setAllowedTypes('milestone_id', 'int')
        ;
        $resolver->setDefined('remove_source_branch')
            ->setAllowedTypes('remove_source_branch', 'bool')
        ;
        $resolver->setDefined('reviewer_ids')
            ->setAllowedTypes('reviewer_ids', 'array')
            ->setAllowedValues('reviewer_ids', static function (array $value): bool {
                return self::isIntegerArray($value);
            })
        ;
        $resolver->setDefined('squash')
            ->setAllowedTypes('squash', 'bool')
        ;
        $resolver->setDefined('target_project_id')
            ->setAllowedTypes('target_project_id', 'int')
        ;

        $baseParams = [
            'source_branch' => $source,
            'target_branch' => $target,
            'title' => $title,
        ];
        $parameters = $resolver->resolve($parameters);

        return $this->post(
            $this->getProjectPath($project_id, 'merge_requests'),
            \array_merge($baseParams, $parameters)
        );
    }

    /**
     * @param array $parameters {
     *
     *     @var string $add_labels               labels to add to the merge request
     *     @var bool   $allow_collaboration      allow commits from upstream members
     *     @var bool   $allow_maintainer_to_push deprecated; use allow_collaboration instead
     *     @var int    $assignee_id              the assignee id
     *     @var int[]  $assignee_ids             the assignee ids
     *     @var string $description              the description
     *     @var bool   $discussion_locked        lock discussions on the merge request
     *     @var string $labels                   labels for the merge request
     *     @var string $merge_after              date after which the merge request can be merged
     *     @var int    $milestone_id             the milestone id
     *     @var string $remove_labels            labels to remove from the merge request
     *     @var bool   $remove_source_branch     remove the source branch when merged
     *     @var int[]  $reviewer_ids             the reviewer ids
     *     @var bool   $squash                   squash commits into one commit when merged
     *     @var string $state_event              close or reopen the merge request
     *     @var string $target_branch            the target branch
     *     @var string $title                    the title
     * }
     */
    public function update(int|string $project_id, int $mr_iid, array $parameters): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('add_labels')
            ->setAllowedTypes('add_labels', 'string')
        ;
        $resolver->setDefined('allow_collaboration')
            ->setAllowedTypes('allow_collaboration', 'bool')
        ;
        $resolver->setDefined('allow_maintainer_to_push')
            ->setAllowedTypes('allow_maintainer_to_push', 'bool')
        ;
        $resolver->setDefined('assignee_id')
            ->setAllowedTypes('assignee_id', 'int')
        ;
        $resolver->setDefined('assignee_ids')
            ->setAllowedTypes('assignee_ids', 'array')
            ->setAllowedValues('assignee_ids', static function (array $value): bool {
                return self::isIntegerArray($value);
            })
        ;
        $resolver->setDefined('description')
            ->setAllowedTypes('description', 'string')
        ;
        $resolver->setDefined('discussion_locked')
            ->setAllowedTypes('discussion_locked', 'bool')
        ;
        $resolver->setDefined('labels')
            ->setAllowedTypes('labels', 'string')
        ;
        $resolver->setDefined('merge_after')
            ->setAllowedTypes('merge_after', 'string')
        ;
        $resolver->setDefined('milestone_id')
            ->setAllowedTypes('milestone_id', 'int')
        ;
        $resolver->setDefined('remove_labels')
            ->setAllowedTypes('remove_labels', 'string')
        ;
        $resolver->setDefined('remove_source_branch')
            ->setAllowedTypes('remove_source_branch', 'bool')
        ;
        $resolver->setDefined('reviewer_ids')
            ->setAllowedTypes('reviewer_ids', 'array')
            ->setAllowedValues('reviewer_ids', static function (array $value): bool {
                return self::isIntegerArray($value);
            })
        ;
        $resolver->setDefined('squash')
            ->setAllowedTypes('squash', 'bool')
        ;
        $resolver->setDefined('state_event')
            ->setAllowedTypes('state_event', 'string')
            ->setAllowedValues('state_event', ['close', 'reopen'])
        ;
        $resolver->setDefined('target_branch')
            ->setAllowedTypes('target_branch', 'string')
        ;
        $resolver->setDefined('title')
            ->setAllowedTypes('title', 'string')
        ;

        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid)), $resolver->resolve($parameters));
    }

    public function remove(int|string $project_id, int $mr_iid): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid)));
    }

    /**
     * @param array $parameters {
     *
     *     @var bool   $auto_merge                    merge when checks pass
     *     @var string $merge_commit_message          custom merge commit message
     *     @var bool   $merge_when_pipeline_succeeds  deprecated; use auto_merge instead
     *     @var string $sha                           SHA that must match the merge request HEAD
     *     @var bool   $should_remove_source_branch   remove the source branch
     *     @var string $squash_commit_message         custom squash commit message
     *     @var bool   $squash                        squash commits into a single commit
     * }
     */
    public function merge(int|string $project_id, int $mr_iid, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('auto_merge')
            ->setAllowedTypes('auto_merge', 'bool')
        ;
        $resolver->setDefined('merge_commit_message')
            ->setAllowedTypes('merge_commit_message', 'string')
        ;
        $resolver->setDefined('merge_when_pipeline_succeeds')
            ->setAllowedTypes('merge_when_pipeline_succeeds', 'bool')
        ;
        $resolver->setDefined('sha')
            ->setAllowedTypes('sha', 'string')
        ;
        $resolver->setDefined('should_remove_source_branch')
            ->setAllowedTypes('should_remove_source_branch', 'bool')
        ;
        $resolver->setDefined('squash_commit_message')
            ->setAllowedTypes('squash_commit_message', 'string')
        ;
        $resolver->setDefined('squash')
            ->setAllowedTypes('squash', 'bool')
        ;

        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/merge'), $resolver->resolve($parameters));
    }

    /**
     * @param array $parameters {
     *
     *     @var bool   $auto_merge             add the merge request to the merge train when checks pass
     *     @var string $sha                    must match the HEAD of the source branch if present
     *     @var bool   $squash                 squash commits into a single commit on merge
     *     @var bool   $when_pipeline_succeeds deprecated in GitLab 17.11. Use auto_merge instead.
     * }
     */
    public function addToMergeTrain(int|string $project_id, int $mr_iid, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('auto_merge')
            ->setAllowedTypes('auto_merge', 'bool')
        ;
        $resolver->setDefined('sha')
            ->setAllowedTypes('sha', 'string')
        ;
        $resolver->setDefined('squash')
            ->setAllowedTypes('squash', 'bool')
        ;
        $resolver->setDefined('when_pipeline_succeeds')
            ->setAllowedTypes('when_pipeline_succeeds', 'bool')
        ;

        return $this->post($this->getProjectPath($project_id, 'merge_trains/merge_requests/'.self::encodePath($mr_iid)), $resolver->resolve($parameters));
    }

    /**
     * @param array $parameters {
     *
     *     @var string $sort     return notes sorted in asc or desc order
     *     @var string $order_by return notes ordered by created_at or updated_at
     * }
     */
    public function showNotes(int|string $project_id, int $mr_iid, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['created_at', 'updated_at'])
        ;

        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/notes'), $resolver->resolve($parameters));
    }

    public function showNote(int|string $project_id, int $mr_iid, int $note_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/notes/'.self::encodePath($note_id)));
    }

    /**
     * @param array $params {
     *
     *     @var string $created_at                  creation timestamp for admins or project owners
     *     @var bool   $internal                    mark the note as internal
     *     @var string $merge_request_diff_head_sha merge request diff head SHA
     * }
     */
    public function addNote(int|string $project_id, int $mr_iid, string $body, array $params = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('created_at')
            ->setAllowedTypes('created_at', 'string')
        ;
        $resolver->setDefined('internal')
            ->setAllowedTypes('internal', 'bool')
        ;
        $resolver->setDefined('merge_request_diff_head_sha')
            ->setAllowedTypes('merge_request_diff_head_sha', 'string')
        ;

        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/notes'), \array_merge([
            'body' => $body,
        ], $resolver->resolve($params)));
    }

    /**
     * @param array $params {
     *
     *     @var bool $confidential deprecated; use internal instead
     * }
     */
    public function updateNote(int|string $project_id, int $mr_iid, int $note_id, string $body, array $params = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('confidential')
            ->setAllowedTypes('confidential', 'bool')
        ;

        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/notes/'.self::encodePath($note_id)), \array_merge([
            'body' => $body,
        ], $resolver->resolve($params)));
    }

    public function removeNote(int|string $project_id, int $mr_iid, int $note_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/notes/'.self::encodePath($note_id)));
    }

    public function showNoteAwardEmojis(int|string $project_id, int $mr_iid, int $note_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/notes/'.self::encodePath($note_id).'/award_emoji'));
    }

    public function showNoteAwardEmoji(int|string $project_id, int $mr_iid, int $note_id, int $award_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/notes/'.self::encodePath($note_id).'/award_emoji/'.self::encodePath($award_id)));
    }

    public function addNoteAwardEmoji(int|string $project_id, int $mr_iid, int $note_id, string $name): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/notes/'.self::encodePath($note_id).'/award_emoji'), ['name' => $name]);
    }

    public function removeNoteAwardEmoji(int|string $project_id, int $mr_iid, int $note_id, int $award_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/notes/'.self::encodePath($note_id).'/award_emoji/'.self::encodePath($award_id)));
    }

    public function showDiscussions(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid)).'/discussions');
    }

    public function showDiscussion(int|string $project_id, int $mr_iid, string $discussion_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid)).'/discussions/'.self::encodePath($discussion_id));
    }

    /**
     * @param array $params {
     *
     *     @var string $body       the note body
     *     @var string $commit_id  commit SHA for a commit-specific discussion
     *     @var string $created_at creation timestamp for admins or project owners
     *     @var array  $position   position parameters for a diff discussion
     * }
     */
    public function addDiscussion(int|string $project_id, int $mr_iid, array $params): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('body')
            ->setAllowedTypes('body', 'string')
        ;
        $resolver->setDefined('commit_id')
            ->setAllowedTypes('commit_id', 'string')
        ;
        $resolver->setDefined('created_at')
            ->setAllowedTypes('created_at', 'string')
        ;
        $resolver->setDefined('position')
            ->setAllowedTypes('position', 'array')
        ;

        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/discussions'), $resolver->resolve($params));
    }

    public function resolveDiscussion(int|string $project_id, int $mr_iid, string $discussion_id, bool $resolved = true): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/discussions/'.self::encodePath($discussion_id)), [
            'resolved' => $resolved,
        ]);
    }

    /**
     * @param array $params {
     *
     *     @var string $created_at creation timestamp for admins or project owners
     * }
     */
    public function addDiscussionNote(int|string $project_id, int $mr_iid, string $discussion_id, string $body, array $params = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('created_at')
            ->setAllowedTypes('created_at', 'string')
        ;

        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/discussions/'.self::encodePath($discussion_id).'/notes'), \array_merge(['body' => $body], $resolver->resolve($params)));
    }

    /**
     * @param array $params {
     *
     *     @var string $body     the note body
     *     @var bool   $resolved resolve or unresolve the note's discussion
     * }
     */
    public function updateDiscussionNote(int|string $project_id, int $mr_iid, string $discussion_id, int $note_id, array $params): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('body')
            ->setAllowedTypes('body', 'string')
        ;
        $resolver->setDefined('resolved')
            ->setAllowedTypes('resolved', 'bool')
        ;

        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/discussions/'.self::encodePath($discussion_id).'/notes/'.self::encodePath($note_id)), $resolver->resolve($params));
    }

    public function removeDiscussionNote(int|string $project_id, int $mr_iid, string $discussion_id, int $note_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/discussions/'.self::encodePath($discussion_id).'/notes/'.self::encodePath($note_id)));
    }

    public function showParticipants(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid)).'/participants');
    }

    public function showResourceLabelEvents(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid)).'/resource_label_events');
    }

    public function showResourceLabelEvent(int|string $project_id, int $mr_iid, int $resource_label_event_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid)).'/resource_label_events/'.self::encodePath($resource_label_event_id));
    }

    /**
     * @param array $parameters {
     *
     *     @var bool $access_raw_diffs access raw diffs
     *     @var bool $unidiff          present diffs in unified diff format
     * }
     */
    public function changes(int|string $project_id, int $mr_iid, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('access_raw_diffs')
            ->setAllowedTypes('access_raw_diffs', 'bool')
        ;
        $resolver->setDefined('unidiff')
            ->setAllowedTypes('unidiff', 'bool')
        ;

        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/changes'), $resolver->resolve($parameters));
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

    /**
     * @param array $parameters {
     *
     *     @var string $approval_password current user's password
     *     @var string $sha               SHA that must match the merge request HEAD
     * }
     */
    public function approve(int|string $project_id, int $mr_iid, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('approval_password')
            ->setAllowedTypes('approval_password', 'string')
        ;
        $resolver->setDefined('sha')
            ->setAllowedTypes('sha', 'string')
        ;

        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/approve'), $resolver->resolve($parameters));
    }

    public function unapprove(int|string $project_id, int $mr_iid): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/unapprove'));
    }

    public function awardEmoji(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/award_emoji'));
    }

    public function showAwardEmoji(int|string $project_id, int $mr_iid, int $award_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/award_emoji/'.self::encodePath($award_id)));
    }

    public function addAwardEmoji(int|string $project_id, int $mr_iid, string $name): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/award_emoji'), ['name' => $name]);
    }

    public function removeAwardEmoji(int|string $project_id, int $mr_iid, int $award_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/award_emoji/'.self::encodePath($award_id)));
    }

    /**
     * @param array $params {
     *
     *     @var bool $skip_ci skip the CI pipeline
     * }
     */
    public function rebase(int|string $project_id, int $mr_iid, array $params = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('skip_ci')
            ->setAllowedTypes('skip_ci', 'bool');

        return $this->put($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid)).'/rebase', $resolver->resolve($params));
    }

    public function approvalState(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/approval_state'));
    }

    public function levelRules(int|string $project_id, int $mr_iid, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/approval_rules'), $resolver->resolve($parameters));
    }

    /**
     * @param array $parameters {
     *
     *     @var int      $approval_project_rule_id approval project rule id
     *     @var int[]    $group_ids                group ids
     *     @var int[]    $user_ids                 user ids
     *     @var string[] $usernames                usernames
     * }
     */
    public function createLevelRule(int|string $project_id, int $mr_iid, string $name, int $approvals_required, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('approval_project_rule_id')
            ->setAllowedTypes('approval_project_rule_id', 'int')
        ;
        $resolver->setDefined('group_ids')
            ->setAllowedTypes('group_ids', 'array')
            ->setAllowedValues('group_ids', static function (array $value): bool {
                return self::isIntegerArray($value);
            })
        ;
        $resolver->setDefined('user_ids')
            ->setAllowedTypes('user_ids', 'array')
            ->setAllowedValues('user_ids', static function (array $value): bool {
                return self::isIntegerArray($value);
            })
        ;
        $resolver->setDefined('usernames')
            ->setAllowedTypes('usernames', 'array')
            ->setAllowedValues('usernames', static function (array $value): bool {
                return self::isStringArray($value);
            })
        ;

        $baseParam = [
            'name' => $name,
            'approvals_required' => $approvals_required,
        ];

        return $this->post(
            $this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/approval_rules'),
            \array_merge($baseParam, $resolver->resolve($parameters))
        );
    }

    /**
     * @param array $parameters {
     *
     *     @var int[]    $group_ids            group ids
     *     @var bool     $remove_hidden_groups remove hidden groups
     *     @var int[]    $user_ids             user ids
     *     @var string[] $usernames            usernames
     * }
     */
    public function updateLevelRule(int|string $project_id, int $mr_iid, int $approval_rule_id, string $name, int $approvals_required, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('group_ids')
            ->setAllowedTypes('group_ids', 'array')
            ->setAllowedValues('group_ids', static function (array $value): bool {
                return self::isIntegerArray($value);
            })
        ;
        $resolver->setDefined('remove_hidden_groups')
            ->setAllowedTypes('remove_hidden_groups', 'bool')
        ;
        $resolver->setDefined('user_ids')
            ->setAllowedTypes('user_ids', 'array')
            ->setAllowedValues('user_ids', static function (array $value): bool {
                return self::isIntegerArray($value);
            })
        ;
        $resolver->setDefined('usernames')
            ->setAllowedTypes('usernames', 'array')
            ->setAllowedValues('usernames', static function (array $value): bool {
                return self::isStringArray($value);
            })
        ;

        $baseParam = [
            'name' => $name,
            'approvals_required' => $approvals_required,
        ];

        return $this->put(
            $this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/approval_rules/'.self::encodePath($approval_rule_id)),
            \array_merge($baseParam, $resolver->resolve($parameters))
        );
    }

    public function deleteLevelRule(int|string $project_id, int $mr_iid, int $approval_rule_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/approval_rules/'.self::encodePath($approval_rule_id)));
    }

    public function dependencies(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/blocks'));
    }

    public function showDependency(int|string $project_id, int $mr_iid, int $block_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/blocks/'.self::encodePath($block_id)));
    }

    /**
     * @param array $parameters {
     *
     *     @var int        $blocking_merge_request_id  global ID of the blocking merge request
     *     @var int        $blocking_merge_request_iid IID of the blocking merge request
     *     @var int|string $blocking_project_id        project containing the blocking merge request
     * }
     */
    public function createDependency(int|string $project_id, int $mr_iid, array $parameters): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('blocking_merge_request_id')
            ->setAllowedTypes('blocking_merge_request_id', 'int')
        ;
        $resolver->setDefined('blocking_merge_request_iid')
            ->setAllowedTypes('blocking_merge_request_iid', 'int')
        ;
        $resolver->setDefined('blocking_project_id')
            ->setAllowedTypes('blocking_project_id', ['int', 'string'])
        ;

        $parameters = $resolver->resolve($parameters);
        $hasBlockingId = isset($parameters['blocking_merge_request_id']);
        $hasBlockingIid = isset($parameters['blocking_merge_request_iid']);

        if ($hasBlockingId === $hasBlockingIid) {
            throw new InvalidOptionsException('Exactly one of "blocking_merge_request_id" or "blocking_merge_request_iid" must be provided.');
        }

        return $this->post($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/blocks'), $parameters);
    }

    public function deleteDependency(int|string $project_id, int $mr_iid, int $block_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/blocks/'.self::encodePath($block_id)));
    }

    public function blockedMergeRequests(int|string $project_id, int $mr_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/'.self::encodePath($mr_iid).'/blockees'));
    }

    private static function isIntegerArray(array $value): bool
    {
        return \count($value) === \count(\array_filter($value, 'is_int'));
    }

    private static function isStringArray(array $value): bool
    {
        return \count($value) === \count(\array_filter($value, 'is_string'));
    }
}
