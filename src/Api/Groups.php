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

class Groups extends AbstractApi
{
    /**
     * @param array $parameters {
     *
     *     @var int[]  $skip_groups      skip the group IDs passes
     *     @var bool   $all_available    show all the groups you have access to
     *     @var string $search           return list of authorized groups matching the search criteria
     *     @var string $order_by         Order groups by name or path (default is name)
     *     @var string $sort             Order groups in asc or desc order (default is asc)
     *     @var bool   $statistics       include group statistics (admins only)
     *     @var bool   $owned            limit by groups owned by the current user
     *     @var int    $min_access_level limit by groups in which the current user has at least this access level
     *     @var bool   $top_level_only   limit to top level groups, excluding all subgroups
     * }
     *
     * @return mixed
     */
    public function all(array $parameters = [])
    {
        $resolver = $this->getGroupSearchResolver();

        return $this->get('groups', $resolver->resolve($parameters));
    }

    /**
     * @param int|string $id
     *
     * @return mixed
     */
    public function show($id)
    {
        return $this->get('groups/'.self::encodePath($id));
    }

    /**
     * @param string $name
     * @param string $path
     * @param string $description
     * @param string $visibility
     * @param bool   $lfs_enabled
     * @param bool   $request_access_enabled
     * @param int    $parent_id
     * @param int    $shared_runners_minutes_limit
     *
     * @return mixed
     */
    public function create(string $name, string $path, string $description = null, string $visibility = 'private', bool $lfs_enabled = null, bool $request_access_enabled = null, int $parent_id = null, int $shared_runners_minutes_limit = null)
    {
        $params = [
            'name' => $name,
            'path' => $path,
            'description' => $description,
            'visibility' => $visibility,
            'lfs_enabled' => $lfs_enabled,
            'request_access_enabled' => $request_access_enabled,
            'parent_id' => $parent_id,
            'shared_runners_minutes_limit' => $shared_runners_minutes_limit,
        ];

        return $this->post('groups', \array_filter($params, function ($value) {
            return null !== $value && (!\is_string($value) || '' !== $value);
        }));
    }

    /**
     * @param int|string $id
     * @param array      $params
     *
     * @return mixed
     */
    public function update($id, array $params)
    {
        return $this->put('groups/'.self::encodePath($id), $params);
    }

    /**
     * @param int|string $group_id
     *
     * @return mixed
     */
    public function remove($group_id)
    {
        return $this->delete('groups/'.self::encodePath($group_id));
    }

    /**
     * @param int|string $group_id
     * @param int|string $project_id
     *
     * @return mixed
     */
    public function transfer($group_id, $project_id)
    {
        return $this->post('groups/'.self::encodePath($group_id).'/projects/'.self::encodePath($project_id));
    }

    /**
     * @param int|string $group_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function allMembers($group_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('query');
        $resolver->setDefined('user_ids')
            ->setAllowedTypes('user_ids', 'array')
            ->setAllowedValues('user_ids', function (array $value) {
                return \count($value) === \count(\array_filter($value, 'is_int'));
            })
        ;

        return $this->get('groups/'.self::encodePath($group_id).'/members/all', $resolver->resolve($parameters));
    }

    /**
     * @param int|string $group_id
     * @param array      $parameters {
     *
     *     @var string $query A query string to search for members.
     * }
     *
     * @return mixed
     */
    public function members($group_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('query');
        $resolver->setDefined('user_ids')
            ->setAllowedTypes('user_ids', 'array')
            ->setAllowedValues('user_ids', function (array $value) {
                return \count($value) === \count(\array_filter($value, 'is_int'));
            })
        ;

        return $this->get('groups/'.self::encodePath($group_id).'/members', $resolver->resolve($parameters));
    }

    /**
     * @param int|string $group_id
     * @param int        $user_id
     *
     * @return mixed
     */
    public function member($group_id, int $user_id)
    {
        return $this->get('groups/'.self::encodePath($group_id).'/members/'.self::encodePath($user_id));
    }

    /**
     * @param int|string $group_id
     * @param int        $user_id
     *
     * @return mixed
     */
    public function allMember($group_id, int $user_id)
    {
        return $this->get('groups/'.self::encodePath($group_id).'/members/all/'.self::encodePath($user_id));
    }

    /**
     * @param int|string $group_id
     * @param int        $user_id
     * @param int        $access_level
     *
     * @return mixed
     */
    public function addMember($group_id, int $user_id, int $access_level)
    {
        return $this->post('groups/'.self::encodePath($group_id).'/members', [
            'user_id' => $user_id,
            'access_level' => $access_level,
        ]);
    }

