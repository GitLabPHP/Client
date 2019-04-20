<?php namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class ProjectHook
 *
 * @property-read string $name
 * @property-read int $project_id
 * @property-read array $push_access_levels
 * @property-read array $merge_access_levels
 * @property-read Project $project
 */
class ProjectProtectedBranch extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'name',
        'project_id',
        'push_access_levels',
        'merge_access_levels',
    );

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     * @return ProjectProtectedBranch
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $protectedBranch = new static($project, $data['name'], $client);

        return $protectedBranch->hydrate($data);
    }

    /**
     * @param Project $project
     * @param int $id
     * @param Client $client
     */
    public function __construct(Project $project, $name, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('name', $name);
    }

    /**
     * @return ProjectHook
     */
    public function show()
    {
        $data = $this->client->projects()->protectedBranch($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @return bool
     */
    public function unprotect()
    {
        $this->client->projects()->unprotectBranch($this->project->id, $this->id);

        return true;
    }

}
