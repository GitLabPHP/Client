<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\ResourceIterationEvents;

class ResourceIterationEventsTest extends TestCase
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
                'iteration' => [
                    'id' => 50,
                    'iid' => 9,
                    'group_id' => 5,
                    'title' => 'Iteration I',
                    'description' => 'Ipsum Lorem',
                    'state' => 1,
                    'created_at' => '2020-01-27T05=>07=>12.573Z',
                    'updated_at' => '2020-01-27T05=>07=>12.573Z',
                    'due_date' => null,
                    'start_date' => null,
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
                'created_at' => '2018-08-21T14=>38=>20.077Z',
                'resource_type' => 'Issue',
                'resource_id' => 253,
                'iteration' => [
                    'id' => 53,
                    'iid' => 13,
                    'group_id' => 5,
                    'title' => 'Iteration II',
                    'description' => 'Ipsum Lorem ipsum',
                    'state' => 2,
                    'created_at' => '2020-01-27T05=>07=>12.573Z',
                    'updated_at' => '2020-01-27T05=>07=>12.573Z',
                    'due_date' => null,
                    'start_date' => null,
                ],
                'action' => 'remove',
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/253/resource_iteration_events', [])
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
            'iteration' => [
                'id' => 50,
                'iid' => 9,
                'group_id' => 5,
                'title' => 'Iteration I',
                'description' => 'Ipsum Lorem',
                'state' => 1,
                'created_at' => '2020-01-27T05=>07=>12.573Z',
                'updated_at' => '2020-01-27T05=>07=>12.573Z',
                'due_date' => null,
                'start_date' => null,
            ],
            'action' => 'add',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/253/resource_iteration_events/142', [])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->show(1, 253, 142));
    }

    /**
     * @return string
     */
    protected function getApiClass(): string
    {
        return ResourceIterationEvents::class;
    }
}
