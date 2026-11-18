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

class ResourceStateEvents extends AbstractApi
{
    /**
     * List project issue state events.
     *
     * @see https://docs.gitlab.com/api/resource_state_events/#list-project-issue-state-events
     */
    public function all(int|string $project_id, int $issue_iid): mixed
    {
        $path = 'issues/'.self::encodePath($issue_iid).'/resource_state_events';

        return $this->get($this->getProjectPath($project_id, $path));
    }

    /**
     * Retrieve a single issue state event.
     *
     * @see https://docs.gitlab.com/api/resource_state_events/#retrieve-a-single-issue-state-event
     */
    public function show(int|string $project_id, int $issue_iid, int $resource_label_event_id): mixed
    {
        $path = 'issues/'.self::encodePath($issue_iid).'/resource_state_events/';
        $path .= self::encodePath($resource_label_event_id);

        return $this->get($this->getProjectPath($project_id, $path));
    }
}
