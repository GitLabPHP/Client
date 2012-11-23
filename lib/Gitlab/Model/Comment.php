<?php

namespace Gitlab\Model;

class Comment extends AbstractModel
{
    protected static $_properties = array(
        'author',
        'note',
        'mr'
    );

    public static function fromArray(MergeRequest $mr, array $data)
    {
        $comment = new Comment($mr);

        if (isset($data['author'])) {
            $data['author'] = new User($data['author']);
        }

        return $comment->hydrate($data);
    }

    public function __construct(MergeRequest $mr)
    {
        $this->mr = $mr;
    }

}
