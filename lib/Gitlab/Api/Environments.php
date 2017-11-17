<?php namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Environments extends AbstractApi
{
    /**
     * @param int $project_id
     * @return mixed
     */
    public function all($project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'environments'));
    }

    /**
     * @param int $project_id
     * @param array $parameters
     * @return mixed
     */
    public function create($project_id, array $parameters = array())
    {
	    $resolver = new OptionsResolver();
	    $resolver->setDefined('name')
	        ->setAllowedTypes('name', 'string');
	    $resolver->setDefined('slug')
	        ->setAllowedTypes('slug', 'string');
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
}
