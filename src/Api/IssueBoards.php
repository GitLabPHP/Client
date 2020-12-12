<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Api;

class IssueBoards extends AbstractApi
{
    /**
     * @param int|string|null $project_id
     * @param array           $parameters
     *
     * @return mixed
     */
    public function all($project_id = null, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        $path = null === $project_id ? 'boards' : $this->getProjectPath($project_id, 'boards');

        return $this->get($path, $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param int        $board_id
     *
     * @return mixed
     */
    public function show($project_id, int $board_id)
    {
        return $this->get($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id)));
    }

    /**
     * @param int|string $project_id
     * @param array      $params
     *
     * @return mixed
     */
    public function create($project_id, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'boards'), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $board_id
     * @param array      $params
     *
     * @return mixed
     */
    public function update($project_id, int $board_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id)), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $board_id
     *
     * @return mixed
     */
    public function remove($project_id, int $board_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $board_id
     *
     * @return mixed
     */
    public function allLists($project_id, int $board_id)
    {
        return $this->get($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id).'/lists'));
    }

    /**
     * @param int|string $project_id
     * @param int        $board_id
     * @param int        $list_id
     *
     * @return mixed
     */
    public function showList($project_id, int $board_id, int $list_id)
    {
        return $this->get($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id).'/lists/'.self::encodePath($list_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $board_id
     * @param int        $label_id
     *
     * @return mixed
     */
    public function createList($project_id, int $board_id, int $label_id)
    {
        $params = [
            'label_id' => $label_id,
        ];

        return $this->post($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id).'/lists'), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $board_id
     * @param int        $list_id
     * @param int        $position
     *
     * @return mixed
     */
    public function updateList($project_id, int $board_id, int $list_id, int $position)
    {
        $params = [
            'position' => $position,
        ];

        return $this->put($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id).'/lists/'.self::encodePath($list_id)), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $board_id
     * @param int        $list_id
     *
     * @return mixed
     */
    public function deleteList($project_id, int $board_id, int $list_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'boards/'.self::encodePath($board_id).'/lists/'.self::encodePath($list_id)));
    }
}
