<?php

namespace Gitlab\Model;

class Project extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'code',
        'name',
        'namespace',
        'description',
        'path',
        'path_with_namespace',
        'default_branch',
        'owner',
        'private',
        'public',
        'issues_enabled',
        'merge_requests_enabled',
        'wall_enabled',
        'wiki_enabled',
        'created_at'
    );

    public static function fromArray(array $data)
    {
        $project = new Project($data['id']);

        if (isset($data['owner'])) {
            $data['owner'] = User::fromArray($data['owner']);
        }

        return $project->hydrate($data);
    }

    public static function create($name, array $params = array())
    {
        $data = static::client()->api('projects')->create($name, $params);

        return Project::fromArray($data);
    }

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function show()
    {
        $data = $this->api('projects')->show($this->id);

        return Project::fromArray($data);
    }

    public function members()
    {
        $data = $this->api('projects')->members($this->id);

        $members = array();
        foreach ($data as $member) {
            $members[] = User::fromArray($member);
        }

        return $members;
    }

    public function member($user_id)
    {
        $data = $this->api('projects')->member($this->id, $user_id);

        return User::fromArray($data);
    }

    public function addMember($user_id, $access_level)
    {
        $data = $this->api('projects')->addMember($this->id, $user_id, $access_level);

        return User::fromArray($data);
    }

    public function saveMember($user_id, $access_level)
    {
        $data = $this->api('projects')->saveMember($this->id, $user_id, $access_level);

        return User::fromArray($data);
    }

    public function removeMember($user_id)
    {
        $this->api('projects')->removeMember($this->id, $user_id);

        return true;
    }

    public function hooks()
    {
        $data = $this->api('projects')->hooks($this->id);

        $hooks = array();
        foreach ($data as $hook) {
            $hooks[] = Hook::fromArray($hook);
        }

        return $hooks;
    }

    public function hook($hook_id)
    {
        $data = $this->api('projects')->hook($this->id, $hook_id);

        return Hook::fromArray($data);
    }

    public function addHook($url)
    {
        $data = $this->api('projects')->addHook($this->id, $url);

        return Hook::fromArray($data);
    }

    public function updateHook($hook_id, $url)
    {
        $data = $this->api('projects')->updateHook($this->id, $hook_id, $url);

        return Hook::fromArray($data);
    }

    public function removeHook($hook_id)
    {
        $this->api('projects')->removeHook($this->id, $hook_id);

        return true;
    }

    public function keys()
    {
        $data = $this->api('projects')->keys($this->id);

        $keys = array();
        foreach ($data as $key) {
            $hooks[] = Key::fromArray($key);
        }

        return $keys;
    }

    public function key($key_id)
    {
        $data = $this->api('projects')->key($this->id, $key_id);

        return Key::fromArray($data);
    }

    public function addKey($title, $key)
    {
        $data = $this->api('projects')->addKey($this->id, $title, $key);

        return Key::fromArray($data);
    }

    public function removeKey($key_id)
    {
        $this->api('projects')->removeKey($this->id, $key_id);

        return true;
    }

    public function branches()
    {
        $data = $this->api('repo')->branches($this->id);

        $branches = array();
        foreach ($data as $branch) {
            $branches[] = Branch::fromArray($this, $branch);
        }

        return $branches;
    }

    public function branch($branch_name)
    {
        $branch = new Branch($this, $branch_name);

        return $branch->show();
    }

    public function protectBranch($branch_name)
    {
        $branch = new Branch($this, $branch_name);

        return $branch->protect();
    }

    public function unprotectBranch($branch_name)
    {
        $branch = new Branch($this, $branch_name);

        return $branch->unprotect();
    }

    public function tags()
    {
        $data = $this->api('repo')->tags($this->id);

        $tags = array();
        foreach ($data as $tag) {
            $tags[] = Tag::fromArray($this, $tag);
        }

        return $tags;
    }

    public function commits()
    {
        $data = $this->api('repo')->commits($this->id);

        $commits = array();
        foreach ($data as $commit) {
            $commits[] = Commit::fromArray($this, $commit);
        }

        return $commits;
    }

    public function mergeRequests()
    {
        $data = $this->api('mr')->all($this->id);

        $mrs = array();
        foreach ($data as $mr) {
            $mrs[] = MergeRequest::fromArray($this, $mr);
        }

        return $mrs;
    }

    public function mergeRequest($id)
    {
        $mr = new MergeRequest($this, $id);

        return $mr->show();
    }

    public function createMergeRequest($source, $target, $title, $assignee = null)
    {
        $data = $this->api('mr')->create($this->id, $source, $target, $title, $assignee);

        return MergeRequest::fromArray($this, $data);
    }

    public function issues()
    {
        $data = $this->api('issues')->all($this->id);

        $issues = array();
        foreach ($data as $issue) {
            $issues[] = Issue::fromArray($this, $issue);
        }

        return $issues;
    }

    public function createIssue($title, array $params = array())
    {
        $params['title'] = $title;
        $data = $this->api('issues')->create($this->id, $params);

        return Issue::fromArray($this, $data);
    }

    public function issue($id)
    {
        $issue = new Issue($this, $id);

        return $issue->show();
    }

    public function updateIssue($id, array $params)
    {
        $issue = new Issue($this, $id);

        return $issue->update($params);
    }

    public function removeIssue($id)
    {
        $issue = new Issue($this, $id);

        return $issue->remove();
    }

    public function createMilestone($title, array $params = array())
    {
        $params['title'] = $title;
        $data = $this->api('milestones')->create($this->id, $params);

        return Milestone::fromArray($this, $data);
    }

    public function milestone($id)
    {
        $milestone = new Milestone($this, $id);

        return $milestone->show();
    }

    public function updateMilestone($id, array $params)
    {
        $milestone = new Milestone($this, $id);

        return $milestone->update($params);
    }
}
