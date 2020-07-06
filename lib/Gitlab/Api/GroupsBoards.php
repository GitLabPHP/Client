<?php

namespace Gitlab\Api;

class GroupsBoards extends AbstractApi
{
    /**
     * @param int|null $group_id
     * @param array    $parameters
     *
     * @return mixed
     */
    public function all($group_id = null, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        $path = null === $group_id ? 'boards' : $this->getGroupPath($group_id, 'boards');

        return $this->get($path, $resolver->resolve($parameters));
    }

    /**
     * @param int $group_id
     * @param int $board_id
     *
     * @return mixed
     */
    public function show($group_id, $board_id)
    {
        return $this->get($this->getGroupPath($group_id, 'boards/'.$this->encodePath($board_id)));
    }

    /**
     * @param int   $group_id
     * @param array $params
     *
     * @return mixed
     */
    public function create($group_id, array $params)
    {
        return $this->post($this->getGroupPath($group_id, 'boards'), $params);
    }

    /**
     * @param int   $group_id
     * @param int   $board_id
     * @param array $params
     *
     * @return mixed
     */
    public function update($group_id, $board_id, array $params)
    {
        return $this->put($this->getGroupPath($group_id, 'boards/'.$this->encodePath($board_id)), $params);
    }

    /**
     * @param int $group_id
     * @param int $board_id
     *
     * @return mixed
     */
    public function remove($group_id, $board_id)
    {
        return $this->delete($this->getGroupPath($group_id, 'boards/'.$this->encodePath($board_id)));
    }

    /**
     * @param int $group_id
     * @param int $board_id
     *
     * @return mixed
     */
    public function allLists($group_id, $board_id)
    {
        return $this->get($this->getGroupPath($group_id, 'boards/'.$this->encodePath($board_id).'/lists'));
    }

    /**
     * @param int $group_id
     * @param int $board_id
     * @param int $list_id
     *
     * @return mixed
     */
    public function showList($group_id, $board_id, $list_id)
    {
        return $this->get($this->getGroupPath($group_id, 'boards/'.$this->encodePath($board_id).'/lists/'.$this->encodePath($list_id)));
    }

    /**
     * @param int $group_id
     * @param int $board_id
     * @param int $label_id
     *
     * @return mixed
     */
    public function createList($group_id, $board_id, $label_id)
    {
        $params = [
            'label_id' => $label_id,
        ];

        return $this->post($this->getGroupPath($group_id, 'boards/'.$this->encodePath($board_id).'/lists'), $params);
    }

    /**
     * @param int $group_id
     * @param int $board_id
     * @param int $list_id
     * @param int $position
     *
     * @return mixed
     */
    public function updateList($group_id, $board_id, $list_id, $position)
    {
        $params = [
            'position' => $position,
        ];

        return $this->put($this->getGroupPath($group_id, 'boards/'.$this->encodePath($board_id).'/lists/'.$this->encodePath($list_id)), $params);
    }

    /**
     * @param int $group_id
     * @param int $board_id
     * @param int $list_id
     *
     * @return mixed
     */
    public function deleteList($group_id, $board_id, $list_id)
    {
        return $this->delete($this->getGroupPath($group_id, 'boards/'.$this->encodePath($board_id).'/lists/'.$this->encodePath($list_id)));
    }
}
