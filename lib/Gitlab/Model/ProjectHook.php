<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property-read int $id
 * @property-read string $url
 * @property-read int|string $project_id
 * @property-read bool $push_events
 * @property-read bool $issues_events
 * @property-read bool $merge_requests_events
 * @property-read bool $job_events
 * @property-read bool $tag_push_events
 * @property-read bool $pipeline_events
 * @property-read string $created_at
 * @property-read Project $project
 */
class ProjectHook extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'project',
        'url',
        'project_id',
        'push_events',
        'issues_events',
        'merge_requests_events',
        'job_events',
        'tag_push_events',
        'pipeline_events',
        'created_at',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return ProjectHook
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $hook = new static($project, $data['id'], $client);

        return $hook->hydrate($data);
    }

    /**
     * @param Project     $project
     * @param int         $id
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Project $project, $id, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }

    /**
     * @return ProjectHook
     */
    public function show()
    {
        $data = $this->client->projects()->hook($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $this->client->projects()->removeHook($this->project->id, $this->id);

        return true;
    }

    /**
     * @return bool
     */
    public function remove()
    {
        return $this->delete();
    }

    /**
     * @param array $params
     *
     * @return ProjectHook
     */
    public function update(array $params)
    {
        $data = $this->client->projects()->updateHook($this->project->id, $this->id, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }
}
