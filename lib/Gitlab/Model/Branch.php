<?php

namespace Gitlab\Model;

use Gitlab\Api\Projects;
use Gitlab\Client;

/**
 * Class Branch.
 *
 * @property-read string $name
 * @property-read bool $protected
 * @property-read Commit $commit
 * @property-read Project $project
 */
class Branch extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = [
        'name',
        'commit',
        'project',
        'protected',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Branch
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $branch = new self($project, $data['name'], $client);

        if (isset($data['commit'])) {
            $data['commit'] = Commit::fromArray($client, $project, $data['commit']);
        }

        return $branch->hydrate($data);
    }

    /**
     * @param Project $project
     * @param string  $name
     * @param Client  $client
     *
     * @return void
     */
    public function __construct(Project $project, $name = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('name', $name);
    }

    /**
     * @return Branch
     */
    public function show()
    {
        $data = $this->client->repositories()->branch($this->project->id, $this->name);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param bool $devPush
     * @param bool $devMerge
     *
     * @return Branch
     */
    public function protect($devPush = false, $devMerge = false)
    {
        $data = $this->client->repositories()->protectBranch($this->project->id, $this->name, $devPush, $devMerge);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @return Branch
     */
    public function unprotect()
    {
        $data = $this->client->repositories()->unprotectBranch($this->project->id, $this->name);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $this->client->repositories()->deleteBranch($this->project->id, $this->name);

        return true;
    }

    /**
     * @param array $parameters
     *
     * @see Projects::commits for available parameters.
     *
     * @return Commit[]
     */
    public function commits(array $parameters = [])
    {
        return $this->project->commits($parameters);
    }

    /**
     * @param string $file_path
     * @param string $content
     * @param string $commit_message
     *
     * @return File
     */
    public function createFile($file_path, $content, $commit_message)
    {
        $data = $this->client->repositories()->createFile($this->project->id, $file_path, $content, $this->name, $commit_message);

        return File::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param string $file_path
     * @param string $content
     * @param string $commit_message
     *
     * @return File
     */
    public function updateFile($file_path, $content, $commit_message)
    {
        $data = $this->client->repositories()->updateFile($this->project->id, $file_path, $content, $this->name, $commit_message);

        return File::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param string $file_path
     * @param string $commit_message
     *
     * @return bool
     */
    public function deleteFile($file_path, $commit_message)
    {
        $this->client->repositories()->deleteFile($this->project->id, $file_path, $this->name, $commit_message);

        return true;
    }
}
