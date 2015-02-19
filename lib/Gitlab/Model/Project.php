<?php namespace Gitlab\Model;

use Gitlab\Api\MergeRequests;
use Gitlab\Client;
use Gitlab\Api\AbstractApi as Api;

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
        'snippets_enabled'
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
        $data = $client->api('projects')->create($name, $params);

        return static::fromArray($client, $data);
    }

    /**
     * @param int $id
     * @param Client $client
     */
    public function __construct($id = null, Client $client = null)
    {
        $this->setClient($client);
        $this->id = $id;
    }

    /**
     * @return Project
     */
    public function show()
    {
        $data = $this->api('projects')->show($this->id);

        return static::fromArray($this->getClient(), $data);
    }

    /**
     * @return bool
     */
    public function remove()
    {
        $this->api('projects')->remove($this->id);

        return true;
    }

    /**
     * @param string $username_query
     * @return User[]
     */
    public function members($username_query = null)
    {
        $data = $this->api('projects')->members($this->id, $username_query);

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
        $data = $this->api('projects')->member($this->id, $user_id);

        return User::fromArray($this->getClient(), $data);
    }

    /**
     * @param int $user_id
     * @param int $access_level
     * @return User
     */
    public function addMember($user_id, $access_level)
    {
        $data = $this->api('projects')->addMember($this->id, $user_id, $access_level);

        return User::fromArray($this->getClient(), $data);
    }

    /**
     * @param int $user_id
     * @param int $access_level
     * @return User
     */
    public function saveMember($user_id, $access_level)
    {
        $data = $this->api('projects')->saveMember($this->id, $user_id, $access_level);

        return User::fromArray($this->getClient(), $data);
    }

    /**
     * @param int $user_id
     * @return bool
     */
    public function removeMember($user_id)
    {
        $this->api('projects')->removeMember($this->id, $user_id);

        return true;
    }

    /**
     * @param int $page
     * @param int $per_page
     * @return ProjectHook[]
     */
    public function hooks($page = 1, $per_page = Api::PER_PAGE)
    {
        $data = $this->api('projects')->hooks($this->id, $page, $per_page);

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
     * @param bool $push_events
     * @param bool $issues_events
     * @param bool $merge_requests_events
     * @return ProjectHook
     */
    public function addHook($url, $push_events = true, $issues_events = false, $merge_requests_events = false)
    {
        $data = $this->api('projects')->addHook($this->id, $url, $push_events, $issues_events, $merge_requests_events);

        return ProjectHook::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param int $hook_id
     * @param string $url
     * @param bool $push_events
     * @param bool $issues_events
     * @param bool $merge_requests_events
     * @param bool $tag_push_events
     * @return mixed
     */
    public function updateHook($hook_id, $url, $push_events = true, $issues_events = true, $merge_requests_events = true, $tag_push_events = true)
    {
        $hook = new ProjectHook($this, $hook_id, $this->getClient());

        return $hook->update(array(
            'url' => $url,
            'push_events' => $push_events,
            'issues_events' => $issues_events,
            'merge_requests_events' => $merge_requests_events,
            'tag_push_events' => $tag_push_events
        ));
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
    public function keys()
    {
        $data = $this->api('projects')->keys($this->id);

        $keys = array();
        foreach ($data as $key) {
            $hooks[] = Key::fromArray($this->getClient(), $key);
        }

        return $keys;
    }

    /**
     * @param int $key_id
     * @return Key
     */
    public function key($key_id)
    {
        $data = $this->api('projects')->key($this->id, $key_id);

        return Key::fromArray($this->getClient(), $data);
    }

    /**
     * @param string $title
     * @param string $key
     * @return Key
     */
    public function addKey($title, $key)
    {
        $data = $this->api('projects')->addKey($this->id, $title, $key);

        return Key::fromArray($this->getClient(), $data);
    }

    /**
     * @param string $key_id
     * @return bool
     */
    public function removeKey($key_id)
    {
        $this->api('projects')->removeKey($this->id, $key_id);

        return true;
    }

    /**
     * @param string $name
     * @param string $ref
     * @return Branch
     */
    public function createBranch($name, $ref)
    {
        $data = $this->api('repositories')->createBranch($this->id, $name, $ref);

        return Branch::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function deleteBranch($name)
    {
        $this->api('repositories')->deleteBranch($this->id, $name);

        return true;
    }

    /**
     * @return Branch[]
     */
    public function branches()
    {
        $data = $this->api('repo')->branches($this->id);

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
     * @return Branch
     */
    public function protectBranch($branch_name)
    {
        $branch = new Branch($this, $branch_name);
        $branch->setClient($this->getClient());

        return $branch->protect();
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
        $data = $this->api('repo')->tags($this->id);

        $tags = array();
        foreach ($data as $tag) {
            $tags[] = Tag::fromArray($this->getClient(), $this, $tag);
        }

        return $tags;
    }

    /**
     * @param int $page
     * @param int $per_page
     * @param string $ref_name
     * @return Commit[]
     */
    public function commits($page = 0, $per_page = Api::PER_PAGE, $ref_name = null)
    {
        $data = $this->api('repo')->commits($this->id, $page, $per_page, $ref_name);

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
        $data = $this->api('repo')->commit($this->id, $sha);

        return Commit::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param string $ref
     * @param int $page
     * @param int $per_page
     * @return Commit[]
     */
    public function commitComments($ref, $page = 0, $per_page = Api::PER_PAGE)
    {
        $data = $this->api('repo')->commitComments($this->id, $ref, $page, $per_page);

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
        $data = $this->api('repo')->createCommitComment($this->id, $ref, $note, $params);

        return CommitNote::fromArray($this->getClient(), $data);
    }

    /**
     * @param string $sha
     * @return mixed
     */
    public function diff($sha)
    {
        return $this->api('repo')->diff($this->id, $sha);
    }

    /**
     * @param array $params
     * @return Node[]
     */
    public function tree(array $params = array())
    {
        $data = $this->api('repo')->tree($this->id, $params);

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
        return $this->api('repo')->blob($this->id, $sha, $filepath);
    }

    /**
     * @param string $file_path
     * @param string $content
     * @param string $branch_name
     * @param string $commit_message
     * @return File
     */
    public function createFile($file_path, $content, $branch_name, $commit_message)
    {
        $data = $this->api('repo')->createFile($this->id, $file_path, $content, $branch_name, $commit_message);

        return File::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param string $file_path
     * @param string $content
     * @param string $branch_name
     * @param string $commit_message
     * @return File
     */
    public function updateFile($file_path, $content, $branch_name, $commit_message)
    {
        $data = $this->api('repo')->updateFile($this->id, $file_path, $content, $branch_name, $commit_message);

        return File::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param string $file_path
     * @param string $branch_name
     * @param string $commit_message
     * @return bool
     */
    public function deleteFile($file_path, $branch_name, $commit_message)
    {
        $this->api('repo')->deleteFile($this->id, $file_path, $branch_name, $commit_message);

        return true;
    }

    /**
     * @return Event[]
     */
    public function events()
    {
        $data = $this->api('projects')->events($this->id);

        $events = array();
        foreach ($data as $event) {
            $events[] = Event::fromArray($this->getClient(), $this, $event);
        }

        return $events;
    }

    /**
     * @param int    $page
     * @param int    $per_page
     * @param string $state
     * @return MergeRequest[]
     */
    public function mergeRequests($page = 1, $per_page = Api::PER_PAGE, $state = MergeRequests::STATE_ALL)
    {
        $data = $this->api('mr')->$state($this->id, $page, $per_page);

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
        $data = $this->api('mr')->create($this->id, $source, $target, $title, $assignee, null, $description);

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
     * @param int $page
     * @param int $per_page
     * @return Issue[]
     */
    public function issues($page = 1, $per_page = Api::PER_PAGE)
    {
        $data = $this->api('issues')->all($this->id, $page, $per_page);

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
        $data = $this->api('issues')->create($this->id, $params);

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
     * @param int $page
     * @param int $per_page
     * @return Milestone[]
     */
    public function milestones($page = 1, $per_page = Api::PER_PAGE)
    {
        $data = $this->api('milestones')->all($this->id, $page, $per_page);

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
        $data = $this->api('milestones')->create($this->id, $params);

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
     * @return Snippet[]
     */
    public function snippets()
    {
        $data = $this->api('snippets')->all($this->id);

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
     * @param string $lifetime
     * @return Snippet
     */
    public function createSnippet($title, $filename, $code, $lifetime = null)
    {
        $data = $this->api('snippets')->create($this->id, $title, $filename, $code, $lifetime);

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
     * @return Snippet
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
        $data = $this->api('projects')->createForkRelation($id, $this->id);

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
        $data = $this->api('projects')->createForkRelation($this->id, $id);

        return Project::fromArray($this->getClient(), $data);
    }

    /**
     * @return bool
     */
    public function removeForkRelation()
    {
        $this->api('projects')->removeForkRelation($this->id);

        return true;
    }

    /**
     * @param string $service_name
     * @param array $params
     * @return bool
     */
    public function setService($service_name, array $params = array())
    {
        $this->api('projects')->setService($this->id, $service_name, $params);

        return true;
    }

    /**
     * @param string $service_name
     * @return bool
     */
    public function removeService($service_name)
    {
        $this->api('projects')->removeService($this->id, $service_name);

        return true;
    }
}
