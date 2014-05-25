<?php

namespace Gitlab\Model;

use Gitlab\Client;

class Milestone extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'iid',
        'project',
        'project_id',
        'title',
        'description',
        'due_date',
        'state',
        'closed',
        'updated_at',
        'created_at'
    );

    public static function fromArray(Client $client, Project $project, array $data)
    {
        $milestone = new static($project, $data['id'], $client);

        return $milestone->hydrate($data);
    }

    public function __construct(Project $project, $id, Client $client = null)
    {
        $this->setClient($client);

        $this->id = $id;
        $this->project = $project;
    }

    public function show()
    {
        $data = $this->api('milestones')->show($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    public function update(array $params)
    {
        $data = $this->api('milestones')->update($this->project->id, $this->id, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    public function complete()
    {
        return $this->update(array('closed' => true));
    }

    public function incomplete()
    {
        return $this->update(array('closed' => false));
    }

}