    /**
     * @param int|string $group_id
     * @param int        $user_id
     * @param int        $access_level
     *
     * @return mixed
     */
    public function saveMember($group_id, int $user_id, int $access_level)
    {
        return $this->put('groups/'.self::encodePath($group_id).'/members/'.self::encodePath($user_id), [
            'access_level' => $access_level,
        ]);
    }

    /**
     * @param int|string $group_id
     * @param int        $user_id
     *
     * @return mixed
     */
    public function removeMember($group_id, int $user_id)
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/members/'.self::encodePath($user_id));
    }

    /**
     * @param int|string $id
     * @param array      $parameters {
     *
     *     @var bool   $archived                    limit by archived status
     *     @var string $visibility                  limit by visibility public, internal, or private
     *     @var string $order_by                    Return projects ordered by id, name, path, created_at, updated_at, or last_activity_at fields.
     *                                              Default is created_at.
     *     @var string $sort                        Return projects sorted in asc or desc order (default is desc)
     *     @var string $search                      return list of authorized projects matching the search criteria
     *     @var bool   $simple                      return only the ID, URL, name, and path of each project
     *     @var bool   $owned                       limit by projects owned by the current user
     *     @var bool   $starred                     limit by projects starred by the current user
     *     @var bool   $with_issues_enabled         Limit by projects with issues feature enabled (default is false)
     *     @var bool   $with_merge_requests_enabled Limit by projects with merge requests feature enabled (default is false)
     *     @var bool   $with_shared                 Include projects shared to this group (default is true)
     *     @var bool   $include_subgroups           Include projects in subgroups of this group (default is false)
     *     @var bool   $with_custom_attributes      Include custom attributes in response (admins only).
     * }
     *
     * @return mixed
     */
    public function projects($id, array $parameters = [])
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
        $resolver->setDefined('starred')
            ->setAllowedTypes('starred', 'bool')
            ->setNormalizer('starred', $booleanNormalizer)
        ;
        $resolver->setDefined('with_issues_enabled')
            ->setAllowedTypes('with_issues_enabled', 'bool')
            ->setNormalizer('with_issues_enabled', $booleanNormalizer)
        ;
        $resolver->setDefined('with_merge_requests_enabled')
            ->setAllowedTypes('with_merge_requests_enabled', 'bool')
            ->setNormalizer('with_merge_requests_enabled', $booleanNormalizer)
        ;
        $resolver->setDefined('with_shared')
            ->setAllowedTypes('with_shared', 'bool')
            ->setNormalizer('with_shared', $booleanNormalizer)
        ;
        $resolver->setDefined('include_subgroups')
            ->setAllowedTypes('include_subgroups', 'bool')
            ->setNormalizer('include_subgroups', $booleanNormalizer)
        ;
        $resolver->setDefined('with_custom_attributes')
            ->setAllowedTypes('with_custom_attributes', 'bool')
            ->setNormalizer('with_custom_attributes', $booleanNormalizer)
        ;

        return $this->get('groups/'.self::encodePath($id).'/projects', $resolver->resolve($parameters));
    }

    /**
     * @param int|string $group_id
     * @param array      $parameters {
     *
     *     @var int[]  $skip_groups   skip the group IDs passes
     *     @var bool   $all_available show all the groups you have access to
     *     @var string $search        return list of authorized groups matching the search criteria
     *     @var string $order_by      Order groups by name or path (default is name)
     *     @var string $sort          Order groups in asc or desc order (default is asc)
     *     @var bool   $statistics    include group statistics (admins only)
     *     @var bool   $owned         Limit by groups owned by the current user.
     * }
     *
     * @return mixed
     */
    public function subgroups($group_id, array $parameters = [])
    {
        $resolver = $this->getSubgroupSearchResolver();

        return $this->get('groups/'.self::encodePath($group_id).'/subgroups', $resolver->resolve($parameters));
    }

    /**
     * @param int|string $group_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function labels($group_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get('groups/'.self::encodePath($group_id).'/labels', $resolver->resolve($parameters));
    }

    /**
     * @param int|string $group_id
     * @param array      $params
     *
     * @return mixed
     */
    public function addLabel($group_id, array $params)
    {
        return $this->post('groups/'.self::encodePath($group_id).'/labels', $params);
    }

    /**
     * @param int|string $group_id
     * @param int        $label_id
     * @param array      $params
     *
     * @return mixed
     */
    public function updateLabel($group_id, int $label_id, array $params)
    {
        return $this->put('groups/'.self::encodePath($group_id).'/labels/'.self::encodePath($label_id), $params);
    }

    /**
     * @param int|string $group_id
     * @param int        $label_id
     *
     * @return mixed
     */
    public function removeLabel($group_id, int $label_id)
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/labels/'.self::encodePath($label_id));
    }

    /**
     * @param int|string $group_id
     * @param array      $parameters
     *
     * @return mixed
     */
    public function variables($group_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get('groups/'.self::encodePath($group_id).'/variables', $resolver->resolve($parameters));
    }

    /**
     * @param int|string $group_id
     * @param string     $key
     *
     * @return mixed
     */
    public function variable($group_id, string $key)
    {
        return $this->get('groups/'.self::encodePath($group_id).'/variables/'.self::encodePath($key));
    }

    /**
     * @param int|string $group_id
     * @param string     $key
     * @param string     $value
     * @param bool|null  $protected
     *
     * @return mixed
     */
    public function addVariable($group_id, string $key, string $value, ?bool $protected = null)
    {
        $payload = [
            'key' => $key,
            'value' => $value,
        ];

        if ($protected) {
            $payload['protected'] = $protected;
        }

        return $this->post('groups/'.self::encodePath($group_id).'/variables', $payload);
    }

    /**
     * @param int|string $group_id
     * @param string     $key
     * @param string     $value
     * @param bool|null  $protected
     *
     * @return mixed
     */
    public function updateVariable($group_id, string $key, string $value, ?bool $protected = null)
    {
        $payload = [
            'value' => $value,
        ];

        if ($protected) {
            $payload['protected'] = $protected;
        }

        return $this->put('groups/'.self::encodePath($group_id).'/variables/'.self::encodePath($key), $payload);
    }

    /**
     * @param int|string $group_id
     * @param string     $key
     *
     * @return mixed
     */
    public function removeVariable($group_id, string $key)
    {
        return $this->delete('groups/'.self::encodePath($group_id).'/variables/'.self::encodePath($key));
    }

    /**
     * @param int|string $group_id
     * @param array      $parameters {
     *
     *     @var bool   $exclude_subgroups   if the parameter is included as true, packages from projects from subgroups
     *                                      are not listed. default is false.
     *     @var string $order_by            the field to use as order. one of created_at (default), name, version, type,
     *                                      or project_path.
     *     @var string $sort                the direction of the order, either asc (default) for ascending order
     *                                      or desc for descending order
     *     @var string $package_type        filter the returned packages by type. one of conan, maven, npm, pypi,
     *                                      composer, nuget, or golang.
     *     @var string $package_name        filter the project packages with a fuzzy search by name
     *     @var bool   $include_versionless when set to true, versionless packages are included in the response
     *     @var string $status              filter the returned packages by status. one of default (default),
     *                                      hidden, or processing.
     * }
     *
     * @return mixed
     */
    public function packages($group_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('exclude_subgroups')
            ->setAllowedTypes('exclude_subgroups', 'bool')
            ->setNormalizer('exclude_subgroups', $booleanNormalizer)
        ;
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['created_at', 'name', 'version', 'type'])
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;
        $resolver->setDefined('package_type')
            ->setAllowedValues('package_type', ['conan', 'maven', 'npm', 'pypi', 'composer', 'nuget', 'golang'])
        ;
        $resolver->setDefined('package_name');
        $resolver->setDefined('include_versionless')
            ->setAllowedTypes('include_versionless', 'bool')
            ->setNormalizer('include_versionless', $booleanNormalizer)
        ;
        $resolver->setDefined('status')
            ->setAllowedValues('status', ['default', 'hidden', 'processing'])
        ;

        return $this->get('groups/'.self::encodePath($group_id).'/packages', $resolver->resolve($parameters));
    }

    /**
     * @return OptionsResolver
     */
    private function getGroupSearchResolver()
    {
        $resolver = $this->getSubgroupSearchResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('top_level_only')
            ->setAllowedTypes('top_level_only', 'bool')
            ->setNormalizer('top_level_only', $booleanNormalizer)
        ;

        return $resolver;
    }

    /**
     * @return OptionsResolver
     */
    private function getSubgroupSearchResolver()
    {
        $resolver = $this->createOptionsResolver();
        $booleanNormalizer = function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('skip_groups')
            ->setAllowedTypes('skip_groups', 'array')
            ->setAllowedValues('skip_groups', function (array $value) {
                return \count($value) === \count(\array_filter($value, 'is_int'));
            })
        ;
        $resolver->setDefined('all_available')
            ->setAllowedTypes('all_available', 'bool')
            ->setNormalizer('all_available', $booleanNormalizer)
        ;
        $resolver->setDefined('search');
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['name', 'path'])
        ;
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc'])
        ;
        $resolver->setDefined('statistics')
            ->setAllowedTypes('statistics', 'bool')
            ->setNormalizer('statistics', $booleanNormalizer)
        ;
        $resolver->setDefined('owned')
            ->setAllowedTypes('owned', 'bool')
            ->setNormalizer('owned', $booleanNormalizer)
        ;
        $resolver->setDefined('min_access_level')
            ->setAllowedValues('min_access_level', [null, 10, 20, 30, 40, 50])
        ;

        return $resolver;
    }
}
