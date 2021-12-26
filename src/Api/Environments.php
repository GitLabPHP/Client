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

use Symfony\Component\OptionsResolver\OptionsResolver;

class Environments extends AbstractApi
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
        $resolver->setDefined('name')
            ->setAllowedTypes('name', 'string');
        $resolver->setDefined('search')
            ->setAllowedTypes('search', 'string');
        $resolver->setDefined('states')
            ->setAllowedTypes('states', 'string')
            ->setAllowedValues('states', ['available', 'stopped']);

        return $this->get($this->getProjectPath($project_id, 'environments'), $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters {
     *
     *     @var string $name         The name of the environment
     *     @var string $external_url Place to link to for this environment
     * }
     *
     * @return mixed
     */
    public function create($project_id, array $parameters = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('name')
            ->setRequired('name')
            ->setAllowedTypes('name', 'string');
        $resolver->setDefined('external_url')
            ->setAllowedTypes('external_url', 'string');

        return $this->post($this->getProjectPath($project_id, 'environments'), $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param int        $environment_id
     *
     * @return mixed
     */
    public function remove($project_id, int $environment_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'environments/'.$environment_id));
    }

    /**
     * @param int|string $project_id
     * @param int        $environment_id
     *
     * @return mixed
     */
    public function stop($project_id, int $environment_id)
    {
        return $this->post($this->getProjectPath($project_id, 'environments/'.self::encodePath($environment_id).'/stop'));
    }

    /**
     * @param int|string $project_id
     * @param int        $environment_id
     *
     * @return mixed
     */
    public function show($project_id, int $environment_id)
    {
        return $this->get($this->getProjectPath($project_id, 'environments/'.self::encodePath($environment_id)));
    }
}
