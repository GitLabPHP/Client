<?php

namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\Options;

class Users extends AbstractApi
{
    /**
     * @param array $parameters {
     *
     *     @var string             $search         search for user by email or username
     *     @var string             $username       lookup for user by username
     *     @var bool               $external       search for external users only
     *     @var string             $extern_uid     lookup for users by external uid
     *     @var string             $provider       lookup for users by provider
     *     @var \DateTimeInterface $created_before return users created before the given time (inclusive)
     *     @var \DateTimeInterface $created_after  return users created after the given time (inclusive)
     *     @var bool               $active         Return only active users. It does not support filtering inactive users.
     *     @var bool               $blocked        Return only blocked users. It does not support filtering non-blocked users.
     * }
     *
     * @return mixed
     */
    public function all(array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value) {
            return $value->format('c');
        };

        $resolver->setDefined('search');
        $resolver->setDefined('username');
        $resolver->setDefined('external')
            ->setAllowedTypes('external', 'bool')
        ;
        $resolver->setDefined('extern_uid');
        $resolver->setDefined('provider');
        $resolver->setDefined('created_before')
            ->setAllowedTypes('created_before', \DateTimeInterface::class)
            ->setNormalizer('created_before', $datetimeNormalizer)
        ;
        $resolver->setDefined('created_after')
            ->setAllowedTypes('created_after', \DateTimeInterface::class)
            ->setNormalizer('created_after', $datetimeNormalizer)
        ;
        $resolver->setDefined('active')
            ->setAllowedTypes('active', 'bool')
            ->setAllowedValues('active', true)
        ;
        $resolver->setDefined('blocked')
            ->setAllowedTypes('blocked', 'bool')
            ->setAllowedValues('blocked', true)
        ;

        return $this->get('users', $resolver->resolve($parameters));
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function show($id)
    {
        return $this->get('users/'.$this->encodePath($id));
    }

