<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Api\MergeRequests;
use Gitlab\Client;

/**
 * @property int            $id
 * @property int            $iid
 * @property string         $target_branch
 * @property string         $source_branch
 * @property int|string     $project_id
 * @property string         $title
 * @property string         $description
 * @property bool           $closed
 * @property bool           $merged
 * @property string         $state
 * @property int|string     $source_project_id
 * @property int|string     $target_project_id
 * @property int            $upvotes
 * @property int            $downvotes
 * @property array          $labels
 * @property User|null      $author
 * @property User|null      $assignee
 * @property Project        $project
 * @property Milestone|null $milestone
 * @property File[]|null    $files
 */
final class MergeRequest extends AbstractModel implements Notable, Stateful
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'iid',
        'target_branch',
        'source_branch',
        'project_id',
        'title',
        'description',
        'closed',
        'merged',
        'author',
        'assignee',
        'project',
        'state',
        'source_project_id',
        'target_project_id',
        'upvotes',
        'downvotes',
        'labels',
        'milestone',
        'files',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return MergeRequest
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $mr = new self($project, $data['id'], $client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        if (isset($data['assignee'])) {
            $data['assignee'] = User::fromArray($client, $data['assignee']);
        }

        if (isset($data['milestone'])) {
            $data['milestone'] = Milestone::fromArray($client, $project, $data['milestone']);
        }

        if (isset($data['files'])) {
            $files = [];
            foreach ($data['files'] as $file) {
                $files[] = File::fromArray($client, $project, $file);
            }

            $data['files'] = $files;
        }

        return $mr->hydrate($data);
    }

    /**
     * @param Project     $project
     * @param int|null    $iid
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Project $project, ?int $iid = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('iid', $iid);
    }

    /**
     * @return MergeRequest
     */
    public function show()
    {
        $data = $this->client->mergeRequests()->show($this->project->id, $this->iid);

        return self::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param array $params
     *
     * @return MergeRequest
     */
    public function update(array $params)
    {
        $data = $this->client->mergeRequests()->update($this->project->id, $this->iid, $params);

        return self::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param string|null $note
     *
     * @return MergeRequest
     */
    public function close(?string $note = null)
    {
        if (null !== $note) {
            $this->addNote($note);
        }

        return $this->update([
            'state_event' => 'close',
        ]);
    }

    /**
     * @return MergeRequest
     */
    public function reopen()
    {
        return $this->update([
            'state_event' => 'reopen',
        ]);
    }

    /**
     * @return MergeRequest
     */
    public function open()
    {
        return $this->reopen();
    }

    /**
     * @param string|null $message
     *
     * @return MergeRequest
     */
    public function merge(?string $message = null)
    {
        $data = $this->client->mergeRequests()->merge($this->project->id, $this->iid, [
            'merge_commit_message' => $message,
        ]);

        return self::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @return MergeRequest
     */
    public function merged()
    {
        return $this->update([
            'state_event' => 'merge',
        ]);
    }

    /**
     * @param string $body
     * @param array  $params
     *
     * @return Note
     */
    public function addNote(string $body, array $params = [])
    {
        $data = $this->client->mergeRequests()->addNote($this->project->id, $this->iid, $body, $params);

        return Note::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @return bool
     */
    public function isClosed()
    {
        return MergeRequests::STATE_CLOSED === $this->state || MergeRequests::STATE_MERGED === $this->state;
    }

    /**
     * @return MergeRequest
     */
    public function changes()
    {
        $data = $this->client->mergeRequests()->changes($this->project->id, $this->iid);

        return self::fromArray($this->getClient(), $this->project, $data);
    }
}
