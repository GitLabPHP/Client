<?php

declare(strict_types=1);

namespace Gitlab\Api;

class Snippets extends AbstractApi
{
    /**
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function all($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'snippets'));
    }

    /**
     * @param int|string $project_id
     * @param int        $snippet_id
     *
     * @return mixed
     */
    public function show($project_id, int $snippet_id)
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id)));
    }

    /**
     * @param int|string $project_id
     * @param string     $title
     * @param string     $filename
     * @param string     $code
     * @param string     $visibility
     *
     * @return mixed
     */
    public function create($project_id, string $title, string $filename, string $code, string $visibility)
    {
        return $this->post($this->getProjectPath($project_id, 'snippets'), [
            'title' => $title,
            'file_name' => $filename,
            'code' => $code,
            'visibility' => $visibility,
        ]);
    }

    /**
     * @param int|string $project_id
     * @param int        $snippet_id
     * @param array      $params
     *
     * @return mixed
     */
    public function update($project_id, int $snippet_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id)), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $snippet_id
     *
     * @return string
     */
    public function content($project_id, int $snippet_id)
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/raw'));
    }

    /**
     * @param int|string $project_id
     * @param int        $snippet_id
     *
     * @return mixed
     */
    public function remove($project_id, int $snippet_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $snippet_id
     *
     * @return mixed
     */
    public function showNotes($project_id, int $snippet_id)
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/notes'));
    }

    /**
     * @param int|string $project_id
     * @param int        $snippet_id
     * @param int        $note_id
     *
     * @return mixed
     */
    public function showNote($project_id, int $snippet_id, int $note_id)
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/notes/'.self::encodePath($note_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $snippet_id
     * @param string     $body
     * @param array      $params
     *
     * @return mixed
     */
    public function addNote($project_id, int $snippet_id, string $body, array $params = [])
    {
        $params['body'] = $body;

        return $this->post($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/notes'), $params);
    }

    /**
     * @param int|string $project_id
     * @param int        $snippet_id
     * @param int        $note_id
     * @param string     $body
     *
     * @return mixed
     */
    public function updateNote($project_id, int $snippet_id, int $note_id, string $body)
    {
        return $this->put($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/notes/'.self::encodePath($note_id)), [
            'body' => $body,
        ]);
    }

    /**
     * @param int|string $project_id
     * @param int        $snippet_id
     * @param int        $note_id
     *
     * @return mixed
     */
    public function removeNote($project_id, int $snippet_id, int $note_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/notes/'.self::encodePath($note_id)));
    }

    /**
     * @param int|string $project_id
     * @param int        $snippet_id
     *
     * @return mixed
     */
    public function awardEmoji($project_id, int $snippet_id)
    {
        return $this->get($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/award_emoji'));
    }

    /**
     * @param int|string $project_id
     * @param int        $snippet_id
     * @param int        $award_id
     *
     * @return mixed
     */
    public function removeAwardEmoji($project_id, int $snippet_id, int $award_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'snippets/'.self::encodePath($snippet_id).'/award_emoji/'.self::encodePath($award_id)));
    }
}
