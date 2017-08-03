<?php namespace Gitlab\Model;

use Gitlab\Api\Projects;
use Gitlab\Api\Repositories;
use Gitlab\Client;

/**
 * Class Project
 *
 * @property-read int $id
 * @property-read string $code
 * @property-read string $name
 * @property-read string $name_with_namespace
 * @property-read string $description
 * @property-read string $path
 * @property-read string $path_with_namespace
 * @property-read string $ssh_url_to_repo
 * @property-read string $http_url_to_repo
 * @property-read string $web_url
 * @property-read string $default_branch
 * @property-read bool $private
 * @property-read bool $public
 * @property-read bool $issues_enabled
 * @property-read bool $merge_requests_enabled
 * @property-read bool $wall_enabled
 * @property-read bool $wiki_enabled
 * @property-read bool $snippets_enabled
 * @property-read string $created_at
 * @property-read int $greatest_access_level
 * @property-read string $last_activity_at
 * @property-read string $tag_list
 * @property-read User $owner
 * @property-read ProjectNamespace $namespace
 */
class Project extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'code',
        'name',
        'name_with_namespace',
        'namespace',
        'description',
        'path',
        'path_with_namespace',
        'ssh_url_to_repo',
        'http_url_to_repo',
        'web_url',
        'default_branch',
        'owner',
        'private',
        'public',
        'issues_enabled',
        'merge_requests_enabled',
        'wall_enabled',
        'wiki_enabled',
        'created_at',
        'greatest_access_level',
        'last_activity_at',
        'snippets_enabled',
        'tag_list'
    );

    /**
     * @param Client $client
     * @param array $data
     * @return Project
     */
    public static function fromArray(Client $client, array $data)
    {
        $project = new static($data['id']);
        $project->setClient($client);

        if (isset($data['owner'])) {
            $data['owner'] = User::fromArray($client, $data['owner']);
        }

        if (isset($data['namespace']) && is_array($data['namespace'])) {
            $data['namespace'] = ProjectNamespace::fromArray($client, $data['namespace']);
        }

        return $project->hydrate($data);
    }

    /**
     * @param Client $client
     * @param string $name
     * @param array $params
     * @return Project
     */
    public static function create(Client $client, $name, array $params = array())
    {
        $data = $client->projects()->create($name, $params);

        return static::fromArray($client, $data);
    }

    /**
     * @param int $user_id
     * @param Client $client
     * @param string $name
     * @param array $params
     * @return Project
     */
    public static function createForUser($user_id, Client $client, $name, array $params = array())
    {
        $data = $client->projects()->createForUser($user_id, $name, $params);

        return static::fromArray($client, $data);
    }
    /**
     * @param int $id
     * @param Client $client
     */
    public function __construct($id = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('id', $id);
    }

    /**
     * @return Project
     */
    public function show()
    {
        $data = $this->client->projects()->show($this->id);

        return static::fromArray($this->getClient(), $data);
    }

    /**
     * @param array $params
     * @return Project
     */
    public function update(array $params)
    {
        $data = $this->client->projects()->update($this->id, $params);

        return static::fromArray($this->getClient(), $data);
    }

    /**
     * @return Project
     */
    public function archive()
    {
        $data = $this->client->projects()->archive($this->id);

        return static::fromArray($this->getClient(), $data);
    }

    /**
     * @return Project
     */
    public function unarchive()
    {
        $data = $this->client->projects()->unarchive($this->id);

        return static::fromArray($this->getClient(), $data);
    }

    /**
     * @return bool
     */
    public function remove()
    {
        $this->client->projects()->remove($this->id);

        return true;
    }

    /**
     * @param string $username_query
     * @return User[]
     */
    public function members($username_query = null)
    {
        $data = $this->client->projects()->members($this->id, $username_query);

        $members = array();
        foreach ($data as $member) {
            $members[] = User::fromArray($this->getClient(), $member);
        }

        return $members;
    }

    /**
     * @param int $user_id
     * @return User
     */
    public function member($user_id)
    {
        $data = $this->client->projects()->member($this->id, $user_id);

        return User::fromArray($this->getClient(), $data);
    }

    /**
     * @param int $user_id
     * @param int $access_level
     * @return User
     */
    public function addMember($user_id, $access_level)
    {
        $data = $this->client->projects()->addMember($this->id, $user_id, $access_level);

        return User::fromArray($this->getClient(), $data);
    }

    /**
     * @param int $user_id
     * @param int $access_level
     * @return User
     */
    public function saveMember($user_id, $access_level)
    {
        $data = $this->client->projects()->saveMember($this->id, $user_id, $access_level);

        return User::fromArray($this->getClient(), $data);
    }

    /**
     * @param int $user_id
     * @return bool
     */
    public function removeMember($user_id)
    {
        $this->client->projects()->removeMember($this->id, $user_id);

        return true;
    }

    /**
     * @param array $parameters
     *
     * @see Projects::hooks() for available parameters.
     *
     * @return ProjectHook[]
     */
    public function hooks(array $parameters = [])
    {
        $data = $this->client->projects()->hooks($this->id, $parameters);

        $hooks = array();
        foreach ($data as $hook) {
            $hooks[] = ProjectHook::fromArray($this->getClient(), $this, $hook);
        }

        return $hooks;
    }

    /**
     * @param int $id
     * @return ProjectHook
     */
    public function hook($id)
    {
        $hook = new ProjectHook($this, $id, $this->getClient());

        return $hook->show();
    }

    /**
     * @param string $url
     * @param array $events
     * @return ProjectHook
     */
    public function addHook($url, array $events = array())
    {
        $data = $this->client->projects()->addHook($this->id, $url, $events);

        return ProjectHook::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param int $hook_id
     * @param array $params
     * @return mixed
     */
    public function updateHook($hook_id, array $params)
    {
        $hook = new ProjectHook($this, $hook_id, $this->getClient());

        return $hook->update($params);
    }

    /**
     * @param int $hook_id
     * @return bool
     */
    public function removeHook($hook_id)
    {
        $hook = new ProjectHook($this, $hook_id, $this->getClient());

        return $hook->delete();
    }

    /**
     * @return Key[]
     */
    public function deployKeys()
    {
        $data = $this->client->projects()->deployKeys($this->id);

        $keys = array();
        foreach ($data as $key) {
            $keys[] = Key::fromArray($this->getClient(), $key);
        }

        return $keys;
    }

    /**
     * @param int $key_id
     * @return Key
     */
    public function deployKey($key_id)
    {
        $data = $this->client->projects()->deployKey($this->id, $key_id);

        return Key::fromArray($this->getClient(), $data);
    }

    /**
     * @param string $title
     * @param string $key
     * @return Key
     */
    public function addDeployKey($title, $key)
    {
        $data = $this->client->projects()->addDeployKey($this->id, $title, $key);

        return Key::fromArray($this->getClient(), $data);
    }

    /**
     * @param string $key_id
     * @return bool
     */
    public function deleteDeployKey($key_id)
    {
        $this->client->projects()->deleteDeployKey($this->id, $key_id);

        return true;
    }

    /**
     * @param string $key_id
     * @return bool
     */
    public function enableDeployKey($key_id)
    {
        $this->client->projects()->enableDeployKey($this->id, $key_id);

        return true;
    }

    /**
     * @param string $name
     * @param string $ref
     * @return Branch
     */
    public function createBranch($name, $ref)
    {
        $data = $this->client->repositories()->createBranch($this->id, $name, $ref);

        return Branch::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function deleteBranch($name)
    {
        $this->client->repositories()->deleteBranch($this->id, $name);

        return true;
    }

    /**
     * @return Branch[]
     */
    public function branches()
    {
        $data = $this->client->repositories()->branches($this->id);

        $branches = array();
        foreach ($data as $branch) {
            $branches[] = Branch::fromArray($this->getClient(), $this, $branch);
        }

        return $branches;
    }

    /**
     * @param string $branch_name
     * @return Branch
     */
    public function branch($branch_name)
    {
        $branch = new Branch($this, $branch_name);
        $branch->setClient($this->getClient());

        return $branch->show();
    }

    /**
     * @param string $branch_name
     * @param bool $devPush
     * @param bool $devMerge
     * @return Branch
     */
    public function protectBranch($branch_name, $devPush = false, $devMerge = false)
    {
        $branch = new Branch($this, $branch_name);
        $branch->setClient($this->getClient());

        return $branch->protect($devPush, $devMerge);
    }

    /**
     * @param string $branch_name
     * @return Branch
     */
    public function unprotectBranch($branch_name)
    {
        $branch = new Branch($this, $branch_name);
        $branch->setClient($this->getClient());

        return $branch->unprotect();
    }

    /**
     * @return Tag[]
     */
    public function tags()
    {
        $data = $this->client->repositories()->tags($this->id);

        $tags = array();
        foreach ($data as $tag) {
            $tags[] = Tag::fromArray($this->getClient(), $this, $tag);
        }

        return $tags;
    }

    /**
     * @param array $parameters
     *
     * @see Repositories::commits() for available parameters.
     *
     * @return Commit[]
     */
    public function commits(array $parameters = [])
    {
        $data = $this->client->repositories()->commits($this->id, $parameters);

        $commits = array();
        foreach ($data as $commit) {
            $commits[] = Commit::fromArray($this->getClient(), $this, $commit);
        }

        return $commits;
    }

    /**
     * @param string $sha
     * @return Commit
     */
    public function commit($sha)
    {
        $data = $this->client->repositories()->commit($this->id, $sha);

        return Commit::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param string $ref
     * @param array $parameters
     *
     * @see Repositories::commitComments() for available parameters.
     *
     * @return Commit[]
     */
    public function commitComments($ref, array $parameters = [])
    {
        $data = $this->client->repositories()->commitComments($this->id, $ref, $parameters);

        $comments = array();
        foreach ($data as $comment) {
            $comments[] = CommitNote::fromArray($this->getClient(), $comment);
        }

        return $comments;
    }

    /**
     * @param string $ref
     * @param string $note
     * @param array $params
     * @return CommitNote
     */
    public function createCommitComment($ref, $note, array $params = array())
    {
        $data = $this->client->repositories()->createCommitComment($this->id, $ref, $note, $params);

        return CommitNote::fromArray($this->getClient(), $data);
    }

    /**
     * @param string $sha
     * @return string
     */
    public function diff($sha)
    {
        return $this->client->repositories()->diff($this->id, $sha);
    }

    /**
     * @param string $from
     * @param string $to
     * @return Comparison
     */
    public function compare($from, $to)
    {
        $data = $this->client->repositories()->compare($this->id, $from, $to);

        return Comparison::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param array $params
     * @return Node[]
     */
    public function tree(array $params = array())
    {
        $data = $this->client->repositories()->tree($this->id, $params);

        $tree = array();
        foreach ($data as $node) {
            $tree[] = Node::fromArray($this->getClient(), $this, $node);
        }

        return $tree;
    }

    /**
     * @param string $sha
     * @param string $filepath
     * @return string
     */
    public function blob($sha, $filepath)
    {
        return $this->client->repositories()->blob($this->id, $sha, $filepath);
    }

    /**
     * @param $sha
     * @param $filepath
     *
     * @return array
     */
    public function getFile($sha, $filepath)
    {
        return $this->client->repositories()->getFile($this->id, $filepath, $sha);
    }

    /**
     * @param string $file_path
     * @param string $content
     * @param string $branch_name
     * @param string $commit_message
     * @param string $author_email
     * @param string $author_name
     * @return File
     */
    public function createFile($file_path, $content, $branch_name, $commit_message, $author_email = null, $author_name = null)
    {
        $data = $this->client->repositories()->createFile($this->id, $file_path, $content, $branch_name, $commit_message, null, $author_email, $author_name);

        return File::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param string $file_path
     * @param string $content
     * @param string $branch_name
     * @param string $commit_message
     * @param string $author_email
     * @param string $author_name
     * @return File
     */
    public function updateFile($file_path, $content, $branch_name, $commit_message, $author_email = null, $author_name = null)
    {
        $data = $this->client->repositories()->updateFile($this->id, $file_path, $content, $branch_name, $commit_message, null, $author_email, $author_name);

        return File::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param string $file_path
     * @param string $branch_name
     * @param string $commit_message
     * @param string $author_email
     * @param string $author_name
     * @return bool
     */
    public function deleteFile($file_path, $branch_name, $commit_message, $author_email = null, $author_name = null)
    {
        $this->client->repositories()->deleteFile($this->id, $file_path, $branch_name, $commit_message, $author_email, $author_name);

        return true;
    }

    /**
     * @param array $parameters
     *
     * @see Projects::events() for available parameters.
     *
     * @return Event[]
     */
    public function events(array $parameters = [])
    {
        $data = $this->client->projects()->events($this->id, $parameters);

        $events = array();
        foreach ($data as $event) {
            $events[] = Event::fromArray($this->getClient(), $this, $event);
        }

        return $events;
    }

    /**
     * @param array $parameters
     *
     * @see MergeRequests::all() for available parameters.
     *
     * @return MergeRequest[]
     */
    public function mergeRequests(array $parameters = [])
    {
        $data = $this->client->mergeRequests()->all($this->id, $parameters);

        $mrs = array();
        foreach ($data as $mr) {
            $mrs[] = MergeRequest::fromArray($this->getClient(), $this, $mr);
        }

        return $mrs;
    }

    /**
     * @param int $id
     * @return MergeRequest
     */
    public function mergeRequest($id)
    {
        $mr = new MergeRequest($this, $id, $this->getClient());

        return $mr->show();
    }

    /**
     * @param string $source
     * @param string $target
     * @param string $title
     * @param int $assignee
     * @param string $description
     * @return MergeRequest
     */
    public function createMergeRequest($source, $target, $title, $assignee = null, $description = null)
    {
        $data = $this->client->mergeRequests()->create($this->id, $source, $target, $title, $assignee, null, $description);

        return MergeRequest::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param int $id
     * @param array $params
     * @return MergeRequest
     */
    public function updateMergeRequest($id, array $params)
    {
        $mr = new MergeRequest($this, $id, $this->getClient());

        return $mr->update($params);
    }

    /**
     * @param int $id
     * @return MergeRequest
     */
    public function closeMergeRequest($id)
    {
        $mr = new MergeRequest($this, $id, $this->getClient());

        return $mr->close();
    }

    /**
     * @param int $id
     * @return MergeRequest
     */
    public function openMergeRequest($id)
    {
        $mr = new MergeRequest($this, $id, $this->getClient());

        return $mr->reopen();
    }

    /**
     * @param int $id
     * @return MergeRequest
     */
    public function mergeMergeRequest($id)
    {
        $mr = new MergeRequest($this, $id, $this->getClient());

        return $mr->merge();
    }

    /**
     * @param array $parameters
     *
     * @see Issues::all() for available parameters.
     *
     * @return Issue[]
     */
    public function issues(array $parameters = [])
    {
        $data = $this->client->issues()->all($this->id, $parameters);

        $issues = array();
        foreach ($data as $issue) {
            $issues[] = Issue::fromArray($this->getClient(), $this, $issue);
        }

        return $issues;
    }

    /**
     * @param string $title
     * @param array $params
     * @return Issue
     */
    public function createIssue($title, array $params = array())
    {
        $params['title'] = $title;
        $data = $this->client->issues()->create($this->id, $params);

        return Issue::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param int $id
     * @return Issue
     */
    public function issue($id)
    {
        $issue = new Issue($this, $id, $this->getClient());

        return $issue->show();
    }

    /**
     * @param int $id
     * @param array $params
     * @return Issue
     */
    public function updateIssue($id, array $params)
    {
        $issue = new Issue($this, $id, $this->getClient());

        return $issue->update($params);
    }

    /**
     * @param int $id
     * @param string $comment
     * @return Issue
     */
    public function closeIssue($id, $comment = null)
    {
        $issue = new Issue($this, $id, $this->getClient());

        return $issue->close($comment);
    }

    /**
     * @param int $id
     * @return Issue
     */
    public function openIssue($id)
    {
        $issue = new Issue($this, $id, $this->getClient());

        return $issue->open();
    }

    /**
     * @param array $parameters
     *
     * @see Milestones::all() for available parameters.
     *
     * @return Milestone[]
     */
    public function milestones(array $parameters = [])
    {
        $data = $this->client->milestones()->all($this->id, $parameters);

        $milestones = array();
        foreach ($data as $milestone) {
            $milestones[] = Milestone::fromArray($this->getClient(), $this, $milestone);
        }

        return $milestones;
    }

    /**
     * @param string $title
     * @param array $params
     * @return Milestone
     */
    public function createMilestone($title, array $params = array())
    {
        $params['title'] = $title;
        $data = $this->client->milestones()->create($this->id, $params);

        return Milestone::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param int $id
     * @return Milestone
     */
    public function milestone($id)
    {
        $milestone = new Milestone($this, $id, $this->getClient());

        return $milestone->show();
    }

    /**
     * @param int $id
     * @param array $params
     * @return Milestone
     */
    public function updateMilestone($id, array $params)
    {
        $milestone = new Milestone($this, $id, $this->getClient());

        return $milestone->update($params);
    }

    /**
     * @param int $id
     * @return Issue[]
     */
    public function milestoneIssues($id)
    {
        $milestone = new Milestone($this, $id, $this->getClient());

        return $milestone->issues();
    }

    /**
     * @return Snippet[]
     */
    public function snippets()
    {
        $data = $this->client->snippets()->all($this->id);

        $snippets = array();
        foreach ($data as $snippet) {
            $snippets[] = Snippet::fromArray($this->getClient(), $this, $snippet);
        }

        return $snippets;
    }

    /**
     * @param string $title
     * @param string $filename
     * @param string $code
     * @return Snippet
     */
    public function createSnippet($title, $filename, $code)
    {
        $data = $this->client->snippets()->create($this->id, $title, $filename, $code);

        return Snippet::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param int $id
     * @return Snippet
     */
    public function snippet($id)
    {
        $snippet = new Snippet($this, $id, $this->getClient());

        return $snippet->show();
    }

    /**
     * @param int $id
     * @return string
     */
    public function snippetContent($id)
    {
        $snippet = new Snippet($this, $id, $this->getClient());

        return $snippet->content();
    }

    /**
     * @param int $id
     * @param array $params
     * @return Snippet
     */
    public function updateSnippet($id, array $params)
    {
        $snippet = new Snippet($this, $id, $this->getClient());

        return $snippet->update($params);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function removeSnippet($id)
    {
        $snippet = new Snippet($this, $id, $this->getClient());

        return $snippet->remove();
    }

    /**
     * @param int $group_id
     * @return Group
     */
    public function transfer($group_id)
    {
        $group = new Group($group_id, $this->getClient());

        return $group->transfer($this->id);
    }

    /**
     * @param int $id
     * @return Project
     */
    public function forkTo($id)
    {
        $data = $this->client->projects()->createForkRelation($id, $this->id);

        return Project::fromArray($this->getClient(), $data);
    }

    /**
     * @param int $id
     * @return Project
     */
    public function forkFrom($id)
    {
        return $this->createForkRelation($id);
    }

    /**
     * @param int $id
     * @return Project
     */
    public function createForkRelation($id)
    {
        $data = $this->client->projects()->createForkRelation($this->id, $id);

        return Project::fromArray($this->getClient(), $data);
    }

    /**
     * @return bool
     */
    public function removeForkRelation()
    {
        $this->client->projects()->removeForkRelation($this->id);

        return true;
    }

    /**
     * @param string $service_name
     * @param array $params
     * @return bool
     */
    public function setService($service_name, array $params = array())
    {
        $this->client->projects()->setService($this->id, $service_name, $params);

        return true;
    }

    /**
     * @param string $service_name
     * @return bool
     */
    public function removeService($service_name)
    {
        $this->client->projects()->removeService($this->id, $service_name);

        return true;
    }

    /**
     * @return Label[]
     */
    public function labels()
    {
        $data = $this->client->projects()->labels($this->id);

        $labels = array();
        foreach ($data as $label) {
            $labels[] = Label::fromArray($this->getClient(), $this, $label);
        }

        return $labels;
    }

    /**
     * @param string $name
     * @param string $color
     * @return Label
     */
    public function addLabel($name, $color)
    {
        $data = $this->client->projects()->addLabel($this->id, array(
            'name' => $name,
            'color' => $color
        ));

        return Label::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param string $name
     * @param array $params
     * @return Label
     */
    public function updateLabel($name, array $params)
    {
        if (isset($params['name'])) {
            $params['new_name'] = $params['name'];
        }

        $params['name'] = $name;

        $data = $this->client->projects()->updateLabel($this->id, $params);

        return Label::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function removeLabel($name)
    {
        $this->client->projects()->removeLabel($this->id, $name);

        return true;
    }

    /**
     * @return array
     */
    public function contributors()
    {
        $data = $this->client->repositories()->contributors($this->id);

        $contributors = array();
        foreach ($data as $contributor) {
            $contributors[] = Contributor::fromArray($this->getClient(), $this, $contributor);
        }

        return $contributors;
    }

    /**
     * @param array $scopes
     * @return Job[]
     */
    public function jobs(array $scopes = [])
    {
        $data = $this->client->jobs()->all($this->id, $scopes);

        $jobs = array();
        foreach ($data as $job) {
            $jobs[] = Job::fromArray($this->getClient(), $this, $job);
        }

        return $jobs;
    }

    /**
     * @param int $pipeline_id
     * @param array $scopes
     * @return Job[]
     */
    public function pipelineJobs($pipeline_id, array $scopes = [])
    {
        $data = $this->client->jobs()->pipelineJobs($this->id, $pipeline_id, $scopes);

        $jobs = array();
        foreach ($data as $job) {
            $jobs[] = Job::fromArray($this->getClient(), $this, $job);
        }

        return $jobs;
    }

    /**
     * @param int $job_id
     * @return Job
     */
    public function job($job_id)
    {
        $data = $this->client->jobs()->show($this->id, $job_id);

        return Job::fromArray($this->getClient(), $this, $data);
    }
}
