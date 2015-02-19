<?php

namespace Gitlab\Model;

use Gitlab\Client;

class Event extends AbstractModel
{
    protected static $_properties = array(
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

    public static function fromArray(Client $client, Project $project, array $data)
    {
        $event = new static($project, $client);

        if (isset($data['author_id'])) {
            $data['author'] = new User($data['author_id'], $client);
        }

        return $event->hydrate($data);
    }

    public function __construct(Project $project, Client $client = null)
    {
        $this->setClient($client);

        $this->project = $project;
    }

}
