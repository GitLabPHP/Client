<?php

namespace Gitlab\Tests\Api;

use Gitlab\Api\ResourceWeightEvents;

class ResourceWeightEventsTest extends TestCase
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
                'issue_id' => 253,
                'weight' => 3,
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
                'issue_id' => 253,
                'weight' => 2,
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('/projects/1/issues/253/resource_weight_events', [])
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
            'issue_id' => 253,
            'weight' => 3,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('/projects/1/issues/253/resource_state_events/142', [])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->show(1, 253, 142));
    }

    /**
     * @return string
     */
    protected function getApiClass(): string
    {
        return ResourceWeightEvents::class;
    }
}
