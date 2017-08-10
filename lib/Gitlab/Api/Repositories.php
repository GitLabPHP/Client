<?php namespace Gitlab\Api;

class Repositories extends AbstractApi
{
    /**
     * @param int $project_id
     * @param array $parameters
     * @return mixed
     */
    public function branches($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('query');

        return $this->get($this->getProjectPath($project_id, 'repository/branches'), $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param int $branch_id
     * @return mixed
     */
    public function branch($project_id, $branch_id)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/branches/'.$this->encodeBranch($branch_id)));
    }

    /**
     * @param int $project_id
     * @param string $branch
     * @param string $ref
     * @return mixed
     */
    public function createBranch($project_id, $branch, $ref)
    {
        return $this->post($this->getProjectPath($project_id, 'repository/branches'), array(
            'branch' => $branch,
            'ref' => $ref
        ));
    }

    /**
     * @param int $project_id
     * @param string $branch
     * @return mixed
     */
    public function deleteBranch($project_id, $branch)
    {
        return $this->delete($this->getProjectPath($project_id, 'repository/branches/'.$this->encodeBranch($branch)));
    }

    /**
     * @param int $project_id
     * @param string $branch_name
     * @param bool $devPush
     * @param bool $devMerge
     * @return mixed
     */
    public function protectBranch($project_id, $branch_name, $devPush = false, $devMerge = false)
    {
        return $this->put($this->getProjectPath($project_id, 'repository/branches/'.$this->encodeBranch($branch_name).'/protect'), array(
            'developers_can_push' => $devPush,
            'developers_can_merge' => $devMerge
        ));
    }

    /**
     * @param int $project_id
     * @param string $branch_name
     * @return mixed
     */
    public function unprotectBranch($project_id, $branch_name)
    {
        return $this->put($this->getProjectPath($project_id, 'repository/branches/'.$this->encodeBranch($branch_name).'/unprotect'));
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
     * @param int    $project_id
     * @param string $tag_name
     * @param string $description
     *
     * @return mixed
     */
    public function createRelease($project_id, $tag_name, $description)
    {
        return $this->post($this->getProjectPath($project_id, 'repository/tags/' . $this->encodeBranch($tag_name) . '/release'), array(
            'id'          => $project_id,
            'tag_name'    => $tag_name,
            'description' => $description
        ));
    }

    /**
     * @param int    $project_id
     * @param string $tag_name
     * @param string $description
     *
     * @return mixed
     */
    public function updateRelease($project_id, $tag_name, $description)
    {
        return $this->put($this->getProjectPath($project_id, 'repository/tags/' . $this->encodeBranch($tag_name) . '/release'), array(
            'id'          => $project_id,
            'tag_name'    => $tag_name,
            'description' => $description
        ));
    }

    /**
     * @param int $project_id
     * @param array $parameters (
     *
     *     @var string             $ref_name The name of a repository branch or tag or if not given the default branch.
     *     @var \DateTimeInterface $since    Only commits after or on this date will be returned.
     *     @var \DateTimeInterface $until    Only commits before or on this date will be returned.
     * )
     *
     * @return mixed
     */
    public function commits($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (\DateTimeInterface $value) {
            return $value->format('c');
        };

        $resolver->setDefined('ref_name');
        $resolver->setDefined('since')
            ->setAllowedTypes('since', \DateTimeInterface::class)
            ->setNormalizer('since', $datetimeNormalizer)
        ;
        $resolver->setDefined('until')
            ->setAllowedTypes('until', \DateTimeInterface::class)
            ->setNormalizer('until', $datetimeNormalizer)
        ;

        return $this->get($this->getProjectPath($project_id, 'repository/commits'), $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param $sha
     * @return mixed
     */
    public function commit($project_id, $sha)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/'.$this->encodePath($sha)));
    }

    /**
     * @param int    $project_id
     * @param string $sha
     * @param array  $parameters
     *
     * @return mixed
     */
    public function commitComments($project_id, $sha, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get(
            $this->getProjectPath($project_id, 'repository/commits/'.$this->encodePath($sha).'/comments'),
            $resolver->resolve($parameters)
        );
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

        return $this->post($this->getProjectPath($project_id, 'repository/commits/'.$this->encodePath($sha).'/comments'), $params);
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
            'repository/compare?from='.$this->encodeBranch($fromShaOrMaster).'&to='.$this->encodeBranch($toShaOrMaster)
        ));
    }

    /**
     * @param int $project_id
     * @param string $sha
     * @return string
     */
    public function diff($project_id, $sha)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/'.$this->encodePath($sha).'/diff'));
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
        return $this->get($this->getProjectPath($project_id, 'repository/files/'.$this->encodePath($filepath).'/raw'), array(
            'ref' => $sha,
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
        return $this->get($this->getProjectPath($project_id, 'repository/files/'.$this->encodePath($file_path)), array(
            'ref' => $ref
        ));
    }

    /**
     * @param int $project_id
     * @param string $file_path
     * @param string $content
     * @param string $branch
     * @param string $commit_message
     * @param string $encoding
     * @param string $author_email
     * @param string $author_name
     * @return mixed
     */
    public function createFile($project_id, $file_path, $content, $branch, $commit_message, $encoding = null, $author_email = null, $author_name = null)
    {
        return $this->post($this->getProjectPath($project_id, 'repository/files'), array(
            'file_path' => $file_path,
            'branch' => $branch,
            'content' => $content,
            'commit_message' => $commit_message,
            'encoding' => $encoding,
            'author_email' => $author_email,
            'author_name' => $author_name,
        ));
    }

    /**
     * @param int $project_id
     * @param string $file_path
     * @param string $content
     * @param string $branch
     * @param string $commit_message
     * @param string $encoding
     * @param string $author_email
     * @param string $author_name
     * @return mixed
     */
    public function updateFile($project_id, $file_path, $content, $branch, $commit_message, $encoding = null, $author_email = null, $author_name = null)
    {
        return $this->put($this->getProjectPath($project_id, 'repository/files'), array(
            'file_path' => $file_path,
            'branch' => $branch,
            'content' => $content,
            'commit_message' => $commit_message,
            'encoding' => $encoding,
            'author_email' => $author_email,
            'author_name' => $author_name,
        ));
    }

    /**
     * @param int $project_id
     * @param string $file_path
     * @param string $branch
     * @param string $commit_message
     * @param string $author_email
     * @param string $author_name
     * @return mixed
     */
    public function deleteFile($project_id, $file_path, $branch, $commit_message, $author_email = null, $author_name = null)
    {
        return $this->delete($this->getProjectPath($project_id, 'repository/files'), array(
            'file_path' => $file_path,
            'branch' => $branch,
            'commit_message' => $commit_message,
            'author_email' => $author_email,
            'author_name' => $author_name,
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

    /**
     * @param int $project_id
     * @param array $params
     * @param string $format Options: "tar.gz", "zip", "tar.bz2" and "tar"
     * @return mixed
     */
    public function archive($project_id, $params = array(), $format = 'tar.gz')
    {
        return $this->get($this->getProjectPath($project_id, 'repository/archive.'.$format), $params);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function encodeBranch($path)
    {
        $path = $this->encodePath($path);

        return str_replace('%2F', '/', $path);
    }
}
