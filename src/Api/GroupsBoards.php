<?php

declare(strict_types=1);

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

        $path = null === $group_id ? 'boards' : 'groups/'.self::encodePath($group_id).'/boards';

        return $this->get($path, $resolver->resolve($parameters));
    }

    /**
     * @param int|string $group_id
     * @param int $board_id
     *
     * @return mixed
     */
    public function show($group_id, int $board_id)
    {
        return $this->get('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id));
    }

    /**
     * @param int|string $group_id
     * @param array $params
     *
     * @return mixed
     */
    public function create($group_id, array $params)
    {
        return $this->post('groups/'.self::encodePath($group_id).'/boards', $params);
    }

    /**
     * @param int|string $group_id
     * @param int   $board_id
     * @param array $params
     *
     * @return mixed
     */
    public function update($group_id, int $board_id, array $params)
    {
        return $this->put('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id), $params);
    }

    /**
     * @param int|string $group_id
     * @param int $board_id
     *
     * @return mixed
     */
    public function remove($group_id, int $board_id)
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id));
    }

    /**
     * @param int|string $group_id
     * @param int $board_id
     *
     * @return mixed
     */
    public function allLists($group_id, int $board_id)
    {
        return $this->get('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id).'/lists');
    }

    /**
     * @param int|string $group_id
     * @param int $board_id
     * @param int $list_id
     *
     * @return mixed
     */
    public function showList($group_id, int $board_id, int $list_id)
    {
        return $this->get('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id).'/lists/'.self::encodePath($list_id));
    }

    /**
     * @param int|string $group_id
     * @param int $board_id
     * @param int $label_id
     *
     * @return mixed
     */
    public function createList($group_id, int $board_id, int $label_id)
    {
        $params = [
            'label_id' => $label_id,
        ];

        return $this->post('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id).'/lists', $params);
    }

    /**
     * @param int|string $group_id
     * @param int $board_id
     * @param int $list_id
     * @param int $position
     *
     * @return mixed
     */
    public function updateList($group_id, int $board_id, int $list_id, int $position)
    {
        $params = [
            'position' => $position,
        ];

        return $this->put('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id).'/lists/'.self::encodePath($list_id), $params);
    }

    /**
     * @param int|string $group_id
     * @param int $board_id
     * @param int $list_id
     *
     * @return mixed
     */
    public function deleteList($group_id, int $board_id, int $list_id)
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id).'/lists/'.self::encodePath($list_id));
    }
}
