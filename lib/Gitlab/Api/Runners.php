<?php namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\Options;

class Runners extends AbstractApi
{
    /**
     * @param array $parameters {
     *
     *     @var string $type                  The type of runners to show, one of: instance_type, group_type,
     *                                              project_type
     *     @var string $status                The status of runners to show, one of: active, paused, online,
     *                                              offline
     * }
     *
     * @throws UndefinedOptionsException If an option name is undefined
     * @throws InvalidOptionsException   If an option doesn't fulfill the
     *                                   specified validation rules
     *
     * @return mixed
     */
    public function owned(array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        $resolver->setDefined('type')
            ->setAllowedValues('type', ['instance_type', 'group_type', 'project_type'])
        ;
        $resolver->setDefined('status')
            ->setAllowedValues('status', ['active', 'paused', 'online', 'offline'])
        ;

        return $this->get('runners', $resolver->resolve($parameters));
    }

    /**
     * @param array $parameters {
     *
     *     @var string $type                  The type of runners to show, one of: instance_type, group_type,
     *                                              project_type
     *     @var string $status                The status of runners to show, one of: active, paused, online,
     *                                              offline
     * }
     *
     * @throws UndefinedOptionsException If an option name is undefined
     * @throws InvalidOptionsException   If an option doesn't fulfill the
     *                                   specified validation rules
     *
     * @return mixed
     */
    public function all(array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        $resolver->setDefined('type')
            ->setAllowedValues('type', ['instance_type', 'group_type', 'project_type'])
        ;
        $resolver->setDefined('status')
            ->setAllowedValues('status', ['active', 'paused', 'online', 'offline'])
        ;

        return $this->get('runners/all', $resolver->resolve($parameters));
    }

    /**
     * @param int|string $runner_id
     * @param array $parameters {
     *
     *     @var bool   $statistics                    Include project statistics.
     *     @var bool   $with_custom_attributes        Include project custom attributes.
     * }
     * @return mixed
     */
    public function details($runner_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get('runners/'.$this->encodePath($runner_id), $resolver->resolve($parameters));
    }

    /**
     * @param int   $runner_id
     * @param array $parameters (
     *
     *     @var string $status      Status of the job; one of: running, success, failed, canceled
     * )
     * @return mixed
     */
    public function jobs($runner_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        $resolver->setDefined('status')
            ->setAllowedValues('status', ['running', 'success', 'failed', 'canceled'])
        ;

        return $this->get($this->getRunnerPath($runner_id, 'jobs'), $resolver->resolve($parameters));
    }

    /**
     * @param int $runner_id
     * @return mixed
     */
    public function remove($runner_id)
    {
        return $this->delete('runners/'.$this->encodePath($runner_id));
    }

    /**
     * @param int $id
     * @param array $parameters
     * @return mixed
     */
    public function update($id, array $parameters)
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value) {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('description');

        $resolver->setDefined('active')
            ->setAllowedTypes('active', 'bool')
            ->setNormalizer('active', $booleanNormalizer);

        $resolver->setDefined('tag_list')
            ->setAllowedTypes('tag_list', 'array');

        $resolver->setDefined('run_untagged')
            ->setAllowedTypes('run_untagged', 'bool')
            ->setNormalizer('run_untagged', $booleanNormalizer);

        $resolver->setDefined('locked')
            ->setAllowedTypes('locked', 'bool')
            ->setNormalizer('locked', $booleanNormalizer);

        $resolver->setDefined('access_level')
            ->setAllowedValues('access_level', ['not_protected', 'ref_protected']);

        $resolver->setDefined('maximum_timeout')
            ->setAllowedTypes('maximum_timeout', 'integer');

        return $this->put('runners/'.$this->encodePath($id), $resolver->resolve($parameters));
    }

}