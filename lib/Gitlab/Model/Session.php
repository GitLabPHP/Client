<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property int    $id
 * @property string $email
 * @property string $name
 * @property string $private_token
 * @property string $created_at
 * @property bool   $blocked
 *
 * @deprecated since version 9.18 and will be removed in 10.0.
 */
class Session extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'email',
        'name',
        'private_token',
        'created_at',
        'blocked',
    ];

    /**
     * @param Client $client
     * @param array  $data
     *
     * @return Session
     */
    public static function fromArray(Client $client, array $data)
    {
        $session = new static($client);

        return $session->hydrate($data);
    }

    /**
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Client $client = null)
    {
        @\trigger_error(\sprintf('The %s class is deprecated since version 9.18 and will be removed in 10.0.', self::class), E_USER_DEPRECATED);

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
     *
     * @return Session
     */
    public function login($email, $password)
    {
        $data = $this->client->users()->session($email, $password);

        return $this->hydrate($data);
    }
}
