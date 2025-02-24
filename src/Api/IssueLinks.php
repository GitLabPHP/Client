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

class IssueLinks extends AbstractApi
{
    public function all(int|string $project_id, int $issue_iid): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/links');
    }

    /**
     * @param array      $parameters        {
     *
     *     @var string $link_type
     * }
     */
    public function create(int|string $project_id, int $issue_iid, int|string $target_project_id, int $target_issue_iid, array $parameters = []): mixed
    {
        $parameters['target_project_id'] = $target_project_id;
        $parameters['target_issue_iid'] = $target_issue_iid;

        return $this->post($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid).'/links'), $parameters);
    }

    /**
     * @param array      $parameters    {
     *
     *     @var string $link_type
     * }
     */
    public function remove(int|string $project_id, int $issue_iid, int|string $issue_link_id, array $parameters = []): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/'.self::encodePath($issue_iid)).'/links/'.self::encodePath($issue_link_id), $parameters);
    }
}
