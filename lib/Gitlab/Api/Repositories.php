<?php

namespace Gitlab\Api;

class Repositories extends AbstractApi
{

    public function branches($project_id, $page = 1, $per_page = 20)
    {
        $path = 'projects/'.urlencode($project_id).'/repository/branches';
        return $this->get($path, array('page' => $page, 'per_page' => $per_page));
    }

    public function branch($project_id, $branch_id)
    {
        return $this->get('projects/'.urlencode($project_id).'/repository/branches/'.urlencode($branch_id));
    }

    public function tags($project_id, $page = 1, $per_page = 20)
    {
        $path = 'projects/'.urlencode($project_id).'/repository/tags';
        return $this->get($path, array('page' => $page, 'per_page' => $per_page));
    }

    public function commits($project_id, $page = 1, $per_page = 20)
    {
        $path = 'projects/'.urlencode($project_id).'/repository/commits';
        return $this->get($path, array('page' => $page, 'per_page' => $per_page));
    }

    public function protectBranch($project_id, $branch_id)
    {
        return $this->put('projects/'.urlencode($project_id).'/repository/branches/'.urlencode($branch_id).'/protect');
    }

    public function unprotectBranch($project_id, $branch_id)
    {
        return $this->put('projects/'.urlencode($project_id).'/repository/branches/'.urlencode($branch_id).'/unprotect');
    }

}
