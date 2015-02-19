<?php namespace Gitlab\Model;

use Gitlab\Client;

class Key extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'title',
        'key',
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
