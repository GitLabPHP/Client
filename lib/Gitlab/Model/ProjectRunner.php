<?php namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class ProjectHook
 *
 * @property-read int $id
 * @property-read int $project_id
 * @property-read string $scope
 * @property-read string $type
 * @property-read string $status
 * @property-read string[] $tag_list
 * @property-read Project $project
 */
class ProjectRunner extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'project_id',
        'scope',
        'type',
        'status',
        'tag_list',
    );

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     * @return ProjectRunner
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $runner = new static($project, $data['id'], $client);

        return $runner->hydrate($data);
    }

    /**
     * @param Project $project
     * @param int $id
     * @param Client $client
     */
    public function __construct(Project $project, $id, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }

//    /**
//     * @return Project
//     */
//    public function show()
//    {
//        $data = $this->client->projects()->runners($this->project->id, $this->id);
//
//        return static::fromArray($this->getClient(), $this->project, $data);
//    }
//
//    /**
//     * @return bool
//     */
//    public function unprotect()
//    {
//        $this->client->projects()->unprotectTag($this->project->id, $this->id);
//
//        return true;
//    }

}
