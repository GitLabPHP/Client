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

namespace Gitlab\Tests\Api;

use Gitlab\Api\Packages;

final class PackagesTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllPackages(): void
    {
        $expectedArray = [
            [
                'id' => 3,
                'name' => 'Hello/0.1@mycompany/stable',
                'conan_package_name' => 'Hello',
                'version' => '0.1',
                'package_type' => 'conan',
                '_links' => [
                    'web_path' => '/foo/bar/-/packages/3',
                    'delete_api_path' => 'https://gitlab.example.com/api/v4/projects/1/packages/3',
                ],
                'created_at' => '2029-12-16T20:33:34.316Z',
                'tags' => [],
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/packages')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->all(1));
    }

    /**
     * @test
     */
    public function shouldShowPackage(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'com/mycompany/my-app', 'version' => '1.0-SNAPSHOT', 'package_type' => 'maven'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/packages/1')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->show(1, 1));
    }

    /**
     * @test
     */
    public function shouldGetAllPackageFiles(): void
    {
        $expectedArray = [
            ['id' => 25, 'file_name' => 'my-app-1.5-20181107.152550-1.jar', 'size' => 2421],
            ['id' => 26, 'file_name' => 'my-app-1.5-20181107.152550-1.pom', 'size' => 1122],
            ['id' => 27, 'file_name' => 'maven-metadata.xml', 'size' => 767],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/packages/1/package_files')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->allFiles(1, 1));
    }

    /**
     * @test
     */
    public function shouldRemovePackage(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/packages/1')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->remove(1, 1));
    }

    /**
     * @test
     */
    public function shouldRemovePackageFile(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/packages/1/package_files/25')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->removeFile(1, 1, 25));
    }

    protected function getApiClass()
    {
        return Packages::class;
    }
}
