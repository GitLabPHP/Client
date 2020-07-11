<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

class TagsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllTags()
    {
        $expectedArray = [
            ['name' => 'v1.0.0'],
            ['name' => 'v1.1.0'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/tags')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->all(1));
    }

    /**
     * @test
     */
    public function shouldShowTag()
    {
        $expectedArray = [
            ['name' => 'v1.0.0'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/tags/v1.0.0')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->show(1, 'v1.0.0'));
    }

    /**
     * @test
     */
    public function shouldCreateTag()
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
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->create(1, $params));
    }

    /**
     * @test
     */
    public function shouldRemoveTag()
    {
        $expectedArray = [
            ['name' => 'v1.1.0'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/repository/tags/v1.1.0')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->remove(1, 'v1.1.0'));
    }

    /**
     * @test
     * @dataProvider releaseDataProvider
     *
     * @param string $releaseName
     * @param string $description
     * @param array  $expectedResult
     */
    public function shouldCreateRelease($releaseName, $description, $expectedResult)
    {
        $params = [
            'description' => $description,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/tags/'.str_replace(['/', '.'], ['%2F', '.'], $releaseName).'/release', $params)
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($expectedResult, $api->createRelease(1, $releaseName, $params));
    }

    /**
     * @test
     * @dataProvider releaseDataProvider
     *
     * @param string $releaseName
     * @param string $description
     * @param array  $expectedResult
     */
    public function shouldUpdateRelease($releaseName, $description, $expectedResult)
    {
        $params = [
            'description' => $description,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/repository/tags/'.str_replace(['/', '.'], ['%2F', '.'], $releaseName).'/release', $params)
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($expectedResult, $api->updateRelease(1, $releaseName, $params));
    }

    public function releaseDataProvider()
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
        return 'Gitlab\Api\Tags';
    }
}
