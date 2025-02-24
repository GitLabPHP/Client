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

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Repositories extends AbstractApi
{
    /**
     * @var string
     */
    public const TYPE_BRANCH = 'branch';

    /**
     * @var string
     */
    public const TYPE_TAG = 'tag';

    /**
     * @param array      $parameters {
     *
     *     @var string $search
     * }
     */
    public function branches(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('search')
            ->setAllowedTypes('search', 'string');

        return $this->get($this->getProjectPath($project_id, 'repository/branches'), $resolver->resolve($parameters));
    }

    public function branch(int|string $project_id, string $branch): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'repository/branches/'.self::encodePath($branch)));
    }

    public function createBranch(int|string $project_id, string $branch, string $ref): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'repository/branches'), [
            'branch' => $branch,
            'ref' => $ref,
        ]);
    }

    public function deleteBranch(int|string $project_id, string $branch): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'repository/branches/'.self::encodePath($branch)));
    }

    public function protectBranch(int|string $project_id, string $branch, bool $devPush = false, bool $devMerge = false): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'repository/branches/'.self::encodePath($branch).'/protect'), [
            'developers_can_push' => $devPush,
            'developers_can_merge' => $devMerge,
        ]);
    }

    public function unprotectBranch(int|string $project_id, string $branch): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'repository/branches/'.self::encodePath($branch).'/unprotect'));
    }

    public function tags(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('search')
            ->setAllowedTypes('search', 'string');

        return $this->get($this->getProjectPath($project_id, 'repository/tags'), $resolver->resolve($parameters));
    }

    public function createTag(int|string $project_id, string $name, string $ref, ?string $message = null): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'repository/tags'), [
            'tag_name' => $name,
            'ref' => $ref,
            'message' => $message,
        ]);
    }

    public function createRelease(int|string $project_id, string $tag_name, string $description, ?string $name = null): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'releases'), \array_filter([
            'id' => $project_id,
            'tag_name' => $tag_name,
            'description' => $description,
            'name' => $name,
        ], fn ($v) => null !== $v));
    }

    public function updateRelease(int|string $project_id, string $tag_name, string $description, ?string $name = null): mixed
    {
        return $this->put($this->getProjectPath($project_id, 'releases/'.self::encodePath($tag_name)), \array_filter([
            'id' => $project_id,
            'tag_name' => $tag_name,
            'description' => $description,
            'name' => $name,
        ], fn ($v) => null !== $v));
    }

    public function releases(int|string $project_id): mixed
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'releases'));
    }

    /**
     * @see https://docs.gitlab.com/ee/api/commits.html#list-repository-commits
     *
     * @param array      $parameters {
     *
     *     @var string             $ref_name the name of a repository branch or tag or if not given the default branch
     *     @var \DateTimeInterface $since    only commits after or on this date will be returned
     *     @var \DateTimeInterface $until    Only commits before or on this date will be returned.
     * }
     */
    public function commits(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $options, \DateTimeInterface $value): string {
            return $value->format('c');
        };
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('path');
        $resolver->setDefined('ref_name');
        $resolver->setDefined('author');
        $resolver->setDefined('since')
            ->setAllowedTypes('since', \DateTimeInterface::class)
            ->setNormalizer('since', $datetimeNormalizer)
        ;
        $resolver->setDefined('until')
            ->setAllowedTypes('until', \DateTimeInterface::class)
            ->setNormalizer('until', $datetimeNormalizer)
        ;
        $resolver->setDefined('all')
            ->setAllowedTypes('all', 'bool')
            ->setNormalizer('all', $booleanNormalizer)
        ;
        $resolver->setDefined('with_stats')
            ->setAllowedTypes('with_stats', 'bool')
            ->setNormalizer('with_stats', $booleanNormalizer)
        ;
        $resolver->setDefined('first_parent')
            ->setAllowedTypes('first_parent', 'bool')
            ->setNormalizer('first_parent', $booleanNormalizer)
        ;
        $resolver->setDefined('order')
            ->setAllowedValues('order', ['default', 'topo'])
        ;

        return $this->get($this->getProjectPath($project_id, 'repository/commits'), $resolver->resolve($parameters));
    }

    public function commit(int|string $project_id, string $sha): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/'.self::encodePath($sha)));
    }

    public function commitRefs(int|string $project_id, string $sha, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        return $this->get(
            $this->getProjectPath($project_id, 'repository/commits/'.self::encodePath($sha).'/refs'),
            $resolver->resolve($parameters)
        );
    }

    /**
     * @param array      $parameters {
     *
     *     @var string $branch         Name of the branch to commit into. To create a new branch, also provide start_branch.
     *     @var string $commit_message commit message
     *     @var string $start_branch   name of the branch to start the new commit from
     *     @var array $actions {
     *         @var string $action        he action to perform, create, delete, move, update
     *         @var string $file_path     full path to the file
     *         @var string $previous_path original full path to the file being moved
     *         @var string $content       File content, required for all except delete. Optional for move.
     *         @var string $encoding      text or base64. text is default.
     *     }
     *     @var string $author_email   specify the commit author's email address
     *     @var string $author_name    Specify the commit author's name.
     * }
     */
    public function createCommit(int|string $project_id, array $parameters = []): mixed
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
                return 0 < \count($actions);
            })
            ->setNormalizer('actions', function (Options $resolver, array $actions) {
                $actionsOptionsResolver = new OptionsResolver();
                $actionsOptionsResolver->setDefined('action')
                    ->setRequired('action')
                    ->setAllowedValues('action', ['create', 'delete', 'move', 'update', 'chmod'])
                ;
                $actionsOptionsResolver->setDefined('file_path')
                    ->setRequired('file_path')
                ;
                $actionsOptionsResolver->setDefined('previous_path');
                $actionsOptionsResolver->setDefined('content');
                $actionsOptionsResolver->setDefined('encoding')
                    ->setAllowedValues('encoding', ['text', 'base64'])
                ;
                $actionsOptionsResolver->setDefined('execute_filemode')
                    ->setAllowedValues('execute_filemode', [true, false])
                ;

                return \array_map(function ($action) use ($actionsOptionsResolver) {
                    return $actionsOptionsResolver->resolve($action);
                }, $actions);
            })
        ;
        $resolver->setDefined('author_email');
        $resolver->setDefined('author_name');

        return $this->post($this->getProjectPath($project_id, 'repository/commits'), $resolver->resolve($parameters));
    }

    public function revertCommit(int|string $project_id, string $branch, string $sha): mixed
    {
        return $this->post($this->getProjectPath($project_id, 'repository/commits/'.self::encodePath($sha).'/revert'), [
            'branch' => $branch,
        ]);
    }

    public function commitComments(int|string $project_id, string $sha, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        return $this->get(
            $this->getProjectPath($project_id, 'repository/commits/'.self::encodePath($sha).'/comments'),
            $resolver->resolve($parameters)
        );
    }

    public function createCommitComment(int|string $project_id, string $sha, string $note, array $params = []): mixed
    {
        $params['note'] = $note;

        return $this->post($this->getProjectPath($project_id, 'repository/commits/'.self::encodePath($sha).'/comments'), $params);
    }

    public function getCommitBuildStatus(int|string $project_id, string $sha, array $params = []): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/'.self::encodePath($sha).'/statuses'), $params);
    }

    public function postCommitBuildStatus(int|string $project_id, string $sha, string $state, array $params = []): mixed
    {
        $params['state'] = $state;

        return $this->post($this->getProjectPath($project_id, 'statuses/'.self::encodePath($sha)), $params);
    }

    public function compare(int|string $project_id, string $fromShaOrMaster, string $toShaOrMaster, bool $straight = false, ?string $fromProjectId = null): mixed
    {
        $params = [
            'from' => $fromShaOrMaster,
            'to' => $toShaOrMaster,
            'straight' => $straight ? 'true' : 'false',
        ];

        if (null !== $fromProjectId) {
            $params['from_project_id'] = self::encodePath($fromProjectId);
        }

        return $this->get($this->getProjectPath($project_id, 'repository/compare'), $params);
    }

    public function diff(int|string $project_id, string $sha): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/'.self::encodePath($sha).'/diff'));
    }

    public function tree(int|string $project_id, array $params = []): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'repository/tree'), $params);
    }

    public function contributors(int|string $project_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'repository/contributors'));
    }

    /**
     * @param string     $format     Options: "tar.gz", "zip", "tar.bz2" and "tar"
     */
    public function archive(int|string $project_id, array $params = [], string $format = 'tar.gz'): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'repository/archive.'.$format), $params);
    }

    public function mergeBase(int|string $project_id, array $refs): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'repository/merge_base'), ['refs' => $refs]);
    }

    public function cherryPick(int|string $project_id, string $sha, array $params = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('branch')
            ->setRequired('branch');

        $resolver->setDefined('dry_run')
            ->setAllowedTypes('dry_run', 'bool')
            ->setNormalizer('dry_run', $booleanNormalizer);

        return $this->post($this->getProjectPath($project_id, 'repository/commits/'.self::encodePath($sha).'/cherry_pick'), $params);
    }

    protected function createOptionsResolver(): OptionsResolver
    {
        $allowedTypeValues = [
            self::TYPE_BRANCH,
            self::TYPE_TAG,
        ];

        $resolver = parent::createOptionsResolver();
        $resolver->setDefined('type')
            ->setAllowedTypes('type', 'string')
            ->setAllowedValues('type', $allowedTypeValues);

        return $resolver;
    }
}
