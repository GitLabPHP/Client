<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property int    $id
 * @property string $title
 * @property string $key
 * @property string $created_at
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
