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

class IssueBoards extends AbstractApi
{
    /**
     * List all project issue boards.
     *
     * @see https://docs.gitlab.com/api/boards/#list-all-project-issue-boards
     */
    public function all(int|string|null $project_id = null, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        $path = null === $project_id ? 'boards' : $this->getProjectPath($project_id, 'boards');

        return $this->get($path, $resolver->resolve($parameters));
    }

    /**
     * Get a single project issue board.
     *
     * @see https://docs.gitlab.com/api/boards/#retrieve-an-issue-board
     */
    public function show(int|string $project_id, int $board_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id)));
    }

    /**
     * Create a project issue board.
     *
     * @see https://docs.gitlab.com/api/boards/#create-an-issue-board
     */
    public function create(int|string $project_id, array $params): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'boards'), $params);
    }

    /**
     * Update a project issue board.
     *
     * @see https://docs.gitlab.com/api/boards/#update-an-issue-board
     */
    public function update(int|string $project_id, int $board_id, array $params): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id)), $params);
    }

    /**
     * Delete a project issue board.
     *
     * @see https://docs.gitlab.com/api/boards/#delete-an-issue-board
     */
    public function remove(int|string $project_id, int $board_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id)));
    }

    /**
     * List the boards lists of a project issue board.
     *
     * @see https://docs.gitlab.com/api/boards/#list-all-board-lists-in-an-issue-board
     */
    public function allLists(int|string $project_id, int $board_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id).'/lists'));
    }

    /**
     * Get a single board list of a project issue board.
     *
     * @see https://docs.gitlab.com/api/boards/#retrieve-a-board-list
     */
    public function showList(int|string $project_id, int $board_id, int $list_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id).'/lists/'.self::encodePath($list_id)));
    }

    /**
     * Create a new board list of a project issue board.
     *
     * @see https://docs.gitlab.com/api/boards/#create-a-board-list
     */
    public function createList(int|string $project_id, int $board_id, int $label_id): mixed
    {
        $params = [
            'label_id' => $label_id,
        ];

        return $this->post($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id).'/lists'), $params);
    }

    /**
     * Update the position of a board list of a project issue board.
     *
     * @see https://docs.gitlab.com/api/boards/#update-a-board-list
     */
    public function updateList(int|string $project_id, int $board_id, int $list_id, int $position): mixed
    {
        $params = [
            'position' => $position,
        ];

        return $this->put($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id).'/lists/'.self::encodePath($list_id)), $params);
    }

    /**
     * Delete a board list of a project issue board.
     *
     * @see https://docs.gitlab.com/api/boards/#delete-a-board-list-from-a-board
     */
    public function deleteList(int|string $project_id, int $board_id, int $list_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id).'/lists/'.self::encodePath($list_id)));
    }
}
