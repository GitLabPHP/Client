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

class ResourceWeightEvents extends AbstractApi
{
    /**
     * List all project issue weight events.
     *
     * @see https://docs.gitlab.com/api/resource_weight_events/#list-all-project-issue-weight-events
     */
    public function all(int|string $project_id, int $issue_iid): mixed
    {
        $path = 'issues/'.self::encodePath($issue_iid).'/resource_weight_events';

        return $this->get($this->getProjectPath($project_id, $path));
    }

    /**
     * Retrieve single issue weight event.
     *
     * @see https://docs.gitlab.com/api/resource_weight_events/#retrieve-single-issue-weight-event
     */
    public function show(int|string $project_id, int $issue_iid, int $resource_label_event_id): mixed
    {
        $path = 'issues/'.self::encodePath($issue_iid).'/resource_weight_events/';
        $path .= self::encodePath($resource_label_event_id);

        return $this->get($this->getProjectPath($project_id, $path));
    }
}
