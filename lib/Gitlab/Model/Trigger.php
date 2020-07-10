<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property-read int $id
 * @property-read string $description
 * @property-read string $created_at
 * @property-read string $last_used
 * @property-read string $token
 * @property-read string $updated_at
 * @property-read User|null $owner
 * @property-read Project $project
 */
class Trigger extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'description',
        'created_at',
        'last_used',
        'token',
        'updated_at',
        'owner',
        'project',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Trigger
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $trigger = new self($project, $data['id'], $client);

        if (isset($data['owner'])) {
            $data['owner'] = User::fromArray($client, $data['owner']);
        }

        return $trigger->hydrate($data);
    }

    /**
     * @param Project     $project
     * @param int|null    $id
     * @param Client|null $client
     */
    public function __construct(Project $project, $id = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }

    /**
     * @return Trigger
     */
    public function show()
    {
        $data = $this->client->projects()->trigger($this->project->id, $this->id);

        return static::fromArray($this->client, $this->project, $data);
    }
}
