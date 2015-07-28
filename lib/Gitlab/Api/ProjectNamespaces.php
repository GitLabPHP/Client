<?php namespace Gitlab\Api;

class ProjectNamespaces extends AbstractApi
{
    /**
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function all($page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('namespaces', array(
            'page' => $page,
            'per_page' => $per_page
        ));
    }

    /**
     * @param string $terms
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function search($terms, $page = 1, $per_page = self::PER_PAGE)
    {
        return $this->get('namespaces', array(
            'search' => $terms,
            'page' => $page,
            'per_page' => $per_page
        ));
    }
}
