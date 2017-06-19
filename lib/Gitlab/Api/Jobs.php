<?php namespace Gitlab\Api;

class Jobs extends AbstractApi
{
    const SCOPE_CREATED = 'created';
    const SCOPE_PENDING = 'pending';
    const SCOPE_RUNNING = 'running';
    const SCOPE_FAILED = 'failed';
    const SCOPE_SUCCESS = 'success';
    const SCOPE_CANCELED = 'canceled';
    const SCOPE_SKIPPED = 'skipped';
    const SCOPE_MANUAL = 'manual';

    /**
     * @param int|string $project_id
     * @param array $scope
     * @return mixed
     */
    public function jobs($project_id, array $scope = [])
    {
        return $this->get("projects/".$this->encodePath($project_id)."/jobs", array(
            'scope' => $scope
        ));
    }

    /**
     * @param int|string $project_id
     * @param int $pipeline_id
     * @param array $scope
     * @return mixed
     */
    public function pipelineJobs($project_id, $pipeline_id, array $scope = [])
    {
        return $this->get("projects/".$this->encodePath($project_id)."/pipelines/".$this->encodePath($pipeline_id)."/jobs", array(
            'scope' => $scope
        ));
    }

    /**
     * @param int|string $project_id
     * @param int $job_id
     * @return mixed
     */
    public function show($project_id, $job_id)
    {
        return $this->get("projects/".$this->encodePath($project_id)."/jobs/".$this->encodePath($job_id));
    }

    /**
     * @param int|string $project_id
     * @param int $job_id
     * @return string
     */
    public function artifacts($project_id, $job_id)
    {
        return $this->get("projects/".$this->encodePath($project_id)."/jobs/".$this->encodePath($job_id)."/artifacts");
    }

    /**
     * @param int|string $project_id
     * @param string $ref_name
     * @param string $job_name
     * @return string
     */
    public function artifactsByRefName($project_id, $ref_name, $job_name)
    {
        return $this->get("projects/".$this->encodePath($project_id)."/jobs/artifacts/".$this->encodePath($ref_name)."/download", array(
            'job' => $job_name
        ));
    }

    /**
     * @param int|string $project_id
     * @param int $job_id
     * @return string
     */
    public function trace($project_id, $job_id)
    {
        return $this->get("projects/".$this->encodePath($project_id)."/jobs/".$this->encodePath($job_id)."/trace");
    }

    /**
     * @param int|string $project_id
     * @param int $job_id
     * @return mixed
     */
    public function cancel($project_id, $job_id)
    {
        return $this->post("projects/".$this->encodePath($project_id)."/jobs/".$this->encodePath($job_id)."/cancel");
    }

    /**
     * @param int|string $project_id
     * @param int $job_id
     * @return mixed
     */
    public function retry($project_id, $job_id)
    {
        return $this->post("projects/".$this->encodePath($project_id)."/jobs/".$this->encodePath($job_id)."/retry");
    }

    /**
     * @param int|string $project_id
     * @param int $job_id
     * @return mixed
     */
    public function erase($project_id, $job_id)
    {
        return $this->post("projects/".$this->encodePath($project_id)."/jobs/".$this->encodePath($job_id)."/erase");
    }

    /**
     * @param int|string $project_id
     * @param int $job_id
     * @return mixed
     */
    public function keepArtifacts($project_id, $job_id)
    {
        return $this->post("projects/".$this->encodePath($project_id)."/jobs/".$this->encodePath($job_id)."/artifacts/keep");
    }

    /**
     * @param int|string $project_id
     * @param int $job_id
     * @return mixed
     */
    public function play($project_id, $job_id)
    {
        return $this->post("projects/".$this->encodePath($project_id)."/jobs/".$this->encodePath($job_id)."/play");
    }
}
