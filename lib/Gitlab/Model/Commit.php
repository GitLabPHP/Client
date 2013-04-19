<?php

namespace Gitlab\Model;

use Gitlab\Client;

class Commit extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'short_id',
        'parents',
        'tree',
        'title',
        'message',
        'author',
        'author_name',
        'author_email',
        'committer',
        'authored_date',
        'committed_date',
        'created_at',
        'project'
    );

    public static function fromArray(Client $client, Project $project, array $data)
    {
        $commit = new Commit($project, $data['id']);
        $commit->setClient($client);

        if (isset($data['parents'])) {
            $parents = array();
            foreach ($data['parents'] as $parent) {
                $parents[] = Commit::fromArray($client, $project, $parent);
            }

            $data['parents'] = $parents;
        }

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        if (isset($data['committer'])) {
            $data['committer'] = User::fromArray($client, $data['committer']);
        }

        return $commit->hydrate($data);
    }

    public function __construct(Project $project, $id = null)
    {
        $this->project = $project;
        $this->id = $id;
    }

}
