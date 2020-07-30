<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property int       $id
 * @property string    $description
 * @property string    $created_at
 * @property string    $last_used
 * @property string    $token
 * @property string    $updated_at
 * @property User|null $owner
 * @property Project   $project
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
