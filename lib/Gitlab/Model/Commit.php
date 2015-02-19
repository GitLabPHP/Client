<?php namespace Gitlab\Model;

use Gitlab\Client;

class Commit extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
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

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     * @return Commit
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $commit = new static($project, $data['id'], $client);

        if (isset($data['parents'])) {
            $parents = array();
            foreach ($data['parents'] as $parent) {
                $parents[] = static::fromArray($client, $project, $parent);
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

    /**
     * @param Project $project
     * @param int $id
     * @param Client  $client
     */
    public function __construct(Project $project, $id = null, Client $client = null)
    {
        $this->setClient($client);

        $this->project = $project;
        $this->id = $id;
    }
}
