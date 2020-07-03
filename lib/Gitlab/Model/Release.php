<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Release.
 *
 * @property-read string $tag_name
 * @property-read string $description
 * @property-read Commit $commit
 */
class Release extends AbstractModel
{
    /**
     * @var array
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
     * @param Client $client
     */
    public function __construct(Client $client = null)
    {
        $this->setClient($client);
    }
}
