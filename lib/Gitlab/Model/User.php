<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property int      $id
 * @property string   $email
 * @property string   $password
 * @property string   $username
 * @property string   $name
 * @property string   $bio
 * @property string   $skype
 * @property string   $linkedin
 * @property string   $twitter
 * @property bool     $dark_scheme
 * @property int      $theme_id
 * @property int      $color_scheme_id
 * @property bool     $blocked
 * @property int|null $project_limit
 * @property int      $access_level
 * @property string   $created_at
 * @property string   $extern_uid
 * @property string   $provider
 * @property string   $state
 * @property bool     $is_admin
 * @property bool     $can_create_group
 * @property bool     $can_create_project
 * @property string   $avatar_url
 * @property string   $current_sign_in_at
 * @property bool     $two_factor_enabled
 */
class User extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
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
        'can_create_project',
        'avatar_url',
        'current_sign_in_at',
        'two_factor_enabled',
    ];

    /**
     * @param Client $client
     * @param array  $data
     *
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
     *
     * @return User
     */
    public static function create(Client $client, $email, $password, array $params = [])
    {
        $data = $client->users()->create($email, $password, $params);

        return static::fromArray($client, $data);
    }

    /**
     * @param int|null    $id
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct($id = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('id', $id);
    }

    /**
     * @return User
     */
    public function show()
    {
        $data = $this->client->users()->show($this->id);

        return static::fromArray($this->getClient(), $data);
    }

    /**
     * @param array $params
     *
     * @return User
     */
    public function update(array $params)
    {
        $data = $this->client->users()->update($this->id, $params);

        return static::fromArray($this->getClient(), $data);
    }

    /**
     * @return bool
     */
    public function remove()
    {
        $this->client->users()->remove($this->id);

        return true;
    }

    /**
     * @return bool
     */
    public function block()
    {
        $this->client->users()->block($this->id);

        return true;
    }

    /**
     * @return bool
     */
    public function unblock()
    {
        $this->client->users()->unblock($this->id);

        return true;
    }

    /**
     * @return Key[]
     */
    public function keys()
    {
        $data = $this->client->users()->keys();

        $keys = [];
        foreach ($data as $key) {
            $keys[] = Key::fromArray($this->getClient(), $key);
        }

        return $keys;
    }

    /**
     * @param string $title
     * @param string $key
     *
     * @return Key
     */
    public function createKey($title, $key)
    {
        $data = $this->client->users()->createKey($title, $key);

        return Key::fromArray($this->getClient(), $data);
    }

    /**
     * @param int    $user_id
     * @param string $title
     * @param string $key
     *
     * @return Key
     */
    public function createKeyForUser($user_id, $title, $key)
    {
        $data = $this->client->users()->createKeyForUser($user_id, $title, $key);

        return Key::fromArray($this->getClient(), $data);
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function removeKey($id)
    {
        $this->client->users()->removeKey($id);

        return true;
    }

    /**
     * @param int $group_id
     * @param int $access_level
     *
     * @return User
     */
    public function addToGroup($group_id, $access_level)
    {
        $group = new Group($group_id, $this->getClient());

        return $group->addMember($this->id, $access_level);
    }

    /**
     * @param int $group_id
     *
     * @return bool
     */
    public function removeFromGroup($group_id)
    {
        $group = new Group($group_id, $this->getClient());

        return $group->removeMember($this->id);
    }
}
