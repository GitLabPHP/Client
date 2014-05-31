<?php

namespace Gitlab\Model;

use Gitlab\Client;

class MergeRequest extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'iid',
        'target_branch',
        'source_branch',
        'project_id',
        'title',
        'closed',
        'merged',
        'author',
        'assignee',
        'project',
        'state',
        'source_project_id',
        'target_project_id',
        'upvotes',
        'downvotes'
    );

    public static function fromArray(Client $client, Project $project, array $data)
    {
        $mr = new static($project, $data['id'], $client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        if (isset($data['assignee'])) {
            $data['assignee'] = User::fromArray($client, $data['assignee']);
        }

        return $mr->hydrate($data);
    }

    public function __construct(Project $project, $id = null, Client $client = null)
    {
        $this->setClient($client);

        $this->project = $project;
        $this->id = $id;
    }

    public function show()
    {
        $data = $this->api('mr')->show($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    public function update(array $params)
    {
        $data = $this->api('mr')->update($this->project->id, $this->id, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    public function close($comment = null)
    {
        if ($comment) {
            $this->addComment($comment);
        }

        return $this->update(array(
            'state_event' => 'close'
        ));
    }

    public function reopen()
    {
        return $this->update(array(
            'state_event' => 'reopen'
        ));
    }

    public function merge($message = null)
    {
        $data = $this->api('mr')->merge($this->project->id, $this->id, array('merge_commit_message' => $message));

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    public function merged()
    {
        return $this->update(array(
            'state_event' => 'merge'
        ));
    }

    public function addComment($note)
    {
        $data = $this->api('mr')->addComment($this->project->id, $this->id, $note);

        return Note::fromArray($this->getClient(), $this, $data);
    }

    public function showComments()
    {
        $notes = array();
        $data = $this->api('mr')->showComments($this->project->id, $this->id);

        foreach ($data as $note) {
            $notes[] = Note::fromArray($this->getClient(), $this, $note);
        }

        return $notes;
    }

    public function isClosed()
    {
        if (in_array($this->state, array('closed', 'merged'))) {
            return true;
        }

        return false;
    }

}
