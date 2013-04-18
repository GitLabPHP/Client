<?php

namespace Gitlab\Model;

use Gitlab\Client;

class User extends AbstractModel
{
    protected static $_properties = array(
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
        'blocked',
        'projects_limit',
        'access_level',
        'created_at',
        'extern_uid',
        'provider',
        'state'
    );

    public static function fromArray(array $data, Client $client)
    {
        $id = isset($data['id']) ? $data['id'] : 0;

        $user = new User($id);
        $user->setClient($client);

        return $user->hydrate($data);
    }

    public static function create($email, $password, array $params = array(), Client $client)
    {
        $data = $client->api('users')->create($email, $password, $params);

        return User::fromArray($data, $client);
    }

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function show()
    {
        $data = $this->api('users')->show($this->id);

        return User::fromArray($data, $this->getClient());
    }

    public function keys()
    {
        $data = $this->api('users')->keys();

        $keys = array();
        foreach ($data as $key) {
            $keys[] = Key::fromArray($key, $this->getClient());
        }

        return $keys;
    }

    public function createKey($title, $key)
    {
        $data = $this->api('users')->createKey($title, $key);

        return Key::fromArray($data, $this->getClient());
    }

    public function removeKey($id)
    {
        $this->api('users')->removeKey($id);

        return true;
    }

}
