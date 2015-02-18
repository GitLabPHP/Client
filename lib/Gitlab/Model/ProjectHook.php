<?php

namespace Gitlab\Model;

use Gitlab\Client;

class ProjectHook extends AbstractModel
{
    protected static $_properties = array(
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

    public static function fromArray(Client $client, Project $project, array $data)
    {
        $hook = new static($project, $data['id'], $client);

        return $hook->hydrate($data);
    }

    public function __construct(Project $project, $id, Client $client = null)
    {
        $this->setClient($client);

        $this->project = $project;
        $this->id = $id;
    }

    public function show()
    {
        $data = $this->api('projects')->hook($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    public function delete()
    {
        $this->api('projects')->removeHook($this->project->id, $this->id);

        return true;
    }

    public function remove()
    {
        return $this->delete();
    }

    public function update(array $params)
    {
        $params = array_merge(array(
            'url' => null,
            'push_events' => null,
            'issues_events' => null,
            'merge_requests_events' => null,
            'tag_push_events' => null
        ), $params);

        $data = $this->api('projects')->updateHook($this->project->id, $this->id, $params['url'], $params['push_events'], $params['issues_events'], $params['merge_requests_events'], $params['tag_push_events']);

        return static::fromArray($this->getClient(), $this->project, $data);
    }
}
