<?php

namespace Gitlab\Model;

use Gitlab\Client;

class Hook extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'url',
        'created_at'
    );

    public static function fromArray(Client $client, array $data)
    {
        $hook = new Hook($data['id']);
        $hook->setClient($client);

        return $hook->hydrate($data);
    }

    public static function create(Client $client, $url)
    {
        $data = $client->api('system_hooks')->create($url);

        return Hook::fromArray($client, $data);
    }

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function test()
    {
        return $this->api('system_hooks')->test($this->id);
    }

    public function delete()
    {
        $this->api('system_hooks')->remove($this->id);

        return true;
    }
}