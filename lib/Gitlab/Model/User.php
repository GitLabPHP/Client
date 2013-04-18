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

    public static function fromArray(Client $client, array $data)
    {
        $id = isset($data['id']) ? $data['id'] : 0;

        $user = new User($id);
        $user->setClient($client);

        return $user->hydrate($data);
    }

    public static function create(Client $client, $email, $password, array $params = array())
    {
        $data = $client->api('users')->create($email, $password, $params);

        return User::fromArray($client, $data);
    }

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function show()
    {
        $data = $this->api('users')->show($this->id);

        return User::fromArray($this->getClient(), $data);
    }

    public function keys()
    {
        $data = $this->api('users')->keys();

        $keys = array();
        foreach ($data as $key) {
            $keys[] = Key::fromArray($this->getClient(), $key);
        }

        return $keys;
    }

    public function createKey($title, $key)
    {
        $data = $this->api('users')->createKey($title, $key);

        return Key::fromArray($this->getClient(), $data);
    }

    public function removeKey($id)
    {
        $this->api('users')->removeKey($id);

        return true;
    }

}
