<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @deprecated since version 10.1 and will be removed in 11.0.
 *
 * @property string  $slug
 * @property string  $title
 * @property string  $format
 * @property string  $content
 * @property Project $project
 */
final class Wiki extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'project',
        'slug',
        'title',
        'format',
        'content',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Wiki
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $wiki = new self($project, $data['slug'], $client);

        return $wiki->hydrate($data);
    }

    /**
     * @param Project     $project
     * @param string|null $slug
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Project $project, ?string $slug = null, Client $client = null)
    {
        parent::__construct();
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

        return self::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param array $params
     *
     * @return Wiki
     */
    public function update(array $params)
    {
        $data = $this->client->wiki()->update($this->project->id, $this->slug, $params);

        return self::fromArray($this->getClient(), $this->project, $data);
    }
}
