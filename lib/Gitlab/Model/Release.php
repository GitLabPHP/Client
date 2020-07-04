<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read string $tag_name
 * @property-read string $description
 */
class Release extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'tag_name',
        'description',
    ];

    /**
     * @param Client $client
     * @param array  $data
     *
     * @return Release
     */
    public static function fromArray(Client $client, array $data)
    {
        $release = new self($client);

        return $release->hydrate($data);
    }

    /**
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Client $client = null)
    {
        $this->setClient($client);
    }
}
