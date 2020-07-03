<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Event
 *
 * @property-read string $title
 * @property-read int $id
 * @property-read string $action_name
 * @property-read string $data
 * @property-read int $target_id
 * @property-read string $target_type
 * @property-read string $target_title
 * @property-read int $author_id
 * @property-read string $author_username
 * @property-read User $author
 * @property-read Project $project
 */
class Event extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'title',
        'project_id',
        'action_name',
        'target_id',
        'target_type',
        'author_id',
        'author_username',
        'data',
        'target_title',
        'author',
        'project'
    );

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     * @return Event
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $event = new static($project, $client);

        if (isset($data['author_id'])) {
            $data['author'] = new User($data['author_id'], $client);
        }

        return $event->hydrate($data);
    }

    /**
     * @param Project $project
     * @param Client  $client
     */
    public function __construct(Project $project, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
    }
}
