<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Milestones;

class MilestonesTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllMilestones()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A milestone'],
            ['id' => 2, 'title' => 'Another milestone'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/milestones')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1));
    }

    /**
     * @test
     */
    public function shouldShowMilestone()
    {
        $expectedArray = ['id' => 1, 'name' => 'A milestone'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/milestones/2')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldCreateMilestone()
    {
        $expectedArray = ['id' => 3, 'title' => 'A new milestone'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/milestones', ['description' => 'Some text', 'title' => 'A new milestone'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create(1, ['description' => 'Some text', 'title' => 'A new milestone']));
    }

    /**
     * @test
     */
    public function shouldUpdateMilestone()
    {
        $expectedArray = ['id' => 3, 'title' => 'Updated milestone'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/milestones/3', ['title' => 'Updated milestone', 'due_date' => '2015-04-01', 'state_event' => 'close'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(1, 3, ['title' => 'Updated milestone', 'due_date' => '2015-04-01', 'state_event' => 'close']));
    }

    /**
     * @test
     */
    public function shouldRemoveMilestone()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/milestones/2')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->remove(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetMilestonesIssues()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/milestones/3/issues')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->issues(1, 3));
    }

    protected function getApiClass()
    {
        return Milestones::class;
    }
}
