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

    public static function fromArray(array $data, Client $client)
    {
        $key = new Key();
        $key->setClient($client);

        return $key->hydrate($data);
    }
}
