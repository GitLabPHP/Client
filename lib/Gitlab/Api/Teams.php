<?php

namespace Gitlab\Api;

class Teams extends AbstractApi
{
    public function all()
    {
        return $this->get('user_teams');
    }

    public function show($team_id)
    {
        return $this->get('user_teams/'.urlencode($team_id));
    }

    public function create($name, $path)
    {
        return $this->post('user_teams', array(
            'name' => $name,
            'path' => $path
        ));
    }

    public function members($team_id)
    {
        return $this->get('user_teams/'.urlencode($team_id).'/members');
    }

    public function member($team_id, $user_id)
    {
        return $this->get('user_teams/'.urlencode($team_id).'/members/'.urlencode($user_id));
    }

    public function addMember($team_id, $user_id, $access_level)
    {
        return $this->post('user_teams/'.urlencode($team_id).'/members', array(
            'user_id' => $user_id,
            'access_level' => $access_level
        ));
    }

    public function removeMember($team_id, $user_id)
    {
        return $this->delete('user_teams/'.urlencode($team_id).'/members/'.urlencode($user_id));
    }

    public function projects($team_id)
    {
        return $this->get('user_teams/'.urlencode($team_id).'/projects');
    }

    public function project($team_id, $project_id)
    {
        return $this->get('user_teams/'.urlencode($team_id).'/projects/'.urlencode($project_id));
    }

    public function addProject($team_id, $project_id, $greatest_access_level )
    {
        return $this->post('user_teams/'.urlencode($team_id).'/projects', array(
            'project_id' => $project_id,
            'greatest_access_level' => $greatest_access_level
        ));
    }

    public function removeProject($team_id, $project_id)
    {
        return $this->delete('user_teams/'.urlencode($team_id).'/projects/'.urlencode($project_id));
    }
}