<?php namespace Gitlab\Model;

use Gitlab\Client;

class Node extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'name',
        'type',
        'mode',
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

        $this->project = $project;
        $this->id = $id;
    }
}
