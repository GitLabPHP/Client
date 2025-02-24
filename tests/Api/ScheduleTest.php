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

use Gitlab\Api\Schedules;
use PHPUnit\Framework\Attributes\Test;

class ScheduleTest extends TestCase
{
    #[Test]
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
            ->willReturn($expectedArray);

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

    #[Test]
    public function shouldShowSchedule(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A schedule'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipeline_schedules/2')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    #[Test]
    public function shouldShowAllSchedule(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A schedule'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipeline_schedules')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showAll(1));
    }

    #[Test]
    public function shouldUpdateSchedule(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'Updated schedule'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/pipeline_schedules/3', ['title' => 'Updated schedule', 'due_date' => '2015-04-01', 'state_event' => 'close'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->update(1, 3, ['title' => 'Updated schedule', 'due_date' => '2015-04-01', 'state_event' => 'close']));
    }

    #[Test]
    public function shouldRemoveSchedule(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/pipeline_schedules/2')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->remove(1, 2));
    }

    #[Test]
    public function shouldCreateScheduleVariable(): void
    {
        $expectedArray = [
            'key' => 'FOO_BAR',
            'variable_type' => 'env_var',
            'value' => 'BAZ',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipeline_schedules/2/variables', $expectedArray)
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->addVariable(
            1,
            2,
            $expectedArray
        ));
    }

    #[Test]
    public function shouldUpdateScheduleVariable(): void
    {
        $variabelName = 'FOO_BAR';
        $expectedArray = [
            'key' => $variabelName,
            'variable_type' => 'env_var',
            'value' => 'BAZ',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/pipeline_schedules/2/variables/'.$variabelName, $expectedArray)
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->updateVariable(
            1,
            2,
            $variabelName,
            $expectedArray
        ));
    }

    #[Test]
    public function shouldRemoveScheduleVariable(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/pipeline_schedules/2/variables/FOO_BAR')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeVariable(1, 2, 'FOO_BAR'));
    }

    #[Test]
    public function shouldTakeOwnership(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipeline_schedules/2/take_ownership')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->takeOwnership(1, 2));
    }

    #[Test]
    public function shouldPlay(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipeline_schedules/2/play')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->play(1, 2));
    }

    protected function getApiClass(): string
    {
        return Schedules::class;
    }
}
