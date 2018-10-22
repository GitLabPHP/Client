<?php namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Tag
 *
 * @property-read string $name
 * @property-read bool $protected
 * @property-read Commit $commit
 * @property-read string $target
 * @property-read Project $project
 */
class Tag extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'name',
        'commit',
        'project',
        'target',
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

    /**
     * @return Tag
     */
    public function show()
    {
        $data = $this->client->repositories()->tag($this->project->id, $this->name);

        return static::fromArray($this->getClient(), $this->project, $data);
    }
}
