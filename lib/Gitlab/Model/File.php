<?php

namespace Gitlab\Model;

use Gitlab\Client;

class File extends AbstractModel
{
    protected static $_properties = array(
        'project',
        'file_path',
        'branch_name'
    );

    public static function fromArray(Client $client, Project $project, array $data)
    {
        $file = new static($project, $data['file_path'], $client);

        return $file->hydrate($data);
    }

    public function __construct(Project $project, $file_path = null, Client $client = null)
    {
        $this->setClient($client);

        $this->project = $project;
        $this->file_path = $file_path;
    }
}
