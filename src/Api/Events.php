<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Niclas Hoyer <info@niclashoyer.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\Options;

class Events extends AbstractApi
{
    /**
     * @param array $parameters {
     *
     *     @var string             $action         include only events of a particular action type
     *     @var string             $target_type    include only events of a particular target type
     *     @var \DateTimeInterface $before         include only events created before a particular date
     *     @var \DateTimeInterface $after          include only events created after a particular date
     *     @var string             $scope          include all events across a user’s projects
     *     @var string             $sort           sort events in asc or desc order by created_at
     *
     * }
     *
     * @return mixed
     */
    public function all(array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('Y-m-d');
        };

        $resolver->setDefined('action');
        $resolver->setDefined('target_type');
        $resolver->setDefined('before')
            ->setAllowedTypes('before', \DateTimeInterface::class)
            ->setNormalizer('before', $datetimeNormalizer)
        ;
        $resolver->setDefined('after')
            ->setAllowedTypes('after', \DateTimeInterface::class)
            ->setNormalizer('after', $datetimeNormalizer)
        ;
        $resolver->setDefined('scope');
        $resolver->setDefined('sort');

        return $this->get('events', $resolver->resolve($parameters));
    }
}
