<?php

namespace Gitlab\Model;

use Gitlab\Client;

class Branch extends AbstractModel
{
    protected static $_properties = array(
        'name',
        'commit',
        'project',
        'protected'
    );

    public static function fromArray(Client $client, Project $project, array $data)
    {
        $branch = new Branch($project, $data['name']);
        $branch->setClient($client);

        if (isset($data['commit'])) {
            $data['commit'] = Commit::fromArray($client, $project, $data['commit']);
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
        $data = $this->api('repositories')->branch($this->project->id, $this->name);

        return Branch::fromArray($this->getClient(), $this->project, $data);
    }

    public function protect()
    {
        $data = $this->api('repositories')->protectBranch($this->project->id, $this->name);

        return Branch::fromArray($this->getClient(), $this->project, $data);
    }

    public function unprotect()
    {
        $data = $this->api('repositories')->unprotectBranch($this->project->id, $this->name);

        return Branch::fromArray($this->getClient(), $this->project, $data);
    }
}
