<?php

namespace Gitlab\Model;

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

    public static function fromArray(Project $project, array $data)
    {
        $milestone = new Milestone($project, $data['id']);

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

        return Milestone::fromArray($this->project, $data);
    }

    public function update(array $params)
    {
        $data = $this->api('milestones')->update($this->project->id, $this->id, $params);

        return Milestone::fromArray($this->project, $data);
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
