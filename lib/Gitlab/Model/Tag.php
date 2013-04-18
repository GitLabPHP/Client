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

    public static function fromArray(Project $project, array $data, Client $client)
    {
        $branch = new Tag($project, $data['name']);
        $branch->setClient($client);

        if (isset($data['commit'])) {
            $data['commit'] = Commit::fromArray($project, $data['commit'], $client);
        }

        return $branch->hydrate($data);
    }

    public function __construct(Project $project, $name = null)
    {
        $this->project = $project;
        $this->name = $name;
    }

}
