<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Version;

class VersionTest extends TestCase
{
    /**
     * @test
     */
    public function shouldShowVersion()
    {
        $expectedArray = [
            'version' => '8.13.0-pre',
            'revision' => '4e963fe',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('version')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->show());
    }

    protected function getApiClass()
    {
        return Version::class;
    }
}
