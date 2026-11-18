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

class ResourceIterationEvents extends AbstractApi
{
    /**
     * List project issue iteration events.
     *
     * @see https://docs.gitlab.com/api/resource_iteration_events/#list-project-issue-iteration-events
     */
    public function all(int|string $project_id, int $issue_iid): mixed
    {
        $path = 'issues/'.self::encodePath($issue_iid).'/resource_iteration_events';

        return $this->get($this->getProjectPath($project_id, $path));
    }

    /**
     * Retrieve a single issue iteration event.
     *
     * @see https://docs.gitlab.com/api/resource_iteration_events/#retrieve-an-issue-iteration-event
     */
    public function show(int|string $project_id, int $issue_iid, int $resource_iteration_event_id): mixed
    {
        $path = 'issues/'.self::encodePath($issue_iid).'/resource_iteration_events/';
        $path .= self::encodePath($resource_iteration_event_id);

        return $this->get($this->getProjectPath($project_id, $path));
    }
}
