<?php

namespace Gitlab\Model;

use Gitlab\Client;
use Gitlab\Api\AbstractApi as Api;

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

    public static function fromArray(Client $client, array $data)
    {
        $project = new Project($data['id']);
        $project->setClient($client);

        if (isset($data['owner'])) {
            $data['owner'] = User::fromArray($client, $data['owner']);
        }

        return $project->hydrate($data);
    }

    public static function create(Client $client, $name, array $params = array())
    {
        $data = $client->api('projects')->create($name, $params);

        return Project::fromArray($client, $data);
    }

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function show()
    {
        $data = $this->api('projects')->show($this->id);

        return Project::fromArray($this->getClient(), $data);
    }

    public function members($username_query = null)
    {
        $data = $this->api('projects')->members($this->id, $username_query);

        $members = array();
        foreach ($data as $member) {
            $members[] = User::fromArray($this->getClient(), $member);
        }

        return $members;
    }

    public function member($user_id)
    {
        $data = $this->api('projects')->member($this->id, $user_id);

        return User::fromArray($this->getClient(), $data);
    }

    public function addMember($user_id, $access_level)
    {
        $data = $this->api('projects')->addMember($this->id, $user_id, $access_level);

        return User::fromArray($this->getClient(), $data);
    }

    public function saveMember($user_id, $access_level)
    {
        $data = $this->api('projects')->saveMember($this->id, $user_id, $access_level);

        return User::fromArray($this->getClient(), $data);
    }

    public function removeMember($user_id)
    {
        $this->api('projects')->removeMember($this->id, $user_id);

        return true;
    }

    public function hooks($page = 1, $per_page = Api::PER_PAGE)
    {
        $data = $this->api('projects')->hooks($this->id, $page, $per_page);

        $hooks = array();
        foreach ($data as $hook) {
            $hooks[] = Hook::fromArray($this->getClient(), $hook);
        }

        return $hooks;
    }

    public function hook($hook_id)
    {
        $data = $this->api('projects')->hook($this->id, $hook_id);

        return Hook::fromArray($this->getClient(), $data);
    }

    public function addHook($url)
    {
        $data = $this->api('projects')->addHook($this->id, $url);

        return Hook::fromArray($this->getClient(), $data);
    }

    public function updateHook($hook_id, $url)
    {
        $data = $this->api('projects')->updateHook($this->id, $hook_id, $url);

        return Hook::fromArray($this->getClient(), $data);
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
            $hooks[] = Key::fromArray($this->getClient(), $key);
        }

        return $keys;
    }

    public function key($key_id)
    {
        $data = $this->api('projects')->key($this->id, $key_id);

        return Key::fromArray($this->getClient(), $data);
    }

    public function addKey($title, $key)
    {
        $data = $this->api('projects')->addKey($this->id, $title, $key);

        return Key::fromArray($this->getClient(), $data);
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
            $branches[] = Branch::fromArray($this->getClient(), $this, $branch);
        }

        return $branches;
    }

    public function branch($branch_name)
    {
        $branch = new Branch($this, $branch_name);
        $branch->setClient($this->getClient());

        return $branch->show();
    }

    public function protectBranch($branch_name)
    {
        $branch = new Branch($this, $branch_name);
        $branch->setClient($this->getClient());

        return $branch->protect();
    }

    public function unprotectBranch($branch_name)
    {
        $branch = new Branch($this, $branch_name);
        $branch->setClient($this->getClient());

        return $branch->unprotect();
    }

    public function tags()
    {
        $data = $this->api('repo')->tags($this->id);

        $tags = array();
        foreach ($data as $tag) {
            $tags[] = Tag::fromArray($this->getClient(), $this, $tag);
        }

        return $tags;
    }

    public function commits($page = 1, $per_page = Api::PER_PAGE)
    {
        $data = $this->api('repo')->commits($this->id, $page, $per_page);

        $commits = array();
        foreach ($data as $commit) {
            $commits[] = Commit::fromArray($this->getClient(), $this, $commit);
        }

        return $commits;
    }

    public function mergeRequests($page = 1, $per_page = Api::PER_PAGE)
    {
        $data = $this->api('mr')->all($this->id, $page, $per_page);

        $mrs = array();
        foreach ($data as $mr) {
            $mrs[] = MergeRequest::fromArray($this->getClient(), $this, $mr);
        }

        return $mrs;
    }

    public function mergeRequest($id)
    {
        $mr = new MergeRequest($this, $id);
        $mr->setClient($this->getClient());

        return $mr->show();
    }

    public function createMergeRequest($source, $target, $title, $assignee = null)
    {
        $data = $this->api('mr')->create($this->id, $source, $target, $title, $assignee);

        return MergeRequest::fromArray($this->getClient(), $this, $data);
    }

    public function issues($page = 1, $per_page = Api::PER_PAGE)
    {
        $data = $this->api('issues')->all($this->id, $page, $per_page);

        $issues = array();
        foreach ($data as $issue) {
            $issues[] = Issue::fromArray($this->getClient(), $this, $issue);
        }

        return $issues;
    }

    public function createIssue($title, array $params = array())
    {
        $params['title'] = $title;
        $data = $this->api('issues')->create($this->id, $params);

        return Issue::fromArray($this->getClient(), $this, $data);
    }

    public function issue($id)
    {
        $issue = new Issue($this, $id);
        $issue->setClient($this->getClient());

        return $issue->show();
    }

    public function updateIssue($id, array $params)
    {
        $issue = new Issue($this, $id);
        $issue->setClient($this->getClient());

        return $issue->update($params);
    }

    public function removeIssue($id)
    {
        $issue = new Issue($this, $id);
        $issue->setClient($this->getClient());

        return $issue->remove();
    }

    public function milestones($page = 1, $per_page = Api::PER_PAGE)
    {
        $data = $this->api('milestones')->all($this->id, $page, $per_page);

        $milestones = array();
        foreach ($data as $milestone) {
            $milestones[] = Milestone::fromArray($this->getClient(), $this, $milestone);
        }

        return $milestones;
    }

    public function createMilestone($title, array $params = array())
    {
        $params['title'] = $title;
        $data = $this->api('milestones')->create($this->id, $params);

        return Milestone::fromArray($this->getClient(), $this, $data);
    }

    public function milestone($id)
    {
        $milestone = new Milestone($this, $id);
        $milestone->setClient($this->getClient());

        return $milestone->show();
    }

    public function updateMilestone($id, array $params)
    {
        $milestone = new Milestone($this, $id);
        $milestone->setClient($this->getClient());

        return $milestone->update($params);
    }
}
