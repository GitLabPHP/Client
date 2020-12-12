<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Api;

class GroupsMilestones extends AbstractApi
{
    /**
     * @var string
     */
    public const STATE_ACTIVE = 'active';

    /**
     * @var string
     */
    public const STATE_CLOSED = 'closed';

    /**
     * @param int|string $group_id
     * @param array      $parameters {
     *
     *     @var int[]  $iids   return only the milestones having the given iids
     *     @var string $state  return only active or closed milestones
     *     @var string $search Return only milestones with a title or description matching the provided string.
     * }
     *
     * @return mixed
     */
    public function all($group_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('iids')
            ->setAllowedTypes('iids', 'array')
            ->setAllowedValues('iids', function (array $value) {
                return \count($value) === \count(\array_filter($value, 'is_int'));
            })
        ;
        $resolver->setDefined('state')
            ->setAllowedValues('state', [self::STATE_ACTIVE, self::STATE_CLOSED])
        ;
        $resolver->setDefined('search');

        return $this->get('groups/'.self::encodePath($group_id).'/milestones', $resolver->resolve($parameters));
    }

    /**
     * @param int|string $group_id
     * @param int        $milestone_id
     *
     * @return mixed
     */
    public function show($group_id, int $milestone_id)
    {
        return $this->get('groups/'.self::encodePath($group_id).'/milestones/'.self::encodePath($milestone_id));
    }

    /**
     * @param int|string $group_id
     * @param array      $params
     *
     * @return mixed
     */
    public function create($group_id, array $params)
    {
        return $this->post('groups/'.self::encodePath($group_id).'/milestones', $params);
    }

    /**
     * @param int|string $group_id
     * @param int        $milestone_id
     * @param array      $params
     *
     * @return mixed
     */
    public function update($group_id, int $milestone_id, array $params)
    {
        return $this->put('groups/'.self::encodePath($group_id).'/milestones/'.self::encodePath($milestone_id), $params);
    }

    /**
     * @param int|string $group_id
     * @param int        $milestone_id
     *
     * @return mixed
     */
    public function remove($group_id, int $milestone_id)
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/milestones/'.self::encodePath($milestone_id));
    }

    /**
     * @param int|string $group_id
     * @param int        $milestone_id
     *
     * @return mixed
     */
    public function issues($group_id, int $milestone_id)
    {
        return $this->get('groups/'.self::encodePath($group_id).'/milestones/'.self::encodePath($milestone_id).'/issues');
    }

    /**
     * @param int|string $group_id
     * @param int        $milestone_id
     *
     * @return mixed
     */
    public function mergeRequests($group_id, int $milestone_id)
    {
        return $this->get('groups/'.self::encodePath($group_id).'/milestones/'.self::encodePath($milestone_id).'/merge_requests');
    }
}
