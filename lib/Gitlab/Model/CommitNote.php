<?php

namespace Gitlab\Model;

use Gitlab\Client;

class CommitNote extends AbstractModel
{
    protected static $_properties = array(
        'note',
        'path',
        'line',
        'line_type',
        'author'
    );

    public static function fromArray(Client $client, array $data)
    {
        $comment = new static($client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        return $comment->hydrate($data);
    }

    public function __construct(Client $client = null)
    {
        $this->setClient($client);
    }

}
