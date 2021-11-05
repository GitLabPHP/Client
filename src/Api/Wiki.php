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

class Wiki extends AbstractApi
{
    /**
     * @param int|string          $project_id
     * @param array<string,mixed> $params
     *
     * @return mixed
     */
    public function create($project_id, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'wikis'), $params);
    }

    /**
     * @param int|string $project_id
     * @param string     $wiki_slug
     *
     * @return mixed
     */
    public function show($project_id, string $wiki_slug)
    {
        return $this->get($this->getProjectPath($project_id, 'wikis/'.self::encodePath($wiki_slug)));
    }

    /**
     * @param int|string          $project_id
     * @param array<string,mixed> $params
     *
     * @return mixed
     */
    public function showAll($project_id, array $params)
    {
        return $this->get($this->getProjectPath($project_id, 'wikis'), $params);
    }

    /**
     * @param int|string          $project_id
     * @param string              $wiki_slug
     * @param array<string,mixed> $params
     *
     * @return mixed
     */
    public function update($project_id, string $wiki_slug, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'wikis/'.self::encodePath($wiki_slug)), $params);
    }

    /**
     * @param int|string $project_id
     * @param string     $wiki_slug
     *
     * @return mixed
     */
    public function remove($project_id, string $wiki_slug)
    {
        return $this->delete($this->getProjectPath($project_id, 'wikis/'.self::encodePath($wiki_slug)));
    }
}
