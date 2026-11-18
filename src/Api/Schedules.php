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

class Schedules extends AbstractApi
{
    /**
     * Create a new pipeline schedule.
     *
     * @see https://docs.gitlab.com/api/pipeline_schedules/#create-a-new-pipeline-schedule
     */
    public function create(int|string $project_id, array $params): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'pipeline_schedules'), $params);
    }

    /**
     * Retrieve a pipeline schedule.
     *
     * @see https://docs.gitlab.com/api/pipeline_schedules/#retrieve-a-pipeline-schedule
     */
    public function show(int|string $project_id, int $schedule_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'pipeline_schedules/'.self::encodePath($schedule_id)));
    }

    /**
     * List all pipeline schedules.
     *
     * @see https://docs.gitlab.com/api/pipeline_schedules/#list-all-pipeline-schedules
     */
    public function showAll(int|string $project_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'pipeline_schedules'));
    }

    /**
     * Update a pipeline schedule.
     *
     * @see https://docs.gitlab.com/api/pipeline_schedules/#update-a-pipeline-schedule
     */
    public function update(int|string $project_id, int $schedule_id, array $params): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'pipeline_schedules/'.self::encodePath($schedule_id)), $params);
    }

    /**
     * Delete a pipeline schedule.
     *
     * @see https://docs.gitlab.com/api/pipeline_schedules/#delete-a-pipeline-schedule
     */
    public function remove(int|string $project_id, int $schedule_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'pipeline_schedules/'.self::encodePath($schedule_id)));
    }

    /**
     * Create a variable for a pipeline schedule.
     *
     * @see https://docs.gitlab.com/api/pipeline_schedules/#create-a-variable-for-a-pipeline-schedule
     */
    public function addVariable(int|string $project_id, int $schedule_id, array $params): mixed
    {
        $path = 'pipeline_schedules/'.self::encodePath($schedule_id).'/variables';

        return $this->post($this->getProjectPath($project_id, $path), $params);
    }

    /**
     * Update a variable for a pipeline schedule.
     *
     * @see https://docs.gitlab.com/api/pipeline_schedules/#update-a-variable-for-a-pipeline-schedule
     */
    public function updateVariable(int|string $project_id, int $schedule_id, string $variable_key, array $params): mixed
    {
        $path = 'pipeline_schedules/'.self::encodePath($schedule_id).'/variables/'.self::encodePath($variable_key);

        return $this->put($this->getProjectPath($project_id, $path), $params);
    }

    /**
     * Delete a variable for a pipeline schedule.
     *
     * @see https://docs.gitlab.com/api/pipeline_schedules/#delete-a-variable-for-a-pipeline-schedule
     */
    public function removeVariable(int|string $project_id, int $schedule_id, string $variable_key): mixed
    {
        $path = 'pipeline_schedules/'.self::encodePath($schedule_id).'/variables/'.self::encodePath($variable_key);

        return $this->delete($this->getProjectPath($project_id, $path));
    }

    /**
     * Update ownership of a pipeline schedule.
     *
     * @see https://docs.gitlab.com/api/pipeline_schedules/#update-ownership-of-a-pipeline-schedule
     */
    public function takeOwnership(int|string $project_id, int $schedule_id): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'pipeline_schedules/'.self::encodePath($schedule_id)).'/take_ownership');
    }

    /**
     * Run a pipeline schedule immediately.
     *
     * @see https://docs.gitlab.com/api/pipeline_schedules/#run-a-pipeline-schedule-immediately
     */
    public function play(int|string $project_id, int $schedule_id): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'pipeline_schedules/'.self::encodePath($schedule_id)).'/play');
    }
}
