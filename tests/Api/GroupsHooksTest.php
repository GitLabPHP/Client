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

class GroupsHooksTest extends TestCase
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
    public function shouldGetAllHooksWithPagination(): void
    {
        $expectedArray = [
            ['id' => 1, 'url' => 'https://example.com/webhook-trigger/1'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/hooks', ['page' => 2, 'per_page' => 50])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1, ['page' => 2, 'per_page' => 50]));
    }

    #[Test]
    public function shouldGetAllHooksForStringGroupPath(): void
    {
        $expectedArray = [
            ['id' => 1, 'url' => 'https://example.com/webhook-trigger/1'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/foo%2Fbar/hooks', [])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all('foo/bar'));
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
            ->with('groups/1/hooks', ['push_events' => true, 'url' => $expectedArray['url']])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->create(1, $expectedArray['url'], ['push_events' => true]));
    }

    #[Test]
    public function shouldCreateHookWithOnlyUrl(): void
    {
        $expectedArray = ['id' => 3, 'url' => 'https://example.com/webhook-trigger/3'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups/1/hooks', ['url' => $expectedArray['url']])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->create(1, $expectedArray['url']));
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
            ->willReturn($expectedBool)
        ;

        $this->assertEquals($expectedBool, $api->remove(1, 2));
    }

    #[Test]
    public function shouldGetEvents(): void
    {
        $expectedArray = [
            [
                'id' => 1,
                'url' => 'https://example.com/webhook-trigger/2',
                'trigger' => 'push_hooks',
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/hooks/2/events', [])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->events(1, 2));
    }

    #[Test]
    public function shouldGetEventsWithStringStatus(): void
    {
        $expectedArray = [
            ['id' => 1, 'response_status' => '500'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/hooks/2/events', ['page' => 2, 'per_page' => 15, 'status' => 'server_failure'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->events(1, 2, [
            'page' => 2,
            'per_page' => 15,
            'status' => 'server_failure',
        ]));
    }

    #[Test]
    public function shouldGetEventsWithIntegerStatus(): void
    {
        $expectedArray = [
            ['id' => 1, 'response_status' => '200'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/hooks/2/events', ['status' => 200])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->events(1, 2, ['status' => 200]));
    }

    #[Test]
    public function shouldResendEvent(): void
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

        $this->assertEquals($expectedArray, $api->resendEvent(1, 2, 3));
    }

    #[Test]
    public function shouldTestHook(): void
    {
        $expectedArray = [
            'message' => '201 Created',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups/1/hooks/2/test/push_events')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->test(1, 2, 'push_events'));
    }

    #[Test]
    public function shouldSetCustomHeader(): void
    {
        $expected = '';

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/1/hooks/2/custom_headers/X-Webhook-Token', ['value' => 'secret'])
            ->willReturn($expected)
        ;

        $this->assertEquals($expected, $api->setCustomHeader(1, 2, 'X-Webhook-Token', 'secret'));
    }

    #[Test]
    public function shouldDeleteCustomHeader(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('groups/1/hooks/2/custom_headers/X-Webhook-Token')
            ->willReturn($expectedBool)
        ;

        $this->assertEquals($expectedBool, $api->deleteCustomHeader(1, 2, 'X-Webhook-Token'));
    }

    #[Test]
    public function shouldSetUrlVariable(): void
    {
        $expected = '';

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/1/hooks/2/url_variables/environment', ['value' => 'staging'])
            ->willReturn($expected)
        ;

        $this->assertEquals($expected, $api->setUrlVariable(1, 2, 'environment', 'staging'));
    }

    #[Test]
    public function shouldDeleteUrlVariable(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('groups/1/hooks/2/url_variables/environment')
            ->willReturn($expectedBool)
        ;

        $this->assertEquals($expectedBool, $api->deleteUrlVariable(1, 2, 'environment'));
    }

    protected function getApiClass(): string
    {
        return GroupsHooks::class;
    }
}
