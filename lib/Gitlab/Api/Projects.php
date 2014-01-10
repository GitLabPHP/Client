<?php

namespace Gitlab\Api;

class Projects extends AbstractApi
{
    public function all($page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('projects/all', array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }
    public function accessible($page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('projects', array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    public function owned($page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('projects/owned', array(
            'page' => $page,
            'per_page' => $per_page
        ));
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
    
    public function createForUser($user_id, $name, array $params = array())
    {
        $params['name'] = $name;

        return $this->post('projects/user/'.urlencode($user_id), $params);
    }

    public function remove($project_id)
    {
        return $this->delete('projects/'.urlencode($project_id));
    }

    public function members($project_id, $username_query = null)
    {
        return $this->get('projects/'.urlencode($project_id).'/members', array(
            'query' => $username_query
        ));
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
    
    public function addTeam($team_id, $project_id, $greatest_access_level)
    {
        return $this->post('user_teams/'.urlencode($team_id).'/projects', array(
            'project_id' => $project_id,
            'greatest_access_level' => $greatest_access_level
        ));
    }

    public function hooks($project_id, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('projects/'.urlencode($project_id).'/hooks', array(
            'page' => $page,
            'per_page' => $per_page
        ));
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
        return $this->delete('projects/'.urlencode($project_id).'/hooks/'.urlencode($hook_id));
    }

    public function keys($project_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/keys');
    }

    public function key($project_id, $key_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/keys/'.urlencode($key_id));
    }

    public function addKey($project_id, $title, $key)
    {
        return $this->post('projects/'.urlencode($project_id).'/keys', array(
            'title' => $title,
            'key' => $key
        ));
    }

    public function removeKey($project_id, $key_id)
    {
        return $this->delete('projects/'.urlencode($project_id).'/keys/'.urlencode($key_id));
    }

    public function events($project_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/events');
    }

    public function search($query)
    {
        return $this->get('projects/search/'.urlencode($query));
    }

    public function createForkRelation($project_id, $forked_project_id)
    {
        return $this->post('projects/'.urlencode($project_id).'/fork/'.urlencode($forked_project_id));
    }

    public function removeForkRelation($project_id)
    {
        return $this->delete('projects/'.urlencode($project_id).'/fork');
    }

    public function setService($project_id, $service_name, array $params = array())
    {
        return $this->put('projects/'.urlencode($project_id).'/services/'.urlencode($service_name), $params);
    }

    public function removeService($project_id, $service_name)
    {
        return $this->delete('projects/'.urlencode($project_id).'/services/'.urlencode($service_name));
    }

}
