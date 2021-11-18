<?php

declare(strict_types=1);

namespace Gitlab\Api;

class ResourceMilestoneEvents extends AbstractApi
{
    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     *
     * @return mixed
     */
    public function all($project_id, int $issue_iid)
    {
        $path = 'issues/'.self::encodePath($issue_iid).'/resource_milestone_events';

        return $this->get($this->getProjectPath($project_id, $path));
    }

    /**
     * @param int|string $project_id
     * @param int        $issue_iid
     * @param int        $resource_milestone_event_id
     *
     * @return mixed
     */
    public function show($project_id, int $issue_iid, int $resource_milestone_event_id)
    {
        $path = 'issues/'.self::encodePath($issue_iid).'/resource_milestone_events/';
        $path .= self::encodePath($resource_milestone_event_id);

        return $this->get($this->getProjectPath($project_id, $path));
    }
}
