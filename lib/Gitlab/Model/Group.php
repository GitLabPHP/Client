<?php namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Group
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $path
 * @property-read string $description
 * @property-read Project[] $projects
 */
class Group extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'name',
        'path',
        'description',
        'projects'
    );

    /**
     * @param Client $client
     * @param array  $data
     * @return Group
     */
    public static function fromArray(Client $client, array $data)
    {
        $group = new static($data['id'], $client);

        if (isset($data['projects'])) {
            $projects = array();
            foreach ($data['projects'] as $project) {
                $projects[] = Project::fromArray($client, $project);
            }
            $data['projects'] = $projects;
        }

        return $group->hydrate($data);
    }

    /**
     * @param Client $client
     * @param string $name
     * @param string $path
     * @return Group
     */
    public static function create(Client $client, $name, $path)
    {
        $data = $client->groups()->create($name, $path);

        return static::fromArray($client, $data);
    }

    /**
     * @param int $id
     * @param Client $client
     */
    public function __construct($id, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('id', $id);
    }

    /**
     * @return Group
     */
    public function show()
    {
        $data = $this->client->groups()->show($this->id);

        return Group::fromArray($this->getClient(), $data);
    }

    /**
     * @param int $project_id
     * @return Group
     */
    public function transfer($project_id)
    {
        $data = $this->client->groups()->transfer($this->id, $project_id);

        return Group::fromArray($this->getClient(), $data);
    }

    /**
     * @return User[]
     */
    public function members()
    {
        $data = $this->client->groups()->members($this->id);

        $members = array();
        foreach ($data as $member) {
            $members[] = User::fromArray($this->getClient(), $member);
        }

        return $members;
    }

    /**
     * @param int $user_id
     * @param int $access_level
     * @return User
     */
    public function addMember($user_id, $access_level)
    {
        $data = $this->client->groups()->addMember($this->id, $user_id, $access_level);

        return User::fromArray($this->getClient(), $data);
    }

    /**
     * @param int $user_id
     * @return bool
     */
    public function removeMember($user_id)
    {
        $this->client->groups()->removeMember($this->id, $user_id);

        return true;
    }

    /**
     * @return Group
     */
    public function projects()
    {
        $data = $this->client->groups()->projects($this->id);

        return Group::fromArray($this->getClient(), $data);
    }

    /**
     * @return Group[]
     */
    public function subgroups()
    {
        $data = $this->client->groups()->subgroups($this->id);

        $groups = array();
        foreach ($data as $group) {
            $groups[] = Group::fromArray($this->getClient(), $group);
        }

        return $groups;
    }
}
