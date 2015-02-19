<?php namespace Gitlab\Model;

use Gitlab\Client;

class Issue extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'iid',
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
        'state',
        'labels'
    );

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     * @return Issue
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $issue = new static($project, $data['id'], $client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        if (isset($data['assignee'])) {
            $data['assignee'] = User::fromArray($client, $data['assignee']);
        }

        return $issue->hydrate($data);
    }

    /**
     * @param Project $project
     * @param int $id
     * @param Client $client
     */
    public function __construct(Project $project, $id = null, Client $client = null)
    {
        $this->setClient($client);

        $this->project = $project;
        $this->id = $id;
    }

    /**
     * @return Issue
     */
    public function show()
    {
        $data = $this->api('issues')->show($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param array $params
     * @return Issue
     */
    public function update(array $params)
    {
        $data = $this->api('issues')->update($this->project->id, $this->id, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param string $comment
     * @return Issue
     */
    public function close($comment = null)
    {
        if ($comment) {
            $this->addComment($comment);
        }

        return $this->update(array(
            'state_event' => 'close'
        ));
    }

    /**
     * @return Issue
     */
    public function open()
    {
        return $this->update(array(
            'state_event' => 'reopen'
        ));
    }

    /**
     * @param string $body
     * @return Note
     */
    public function addComment($body)
    {
        $data = $this->api('issues')->addComment($this->project->id, $this->id, array(
            'body' => $body
        ));

        return Note::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @return Note[]
     */
    public function showComments()
    {
        $notes = array();
        $data = $this->api('issues')->showComments($this->project->id, $this->id);

        foreach ($data as $note) {
            $notes[] = Note::fromArray($this->getClient(), $this, $note);
        }

        return $notes;
    }
}
