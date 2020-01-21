<?php namespace Gitlab\Api;

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
     * @param array  $options
     *
     * @return mixed
     */
    public function create($url, $options = [])
    {
        $parameters = array_merge($options, ['url' => $url]);

        return $this->post('hooks', $parameters);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function test($id)
    {
        return $this->get('hooks/'.$this->encodePath($id));
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function remove($id)
    {
        return $this->delete('hooks/'.$this->encodePath($id));
    }
}
