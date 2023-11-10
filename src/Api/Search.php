<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\Options;

class Search extends AbstractApi
{
    /**
     * @param array $parameters {
     *
     *     @var string             $scope                       The scope to search in
     *     @var string             $search                      The search query
     *     @var string             $state                  	    Filter by state. Issues and merge requests are supported; it is ignored for other scopes.
     *     @var bool               $confidential                Filter by confidentiality. Issues scope is supported; it is ignored for other scopes.
     *     @var string             $order_by                    Allowed values are created_at only. If this is not set, the results are either sorted by created_at in descending order for basic search, or by the most relevant documents when using advanced search.
     *     @var string             $sort                        Return projects sorted in asc or desc order (default is desc)
     * }
     *
     * @throws UndefinedOptionsException If an option name is undefined
     * @throws InvalidOptionsException   If an option doesn't fulfill the
     *                                   specified validation rules
     *
     * @return mixed
     */
    public function all(array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };
        $resolver->setDefined('confidential')
            ->setAllowedTypes('confidential', 'bool')
            ->setNormalizer('confidential', $booleanNormalizer);
        $scope = [
            'projects',
            'issues',
            'merge_requests',
            'milestones',
            'snippet_titles',
            'users',
        ];
        $resolver->setRequired('scope')
            ->setAllowedValues('scope', $scope);
        $resolver->setRequired('search');
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['created_at']);
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc']);

        return $this->get('search', $resolver->resolve($parameters));
    }
}
