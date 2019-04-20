<?php namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class ProjectHook
 *
 * @property-read string $name
 * @property-read int $project_id
 * @property-read array $create_access_levels
 * @property-read Project $project
 */
class ProjectProtectedTag extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'name',
        'project_id',
        'create_access_levels',
    );

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     * @return ProjectProtectedBranch
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $protectedTag = new static($project, $data['name'], $client);

        return $protectedTag->hydrate($data);
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
     * @return ProjectProtectedTag
     */
    public function show()
    {
        $data = $this->client->projects()->protectedTag($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @return bool
     */
    public function unprotect()
    {
        $this->client->projects()->unprotectTag($this->project->id, $this->id);

        return true;
    }

}
