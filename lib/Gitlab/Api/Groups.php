<?php

namespace Gitlab\Api;

class Groups extends AbstractApi
{
    public function all()
    {
        return $this->get('groups');
    }

    public function show($id)
    {
        return $this->get('groups/'.urlencode($id));
    }

    public function create($name, $path)
    {
        return $this->post('groups', array(
            'name' => $name,
            'path' => $path
        ));
    }
}
