<?php

namespace Gitlab\Model;

use Gitlab\Client;

class Snippet extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'title',
        'file_name',
        'author',
        'expires_at',
        'updated_at',
        'created_at',
        'project'
    );

    public static function fromArray(Client $client, Project $project, array $data)
    {
        $snippet = new static($project, $data['id'], $client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        return $snippet->hydrate($data);
    }

    public function __construct($project, $id = null, Client $client = null)
    {
        $this->setClient($client);

        $this->project = $project;
        $this->id = $id;
    }

    public function show()
    {
        $data = $this->api('snippets')->show($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this, $data);
    }

    public function update(array $params)
    {
        $data = $this->api('snippets')->update($this->project->id, $this->id, $params);

        return static::fromArray($this->getClient(), $this, $data);
    }

    public function content()
    {
        return $this->api('snippets')->content($this->project->id, $this->id);
    }

    public function remove()
    {
        $this->api('snippets')->remove($this->project->id, $this->id);

        return true;
    }
}
