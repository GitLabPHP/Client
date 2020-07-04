<?php

namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IssuesStatistics extends AbstractApi
{
    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function all($parameters)
    {
        return $this->get('issues_statistics', $this->createOptionsResolver()->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function project($project_id, $parameters)
    {
        return $this->get($this->getProjectPath($project_id, 'issues_statistics'), $this->createOptionsResolver()->resolve($parameters));
    }

    /**
     * @param int   $group_id
     * @param array $parameters
     *
     * @return mixed
     */
    public function group($group_id, $parameters)
    {
        return $this->get($this->getGroupPath($group_id, 'issues_statistics'), $this->createOptionsResolver()->resolve($parameters));
    }

    /**
     * @return OptionsResolver
     */
    protected function createOptionsResolver()
    {
        $resolver = new OptionsResolver();

        $resolver->setDefined('milestone')
            ->setAllowedTypes('milestone', 'string');

        $resolver->setDefined('labels')
            ->setAllowedTypes('labels', 'string');

        $resolver->setDefined('scope')
            ->setAllowedValues('scope', ['created-by-me', 'assigned-to-me', 'all']);

        $resolver->setDefined('author_id')
            ->setAllowedTypes('author_id', 'integer');

        $resolver->setDefined('author_username')
            ->setAllowedTypes('author_username', 'string');

        $resolver->setDefined('assignee_id')
            ->setAllowedTypes('assignee_id', 'integer');

        $resolver->setDefined('assignee_username')
            ->setAllowedTypes('assignee_username', 'string');

        $resolver->setDefined('my_reaction_emoji')
            ->setAllowedTypes('my_reaction_emoji', 'string');

        $resolver->setDefined('search')
            ->setAllowedTypes('search', 'string');

        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value) {
            return $value->format('c');
        };

        $resolver->setDefined('created_after')
            ->setAllowedTypes('created_after', \DateTimeInterface::class)
            ->setNormalizer('created_after', $datetimeNormalizer);

        $resolver->setDefined('created_before')
            ->setAllowedTypes('created_before', \DateTimeInterface::class)
            ->setNormalizer('created_before', $datetimeNormalizer);

        $resolver->setDefined('updated_after')
            ->setAllowedTypes('updated_after', \DateTimeInterface::class)
            ->setNormalizer('updated_after', $datetimeNormalizer);

        $resolver->setDefined('updated_before')
            ->setAllowedTypes('updated_before', \DateTimeInterface::class)
            ->setNormalizer('updated_before', $datetimeNormalizer);

        $booleanNormalizer = function (Options $resolver, $value) {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('confidential')
            ->setAllowedTypes('confidential', 'bool')
            ->setNormalizer('confidential', $booleanNormalizer);

        return $resolver;
    }
}
