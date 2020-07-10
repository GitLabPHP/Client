<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
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
 * @property-read Commit[]|null $parents
 * @property-read Node[] $tree
 * @property-read User|null $committer
 * @property-read User|null $author
 * @property-read Project $project
 * @property-read array|null $stats
 */
class Commit extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
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
        'stats',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Commit
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $commit = new static($project, $data['id'], $client);

        if (isset($data['parents'])) {
            $parents = [];
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
     * @param Project     $project
     * @param int|null    $id
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Project $project, $id = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }
}
