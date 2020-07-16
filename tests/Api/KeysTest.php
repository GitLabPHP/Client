<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Keys;

class KeysTest extends TestCase
{
    /**
     * @test
     */
    public function shouldShowKey()
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
