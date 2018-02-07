<?php namespace Gitlab\Api;

class Epics extends AbstractApi
{
    /**
     * @param int $group_id
     * @param array $parameters 
     *
     * @return mixed
     */
    public function all($group_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get('groups/'.$this->encodePath($group_id).'/-/epics', $resolver->resolve($parameters));
    }

    /**
     * @param int $group_id
     * @param int $epic_iid
     * @return mixed
     */
    public function show($group_id, $epic_iid)
    {
        return $this->get('groups/'.$this->encodePath($group_id).'/-/epics/'.$this->encodePath($epic_iid));
    }

    /**
     * @param int $group_id
     * @param array $params
     * @return mixed
     */
    public function create($group_id, array $params)
    {
        return $this->post('groups/'.$this->encodePath($group_id).'/-/epics', $params);
    }

    /**
     * @param int $group_id
     * @param int $epic_iid
     * @param array $params
     * @return mixed
     */
    public function update($group_id, $epic_iid, array $params)
    {
        return $this->put('groups/'.$this->encodePath($group_id).'/-/epics/'.$this->encodePath($epic_iid), $params);
    }

    /**
     * @param int $group_id
     * @param int $epic_iid
     * @return mixed
     */
    public function remove($group_id, $epic_iid)
    {
        return $this->delete('groups/'.$this->encodePath($group_id).'/-/epics/'.$this->encodePath($epic_iid));
    }
}
