<?php

namespace Gitlab\Model;

use Gitlab\Client;

class Team extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'name',
        'path',
        'owner_id'
    );

    public static function fromArray(Client $client, array $data)
    {
        $team = new Team($data['id'], $client);

        return $team->hydrate($data);
    }

    public static function create(Client $client, $name, $path)
    {
        $data = $client->api('teams')->create($name, $path);

        return Team::fromArray($client, $data);
    }

    public function __construct($id = null, Client $client = null)
    {
        $this->setClient($client);

        $this->id = $id;
    }

    public function show()
    {
        $data = $this->api('teams')->show($this->id);

        return Team::fromArray($this->getClient(), $data);
    }

    public function members()
    {
        $data = $this->api('teams')->members($this->id);

        $members = array();
        foreach ($data as $member) {
            $members[] = User::fromArray($this->getClient(), $member);
        }

        return $members;
    }

    public function member($user_id)
    {
        $data = $this->api('teams')->member($this->id, $user_id);

        return User::fromArray($this->getClient(), $data);
    }

    public function addMember($user_id, $access_level)
    {
        $data = $this->api('teams')->addMember($this->id, $user_id, $access_level);

        return User::fromArray($this->getClient(), $data);
    }

    public function removeMember($user_id)
    {
        $this->api('teams')->removeMember($this->id, $user_id);

        return true;
    }

    public function projects()
    {
        $data = $this->api('teams')->projects($this->id);

        $projects = array();
        foreach ($data as $project) {
            $projects[] = Project::fromArray($this->getClient(), $project);
        }

        return $projects;
    }

    public function project($project_id)
    {
        $data = $this->api('teams')->project($this->id, $project_id);

        return Project::fromArray($this->getClient(), $data);
    }

    public function addProject($project_id, $greatest_access_level)
    {
        $data = $this->api('teams')->addProject($this->id, $project_id, $greatest_access_level);

        return Project::fromArray($this->getClient(), $data);
    }

    public function removeProject($project_id)
    {
        $this->api('teams')->removeProject($this->id, $project_id);

        return true;
    }
}