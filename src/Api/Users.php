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
     */
    public function all(array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
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

    public function show(int $id): mixed
    {
        return $this->get('users/'.self::encodePath($id));
    }

    /**
     * @param array $parameters {
     *
     *     @var string $type Filter memberships by type. Can be either Project or Namespace
     * }
     */
    public function usersMemberships(int $id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('type')
                 ->setAllowedValues('type', ['Project', 'Namespace'])
        ;

        return $this->get('users/'.self::encodePath($id).'/memberships', $resolver->resolve($parameters));
    }

    /**
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
     */
    public function usersProjects(int $id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
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

        return $this->get('users/'.self::encodePath($id).'/projects', $resolver->resolve($parameters));
    }

    /**
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
     *     @var bool   $with_custom_attributes      Include custom attributes in response (administrator only)
     * }
     */
    public function usersStarredProjects(int $id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
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
        $resolver->setDefined('with_custom_attributes')
            ->setAllowedTypes('with_custom_attributes', 'bool')
            ->setNormalizer('with_custom_attributes', $booleanNormalizer)
        ;

        return $this->get('users/'.self::encodePath($id).'/starred_projects', $resolver->resolve($parameters));
    }

    public function user(): mixed
    {
        return $this->get('user');
    }

    public function create(string $email, string $password, array $params = []): mixed
    {
        $params['email'] = $email;
        $params['password'] = $password;

        return $this->post('users', $params);
    }

    public function update(int $id, array $params, array $files = []): mixed
    {
        return $this->put('users/'.self::encodePath($id), $params, [], $files);
    }

    /**
     * @param array $params {
     *
     *     @var bool   $hard_delete     If true, contributions that would usually be moved to the ghost user are
     *                                  deleted instead, as well as groups owned solely by this user.
     * }
     */
    public function remove(int $id, array $params = []): mixed
    {
        return $this->delete('users/'.self::encodePath($id), $params);
    }

    public function block(int $id): mixed
    {
        return $this->post('users/'.self::encodePath($id).'/block');
    }

    public function unblock(int $id): mixed
    {
        return $this->post('users/'.self::encodePath($id).'/unblock');
    }

    public function activate(int $id): mixed
    {
        return $this->post('users/'.self::encodePath($id).'/activate');
    }

    public function deactivate(int $id): mixed
    {
        return $this->post('users/'.self::encodePath($id).'/deactivate');
    }

    public function me(): mixed
    {
        return $this->get('user');
    }

    public function keys(): mixed
    {
        return $this->get('user/keys');
    }

    public function key(int $id): mixed
    {
        return $this->get('user/keys/'.self::encodePath($id));
    }

    public function createKey(string $title, string $key): mixed
    {
        return $this->post('user/keys', [
            'title' => $title,
            'key' => $key,
        ]);
    }

    public function removeKey(int $id): mixed
    {
        return $this->delete('user/keys/'.self::encodePath($id));
    }

    public function userKeys(int $user_id): mixed
    {
        return $this->get('users/'.self::encodePath($user_id).'/keys');
    }

    public function userKey(int $user_id, int $key_id): mixed
    {
        return $this->get('users/'.self::encodePath($user_id).'/keys/'.self::encodePath($key_id));
    }

    public function createKeyForUser(int $user_id, string $title, string $key): mixed
    {
        return $this->post('users/'.self::encodePath($user_id).'/keys', [
            'title' => $title,
            'key' => $key,
        ]);
    }

    public function removeUserKey(int $user_id, int $key_id): mixed
    {
        return $this->delete('users/'.self::encodePath($user_id).'/keys/'.self::encodePath($key_id));
    }

    public function emails(): mixed
    {
        return $this->get('user/emails');
    }

    public function email(int $id): mixed
    {
        return $this->get('user/emails/'.self::encodePath($id));
    }

    public function userEmails(int $user_id): mixed
    {
        return $this->get('users/'.self::encodePath($user_id).'/emails');
    }

    public function createEmailForUser(int $user_id, string $email, bool $skip_confirmation = false): mixed
    {
        return $this->post('users/'.self::encodePath($user_id).'/emails', [
            'email' => $email,
            'skip_confirmation' => $skip_confirmation,
        ]);
    }

    public function removeUserEmail(int $user_id, int $email_id): mixed
    {
        return $this->delete('users/'.self::encodePath($user_id).'/emails/'.self::encodePath($email_id));
    }

    public function userImpersonationTokens(int $user_id, array $params = []): mixed
    {
        $resolver = $this->createOptionsResolver();

        $resolver->setDefined('state')
            ->setAllowedValues('state', ['all', 'active', 'inactive'])
        ;

        return $this->get('users/'.self::encodePath($user_id).'/impersonation_tokens', $resolver->resolve($params));
    }

    public function userImpersonationToken(int $user_id, int $impersonation_token_id): mixed
    {
        return $this->get('users/'.self::encodePath($user_id).'/impersonation_tokens/'.self::encodePath($impersonation_token_id));
    }

    public function createImpersonationToken(int $user_id, string $name, array $scopes, ?string $expires_at = null): mixed
    {
        return $this->post('users/'.self::encodePath($user_id).'/impersonation_tokens', [
            'name' => $name,
            'scopes' => $scopes,
            'expires_at' => $expires_at,
        ]);
    }

    public function removeImpersonationToken(int $user_id, int $impersonation_token_id): mixed
    {
        return $this->delete('users/'.self::encodePath($user_id).'/impersonation_tokens/'.self::encodePath($impersonation_token_id));
    }

    /**
     * @param array $parameters {
     *
     *     @var string             $action      include only events of a particular action type
     *     @var string             $target_type include only events of a particular target type
     *     @var \DateTimeInterface $before      include only events created before a particular date
     *     @var \DateTimeInterface $after       include only events created after a particular date
     *     @var string             $sort        Sort events in asc or desc order by created_at (default is desc)
     * }
     */
    public function events(int $user_id, array $parameters = []): mixed
    {
        $resolver = $this->createOptionsResolver();
        $datetimeNormalizer = function (Options $resolver, \DateTimeInterface $value): string {
            return $value->format('Y-m-d');
        };

        $resolver->setDefined('action')
            ->setAllowedValues('action', ['created', 'updated', 'closed', 'reopened', 'pushed', 'commented', 'merged', 'joined', 'left', 'destroyed', 'expired'])
        ;
        $resolver->setDefined('target_type')
            ->setAllowedValues('target_type', ['issue', 'milestone', 'merge_request', 'note', 'project', 'snippet', 'user'])
        ;
        $resolver->setDefined('before')
            ->setAllowedTypes('before', \DateTimeInterface::class)
            ->setNormalizer('before', $datetimeNormalizer);
        $resolver->setDefined('after')
            ->setAllowedTypes('after', \DateTimeInterface::class)
            ->setNormalizer('after', $datetimeNormalizer)
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;

        return $this->get('users/'.self::encodePath($user_id).'/events', $resolver->resolve($parameters));
    }

    /**
     * Deletes a user’s authentication identity using the provider name associated with that identity.
     */
    public function removeUserIdentity(int $user_id, string $provider): mixed
    {
        return $this->delete('users/'.self::encodePath($user_id).'/identities/'.self::encodePath($provider));
    }
}
