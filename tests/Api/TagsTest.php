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

use Gitlab\Api\Tags;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class TagsTest extends TestCase
{
    #[Test]
    public function shouldGetAllTags(): void
    {
        $expectedArray = [
            ['name' => 'v1.0.0'],
            ['name' => 'v1.1.0'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/tags')
            ->willReturn($expectedArray);
        $this->assertEquals($expectedArray, $api->all(1));
    }

    #[Test]
    public function shouldShowTag(): void
    {
        $expectedArray = [
            ['name' => 'v1.0.0'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/tags/v1.0.0')
            ->willReturn($expectedArray);
        $this->assertEquals($expectedArray, $api->show(1, 'v1.0.0'));
    }

    #[Test]
    public function shouldCreateTag(): void
    {
        $expectedArray = [
            ['name' => 'v1.1.0'],
        ];

        $params = [
            'id' => 1,
            'tag_name' => 'v1.1.0',
            'ref' => 'ref/heads/master',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/tags', $params)
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->create(1, $params));
    }

    #[Test]
    public function shouldRemoveTag(): void
    {
        $expectedArray = [
            ['name' => 'v1.1.0'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/repository/tags/v1.1.0')
            ->willReturn($expectedArray);
        $this->assertEquals($expectedArray, $api->remove(1, 'v1.1.0'));
    }

    #[Test]
    #[DataProvider('releaseDataProvider')]
    public function shouldCreateRelease(string $releaseName, string $description, array $expectedResult): void
    {
        $params = [
            'description' => $description,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/tags/'.\str_replace('/', '%2F', $releaseName).'/release', $params)
            ->willReturn($expectedResult);

        $this->assertEquals($expectedResult, $api->createRelease(1, $releaseName, $params));
    }

    #[Test]
    #[DataProvider('releaseDataProvider')]
    public function shouldUpdateRelease(string $releaseName, string $description, array $expectedResult): void
    {
        $params = [
            'description' => $description,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/repository/tags/'.\str_replace('/', '%2F', $releaseName).'/release', $params)
            ->willReturn($expectedResult);

        $this->assertEquals($expectedResult, $api->updateRelease(1, $releaseName, $params));
    }

    public static function releaseDataProvider(): array
    {
        return [
            [
                'releaseName' => 'v1.1.0',
                'description' => 'Amazing release. Wow',
                'expectedResult' => [
                    'tag_name' => '1.0.0',
                    'description' => 'Amazing release. Wow',
                ],
            ],
            [
                'releaseName' => 'version/1.1.0',
                'description' => 'Amazing release. Wow',
                'expectedResult' => [
                    'tag_name' => 'version/1.1.0',
                    'description' => 'Amazing release. Wow',
                ],
            ],
        ];
    }

    protected function getApiClass(): string
    {
        return Tags::class;
    }
}
