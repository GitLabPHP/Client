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
    public function create(int|string $project_id, array $params): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'pipeline_schedules'), $params);
    }

    public function show(int|string $project_id, int $schedule_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'pipeline_schedules/'.self::encodePath($schedule_id)));
    }

    public function showAll(int|string $project_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'pipeline_schedules'));
    }

    public function update(int|string $project_id, int $schedule_id, array $params): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'pipeline_schedules/'.self::encodePath($schedule_id)), $params);
    }

    public function remove(int|string $project_id, int $schedule_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'pipeline_schedules/'.self::encodePath($schedule_id)));
    }

    public function addVariable(int|string $project_id, int $schedule_id, array $params): mixed
    {
        $path = 'pipeline_schedules/'.self::encodePath($schedule_id).'/variables';

        return $this->post($this->getProjectPath($project_id, $path), $params);
    }

    public function updateVariable(int|string $project_id, int $schedule_id, string $variable_key, array $params): mixed
    {
        $path = 'pipeline_schedules/'.self::encodePath($schedule_id).'/variables/'.self::encodePath($variable_key);

        return $this->put($this->getProjectPath($project_id, $path), $params);
    }

    public function removeVariable(int|string $project_id, int $schedule_id, string $variable_key): mixed
    {
        $path = 'pipeline_schedules/'.self::encodePath($schedule_id).'/variables/'.self::encodePath($variable_key);

        return $this->delete($this->getProjectPath($project_id, $path));
    }

    public function takeOwnership(int|string $project_id, int $schedule_id): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'pipeline_schedules/'.self::encodePath($schedule_id)).'/take_ownership');
    }

    public function play(int|string $project_id, int $schedule_id): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'pipeline_schedules/'.self::encodePath($schedule_id)).'/play');
    }
}
