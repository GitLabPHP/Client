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

class Releases extends AbstractApi
{
    /**
     * @param int|string $project_id
     * @param array $parameters {
     *
     * @return mixed
     *
     * @var string $sort Return releases sorted in asc or desc order. Default is desc.
     * }
     * @var string $order_by Return tags ordered by `name`, `updated` or `version` fields. Default is `updated`.
     */
    public function all($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['name', 'updated', 'version']);
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc']);
        $resolver->setDefined('include_html_description')
            ->setAllowedTypes('include_html_description', 'boolean');

        return $this->get($this->getProjectPath($project_id, 'releases'), $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param string $tag_name
     *
     * @return mixed
     */
    public function show($project_id, string $tag_name)
    {
        return $this->get($this->getProjectPath($project_id, 'releases/'.self::encodePath($tag_name)));
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function showLatest($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'releases/permalink/latest'));
    }

    /**
     * @param int|string $project_id
     * @param array $parametersgit
     *
     * @return mixed
     */
    public function create($project_id, array $parameters = [])
    {
        return $this->post($this->getProjectPath($project_id, 'releases'), $parameters);
    }

    /**
     * @param int|string $project_id
     * @param string $tag_name
     * @param array $parameters
     *
     * @return mixed
     */
    public function update($project_id, string $tag_name, array $parameters)
    {
        return $this->put($this->getProjectPath($project_id, 'releases/'.self::encodePath($tag_name)), $parameters);
    }

    /**
     * @param int|string $project_id
     * @param string $tag_name
     *
     * @return mixed
     */
    public function remove($project_id, string $tag_name)
    {
        return $this->delete($this->getProjectPath($project_id, 'releases/'.self::encodePath($tag_name)));
    }
}
