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
     * @param array      $parameters {
     *
     *     @var int[]  $iids   return only the milestones having the given iids
     *     @var string $state  return only active or closed milestones
     *     @var string $search Return only milestones with a title or description matching the provided string
     *     @var \DateTimeInterface $updated_after Return only milestones updated on or after the given datetime. Expected in ISO 8601 format (2019-03-15T08:00:00Z)
     *     @var \DateTimeInterface $updated_before Return only milestones updated on or before the given datetime. Expected in ISO 8601 format (2019-03-15T08:00:00Z)
     * }
     */
    public function all(int|string $group_id, array $parameters = []): mixed
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
            ->setAllowedValues('state', [self::STATE_ACTIVE, self::STATE_CLOSED])
        ;
        $resolver->setDefined('search');

        $resolver->setDefined('updated_after')
            ->setAllowedTypes('updated_after', \DateTimeInterface::class)
            ->setNormalizer('updated_after', $datetimeNormalizer);
        $resolver->setDefined('updated_before')
            ->setAllowedTypes('updated_before', \DateTimeInterface::class)
            ->setNormalizer('updated_before', $datetimeNormalizer);

        return $this->get('groups/'.self::encodePath($group_id).'/milestones', $resolver->resolve($parameters));
    }

    public function show(int|string $group_id, int $milestone_id): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/milestones/'.self::encodePath($milestone_id));
    }

    public function create(int|string $group_id, array $params): mixed
    {
        return $this->post('groups/'.self::encodePath($group_id).'/milestones', $params);
    }

    public function update(int|string $group_id, int $milestone_id, array $params): mixed
    {
        return $this->put('groups/'.self::encodePath($group_id).'/milestones/'.self::encodePath($milestone_id), $params);
    }

    public function remove(int|string $group_id, int $milestone_id): mixed
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/milestones/'.self::encodePath($milestone_id));
    }

    public function issues(int|string $group_id, int $milestone_id): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/milestones/'.self::encodePath($milestone_id).'/issues');
    }

    public function mergeRequests(int|string $group_id, int $milestone_id): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/milestones/'.self::encodePath($milestone_id).'/merge_requests');
    }
}
