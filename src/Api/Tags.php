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

class Tags extends AbstractApi
{
    /**
     * @param int|string $project_id
     * @param array      $parameters {
     *
     *     @var string $order_by Return tags ordered by `name` or `updated` fields. Default is `updated`.
     *     @var string $sort     Return tags sorted in asc or desc order. Default is desc.
     *     @var string $search   Return list of tags matching the search criteria. You can use `^term` and `term$` to
     *                           find tags that begin and end with term respectively.
     * }
     *
     * @return mixed
     */
    public function all($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['name', 'updated']);
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc']);
        $resolver->setDefined('search');

        return $this->get($this->getProjectPath($project_id, 'repository/tags'), $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param string     $tag_name
     *
     * @return mixed
     */
    public function show($project_id, string $tag_name)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/tags/'.self::encodePath($tag_name)));
    }

    /**
     * @param int|string $project_id
     * @param array      $params
     *
     * @return mixed
     */
    public function create($project_id, array $params = [])
    {
        return $this->post($this->getProjectPath($project_id, 'repository/tags'), $params);
    }

    /**
     * @param int|string $project_id
     * @param string     $tag_name
     *
     * @return mixed
     */
    public function remove($project_id, string $tag_name)
    {
        return $this->delete($this->getProjectPath($project_id, 'repository/tags/'.self::encodePath($tag_name)));
    }

    /**
     * @param int|string $project_id
     * @param string     $tag_name
     * @param array      $params
     *
     * @return mixed
     */
    public function createRelease($project_id, string $tag_name, array $params = [])
    {
        return $this->post($this->getProjectPath($project_id, 'repository/tags/'.self::encodePath($tag_name).'/release'), $params);
    }

    /**
     * @param int|string $project_id
     * @param string     $tag_name
     * @param array      $params
     *
     * @return mixed
     */
    public function updateRelease($project_id, string $tag_name, array $params = [])
    {
        return $this->put($this->getProjectPath($project_id, 'repository/tags/'.self::encodePath($tag_name).'/release'), $params);
    }
}
