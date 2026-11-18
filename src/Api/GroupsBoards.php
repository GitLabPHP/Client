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
    /**
     * List all group issue boards in a group.
     *
     * @see https://docs.gitlab.com/api/group_boards/#list-all-group-issue-boards-in-a-group
     */
    public function all(int|string|null $group_id = null, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        $path = null === $group_id ? 'boards' : 'groups/'.self::encodePath($group_id).'/boards';

        return $this->get($path, $resolver->resolve($parameters));
    }

    /**
     * Get a single group issue board.
     *
     * @see https://docs.gitlab.com/api/group_boards/#retrieve-a-group-issue-board
     */
    public function show(int|string $group_id, int $board_id): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id));
    }

    /**
     * Create a group issue board.
     *
     * @see https://docs.gitlab.com/api/group_boards/#create-a-group-issue-board
     */
    public function create(int|string $group_id, array $params): mixed
    {
        return $this->post('groups/'.self::encodePath($group_id).'/boards', $params);
    }

    /**
     * Update a group issue board.
     *
     * @see https://docs.gitlab.com/api/group_boards/#update-a-group-issue-board
     */
    public function update(int|string $group_id, int $board_id, array $params): mixed
    {
        return $this->put('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id), $params);
    }

    /**
     * Delete a group issue board.
     *
     * @see https://docs.gitlab.com/api/group_boards/#delete-a-group-issue-board
     */
    public function remove(int|string $group_id, int $board_id): mixed
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id));
    }

    /**
     * List the boards lists of a group issue board.
     *
     * @see https://docs.gitlab.com/api/group_boards/#list-group-issue-board-lists
     */
    public function allLists(int|string $group_id, int $board_id): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id).'/lists');
    }

    /**
     * Get a single board list of a group issue board.
     *
     * @see https://docs.gitlab.com/api/group_boards/#retrieve-a-group-issue-board-list
     */
    public function showList(int|string $group_id, int $board_id, int $list_id): mixed
    {
        return $this->get('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id).'/lists/'.self::encodePath($list_id));
    }

    /**
     * Create a new board list of a group issue board.
     *
     * @see https://docs.gitlab.com/api/group_boards/#create-a-group-issue-board-list
     */
    public function createList(int|string $group_id, int $board_id, int $label_id): mixed
    {
        $params = [
            'label_id' => $label_id,
        ];

        return $this->post('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id).'/lists', $params);
    }

    /**
     * Update the position of a board list of a group issue board.
     *
     * @see https://docs.gitlab.com/api/group_boards/#update-a-group-issue-board-list
     */
    public function updateList(int|string $group_id, int $board_id, int $list_id, int $position): mixed
    {
        $params = [
            'position' => $position,
        ];

        return $this->put('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id).'/lists/'.self::encodePath($list_id), $params);
    }

    /**
     * Delete a board list of a group issue board.
     *
     * @see https://docs.gitlab.com/api/group_boards/#delete-a-group-issue-board-list
     */
    public function deleteList(int|string $group_id, int $board_id, int $list_id): mixed
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/boards/'.self::encodePath($board_id).'/lists/'.self::encodePath($list_id));
    }
}
