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
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function all($project_id, int $issue_iid)
    {
        $path = 'issues/'.self::encodePath($issue_iid).'/resource_state_events';

        return $this->get($this->getProjectPath($project_id, $path));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int        $resource_label_event_id
     *
     * @return mixed
     */
    public function show($project_id, int $issue_iid, int $resource_label_event_id)
    {
        $path = 'issues/'.self::encodePath($issue_iid).'/resource_state_events/';
        $path .= self::encodePath($resource_label_event_id);

        return $this->get($this->getProjectPath($project_id, $path));
    }
}
