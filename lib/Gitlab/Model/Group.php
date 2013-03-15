<?php

namespace Gitlab\Model;

class Group extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'name',
        'path',
        'owner_id',
        'projects'
    );

    public static function fromArray(array $data)
    {
        $group = new Group($data['id']);

        if (isset($data['projects'])) {
            $projects = array();
            foreach ($data['projects'] as $project) {
                $projects[] = Project::fromArray($project);
            }
            $data['projects'] = $projects;
        }

        return $group->hydrate($data);
    }

    public static function create($name, $path)
    {
        $data = static::client()->api('groups')->create($name, $path);

        return Group::fromArray($data);
    }

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function show()
    {
        $data = $this->api('groups')->show($this->id);

        return Group::fromArray($data);
    }
}
