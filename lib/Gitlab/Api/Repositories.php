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

    public function commits($project_id, $page = 1, $per_page = self::PER_PAGE, $ref_name = null)
    {
        return $this->get('projects/'.urlencode($project_id).'/repository/commits', array(
            'page' => $page,
            'per_page' => $per_page,
            'ref_name' => $ref_name
        ));
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
