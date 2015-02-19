<?php namespace Gitlab\Model;

use Gitlab\Client;

class Hook extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'url',
        'created_at'
    );

    /**
     * @param Client $client
     * @param array  $data
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
     * @return Hook
     */
    public static function create(Client $client, $url)
    {
        $data = $client->api('system_hooks')->create($url);

        return static::fromArray($client, $data);
    }

    /**
     * @param int $id
     * @param Client $client
     */
    public function __construct($id, Client $client = null)
    {
        $this->setClient($client);

        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function test()
    {
        $this->api('system_hooks')->test($this->id);

        return true;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $this->api('system_hooks')->remove($this->id);

        return true;
    }
}
