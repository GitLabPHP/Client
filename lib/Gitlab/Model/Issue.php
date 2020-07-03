<?php namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Issue
 *
 * @property-read int $id
 * @property-read int $iid
 * @property-read int $project_id,
 * @property-read string $title
 * @property-read string $description
 * @property-read array $labels
 * @property-read bool $closed
 * @property-read string $updated_at
 * @property-read string $created_at
 * @property-read string $state
 * @property-read User $assignee
 * @property-read User $author
 * @property-read Milestone $milestone
 * @property-read Project $project
 */
class Issue extends AbstractModel implements Noteable
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
        'state'
    );

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     * @return Issue
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $issue = new static($project, $data['iid'], $client);

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
     * @param int|null $iid
     * @param Client|null $client
     */
    public function __construct(Project $project, $iid = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('iid', $iid);
    }

    /**
     * @return Issue
     */
    public function show()
    {
        $data = $this->client->issues()->show($this->project->id, $this->iid);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param array $params
     * @return Issue
     */
    public function update(array $params)
    {
        $data = $this->client->issues()->update($this->project->id, $this->iid, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param Project $toProject
     * @return Issue
     */
    public function move(Project $toProject)
    {
        $data = $this->client->issues()->move($this->project->id, $this->iid, $toProject->id);

        return static::fromArray($this->getClient(), $toProject, $data);
    }

    /**
     * @param string|null $comment
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
     * @return Issue
     */
    public function reopen()
    {
        return $this->open();
    }

    /**
     * @param string $comment
     * @return Note
     */
    public function addComment($comment)
    {
        $data = $this->client->issues()->addComment($this->project->id, $this->iid, array(
            'body' => $comment
        ));

        return Note::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @return Note[]
     */
    public function showComments()
    {
        $notes = array();
        $data = $this->client->issues()->showComments($this->project->id, $this->iid);

        foreach ($data as $note) {
            $notes[] = Note::fromArray($this->getClient(), $this, $note);
        }

        return $notes;
    }

    /**
     * @return bool
     */
    public function isClosed()
    {
        return $this->state === 'closed';
    }

    /**
     * @param string $label
     * @return bool
     */
    public function hasLabel($label)
    {
        return in_array($label, $this->labels);
    }

    /**
     * @return IssueLink[]
     */
    public function links()
    {
        $data = $this->client->issueLinks()->all($this->project->id, $this->iid);
        if (!is_array($data)) {
            return array();
        }

        $projects = $this->client->projects();

        return array_map(function ($data) use ($projects) {
            return IssueLink::fromArray(
                $this->client,
                Project::fromArray($this->client, $projects->show($data['project_id'])),
                $data
            );
        }, $data);
    }

    /**
     * @param Issue $target
     * @return Issue[]
     */
    public function addLink(Issue $target)
    {
        $data = $this->client->issueLinks()->create($this->project->id, $this->iid, $target->project->id, $target->iid);
        if (!is_array($data)) {
            return array();
        }

        return [
            'source_issue' => static::fromArray($this->client, $this->project, $data['source_issue']),
            'target_issue' => static::fromArray($this->client, $target->project, $data['target_issue']),
        ];
    }

    /**
     * @param int $issue_link_id
     * @return Issue[]
     */
    public function removeLink($issue_link_id)
    {
        // The two related issues have the same link ID.
        $data = $this->client->issueLinks()->remove($this->project->id, $this->iid, $issue_link_id);
        if (!is_array($data)) {
            return array();
        }

        $targetProject = Project::fromArray(
            $this->client,
            $this->client->projects()->show($data['target_issue']['project_id'])
        );

        return [
            'source_issue' => static::fromArray($this->client, $this->project, $data['source_issue']),
            'target_issue' => static::fromArray($this->client, $targetProject, $data['target_issue']),
        ];
    }
}
