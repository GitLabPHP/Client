<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property int        $id
 * @property int        $iid
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
class Milestone extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'iid',
        'project',
        'project_id',
        'title',
        'description',
        'due_date',
        'start_date',
        'state',
        'closed',
        'updated_at',
        'created_at',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Milestone
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $milestone = new static($project, $data['id'], $client);

        return $milestone->hydrate($data);
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
        $this->setData('id', $id);
        $this->setData('project', $project);
    }

    /**
     * @return Milestone
     */
    public function show()
    {
        $data = $this->client->milestones()->show($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param array $params
     *
     * @return Milestone
     */
    public function update(array $params)
    {
        $data = $this->client->milestones()->update($this->project->id, $this->id, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @return Milestone
     */
    public function complete()
    {
        return $this->update(['closed' => true]);
    }

    /**
     * @return Milestone
     */
    public function incomplete()
    {
        return $this->update(['closed' => false]);
    }

    /**
     * @return Issue[]
     */
    public function issues()
    {
        $data = $this->client->milestones()->issues($this->project->id, $this->id);

        $issues = [];
        foreach ($data as $issue) {
            $issues[] = Issue::fromArray($this->getClient(), $this->project, $issue);
        }

        return $issues;
    }
}
