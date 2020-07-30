<?php

namespace Gitlab\Model;

use Gitlab\Api\Issues;
use Gitlab\Client;

/**
 * @final
 *
 * @property int        $id
 * @property int        $iid
 * @property int|string $project_id,
 * @property string     $title
 * @property string     $description
 * @property array      $labels
 * @property bool       $closed
 * @property string     $updated_at
 * @property string     $created_at
 * @property string     $state
 * @property User|null  $assignee
 * @property User|null  $author
 * @property Milestone  $milestone
 * @property Project    $project
 */
class Issue extends AbstractModel implements Noteable, Notable
{
    /**
     * @var string[]
     */
    protected static $properties = [
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
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
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
     * @param Project     $project
     * @param int|null    $iid
     * @param Client|null $client
     *
     * @return void
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
     *
     * @return Issue
     */
    public function update(array $params)
    {
        $data = $this->client->issues()->update($this->project->id, $this->iid, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param Project $toProject
     *
     * @return Issue
     */
    public function move(Project $toProject)
    {
        $data = $this->client->issues()->move($this->project->id, $this->iid, $toProject->id);

        return static::fromArray($this->getClient(), $toProject, $data);
    }

    /**
     * @param string|null $note
     *
     * @return Issue
     */
    public function close($note = null)
    {
        if (null !== $note) {
            $this->addNote($note);
        }

        return $this->update([
            'state_event' => 'close',
        ]);
    }

    /**
     * @return Issue
     */
    public function open()
    {
        return $this->update([
            'state_event' => 'reopen',
        ]);
    }

    /**
     * @return Issue
     */
    public function reopen()
    {
        return $this->open();
    }

    /**
     * @param string $body
     *
     * @return Note
     */
    public function addNote($body)
    {
        $data = $this->client->issues()->addNote($this->project->id, $this->iid, $body);

        return Note::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param string      $comment
     * @param string|null $created_at
     *
     * @return Note
     *
     * @deprecated since version 9.18 and will be removed in 10.0. Use the addNote() method instead.
     */
    public function addComment($comment, $created_at = null)
    {
        @\trigger_error(\sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0. Use the addNote() method instead.', __METHOD__), E_USER_DEPRECATED);

        if (null === $created_at) {
            return $this->addNote($comment);
        }

        $data = $this->client->issues()->addComment($this->project->id, $this->iid, [
            'body' => $comment,
            'created_at' => $created_at,
        ]);

        return Note::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @return Note[]
     *
     * @deprecated since version 9.18 and will be removed in 10.0. Use the result pager with the conventional API methods.
     */
    public function showComments()
    {
        @\trigger_error(\sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0. Use the result pager with the conventional API methods.', __METHOD__), E_USER_DEPRECATED);

        $notes = [];
        $data = $this->client->issues()->showNotes($this->project->id, $this->iid);

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
        return Issues::STATE_CLOSED === $this->state;
    }

    /**
     * @param string $label
     *
     * @return bool
     */
    public function hasLabel($label)
    {
        return \in_array($label, $this->labels, true);
    }

    /**
     * @return IssueLink[]
     */
    public function links()
    {
        $data = $this->client->issueLinks()->all($this->project->id, $this->iid);
        if (!\is_array($data)) {
            return [];
        }

        $projects = $this->client->projects();

        return \array_map(function ($data) use ($projects) {
            return IssueLink::fromArray(
                $this->client,
                Project::fromArray($this->client, $projects->show($data['project_id'])),
                $data
            );
        }, $data);
    }

    /**
     * @param Issue $target
     *
     * @return Issue[]
     */
    public function addLink(self $target)
    {
        $data = $this->client->issueLinks()->create($this->project->id, $this->iid, $target->project->id, $target->iid);
        if (!\is_array($data)) {
            return [];
        }

        return [
            'source_issue' => static::fromArray($this->client, $this->project, $data['source_issue']),
            'target_issue' => static::fromArray($this->client, $target->project, $data['target_issue']),
        ];
    }

    /**
     * @param int $issue_link_id
     *
     * @return Issue[]
     */
    public function removeLink($issue_link_id)
    {
        // The two related issues have the same link ID.
        $data = $this->client->issueLinks()->remove($this->project->id, $this->iid, $issue_link_id);
        if (!\is_array($data)) {
            return [];
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
