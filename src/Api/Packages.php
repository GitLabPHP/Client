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

use Symfony\Component\OptionsResolver\Options;

class Packages extends AbstractApi
{
    /**
     * @param int|string $project_id
     * @param array      $parameters {
     *
     *     @var string $order_by            the field to use as order. one of created_at (default), name,
     *                                      version, or type
     *     @var string $sort                the direction of the order, either asc (default) for ascending order or
     *                                      desc for descending order.
     *     @var string $package_type        filter the returned packages by type. one of conan, maven, npm, pypi,
     *                                      composer, nuget, or golang.
     *     @var string $package_name        filter the project packages with a fuzzy search by name.
     *     @var bool   $include_versionless when set to true, versionless packages are included in the response.
     *     @var string $status              filter the returned packages by status. one of default (default),
     *                                      hidden, or processing.
     * }
     *
     * @return mixed
     */
    public function all($project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

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
            ->setNormalizer('include_versionless', function (Options $resolver, $value): string {
                return $value ? 'true' : 'false';
            })
        ;
        $resolver->setDefined('status')
            ->setAllowedValues('status', ['default', 'hidden', 'processing'])
        ;

        return $this->get($this->getProjectPath($project_id, 'packages'), $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param int        $package_id
     *
     * @return mixed
     */
    public function show($project_id, int $package_id)
    {
        return $this->get($this->getPackagePath($project_id, $package_id));
    }

    /**
     * @param int|string $project_id
     * @param int        $package_id
     *
     * @return mixed
     */
    public function allFiles($project_id, int $package_id)
    {
        return $this->get($this->getPackagePath($project_id, $package_id).'/package_files');
    }

    /**
     * @param int|string $project_id
     * @param int        $package_id
     *
     * @return mixed
     */
    public function remove($project_id, int $package_id)
    {
        return $this->delete($this->getPackagePath($project_id, $package_id));
    }

    /**
     * @param int|string $project_id
     * @param int        $package_id
     * @param int        $package_file_id
     *
     * @return mixed
     */
    public function removeFile($project_id, int $package_id, int $package_file_id)
    {
        return $this->delete(
            $this->getPackagePath($project_id, $package_id).'/package_files/'.self::encodePath($package_file_id)
        );
    }

    /**
     * @param int|string $project_id
     * @param int        $package_id
     *
     * @return string
     */
    private function getPackagePath($project_id, int $package_id): string
    {
        return $this->getProjectPath($project_id, 'packages/'.self::encodePath($package_id));
    }
}
