<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Wiki
 *
 * @property-read string $slug
 * @property-read string $title
 * @property-read string $format
 * @property-read string $content
 * @property-read Project $project
 */
class Wiki extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        "project",
        "slug",
        "title",
        "format",
        "content",
    );

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     * @return Wiki
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $wiki = new static($project, $data['slug'], $client);

        return $wiki->hydrate($data);
    }

    /**
     * @param Project $project
     * @param string $slug
     * @param Client  $client
     */
    public function __construct(Project $project, $slug = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('slug', $slug);
    }

    /**
     * @return Wiki
     */
    public function show()
    {
        $data = $this->client->wiki()->show($this->project->id, $this->slug);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param array $params
     * @return Wiki
     */
    public function update(array $params)
    {
        $data = $this->client->wiki()->update($this->project->id, $this->slug, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }
}
