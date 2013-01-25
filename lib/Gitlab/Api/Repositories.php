<?php

namespace Gitlab\Api;

class Repositories extends AbstractApi
{

    public function branches($project_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/repository/branches');
    }

    public function branch($project_id, $branch_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/repository/branches/'.urlencode($branch_id));
    }

    public function tags($project_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/repository/tags');
    }

    public function commits($project_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/repository/commits');
    }

    public function protectBranch($project_id, $branch_id)
    {
        return $this->post('projects/'.urlencode($project_id).'/repository/branches/'.urlencode($branch_id).'/protect');
    }

    public function unprotectBranch($project_id, $branch_id)
    {
        return $this->post('projects/'.urlencode($project_id).'/repository/branches/'.urlencode($branch_id).'/unprotect');
    }

}
