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

    public function show(int|string $group_id, int $epic_id): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/epics/'.self::encodePath($epic_id));
    }

    public function create(int|string $group_id, array $params): mixed
    {
        return $this->post('groups/'.self::encodePath($group_id).'/epics', $params);
    }

    public function update(int|string $group_id, int $epic_id, array $params): mixed
    {
        return $this->put('groups/'.self::encodePath($group_id).'/epics/'.self::encodePath($epic_id), $params);
    }

    public function remove(int|string $group_id, int $epic_id): mixed
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/epics/'.self::encodePath($epic_id));
    }

    public function issues(int|string $group_id, int $epic_iid): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/epics/'.self::encodePath($epic_iid).'/issues');
    }
}
