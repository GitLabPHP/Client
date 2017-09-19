<?php namespace Gitlab\Api;

class IssueBoards extends AbstractApi
{
    /**
     * @param int $project_id
     * @param array $parameters
     *
     * @return mixed
     */
    public function all($project_id = null, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        $path = $project_id === null ? 'boards' : $this->getProjectPath($project_id, 'boards');

        return $this->get($path, $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param int $board_id
     * @return mixed
     */
    public function allLists($project_id, $board_id)
    {
        return $this->get($this->getProjectPath($project_id, 'boards/'.$this->encodePath($board_id).'/lists'));
    }


    /**
     * @param int $project_id
     * @param int $board_id
     * @param int $list_id
     * @return mixed
     */
    public function showList($project_id, $board_id, $list_id)
    {
        return $this->get($this->getProjectPath($project_id, 'boards/'.$this->encodePath($board_id).'/lists'.$this->encodePath($list_id)));
    }

    /**
     * @param int $project_id
     * @param int $board_id
     * @param int $label_id
     * @return mixed
     */
    public function createList($project_id, $board_id, $label_id)
    {
        $params = array(
            'id' => $project_id,
            'board_id' => $board_id,
            'label_id' => $label_id
        );

        return $this->get($this->getProjectPath($project_id, 'boards/'.$this->encodePath($board_id).'/lists'), $params);
    }

    /**
     * @param int $project_id
     * @param int $board_id
     * @param int $list_id
     * @param int $position
     * @return mixed
     */
    public function updateList($project_id, $board_id, $list_id, $position)
    {
        $params = array(
            'id' => $project_id,
            'board_id' => $board_id,
            'list_id' => $list_id,
            'position' => $position
        );

        return $this->put($this->getProjectPath($project_id, 'boards/'.$this->encodePath($board_id).'/lists/'.$this->encodePath($list_id)), $params);
    }

    /**
     * @param int $project_id
     * @param int $board_id
     * @param int $list_id
     * @return mixed
     */
    public function deleteList($project_id, $board_id, $list_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'boards/'.$this->encodePath($board_id).'/lists/'.$this->encodePath($list_id)));
    }
}
