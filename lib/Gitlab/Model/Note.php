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

    public static function fromArray(Client $client, $type, array $data)
    {
        $comment = new static($type, $data['id'], $client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        return $comment->hydrate($data);
    }

    public function __construct($type, $id = null, Client $client = null)
    {
        $this->setClient($client);

        $this->parent_type = get_class($type);
        $this->parent = $type;
        $this->id = $id;
    }

}
