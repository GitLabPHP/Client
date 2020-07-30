<?php

namespace Gitlab\Model;

use Gitlab\Api\Projects;
use Gitlab\Client;

/**
 * @final
 *
 * @property string      $name
 * @property bool        $protected
 * @property Commit|null $commit
 * @property Project     $project
 */
class Branch extends AbstractModel
{
    /**
     * @var string[]
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
        $branch = new static($project, $data['name'], $client);

        if (isset($data['commit'])) {
            $data['commit'] = Commit::fromArray($client, $project, $data['commit']);
        }

        return $branch->hydrate($data);
    }

    /**
     * @param Project     $project
     * @param string|null $name
     * @param Client|null $client
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
     * @param string      $file_path
     * @param string      $content
     * @param string      $commit_message
     * @param string|null $author_email
     * @param string|null $author_name
     *
     * @return File
     */
    public function createFile(
        $file_path,
        $content,
        $commit_message,
        $author_email = null,
        $author_name = null
    ) {
        $parameters = [
            'file_path' => $file_path,
            'branch' => $this->name,
            'content' => $content,
            'commit_message' => $commit_message,
        ];

        if (null !== $author_email) {
            $parameters['author_email'] = $author_email;
        }

        if (null !== $author_name) {
            $parameters['author_name'] = $author_name;
        }

        $data = $this->client->repositoryFiles()->createFile($this->project->id, $parameters);

        return File::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param string      $file_path
     * @param string      $content
     * @param string      $commit_message
     * @param string|null $author_email
     * @param string|null $author_name
     *
     * @return File
     */
    public function updateFile(
        $file_path,
        $content,
        $commit_message,
        $author_email = null,
        $author_name = null
    ) {
        $parameters = [
            'file_path' => $file_path,
            'branch' => $this->name,
            'content' => $content,
            'commit_message' => $commit_message,
        ];

        if (null !== $author_email) {
            $parameters['author_email'] = $author_email;
        }

        if (null !== $author_name) {
            $parameters['author_name'] = $author_name;
        }

        $data = $this->client->repositoryFiles()->updateFile($this->project->id, $parameters);

        return File::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param string      $file_path
     * @param string      $commit_message
     * @param string|null $author_email
     * @param string|null $author_name
     *
     * @return bool
     */
    public function deleteFile($file_path, $commit_message, $author_email = null, $author_name = null)
    {
        $parameters = [
            'file_path' => $file_path,
            'branch' => $this->name,
            'commit_message' => $commit_message,
        ];

        if (null !== $author_email) {
            $parameters['author_email'] = $author_email;
        }

        if (null !== $author_name) {
            $parameters['author_name'] = $author_name;
        }

        $this->client->repositoryFiles()->deleteFile($this->project->id, $parameters);

        return true;
    }
}
