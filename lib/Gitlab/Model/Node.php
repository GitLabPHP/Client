<?php namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Node
 *
 * @property-read string $name
 * @property-read string $type
 * @property-read string $mode
 * @property-read string $path
 * @property-read int $id
 * @property-read Project $project
 */
class Node extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'name',
        'type',
        'mode',
        'path',
        'id',
        'project'
    );

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     * @return Node
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $node = new static($project, $data['id'], $client);

        return $node->hydrate($data);
    }

    /**
     * @param Project $project
     * @param int $id
     * @param Client $client
     */
    public function __construct(Project $project, $id = null, Client $client)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }
}
