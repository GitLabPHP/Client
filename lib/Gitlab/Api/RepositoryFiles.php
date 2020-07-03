<?php

namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\OptionsResolver;

class RepositoryFiles extends AbstractApi
{
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
     * @param string $ref
     * @return mixed
     */
    public function getRawFile($project_id, $file_path, $ref)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/files/'.$this->encodePath($file_path).'/raw'), array(
            'ref' => $ref,
        ));
    }

    /**
     * @param int   $project_id
     * @param array $parameters {
     *
     *     @var string $file_path      Url encoded full path to new file. Ex. lib%2Fclass%2Erb.
     *     @var string $branch         Name of the branch.
     *     @var string $start_branch   Name of the branch to start the new commit from.
     *     @var string $encoding       Change encoding to 'base64'. Default is text.
     *     @var string $author_email   Specify the commit author's email address.
     *     @var string $author_name    Specify the commit author's name.
     *     @var string $content        File content.
     *     @var string $commit_message Commit message.
     * }
     *
     * @return mixed
     */
    public function createFile($project_id, array $parameters = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('file_path');
        $resolver->setRequired('branch');
        $resolver->setDefined('start_branch');
        $resolver->setDefined('encoding')
            ->setAllowedValues('encoding', ['text', 'base64'])
        ;
        $resolver->setDefined('author_email');
        $resolver->setDefined('author_name');
        $resolver->setRequired('content');
        $resolver->setRequired('commit_message');

        $resolved = $resolver->resolve($parameters);

        return $this->post($this->getProjectPath($project_id, 'repository/files/'.$this->encodePath($resolved['file_path'])), $resolved);
    }

    /**
     * @param int   $project_id
     * @param array $parameters {
     *
     *     @var string $file_path      Url encoded full path to new file. Ex. lib%2Fclass%2Erb.
     *     @var string $branch         Name of the branch.
     *     @var string $start_branch   Name of the branch to start the new commit from.
     *     @var string $encoding       Change encoding to 'base64'. Default is text.
     *     @var string $author_email   Specify the commit author's email address.
     *     @var string $author_name    Specify the commit author's name.
     *     @var string $content        File content.
     *     @var string $commit_message Commit message.
     *     @var string $last_commit_id Last known file commit id.
     * }
     *
     * @return mixed
     */
    public function updateFile($project_id, array $parameters = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('file_path');
        $resolver->setRequired('branch');
        $resolver->setDefined('start_branch');
        $resolver->setDefined('encoding')
            ->setAllowedValues('encoding', ['text', 'base64'])
        ;
        $resolver->setDefined('author_email');
        $resolver->setDefined('author_name');
        $resolver->setRequired('content');
        $resolver->setRequired('commit_message');
        $resolver->setDefined('last_commit_id');

        $resolved = $resolver->resolve($parameters);

        return $this->put($this->getProjectPath($project_id, 'repository/files/'.$this->encodePath($resolved['file_path'])), $resolved);
    }

    /**
     * @param int   $project_id
     * @param array $parameters {
     *
     *     @var string $file_path      Url encoded full path to new file. Ex. lib%2Fclass%2Erb.
     *     @var string $branch         Name of the branch.
     *     @var string $start_branch   Name of the branch to start the new commit from.
     *     @var string $author_email   Specify the commit author's email address.
     *     @var string $author_name    Specify the commit author's name.
     *     @var string $commit_message Commit message.
     * }
     *
     * @return mixed
     */
    public function deleteFile($project_id, array $parameters = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('file_path');
        $resolver->setRequired('branch');
        $resolver->setDefined('start_branch');
        $resolver->setDefined('author_email');
        $resolver->setDefined('author_name');
        $resolver->setRequired('commit_message');

        $resolved = $resolver->resolve($parameters);

        return $this->delete($this->getProjectPath($project_id, 'repository/files/'.$this->encodePath($resolved['file_path'])), $resolved);
    }
}
