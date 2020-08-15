<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Issues;

/**
 * @method Issues|\PHPUnit_Framework_MockObject_MockObject getApiMock()
 */
class IssueSubscribeTest extends TestCase
{
    public function testSubscribeIssue(): void
    {
        $expectedValue = '';
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/subscribe')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $api->subscribe(1, 2));
    }

    public function testUnsubscribeIssue(): void
    {
        $expectedValue = '';
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/2/unsubscribe')
            ->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $api->unsubscribe(1, 2));
    }

    protected function getApiClass()
    {
        return Issues::class;
    }
}
