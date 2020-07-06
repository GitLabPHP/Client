<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property-read Commit|null $commit
 * @property-read int $id
 * @property-read string $coverage
 * @property-read string $created_at
 * @property-read string $artifacts_file
 * @property-read string $finished_at
 * @property-read string $name
 * @property-read Pipeline|null $pipeline
 * @property-read string $ref
 * @property-read string $runner
 * @property-read string $stage
 * @property-read string $started_at
 * @property-read string $status
 * @property-read string|bool $tag
 * @property-read User|null $user
 */
class Job extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'commit',
        'coverage',
        'created_at',
        'artifacts_file',
        'finished_at',
        'name',
        'pipeline',
        'ref',
        'runner',
        'stage',
        'started_at',
        'status',
        'tag',
        'user',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Job
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $job = new static($project, $data['id'], $client);

        if (isset($data['user'])) {
            $data['user'] = User::fromArray($client, $data['user']);
        }

        if (isset($data['commit'])) {
            $data['commit'] = Commit::fromArray($client, $project, $data['commit']);
        }

        if (isset($data['pipeline'])) {
            $data['pipeline'] = Pipeline::fromArray($client, $project, $data['pipeline']);
        }

        return $job->hydrate($data);
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
}
