<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property-read string $note
 * @property-read string $path
 * @property-read string $line
 * @property-read string $line_type
 * @property-read User|null $author
 */
class CommitNote extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'note',
        'path',
        'line',
        'line_type',
        'author',
    ];

    /**
     * @param Client $client
     * @param array  $data
     *
     * @return CommitNote
     */
    public static function fromArray(Client $client, array $data)
    {
        $comment = new static($client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        return $comment->hydrate($data);
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
