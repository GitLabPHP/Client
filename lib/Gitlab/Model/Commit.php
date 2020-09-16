<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property string        $id
 * @property string        $short_id
 * @property string        $title
 * @property string        $message
 * @property string        $author_name
 * @property string        $author_email
 * @property string        $authored_date
 * @property string        $committed_date
 * @property string        $created_at
 * @property Commit[]|null $parents
 * @property Node[]        $tree
 * @property User|null     $committer
 * @property User|null     $author
 * @property Project       $project
 * @property array|null    $stats
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
     * @param string|null $id
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
