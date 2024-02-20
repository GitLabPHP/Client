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

class PersonalAccessTokens extends AbstractApi
{
    /**
    * @param array $parameters {
    *
    *     @var string             $search            search text
    *     @var string             $state             state of the token
    *     @var int                $user_id           tokens belonging to the given user
    *     @var bool               $revoked           whether the token is revoked or not
    *     @var \DateTimeInterface $created_before    return tokens created before the given time (inclusive)
    *     @var \DateTimeInterface $created_after     return tokens created after the given time (inclusive)
    *     @var \DateTimeInterface $last_used_after   return tokens used before the given time (inclusive)
    *     @var \DateTimeInterface $last_used_before  return tokens used after the given time (inclusive)
    * }
    *
    * @return mixed
    */
    public function all(array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('c');
        };
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };
        
        $resolver->setDefined('search');
        $resolver->setDefined('state')
            ->setAllowedValues('state', ['active', 'inactive']);
        $resolver->setDefined('user_id')
            ->setAllowedTypes('user_id', 'int')
            ->setAllowedValues('user_id', function ($value): bool {
                return $value > 0;
            })
        ;
        $resolver->setDefined('created_before')
            ->setAllowedTypes('created_before', \DateTimeInterface::class)
            ->setNormalizer('created_before', $datetimeNormalizer)
        ;
        $resolver->setDefined('created_after')
            ->setAllowedTypes('created_after', \DateTimeInterface::class)
            ->setNormalizer('created_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('last_used_after')
            ->setAllowedTypes('last_used_after', \DateTimeInterface::class)
            ->setNormalizer('last_used_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('last_used_before')
            ->setAllowedTypes('last_used_before', \DateTimeInterface::class)
            ->setNormalizer('last_used_before', $datetimeNormalizer)
        ;
        $resolver->setDefined('revoked')
            ->setAllowedTypes('revoked', 'bool')
            ->setNormalizer('revoked', $booleanNormalizer);
        ;
        
        return $this->get('personal_access_tokens', $resolver->resolve($parameters));
    }
    
    /**
    * @param int $id
    *
    * @return mixed
    */
    public function show(int $id)
    {
        return $this->get('personal_access_tokens/'.self::encodePath($id));
    }
    
    /**
    * @return mixed
    */
    public function current()
    {
        return $this->get('personal_access_tokens/self');
    }
    
    
    /**
    * @param int $id
    * 
    * @param array $params
    *
    * @return mixed
    */
    public function rotate(int $id, array $params = [])
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('c');
        };
        $resolver->setDefined('expires_at')
            ->setAllowedTypes('expires_at', \DateTimeInterface::class)
            ->setNormalizer('expires_at', $datetimeNormalizer)
        ;
        return $this->post('personal_access_tokens/'.self::encodePath($id).'/rotate', $resolver->resolve($params));
    }
    
    /**
    * @param array $params
    *
    * @return mixed
    */
    public function rotateCurrent(array $params = [])
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('c');
        };
        $resolver->setDefined('expires_at')
            ->setAllowedTypes('expires_at', \DateTimeInterface::class)
            ->setNormalizer('expires_at', $datetimeNormalizer)
        ;
        return $this->post('personal_access_tokens/self/rotate', $resolver->resolve($params));
    }
    
    /**
    * @param int   $id
    *
    * @return mixed
    */
    public function remove(int $id)
    {
        return $this->delete('personal_access_tokens/'.self::encodePath($id));
    }
    
    /**
    * @return mixed
    */
    public function removeCurrent()
    {
        return $this->delete('personal_access_tokens/self');
    }
}
