<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Session
 *
 * @property-read int $id
 * @property-read string $email
 * @property-read string $name
 * @property-read string $private_token
 * @property-read string $created_at
 * @property-read bool $blocked
 */
class Session extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'email',
        'name',
        'private_token',
        'created_at',
        'blocked'
    );

    /**
     * @param Client $client
     * @param array  $data
     * @return Session
     */
    public static function fromArray(Client $client, array $data)
    {
        $session = new static($client);

        return $session->hydrate($data);
    }

    /**
     * @param Client $client
     */
    public function __construct(Client $client = null)
    {
        $this->setClient($client);
    }

    /**
     * @return User
     */
    public function me()
    {
        $data = $this->client->users()->user();

        return User::fromArray($this->getClient(), $data);
    }

    /**
     * @param string $email
     * @param string $password
     * @return Session
     */
    public function login($email, $password)
    {
        $data = $this->client->users()->session($email, $password);

        return $this->hydrate($data);
    }
}
