<?php namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Commit
 *
 * @property-read int $id
 * @property-read string $ref
 * @property-read string $sha
 * @property-read string $status
 */
class Pipeline extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'ref',
        'sha',
        'status'
    );

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     * @return Pipeline
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $pipeline = new self($project, $data['id'], $client);

        return $pipeline->hydrate($data);
    }

    /**
     * @param Project $project
     * @param int $id
     * @param Client  $client
     */
    public function __construct(Project $project, $id = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }
}
