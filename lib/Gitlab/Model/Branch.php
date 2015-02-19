<?php namespace Gitlab\Model;

use Gitlab\Client;
use Gitlab\Api\AbstractApi as Api;

/**
 * Class Branch
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
    protected static $properties = array(
        'name',
        'commit',
        'project',
        'protected'
    );

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
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
     * @param Project $project
     * @param string $name
     * @param Client $client
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
        $data = $this->api('repositories')->branch($this->project->id, $this->name);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @return Branch
     */
    public function protect()
    {
        $data = $this->api('repositories')->protectBranch($this->project->id, $this->name);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @return Branch
     */
    public function unprotect()
    {
        $data = $this->api('repositories')->unprotectBranch($this->project->id, $this->name);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $this->api('repositories')->deleteBranch($this->project->id, $this->name);

        return true;
    }

    /**
     * @param int $page
     * @param int $per_page
     * @return Commit[]
     */
    public function commits($page = 1, $per_page = Api::PER_PAGE)
    {
        return $this->project->commits($page, $per_page, $this->name);
    }

    /**
     * @param string $file_path
     * @param string $content
     * @param string $commit_message
     * @return File
     */
    public function createFile($file_path, $content, $commit_message)
    {
        $data = $this->api('repositories')->createFile($this->project->id, $file_path, $content, $this->name, $commit_message);

        return File::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param string $file_path
     * @param string $content
     * @param string $commit_message
     * @return File
     */
    public function updateFile($file_path, $content, $commit_message)
    {
        $data = $this->api('repositories')->updateFile($this->project->id, $file_path, $content, $this->name, $commit_message);

        return File::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param string $file_path
     * @param string $commit_message
     * @return bool
     */
    public function deleteFile($file_path, $commit_message)
    {
        $this->api('repositories')->deleteFile($this->project->id, $file_path, $this->name, $commit_message);

        return true;
    }
}
