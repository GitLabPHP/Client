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

use Gitlab\Api\Milestones;
use PHPUnit\Framework\Attributes\Test;

class MilestonesTest extends TestCase
{
    #[Test]
    public function shouldGetAllMilestones(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A milestone'],
            ['id' => 2, 'title' => 'Another milestone'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/milestones')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1));
    }

    #[Test]
    public function shouldShowMilestone(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A milestone'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/milestones/2')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    #[Test]
    public function shouldCreateMilestone(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'A new milestone'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/milestones', ['description' => 'Some text', 'title' => 'A new milestone'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->create(1, ['description' => 'Some text', 'title' => 'A new milestone']));
    }

    #[Test]
    public function shouldUpdateMilestone(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'Updated milestone'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/milestones/3', ['title' => 'Updated milestone', 'due_date' => '2015-04-01', 'state_event' => 'close'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->update(1, 3, ['title' => 'Updated milestone', 'due_date' => '2015-04-01', 'state_event' => 'close']));
    }

    #[Test]
    public function shouldRemoveMilestone(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/milestones/2')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->remove(1, 2));
    }

    #[Test]
    public function shouldGetMilestonesIssues(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/milestones/3/issues')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->issues(1, 3));
    }

    #[Test]
    public function shouldGetMilestonesMergeRequests(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A merge request'],
            ['id' => 2, 'title' => 'Another merge request'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/milestones/3/merge_requests')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->mergeRequests(1, 3));
    }

    protected function getApiClass(): string
    {
        return Milestones::class;
    }
}
