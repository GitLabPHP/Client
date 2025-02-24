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

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SystemHooks extends AbstractApi
{
    public function all(): mixed
    {
        return $this->get('hooks');
    }

    /**
     * @param array<string,string|bool> $parameters {
     *
     *     @var string  $token                      secret token to validate received payloads
     *     @var bool    $push_events                when true, the hook fires on push events
     *     @var bool    $tag_push_events            when true, the hook fires on new tags being pushed
     *     @var bool    $merge_requests_events      trigger hook on merge requests events
     *     @var bool    $repository_update_events   trigger hook on repository update events
     *     @var bool    $enable_ssl_verification    do SSL verification when triggering the hook
     * }
     */
    public function create(string $url, array $parameters = []): mixed
    {
        $parameters = $this->createOptionsResolver()->resolve($parameters);

        $parameters['url'] = $url;

        return $this->post('hooks', $parameters);
    }

    public function test(int $id): mixed
    {
        return $this->get('hooks/'.self::encodePath($id));
    }

    public function remove(int $id): mixed
    {
        return $this->delete('hooks/'.self::encodePath($id));
    }

    protected function createOptionsResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();

        $resolver->setDefined('token');

        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('push_events')
                 ->setAllowedTypes('push_events', 'bool')
                 ->setNormalizer('push_events', $booleanNormalizer)
        ;

        $resolver->setDefined('tag_push_events')
                 ->setAllowedTypes('tag_push_events', 'bool')
                 ->setNormalizer('tag_push_events', $booleanNormalizer)
        ;

        $resolver->setDefined('merge_requests_events')
                 ->setAllowedTypes('merge_requests_events', 'bool')
                 ->setNormalizer('merge_requests_events', $booleanNormalizer)
        ;

        $resolver->setDefined('repository_update_events')
                 ->setAllowedTypes('repository_update_events', 'bool')
                 ->setNormalizer('repository_update_events', $booleanNormalizer)
        ;

        $resolver->setDefined('enable_ssl_verification')
                 ->setAllowedTypes('enable_ssl_verification', 'bool')
                 ->setNormalizer('enable_ssl_verification', $booleanNormalizer)
        ;

        return $resolver;
    }
}
