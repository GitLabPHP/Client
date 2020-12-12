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

class Deployments extends AbstractApi
{
    /**
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function all($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

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
