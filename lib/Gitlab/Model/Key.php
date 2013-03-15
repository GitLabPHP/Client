<?php

namespace Gitlab\Model;

class Key extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'title',
        'key',
        'created_at'
    );

    public static function fromArray(array $data)
    {
        $key = new Key();

        return $key->hydrate($data);
    }
}
