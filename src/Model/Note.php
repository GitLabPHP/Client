<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @deprecated since version 10.1 and will be removed in 11.0.
 *
 * @property int                $id
 * @property User|null          $author
 * @property string             $body
 * @property string             $created_at
 * @property string             $updated_at
 * @property string             $parent_type
 * @property Issue|MergeRequest $parent
 * @property string             $attachment
 * @property bool               $system
 */
final class Note extends AbstractModel
{
    /**
     * @var string[]
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
     * @param Client  $client
     * @param Notable $type
     * @param array   $data
     *
     * @return Note
     */
    public static function fromArray(Client $client, Notable $type, array $data)
    {
        $comment = new self($type, $client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        return $comment->hydrate($data);
    }

    /**
     * @param Notable     $type
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Notable $type, Client $client = null)
    {
        parent::__construct();
        $this->setClient($client);
        $this->setData('parent_type', \get_class($type));
        $this->setData('parent', $type);
    }
}
