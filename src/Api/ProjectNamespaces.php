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

class ProjectNamespaces extends AbstractApi
{
    /**
     * @see https://docs.gitlab.com/api/namespaces/#list-all-namespaces
     *
     * @param array $parameters {
     *
     *     @var string $search Returns a list of namespaces the user is authorized to see based on the search criteria.
     * }
     */
    public function all(array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('search');

        return $this->get('namespaces', $resolver->resolve($parameters));
    }

    /**
     * @see https://docs.gitlab.com/api/namespaces/#retrieve-namespace-details
     */
    public function show(int|string $namespace_id): mixed
    {
        return $this->get('namespaces/'.self::encodePath($namespace_id));
    }
}
