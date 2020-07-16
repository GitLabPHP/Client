<?php

declare(strict_types=1);

namespace Gitlab\Api;

use Psr\Http\Message\StreamInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Jobs extends AbstractApi
{
    public const SCOPE_CREATED = 'created';

    public const SCOPE_PENDING = 'pending';

    public const SCOPE_RUNNING = 'running';

    public const SCOPE_FAILED = 'failed';

    public const SCOPE_SUCCESS = 'success';

    public const SCOPE_CANCELED = 'canceled';

    public const SCOPE_SKIPPED = 'skipped';

    public const SCOPE_MANUAL = 'manual';

    /**
     * @param int|string $project_id
     * @param array      $parameters {
     *
     *     @var string|string[] $scope The scope of jobs to show, one or array of: created, pending, running, failed,
     *                                 success, canceled, skipped, manual; showing all jobs if none provided.
     * }
     *
     * @return mixed
     */
    public function all($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get('projects/'.self::encodePath($project_id).'/jobs', $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param int        $pipeline_id
     * @param array      $parameters  {
     *
     *     @var string|string[] $scope The scope of jobs to show, one or array of: created, pending, running, failed,
     *                                 success, canceled, skipped, manual; showing all jobs if none provided.
     * }
     *
     * @return mixed
     */
    public function pipelineJobs($project_id, int $pipeline_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get(
            $this->getProjectPath($project_id, 'pipelines/').self::encodePath($pipeline_id).'/jobs',
            $resolver->resolve($parameters)
        );
    }

    /**
     * @param int|string $project_id
     * @param int        $job_id
     *
     * @return mixed
     */
    public function show($project_id, int $job_id)
    {
        return $this->get('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id));
    }

    /**
     * @param int|string $project_id
     * @param int        $job_id
     *
     * @return StreamInterface
     */
    public function artifacts($project_id, int $job_id)
    {
        return $this->getAsResponse('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/artifacts')->getBody();
    }

    /**
     * @param int|string $project_id
     * @param string     $ref_name
     * @param string     $job_name
     *
     * @return StreamInterface
     */
    public function artifactsByRefName($project_id, string $ref_name, string $job_name)
    {
        return $this->getAsResponse('projects/'.self::encodePath($project_id).'/jobs/artifacts/'.self::encodePath($ref_name).'/download', [
            'job' => self::encodePath($job_name),
        ])->getBody();
    }

    /**
     * @param int|string $project_id
     * @param string     $ref_name
     * @param string     $job_name
     * @param string     $artifact_path
     *
     * @return StreamInterface
     */
    public function artifactByRefName($project_id, string $ref_name, string $job_name, string $artifact_path)
    {
        return $this->getAsResponse('projects/'.self::encodePath($project_id).'/jobs/artifacts/'.self::encodePath($ref_name).'/raw/'.self::encodePath($artifact_path), [
            'job' => self::encodePath($job_name),
        ])->getBody();
    }

    /**
     * @param int|string $project_id
     * @param int        $job_id
     *
     * @return string
     */
    public function trace($project_id, int $job_id)
    {
        return $this->get('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/trace');
    }

    /**
     * @param int|string $project_id
     * @param int        $job_id
     *
     * @return mixed
     */
    public function cancel($project_id, int $job_id)
    {
        return $this->post('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/cancel');
    }

    /**
     * @param int|string $project_id
     * @param int        $job_id
     *
     * @return mixed
     */
    public function retry($project_id, int $job_id)
    {
        return $this->post('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/retry');
    }

    /**
     * @param int|string $project_id
     * @param int        $job_id
     *
     * @return mixed
     */
    public function erase($project_id, int $job_id)
    {
        return $this->post('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/erase');
    }

    /**
     * @param int|string $project_id
     * @param int        $job_id
     *
     * @return mixed
     */
    public function keepArtifacts($project_id, int $job_id)
    {
        return $this->post('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/artifacts/keep');
    }

    /**
     * @param int|string $project_id
     * @param int        $job_id
     *
     * @return mixed
     */
    public function play($project_id, int $job_id)
    {
        return $this->post('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/play');
    }

    /**
     * {@inheritdoc}
     */
    protected function createOptionsResolver()
    {
        $allowedScopeValues = [
            self::SCOPE_CANCELED,
            self::SCOPE_CREATED,
            self::SCOPE_FAILED,
            self::SCOPE_MANUAL,
            self::SCOPE_PENDING,
            self::SCOPE_RUNNING,
            self::SCOPE_SKIPPED,
            self::SCOPE_SUCCESS,
        ];

        $resolver = parent::createOptionsResolver();
        $resolver->setDefined('scope')
            ->setAllowedTypes('scope', ['string', 'array'])
            ->setAllowedValues('scope', $allowedScopeValues)
            ->addAllowedValues('scope', function ($value) use ($allowedScopeValues) {
                return \is_array($value) && 0 === \count(\array_diff($value, $allowedScopeValues));
            })
            ->setNormalizer('scope', function (OptionsResolver $resolver, $value) {
                return (array) $value;
            })
        ;

        return $resolver;
    }
}
