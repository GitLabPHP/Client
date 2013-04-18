<?php

namespace Gitlab\Model;

use Gitlab\Client;

class Milestone extends AbstractModel
{
    protected static $_properties = array(
        'id',
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

    public static function fromArray(Project $project, array $data, Client $client)
    {
        $milestone = new Milestone($project, $data['id']);
        $milestone->setClient($client);

        return $milestone->hydrate($data);
    }

    public function __construct(Project $project, $id)
    {
        $this->id = $id;
        $this->project = $project;
    }

    public function show()
    {
        $data = $this->api('milestones')->show($this->project->id, $this->id);

        return Milestone::fromArray($this->project, $data, $this->getClient());
    }

    public function update(array $params)
    {
        $data = $this->api('milestones')->update($this->project->id, $this->id, $params);

        return Milestone::fromArray($this->project, $data, $this->getClient());
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
