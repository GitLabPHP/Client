<?php

namespace Gitlab\Model;

use Gitlab\Client;

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

    public static function fromArray(array $data, Client $client)
    {
        $session = new Session();
        $session->setClient($client);

        return $session->hydrate($data);
    }

    public function me()
    {
        $data = $this->api('users')->show();

        return User::fromArray($data, $this->getClient());
    }

    public function login($email, $password)
    {
        $data = $this->api('users')->session($email, $password);

        return $this->hydrate($data);
    }
}
