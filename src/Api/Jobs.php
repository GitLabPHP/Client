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

use Psr\Http\Message\StreamInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Jobs extends AbstractApi
{
    /**
     * @var string
     */
    public const SCOPE_CREATED = 'created';

    /**
     * @var string
     */
    public const SCOPE_PENDING = 'pending';

    /**
     * @var string
     */
    public const SCOPE_RUNNING = 'running';

    /**
     * @var string
     */
    public const SCOPE_FAILED = 'failed';

    /**
     * @var string
     */
    public const SCOPE_SUCCESS = 'success';

    /**
     * @var string
     */
    public const SCOPE_CANCELED = 'canceled';

    /**
     * @var string
     */
    public const SCOPE_SKIPPED = 'skipped';

    /**
     * @var string
     */
    public const SCOPE_MANUAL = 'manual';

    /**
     * @see https://docs.gitlab.com/api/jobs/#list-all-jobs-for-a-project
     *
     * @param array      $parameters {
     *
     *     @var string|string[] $scope The scope of jobs to show, one or array of: created, pending, running, failed,
     *                                 success, canceled, skipped, manual; showing all jobs if none provided.
     * }
     */
    public function all(int|string $project_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        return $this->get('projects/'.self::encodePath($project_id).'/jobs', $resolver->resolve($parameters));
    }

    /**
     * @see https://docs.gitlab.com/api/jobs/#list-all-jobs-by-pipeline
     *
     * @param array      $parameters  {
     *
     *     @var string|string[] $scope The scope of jobs to show, one or array of: created, pending, running, failed,
     *                                 success, canceled, skipped, manual; showing all jobs if none provided.
     * }
     */
    public function pipelineJobs(int|string $project_id, int $pipeline_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        return $this->get(
            $this->getProjectPath($project_id, 'pipelines/').self::encodePath($pipeline_id).'/jobs',
            $resolver->resolve($parameters)
        );
    }

    /**
     * @see https://docs.gitlab.com/api/jobs/#list-all-trigger-jobs-by-pipeline
     *
     * @param array      $parameters  {
     *
     *     @var string|string[] $scope            The scope of bridge jobs to show, one or array of: created, pending, running, failed,
     *                                            success, canceled, skipped, manual; showing all jobs if none provided
     *     @var bool            $include_retried  Include retried jobs in the response. Defaults to false. Introduced in GitLab 13.9.
     * }
     */
    public function pipelineBridges(int|string $project_id, int $pipeline_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        return $this->get(
            $this->getProjectPath($project_id, 'pipelines/').self::encodePath($pipeline_id).'/bridges',
            $resolver->resolve($parameters)
        );
    }

    /**
     * @see https://docs.gitlab.com/api/jobs/#retrieve-a-job-by-job-id
     */
    public function show(int|string $project_id, int $job_id): mixed
    {
        return $this->get('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id));
    }

    /**
     * @see https://docs.gitlab.com/api/job_artifacts/#download-job-artifacts-by-job-id
     */
    public function artifacts(int|string $project_id, int $job_id): StreamInterface
    {
        return $this->getAsResponse('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/artifacts')->getBody();
    }

    /**
     * @see https://docs.gitlab.com/api/job_artifacts/#download-job-artifacts-by-reference-name
     */
    public function artifactsByRefName(int|string $project_id, string $ref_name, string $job_name): StreamInterface
    {
        return $this->getAsResponse('projects/'.self::encodePath($project_id).'/jobs/artifacts/'.self::encodePath($ref_name).'/download', [
            'job' => $job_name,
        ])->getBody();
    }

    /**
     * @see https://docs.gitlab.com/api/job_artifacts/#download-a-single-artifact-file-by-reference-name
     */
    public function artifactByRefName(int|string $project_id, string $ref_name, string $job_name, string $artifact_path): StreamInterface
    {
        return $this->getAsResponse('projects/'.self::encodePath($project_id).'/jobs/artifacts/'.self::encodePath($ref_name).'/raw/'.self::encodePath($artifact_path), [
            'job' => $job_name,
        ])->getBody();
    }

    /**
     * @see https://docs.gitlab.com/api/job_artifacts/#download-a-single-artifact-file-by-job-id
     */
    public function artifactByJobId(int|string $project_id, int $job_id, string $artifact_path): StreamInterface
    {
        return $this->getAsResponse('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/artifacts/'.self::encodePath($artifact_path))->getBody();
    }

    /**
     * @see https://docs.gitlab.com/api/jobs/#retrieve-a-log-file-for-a-job
     */
    public function trace(int|string $project_id, int $job_id): mixed
    {
        return $this->get('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/trace');
    }

    /**
     * @see https://docs.gitlab.com/api/jobs/#cancel-a-job
     */
    public function cancel(int|string $project_id, int $job_id): mixed
    {
        return $this->post('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/cancel');
    }

    /**
     * @see https://docs.gitlab.com/api/jobs/#retry-a-job
     */
    public function retry(int|string $project_id, int $job_id): mixed
    {
        return $this->post('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/retry');
    }

    /**
     * @see https://docs.gitlab.com/api/jobs/#erase-a-job
     */
    public function erase(int|string $project_id, int $job_id): mixed
    {
        return $this->post('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/erase');
    }

    /**
     * @see https://docs.gitlab.com/api/job_artifacts/#keep-job-artifacts
     */
    public function keepArtifacts(int|string $project_id, int $job_id): mixed
    {
        return $this->post('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/artifacts/keep');
    }

    /**
     * @see https://docs.gitlab.com/api/jobs/#run-a-job
     *
     * @param array $parameters {
     *
     *     @var array $job_inputs               job input values to use when playing the job
     *     @var array $job_variables_attributes custom variables available to the job
     * }
     */
    public function play(int|string $project_id, int $job_id, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('job_inputs')
            ->setAllowedTypes('job_inputs', 'array')
        ;
        $resolver->setDefined('job_variables_attributes')
            ->setAllowedTypes('job_variables_attributes', 'array')
            ->setAllowedValues('job_variables_attributes', function (array $variables): bool {
                foreach ($variables as $variable) {
                    if (!\is_array($variable) || !isset($variable['key'], $variable['value'])) {
                        return false;
                    }

                    if (!\is_string($variable['key']) || !\is_string($variable['value'])) {
                        return false;
                    }
                }

                return true;
            })
        ;

        return $this->post('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/play', $resolver->resolve($parameters));
    }

    protected function createOptionsResolver(): OptionsResolver
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

        $resolver->setDefined('include_retried')
            ->setAllowedTypes('include_retried', ['bool']);

        return $resolver;
    }
}
