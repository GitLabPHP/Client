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

class Registry extends AbstractApi
{
    /**
     * @param array $parameters {
     *
     *     @var bool $tags       include an array of tags in the response
     *     @var bool $tags_count include tags_count in the response
     *     @var bool $size       include the deduplicated size in the response
     * }
     */
    public function repository(int|string $repository_id, array $parameters = []): mixed
    {
        $resolver = self::createRepositoryResolver();

        return $this->get('registry/repositories/'.self::encodePath($repository_id), $resolver->resolve($parameters));
    }

    public function removeRepository(int|string $project_id, int $repository_id): mixed
    {
        return $this->delete($this->getProjectPath($project_id, 'registry/repositories/'.self::encodePath($repository_id)));
    }

    public function repositoryTags(int|string $project_id, int $repository_id): mixed
    {
        return $this->get($this->getProjectPath($project_id, 'registry/repositories/'.self::encodePath($repository_id).'/tags'));
    }

    public function repositoryTag(int|string $project_id, int $repository_id, string $tag_name): mixed
    {
        return $this->get($this->getProjectPath(
            $project_id,
            'registry/repositories/'.self::encodePath($repository_id).'/tags/'.self::encodePath($tag_name)
        ));
    }

    public function removeRepositoryTag(int|string $project_id, int $repository_id, string $tag_name): mixed
    {
        return $this->delete($this->getProjectPath(
            $project_id,
            'registry/repositories/'.self::encodePath($repository_id).'/tags/'.self::encodePath($tag_name)
        ));
    }

    /**
     * @param array $parameters {
     *
     *     @var string $name_regex_delete regex of tag names to delete
     *     @var string $name_regex_keep   regex of tag names to keep
     *     @var int    $keep_n            number of latest matching tags to keep
     *     @var string $older_than        delete tags older than this human-readable duration
     * }
     */
    public function removeRepositoryTags(int|string $project_id, int $repository_id, array $parameters): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('name_regex_delete')
            ->setAllowedTypes('name_regex_delete', 'string')
        ;
        $resolver->setDefined('name_regex_keep')
            ->setAllowedTypes('name_regex_keep', 'string')
        ;
        $resolver->setDefined('keep_n')
            ->setAllowedTypes('keep_n', 'int')
        ;
        $resolver->setDefined('older_than')
            ->setAllowedTypes('older_than', 'string')
        ;

        return $this->delete(
            $this->getProjectPath($project_id, 'registry/repositories/'.self::encodePath($repository_id).'/tags'),
            $resolver->resolve($parameters)
        );
    }

    private static function createRepositoryResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('tags')
            ->setAllowedTypes('tags', 'bool')
            ->setNormalizer('tags', $booleanNormalizer)
        ;
        $resolver->setDefined('tags_count')
            ->setAllowedTypes('tags_count', 'bool')
            ->setNormalizer('tags_count', $booleanNormalizer)
        ;
        $resolver->setDefined('size')
            ->setAllowedTypes('size', 'bool')
            ->setNormalizer('size', $booleanNormalizer)
        ;

        return $resolver;
    }
}
