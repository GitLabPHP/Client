<?php namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Note
 *
 * @property-read integer $id
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
    protected static $properties = array(
        'id',
        'author',
        'body',
        'created_at',
        'updated_at',
        'parent_type',
        'parent',
        'attachment',
        'system'
    );

    /**
     * @param Client $client
     * @param Noteable $type
     * @param array $data
     * @return mixed
     */
    public static function fromArray(Client $client, Noteable $type, array $data)
    {
        $comment = new static($type, $client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        return $comment->hydrate($data);
    }

    /**
     * @param Noteable $type
     * @param Client $client
     */
    public function __construct(Noteable $type, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('parent_type', get_class($type));
        $this->setData('parent', $type);
    }
}
