<?php namespace Gitlab\Model;

use Gitlab\Client;

class ProjectHook extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'project',
        'url',
        'project_id',
        'push_events',
        'issues_events',
        'merge_requests_events',
        'tag_push_events',
        'created_at'
    );

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     * @return ProjectHook
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $hook = new static($project, $data['id'], $client);

        return $hook->hydrate($data);
    }

    /**
     * @param Project $project
     * @param int $id
     * @param Client $client
     */
    public function __construct(Project $project, $id, Client $client = null)
    {
        $this->setClient($client);

        $this->project = $project;
        $this->id = $id;
    }

    /**
     * @return ProjectHook
     */
    public function show()
    {
        $data = $this->api('projects')->hook($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $this->api('projects')->removeHook($this->project->id, $this->id);

        return true;
    }

    /**
     * @return bool
     */
    public function remove()
    {
        return $this->delete();
    }

    /**
     * @param array $params
     * @return ProjectHook
     */
    public function update(array $params)
    {
        $params = array_merge(array(
            'url' => false,
            'push_events' => false,
            'issues_events' => false,
            'merge_requests_events' => false,
            'tag_push_events' => false
        ), $params);

        $data = $this->api('projects')->updateHook($this->project->id, $this->id, $params['url'], $params['push_events'], $params['issues_events'], $params['merge_requests_events'], $params['tag_push_events']);

        return static::fromArray($this->getClient(), $this->project, $data);
    }
}
