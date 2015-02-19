<?php namespace Gitlab\Model;

use Gitlab\Client;

class User extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'email',
        'password',
        'username',
        'name',
        'bio',
        'skype',
        'linkedin',
        'twitter',
        'dark_scheme',
        'theme_id',
        'color_scheme_id',
        'blocked',
        'projects_limit',
        'access_level',
        'created_at',
        'extern_uid',
        'provider',
        'state',
        'is_admin',
        'can_create_group',
        'can_create_project'
    );

    /**
     * @param Client $client
     * @param array  $data
     * @return User
     */
    public static function fromArray(Client $client, array $data)
    {
        $id = isset($data['id']) ? $data['id'] : 0;

        $user = new static($id, $client);

        return $user->hydrate($data);
    }

    /**
     * @param Client $client
     * @param string $email
     * @param string $password
     * @param array  $params
     * @return User
     */
    public static function create(Client $client, $email, $password, array $params = array())
    {
        $data = $client->api('users')->create($email, $password, $params);

        return static::fromArray($client, $data);
    }

    /**
     * @param int $id
     * @param Client $client
     */
    public function __construct($id = null, Client $client = null)
    {
        $this->setClient($client);

        $this->id = $id;
    }

    /**
     * @return User
     */
    public function show()
    {
        $data = $this->api('users')->show($this->id);

        return static::fromArray($this->getClient(), $data);
    }

    /**
     * @param array $params
     * @return User
     */
    public function update(array $params)
    {
        $data = $this->api('users')->update($this->id, $params);

        return static::fromArray($this->getClient(), $data);
    }

    /**
     * @return bool
     */
    public function remove()
    {
        $this->api('users')->remove($this->id);

        return true;
    }

    /**
     * @return Key[]
     */
    public function keys()
    {
        $data = $this->api('users')->keys();

        $keys = array();
        foreach ($data as $key) {
            $keys[] = Key::fromArray($this->getClient(), $key);
        }

        return $keys;
    }

    /**
     * @param string $title
     * @param string $key
     * @return Key
     */
    public function createKey($title, $key)
    {
        $data = $this->api('users')->createKey($title, $key);

        return Key::fromArray($this->getClient(), $data);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function removeKey($id)
    {
        $this->api('users')->removeKey($id);

        return true;
    }

    /**
     * @param int $group_id
     * @param int $access_level
     * @return User
     */
    public function addToGroup($group_id, $access_level)
    {
        $group = new Group($group_id, $this->getClient());

        return $group->addMember($this->id, $access_level);
    }

    /**
     * @param int $group_id
     * @return bool
     */
    public function removeFromGroup($group_id)
    {
        $group = new Group($group_id, $this->getClient());

        return $group->removeMember($this->id);
    }
}
