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

use Gitlab\Api\Releases;

class ReleasesTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllReleases(): void
    {
        $expectedArray = [
            ['tag_name' => 'v1.0.0'],
            ['tag_name' => 'v1.1.0'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/releases')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->all(1));
    }

    /**
     * @test
     */
    public function shouldShowRelease(): void
    {
        $expectedArray = [
            ['tag_name' => 'v1.0.0'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/releases/v1.0.0')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->show(1, 'v1.0.0'));
    }


    /**
     * @test
     *
     * @dataProvider releaseDataProvider
     *
     * @param string $releaseName
     * @param string $description
     * @param array  $expectedResult
     */
    public function shouldCreateRelease(string $releaseName, string $description, array $expectedResult): void
    {
        $params = [
            'description' => $description,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/releases/'. $releaseName, $params)
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($expectedResult, $api->createRelease(1, $releaseName, $params));
    }

    /**
     * @test
     */
    public function shouldRemoveRelease(): void
    {
        $expectedArray = [
            ['name' => 'v1.1.0'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/releases/v1.1.0')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->remove(1, 'v1.1.0'));
    }


    /**
     * @test
     *
     * @dataProvider releaseDataProvider
     *
     * @param string $releaseName
     * @param string $description
     * @param array  $expectedResult
     */
    public function shouldUpdateRelease(string $releaseName, string $description, array $expectedResult): void
    {
        $params = [
            'description' => $description,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/releases/'. $releaseName, $params)
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($expectedResult, $api->updateRelease(1, $releaseName, $params));
    }

    public static function releaseDataProvider(): array
    {
        return [
            [
                'tagName' => 'v1.1.0',
                'description' => 'Amazing release. Wow',
                'expectedResult' => [
                    'tag_name' => '1.0.0',
                    'description' => 'Amazing release. Wow',
                ],
            ],
            [
                'tagName' => 'version/1.1.0',
                'description' => 'Amazing release. Wow',
                'expectedResult' => [
                    'tag_name' => 'version/1.1.0',
                    'description' => 'Amazing release. Wow',
                ],
            ],
        ];
    }

    protected function getApiClass()
    {
        return Releases::class;
    }
}
