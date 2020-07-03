<?php namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Contributor
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
     * @var array
     */
    protected static $properties = array(
        'name',
        'email',
        'commits',
        'additions',
        'deletions',
        'project'
    );

    /**
     * @param Client $client
     * @param Project $project
     * @param array $data
     * @return Contributor
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $contributor = new self($project, $client);

        return $contributor->hydrate($data);
    }

    /**
     * @param Project $project
     * @param Client $client
     */
    public function __construct(Project $project, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
    }
}
