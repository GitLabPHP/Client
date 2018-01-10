<?php namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Environments extends AbstractApi
{
    /**
     * @param int $project_id
     * @param array $parameters
     * @return mixed
     */
    public function all($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        return $this->get($this->getProjectPath($project_id, 'environments'), $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param array $parameters (
     *
     *     @var string $name         The name of the environment
     *     @var string $external_url Place to link to for this environment
     * )
     * @return mixed
     */
    public function create($project_id, array $parameters = array())
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('name')
            ->setRequired('name')
            ->setAllowedTypes('name', 'string');
        $resolver->setDefined('external_url')
            ->setAllowedTypes('external_url', 'string');

        return $this->post($this->getProjectPath($project_id, 'environment'), $resolver->resolve($parameters));
    }

    /**
     * @param int $project_id
     * @param string $environment_id
     * @return mixed
     */
    public function remove($project_id, $environment_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'environments/' . $environment_id));
    }

    /**
     * @param int $project_id
     * @param string $environment_id
     * @return mixed
     */
    public function stop($project_id, $environment_id)
    {
        return $this->post($this->getProjectPath($project_id, 'environments/'.$this->encodePath($environment_id).'/stop'));
    }
}
