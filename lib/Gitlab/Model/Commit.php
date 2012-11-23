<?php

namespace Gitlab\Model;

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

    public static function fromArray(Project $project, array $data)
    {
        $commit = new Commit($project, $data['id']);

        if (isset($data['parents'])) {
            $parents = array();
            foreach ($data['parents'] as $parent) {
                $parents[] = Commit::fromArray($project, $parent);
            }

            $data['parents'] = $parents;
        }

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($data['author']);
        }

        if (isset($data['committer'])) {
            $data['committer'] = User::fromArray($data['committer']);
        }

        return $commit->hydrate($data);
    }

    public function __construct(Project $project, $id = null)
    {
        $this->project = $project;
        $this->id = $id;
    }

}
