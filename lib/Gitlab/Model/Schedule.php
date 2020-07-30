<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property int        $id
 * @property int|string $project_id
 * @property string     $title
 * @property string     $description
 * @property string     $due_date
 * @property string     $start_date
 * @property string     $state
 * @property bool       $closed
 * @property string     $updated_at
 * @property string     $created_at
 * @property Project    $project
 */
class Schedule extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'project',
        'project_id',
        'description',
        'ref',
        'cron',
        'cron_timezone',
        'next_run_at',
        'active',
        'created_at',
        'updated_at',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Schedule
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $schedule = new static($project, $data['id'], $client);

        return $schedule->hydrate($data);
    }

    /**
     * @param Project     $project
     * @param int|null    $id
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Project $project, $id = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }

    /**
     * @return Schedule
     */
    public function show()
    {
        $data = $this->client->schedules()->show($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param array $params
     *
     * @return Schedule
     */
    public function update(array $params)
    {
        $data = $this->client->schedules()->update($this->project->id, $this->id, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }
}
