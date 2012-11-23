<?php

namespace Gitlab\Model;

class Branch extends AbstractModel
{
    protected static $_properties = array(
        'name',
        'commit',
        'project'
    );

    public static function fromArray(Project $project, array $data)
    {
        $branch = new Branch($project, $data['name']);

        if (isset($data['commit'])) {
            $data['commit'] = Commit::fromArray($data['commit']);
        }

        return $branch->hydrate($data);
    }

    public function __construct(Project $project, $name = null)
    {
        $this->project = $project;
        $this->name = $name;
    }

    public function show()
    {
        $data = $this->api('projects')->branch($this->project->id, $this->name);

        return Branch::fromArray($this->project, $data);
    }

}
