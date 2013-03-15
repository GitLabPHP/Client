<?php

namespace Gitlab\Api;

class SystemHooks extends AbstractApi
{
    public function all()
    {
        return $this->get('hooks');
    }

    public function create($url)
    {
        return $this->post('hooks', array(
            'url' => $url
        ));
    }

    public function test($id)
    {
        return $this->get('hooks/'.urlencode($id));
    }

    public function remove($id)
    {
        return $this->delete('hooks/'.urlencode($id));
    }
}
