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

use Gitlab\Api\Keys;

class KeysTest extends TestCase
{
    /**
     * @test
     */
    public function shouldShowKey(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'A key', 'key' => 'ssh-rsa key', 'created_at' => '2016-01-01T01:00:00.000Z'];
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('keys/1')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->show(1));
    }

    protected function getApiClass()
    {
        return Keys::class;
    }
}
