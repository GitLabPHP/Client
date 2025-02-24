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

use Symfony\Component\OptionsResolver\OptionsResolver;

class Environments extends AbstractApi
{
    public function all(int|string $project_id, array $parameters = []): mixed
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
     * @param array      $parameters {
     *
     *     @var string $name         The name of the environment
     *     @var string $external_url Place to link to for this environment
     *     @var string $tier         The tier of the new environment. Allowed values are production, staging, testing, development, and other.
     * }
     */
    public function create(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('name')
            ->setRequired('name')
            ->setAllowedTypes('name', 'string');
        $resolver->setDefined('external_url')
            ->setAllowedTypes('external_url', 'string');
        $resolver->setDefined('tier')
            ->setAllowedValues('tier', ['production', 'staging', 'testing', 'development', 'other']);

        return $this->post($this->getProjectPath($project_id, 'environments'), $resolver->resolve($parameters));
    }

    public function remove(int|string $project_id, int $environment_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'environments/'.$environment_id));
    }

    public function stop(int|string $project_id, int $environment_id): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'environments/'.self::encodePath($environment_id).'/stop'));
    }

    public function show(int|string $project_id, int $environment_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'environments/'.self::encodePath($environment_id)));
    }
}
