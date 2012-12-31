<?php

namespace Gitlab\Model;

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
        'created_at'
    );

    public static function fromArray(array $data)
    {
        $id = isset($data['id']) ? $data['id'] : 0;
        $user = new User($id);

        return $user->hydrate($data);
    }

    public static function create($email, $password, array $params = array())
    {
        $data = static::client()->api('users')->create($email, $password, $params);

        return User::fromArray($data);
    }

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function show()
    {
        $data = $this->api('users')->show($this->id);

        return User::fromArray($data);
    }

}
