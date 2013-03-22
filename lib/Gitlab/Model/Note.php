<?php

namespace Gitlab\Model;

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

    public static function fromArray($type, array $data)
    {
        $comment = new Note($type);

        if (isset($data['author'])) {
            $data['author'] = new User($data['author']);
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
