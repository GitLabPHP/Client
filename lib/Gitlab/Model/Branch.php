<?php

namespace Gitlab\Model;

use Gitlab\Client;
use Gitlab\Api\AbstractApi as Api;

class Branch extends AbstractModel
{
    protected static $_properties = array(
        'name',
        'commit',
        'project',
        'protected'
    );

    public static function fromArray(Client $client, Project $project, array $data)
    {
        $branch = new Branch($project, $data['name'], $client);

        if (isset($data['commit'])) {
            $data['commit'] = Commit::fromArray($client, $project, $data['commit']);
        }

        return $branch->hydrate($data);
    }

    public function __construct(Project $project, $name = null, Client $client = null)
    {
        $this->setClient($client);

        $this->project = $project;
        $this->name = $name;
    }

    public function show()
    {
        $data = $this->api('repositories')->branch($this->project->id, $this->name);

        return Branch::fromArray($this->getClient(), $this->project, $data);
    }

    public function protect()
    {
        $data = $this->api('repositories')->protectBranch($this->project->id, $this->name);

        return Branch::fromArray($this->getClient(), $this->project, $data);
    }

    public function unprotect()
    {
        $data = $this->api('repositories')->unprotectBranch($this->project->id, $this->name);

        return Branch::fromArray($this->getClient(), $this->project, $data);
    }

    public function commits($page = 1, $per_page = Api::PER_PAGE)
    {
        return $this->project->commits($page, $per_page, $this->name);
    }

    public function createFile($file_path, $content, $commit_message)
    {
        $data = $this->api('repo')->createFile($this->project->id, $file_path, $content, $this->name, $commit_message);

        return File::fromArray($this->getClient(), $this, $data);
    }

    public function updateFile($file_path, $content, $commit_message)
    {
        $data = $this->api('repo')->updateFile($this->project->id, $file_path, $content, $this->name, $commit_message);

        return File::fromArray($this->getClient(), $this, $data);
    }

    public function deleteFile($file_path, $commit_message)
    {
        $this->api('repo')->deleteFile($this->project->id, $file_path, $this->name, $commit_message);

        return true;
    }
}
