<?php

namespace Gitlab\Model;

use Gitlab\Client;

class Note extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'author',
        'body',
        'created_at',
        'updated_at',
        'parent_type',
        'parent',
        'attachment'
    );

    public static function fromArray($type, array $data, Client $client)
    {
        $comment = new Note($type);
        $comment->setClient($client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($data['author'], $client);
        }

        return $comment->hydrate($data);
    }

    public function __construct($type, $id = null)
    {
        $this->parent_type = get_class($type);
        $this->parent = $type;

        $this->id = $id;
    }

}
