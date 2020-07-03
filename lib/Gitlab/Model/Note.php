<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Note.
 *
 * @property-read int $id
 * @property-read User $author
 * @property-read string $body
 * @property-read string $created_at
 * @property-read string $updated_at
 * @property-read string $parent_type
 * @property-read Issue|MergeRequest $parent
 * @property-read string $attachment
 * @property-read bool $system
 */
class Note extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = [
        'id',
        'author',
        'body',
        'created_at',
        'updated_at',
        'parent_type',
        'parent',
        'attachment',
        'system',
    ];

    /**
     * @param Client           $client
     * @param Noteable|Notable $type
     * @param array            $data
     *
     * @return mixed
     */
    public static function fromArray(Client $client, $type, array $data)
    {
        $comment = new self($type, $client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        return $comment->hydrate($data);
    }

    /**
     * @param Noteable|Notable $type
     * @param Client           $client
     *
     * @return void
     */
    public function __construct($type, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('parent_type', get_class($type));
        $this->setData('parent', $type);
    }
}
