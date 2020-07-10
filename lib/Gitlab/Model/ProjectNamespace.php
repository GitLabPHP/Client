<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $path
 * @property-read string $kind
 * @property-read int $owner_id
 * @property-read string $created_at
 * @property-read string $updated_at
 * @property-read string $description
 */
class ProjectNamespace extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'name',
        'path',
        'kind',
        'owner_id',
        'created_at',
        'updated_at',
        'description',
    ];

    /**
     * @param Client $client
     * @param array  $data
     *
     * @return ProjectNamespace
     */
    public static function fromArray(Client $client, array $data)
    {
        $project = new static($data['id']);
        $project->setClient($client);

        return $project->hydrate($data);
    }

    /**
     * @param int         $id
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct($id = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('id', $id);
    }
}
