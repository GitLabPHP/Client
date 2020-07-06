<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property-read int $id
 * @property-read string $url
 * @property-read string $created_at
 */
class Hook extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'url',
        'created_at',
    ];

    /**
     * @param Client $client
     * @param array  $data
     *
     * @return Hook
     */
    public static function fromArray(Client $client, array $data)
    {
        $hook = new static($data['id'], $client);

        return $hook->hydrate($data);
    }

    /**
     * @param Client $client
     * @param string $url
     *
     * @return Hook
     */
    public static function create(Client $client, $url)
    {
        $data = $client->systemHooks()->create($url);

        return static::fromArray($client, $data);
    }

    /**
     * @param int         $id
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct($id, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('id', $id);
    }

    /**
     * @return bool
     */
    public function test()
    {
        $this->client->systemHooks()->test($this->id);

        return true;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $this->client->systemHooks()->remove($this->id);

        return true;
    }
}
