<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @deprecated since version 10.1 and will be removed in 11.0.
 *
 * @property int    $id
 * @property string $title
 * @property string $key
 * @property string $created_at
 */
final class Key extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'title',
        'key',
        'created_at',
    ];

    /**
     * @param Client $client
     * @param array  $data
     *
     * @return Key
     */
    public static function fromArray(Client $client, array $data)
    {
        $key = new self($client);

        return $key->hydrate($data);
    }

    /**
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Client $client = null)
    {
        parent::__construct();
        $this->setClient($client);
    }
}
