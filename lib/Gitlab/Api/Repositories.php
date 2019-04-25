<?php namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Repositories extends AbstractApi
{
    const TYPE_BRANCH = 'branch';
    const TYPE_TAG = 'tag';

    /**
     * @param int $project_id
     * @param array $parameters (
     *
     *     @var string $search
     * )
     * @return mixed
     */
    public function branches($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('search')
            ->setAllowedTypes('search', 'string');

        return $this->get($this->getProjectPath($project_id, 'repository/branches'), $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param int $branch_id
     * @return mixed
     */
    public function branch($project_id, $branch_id)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/branches/'.$this->encodePath($branch_id)));
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
        return $this->delete($this->getProjectPath($project_id, 'repository/branches/'.$this->encodePath($branch)));
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
        return $this->put($this->getProjectPath($project_id, 'repository/branches/'.$this->encodePath($branch_name).'/protect'), array(
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
        return $this->put($this->getProjectPath($project_id, 'repository/branches/'.$this->encodePath($branch_name).'/unprotect'));
    }

    /**
     * @param int $project_id
     * @param array $parameters
     * @return mixed
     */
    public function tags($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'repository/tags'), $resolver->resolve($parameters));
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
     * @param string $tag_name
     * @param string $description
     * @return mixed
     */
    public function createRelease($project_id, $tag_name, $description)
    {
        return $this->post($this->getProjectPath($project_id, 'repository/tags/' . $this->encodePath($tag_name) . '/release'), array(
            'id'          => $project_id,
            'tag_name'    => $tag_name,
            'description' => $description
        ));
    }

    /**
     * @param int $project_id
     * @param string $tag_name
     * @param string $description
     * @return mixed
     */
    public function updateRelease($project_id, $tag_name, $description)
    {
        return $this->put($this->getProjectPath($project_id, 'repository/tags/' . $this->encodePath($tag_name) . '/release'), array(
            'id'          => $project_id,
            'tag_name'    => $tag_name,
            'description' => $description
        ));
    }

    /**
     * @param int $project_id
     * @return mixed
     */
    public function releases($project_id)
    {
        $resolver = $this->createOptionsResolver();
        return $this->get($this->getProjectPath($project_id, 'releases'));
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
        $datetimeNormalizer = function (Options $options, \DateTimeInterface $value) {
            return $value->format('c');
        };

        $resolver->setDefined('path');
        $resolver->setDefined('ref_name');
        $resolver->setDefined('since')
            ->setAllowedTypes('since', \DateTimeInterface::class)
            ->setNormalizer('since', $datetimeNormalizer)
        ;
        $resolver->setDefined('until')
            ->setAllowedTypes('until', \DateTimeInterface::class)
            ->setNormalizer('until', $datetimeNormalizer)
        ;
        $resolver->setDefined('all');
        $resolver->setDefined('with_stats');

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
     * @param int $project_id
     * @param $sha
     * @param array $parameters
     * @return mixed
     */
    public function commitRefs($project_id, $sha, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get(
            $this->getProjectPath($project_id, 'repository/commits/' . $this->encodePath($sha) . '/refs'),
            $resolver->resolve($parameters)
        );
    }

    /**
     * @param int $project_id
     * @param array $parameters (
     *
     *     @var string $branch         Name of the branch to commit into. To create a new branch, also provide start_branch.
     *     @var string $commit_message Commit message.
     *     @var string $start_branch   Name of the branch to start the new commit from.
     *     @var array $actions (
     *
     *         @var string $action        he action to perform, create, delete, move, update.
     *         @var string $file_path     Full path to the file.
     *         @var string $previous_path Original full path to the file being moved.
     *         @var string $content       File content, required for all except delete. Optional for move.
     *         @var string $encoding      text or base64. text is default.
     *     )
     *     @var string $author_email   Specify the commit author's email address.
     *     @var string $author_name    Specify the commit author's name.
     * )
     *
     * @return mixed
     */
    public function createCommit($project_id, array $parameters = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('branch')
            ->setRequired('branch')
        ;
        $resolver->setDefined('commit_message')
            ->setRequired('commit_message')
        ;
        $resolver->setDefined('start_branch');
        $resolver->setDefined('actions')
            ->setRequired('actions')
            ->setAllowedTypes('actions', 'array')
            ->setAllowedValues('actions', function (array $actions) {
                return !empty($actions);
            })
            ->setNormalizer('actions', function (Options $resolver, array $actions) {
                $actionsOptionsResolver = new OptionsResolver();
                $actionsOptionsResolver->setDefined('action')
                    ->setRequired('action')
                    ->setAllowedValues('action', ['create', 'delete', 'move', 'update'])
                ;
                $actionsOptionsResolver->setDefined('file_path')
                    ->setRequired('file_path')
                ;
                $actionsOptionsResolver->setDefined('previous_path');
                $actionsOptionsResolver->setDefined('content');
                $actionsOptionsResolver->setDefined('encoding')
                    ->setAllowedValues('encoding', ['test', 'base64'])
                ;

                return array_map(function ($action) use ($actionsOptionsResolver) {
                    return $actionsOptionsResolver->resolve($action);
                }, $actions);
            })
        ;
        $resolver->setDefined('author_email');
        $resolver->setDefined('author_name');

        return $this->post($this->getProjectPath($project_id, 'repository/commits'), $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param string $sha
     * @param array $parameters
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
     * @param string $sha
     * @param array $params
     * @return mixed
     */
    public function getCommitBuildStatus($project_id, $sha, array $params = array())
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/'.$this->encodePath($sha).'/statuses'), $params);
    }

    /**
     * @param int $project_id
     * @param string $sha
     * @param string $state
     * @param array $params
     * @return mixed
     */
    public function postCommitBuildStatus($project_id, $sha, $state, array $params = array())
    {
        $params['state'] = $state;

        return $this->post($this->getProjectPath($project_id, 'statuses/'.$this->encodePath($sha)), $params);
    }

    /**
     * @param int $project_id
     * @param string $fromShaOrMaster
     * @param string $toShaOrMaster
     * @param bool $straight
     * @return mixed
     */
    public function compare($project_id, $fromShaOrMaster, $toShaOrMaster, $straight = false)
    {
        return $this->get($this->getProjectPath(
            $project_id,
            'repository/compare?from='.$this->encodePath($fromShaOrMaster).'&to='.$this->encodePath($toShaOrMaster).'&straight='.$this->encodePath($straight ? 'true' : 'false')
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
        @trigger_error(sprintf('The %s() method is deprecated since version 9.2 and will be removed in 10.0. Use the %s::getRawFile() method instead.', __METHOD__, RepositoryFiles::class), E_USER_DEPRECATED);

        return $this->client->repositoryFiles()->getRawFile($project_id, $filepath, $sha);
    }

    /**
     * @param int $project_id
     * @param string $file_path
     * @param string $ref
     * @return mixed
     */
    public function getFile($project_id, $file_path, $ref)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.2 and will be removed in 10.0. Use the %s::getFile() method instead.', __METHOD__, RepositoryFiles::class), E_USER_DEPRECATED);

        return $this->client->repositoryFiles()->getFile($project_id, $file_path, $ref);
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
        @trigger_error(sprintf('The %s() method is deprecated since version 9.2 and will be removed in 10.0. Use the %s::createFile() method instead.', __METHOD__, RepositoryFiles::class), E_USER_DEPRECATED);

        return $this->client->repositoryFiles()->createFile($project_id, [
            'file_path' => $file_path,
            'branch' => $branch,
            'content' => $content,
            'commit_message' => $commit_message,
            'encoding' => $encoding,
            'author_email' => $author_email,
            'author_name' => $author_name,
        ]);
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
        @trigger_error(sprintf('The %s() method is deprecated since version 9.2 and will be removed in 10.0. Use the %s::updateFile() method instead.', __METHOD__, RepositoryFiles::class), E_USER_DEPRECATED);

        return $this->client->repositoryFiles()->updateFile($project_id, [
            'file_path' => $file_path,
            'branch' => $branch,
            'content' => $content,
            'commit_message' => $commit_message,
            'encoding' => $encoding,
            'author_email' => $author_email,
            'author_name' => $author_name,
        ]);
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
        @trigger_error(sprintf('The %s() method is deprecated since version 9.2 and will be removed in 10.0. Use the %s::deleteFile() method instead.', __METHOD__, RepositoryFiles::class), E_USER_DEPRECATED);

        return $this->client->repositoryFiles()->deleteFile($project_id, [
            'file_path' => $file_path,
            'branch' => $branch,
            'commit_message' => $commit_message,
            'author_email' => $author_email,
            'author_name' => $author_name,
        ]);
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
     * @param int $project_id
     * @param array $refs
     * @return mixed
     */
    public function mergeBase($project_id, $refs)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/merge_base'), array('refs' => $refs));
    }

    protected function createOptionsResolver()
    {
        $allowedTypeValues = [
            self::TYPE_BRANCH,
            self::TYPE_TAG
        ];

        $resolver = parent::createOptionsResolver();
        $resolver->setDefined('type')
            ->setAllowedTypes('type', 'string')
            ->setAllowedValues('type', $allowedTypeValues);

        return $resolver;
    }
}
