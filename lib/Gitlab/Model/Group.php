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

    public static function fromArray(array $data, Client $client)
    {
        $group = new Group($data['id']);
        $group->setClient($client);

        if (isset($data['projects'])) {
            $projects = array();
            foreach ($data['projects'] as $project) {
                $projects[] = Project::fromArray($project, $client);
            }
            $data['projects'] = $projects;
        }

        return $group->hydrate($data);
    }

    public static function create($name, $path, Client $client)
    {
        $data = $client->api('groups')->create($name, $path);

        return Group::fromArray($data, $client);
    }

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function show()
    {
        $data = $this->api('groups')->show($this->id);

        return Group::fromArray($data, $this->getClient());
    }
}
