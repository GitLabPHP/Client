<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\ResourceLabelEvents;

class ResourceLabelEventsTest extends TestCase
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
                'resource_id' => 253,
                'label' => [
                    'id' => 73,
                    'name' => 'a1',
                    'color' => '#34495E',
                    'description' => '',
                ],
                'action' => 'add',
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
                'created_at' => '2018-08-20T13=>38=>20.077Z',
                'resource_type' => 'Issue',
                'resource_id' => 253,
                'label' => [
                    'id' => 74,
                    'name' => 'p1',
                    'color' => '#0033CC',
                    'description' => '',
                ],
                'action' => 'remove',
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/253/resource_label_events', [])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->all(1, 253));
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
            'resource_id' => 253,
            'label' => [
                'id' => 73,
                'name' => 'a1',
                'color' => '#34495E',
                'description' => '',
            ],
            'action' => 'add',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/253/resource_label_events/142', [])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->show(1, 253, 142));
    }

    /**
     * @return string
     */
    protected function getApiClass(): string
    {
        return ResourceLabelEvents::class;
    }
}
