<?php

namespace Gitlab\Api;

class SystemHooks extends AbstractApi
{
    /**
     * @return mixed
     */
    public function all()
    {
        return $this->get('hooks');
    }

    /**
     * @param string $url
     *
     * @return mixed
     */
    public function create($url)
    {
        return $this->post('hooks', [
            'url' => $url,
        ]);
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function test($id)
    {
        return $this->get('hooks/'.$this->encodePath($id));
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function remove($id)
    {
        return $this->delete('hooks/'.$this->encodePath($id));
    }
}
