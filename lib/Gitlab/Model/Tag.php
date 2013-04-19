<?php

namespace Gitlab\Model;

use Gitlab\Client;

class Tag extends AbstractModel
{
    protected static $_properties = array(
        'name',
        'commit',
        'project',
        'protected'
    );

    public static function fromArray(Client $client, Project $project, array $data)
    {
        $branch = new Tag($project, $data['name']);
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

}
