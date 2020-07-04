<?php

namespace Gitlab\Api;

class DeployKeys extends AbstractApi
{
    /**
     * @param array $parameters
     *
     * @return mixed
     */
    public function all(array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get('deploy_keys', $resolver->resolve($parameters));
    }
}
