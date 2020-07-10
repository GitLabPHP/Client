<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property-read int $id
 * @property-read string $title
 * @property-read string $key
 * @property-read string $created_at
 */
class Key extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'title',
        'key',
        'created_at',
    ];

    /**
     * @param Client $client
     * @param array  $data
     *
     * @return Key
     */
    public static function fromArray(Client $client, array $data)
    {
        $key = new static($client);

        return $key->hydrate($data);
    }

    /**
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Client $client = null)
    {
        $this->setClient($client);
    }
}
