<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Commit
 *
 * @property-read string $id
 * @property-read string $short_id
 * @property-read string $title
 * @property-read string $message
 * @property-read string $author_name
 * @property-read string $author_email
 * @property-read string $authored_date
 * @property-read string $committed_date
 * @property-read string $created_at
 * @property-read Commit[] $parents
 * @property-read Node[] $tree
 * @property-read User $committer
 * @property-read User $author
 * @property-read Project $project
 */
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
        'project',
        'stats'
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
        $this->setData('project', $project);
        $this->setData('id', $id);
    }
}
