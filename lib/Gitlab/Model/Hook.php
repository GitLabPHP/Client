<?php

namespace Gitlab\Model;

class Hook extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'url',
        'created_at'
    );

    public static function fromArray(array $data)
    {
        $hook = new Hook($data['id']);

        return $hook->hydrate($data);
    }

    public static function create($url)
    {
        $data = static::client()->api('system_hooks')->create($url);

        return Hook::fromArray($data);
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