<?php namespace Gitlab\Model;

use Gitlab\Client;

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
        'attachment'
    );

    /**
     * @param Client $client
     * @param string $type
     * @param array $data
     * @return mixed
     */
    public static function fromArray(Client $client, $type, array $data)
    {
        $comment = new static($type, $data['id'], $client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        return $comment->hydrate($data);
    }

    /**
     * @param string $type
     * @param int $id
     * @param Client $client
     */
    public function __construct($type, $id = null, Client $client = null)
    {
        $this->setClient($client);

        $this->parent_type = get_class($type);
        $this->parent = $type;
        $this->id = $id;
    }
}
