<?php

namespace Gitlab\Api;

class Groups extends AbstractApi
{
    public function all($page = 1, $per_page = static::PER_PAGE)
    {
        return $this->get('groups', array(
            'page' => $page,
            'per_page' => $per_page
        ));
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
