<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property int       $id
 * @property string    $title
 * @property string    $file_name
 * @property string    $updated_at
 * @property string    $created_at
 * @property Project   $project
 * @property User|null $author
 */
final class Snippet extends AbstractModel implements Notable
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'title',
        'file_name',
        'author',
        'updated_at',
        'created_at',
        'project',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Snippet
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $snippet = new self($project, $data['id'], $client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        return $snippet->hydrate($data);
    }

    /**
     * @param Project     $project
     * @param int         $id
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Project $project, int $id = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }

    /**
     * @return Snippet
     */
    public function show()
    {
        $data = $this->client->snippets()->show($this->project->id, $this->id);

        return self::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param array $params
     *
     * @return Snippet
     */
    public function update(array $params)
    {
        $data = $this->client->snippets()->update($this->project->id, $this->id, $params);

        return self::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @return string
     */
    public function content()
    {
        return $this->client->snippets()->content($this->project->id, $this->id);
    }

    /**
     * @return bool
     */
    public function remove()
    {
        $this->client->snippets()->remove($this->project->id, $this->id);

        return true;
    }

    /**
     * @param string $body
     *
     * @return Note
     */
    public function addNote(string $body)
    {
        $data = $this->client->snippets()->addNote($this->project->id, $this->id, $body);

        return Note::fromArray($this->getClient(), $this, $data);
    }
}
