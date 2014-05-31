<?php

namespace Gitlab\Model;

use Gitlab\Client;

class Node extends AbstractModel
{
    protected static $_properties = array(
        'name',
        'type',
        'mode',
        'id',
        'project'
    );

    public static function fromArray(Client $client, Project $project, array $data)
    {
        $node = new static($project, $data['id'], $client);

        return $node->hydrate($data);
    }

    public function __construct(Project $project, $id = null, Client $client)
    {
        $this->setClient($client);

        $this->project = $project;
        $this->id = $id;
    }
}
