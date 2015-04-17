<?php namespace Gitlab\Api;

class Repositories extends AbstractApi
{
    /**
     * @param int $project_id
     * @return mixed
     */
    public function branches($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/branches'));
    }

    /**
     * @param int $project_id
     * @param int $branch_id
     * @return mixed
     */
    public function branch($project_id, $branch_id)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/branches/'.rawurlencode($branch_id)));
    }

    /**
     * @param int $project_id
     * @param string $branch_name
     * @param string $ref
     * @return mixed
     */
    public function createBranch($project_id, $branch_name, $ref)
    {
        return $this->post($this->getProjectPath($project_id, 'repository/branches'), array(
            'branch_name' => $branch_name,
            'ref' => $ref
        ));
    }

    /**
     * @param int $project_id
     * @param string $branch_name
     * @return mixed
     */
    public function deleteBranch($project_id, $branch_name)
    {
        return $this->delete($this->getProjectPath($project_id, 'repository/branches/'.rawurlencode($branch_name)));
    }

    /**
     * @param int $project_id
     * @param string $branch_name
     * @return mixed
     */
    public function protectBranch($project_id, $branch_name)
    {
        return $this->put($this->getProjectPath($project_id, 'repository/branches/'.rawurlencode($branch_name).'/protect'));
    }

    /**
     * @param int $project_id
     * @param string $branch_name
     * @return mixed
     */
    public function unprotectBranch($project_id, $branch_name)
    {
        return $this->put($this->getProjectPath($project_id, 'repository/branches/'.rawurlencode($branch_name).'/unprotect'));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function tags($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/tags'));
    }

    /**
     * @param int $project_id
     * @param string $name
     * @param string $ref
     * @param string $message
     * @return mixed
     */
    public function createTag($project_id, $name, $ref, $message = null)
    {
        return $this->post($this->getProjectPath($project_id, 'repository/tags'), array(
            'tag_name' => $name,
            'ref' => $ref,
            'message' => $message
        ));
    }

    /**
     * @param int $project_id
     * @param int $page
     * @param int $per_page
     * @param null $ref_name
     * @return mixed
     */
    public function commits($project_id, $page = 0, $per_page = self::PER_PAGE, $ref_name = null)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits'), array(
            'page' => $page,
            'per_page' => $per_page,
            'ref_name' => $ref_name
        ));
    }

    /**
     * @param int $project_id
     * @param $sha
     * @return mixed
     */
    public function commit($project_id, $sha)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/'.rawurlencode($sha)));
    }

    /**
     * @param int $project_id
     * @param string $sha
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function commitComments($project_id, $sha, $page = 0, $per_page = self::PER_PAGE)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/'.rawurlencode($sha).'/comments'), array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    /**
     * @param int $project_id
     * @param string $sha
     * @param string $note
     * @param array $params
     * @return mixed
     */
    public function createCommitComment($project_id, $sha, $note, array $params = array())
    {
        $params['note'] = $note;

        return $this->post($this->getProjectPath($project_id, 'repository/commits/'.rawurlencode($sha).'/comments'), $params);
    }

    /**
     * @param int $project_id
     * @param string $fromShaOrMaster
     * @param string $toShaOrMaster
     * @return mixed
     */
    public function compare($project_id, $fromShaOrMaster, $toShaOrMaster)
    {
        return $this->get($this->getProjectPath(
            $project_id,
            'repository/compare?from='.rawurlencode($fromShaOrMaster).'&to='.rawurlencode($toShaOrMaster)
        ));
    }

    /**
     * @param int $project_id
     * @param string $sha
     * @return string
     */
    public function diff($project_id, $sha)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/'.rawurlencode($sha).'/diff'));
    }

    /**
     * @param int $project_id
     * @param array $params
     * @return mixed
     */
    public function tree($project_id, array $params = array())
    {
        return $this->get($this->getProjectPath($project_id, 'repository/tree'), $params);
    }

    /**
     * @param int $project_id
     * @param string $sha
     * @param string $filepath
     * @return mixed
     */
    public function blob($project_id, $sha, $filepath)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/'.rawurlencode($sha).'/blob'), array(
            'filepath' => $filepath
        ));
    }

    /**
     * @param int $project_id
     * @param string $file_path
     * @param string $ref
     * @return mixed
     */
    public function getFile($project_id, $file_path, $ref)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/files'), array(
            'file_path' => $file_path,
            'ref' => $ref
        ));
    }

    /**
     * @param int $project_id
     * @param string $file_path
     * @param string $content
     * @param string $branch_name
     * @param string $commit_message
     * @param string $encoding
     * @return mixed
     */
    public function createFile($project_id, $file_path, $content, $branch_name, $commit_message, $encoding = null)
    {
        return $this->post($this->getProjectPath($project_id, 'repository/files'), array(
            'file_path' => $file_path,
            'branch_name' => $branch_name,
            'content' => $content,
            'commit_message' => $commit_message,
            'encoding' => $encoding
        ));
    }

    /**
     * @param int $project_id
     * @param string $file_path
     * @param string $content
     * @param string $branch_name
     * @param string $commit_message
     * @param string $encoding
     * @return mixed
     */
    public function updateFile($project_id, $file_path, $content, $branch_name, $commit_message, $encoding = null)
    {
        return $this->put($this->getProjectPath($project_id, 'repository/files'), array(
            'file_path' => $file_path,
            'branch_name' => $branch_name,
            'content' => $content,
            'commit_message' => $commit_message,
            'encoding' => $encoding
        ));
    }

    /**
     * @param int $project_id
     * @param string $file_path
     * @param string $branch_name
     * @param string $commit_message
     * @return mixed
     */
    public function deleteFile($project_id, $file_path, $branch_name, $commit_message)
    {
        return $this->delete($this->getProjectPath($project_id, 'repository/files'), array(
            'file_path' => $file_path,
            'branch_name' => $branch_name,
            'commit_message' => $commit_message
        ));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function contributors($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/contributors'));
    }
}
