<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Api;

class GroupsBoards extends AbstractApi
{
    public function all(int|string|null $group_id = null, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        $path = null === $group_id ? 'boards' : 'groups/'.self::encodePath($group_id).'/boards';

        return $this->get($path, $resolver->resolve($parameters));
    }

    public function show(int|string $group_id, int $board_id): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id));
    }

    public function create(int|string $group_id, array $params): mixed
    {
        return $this->post('groups/'.self::encodePath($group_id).'/boards', $params);
    }

    public function update(int|string $group_id, int $board_id, array $params): mixed
    {
        return $this->put('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id), $params);
    }

    public function remove(int|string $group_id, int $board_id): mixed
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id));
    }

    public function allLists(int|string $group_id, int $board_id): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id).'/lists');
    }

    public function showList(int|string $group_id, int $board_id, int $list_id): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id).'/lists/'.self::encodePath($list_id));
    }

    public function createList(int|string $group_id, int $board_id, int $label_id): mixed
    {
        $params = [
            'label_id' => $label_id,
        ];

        return $this->post('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id).'/lists', $params);
    }

    public function updateList(int|string $group_id, int $board_id, int $list_id, int $position): mixed
    {
        $params = [
            'position' => $position,
        ];

        return $this->put('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id).'/lists/'.self::encodePath($list_id), $params);
    }

    public function deleteList(int|string $group_id, int $board_id, int $list_id): mixed
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id).'/lists/'.self::encodePath($list_id));
    }
}
