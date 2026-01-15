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

use Gitlab\Api\GroupsHooks;
use PHPUnit\Framework\Attributes\Test;

class GroupHooksTest extends TestCase
{
    #[Test]
    public function shouldGetAllHooks(): void
    {
        $expectedArray = [
            ['id' => 1, 'url' => 'https://example.com/webhook-trigger/1'],
            ['id' => 2, 'url' => 'https://example.com/webhook-trigger/2'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/hooks', [])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1));
    }

    #[Test]
    public function shouldShowHook(): void
    {
        $expectedArray = ['id' => 2, 'url' => 'https://example.com/webhook-trigger/2'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/hooks/2')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    #[Test]
    public function shouldCreateHook(): void
    {
        $expectedArray = ['id' => 3, 'url' => 'https://example.com/webhook-trigger/3'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups/1/hooks', ['url' => $expectedArray['url']])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->create(1, ['url' => $expectedArray['url']]));
    }

    #[Test]
    public function shouldUpdateHook(): void
    {
        $expectedArray = ['id' => 2, 'url' => 'https://example.com/webhook-trigger-rename/2'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/1/hooks/2', ['url' => $expectedArray['url'], 'name' => 'Test Webhook'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->update(1, 2, ['url' => $expectedArray['url'], 'name' => 'Test Webhook']));
    }

    #[Test]
    public function shouldRemoveHook(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('groups/1/hooks/2')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->remove(1, 2));
    }

    #[Test]
    public function shouldGetAllEvents(): void
    {
        $expectedArray = [
            [
                'id' => 1,
                'url' => 'https://example.com/webhook-trigger/2',
                'trigger' => 'push_hooks',
            ], [
                'id' => 2,
                'url' => 'https://example.com/webhook-trigger/2',
                'trigger' => 'push_hooks',
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/hooks/2/events')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->allEvents(1, 2));
    }

    #[Test]
    public function shouldResend(): void
    {
        $expectedArray = [
            'response_status' => 200,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups/1/hooks/2/events/3/resend')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->resend(1, 2, 3));
    }

    #[Test]
    public function shouldTest(): void
    {
        $expectedArray = [
            'message' => '201 Created',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups/1/hooks/2/test/issues_events')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->test(1, 2, 'issues_events'));
    }

    #[Test]
    public function shouldSetCustomHeader(): void
    {
        $expected = '';

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/1/hooks/2/custom_headers/X-Header', ['value' => 'Test'])
            ->willReturn($expected)
        ;

        $this->assertEquals($expected, $api->setCustomHeader(1, 2, 'X-Header', 'Test'));
    }

    #[Test]
    public function shouldDeleteCustomHeader(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('groups/1/hooks/2/custom_headers/X-Header')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->deleteCustomHeader(1, 2, 'X-Header'));
    }

    #[Test]
    public function shouldSetUrlVariable(): void
    {
        $expected = '';

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/1/hooks/2/url_variables/testvar', ['value' => '123'])
            ->willReturn($expected)
        ;

        $this->assertEquals($expected, $api->setUrlVariable(1, 2, 'testvar', '123'));
    }

    #[Test]
    public function shouldDeleteUrlVariable(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('groups/1/hooks/2/url_variables/testvar')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->deleteUrlVariable(1, 2, 'testvar'));
    }

    protected function getApiClass(): string
    {
        return GroupsHooks::class;
    }
}
