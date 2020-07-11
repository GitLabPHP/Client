<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $path
 * @property-read string $description
 * @property-read string $visibility
 * @property-read bool $lfs_enabled
 * @property-read string $avatar_url
 * @property-read string $web_url
 * @property-read bool $request_access_enabled
 * @property-read string $full_name
 * @property-read string $full_path
 * @property-read int|string $file_template_project_id
 * @property-read int|null $parent_id
 * @property-read Project[]|null $projects
 * @property-read Project[]|null $shared_projects
 */
class Group extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'name',
        'path',
        'description',
        'visibility',
        'lfs_enabled',
        'avatar_url',
        'web_url',
        'request_access_enabled',
        'full_name',
        'full_path',
        'file_template_project_id',
        'parent_id',
        'projects',
        'shared_projects',
    ];

    /**
     * @param Client $client
     * @param array  $data
     *
     * @return Group
     */
    public static function fromArray(Client $client, array $data)
    {
        $group = new static($data['id'], $client);

        if (isset($data['projects'])) {
            $projects = [];
            foreach ($data['projects'] as $project) {
                $projects[] = Project::fromArray($client, $project);
            }
            $data['projects'] = $projects;
        }

        if (isset($data['shared_projects'])) {
            $projects = [];
            foreach ($data['shared_projects'] as $project) {
                $projects[] = Project::fromArray($client, $project);
            }
            $data['shared_projects'] = $projects;
        }

        return $group->hydrate($data);
    }

    /**
     * @param Client $client
     * @param string $name
     * @param string $path
     *
     * @return Group
     */
    public static function create(Client $client, $name, $path)
    {
        $data = $client->groups()->create($name, $path);

        return static::fromArray($client, $data);
    }

    /**
     * @param int         $id
     * @param Client|null $client
     *
     * @return void
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

        return self::fromArray($this->getClient(), $data);
    }

    /**
     * @param int|string $project_id
     *
     * @return Group
     */
    public function transfer($project_id)
    {
        $data = $this->client->groups()->transfer($this->id, $project_id);

        return self::fromArray($this->getClient(), $data);
    }

    /**
     * @param string|null $query
     *
     * @return User[]
     */
    public function members($query = null)
    {
        $data = $this->client->groups()->members($this->id, null === $query ? [] : ['query' => $query]);

        $members = [];
        foreach ($data as $member) {
            $members[] = User::fromArray($this->getClient(), $member);
        }

        return $members;
    }

    /**
     * @param int $user_id
     * @param int $access_level
     *
     * @return User
     */
    public function addMember($user_id, $access_level)
    {
        $data = $this->client->groups()->addMember($this->id, $user_id, $access_level);

        return User::fromArray($this->getClient(), $data);
    }

    /**
     * @param int $user_id
     *
     * @return bool
     */
    public function removeMember($user_id)
    {
        $this->client->groups()->removeMember($this->id, $user_id);

        return true;
    }

    /**
     * @return Project[]
     */
    public function projects()
    {
        $data = $this->client->groups()->projects($this->id);

        $projects = [];
        foreach ($data as $project) {
            $projects[] = Project::fromArray($this->getClient(), $project);
        }

        return $projects;
    }

    /**
     * @return Group[]
     */
    public function subgroups()
    {
        $data = $this->client->groups()->subgroups($this->id);

        $groups = [];
        foreach ($data as $group) {
            $groups[] = self::fromArray($this->getClient(), $group);
        }

        return $groups;
    }
}
