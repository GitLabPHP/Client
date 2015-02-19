<?php namespace Gitlab\Model;

use Gitlab\Client;

class ProjectNamespace extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'path',
        'kind',
        'owner_id',
        'created_at',
        'updated_at',
        'description'
    );

    /**
     * @param Client $client
     * @param array  $data
     * @return ProjectNamespace
     */
    public static function fromArray(Client $client, array $data)
    {
        $project = new static($data['id']);
        $project->setClient($client);

        return $project->hydrate($data);
    }

    /**
     * @param int $id
     * @param Client $client
     */
    public function __construct($id = null, Client $client = null)
    {
        $this->setClient($client);
        $this->id = $id;
    }
}
