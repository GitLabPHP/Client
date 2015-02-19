<?php namespace Gitlab\Model;

use Gitlab\Client;

class Snippet extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'title',
        'file_name',
        'author',
        'expires_at',
        'updated_at',
        'created_at',
        'project'
    );

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     * @return Snippet
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $snippet = new static($project, $data['id'], $client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        return $snippet->hydrate($data);
    }

    /**
     * @param Project $project
     * @param int $id
     * @param Client $client
     */
    public function __construct(Project $project, $id = null, Client $client = null)
    {
        $this->setClient($client);

        $this->project = $project;
        $this->id = $id;
    }

    /**
     * @return Snippet
     */
    public function show()
    {
        $data = $this->api('snippets')->show($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param array $params
     * @return Snippet
     */
    public function update(array $params)
    {
        $data = $this->api('snippets')->update($this->project->id, $this->id, $params);

        return static::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @return string
     */
    public function content()
    {
        return $this->api('snippets')->content($this->project->id, $this->id);
    }

    /**
     * @return bool
     */
    public function remove()
    {
        $this->api('snippets')->remove($this->project->id, $this->id);

        return true;
    }
}
