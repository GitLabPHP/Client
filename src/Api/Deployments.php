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

class Deployments extends AbstractApi
{
    /**
     * @param array      $parameters {
     *
     *     @var string             $order_by        Return deployments ordered by id, iid, created_at, updated_at, finished_at, or ref fields (default is id)
     *     @var string             $sort            Return deployments sorted in asc or desc order (default is asc)
     *     @var \DateTimeInterface $updated_after   Return deployments updated after the specified date
     *     @var \DateTimeInterface $updated_before  Return deployments updated before the specified date
     *     @var \DateTimeInterface $finished_after  Return deployments finished after the specified date. Use order_by finished_at and status success.
     *     @var \DateTimeInterface $finished_before Return deployments finished before the specified date. Use order_by finished_at and status success.
     *     @var string             $status          Return deployments filtered by status: created, running, success, failed, canceled, or blocked
     *     @var string             $environment     Return deployments filtered to a particular environment
     * }
     */
    public function all(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('c');
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
        $resolver->setDefined('status')
            ->setAllowedTypes('status', 'string')
            ->setAllowedValues('status', ['created', 'running', 'success', 'failed', 'canceled', 'blocked'])
        ;
        $resolver->setDefined('environment')
            ->setAllowedTypes('environment', 'string')
        ;

        return $this->get($this->getProjectPath($project_id, 'deployments'), $resolver->resolve($parameters));
    }

    public function show(int|string $project_id, int $deployment_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'deployments/'.$deployment_id));
    }

    /**
     * @param array $parameters {
     *
     *     @var string $state  return all merge requests or just those that are opened, closed, locked, or merged
     *     @var string $labels return merge requests matching a comma separated list of labels
     * }
     */
    public function mergeRequests(int|string $project_id, int $deployment_id, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('state')
            ->setAllowedValues('state', ['all', 'merged', 'opened', 'closed', 'locked'])
        ;
        $resolver->setDefined('labels');

        return $this->get($this->getProjectPath($project_id, 'deployments/'.$deployment_id.'/merge_requests'), $resolver->resolve($parameters));
    }
}
