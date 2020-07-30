<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property string $tag_name
 * @property string $description
 */
final class Release extends AbstractModel
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
