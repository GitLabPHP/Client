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

class Wiki extends AbstractApi
{
    /**
     * @param array<string,mixed> $params
     */
    public function create(int|string $project_id, array $params): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'wikis'), $params);
    }

    public function show(int|string $project_id, string $wiki_slug): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'wikis/'.self::encodePath($wiki_slug)));
    }

    /**
     * @param array<string,mixed> $params     {
     *
     *     @var bool $with_content Include pages' content
     * }
     */
    public function showAll(int|string $project_id, array $params): mixed
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('with_content')
            ->setAllowedTypes('with_content', 'bool');

        return $this->get($this->getProjectPath($project_id, 'wikis'), $resolver->resolve($params));
    }

    /**
     * @param array<string,mixed> $params
     */
    public function update(int|string $project_id, string $wiki_slug, array $params): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'wikis/'.self::encodePath($wiki_slug)), $params);
    }

    public function remove(int|string $project_id, string $wiki_slug): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'wikis/'.self::encodePath($wiki_slug)));
    }
}
