<?php

namespace Gitlab\Model;

use Gitlab\Client;

class Key extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'title',
        'key',
        'created_at'
    );

    public static function fromArray(Client $client, array $data)
    {
        $key = new static($client);

        return $key->hydrate($data);
    }

    public function __construct(Client $client = null)
    {
        $this->setClient($client);
    }
}
