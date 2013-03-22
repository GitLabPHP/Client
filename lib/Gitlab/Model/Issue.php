<?php

namespace Gitlab\Model;

class Issue extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'project_id',
        'title',
        'description',
        'labels',
        'milestone',
        'assignee',
        'author',
        'closed',
        'updated_at',
        'created_at',
        'project',
        'state'
    );

    public static function fromArray(Project $project, array $data)
    {
        $issue = new Issue($project, $data['id']);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($data['author']);
        }

        if (isset($data['assignee'])) {
            $data['assignee'] = User::fromArray($data['assignee']);
        }

        return $issue->hydrate($data);
    }

    public function __construct(Project $project, $id = null)
    {
        $this->project = $project;
        $this->id = $id;
    }

    public function show()
    {
        $data = $this->api('issues')->show($this->project->id, $this->id);

        return Issue::fromArray($this->project, $data);
    }

    public function update(array $params)
    {
        $data = $this->api('issues')->update($this->project->id, $this->id, $params);

        return Issue::fromArray($this->project, $data);
    }

    public function close()
    {
        return $this->update(array('closed' => true));
    }

    public function open()
    {
        return $this->update(array('closed' => false));
    }

    public function remove()
    {
        $this->api('issues')->remove($this->project->id, $this->id);

        return true;
    }

    public function addComment($body)
    {
        $data = $this->api('issues')->addComment($this->project->id, $this->id, array(
            'body' => $body
        ));

        return Note::fromArray($this, $data);
    }

}
