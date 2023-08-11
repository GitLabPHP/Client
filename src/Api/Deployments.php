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

class Deployments extends AbstractApi
{
    /**
     * @param int|string $project_id
     * @param array      $parameters {
     *
     *     @var string $order_by                    Return deployments ordered by id, iid, created_at, updated_at,
     *                                              or ref fields (default is id)
     *     @var string $sort                        Return deployments sorted in asc or desc order (default is desc)
     * }
     *
     * @return mixed
     */
    public function all($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('order_by')
            ->setAllowedTypes('order_by', 'string')
            ->setAllowedValues('order_by', ['id', 'iid', 'created_at', 'updated_at', 'ref']);
        $resolver->setDefined('sort')
            ->setAllowedTypes('sort', 'string')
            ->setAllowedValues('sort', ['desc', 'asc']);
        $resolver->setDefined('status')
            ->setAllowedTypes('status', 'string')
            ->setAllowedValues('status', ['created', 'running', 'success', 'failed', 'canceled','blocked']);
        $resolver->setDefined('environment')
            ->setAllowedTypes('environment', 'string');

        return $this->get($this->getProjectPath($project_id, 'deployments'), $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param int        $deployment_id
     *
     * @return mixed
     */
    public function show($project_id, int $deployment_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deployments/'.$deployment_id));
    }
}
