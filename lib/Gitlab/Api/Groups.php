<?php

namespace Gitlab\Api;

class Groups extends AbstractApi
{
    public function all($page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('groups', array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    public function show($id)
    {
        return $this->get('groups/'.urlencode($id));
    }

    public function create($name, $path)
    {
        return $this->post('groups', array(
            'name' => $name,
            'path' => $path
        ));
    }

    public function transfer($group_id, $project_id)
    {
        return $this->post('groups/'.urlencode($group_id).'/projects/'.urlencode($project_id));
    }

	public function members($id, $page = 1, $per_page = self::PER_PAGE)
	{
		return $this->get('groups/'.urlencode($id).'/members', array(
			'page=' => $page,
			'per_page' => $per_page
		));
	}

	public function addMember($group_id, $user_id, $access_level)
	{
		return $this->post('groups/'.urlencode($group_id).'/members', array(
			'user_id' => $user_id,
			'access_level' => $access_level
		));
	}

	public function removeMember($group_id, $user_id)
	{
		return $this->delete('groups/'.urlencode($group_id).'/members/'.urlencode($user_id));
	}
}
