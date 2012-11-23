<?php

namespace Gitlab\Model;

class User extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'email',
        'name',
        'bio',
        'skype',
        'linkedin',
        'twitter',
        'dark_scheme',
        'theme_id',
        'blocked',
        'created_at'
    );

    public static function fromArray(array $data)
    {
        $user = new User($data['id']);

        return $user->hydrate($data);
    }

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function show()
    {
        return $this->api('users')->show($this->id);
    }
}
