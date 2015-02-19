<?php namespace Gitlab\Model;

use Gitlab\Client;

class Milestone extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'iid',
        'project',
        'project_id',
        'title',
        'description',
        'due_date',
        'state',
        'closed',
        'updated_at',
        'created_at'
    );

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     * @return Milestone
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $milestone = new static($project, $data['id'], $client);

        return $milestone->hydrate($data);
    }

    /**
     * @param Project $project
     * @param int $id
     * @param Client  $client
     */
    public function __construct(Project $project, $id, Client $client = null)
    {
        $this->setClient($client);

        $this->id = $id;
        $this->project = $project;
    }

    /**
     * @return Milestone
     */
    public function show()
    {
        $data = $this->api('milestones')->show($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param array $params
     * @return Milestone
     */
    public function update(array $params)
    {
        $data = $this->api('milestones')->update($this->project->id, $this->id, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @return Milestone
     */
    public function complete()
    {
        return $this->update(array('closed' => true));
    }

    /**
     * @return Milestone
     */
    public function incomplete()
    {
        return $this->update(array('closed' => false));
    }
}
