<?php

namespace Gitlab\Model;

class MergeRequest extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'target_branch',
        'source_branch',
        'project_id',
        'title',
        'closed',
        'merged',
        'author',
        'assignee',
        'project'
    );

    public static function fromArray(Project $project, array $data)
    {
        $mr = new MergeRequest($project, $data['id']);

        if (isset($data['author'])) {
            $data['author'] = new User($data['author']);
        }

        if (isset($data['assignee'])) {
            $data['assignee'] = new User($data['assignee']);
        }

        return $mr->hydrate($data);
    }

    public function __construct(Project $project, $id = null)
    {
        $this->project = $project;
        $this->id = $id;
    }

    public function show()
    {
        $data = $this->api('mr')->show($this->project->id, $this->id);

        return MergeRequest::fromArray($this->project, $data);
    }

    public function update(array $params)
    {
        $data = $this->api('mr')->update($this->project->id, $this->id, $params);

        return MergeRequest::fromArray($this->project, $data);
    }

    public function close()
    {
        return $this->update(array('closed' => true));
    }

    public function open()
    {
        return $this->update(array('closed' => false));
    }

    public function addComment($note)
    {
        $data = $this->api('mr')->addComment($this->project->id, $this->id, $note);

        return Note::fromArray($this, $data);
    }

}
