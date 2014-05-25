<?php

namespace Gitlab\Model;

use Gitlab\Client;

class ProjectNamespace extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'path',
        'kind',
        'owner_id',
        'created_at',
        'updated_at',
        'description'
    );

    public static function fromArray(Client $client, array $data)
    {
        $project = new static($data['id']);
        $project->setClient($client);

        return $project->hydrate($data);
    }

    public function __construct($id = null, Client $client = null)
    {
        $this->setClient($client);
        $this->id = $id;
    }
}
