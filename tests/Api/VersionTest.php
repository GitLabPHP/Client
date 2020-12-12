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

use Gitlab\Api\Version;

class VersionTest extends TestCase
{
    /**
     * @test
     */
    public function shouldShowVersion(): void
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
