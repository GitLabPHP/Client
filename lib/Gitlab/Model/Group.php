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
        $group = new Group($data['id'], $client);

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

    public function __construct($id, Client $client = null)
    {
        $this->setClient($client);

        $this->id = $id;
    }

    public function show()
    {
        $data = $this->api('groups')->show($this->id);

        return Group::fromArray($this->getClient(), $data);
    }

    public function transfer($project_id)
    {
        $data = $this->api('groups')->transfer($this->id, $project_id);

        return Group::fromArray($this->getClient(), $data);
    }

	public function members()
	{
		$data = $this->api('groups')->members($this->id);

		$members = array();
		foreach ($data as $member) {
			$members[] = User::fromArray($this->getClient(), $member);
		}

		return $members;
	}

	public function addMember($user_id, $access_level)
	{
		$data = $this->api('groups')->addMember($this->id, $user_id, $access_level);

		return User::fromArray($this->getClient(), $data);
	}

	public function removeMember($user_id)
	{
		$this->api('groups')->removeMember($this->id, $user_id);

		return true;
	}
}
