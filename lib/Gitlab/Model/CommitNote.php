<?php namespace Gitlab\Model;

use Gitlab\Client;

class CommitNote extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'note',
        'path',
        'line',
        'line_type',
        'author'
    );

    /**
     * @param Client $client
     * @param array  $data
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
     * @param Client $client
     */
    public function __construct(Client $client = null)
    {
        $this->setClient($client);
    }
}
