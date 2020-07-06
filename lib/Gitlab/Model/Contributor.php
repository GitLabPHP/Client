<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property-read string $name
 * @property-read string $email
 * @property-read int $commits
 * @property-read int $additions
 * @property-read int $deletions
 * @property-read Project $project
 */
class Contributor extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'name',
        'email',
        'commits',
        'additions',
        'deletions',
        'project',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Contributor
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $contributor = new static($project, $client);

        return $contributor->hydrate($data);
    }

    /**
     * @param Project     $project
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Project $project, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
    }
}
