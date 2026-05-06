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

namespace Gitlab\Tests\Api;

use Gitlab\Api\Registry;
use PHPUnit\Framework\Attributes\Test;

final class RegistryTest extends TestCase
{
    #[Test]
    public function shouldGetRepository(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A registry'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('registry/repositories/1', [])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->repository(1));
    }

    #[Test]
    public function shouldGetRepositoryWithParams(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A registry', 'tags' => ['tag1', 'tag2'], 'tags_count' => 2, 'size' => 12345];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('registry/repositories/1', ['tags' => 'true', 'tags_count' => 'true', 'size' => 'true'])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->repository(1, ['tags' => true, 'tags_count' => true, 'size' => true]));
    }

    #[Test]
    public function shouldRemoveRepository(): void
    {
        $expectedString = '';

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/registry/repositories/2')
            ->willReturn($expectedString);

        $this->assertEquals($expectedString, $api->removeRepository(1, 2));
    }

    #[Test]
    public function shouldGetRepositoryTags(): void
    {
        $expectedArray = [
            ['name' => 'v1.0.0', 'path' => 'group/project:v1.0.0'],
            ['name' => 'v1.1.0', 'path' => 'group/project:v1.1.0'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/registry/repositories/2/tags')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->repositoryTags(1, 2));
    }

    #[Test]
    public function shouldGetRepositoryTag(): void
    {
        $expectedArray = ['name' => 'v1.0.0', 'path' => 'group/project:v1.0.0'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/registry/repositories/2/tags/v1.0.0')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->repositoryTag(1, 2, 'v1.0.0'));
    }

    #[Test]
    public function shouldRemoveRepositoryTag(): void
    {
        $expectedString = '';

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/registry/repositories/2/tags/v1.0.0')
            ->willReturn($expectedString);

        $this->assertEquals($expectedString, $api->removeRepositoryTag(1, 2, 'v1.0.0'));
    }

    #[Test]
    public function shouldRemoveRepositoryTags(): void
    {
        $expectedString = '';
        $parameters = [
            'name_regex_delete' => '.*',
            'name_regex_keep' => 'stable.*',
            'keep_n' => 5,
            'older_than' => '2d',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/registry/repositories/2/tags', $parameters)
            ->willReturn($expectedString);

        $this->assertEquals($expectedString, $api->removeRepositoryTags(1, 2, $parameters));
    }

    protected function getApiClass(): string
    {
        return Registry::class;
    }
}
