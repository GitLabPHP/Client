<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property-read string $name
 * @property-read string $type
 * @property-read string $mode
 * @property-read int $id
 * @property-read string $path
 * @property-read Project $project
 */
class Node extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'name',
        'type',
        'mode',
        'id',
        'path',
        'project',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Node
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $node = new static($project, $data['id'], $client);

        return $node->hydrate($data);
    }

    /**
     * @param Project     $project
     * @param int         $id
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Project $project, $id = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }
}
