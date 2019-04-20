<?php namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Key
 *
 * @property-read int $id
 * @property-read string $title
 * @property-read string $key
 * @property-read string $can_push
 * @property-read string $created_at
 */
class Key extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'title',
        'key',
        'can_push',
        'created_at'
    );

    /**
     * @param Client $client
     * @param array $data
     * @return Key
     */
    public static function fromArray(Client $client, array $data)
    {
        $key = new static($client);

        return $key->hydrate($data);
    }

    /**
     * @param Client $client
     */
    public function __construct(Client $client = null)
    {
        $this->setClient($client);
    }
}
