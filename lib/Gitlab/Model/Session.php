<?php

namespace Gitlab\Model;

class Session extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'email',
        'name',
        'private_token',
        'created_at',
        'blocked'
    );

    public static function fromArray(array $data)
    {
        $session = new Session();

        return $session->hydrate($data);
    }

    public function me()
    {
        $data = $this->api('users')->show();

        return User::fromArray($data);
    }

    public function login($email, $password)
    {
        $data = $this->api('users')->session($email, $password);

        return $this->hydrate($data);
    }
}
