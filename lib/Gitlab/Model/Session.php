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

    public static function fromArray(Client $client, array $data)
    {
        $session = new static($client);

        return $session->hydrate($data);
    }

    public function __construct(Client $client = null)
    {
        $this->setClient($client);
    }

    public function me()
    {
        $data = $this->api('users')->show();

        return User::fromArray($this->getClient(), $data);
    }

    public function login($email, $password)
    {
        $data = $this->api('users')->session($email, $password);

        return $this->hydrate($data);
    }
}
