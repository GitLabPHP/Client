<?php

namespace Gitlab\Model;

use Gitlab\Client;

class Group extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'name',
        'path',
        'owner_id',
        'projects'
    );

    public static function fromArray(Client $client, array $data)
    {
        $group = new Group($data['id']);
        $group->setClient($client);

        if (isset($data['projects'])) {
            $projects = array();
            foreach ($data['projects'] as $project) {
                $projects[] = Project::fromArray($client, $project);
            }
            $data['projects'] = $projects;
        }

        return $group->hydrate($data);
    }

    public static function create(Client $client, $name, $path)
    {
        $data = $client->api('groups')->create($name, $path);

        return Group::fromArray($client, $data);
    }

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function show()
    {
        $data = $this->api('groups')->show($this->id);

        return Group::fromArray($this->getClient(), $data);
    }
}
