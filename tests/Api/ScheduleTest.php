<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Schedules;

class ScheduleTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCreateSchedule(): void
    {
        $expectedArray = [
            'id' => 13,
            'description' => 'Test schedule pipeline',
            'ref' => 'master',
            'cron' => '* * * * *',
            'cron_timezone' => 'Asia/Tokyo',
            'next_run_at' => '2017-05-19T13:41:00.000Z',
            'active' => true,
            'created_at' => '2017-05-19T13:31:08.849Z',
            'updated_at' => '2017-05-19T13:40:17.727Z',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipeline_schedules', [
                'id' => 13,
                'description' => 'Test schedule pipeline',
                'ref' => 'master',
                'cron' => '* * * * *',
                'cron_timezone' => 'Asia/Tokyo',
                'next_run_at' => '2017-05-19T13:41:00.000Z',
                'active' => true,
                'created_at' => '2017-05-19T13:31:08.849Z',
                'updated_at' => '2017-05-19T13:40:17.727Z',
            ])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->create(
            1,
            [
                'id' => 13,
                'description' => 'Test schedule pipeline',
                'ref' => 'master',
                'cron' => '* * * * *',
                'cron_timezone' => 'Asia/Tokyo',
                'next_run_at' => '2017-05-19T13:41:00.000Z',
                'active' => true,
                'created_at' => '2017-05-19T13:31:08.849Z',
                'updated_at' => '2017-05-19T13:40:17.727Z',
            ]
        ));
    }

    /**
     * @test
     */
    public function shouldShowSchedule(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A schedule'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipeline_schedules/2')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldShowAllSchedule(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A schedule'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipeline_schedules')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->showAll(1));
    }

    /**
     * @test
     */
    public function shouldUpdateSchedule(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'Updated schedule'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/pipeline_schedules/3', ['title' => 'Updated schedule', 'due_date' => '2015-04-01', 'state_event' => 'close'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(1, 3, ['title' => 'Updated schedule', 'due_date' => '2015-04-01', 'state_event' => 'close']));
    }

    /**
     * @test
     */
    public function shouldRemoveSchedule(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/pipeline_schedules/2')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->remove(1, 2));
    }

    protected function getApiClass()
    {
        return Schedules::class;
    }
}
