<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @deprecated since version 10.1 and will be removed in 11.0.
 *
 * @property string    $note
 * @property string    $path
 * @property string    $line
 * @property string    $line_type
 * @property User|null $author
 */
final class CommitNote extends AbstractModel
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
        $comment = new self($client);

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
        parent::__construct();
        $this->setClient($client);
    }
}
