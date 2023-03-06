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

use Gitlab\Api\ResourceStateEvents;

class ResourceStateEventsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllEvents(): void
    {
        $expectedArray = [
            [
                'id' => 142,
                'user' => [
                    'id' => 1,
                    'name' => 'Administrator',
                    'username' => 'root',
                    'state' => 'active',
                    'avatar_url' => 'https=>//www.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=80&d=identicon',
                    'web_url' => 'http=>//gitlab.example.com/root',
                ],
                'created_at' => '2018-08-20T13=>38=>20.077Z',
                'resource_type' => 'Issue',
                'resource_id' => 11,
                'state' => 'opened',
            ],
            [
                'id' => 143,
                'user' => [
                    'id' => 1,
                    'name' => 'Administrator',
                    'username' => 'root',
                    'state' => 'active',
                    'avatar_url' => 'https=>//www.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=80&d=identicon',
                    'web_url' => 'http=>//gitlab.example.com/root',
                ],
                'created_at' => '2018-08-21T14=>38=>20.077Z',
                'resource_type' => 'Issue',
                'resource_id' => 11,
                'state' => 'closed',
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/11/resource_state_events', [])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->all(1, 11));
    }

    /**
     * @test
     */
    public function shouldShowEvent(): void
    {
        $expectedArray = [
            'id' => 142,
            'user' => [
                'id' => 1,
                'name' => 'Administrator',
                'username' => 'root',
                'state' => 'active',
                'avatar_url' => 'https=>//www.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=80&d=identicon',
                'web_url' => 'http=>//gitlab.example.com/root',
            ],
            'created_at' => '2018-08-20T13=>38=>20.077Z',
            'resource_type' => 'Issue',
            'resource_id' => 11,
            'state' => 'opened',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/11/resource_state_events/142', [])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->show(1, 11, 142));
    }

    /**
     * @return string
     */
    protected function getApiClass(): string
    {
        return ResourceStateEvents::class;
    }
}
