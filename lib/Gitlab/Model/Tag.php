<?php namespace Gitlab\Model;

use Gitlab\Client;

class Tag extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'name',
        'commit',
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

        $this->project = $project;
        $this->name = $name;
    }
}
