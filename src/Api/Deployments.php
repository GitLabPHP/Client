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

class Deployments extends AbstractApi
{
    /**
     * @param array      $parameters {
     *
     *     @var string $order_by                    Return deployments ordered by id, iid, created_at, updated_at, finished_at,
     *                                              or ref fields (default is id)
     *     @var string $sort                        Return deployments sorted in asc or desc order (default is desc)
     *     @var string $status                      Return deployments filtered by status of deployment allowed
     *                                              values of status are 'created', 'running', 'success', 'failed',
     *                                              'canceled', 'blocked'
     *     @var string $environment                 Return deployments filtered to a particular environment
     * }
     */
    public function all(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            $utc = (new \DateTimeImmutable($value->format(\DateTimeImmutable::RFC3339_EXTENDED)))->setTimezone(new \DateTimeZone('UTC'));

            return $utc->format('Y-m-d\TH:i:s.v\Z');
        };

        $resolver->setDefined('order_by')
            ->setAllowedTypes('order_by', 'string')
            ->setAllowedValues('order_by', ['id', 'iid', 'created_at', 'updated_at', 'finished_at', 'ref'])
        ;

        $resolver->setDefined('sort')
            ->setAllowedTypes('sort', 'string')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;

        $resolver->setDefined('updated_after')
            ->setAllowedTypes('updated_after', \DateTimeInterface::class)
            ->setNormalizer('updated_after', $datetimeNormalizer)
        ;

        $resolver->setDefined('updated_before')
            ->setAllowedTypes('updated_before', \DateTimeInterface::class)
            ->setNormalizer('updated_before', $datetimeNormalizer)
        ;

        $resolver->setDefined('finished_after')
            ->setAllowedTypes('finished_after', \DateTimeInterface::class)
            ->setNormalizer('finished_after', $datetimeNormalizer)
        ;

        $resolver->setDefined('finished_before')
            ->setAllowedTypes('finished_before', \DateTimeInterface::class)
            ->setNormalizer('finished_before', $datetimeNormalizer)
        ;

        $resolver->setDefined('environment')
            ->setAllowedTypes('environment', 'string')
        ;

        $resolver->setDefined('status')
            ->setAllowedTypes('status', 'string')
            ->setAllowedValues('status', ['created', 'running', 'success', 'failed', 'canceled', 'blocked'])
        ;

        return $this->get($this->getProjectPath($project_id, 'deployments'), $resolver->resolve($parameters));
    }

    public function show(int|string $project_id, int $deployment_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'deployments/'.$deployment_id));
    }
}
