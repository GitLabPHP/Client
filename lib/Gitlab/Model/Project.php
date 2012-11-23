<?php

namespace Gitlab\Model;

class Project extends AbstractModel
{
    protected static $_properties = array(
        'id',
        'code',
        'name',
        'description',
        'path',
        'default_branch',
        'owner',
        'private',
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
        return $this->api('projects')->hooks($this->id);
    }

    public function hook($hook_id)
    {
        return $this->api('projects')->hook($this->id, $hook_id);
    }

    public function addHook($url)
    {
       return $this->api('projects')->addHook($this->id, $url);
    }

    public function updateHook($hook_id, $url)
    {
       return $this->api('projects')->updateHook($this->id, $hook_id, $url);
    }

    public function removeHook($hook_id)
    {
        return $this->api('projects')->removeHook($this->id, $hook_id);
    }
}
