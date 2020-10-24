<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @deprecated since version 10.1 and will be removed in 11.0.
 *
 * @property string  $name
 * @property string  $type
 * @property string  $mode
 * @property int     $id
 * @property string  $path
 * @property Project $project
 */
final class Node extends AbstractModel
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
        $node = new self($project, $data['id'], $client);

        return $node->hydrate($data);
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
        parent::__construct();
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }
}
