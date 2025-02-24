<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\OptionsResolver;

class RepositoryFiles extends AbstractApi
{
    public function getFile(int|string $project_id, string $file_path, string $ref): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'repository/files/'.self::encodePath($file_path)), [
            'ref' => $ref,
        ]);
    }

    public function getRawFile(int|string $project_id, string $file_path, string $ref): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'repository/files/'.self::encodePath($file_path).'/raw'), [
            'ref' => $ref,
        ]);
    }

    /**
     * @param array      $parameters {
     *
     *     @var string $file_path      Url encoded full path to new file. Ex. lib%2Fclass%2Erb.
     *     @var string $branch         name of the branch
     *     @var string $start_branch   name of the branch to start the new commit from
     *     @var string $encoding       change encoding to 'base64' (default is text)
     *     @var string $author_email   specify the commit author's email address
     *     @var string $author_name    specify the commit author's name
     *     @var string $content        file content
     *     @var string $commit_message Commit message.
     * }
     */
    public function createFile(int|string $project_id, array $parameters = []): mixed
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

        return $this->post($this->getProjectPath($project_id, 'repository/files/'.self::encodePath($resolved['file_path'])), $resolved);
    }

    /**
     * @param array      $parameters {
     *
     *     @var string $file_path      Url encoded full path to new file. Ex. lib%2Fclass%2Erb.
     *     @var string $branch         name of the branch
     *     @var string $start_branch   name of the branch to start the new commit from
     *     @var string $encoding       change encoding to 'base64' (default is text)
     *     @var string $author_email   specify the commit author's email address
     *     @var string $author_name    specify the commit author's name
     *     @var string $content        file content
     *     @var string $commit_message commit message
     *     @var string $last_commit_id last known file commit id
     * }
     */
    public function updateFile(int|string $project_id, array $parameters = []): mixed
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

        return $this->put($this->getProjectPath($project_id, 'repository/files/'.self::encodePath($resolved['file_path'])), $resolved);
    }

    /**
     * @param array      $parameters {
     *
     *     @var string $file_path      Url encoded full path to new file. Ex. lib%2Fclass%2Erb.
     *     @var string $branch         name of the branch
     *     @var string $start_branch   name of the branch to start the new commit from
     *     @var string $author_email   specify the commit author's email address
     *     @var string $author_name    specify the commit author's name
     *     @var string $commit_message Commit message.
     * }
     */
    public function deleteFile(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('file_path');
        $resolver->setRequired('branch');
        $resolver->setDefined('start_branch');
        $resolver->setDefined('author_email');
        $resolver->setDefined('author_name');
        $resolver->setRequired('commit_message');

        $resolved = $resolver->resolve($parameters);

        return $this->delete($this->getProjectPath($project_id, 'repository/files/'.self::encodePath($resolved['file_path'])), $resolved);
    }
}
