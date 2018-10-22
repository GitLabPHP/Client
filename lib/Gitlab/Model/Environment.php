<?php namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Event
 *
 * @property-read string $name
 * @property-read int $id
 * @property-read string $slug
 * @property-read string $external_url
 */
class Environment extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'name',
        'slug',
        'external_url'
    );

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     * @return Event
     */
    public static function fromArray(Client $client, array $data)
    {
        $event = new static($client);

        return $event->hydrate($data);
    }

    /**
     * @param Project $project
     * @param Client  $client
     */
    public function __construct(Client $client = null)
    {
        $this->setClient($client);
    }
}
