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
 * @property-read string $full_name
 * @property-read string $full_path
 * @property-read string $visibility
 * @property-read bool $lfs_enabled
 * @property-read string $avatar_url
 * @property-read string $web_url
 * @property-read bool $request_access_enabled
 * @property-read int $parent_id
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
        'projects',
        'full_name',
        'full_path',
        'visibility',
        'lfs_enabled',
        'avatar_url',
        'web_url',
        'request_access_enabled',
        'parent_id'
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
    public function members($page = null, $perPage = null)
    {
        $parameters = [];
        if($page !== null) {
            $parameters['page'] = $page;
        }
        if($perPage !== null) {
            $parameters['per_page'] = $perPage;
        }
        $data = $this->client->groups()->members($this->id, $parameters);

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
     * @return \Gitlab\Model\Project[]
     */
    public function projects($page = null, $perPage = null, &$pagination = null, $search = null)
    {
        $parameters = [];
        if(null !== $page) {
            $parameters['page'] = $page;
        }
        if(null !== $perPage) {
            $parameters['per_page'] = $perPage;
        }
        if(null !== $search) {
            $parameters['search'] = $search;
        }
        $parameters['order_by'] = "last_activity_at";

        $data = $this->client->groups()->projects($this->id, $parameters);
        if($pagination !== null) {
            $pagination = $this->client->groups()->getPagination();
        }
        $projects = [];
        foreach ($data as $datum) {
            $projects[] = Project::fromArray($this->getClient(), $datum);
        }
        return $projects;
    }
}
