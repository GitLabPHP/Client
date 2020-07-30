<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property int        $id
 * @property string     $ref
 * @property string     $sha
 * @property string     $status
 * @property Project    $project
 * @property array|null $variables
 * @property string     $created_at
 * @property string     $updated_at
 * @property string     $started_at
 * @property string     $finished_at
 * @property string     $committed_at
 * @property int        $duration
 * @property string     $web_url
 * @property User|null  $user
 */
class Pipeline extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'ref',
        'sha',
        'status',
        'project',
        'variables',
        'created_at',
        'updated_at',
        'started_at',
        'finished_at',
        'committed_at',
        'duration',
        'web_url',
        'user',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Pipeline
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $pipeline = new static($project, $data['id'], $client);

        if (isset($data['variables'])) {
            $valueMap = [];
            foreach ($data['variables'] as $variableData) {
                $valueMap[$variableData['key']] = $variableData['value'];
            }
            $data['variables'] = $valueMap;
        }

        if (isset($data['user'])) {
            $data['user'] = User::fromArray($client, $data['user']);
        }

        return $pipeline->hydrate($data);
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
     * @return Pipeline
     */
    public function show()
    {
        $projectsApi = $this->client->projects();

        $data = $projectsApi->pipeline($this->project->id, $this->id);
        $data['variables'] = $projectsApi->pipelineVariables($this->project->id, $this->id);

        return static::fromArray($this->client, $this->project, $data);
    }
}
