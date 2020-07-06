<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property-read string $name
 * @property-read string $message
 * @property-read Commit|null $commit
 * @property-read Release|null $release
 * @property-read Project $project
 * @property-read bool $protected
 */
class Tag extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'name',
        'message',
        'commit',
        'release',
        'project',
        'protected',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Tag
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $branch = new static($project, $data['name'], $client);

        if (isset($data['commit'])) {
            $data['commit'] = Commit::fromArray($client, $project, $data['commit']);
        }

        if (isset($data['release'])) {
            $data['release'] = Release::fromArray($client, $data['release']);
        }

        return $branch->hydrate($data);
    }

    /**
     * @param Project     $project
     * @param string|null $name
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Project $project, $name = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('name', $name);
    }
}