    /**
     * @param int   $id
     * @param array $parameters {
     *
     *     @var bool   $archived                    limit by archived status
     *     @var string $visibility                  limit by visibility public, internal, or private
     *     @var string $order_by                    Return projects ordered by id, name, path, created_at, updated_at,
     *                                              or last_activity_at fields (default is created_at)
     *     @var string $sort                        Return projects sorted in asc or desc order (default is desc)
     *     @var string $search                      return list of projects matching the search criteria
     *     @var bool   $simple                      return only the ID, URL, name, and path of each project
     *     @var bool   $owned                       limit by projects owned by the current user
     *     @var bool   $membership                  limit by projects that the current user is a member of
     *     @var bool   $starred                     limit by projects starred by the current user
     *     @var bool   $statistics                  include project statistics
     *     @var bool   $with_issues_enabled         limit by enabled issues feature
     *     @var bool   $with_merge_requests_enabled limit by enabled merge requests feature
     *     @var int    $min_access_level            Limit by current user minimal access level
     * }
     *
     * @return mixed
     */
    public function usersProjects($id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value) {
            return $value ? 'true' : 'false';
        };
        $resolver->setDefined('archived')
            ->setAllowedTypes('archived', 'bool')
            ->setNormalizer('archived', $booleanNormalizer)
        ;
        $resolver->setDefined('visibility')
            ->setAllowedValues('visibility', ['public', 'internal', 'private'])
        ;
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['id', 'name', 'path', 'created_at', 'updated_at', 'last_activity_at'])
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;
        $resolver->setDefined('search');
        $resolver->setDefined('simple')
            ->setAllowedTypes('simple', 'bool')
            ->setNormalizer('simple', $booleanNormalizer)
        ;
        $resolver->setDefined('owned')
            ->setAllowedTypes('owned', 'bool')
            ->setNormalizer('owned', $booleanNormalizer)
        ;
        $resolver->setDefined('membership')
            ->setAllowedTypes('membership', 'bool')
            ->setNormalizer('membership', $booleanNormalizer)
        ;
        $resolver->setDefined('starred')
            ->setAllowedTypes('starred', 'bool')
            ->setNormalizer('starred', $booleanNormalizer)
        ;
        $resolver->setDefined('statistics')
            ->setAllowedTypes('statistics', 'bool')
            ->setNormalizer('statistics', $booleanNormalizer)
        ;
        $resolver->setDefined('with_issues_enabled')
            ->setAllowedTypes('with_issues_enabled', 'bool')
            ->setNormalizer('with_issues_enabled', $booleanNormalizer)
        ;
        $resolver->setDefined('with_merge_requests_enabled')
            ->setAllowedTypes('with_merge_requests_enabled', 'bool')
            ->setNormalizer('with_merge_requests_enabled', $booleanNormalizer)
        ;
        $resolver->setDefined('min_access_level')
            ->setAllowedValues('min_access_level', [null, 10, 20, 30, 40, 50])
        ;

        return $this->get('users/'.$this->encodePath($id).'/projects', $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->get('user');
    }

    /**
     * @param string $email
     * @param string $password
     * @param array  $params
     *
     * @return mixed
     */
    public function create($email, $password, array $params = [])
    {
        $params['email'] = $email;
        $params['password'] = $password;

        return $this->post('users', $params);
    }

    /**
     * @param int   $id
     * @param array $params
     * @param array $files
     *
     * @return mixed
     */
    public function update($id, array $params, array $files = [])
    {
        return $this->put('users/'.$this->encodePath($id), $params, [], $files);
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function remove($id)
    {
        return $this->delete('users/'.$this->encodePath($id));
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function block($id)
    {
        return $this->post('users/'.$this->encodePath($id).'/block');
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function unblock($id)
    {
        return $this->post('users/'.$this->encodePath($id).'/unblock');
    }

    /**
     * @param string $emailOrUsername
     * @param string $password
     *
     * @return mixed
     *
     * @deprecated since version 9.18 and will be removed in 10.0.
     */
    public function session($emailOrUsername, $password)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0.', __METHOD__), E_USER_DEPRECATED);

        return $this->post('session', [
            'login' => $emailOrUsername,
            'email' => $emailOrUsername,
            'password' => $password,
        ]);
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return mixed
     *
     * @deprecated since version 9.18 and will be removed in 10.0.
     */
    public function login($email, $password)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0.', __METHOD__), E_USER_DEPRECATED);

        return $this->session($email, $password);
    }

    /**
     * @return mixed
     */
    public function me()
    {
        return $this->get('user');
    }

    /**
     * @return mixed
     */
    public function keys()
    {
        return $this->get('user/keys');
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function key($id)
    {
        return $this->get('user/keys/'.$this->encodePath($id));
    }

    /**
     * @param string $title
     * @param string $key
     *
     * @return mixed
     */
    public function createKey($title, $key)
    {
        return $this->post('user/keys', [
            'title' => $title,
            'key' => $key,
        ]);
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function removeKey($id)
    {
        return $this->delete('user/keys/'.$this->encodePath($id));
    }

    /**
     * @param int $user_id
     *
     * @return mixed
     */
    public function userKeys($user_id)
    {
        return $this->get('users/'.$this->encodePath($user_id).'/keys');
    }

    /**
     * @param int $user_id
     * @param int $key_id
     *
     * @return mixed
     */
    public function userKey($user_id, $key_id)
    {
        return $this->get('users/'.$this->encodePath($user_id).'/keys/'.$this->encodePath($key_id));
    }

    /**
     * @param int    $user_id
     * @param string $title
     * @param string $key
     *
     * @return mixed
     */
    public function createKeyForUser($user_id, $title, $key)
    {
        return $this->post('users/'.$this->encodePath($user_id).'/keys', [
            'title' => $title,
            'key' => $key,
        ]);
    }

    /**
     * @param int $user_id
     * @param int $key_id
     *
     * @return mixed
     */
    public function removeUserKey($user_id, $key_id)
    {
        return $this->delete('users/'.$this->encodePath($user_id).'/keys/'.$this->encodePath($key_id));
    }

    /**
     * @return mixed
     */
    public function emails()
    {
        return $this->get('user/emails');
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function email($id)
    {
        return $this->get('user/emails/'.$this->encodePath($id));
    }

    /**
     * @param int $user_id
     *
     * @return mixed
     */
    public function userEmails($user_id)
    {
        return $this->get('users/'.$this->encodePath($user_id).'/emails');
    }

    /**
     * @param int    $user_id
     * @param string $email
     * @param bool   $skip_confirmation
     *
     * @return mixed
     */
    public function createEmailForUser($user_id, $email, $skip_confirmation = false)
    {
        return $this->post('users/'.$this->encodePath($user_id).'/emails', [
            'email' => $email,
            'skip_confirmation' => $skip_confirmation,
        ]);
    }

    /**
     * @param int $user_id
     * @param int $email_id
     *
     * @return mixed
     */
    public function removeUserEmail($user_id, $email_id)
    {
        return $this->delete('users/'.$this->encodePath($user_id).'/emails/'.$this->encodePath($email_id));
    }

    /**
     * @param int   $user_id
     * @param array $params
     *
     * @return mixed
     */
    public function userImpersonationTokens($user_id, array $params = [])
    {
        $resolver = $this->createOptionsResolver();

        $resolver->setDefined('state')
            ->setAllowedValues('state', ['all', 'active', 'inactive'])
        ;

        return $this->get('users/'.$this->encodePath($user_id).'/impersonation_tokens', $resolver->resolve($params));
    }

    /**
     * @param int $user_id
     * @param int $impersonation_token_id
     *
     * @return mixed
     */
    public function userImpersonationToken($user_id, $impersonation_token_id)
    {
        return $this->get('users/'.$this->encodePath($user_id).'/impersonation_tokens/'.$this->encodePath($impersonation_token_id));
    }

    /**
     * @param int         $user_id
     * @param string      $name
     * @param array       $scopes
     * @param string|null $expires_at
     *
     * @return mixed
     */
    public function createImpersonationToken($user_id, $name, array $scopes, $expires_at = null)
    {
        return $this->post('users/'.$this->encodePath($user_id).'/impersonation_tokens', [
            'name' => $name,
            'scopes' => $scopes,
            'expires_at' => $expires_at,
        ]);
    }

    /**
     * @param int $user_id
     * @param int $impersonation_token_id
     *
     * @return mixed
     */
    public function removeImpersonationToken($user_id, $impersonation_token_id)
    {
        return $this->delete('users/'.$this->encodePath($user_id).'/impersonation_tokens/'.$this->encodePath($impersonation_token_id));
    }
}
