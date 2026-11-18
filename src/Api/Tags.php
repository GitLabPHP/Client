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

class Tags extends AbstractApi
{
    /**
     * List project repository tags.
     *
     * @see https://docs.gitlab.com/api/tags/#list-all-project-repository-tags
     *
     * @param array      $parameters {
     *
     *     @var string $order_by Return tags ordered by `name`, `updated` or `version` fields. Default is `updated`.
     *     @var string $sort     Return tags sorted in asc or desc order. Default is desc.
     *     @var string $search   Return list of tags matching the search criteria. You can use `^term` and `term$` to
     *                           find tags that begin and end with term respectively.
     * }
     */
    public function all(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['name', 'updated', 'version']);
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc']);
        $resolver->setDefined('search');

        return $this->get($this->getProjectPath($project_id, 'repository/tags'), $resolver->resolve($parameters));
    }

    /**
     * Retrieve a single repository tag.
     *
     * @see https://docs.gitlab.com/api/tags/#retrieve-a-single-repository-tag
     */
    public function show(int|string $project_id, string $tag_name): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'repository/tags/'.self::encodePath($tag_name)));
    }

    /**
     * Create a new tag.
     *
     * @see https://docs.gitlab.com/api/tags/#create-a-new-tag
     */
    public function create(int|string $project_id, array $params = []): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'repository/tags'), $params);
    }

    /**
     * Delete a tag.
     *
     * @see https://docs.gitlab.com/api/tags/#delete-a-tag
     */
    public function remove(int|string $project_id, string $tag_name): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'repository/tags/'.self::encodePath($tag_name)));
    }

    /**
     * Create a release for a tag.
     *
     * Note: the `POST .../repository/tags/:tag_name/release` endpoint this method calls
     * is no longer documented on the current Tags API page; it has been superseded by
     * the Releases API. Linked here as the closest current equivalent.
     *
     * @see https://docs.gitlab.com/api/releases/#create-a-release
     */
    public function createRelease(int|string $project_id, string $tag_name, array $params = []): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'repository/tags/'.self::encodePath($tag_name).'/release'), $params);
    }

    /**
     * Update a release for a tag.
     *
     * Note: the `PUT .../repository/tags/:tag_name/release` endpoint this method calls
     * is no longer documented on the current Tags API page; it has been superseded by
     * the Releases API. Linked here as the closest current equivalent.
     *
     * @see https://docs.gitlab.com/api/releases/#update-a-release
     */
    public function updateRelease(int|string $project_id, string $tag_name, array $params = []): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'repository/tags/'.self::encodePath($tag_name).'/release'), $params);
    }
}
