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

class GroupsEpics extends AbstractApi
{
    /**
     * @var string
     */
    public const STATE_ALL = 'all';

    /**
     * @var string
     */
    public const STATE_OPENED = 'opened';

    /**
     * @var string
     */
    public const STATE_CLOSED = 'closed';

    /**
     * List all epics of a group.
     *
     * @see https://docs.gitlab.com/api/epics/#list-all-group-epics
     *
     * @param array      $parameters {
     *
     *     @var int[]  $iids   return only the epics having the given iids
     *     @var string $state  return only active or closed epics
     *     @var string $search Return only epics with a title or description matching the provided string.
     * }
     */
    public function all(int|string $group_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('iids')
            ->setAllowedTypes('iids', 'array')
            ->setAllowedValues('iids', function (array $value) {
                return \count($value) === \count(\array_filter($value, 'is_int'));
            })
        ;
        $resolver->setDefined('state')
            ->setAllowedValues('state', [self::STATE_ALL, self::STATE_OPENED, self::STATE_CLOSED])
        ;
        $resolver->setDefined('search');

        return $this->get('groups/'.self::encodePath($group_id).'/epics', $resolver->resolve($parameters));
    }

    /**
     * Get a single epic of a group.
     *
     * @see https://docs.gitlab.com/api/epics/#retrieve-an-epic
     */
    public function show(int|string $group_id, int $epic_id): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/epics/'.self::encodePath($epic_id));
    }

    /**
     * Create a new epic.
     *
     * @see https://docs.gitlab.com/api/epics/#create-an-epic
     */
    public function create(int|string $group_id, array $params): mixed
    {
        return $this->post('groups/'.self::encodePath($group_id).'/epics', $params);
    }

    /**
     * Update an existing epic.
     *
     * @see https://docs.gitlab.com/api/epics/#update-an-epic
     */
    public function update(int|string $group_id, int $epic_id, array $params): mixed
    {
        return $this->put('groups/'.self::encodePath($group_id).'/epics/'.self::encodePath($epic_id), $params);
    }

    /**
     * Delete an epic.
     *
     * @see https://docs.gitlab.com/api/epics/#delete-an-epic
     */
    public function remove(int|string $group_id, int $epic_id): mixed
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/epics/'.self::encodePath($epic_id));
    }

    /**
     * List all issues assigned to an epic.
     *
     * @see https://docs.gitlab.com/api/epic_issues/#list-all-issues-for-an-epic
     */
    public function issues(int|string $group_id, int $epic_iid): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/epics/'.self::encodePath($epic_iid).'/issues');
    }
}
