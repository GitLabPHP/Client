<?php

namespace Gitlab\Api;

class Projects extends AbstractApi
{
    public function all($page = 1, $per_page = 20)
    {
        return $this->get('projects', array('page' => $page, 'per_page' => $per_page));
    }

    public function show($project_id)
    {
        return $this->get('projects/'.urlencode($project_id));
    }

    public function create($name, array $params = array())
    {
        $params['name'] = $name;

        return $this->post('projects', $params);
    }

    public function members($project_id, $page = 1, $per_page = 20)
    {
        $path = 'projects/'.urlencode($project_id).'/members';
        return $this->get($path, array('page' => $page, 'per_page' => $per_page));
    }

    public function member($project_id, $user_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/members/'.urlencode($user_id));
    }

    public function addMember($project_id, $user_id, $access_level)
    {
        return $this->post('projects/'.urlencode($project_id).'/members', array(
            'user_id' => $user_id,
            'access_level' => $access_level
        ));
    }

    public function saveMember($project_id, $user_id, $access_level)
    {
        return $this->put('projects/'.urlencode($project_id).'/members/'.urldecode($user_id), array(
            'access_level' => $access_level
        ));
    }

    public function removeMember($project_id, $user_id)
    {
        return $this->delete('projects/'.urlencode($project_id).'/members/'.urldecode($user_id));
    }

    public function hooks($project_id, $page = 1, $per_page = 20)
    {
        $path = 'projects/'.urlencode($project_id).'/hooks';
        return $this->get($path, array('page' => $page, 'per_page' => $per_page));
    }

    public function hook($project_id, $hook_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/hooks/'.urlencode($hook_id));
    }

    public function addHook($project_id, $url)
    {
        return $this->post('projects/'.urlencode($project_id).'/hooks', array(
            'url' => $url
        ));
    }

    public function updateHook($project_id, $hook_id, $url)
    {
        return $this->put('projects/'.urlencode($project_id).'/hooks/'.urlencode($hook_id), array(
            'url' => $url
        ));
    }

    public function removeHook($project_id, $hook_id)
    {
        return $this->delete('projects/'.urlencode($project_id).'/hooks', array(
            'hook_id' => $hook_id
        ));
    }

}
