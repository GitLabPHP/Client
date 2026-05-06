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

class PersonalAccessTokens extends AbstractApi
{
    /**
     * @param array $parameters {
     *
     *     @var string             $search            search text
     *     @var string             $state             state of the token
     *     @var int|string         $user_id           tokens belonging to the given user
     *     @var bool               $revoked           whether the token is revoked or not
     *     @var \DateTimeInterface $created_after     return tokens created after the given time
     *     @var \DateTimeInterface $created_before    return tokens created before the given time
     *     @var \DateTimeInterface $expires_after     return tokens that expire after the given date
     *     @var \DateTimeInterface $expires_before    return tokens that expire before the given date
     *     @var \DateTimeInterface $last_used_after   return tokens last used after the given time
     *     @var \DateTimeInterface $last_used_before  return tokens last used before the given time
     *     @var string             $sort              sort by created, expires, last_used, or name
     * }
     */
    public function all(array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('c');
        };
        $dateNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('Y-m-d');
        };
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('search')
            ->setAllowedTypes('search', 'string')
        ;
        $resolver->setDefined('state')
            ->setAllowedValues('state', ['active', 'inactive'])
        ;
        $resolver->setDefined('user_id')
            ->setAllowedTypes('user_id', ['int', 'string'])
            ->setAllowedValues('user_id', function ($value): bool {
                return \is_int($value) ? $value > 0 : '' !== $value;
            })
        ;
        $resolver->setDefined('revoked')
            ->setAllowedTypes('revoked', 'bool')
            ->setNormalizer('revoked', $booleanNormalizer)
        ;
        $resolver->setDefined('created_after')
            ->setAllowedTypes('created_after', \DateTimeInterface::class)
            ->setNormalizer('created_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('created_before')
            ->setAllowedTypes('created_before', \DateTimeInterface::class)
            ->setNormalizer('created_before', $datetimeNormalizer)
        ;
        $resolver->setDefined('expires_after')
            ->setAllowedTypes('expires_after', \DateTimeInterface::class)
            ->setNormalizer('expires_after', $dateNormalizer)
        ;
        $resolver->setDefined('expires_before')
            ->setAllowedTypes('expires_before', \DateTimeInterface::class)
            ->setNormalizer('expires_before', $dateNormalizer)
        ;
        $resolver->setDefined('last_used_after')
            ->setAllowedTypes('last_used_after', \DateTimeInterface::class)
            ->setNormalizer('last_used_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('last_used_before')
            ->setAllowedTypes('last_used_before', \DateTimeInterface::class)
            ->setNormalizer('last_used_before', $datetimeNormalizer)
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['created_asc', 'created_desc', 'expires_asc', 'expires_desc', 'last_used_asc', 'last_used_desc', 'name_asc', 'name_desc'])
        ;

        return $this->get('personal_access_tokens', $resolver->resolve($parameters));
    }

    public function show(int|string $id): mixed
    {
        return $this->get('personal_access_tokens/'.self::encodePath($id));
    }

    public function current(): mixed
    {
        return $this->get('personal_access_tokens/self');
    }

    /**
     * @param array $parameters {
     *
     *     @var \DateTimeInterface $expires_at expiration date of the access token
     * }
     */
    public function rotate(int|string $id, array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $dateNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('Y-m-d');
        };
        $resolver->setDefined('expires_at')
            ->setAllowedTypes('expires_at', \DateTimeInterface::class)
            ->setNormalizer('expires_at', $dateNormalizer)
        ;

        return $this->post('personal_access_tokens/'.self::encodePath($id).'/rotate', $resolver->resolve($parameters));
    }

    /**
     * @param array $parameters {
     *
     *     @var \DateTimeInterface $expires_at expiration date of the access token
     * }
     */
    public function rotateCurrent(array $parameters = []): mixed
    {
        $resolver = new OptionsResolver();
        $dateNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('Y-m-d');
        };
        $resolver->setDefined('expires_at')
            ->setAllowedTypes('expires_at', \DateTimeInterface::class)
            ->setNormalizer('expires_at', $dateNormalizer)
        ;

        return $this->post('personal_access_tokens/self/rotate', $resolver->resolve($parameters));
    }

    public function remove(int|string $id): mixed
    {
        return $this->delete('personal_access_tokens/'.self::encodePath($id));
    }

    public function removeCurrent(): mixed
    {
        return $this->delete('personal_access_tokens/self');
    }
}
