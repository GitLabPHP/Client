<?php namespace Gitlab\Api;

class ProjectNamespaces extends AbstractApi
{
    /**
     * @param array $parameters (
     *
     *     @var string $search Returns a list of namespaces the user is authorized to see based on the search criteria.
     * )
     *
     * @return mixed
     */
    public function all(array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('search');

        return $this->get('namespaces', $resolver->resolve($parameters));
    }
}
