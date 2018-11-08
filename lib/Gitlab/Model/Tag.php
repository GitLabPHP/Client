<?php namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Tag
 *
 * @property-read string $name
 * @property-read bool $protected
 * @property-read Commit $commit
 * @property-read Project $project
 */
class Tag extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'name',
        'message',
        'commit',
        'release',
        'project',
        'protected'
    );

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     * @return Tag
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $branch = new static($project, $data['name'], $client);

        if (isset($data['commit'])) {
            $data['commit'] = Commit::fromArray($client, $project, $data['commit']);
        }

        if (isset($data['release'])) {
            $data['release'] = Release::fromArray($client, $data['release']);
        }

        return $branch->hydrate($data);
    }

    /**
     * @param Project $project
     * @param string $name
     * @param Client $client
     */
    public function __construct(Project $project, $name = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('name', $name);
    }
}
