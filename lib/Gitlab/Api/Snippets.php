<?php

namespace Gitlab\Api;

class Snippets extends AbstractApi
{
    public function all($project_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/snippets');
    }

    public function show($project_id, $snippet_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/snippets/'.urlencode($snippet_id));
    }

    public function create($project_id, $title, $filename, $code, $lifetime = null)
    {
        return $this->post('projects/'.urlencode($project_id).'/snippets', array(
            'title' => $title,
            'file_name' => $filename,
            'code' => $code,
            'lifetime' => $lifetime
        ));
    }

    public function update($project_id, $snippet_id, array $params)
    {
        return $this->put('projects/'.urlencode($project_id).'/snippets/'.urlencode($snippet_id), $params);
    }

    public function content($project_id, $snippet_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/snippets/'.urlencode($snippet_id).'/raw');
    }

    public function remove($project_id, $snippet_id)
    {
        return $this->delete('projects/'.urlencode($project_id).'/snippets/'.urlencode($snippet_id));
    }

}