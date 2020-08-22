<?php

declare(strict_types=1);

namespace Gitlab\Api;

class Schedules extends AbstractApi
{
    /**
     * @param int|string $project_id
     * @param array      $params
     *
     * @return mixed
     */
    public function create($project_id, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'pipeline_schedules'), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $schedule_id
     *
     * @return mixed
     */
    public function show($project_id, int $schedule_id)
    {
        return $this->get($this->getProjectPath($project_id, 'pipeline_schedules/'.self::encodePath($schedule_id)));
    }

    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function showAll($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'pipeline_schedules'));
    }

    /**
     * @param int|string $project_id
     * @param int        $schedule_id
     * @param array      $params
     *
     * @return mixed
     */
    public function update($project_id, int $schedule_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'pipeline_schedules/'.self::encodePath($schedule_id)), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $schedule_id
     *
     * @return mixed
     */
    public function remove($project_id, int $schedule_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'pipeline_schedules/'.self::encodePath($schedule_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $schedule_id
     * @param array      $params
     *
     * @return mixed
     */
    public function addVariable($project_id, int $schedule_id, array $params)
    {
        $path = 'pipeline_schedules/'.self::encodePath($schedule_id).'/variables';

        return $this->post($this->getProjectPath($project_id, $path), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $schedule_id
     * @param string     $variable_key
     * @param array      $params
     *
     * @return mixed
     */
    public function updateVariable($project_id, int $schedule_id, string $variable_key, array $params)
    {
        $path = 'pipeline_schedules/'.self::encodePath($schedule_id).'/variables/'.self::encodePath($variable_key);

        return $this->put($this->getProjectPath($project_id, $path), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $schedule_id
     * @param string     $variable_key
     *
     * @return mixed
     */
    public function removeVariable($project_id, int $schedule_id, string $variable_key)
    {
        $path = 'pipeline_schedules/'.self::encodePath($schedule_id).'/variables/'.self::encodePath($variable_key);

        return $this->delete($this->getProjectPath($project_id, $path), $params);
    }
}
