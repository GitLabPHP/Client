<?php namespace Gitlab\Api;

class Snippets extends AbstractApi
{
    /**
     * @param int $project_id
     * @return mixed
     */
    public function all($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'snippets'));
    }

    /**
     * @param int $project_id
     * @param int $snippet_id
     * @return mixed
     */
    public function show($project_id, $snippet_id)
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.$this->encodePath($snippet_id)));
    }

    /**
     * @param int $project_id
     * @param string $title
     * @param string $filename
     * @param string $code
     * @return mixed
     */
    public function create($project_id, $title, $filename, $code)
    {
        return $this->post($this->getProjectPath($project_id, 'snippets'), array(
            'title' => $title,
            'file_name' => $filename,
            'code' => $code
        ));
    }

    /**
     * @param int $project_id
     * @param int $snippet_id
     * @param array $params
     * @return mixed
     */
    public function update($project_id, $snippet_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'snippets/'.$this->encodePath($snippet_id)), $params);
    }

    /**
     * @param int $project_id
     * @param int $snippet_id
     * @return string
     */
    public function content($project_id, $snippet_id)
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.$this->encodePath($snippet_id).'/raw'));
    }

    /**
     * @param int $project_id
     * @param int $snippet_id
     * @return mixed
     */
    public function remove($project_id, $snippet_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'snippets/'.$this->encodePath($snippet_id)));
    }

    /**
     * @param int $project_id
     * @param int $snippet_id
     *
     * @return mixed
     */
    public function awardEmoji($project_id, $snippet_id)
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.$this->encodePath($snippet_id).'/award_emoji'));
    }
}
