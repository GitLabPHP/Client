<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property string $tag_name
 * @property string $description
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
        $release = new static($client);

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
