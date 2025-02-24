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

    public function show(int|string $project_id, int $job_id): mixed
    {
        return $this->get('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id));
    }

    public function artifacts(int|string $project_id, int $job_id): StreamInterface
    {
        return $this->getAsResponse('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/artifacts')->getBody();
    }

    public function artifactsByRefName(int|string $project_id, string $ref_name, string $job_name): StreamInterface
    {
        return $this->getAsResponse('projects/'.self::encodePath($project_id).'/jobs/artifacts/'.self::encodePath($ref_name).'/download', [
            'job' => $job_name,
        ])->getBody();
    }

    public function artifactByRefName(int|string $project_id, string $ref_name, string $job_name, string $artifact_path): StreamInterface
    {
        return $this->getAsResponse('projects/'.self::encodePath($project_id).'/jobs/artifacts/'.self::encodePath($ref_name).'/raw/'.self::encodePath($artifact_path), [
            'job' => $job_name,
        ])->getBody();
    }

    public function artifactByJobId(int|string $project_id, int $job_id, string $artifact_path): StreamInterface
    {
        return $this->getAsResponse('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/artifacts/'.self::encodePath($artifact_path))->getBody();
    }

    public function trace(int|string $project_id, int $job_id): mixed
    {
        return $this->get('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/trace');
    }

    public function cancel(int|string $project_id, int $job_id): mixed
    {
        return $this->post('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/cancel');
    }

    public function retry(int|string $project_id, int $job_id): mixed
    {
        return $this->post('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/retry');
    }

    public function erase(int|string $project_id, int $job_id): mixed
    {
        return $this->post('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/erase');
    }

    public function keepArtifacts(int|string $project_id, int $job_id): mixed
    {
        return $this->post('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/artifacts/keep');
    }

    public function play(int|string $project_id, int $job_id): mixed
    {
        return $this->post('projects/'.self::encodePath($project_id).'/jobs/'.self::encodePath($job_id).'/play');
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
