<?php

namespace Gitlab\Api;

class Branches extends AbstractApi
{
    public function all($projectId)
    {
        return $this->get(sprintf("/projects/%s/repository/branches", urlencode($projectId)));
    }

    public function show($projectId, $branchName)
    {
        return $this->get(sprintf("/projects/%s/repository/branches/%s", urlencode($projectId), urlencode($branchName)));
    }

    public function protect($projectId, $branchName)
    {
        return $this->put(sprintf("/projects/%s/repository/branches/%s/protect", urlencode($projectId), urlencode($branchName)));
    }

    public function unprotect($projectId, $branchName)
    {
        return $this->put(sprintf("/projects/%s/repository/branches/%s/unprotect", urlencode($projectId), urlencode($branchName)));
    }

    public function create($projectId, $branchName, $ref)
    {
        $params = array('branch_name' => $branchName, 'ref' => $ref);

        return $this->post(sprintf("/projects/%s/repository/branches", urlencode($projectId)), $params);
    }
}